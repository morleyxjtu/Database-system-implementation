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
  		<input type="submit" valu="Search">
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
	if($_GET['aid']){
		$db_connection = mysql_connect("localhost", "cs143", "");
	    mysql_select_db("CS143", $db_connection);
	    $aid = $_GET['aid'];
	    $query_actor = 'SELECT * FROM Actor WHERE id ='.$aid;
	    $rs = mysql_query($query_actor, $db_connection);
		$error = mysql_error();
    	if ($error != '' ) {
			print '<strong>An error occurred!</strong> ' . $error;
			mysql_close($db_connection);
			exit();
		}
	    while($row = mysql_fetch_row($rs)) {
	    	echo '<h4>Actor Information:</h4>';
	    	echo '<h4>Name: </h4>'.$row[2].' '.$row[1].'<br>';
	    	echo '<h4>Sex: </h4>'.$row[3].'<br>';
	    	echo '<h4>Date of birth: </h4>'.$row[4].'<br>';
	    	$dod = !empty($row[5]) ? "'$row[5]'" : "Still alive";
	    	echo '<h4>Date of death: </h4>'.$dod.'<br>';
	    }

	    echo '<h4>Act in:</h4><br>';
	    $query_movie = 'SELECT id, title, role FROM MovieActor, Movie WHERE aid ='.$aid.' AND mid = id';
	    $rs = mysql_query($query_movie, $db_connection);
	    $error = mysql_error();
    	if ($error != '' ) {
			print '<strong>An error occurred!</strong> ' . $error;
			mysql_close($db_connection);
			exit();
		}
	    while($row = mysql_fetch_row($rs)) {
	    	echo 'In <a href = "./showMovieInfo.php?mid='.$row[0].'">'.$row[1].'</a> acted as '.$row[2].'<br>';
	    }
        mysql_close($db_connection);
	}
	?>

</td></tr>
</table>
</body>
</html>