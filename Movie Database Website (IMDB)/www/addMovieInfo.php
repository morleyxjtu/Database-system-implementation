<html>
<head>
	<title>CS143 Movie Database_Muchen Xu</title>
</head>

<body style="margin: 0px; padding: 0px; font-family: 'Trebuchet MS',verdana;">

<table width="100%" style="height: 100%;" cellpadding="10" cellspacing="0" border="0">

<tr>
<td colspan="2" style="height: 100px;" color= white bgcolor="#848484" align="center">
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
	<form method = "GET" action = "addMovieInfo.php">
		<p> * are required items</p>
		<p>Title: <input type="text" name="title" required>*</p>
		<p>Company: <input type="text" name="company"></p>
		<p>Year: <input type="text" name="year"></p>
		MPAA Rating: <select name="rating" id = "rating">
			<option value="N">Unknown</option>
			<option value="G">G</option>
			<option value="NC-17">NC-17</option>
  			<option value="PG">PG</option>
  			<option value="PG-13">PG-13</option>
  			<option value="R">R</option>	
		</select>
		<p>Genre:<br>
			<input type="checkbox" name="genre[]" id="genre" value="Action"> Action 
  			<input type="checkbox" name="genre[]" id="genre" value="Adult"> Adult 
  			<input type="checkbox" name="genre[]" id="genre" value="Adventure"> Adventure 
  			<input type="checkbox" name="genre[]" id="genre" value="Animation"> Animation 
  			<input type="checkbox" name="genre[]" id="genre" value="Comedy"> Comedy 
  			<input type="checkbox" name="genre[]" id="genre" value="Crime"> Crime 
  			<input type="checkbox" name="genre[]" id="genre" value="Documentary"> Documentary 
  			<input type="checkbox" name="genre[]" id="genre" value="Drama"> Drama 
  			<input type="checkbox" name="genre[]" id="genre" value="Family"> Family 
  			<input type="checkbox" name="genre[]" id="genre" value="Fantasy"> Fantasy 
  			<input type="checkbox" name="genre[]" id="genre" value="Horror"> Horror 
  			<input type="checkbox" name="genre[]" id="genre" value="Musical"> Musical 
  			<input type="checkbox" name="genre[]" id="genre" value="Mystery"> Mystery 
  			<input type="checkbox" name="genre[]" id="genre" value="Romance"> Romance 
  			<input type="checkbox" name="genre[]" id="genre" value="Sci-Fi"> Sci-Fi 
  			<input type="checkbox" name="genre[]" id="genre" value="Short"> Short 
  			<input type="checkbox" name="genre[]" id="genre" value="Thriller"> Thriller 
  			<input type="checkbox" name="genre[]" id="genre" value="War"> War 
  			<input type="checkbox" name="genre[]" id="genre" value="Western"> Western <br>
  			<input type="submit" value="Add">
		</p>

	</form>

	<?php
		if($_GET['title']){
			$db_connection = mysql_connect("localhost", "cs143", "");
		    mysql_select_db("CS143", $db_connection);
		    $title = $_GET['title'];
		    $company = $_GET['company'];
		    $company = !empty($company) ? "'$company'" : "NULL";
		    $year = $_GET['year'];
		    $year = !empty($year) ? "'$year'" : "NULL";
		    $rating = $_GET['rating'];
		    if($rating == "N"){$rating = 'NULL';};

		    $query_max = 'SELECT * FROM MaxMovieID';
		    $rs = mysql_query($query_max, $db_connection);
		    $maxMID = mysql_fetch_row($rs);

		    $query_insert_movie = 'INSERT INTO Movie VALUES ('.($maxMID[0]+1).',"'.$title.'",'.$year.',"'.$rating.'",'.$company.')';
		    mysql_query($query_insert_movie, $db_connection);
	    	$error = mysql_error();
	    	if ($error != '' ) {
				print '<strong>An error occurred!</strong> ' . $error;
				mysql_close($db_connection);
				exit();
			}
		    $genre_name = $_GET['genre'];
		    $genre = !empty($genre) ? "'$genre'" : "NULL";
		    foreach ($genre_name as $key) {
		    	$query_insert_genre = 'INSERT INTO MovieGenre VALUES ('.($maxMID[0]+1).',"'.$key.'")';
		    	mysql_query($query_insert_genre, $db_connection);
		    	$error = mysql_error();
		    	if ($error != '' ) {
					print '<strong>An error occurred!</strong> ' . $error;
					mysql_close($db_connection);
					exit();
				}
		    }
		    $query_max = 'UPDATE MaxMovieID SET id = '.($maxMID[0]+1);
			mysql_query($query_max, $db_connection);
			mysql_close($db_connection);
			echo "Data submitted!";
		}
	?>
</td>
</tr>
</table>
</body>
</html>