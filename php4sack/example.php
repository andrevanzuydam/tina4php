<?php
/*
  Library to implement tw-sack.js in php
*/
/**********************************************************
 Name : include sack js
 Description : Examples for PHP4SACK
 Revision : 1.05
 Created By : andre
 Last Modified by Andre van Zuydam on 09/08/2010 08:32:43
**********************************************************/
?>
<html>

  <head>
     <title> Example for Sack </title>
  </head>

<body>
  <form name="somename" method="post">
  <table border="1">
      <tr><th colspan="2">Ajax Div Example</th></tr>
      <tr><td> Name </td><td><input type="text" name="name" value="Some Name"></td></tr>
      <tr><td> Check Array  1</td><td><input type="checkbox" name="checkarray[]" value="1"></td></tr>
      <tr><td> Check Array  2</td><td><input type="checkbox" name="checkarray[]" value="2"></td></tr>
      <tr><td colspan="2"><input type="button" value="Go" onclick="document.getElementById('ajxDivExample').onclick()"></td></tr> 
  </table>
  <?php
  require_once ("PHP4sackinclude.php");
  require_once ("PHP4sack.php");
  echo sack_input ($type="div", //could be any input type but buttons are not going to work practically!! 
                       $name="ajxDivExample", 
                       $script="ajaxscript.php", 
                       $target="ajxDivExample", 
                       $params="name,checkarray", 
                       $event="OnClick", 
                       $loadinghtml="Loading...", 
                       $runevent=true, //we want to run divs by default because they need to show something ?
                       $extraevent="", //use this for inputs
                       $prerun=""
                       );  
  
  
  //Below an example for a dynamic input
  ?>
  <table border="1">
      <tr><th colspan="2">Ajax Input Example </th></tr>
      <tr><td> Type something in the box on the right </td><td>
      <?php 
         echo  sack_input ($type="text", //could be any input type but buttons are not going to work practically!! 
                       $name="inputname", 
                       $script="ajaxscript2.php", 
                       $target="result", //based on the id - see textarea called result below
                       $params="inputname", 
                       $event="OnKeyUp", 
                       $loadinghtml="Loading...", 
                       $runevent=false, //run event is false for inputs ?
                       $extraevent="", //use this for inputs
                       $prerun=""
                       ); 
      
       ?></td></tr>
      <tr><td> Result </td><td><textarea id="result" name="result"></textarea></td></tr>
  </table>
  
  </form>
</body>
</html>