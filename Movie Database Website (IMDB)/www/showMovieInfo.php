<html>
<head>
	<title>CS143 Movie Database_Muchen Xu</title>
</head>

<body style="margin: 0px; padding: 0px; font-family: 'Trebuchet MS',verdana;">

<table width="100%" style="height: 100%;" cellpadding="10" cellspacing="0" border="0">

<tr><td colspan="2" style="height: 100px;" color= white bgcolor="#848484" align="center">
	<h2><a href="./index.php">CS143-MDB</a></h2>
	<form method="GET" action="search.php">
  		Movie/Actor <input type="text" name="fname">
  		<input type="submit" value="Search">
	</form>
</td></tr>

<tr>
<td width="20%" valign="top" bgcolor="#999f8e">
	<h4> Contribute New Content : </h4>
	<ul>
  		<li><a href="./addActorDirector.php">Add Actor/Director Information</a></li>
  		<li><a href="./addMovieInfo.php">Add Movie Information</a></li>
  		<li><a href="./addReview.php">Add Comments to Movies</a></li>
  		<li><a href="./addMovieActor.php">Add Actor to Movie</a></li>
  		<li><a href="./addMovieDirector.php">Add Director to Movie</a></li>
	</ul>
</td>

<td width="80%" valign="top" bgcolor="#d2d8c7">
	<?php
	if($_GET['mid']){
		$db_connection = mysql_connect("localhost", "cs143", "");
	    mysql_select_db("CS143", $db_connection);
	    $mid = $_GET['mid'];
	    $query_movie = 'SELECT * FROM Movie WHERE id ='.$mid;
	    $rs = mysql_query($query_movie, $db_connection);
	    $error = mysql_error();
    	if ($error != '' ) {
			print '<strong>An error occurred!</strong> ' . $error;
			mysql_close($db_connection);
			exit();
		}
	    if (mysql_num_rows($rs) != 0){
	    	echo "Hellon";
		    while($row = mysql_fetch_row($rs)) {
		    	echo '<h3>Movie Information:</h3>';
		    	$title = $row[1];	
		    	$year = $row[2];
		    	$rating = $row[3];
		    	$company = $row[4];
		    	echo '<h4>Titile: </h4>'.$title. ' ('. $year.')';
		    	echo '<h4>MPAA Rating: </h4>'.$rating.'<br>';
		    	echo '<h4>Company: </h4>'.$company.'<br>';
		    }

		    echo '<h4>Genre: </h4>';
		    $query_genre = 'SELECT * FROM MovieGenre WHERE mid ='.$mid;
		    $rs = mysql_query($query_genre, $db_connection);
		    $error = mysql_error();
	    	if ($error != '' ) {
				print '<strong>An error occurred!</strong> ' . $error;
				mysql_close($db_connection);
				exit();
			}
	    	while($row = mysql_fetch_row($rs)) {
		    	$genre = $row[1];
		    	echo $genre.' ';
		    }
		    echo '<br>';
		    echo '<br>';

		    echo '<h4>Director:</h4><br>';
		    $query_director = 'SELECT id, first, last FROM MovieDirector, Director WHERE mid ='.$mid.' AND did = id';
		    $rs = mysql_query($query_director, $db_connection);
		    $error = mysql_error();
	    	if ($error != '' ) {
				print '<strong>An error occurred!</strong> ' . $error;
				mysql_close($db_connection);
				exit();
			}
		    while($row = mysql_fetch_row($rs)) {
		    	echo $row[1]. ' '. $row[2]. '</a><br>';
		    }
		    echo '<br>';

		    echo '<h4>Actor:</h4><br>';
		    $query_actor = 'SELECT id, first, last, role FROM MovieActor, Actor WHERE mid ='.$mid.' AND aid = id';
		    $rs = mysql_query($query_actor, $db_connection);
		    $error = mysql_error();
	    	if ($error != '' ) {
				print '<strong>An error occurred!</strong> ' . $error;
				mysql_close($db_connection);
				exit();
			}
		    while($row = mysql_fetch_row($rs)) {
		    	echo '<a href = "./showActorInfo.php?aid='.$row[0].'">'.$row[1]. ' '. $row[2]. '</a> act as '.$row[3].'<br>';
		    }

		    echo '<h4>Review:</h4><br>';
		    $query_review_avg = 'SELECT AVG(rating), COUNT(rating) FROM Review WHERE mid ='.$mid;
		    $query_review = 'SELECT * FROM Review WHERE mid ='.$mid;
		    $rs_avg = mysql_query($query_review_avg, $db_connection);
		    $error = mysql_error();
	    	if ($error != '' ) {
				print '<strong>An error occurred!</strong> ' . $error;
				mysql_close($db_connection);
				exit();
			}
		    while($row = mysql_fetch_row($rs_avg)) {
		    	echo 'Average Score: '.$row[0].'/5 (5.0 is the best) by '.$row[1].' reviews';
		    }
		    echo '<a href = "./addReview.php?mid='.$mid.'"> Add your review!</a><br>';
		    $rs = mysql_query($query_review, $db_connection);
		    $error = mysql_error();
	    	if ($error != '' ) {
				print '<strong>An error occurred!</strong> ' . $error;
				mysql_close($db_connection);
				exit();
			}
		    while($row = mysql_fetch_row($rs)) {
		    	echo 'At '.$row[1].', '.$row[0].' rated this movie '.$row[3].' Comment: '.$row[4].'<br>';
		    }
		}else{
			echo "No information on this movie yet!";
		}
        mysql_close($db_connection);
	}
	?>

</td></tr>
</table>
</body>
</html>