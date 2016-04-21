<html>
<style>
table, th, td {
    border: 1px solid black;
}
</style>
<body>
	<FORM METHOD = "GET" ACTION = "query.php">
        <TEXTAREA NAME="area" ROWS=10 COLS=30>
        </TEXTAREA>	
        <INPUT TYPE="submit">
    </FORM>


    <?php
    if($_GET['area']){
    	$db_connection = mysql_connect("localhost", "cs143", "");
    	mysql_select_db("CS143", $db_connection);
        $query = $_GET['area'];
        $rs = mysql_query($query, $db_connection);

        echo "<table>";
        echo "<tr>";
        $num = mysql_num_fields($rs);
        for ($i = 0; $i < $num; $i ++){
          	echo "<th>".mysql_field_name($rs, $i)."</th>";
        }
        echo "</tr>";

        while($row = mysql_fetch_row($rs)) {
            echo "<tr>";
        	for ($x = 0; $x < sizeof($row); $x ++){
        		if ($row[$x]===NULL){
        			echo "<td> NULL </td>";
        		}else{
        			echo "<td>". $row[$x]. "</td>";
        		}
        	}
        	echo "</tr>";
        }

        echo "</table>";
    	mysql_close($db_connection);
    }
    ?>
</body>
</html>  
