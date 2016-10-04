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
	<form method = "GET" action = "addActorDirector.php">
		<p> * are required items</p>
		<p> Please use "yyyymmdd" or "yyyy/mm/dd" format for the date input</p>
		<p>Identity: <input type="radio" name = "identity" value = "Actor" checked>Actor
		<input type="radio" name = "identity" value = "Director">Director
		<input type="radio" name = "identity" value = "Actor-Director">Actor and Director </p>
		<p>First Name: <input type="text" name="first"></p>
		<p>Last Name: <input type="text" name="last"></p>
		<p>Sex: <input type="radio" name = "sex" value = "'Male'" checked>Male 
		<input type="radio" name = "sex" value = "'Female'">Female</p>
		<p>Date of Birth: <input type="text" name="dob" required>*</p>
		<p>Date of Death: <input type="text" name="dod"> (Leave blank if still alive)</p>
		<p><input type="submit" value = "Add"></p>
	</form>

	<?php
		if($_GET['dob']){
			$db_connection = mysql_connect("localhost", "cs143", "");
		    mysql_select_db("CS143", $db_connection);
		    $identity = $_GET['identity'];
		    $firstname = $_GET['first'];
		    $firstname = !empty($firstname) ? "'$firstname'" : "NULL";
		    $lastname = $_GET['last'];
		    $lastname = !empty($lastname) ? "'$lastname'" : "NULL";
		    $sex = $_GET['sex'];
		    $dob = $_GET['dob'];
		    $dod = $_GET['dod'];
		    $dod = !empty($dod) ? "$dod" : "NULL";
		    $query_max = 'SELECT * FROM MaxPersonID';
		    $rs = mysql_query($query_max, $db_connection);
		    $error = mysql_error();
		    $maxPID = mysql_fetch_row($rs);
		    
		    if ($identity == "Actor-Director"){
		    	$query_insert_actor = 'INSERT INTO Actor VALUES ('.($maxPID[0]+1).','.$lastname.','.$firstname.',"'.$sex.'",'.$dob.','.$dod. ')';
		    	$query_insert_director = 'INSERT INTO Director VALUES ('.($maxPID[0]+1).','.$lastname.','.$firstname.','.$dob.','.$dod. ')';
		    	mysql_query($query_insert_actor, $db_connection);
		    	$error = mysql_error();
		    	if ($error != '' ) {
					print '<strong>An error occurred!</strong> ' . $error;
					mysql_close($db_connection);
					exit();
				}
		    	mysql_query($query_insert_director, $db_connection);
		    	$error = mysql_error();
		    	if ($error != '' ) {
					print '<strong>An error occurred!</strong> ' . $error;
					mysql_close($db_connection);
					exit();
				}
		    }elseif ($identity == "Director") {
		    	$query_insert = 'INSERT INTO Director VALUES ('.($maxPID[0]+1).','.$lastname.','.$firstname.','.$dob.','.$dod. ')';
		    	mysql_query($query_insert, $db_connection);
		    	$error = mysql_error();
		    	if ($error != '' ) {
					print '<strong>An error occurred!</strong> ' . $error;
					mysql_close($db_connection);
					exit();
				}
		    }else{
		    	$query_insert = 'INSERT INTO Actor VALUES ('.($maxPID[0]+1).','.$lastname.','.$firstname.','.$sex.','.$dob.','.$dod. ')';
		    	mysql_query($query_insert, $db_connection);
		    	$error = mysql_error();
		    	if ($error != '' ) {
					print '<strong>An error occurred!</strong> ' . $error;
					mysql_close($db_connection);
					exit();
				}
		    }
		    $query_max = 'UPDATE MaxPersonID SET id = '.($maxPID[0]+1);
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