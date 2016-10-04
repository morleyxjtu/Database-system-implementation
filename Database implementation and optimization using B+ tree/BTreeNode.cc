#include "BTreeNode.h"
#include "PageFile.h"
#include <cstring>
#include <iostream>

using namespace std;

BTLeafNode::BTLeafNode()
{
	//initialize the buffer to be -1
	memset(buffer, -1, PageFile::PAGE_SIZE);
	max_key=(PageFile::PAGE_SIZE-sizeof(PageId))/(sizeof(int)+sizeof(RecordId));
}
/*
 * Read the content of the node from the page pid in the PageFile pf.
 * @param pid[IN] the PageId to read
 * @param pf[IN] PageFile to read from
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::read(PageId pid, const PageFile& pf)
{ 
	// use read function in PageFile to read a disk page with pid to the buffer defined in BTreeNode.h
	RC rc;
	rc = pf.read(pid, buffer);
	return rc; 
}
    
/*
 * Write the content of the node to the page pid in the PageFile pf.
 * @param pid[IN] the PageId to write to
 * @param pf[IN] PageFile to write to
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::write(PageId pid, PageFile& pf)
{ 
	RC rc;
	rc = pf.write(pid, buffer);
	return rc; 
}

/*
 * Return the number of keys stored in the node.
 * @return the number of keys in the node
 */
int BTLeafNode::getKeyCount()
{ 
	int count=0;
	int pid;
	//use last byte for PageId and the rest 1020 bytes for (key, RecordId) pair, max. 85 pairs
	
	char* pid_location = buffer+sizeof(int);
	memcpy(&pid, pid_location, sizeof(int));

	//the buffer was initialized to -1, pid cannot be -1.
	//scan thru the buffer until pid =-1 or count reaches max.
	while(pid!=-1 && count <max_key)
	{
		count++;
		pid_location += sizeof(int)+sizeof(RecordId);
		memcpy(&pid, pid_location, sizeof(int));
	}
	return count; 
}

/*
 * Insert a (key, rid) pair to the node.
 * @param key[IN] the key to insert
 * @param rid[IN] the RecordId to insert
 * @return 0 if successful. Return an error code if the node is full.
 */
RC BTLeafNode::insert(int key, const RecordId& rid)
{ 
	if (getKeyCount() >= max_key)
	{
		return RC_NODE_FULL;
	}

	int i=0; //store number of (RecordID, key) pairs
	int current_key;
	int current_pid; //pid in RecordId not the one at the end of the buffer
	char* key_location = buffer;

	memcpy(&current_key, key_location, sizeof(key));
	memcpy(&current_pid, key_location+sizeof(int), sizeof(int));

	//scan thru the buffer until current_key > key or current_pid=-1(end of the buffer)
	//since the buffer cannot be full, buffer always ends with current_pid=-1
	while (key >= current_key && current_pid!=-1)
	{
		i++;
		key_location += sizeof(key)+sizeof(rid);
		memcpy(&current_key, key_location, sizeof(key));
		memcpy(&current_pid, key_location+sizeof(int), sizeof(int));
	}
	//key_location is the insert location. move right of it one pair right
	memmove(key_location+sizeof(key)+sizeof(rid), key_location, (getKeyCount()-i)*(sizeof(key)+sizeof(rid)));
	//move key and rid into the insert location
	memcpy(key_location, &key, sizeof(key));
	memcpy(key_location+sizeof(key), &rid, sizeof(rid));

	return 0; 
}

