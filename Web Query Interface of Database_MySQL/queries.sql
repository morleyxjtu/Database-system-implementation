SELECT CONCAT(first, " ", last) AS name FROM Actor, MovieActor, Movie WHERE Actor.id = MovieActor.aid AND MovieActor.mid = Movie.id AND Movie.title = 'Die Another Day';
#required queries: Give me the names of all the actors in the movie 'Die Another Day'

SELECT COUNT(aid) FROM (SELECT aid FROM MovieActor GROUP BY aid HAVING COUNT(*)>1) S;
#required queries: Give me the count of all the actors who acted in multiple movies

SELECT title FROM Movie WHERE year >=2003;
#my query: give the title of the movies after 2003 (include 2003)