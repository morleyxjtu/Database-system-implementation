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
	<form method="GET" action="addMovieDirector.php" name = "input">
  		Movie Title: <input type="text" name="movie" required>
  		Director Name: <input type="text" name="dir" required>
  		<input type="submit" value = "search">
	</form>
	<?php
	    if($_GET['dir'] AND $_GET['movie']){
			$db_connection = mysql_connect("localhost", "cs143", "");
		    mysql_select_db("CS143", $db_connection);

		    $dir_name = $_GET['dir'];
		    $keywords = explode(" ", $dir_name);
		    foreach ($keywords as $word) {
		    	$sql_dir[] = ' CONCAT(first, " ", last) LIKE "%'. $word. '%"';
		    }
		    $query_dir = 'SELECT * FROM Director WHERE'. implode(' AND ', $sql_dir);
		    $rs_dir = mysql_query($query_dir, $db_connection);
	    	$error = mysql_error();
	    	if ($error != '' ) {
				print '<strong>An error occurred!</strong> ' . $error;
				mysql_close($db_connection);
				exit();
			}
		    
		    if (mysql_num_rows($rs_dir) != 0){
		     	echo "Searching results in Director database";
	    	    echo "<table>";
		        echo "<tr>";
		        $num = mysql_num_fields($rs_dir);
		        for ($i = 1; $i < $num; $i ++){
		          	echo "<th>".mysql_field_name($rs_dir, $i)."</th>";
		        }
		        echo "</tr>";
		        echo '<form method="GET" action="addMovieDirector.php" name = "selection">';
		        while($row = mysql_fetch_row($rs_dir)) {
		            echo "<tr>";
		        	for ($x = 1; $x < sizeof($row); $x ++){
		        		if ($row[$x]===NULL){
		        			echo "<td> NULL </td>";
		        		}else{
		        			echo "<td>". $row[$x]. "</td>";
		        		}
		        	}
		        	echo '<td><input type="radio" name="check_dir" value="'.$row[0].'"> Choose this director</td>';
		        	echo "</tr>";
		        }
		        echo "</table>";
		        
		    }else{
		    	echo "No matching results in Director database";
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
		        	echo '<td><input type="radio" name="check_movie" value="'.$row[0].'"> Choose this movie</td>';
		        	echo "</tr>";
		        }
		        echo "</table>";
		    }else{
		    	echo 'No matching results in Movie database';
		    }
		    if (mysql_num_rows($rs_dir) != 0 AND mysql_num_rows($rs_movie) != 0){
		    	echo '<input type="submit" value = "submit"></form>';
		    }
    	    mysql_close($db_connection);
		}
	?>
	<?php
	    if ($_GET['check_dir'] AND $_GET['check_movie']){
			$db_connection = mysql_connect("localhost", "cs143", "");
		    mysql_select_db("CS143", $db_connection);
		    
	    	$query_insert = 'INSERT INTO MovieDirector VALUES ('.$_GET['check_movie'].', '.$_GET['check_dir'].')';
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