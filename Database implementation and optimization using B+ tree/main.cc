/**
 * Copyright (C) 2008 by The Regents of the University of California
 * Redistribution of this file is permitted under the terms of the GNU
 * Public License (GPL).
 *
 * @author Junghoo "John" Cho <cho AT cs.ucla.edu>
 * @date 3/24/2008
 */
 
#include "Bruinbase.h"
#include "SqlEngine.h"
#include <cstdio>

#include "BTreeNode.h"
#include <iostream>
#include <cstring>

using namespace std;

int main()
{
  // run the SQL engine taking user commands from standard input (console).
  SqlEngine::run(stdin);
/*
  //P2B test: BTreeNode.cc

  //leaf node
  BTLeafNode test;
  int test_pid = 0;
  PageFile pf;
  string a="test";

  pf.open(a, 'w');
  cout<<"end pid "<<pf.endPid()<<endl;
  //test getKeyCount() and insert()
  cout<<"initial key number"<<test.getKeyCount()<<endl;
  int key=0;
  int rc;
  for (int i=0; i<90; i++)
  {
    RecordId rid={key+1, key+2};
    rc=test.insert(key, rid);
    if (rc!=0)
    {
      cout<<i<<" "<<rc<<endl;
    }
    key+=10;
  }
  cout<<"final key number"<<test.getKeyCount()<<endl;

  test.setNextNodePtr(10);
  cout<<test.getNextNodePtr()<<endl;
  test.write(test_pid, pf);


  //test insertAndSplit();
  BTLeafNode sibling;
  int sibling_pid=pf.endPid();
  int siblingKey;
  test.insertAndSplit(425, ((RecordId){426,427}), sibling, siblingKey);
  cout <<test.getKeyCount()<<" "<<sibling.getKeyCount()<<endl;
  sibling.write(sibling_pid,pf);
  test.setNextNodePtr(sibling_pid);
  test.write(test_pid, pf);
  cout<<"end pid "<<pf.endPid()<<endl;
  cout<<"first node end pid: "<<test.getNextNodePtr()<<endl;
  cout<<"second node end pid: "<<sibling.getNextNodePtr()<<endl;
  //test locate()
  int eid;
  rc=test.locate(15, eid);
  cout<<rc<<" "<<eid<<endl;
  rc=test.locate(10, eid);
  cout<<rc<<" "<<eid<<endl;

  //test readEntry()
  eid=9;
  RecordId rid;
  test.readEntry(eid, key, rid);
  cout<<key<<" "<<rid.pid<<" "<<rid.sid<<endl;
*/
/*
  //non-leaf node
  BTNonLeafNode test2;
  int test2_pid = 100;
  PageFile pf;
  //test getKeyCount() and insert()
  cout<<test2.getKeyCount()<<endl;

  test2.initializeRoot(0,425,1);
  test2.insert(500, 10);
  cout<<test2.getKeyCount()<<endl;

  int pid;
  test2.locateChildPtr(426, pid);
  cout<< pid<<endl;

  int key=0;
  int rc;
  for (int i=0; i<125; i++)
  {
    pid=key+1;
    rc=test2.insert(key, pid);
    if (rc!=0)
    {
      cout<<i<<" "<<rc<<endl;
    }
    key+=10;
  }
  cout<<"final key number"<<test2.getKeyCount()<<endl;

  BTNonLeafNode sibling;
  int midKey;
  test2.insertAndSplit(600, 601, sibling, midKey);
  cout <<test2.getKeyCount()<<" "<<sibling.getKeyCount()<<endl;
  cout<<midKey<<endl;
*/
  return 0;

}