/*
 * Insert the (key, rid) pair to the node
 * and split the node half and half with sibling.
 * The first key of the sibling node is returned in siblingKey.
 * @param key[IN] the key to insert.
 * @param rid[IN] the RecordId to insert.
 * @param sibling[IN] the sibling node to split with. This node MUST be EMPTY when this function is called.
 * @param siblingKey[OUT] the first key in the sibling node after split.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::insertAndSplit(int key, const RecordId& rid, 
                              BTLeafNode& sibling, int& siblingKey)
{ 
	if (getKeyCount()<max_key)
	{
		return RC_INVALID_FILE_MODE;
	}
	if (sibling.getKeyCount()!=0)
	{
		return RC_INVALID_FILE_FORMAT;
	}

	//create a new buffer which is one pair bigger than buffer (for inserting new pair)
	char overflow_buffer[PageFile::PAGE_SIZE-sizeof(PageId)+sizeof(key)+sizeof(rid)];
	memset(overflow_buffer, -1, PageFile::PAGE_SIZE-sizeof(PageId)+sizeof(key)+sizeof(rid));
	memcpy(overflow_buffer, buffer, PageFile::PAGE_SIZE-sizeof(PageId));

	int i=0; //store number of (RecordID, key) pairs
	int current_key;
	int current_pid;
	int next_pid; //pid of next node
	char* overflow_key_location = overflow_buffer;
	memcpy(&current_key, overflow_key_location, sizeof(key));
	memcpy(&current_pid, overflow_key_location+sizeof(int), sizeof(int));
	memcpy(&next_pid, buffer+PageFile::PAGE_SIZE-sizeof(PageId), sizeof(PageId));//store the pid of next node
	
	while (key >= current_key && current_pid!=-1)
	{
		i++;
		overflow_key_location += sizeof(key)+sizeof(rid);
		memcpy(&current_key, overflow_key_location, sizeof(key));
		memcpy(&current_pid, overflow_key_location+sizeof(int), sizeof(int));
	}

	memmove(overflow_key_location+sizeof(key)+sizeof(rid), overflow_key_location, (getKeyCount()-i)*(sizeof(key)+sizeof(rid)));
	memcpy(overflow_key_location, &key, sizeof(key));
	memcpy(overflow_key_location+sizeof(key), &rid, sizeof(rid));

	int half = (max_key+1)/2;

	//initialize buffer, copy half of the overflow_buffer to it. The next node pid (i.e., pid of sibling) will be set using setNextNodePtr(PageId pid)
	memset(buffer, -1, PageFile::PAGE_SIZE);
	memcpy(buffer, overflow_buffer, half*(sizeof(key)+sizeof(rid)));
	//copy the other half of the overflow_buffer to it. The next node pid is the pid for previous buffer
	memcpy(sibling.buffer, overflow_buffer+half*(sizeof(key)+sizeof(rid)), half*(sizeof(key)+sizeof(rid)));
	memcpy(sibling.buffer+PageFile::PAGE_SIZE-sizeof(PageId), &next_pid, sizeof(PageId));
	
	memcpy(&siblingKey, sibling.buffer, sizeof(int));
	
	return 0; 
}

/**
 * If searchKey exists in the node, set eid to the index entry
 * with searchKey and return 0. If not, set eid to the index entry
 * immediately after the largest index key that is smaller than searchKey,
 * and return the error code RC_NO_SUCH_RECORD.
 * Remember that keys inside a B+tree node are always kept sorted.
 * @param searchKey[IN] the key to search for.
 * @param eid[OUT] the index entry number with searchKey or immediately
                   behind the largest key smaller than searchKey.
 * @return 0 if searchKey is found. Otherwise return an error code.
 */
RC BTLeafNode::locate(int searchKey, int& eid)
{ 
	int i=0; //eid
	int current_key;
	int current_pid;
	char* key_location = buffer;

	memcpy(&current_key, key_location, sizeof(int));
	memcpy(&current_pid, key_location+sizeof(int), sizeof(int));

	while (searchKey > current_key && current_pid!=-1 && i < max_key)
	{
		i++;
		key_location += sizeof(int)+sizeof(RecordId);
		memcpy(&current_key, key_location, sizeof(int));
		if(searchKey==current_key)
		{
			eid=i;
			return 0;
		}
		memcpy(&current_pid, key_location+sizeof(int), sizeof(int));
	}

	if(i==0)
	{
		eid = 0;
		return RC_NO_SUCH_RECORD;
	}
	else
	{
		eid = i-1;
		return RC_NO_SUCH_RECORD;
	}

}

