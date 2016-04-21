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
		$mid = $_GET['mid'];
	?>
	<FORM METHOD = "GET" ACTION = "addReview.php">
		<p> * are required items</p>
		Reviewer name: <input type="text" name="name"><br>
		Movie ID: <input type="text" name="mid" required value = "<?php echo $mid; ?>">* (Don`t know the Movie ID? Just search it!) <br>
		Movie Rating (out of 5): <select name="rating" id = "rating">
			<option value=1>1</option>
			<option value=2>2</option>
  			<option value=3>3</option>
  			<option value=4>4</option>
  			<option value=5>5</option>
		</select><br>
        Movie Comment: <br>
        <TEXTAREA NAME="comment" ROWS=10 COLS=30></TEXTAREA>	
        <INPUT type="submit" name="subject" value="Add">
    </FORM>

	<?php
	if($_GET['mid'] AND $_GET['subject']){
		$db_connection = mysql_connect("localhost", "cs143", "");
	    mysql_select_db("CS143", $db_connection);
	    $mid = $_GET['mid'];
	    $name = $_GET['name'];
	    $name = !empty($name) ? "'$name'" : "NULL";
	    $rating = $_GET['rating'];
	    $comment = $_GET['comment'];
	    $comment = !empty($comment) ? "'$comment'" : "NULL";
	    $query_insert_comment = 'INSERT INTO Review VALUES ('.$name.',NOW(),'.$mid.','.$rating.','.$comment.')';
	    mysql_query($query_insert_comment, $db_connection);
    	$error = mysql_error();
    	if ($error != '' ) {
			print '<strong>An error occurred!</strong> ' . $error;
			mysql_close($db_connection);
			exit();
		}
	    mysql_close($db_connection);
	    echo "Data submitted!";
	}
	?>

</td></tr>
</table>
</body>
</html>