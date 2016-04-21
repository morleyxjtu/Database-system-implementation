/**
 * Copyright (C) 2008 by The Regents of the University of California
 * Redistribution of this file is permitted under the terms of the GNU
 * Public License (GPL).
 *
 * @author Junghoo "John" Cho <cho AT cs.ucla.edu>
 * @date 3/24/2008
 */

#include <cstdio>
#include <cstring>
#include <cstdlib>
#include <iostream>
#include <fstream>
#include "Bruinbase.h"
#include "SqlEngine.h"
#include "RecordFile.h"
#include "BTreeIndex.h"

using namespace std;

// external functions and variables for load file and sql command parsing 
extern FILE* sqlin;
int sqlparse(void);


RC SqlEngine::run(FILE* commandline)
{
  fprintf(stdout, "Bruinbase> ");

  // set the command line input and start parsing user input
  sqlin = commandline;
  sqlparse();  // sqlparse() is defined in SqlParser.tab.c generated from
               // SqlParser.y by bison (bison is GNU equivalent of yacc)

  return 0;
}

RC SqlEngine::select(int attr, const string& table, const vector<SelCond>& cond)
{
  RecordFile rf;   // RecordFile containing the table
  RecordId   rid;  // record cursor for table scanning
  RecordId maxRid;
  BTreeIndex btree;

  RC     rc;
  int    key;  
  int    maxKey;   
  string value;
  int    count = 0;
  int    diff;

  int equal;
  int min;
  int max;

  bool treeCon = false;  
  bool valueCon = false;
  bool key_eq = false;
  bool key_ne = false;
  bool key_gt = false;
  bool key_lt = false;
  bool key_ge = false;
  bool key_le = false;


  IndexCursor cursor;
  IndexCursor maxCursor;

  // open the table file
  if ((rc = rf.open(table + ".tbl", 'r')) < 0) {
    fprintf(stderr, "Error: table %s does not exist\n", table.c_str());
    goto exit_select;
  }

  //analyse condition
  for(unsigned i = 0; i < cond.size(); i++)
  {
    //if there exists on or more conditions on key
    if (cond[i].attr ==1)
    {
      switch(cond[i].comp)
      {
        case SelCond::EQ:
          if(key_eq && atoi(cond[i].value) != equal)
          {
            fprintf(stderr, "Error: contradictory conditions in WHERE");
            goto exit_select;
          }
          key_eq = true;
          treeCon = true;
          equal = atoi(cond[i].value);
          break;

        case SelCond::NE:
          key_ne = true;
          break;

        case SelCond::GT:
          if(key_gt || key_ge)
          {
            if(atoi(cond[i].value)+1>min)
            {
              min = atoi(cond[i].value)+1;
            }
          }
          else
          {
            min = atoi(cond[i].value)+1;
          }
          key_gt = true;
          treeCon = true;
          break;

        case SelCond::LT:
          if(key_lt || key_le)
          {
            if(atoi(cond[i].value)-1<max)
            {
              max = atoi(cond[i].value)-1;
            }
          }
          else
          {
            max = atoi(cond[i].value)-1;
          }
          key_lt = true;
          treeCon = true;
          break;
    
        case SelCond::GE:
          if(key_gt || key_ge)
          {
            if(atoi(cond[i].value)>min)
            {
              min = atoi(cond[i].value);
            }
          }
          else
          {
            min = atoi(cond[i].value);
          }
          key_ge = true;
          treeCon = true;
          break;

        case SelCond::LE:
          if(key_lt || key_le)
          {
            if(atoi(cond[i].value)<max)
            {
              max = atoi(cond[i].value);
            }
          }
          else
          {
            max = atoi(cond[i].value);
          }
          key_le = true;
          treeCon = true;
          break;
      }
    }
    else //no conditions on key. Go to no-index method
    {
      valueCon = true;
    }
  }

  //cout<<"min"<<min<<" max"<<max<<endl;


  //check key condition confliction
  if(key_eq && (key_gt || key_ge)){
    if (equal < min){
      fprintf(stderr, "Error: contradictory conditions");
      goto exit_select;
    }
  }
  if(key_eq && (key_lt || key_le)){
    if (equal > max){
      fprintf(stderr, "Error: contradictory conditions");
      goto exit_select;
    }
  }
  if((key_gt || key_ge) && (key_lt || key_le)){
    if (min > max){
      fprintf(stderr, "Error: contradictory conditions");
      goto exit_select;
    }
  }

  if (attr == 4 && cond.size() ==0){
    treeCon = true;
  }

  if(attr == 1 && cond.size() == 0){
    treeCon = true;
  }

  if(btree.open(table + ".idx", 'r') ==0 && treeCon) //B tree exists AND one or more non-NE conditions
  {
    
    //cout<<"new select"<<endl;
    if (key_eq)
    {
      rc = btree.locate(equal, cursor);
      if(rc!=0){
       goto exit_btree;
      }
    }
    else if (key_gt || key_ge)//if key conditions contain GT or GE but not equal
    {
      btree.locate(min, cursor); //locate the min in B tree
    }
    else //no GT or GE, locate 0 in B tree
    {
      btree.locate(0, cursor);
      //cout<<"min = 0"<<endl;
      //cout<<"PID"<<cursor.pid<<" EID"<<cursor.eid<<endl;
    }

    if (key_lt || key_le) //if key conditions contain LT or LE but not equal
    {
      btree.locate(max, maxCursor);
      btree.readForward(maxCursor, maxKey, maxRid); //locate the max in B tree and read the rid
    }

    while(btree.readForward(cursor, key, rid)==0) //start reading keys in B tree leaf from min or 0
    {
      //cout<<"SID"<<rid.sid<<endl;
      if (key < min && (key_gt || key_ge)){
        goto next_while;
      }
      if(valueCon){
        if((rc = rf.read(rid, key, value)) != 0){
          goto exit_btree;
        }
      }
      for (unsigned i = 0; i < cond.size(); i++) {
      // compute the difference between the tuple value and the condition value
        switch (cond[i].attr) {
        case 1:
          diff = key - atoi(cond[i].value);
          break;
        case 2:
          diff = strcmp(value.c_str(), cond[i].value);
          break;
        }

        switch (cond[i].comp) {
        case SelCond::EQ:
          if (diff != 0) goto next_while;
          break;
        case SelCond::NE:
          if (diff == 0) goto next_while;
          break;
        case SelCond::GT:
          if (diff <= 0) goto next_while;
          break;
        case SelCond::LT:
          if (diff >= 0) goto next_while;
          break;
        case SelCond::GE:
          if (diff < 0) goto next_while;
          break;
        case SelCond::LE:
          if (diff > 0) goto next_while;
          break;
        }
      }
        // the condition is met for the tuple. 
        // increase matching tuple counter
        count++;
        //cout<<"attr is: "<<attr<<endl;

        switch (attr) {
        case 1:  // SELECT key
          fprintf(stdout, "%d\n", key);

          break;
        case 2:  // SELECT value
          if (!valueCon){
            if((rc = rf.read(rid, key, value)) != 0){
              goto exit_btree;
            }
          }
          fprintf(stdout, "%s\n", value.c_str());
          break;
        case 3:  // SELECT *
          if (!valueCon){
            if((rc = rf.read(rid, key, value)) != 0){
              goto exit_btree;
            }
          }
          fprintf(stdout, "%d '%s'\n", key, value.c_str());
          break;
        }
      

      next_while:

      if(key_eq){
        break;
      }

      if((key_lt || key_le) && key > maxKey){
        break;
      }

    }
    if (attr == 4) {
      fprintf(stdout, "%d\n", count);
    }
  }
  else
  {
    //cout<<"old select"<<endl;
      // scan the table file from the beginning
    rid.pid = rid.sid = 0;
    count = 0;
    while (rid < rf.endRid()) {
      // read the tuple
      if ((rc = rf.read(rid, key, value)) < 0) {
        fprintf(stderr, "Error: while reading a tuple from table %s\n", table.c_str());
        goto exit_select;
      }

      // check the conditions on the tuple
      for (unsigned i = 0; i < cond.size(); i++) {
        // compute the difference between the tuple value and the condition value
        switch (cond[i].attr) {
        case 1:
          diff = key - atoi(cond[i].value);
          break;
        case 2:
          diff = strcmp(value.c_str(), cond[i].value);
          break;
        }

        // skip the tuple if any condition is not met
        switch (cond[i].comp) {
        case SelCond::EQ:
          if (diff != 0) goto next_tuple;
          break;
        case SelCond::NE:
          if (diff == 0) goto next_tuple;
          break;
        case SelCond::GT:
          if (diff <= 0) goto next_tuple;
          break;
        case SelCond::LT:
          if (diff >= 0) goto next_tuple;
          break;
        case SelCond::GE:
          if (diff < 0) goto next_tuple;
          break;
        case SelCond::LE:
          if (diff > 0) goto next_tuple;
          break;
        }
      }

      // the condition is met for the tuple. 
      // increase matching tuple counter
      count++;

      // print the tuple 
      switch (attr) {
      case 1:  // SELECT key
        fprintf(stdout, "%d\n", key);
        break;
      case 2:  // SELECT value
        fprintf(stdout, "%s\n", value.c_str());
        break;
      case 3:  // SELECT *
        fprintf(stdout, "%d '%s'\n", key, value.c_str());
        break;
      }
      // move to the next tuple
      next_tuple:
      ++rid;
    }

    // print matching tuple count if "select count(*)"
    if (attr == 4) {
      fprintf(stdout, "%d\n", count);
    }
  }

  rc = 0;

  exit_btree:
  btree.close();  
  // close the table file and return
  exit_select:
  rf.close();
  return rc;
}

