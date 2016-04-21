1. OPTIMIZATION, WHEN TO USE B+ TREE:
-- Following Spec: when there exists any non-NE key condition in WHERE
-- when there is no conditions in WHERE and the SELECT is either on key (i.e., attr = 1) or on count(*) (i.e., attr = 4)
   example for this optimization: SELECT key FROM medium
   Without B+ tree,  read 14 pages
   With B+ tree, read 5 pages
