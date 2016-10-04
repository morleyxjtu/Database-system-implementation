Goal:
Implement B+ tree to handle SELECT queries to reduces the number of I/O operations 
   
Example for optimization: 
Query: 'SELECT key FROM medium'
Without B+ tree,  read 14 pages
With B+ tree, read 5 pages

SqlEngine.cc, BTreeNode.h, BTreeNode.cc, BTreeIndex.h and BTreeIndex.cc are done by myself. The rest files provided by the class.
