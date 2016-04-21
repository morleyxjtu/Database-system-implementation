<html>
<body>

<form method="GET" action="calculator.php">
  Name: <input type="text" name="fname">
  <input type="submit">
</form>

<?php
if($_GET['fname']){
  $name = $_GET['fname'];

  if (empty($name)){
      echo "EM";
      return;
  }//if input is empty, return nothing
  $arra = str_split($name);

  if (preg_match('/[^0-9\+\-\*\/\.\ ]/', $name, $matches)){
      echo "Invalid Expression!";
      return;
  }//if input contains anything other than number, operaters, "." and space, return error

  if (preg_match('/[0-9]\ *\/\ *0\ *(?!\.)/', $name, $matches)){
      echo "Division by zero error!";
      return;
  }//if input contains "/0" and not followed by ".", return error

  if (preg_match('/(^0|([^0-9])0)[0-9]/', $name, $matches)){
      echo "Invalid Expression!";
      return;
  }//if input contains a number starting with 0

  if (preg_match('/^\ *[^0-9\-\ ]/', $name, $matches)){
      echo "Invalid Expression!";
      return;
  }//if the line starts with anything other than number, "-" or space

  if (preg_match('/[^0-9\ ]\ *$/', $name, $matches)){
      echo "Invalid Expression!";
      return;
  }//if the line ends with anything other than number, or space

  if (preg_match('/[0-9\.]\ +[0-9\.]/', $name, $matches)){
      echo "Invalid Expression!";
      return;
  }//if there is one or more spaces in between mumbers, dot and number, return error

  if (preg_match('/(^\ *\-\ +[0-9])|([\+\-\*\/]\ *\-\ +[0-9])/', $name, $matches)){
      echo "Invalid Expression!";
      return;
  }//if there is one or more spaces in between negative sign and number, return error

  if (preg_match('/[\+\-\*\/]\ *[\+\-\*\/]\ *[\+\-\*\/]\ */', $name, $matches)){
      echo "Invalid Expression!";
      return;
  }//if there are three operators, return error

  if (preg_match('/^\ *[\+\-\*\/]\ *[\+\-\*\/]/', $name, $matches)){
      echo "Invalid Expression!";
      return;
  }//if there are two operators at beginning, return error  

  if (preg_match('/[\+\-\*\/]\ *[\+\*\/]/', $name, $matches)){
      echo "Invalid Expression!";
      return;
  }//if there are two operators with the second one not "-", return error  

  if (preg_match('/[0-9]\ *\-\-\ *[0-9]/', $name, $matches)){
        for ($x = 0; $x < sizeof($arra); $x++){
            if ($arra[$x] == "-" and $arra[$x+1] == "-" ){
                array_splice($arra, $x+1, 0, " ");
            }
        }
        $name=implode("",$arra);
  }

   eval("\$result=$name;");
   if (FALSE === $result or NULL === $result){
       echo "Invalid Expression!";
   }else{
       echo $name, "=", $result;
   }

}
?>

</body>
</html>