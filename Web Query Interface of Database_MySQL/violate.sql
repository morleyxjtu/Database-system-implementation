
INSERT INTO Movie VALUES (2, 'The Martian', 2015, NULL, '20th Century Fox');
# Primary key constraint violation "Every movie has a unique, non-null ID number"
# Because tuple with id=2 already exist, insert another tuple with id=2 violates the uniqueness of id attribute
# Error message "ERROR 1062 (23000): Duplicate entry '2' for key 'PRIMARY'"

INSERT INTO Movie VALUES (5000, NULL, NULL, NULL, NULL);
# Non-null constraint violation "Every movie must have a non-NULL title"
# Because the inerted tuple has NULL title
# Error message "ERROR 1048 (23000): Column 'title' cannot be null"

UPDATE Movie SET year = 0 WHERE id = 2;
# Check constraint violation "Movie year should be bigger than 0 and smaller than 2016"
# Because the year for tuple with id=2 is updated to 0

INSERT INTO Actor VALUES (10, NULL, NULL, NULL, '19900101', NULL);
# Primary key constraint violation "Every actor has a unique, non-null ID number"
# Because tuple with id=10 already exist, insert another tuple with id=2 violates the uniqueness of id attribute
# Error message "ERROR 1062 (23000): Duplicate entry '10' for key 'PRIMARY'"

INSERT INTO Actor VALUES (1, NULL, NULL, NULL, NULL, NULL);
# Non-null constraint violation "Every actor must have a date of birth"
# Because inserted tuple has dob as NULL
# Error message "ERROR 1048 (23000): Column 'dob' cannot be null"

UPDATE Actor SET dob = '19900101', dod = '19800101' WHERE id = 2;
# Check constraint violation "data of birth should be earlier than date of death if person is still alive"
# Because dob 1990-01-01 is later than dod 1980-01-01

INSERT INTO Director VALUES (37390, NULL, NULL, '19950101', NULL);
# Primary key constraint violation "Every director has a unique, non-null ID number"
# Because tuple with id=3790 already exist, insert another tuple with id=3 violates the uniqueness of id attribute
# Error message "ERROR 1062 (23000): Duplicate entry '37390' for key 'PRIMARY'"

INSERT INTO Director VALUES (1, NULL, NULL, NULL, NULL);
# Non-null constraint violation "Every actor must have a date of birth"
# Because the dob for tuple with id=10 is updated to NULL
# Error message "ERROR 1048 (23000): Column 'dob' cannot be null"

UPDATE Director SET dob = '19950101', dod = '19700301' WHERE id = 2;
# Check constraint violation "data of birth should be earlier than date of death if person is still alive"
# Because dob 1995-01-01 is later than dod 1970-03-01

INSERT INTO MovieGenre VALUES (0, 'Drama');
# Referential intergrity constraint violation "mid in MovieGenre refers to id in Movie"
# mid in MovieGenre is foreign key which must refer to a valid id in Movie
# Error message "ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieGenre`, CONSTRAINT `MovieGenre_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))"

INSERT INTO MovieDirector VALUES (2832, 68623);
# Primary key constraint violation "movie and director combination is unique and non-NULL"
# Because tuple with mid=2832 did =68623 already exist, insert another same tuple violates the uniqueness of mid attribute
# Error message "ERROR 1062 (23000): Duplicate entry '2832-68623' for key 'PRIMARY'"

INSERT INTO MovieDirector VALUES (0, 1);
# Referential intergrity constraint violation "mid in MovieDirector refers to id in Movie"
# mid in MovieDirector is foreign key which must refer to a valid id in Movie
# Error message "ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieDirector`, CONSTRAINT `MovieDirector_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))"

UPDATE MovieDirector SET did = 0 WHERE mid = 71;
# Referential intergrity constraint violation "did in MovieDirector refers to id in Director"
# did in MovieDirector is foreign key which must refer to a valid id in Director
# Error message "ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieDirector`, CONSTRAINT `MovieDirector_ibfk_2` FOREIGN KEY (`did`) REFERENCES `Director` (`id`))"

UPDATE MovieActor SET mid = 0 WHERE aid = 113;
# Referential intergrity constraint violation "mid in MovieActor refers to id in Movie"
# mid in MovieActor is foreign key which must refer to a valid id in Movie
# Error message "ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieActor`, CONSTRAINT `MovieActor_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))"

UPDATE MovieActor SET aid = 0 WHERE mid = 71;
# Referential intergrity constraint violation "did in MovieActor refers to id in Director"
# aid in MovieActor is foreign key which must refer to a valid id in Actor
# Error message "ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`MovieActor`, CONSTRAINT `MovieActor_ibfk_2` FOREIGN KEY (`aid`) REFERENCES `Actor` (`id`))"

INSERT INTO Review VALUES (NULL, NULL, 0, NULL, NULL);
# Referential intergrity constraint violation "mid in Review refers to id in Movie"
# mid in Review is foreign key which must refer to a valid id in Movie
# Error message "ERROR 1452 (23000): Cannot add or update a child row: a foreign key constraint fails (`CS143`.`Review`, CONSTRAINT `Review_ibfk_1` FOREIGN KEY (`mid`) REFERENCES `Movie` (`id`))"
