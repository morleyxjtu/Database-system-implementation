/*
 * Copyright (C) 2008 by The Regents of the University of California
 * Redistribution of this file is permitted under the terms of the GNU
 * Public License (GPL).
 *
 * @author Junghoo "John" Cho <cho AT cs.ucla.edu>
 * @date 3/24/2008
 */
 
#include "BTreeIndex.h"
#include "BTreeNode.h"
#include <cstring>
#include <iostream>

using namespace std;

/*
 * BTreeIndex constructor
 */
BTreeIndex::BTreeIndex()
{
    rootPid = -1;
    tempRootPid = -1;
    treeHeight = 0;
    currentHeight = 1;
    lastNonLeaf = -1;
    memset(buffer, -1, PageFile::PAGE_SIZE);
}

/*
 * Open the index file in read or write mode.
 * Under 'w' mode, the index file should be created if it does not exist.
 * @param indexname[IN] the name of the index file
 * @param mode[IN] 'r' for read, 'w' for write
 * @return error code. 0 if no error
 */
RC BTreeIndex::open(const string& indexname, char mode)
{
    RC rc;
    rc=pf.open(indexname, mode);

    if (rc != 0)
    {
        return rc;
    }

    if (pf.endPid() != 0)
    {
        rc = pf.read(0, buffer);
        memcpy(&rootPid, buffer, sizeof(PageId));
        memcpy(&treeHeight, buffer+sizeof(PageId), sizeof(int));
    }
    return rc;
}

/*
 * Close the index file.
 * @return error code. 0 if no error
 */
RC BTreeIndex::close()
{
    RC rc;
    memset(buffer, -1, PageFile::PAGE_SIZE);
    memcpy(buffer, &rootPid, sizeof(PageId));
    memcpy(buffer+sizeof(PageId), &treeHeight, sizeof(int));
    rc = pf.write(0, buffer);

    if (rc != 0)
    {
        return rc;
    }

    rc = pf.close();
    return rc;
}

/*
 * Insert (key, RecordId) pair to the index.
 * @param key[IN] the key for the value inserted into the index
 * @param rid[IN] the RecordId for the record being inserted into the index
 * @return error code. 0 if no error
 */
RC BTreeIndex::insert(int key, const RecordId& rid)
{
	RC rc;
    //insert the first pair when the file is empty
    if (treeHeight == 0)
    {
    	BTLeafNode leaf;
    	int initial_pid = 1;

    	rc = leaf.insert(key, rid);
        if (rc != 0)
        {
            return rc;
        }

    	rc = leaf.write(initial_pid, pf);
        if (rc != 0)
        {
            return rc;
        }

    	treeHeight++;

    	return 0;
    }
    //initialize the root node
    if (treeHeight == 1 && pf.endPid() == 2)
    {
    	BTLeafNode leaf;
    	leaf.read(1, pf);

    	if (leaf.insert(key, rid) == 0)
    	{
    		leaf.write(1, pf);
    		return 0;
    	}

    	BTLeafNode leaf_sib;
    	int sibKey;
    	int sibPid;
    	leaf.insertAndSplit(key, rid, leaf_sib, sibKey);
    	sibPid = pf.endPid();
    	leaf.setNextNodePtr(sibPid);

    	BTNonLeafNode root;
    	root.initializeRoot(1, sibKey, sibPid);
    	rootPid = pf.endPid()+1;
        
    	leaf.setParent(rootPid);
    	leaf_sib.setParent(rootPid);

    	leaf.write(1, pf);
    	leaf_sib.write(sibPid, pf);
    	root.write(rootPid, pf);

        treeHeight++;

        return 0;
    }
    //element insertion
    currentHeight = 1;
    tempRootPid = rootPid;
    lastNonLeaf = -1;
    insertRecursion(key, rid);

    return 0;
}

RC BTreeIndex::insertRecursion(int key, const RecordId& rid)
{
    if(currentHeight != treeHeight)
    {
        BTNonLeafNode nonLeaf;
        nonLeaf.read(tempRootPid, pf);
        nonLeaf.setParent(lastNonLeaf);

        PageId child = -1;
        nonLeaf.locateChildPtr(key, child);

        lastNonLeaf = tempRootPid;
        tempRootPid = child;
        currentHeight++;

        insertRecursion(key, rid);
    }
    else
    {
        BTLeafNode leaf;
        leaf.read(tempRootPid, pf);

        if (leaf.insert(key, rid) == 0)
        {
            leaf.write(tempRootPid, pf);
            return 0;
        } 

        BTLeafNode leaf_sib;
        int sibKey;
        PageId sibPid;
        PageId parentPid;
        parentPid = lastNonLeaf;
        leaf.insertAndSplit(key, rid, leaf_sib, sibKey);

        sibPid = pf.endPid();
        leaf.setNextNodePtr(sibPid);
        
        leaf.setParent(parentPid);        
        leaf_sib.setParent(parentPid);

        leaf.write(tempRootPid, pf);
        leaf_sib.write(sibPid, pf);

        insertParent(parentPid, sibKey, sibPid);
    }
}

