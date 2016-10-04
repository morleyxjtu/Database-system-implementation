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
	<form method="GET" action="addMovieActor.php" name = "input">
  		Movie Title: <input type="text" name="movie" required>
  		Actor Name: <input type="text" name="actor" required>
  		<input type="submit" value = "search">
	</form>
	<?php
	    if($_GET['actor'] AND $_GET['movie']){
			$db_connection = mysql_connect("localhost", "cs143", "");
		    mysql_select_db("CS143", $db_connection);

		    $actor_name = $_GET['actor'];
		    $keywords = explode(" ", $actor_name);
		    foreach ($keywords as $word) {
		    	$sql_actor[] = ' CONCAT(first, " ", last) LIKE "%'. $word. '%"';
		    }
		    $query_actor = 'SELECT * FROM Actor WHERE'. implode(' AND ', $sql_actor);
		    $rs_actor = mysql_query($query_actor, $db_connection);
	    	$error = mysql_error();
	    	if ($error != '' ) {
				print '<strong>An error occurred!</strong> ' . $error;
				mysql_close($db_connection);
				exit();
			}
		    
		    if (mysql_num_rows($rs_actor) != 0){
		     	echo "Searching results in Actor database";
	    	    echo "<table>";
		        echo "<tr>";
		        $num = mysql_num_fields($rs_actor);
		        for ($i = 1; $i < $num; $i ++){
		          	echo "<th>".mysql_field_name($rs_actor, $i)."</th>";
		        }
		        echo "</tr>";
		        echo '<form method="GET" action="addMovieActor.php" name = "selection">';
		        while($row = mysql_fetch_row($rs_actor)) {
		            echo "<tr>";
		        	for ($x = 1; $x < sizeof($row); $x ++){
		        		if ($row[$x]===NULL){
		        			echo "<td> NULL </td>";
		        		}else{
		        			echo "<td>". $row[$x]. "</td>";
		        		}
		        	}
		        	echo '<td><input type="radio" name="check_actor" value="'.$row[0].'"> Choose this actor</td>';
		        	echo "</tr>";
		        }
		        echo "</table>";
		        
		    }else{
		    	echo "No matching results in Actor database";
		    }

		    $movie_name = $_GET['movie'];
		    $keywords = explode(" ", $movie_name);
			foreach ($keywords as $word) {
		    	$sql_movie[] = ' title LIKE "%'. $word. '%"';
		    }
		    $query_movie = 'SELECT * FROM Movie WHERE'. implode(' AND ', $sql_movie);
		    $rs_movie = mysql_query($query_movie, $db_connection);
    		$error = mysql_error();
	    	if ($error != '' ) {
				print '<strong>An error occurred!</strong> ' . $error;
				mysql_close($db_connection);
				exit();
			}
		    if (mysql_num_rows($rs_movie) != 0){
		    	echo "Searching results in Movie database";
	    	    echo "<table>";
		        echo "<tr>";
		        $num = mysql_num_fields($rs_movie);
		        for ($i = 1; $i < $num; $i ++){
		          	echo "<th>".mysql_field_name($rs_movie, $i)."</th>";
		        }
		        echo "</tr>";

		        
		        while($row = mysql_fetch_row($rs_movie)) {
		            echo "<tr>";
		        	for ($x = 1; $x < sizeof($row); $x ++){
		        		if ($row[$x]===NULL){
		        			echo "<td> NULL </td>";
		        		}else{
		        			echo "<td>". $row[$x]. "</td>";
		        		}
		        	}
		        	echo '<td><input type="radio" name="check_movie" value="'.$row[0].'"> Choose this actor</td>';
		        	echo "</tr>";
		        }
		        echo "</table>";
		    }else{
		    	echo 'No matching results in Movie database';
		    }
		    if (mysql_num_rows($rs_actor) != 0 AND mysql_num_rows($rs_movie) != 0){
		    	echo 'Role of the actor: <input type="text" name = "role"><br>';
		    	echo '<input type="submit" value = "submit"></form>';
		    }
		    mysql_close($db_connection);
		}
	?>
	<?php
	    if ($_GET['check_actor'] AND $_GET['check_movie']){
			$db_connection = mysql_connect("localhost", "cs143", "");
		    mysql_select_db("CS143", $db_connection);

	    	$role =$_GET['role'];
	    	$role = !empty($role)?"'$role'":"NULL";
	    	$query_insert = 'INSERT INTO MovieActor VALUES ('.$_GET['check_movie'].', '.$_GET['check_actor'].','.$role.')';
	    	mysql_query($query_insert, $db_connection);
	    	$error = mysql_error();
	    	if ($error != '' ) {
				print '<strong>An error occurred!</strong> ' . $error;
				mysql_close($db_connection);
				exit();
			}
	    }
	    mysql_close($db_connection);
	?>
</td></tr>
</table>
</body>
</html>