<?php
  echo "<pre>AJAX SCRIPT STARTS HERE !!!!
Click here to refresh the script      
  \n";
    
  echo "Show the variables passed to the script \n";  
  print_r ($_REQUEST);
  
  echo "<h3>Run a javascript script to output the input  </h3>";
  echo "<script>window.alert ('Your name is {$_REQUEST["name"]}'); </script>";
  
  echo "</pre>";
?>