RC BTreeIndex::insertParent(PageId parentPid, int key, PageId pid)
{
        BTNonLeafNode nonLeaf;
        PageId nonLeafPid;

        nonLeaf.read(parentPid, pf);

        if (nonLeaf.insert(key, pid) == 0)
        {
            nonLeaf.write(parentPid, pf);
            return 0;
        } 

        BTNonLeafNode nonLeaf_sib;
        int sibKey;
        PageId sibPid;
        sibPid = pf.endPid();
        int midKey;
        nonLeaf.insertAndSplit(key, pid, nonLeaf_sib, midKey);
        nonLeaf.write(parentPid, pf);
        nonLeaf_sib.write(sibPid, pf);

        int grandParent=-1;
        grandParent = nonLeaf.getParent();
        if(grandParent==-1)
        {
            grandParent=pf.endPid();
            rootPid = grandParent;
            treeHeight++;

            BTNonLeafNode root;
            root.initializeRoot(parentPid, midKey, sibPid);
            root.write(rootPid, pf);
            return 0;
        }

        insertParent(grandParent, midKey, sibPid);
}

/**
 * Run the standard B+Tree key search algorithm and identify the
 * leaf node where searchKey may exist. If an index entry with
 * searchKey exists in the leaf node, set IndexCursor to its location
 * (i.e., IndexCursor.pid = PageId of the leaf node, and
 * IndexCursor.eid = the searchKey index entry number.) and return 0.
 * If not, set IndexCursor.pid = PageId of the leaf node and
 * IndexCursor.eid = the index entry immediately after the largest
 * index key that is smaller than searchKey, and return the error
 * code RC_NO_SUCH_RECORD.
 * Using the returned "IndexCursor", you will have to call readForward()
 * to retrieve the actual (key, rid) pair from the index.
 * @param key[IN] the key to find
 * @param cursor[OUT] the cursor pointing to the index entry with
 *                    searchKey or immediately behind the largest key
 *                    smaller than searchKey.
 * @return 0 if searchKey is found. Othewise an error code
 */
RC BTreeIndex::locate(int searchKey, IndexCursor& cursor)
{
    RC rc;
    if (rootPid == -1){
        tempRootPid = 1;
    }
    else{
        tempRootPid = rootPid;
    }

    currentHeight = 1;
    rc = locateRecursion(searchKey, cursor);
    return rc;
}

RC BTreeIndex::locateRecursion(int searchKey, IndexCursor& cursor)
{
    RC rc;
    if (currentHeight != treeHeight)
    {
        BTNonLeafNode nonLeaf;
        nonLeaf.read(tempRootPid, pf);
        nonLeaf.locateChildPtr(searchKey, tempRootPid);
        currentHeight++;
        locateRecursion(searchKey, cursor);
    }
    else
    {
        BTLeafNode leaf;
        leaf.read(tempRootPid, pf);
        int eid;
        rc=leaf.locate(searchKey, eid);

        cursor.pid = tempRootPid;
        cursor.eid = eid;

        if (rc == 0)
        {
            return 0;
        }
        else
        {
            return RC_NO_SUCH_RECORD;
        }
    }

}

/*
 * Read the (key, rid) pair at the location specified by the index cursor,
 * and move foward the cursor to the next entry.
 * @param cursor[IN/OUT] the cursor pointing to an leaf-node index entry in the b+tree
 * @param key[OUT] the key stored at the index cursor location.
 * @param rid[OUT] the RecordId stored at the index cursor location.
 * @return error code. 0 if no error
 */
RC BTreeIndex::readForward(IndexCursor& cursor, int& key, RecordId& rid)
{
    RC rc;
    if(cursor.pid <= 0 || cursor.eid <0)
    {
        return RC_INVALID_CURSOR;
    }

    BTLeafNode leaf;
    rc=leaf.read(cursor.pid, pf);
    if (rc!=0)
    {
        return rc;
    }

    leaf.readEntry(cursor.eid, key, rid);
    if (rc!=0)
    {
        return rc;
    }

    if(cursor.eid >= leaf.getKeyCount()-1)
    {
        cursor.eid = 0;
        cursor.pid = leaf.getNextNodePtr();
    }
    else
    {
        cursor.eid++;
    }

    return 0;
}