/*
 * Read the (key, rid) pair from the eid entry.
 * @param eid[IN] the entry number to read the (key, rid) pair from
 * @param key[OUT] the key from the entry
 * @param rid[OUT] the RecordId from the entry
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::readEntry(int eid, int& key, RecordId& rid)
{ 
	if(eid>=getKeyCount() || eid <0)
	{
		return RC_NO_SUCH_RECORD;
	}

	char* key_location = buffer;
	memcpy(&key, key_location+eid*(sizeof(key)+sizeof(rid)), sizeof(key));
	memcpy(&rid, key_location+eid*(sizeof(key)+sizeof(rid))+sizeof(key), sizeof(rid));

	return 0; 
}

/*
 * Return the pid of the next slibling node.
 * @return the PageId of the next sibling node 
 */
PageId BTLeafNode::getNextNodePtr()
{
	PageId pid;
	char* key_location = buffer;
	memcpy(&pid, key_location+PageFile::PAGE_SIZE-sizeof(PageId), sizeof(PageId));
	if (pid == -1)
	{
		return RC_INVALID_PID;
	}else
	{
		return pid; 
	}
}

/*
 * Set the pid of the next slibling node.
 * @param pid[IN] the PageId of the next sibling node 
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTLeafNode::setNextNodePtr(PageId pid)
{
	if (pid <0)
	{
		return RC_INVALID_PID;
	}

	memcpy(buffer+PageFile::PAGE_SIZE-sizeof(PageId), &pid, sizeof(PageId));
	return 0;
}
/*
RC BTLeafNode::show()
{
	int item;
	memcpy(&item, buffer+12, sizeof(int));
	return item;
}
*/
BTNonLeafNode::BTNonLeafNode()
{
	memset(buffer, -1, PageFile::PAGE_SIZE);
	//save 5 bytes empty space for possible purposes. 125 keys max
	max_key=(PageFile::PAGE_SIZE-6*sizeof(PageId))/(sizeof(int)+sizeof(PageId));
}

/*
 * Read the content of the node from the page pid in the PageFile pf.
 * @param pid[IN] the PageId to read
 * @param pf[IN] PageFile to read from
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::read(PageId pid, const PageFile& pf)
{
	RC rc;
	rc = pf.read(pid, buffer);
	return rc; 
}
    
/*
 * Write the content of the node to the page pid in the PageFile pf.
 * @param pid[IN] the PageId to write to
 * @param pf[IN] PageFile to write to
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::write(PageId pid, PageFile& pf)
{
	RC rc;
	rc = pf.write(pid, buffer);
	return rc; 
}

/*
 * Return the number of keys stored in the node.
 * @return the number of keys in the node
 */
int BTNonLeafNode::getKeyCount()
{
	int count=0;
	int pid;
	
	char* pid_location = buffer+2*sizeof(int);

	memcpy(&pid, pid_location, sizeof(int));

	//the buffer was initialized to -1. assuming no minus pid.
	while(pid!=-1 && count <max_key)
	{
		count++;
		pid_location += sizeof(int)+sizeof(PageId);
		memcpy(&pid, pid_location, sizeof(int));
	}
	return count; 
}


/*
 * Insert a (key, pid) pair to the node.
 * @param key[IN] the key to insert
 * @param pid[IN] the PageId to insert
 * @return 0 if successful. Return an error code if the node is full.
 */

RC BTNonLeafNode::insert(int key, PageId pid)
{
	if (getKeyCount() >= max_key)
	{
		return RC_NODE_FULL;
	}

	int i=0; //store number of (PageId, key) pairs
	int current_key;
	int current_pid;
	char* key_location = buffer+sizeof(pid);

	memcpy(&current_key, key_location, sizeof(key));
	memcpy(&current_pid, key_location+sizeof(key), sizeof(pid));

	while (key >= current_key && current_pid!=-1)
	{
		i++;
		key_location += sizeof(key)+sizeof(pid);
		memcpy(&current_key, key_location, sizeof(key));
		memcpy(&current_pid, key_location+sizeof(key), sizeof(pid));
	}
	
	//move right of the insert pair one pair right
	memmove(key_location+sizeof(key)+sizeof(pid), key_location, (getKeyCount()-i)*(sizeof(key)+sizeof(pid)));
	memcpy(key_location, &key, sizeof(key));		
	memcpy(key_location+sizeof(key), &pid, sizeof(pid));

	return 0; 
}

