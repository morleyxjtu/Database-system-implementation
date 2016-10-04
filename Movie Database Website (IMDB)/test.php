<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title></title>
    <style type="text/css">
    #menulink {
      display: none;
    }

    a:link {
    color: #777;
    text-decoration: none;
    }

    a:visited {
      color: #333;
      text-decoration: none;

    }

  a:link:hover, a:visited:hover  {
    color: #777;
    background-color: #ccc;
  }

  a:link:active, a:visited:active {
    color: #ccc;
    background-color: #ccc;
  }
    </style>
    <script type="text/javascript">

      function toggle(zap) {
      if (document.getElementById) {
        var abra = document.getElementById(zap).style;
        if (abra.display == "block") {
          abra.display = "none";
          } else {
          abra.display = "block"
          }
        return false
        } else {
        return true
        }
      }

    </script>

  </head>
  <body>
  <div id="navsite">
  <ul>
   <li><a href="http://www.java2s.com">Home</a></li>
   <li><a href="http://www.java2s.com">Home</a></li>
   <li><a href="http://www.java2s.com">Home</a></li>
    <li><a href="http://www.java2s.com">Home</a></li>
   <li><a href="http://www.java2s.com">Home</a></li>
   <li><a href="http://www.java2s.com">Home</a></li>
  </ul>
  <h5><a href="#" onclick="return toggle('menulink');">Links (+/-)(Click me to expand)</a></h5>
  <ul id="menulink">
   <li><a href="http://www.java2s.com">Home</a></li>
   <li><a href="http://www.java2s.com">Home</a></li>
   <li><a href="http://www.java2s.com">Home</a></li>
    <li><a href="http://www.java2s.com">Home</a></li>
   <li><a href="http://www.java2s.com">Home</a></li>
  </ul>
  </div>
  </body>
 </html>