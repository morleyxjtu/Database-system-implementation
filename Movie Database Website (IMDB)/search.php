<html>

<style>
table.table2, th, td {
    border: 1px solid black;
}
</style>

<head>
	<title>CS143 Movie Database_Muchen Xu</title>
</head>

<body style="margin: 0px; padding: 0px; font-family: 'Trebuchet MS',verdana;">

<table class="table1" width="100%" style="height: 100%;" cellpadding="10" cellspacing="0" border="0">

<tr><td colspan="2" style="height: 100px;" color= white bgcolor="#848484" align="center">
	<h2><a href="./index.php">CS143-MDB</a></h2>
	<form method="GET" action="search.php">
  		Movie/Actor <input type="text" name="fname" required>
  		<input type="submit" value ="Search">
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
    if($_GET['fname']){

		$db_connection = mysql_connect("localhost", "cs143", "");
	    mysql_select_db("CS143", $db_connection);

	    $name = $_GET['fname'];
	    $keywords = explode(" ", $name);

	    foreach ($keywords as $word) {
	    	$sql_actor[] = ' CONCAT(first, " ", last) LIKE "%'. $word. '%"';
	    }
	    $query_actor = 'SELECT * FROM Actor WHERE'. implode(' AND ', $sql_actor).'ORDER BY first';
	    $rs = mysql_query($query_actor, $db_connection);
		$error = mysql_error();
    	if ($error != '' ) {
			print '<strong>An error occurred!</strong> ' . $error;
			mysql_close($db_connection);
			exit();
		}
	    
	     if (mysql_num_rows($rs) != 0){
	     	echo "<h4>Searching results in Actor database (ordered by first name)</h4>";
    	    echo "<table class='table2'>";
	        while($row = mysql_fetch_row($rs)) {
	        	echo '<a href = "./showActorInfo.php?aid='.$row[0].'">'.$row[2]. ' '. $row[1]. '('.$row[4].')</a><br>';
	        	echo "</tr>";
	        }
	        echo "</table>";
	     }else{
	     	echo "No match in Actor database <br>";
	     }


		foreach ($keywords as $word) {
	    	$sql_movie[] = ' title LIKE "%'. $word. '%"';
	    }
	    $query_movie = 'SELECT * FROM Movie WHERE'. implode(' AND ', $sql_movie).'ORDER BY year';
	    $rs = mysql_query($query_movie, $db_connection);
		$error = mysql_error();
    	if ($error != '' ) {
			print '<strong>An error occurred!</strong> ' . $error;
			mysql_close($db_connection);
			exit();
		}
	    if (mysql_num_rows($rs) != 0){
	    	echo "<h4>Searching results in Movie database (ordered by year)</h4>";
    	    echo "<table>";
	        while($row = mysql_fetch_row($rs)) {
	            echo "<tr>";
	        	echo '<a href = "./showMovieInfo.php?mid='.$row[0].'">'.$row[1]. ' ('. $row[2]. ')</a><br>';
	        	echo "</tr>";
	        }
	        echo "</table>";
	    }else{
	    	echo "No match in Movie database";
	    }
	    

	    mysql_close($db_connection);

	}
?>


</td></tr>
</table>
</body>
</html>