/*
 * Insert the (key, pid) pair to the node
 * and split the node half and half with sibling.
 * The middle key after the split is returned in midKey.
 * @param key[IN] the key to insert
 * @param pid[IN] the PageId to insert
 * @param sibling[IN] the sibling node to split with. This node MUST be empty when this function is called.
 * @param midKey[OUT] the key in the middle after the split. This key should be inserted to the parent node.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::insertAndSplit(int key, PageId pid, BTNonLeafNode& sibling, int& midKey)
{
	if (getKeyCount()<max_key)
	{
		return RC_INVALID_FILE_MODE;
	}
	if (sibling.getKeyCount()!=0)
	{
		return RC_INVALID_FILE_FORMAT;
	}

	char overflow_buffer[PageFile::PAGE_SIZE+sizeof(key)+sizeof(pid)];
	memset(overflow_buffer, -1, PageFile::PAGE_SIZE+sizeof(key)+sizeof(pid));
	memcpy(overflow_buffer, buffer, PageFile::PAGE_SIZE);
	char* overflow_key_location = overflow_buffer+sizeof(pid);
	

	int i=0; //store number of (RecordID, key) pairs
	int current_key;
	int current_pid;
	memcpy(&current_key, overflow_key_location, sizeof(key));
	memcpy(&current_pid, overflow_key_location+sizeof(int), sizeof(pid));
	
	while (key >= current_key && current_pid!=-1)
	{
		i++;
		overflow_key_location += sizeof(key)+sizeof(pid);
		memcpy(&current_key, overflow_key_location, sizeof(key));
		memcpy(&current_pid, overflow_key_location+sizeof(int), sizeof(int));
	}

	memmove(overflow_key_location+sizeof(key)+sizeof(pid), overflow_key_location, (getKeyCount()-i)*(sizeof(key)+sizeof(pid)));
	memcpy(overflow_key_location, &key, sizeof(key));
	memcpy(overflow_key_location+sizeof(key), &pid, sizeof(pid));

	int half = (max_key+1)/2;

	memset(buffer, -1, PageFile::PAGE_SIZE);
	memcpy(buffer, overflow_buffer, half*(sizeof(key)+sizeof(pid))+sizeof(pid));

	memcpy(sibling.buffer, overflow_buffer+sizeof(pid)+half*(sizeof(key)+sizeof(pid))+sizeof(key), sizeof(pid)+(max_key-half)*(sizeof(key)+sizeof(pid)));
	
	memcpy(&midKey, overflow_buffer+sizeof(pid)+half*(sizeof(key)+sizeof(pid)),sizeof(key));
	return 0;
}

/*
 * Given the searchKey, find the child-node pointer to follow and
 * output it in pid.
 * @param searchKey[IN] the searchKey that is being looked up.
 * @param pid[OUT] the pointer to the child node to follow.
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::locateChildPtr(int searchKey, PageId& pid)
{
	int current_key;
	int current_pid;
	char* key_location = buffer+sizeof(pid);

	memcpy(&current_key, key_location, sizeof(int));
	memcpy(&current_pid, key_location+sizeof(int), sizeof(pid));

	if (searchKey<current_key)
	{
		memcpy(&pid, buffer, sizeof(pid));
		return 0;
	}

	while (searchKey >= current_key && current_pid!=-1)
	{
		key_location += sizeof(int)+sizeof(pid);
		memcpy(&current_key, key_location, sizeof(int));
		memcpy(&current_pid, key_location+sizeof(int), sizeof(pid));
	}

	memcpy(&pid, key_location-sizeof(pid), sizeof(pid));

	return 0;
}

/*
 * Initialize the root node with (pid1, key, pid2).
 * @param pid1[IN] the first PageId to insert
 * @param key[IN] the key that should be inserted between the two PageIds
 * @param pid2[IN] the PageId to insert behind the key
 * @return 0 if successful. Return an error code if there is an error.
 */
RC BTNonLeafNode::initializeRoot(PageId pid1, int key, PageId pid2)
{
	memset(buffer, -1, PageFile::PAGE_SIZE);
	char* pid1_position = buffer;

	memcpy(pid1_position, &pid1, sizeof(PageId));
	memcpy(pid1_position+sizeof(PageId), &key, sizeof(key));
	memcpy(pid1_position+sizeof(PageId)+sizeof(key), &pid2, sizeof(PageId));

	return 0;
}