RC SqlEngine::load(const string& table, const string& loadfile, bool index)
{
  /* your code here */
  RecordFile rf;   // RecordFile containing the table
  RecordId   rid;  // record cursor for table scanning
  RC rc; //error code
  BTreeIndex btree;

  string line;
  int key;
  string value;
  
  if((rc = rf.open(table + ".tbl", 'w')) != 0){
    return rc;
  } // open the table file

  ifstream file(loadfile.c_str()); //open the data file

  if (file.is_open()) 
  {
    if(index)
    {
      if((rc = btree.open(table + ".idx", 'w')) != 0){
        goto exit_index;
      }

      while (getline(file, line)) 
      {
        parseLoadLine(line, key, value);
        if((rc = rf.append(key, value, rid)) != 0){
          goto exit_index;
        }

        if((rc = btree.insert(key, rid)) != 0){
          goto exit_index;
        }
      }
    }
    else
    {
      while (getline(file, line)) 
      {
        parseLoadLine(line, key, value);
        if((rc = rf.append(key, value, rid)) != 0){
          goto exit_file;
        }
      }
    }
    
    rc = 0;
    exit_index:
    btree.close();
    exit_file:
    rf.close();
    file.close();
  }
  else 
  {
    return RC_FILE_OPEN_FAILED;
  }
  
  return rc;
}

RC SqlEngine::parseLoadLine(const string& line, int& key, string& value)
{
    const char *s;
    char        c;
    string::size_type loc;
    
    // ignore beginning white spaces
    c = *(s = line.c_str());
    while (c == ' ' || c == '\t') { c = *++s; }

    // get the integer key value
    key = atoi(s);

    // look for comma
    s = strchr(s, ',');
    if (s == NULL) { return RC_INVALID_FILE_FORMAT; }

    // ignore white spaces
    do { c = *++s; } while (c == ' ' || c == '\t');
    
    // if there is nothing left, set the value to empty string
    if (c == 0) { 
        value.erase();
        return 0;
    }

    // is the value field delimited by ' or "?
    if (c == '\'' || c == '"') {
        s++;
    } else {
        c = '\n';
    }

    // get the value string
    value.assign(s);
    loc = value.find(c, 0);
    if (loc != string::npos) { value.erase(loc); }

    return 0;
}
