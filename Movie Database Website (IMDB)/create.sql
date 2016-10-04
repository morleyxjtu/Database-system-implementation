
CREATE TABLE Movie(id int NOT NULL, title varchar(100) NOT NULL, year int, rating varchar(10), company varchar(50), PRIMARY KEY (id), CHECK (year > 0 AND year <= 2016)) ENGINE=InnoDB;
# Every movie has a unique, non-null ID number (primary key)
# Every movie must have a title
# Movie year should be bigger than 0 and smaller than 2016 (check constraint)

CREATE TABLE Actor(id int NOT NULL, last varchar(20), first varchar(20), sex varchar(6), dob date NOT NULL, dod date, PRIMARY KEY (id), CHECK(dod IS NULL OR dob<dod)) ENGINE=InnoDB;
# Every actor has a unique, non-null ID number (primary key)
# Every actor must have a date of birth
# data of birth should be earlier than date of death if person is still alive (check constraint)

CREATE TABLE Director(id int NOT NULL, last varchar(20), first varchar(20), dob date NOT NULL, dod date, PRIMARY KEY (id), CHECK(dod IS NULL OR dob<dod)) ENGINE=InnoDB;
# Every director has a unique ID number (primary key)
# Every director must have a date of birth
# data of birth should be earlier than date of death if person is still alive (check constraint)

CREATE TABLE MovieGenre(mid int NOT NULL, genre varchar(20), FOREIGN KEY (mid) REFERENCES Movie (id)) ENGINE=InnoDB;
# mid in MovieGenre must refer to id in Movie (referential intergrity)

CREATE TABLE MovieDirector(mid int NOT NULL, did int, PRIMARY KEY (mid, did), FOREIGN KEY (mid) REFERENCES Movie (id), FOREIGN KEY (did) REFERENCES Director (id)) ENGINE=InnoDB;
# movie and director combination is unique and non-NULL
# mid in MovieDirector must refer to id in Movie (referential intergrity)
# did in MovieDirector must refer to id in Director (referential intergrity)

CREATE TABLE MovieActor(mid int NOT NULL, aid int, role varchar(50), FOREIGN KEY (mid) REFERENCES Movie (id), FOREIGN KEY (aid) REFERENCES Actor (id)) ENGINE=InnoDB;
# mid in MovieActor must refer to id in Movie (referential intergrity)
# aid in MovieActor must refer to id in Actor (referential intergrity)

CREATE TABLE Review(name varchar(20), time timestamp, mid int NOT NULL, rating int, comment varchar(500), FOREIGN KEY (mid) REFERENCES Movie (id)) ENGINE=InnoDB;
# mid refer to id in Movie (referential intergrity)

CREATE TABLE MaxPersonID(id int NOT NULL) ENGINE=InnoDB;

CREATE TABLE MaxMovieID(id int NOT NULL) ENGINE=InnoDB;

