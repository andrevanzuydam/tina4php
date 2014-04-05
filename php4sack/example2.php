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

<style>
  [draggable=true] {
  -khtml-user-drag: element;
  }

  .over {
    background : blue;   
  }
  
  .dropped {
    background: orange;
  }
  
  [draggable] {
    -moz-user-select: none;
    -khtml-user-select: none;
    -webkit-user-select: none;
    user-select: none;
    /* Required to make elements draggable in old WebKit */
    -khtml-user-drag: element;
    -webkit-user-drag: element;
  }
  
  .dragme {
     width : 100px;
     height: 20px;
     border: 2px solid black;
     
  }
  
  .droptarget {
     width : 500px;
     height: auto; 
     border: 2px solid blue; 
  }
  
  .droptarget div {
     width : 500px;
     height: auto; 
     border: 2px solid red; 
  }
  
  div {
    height: 20px;
  }
  
</style>

<body>
  <form name="somename" method="post">
  
  
  
  <table id="target1" class="droptarget">
     <tr><th colspan="2">target1</th></tr>
     <tr><td>001</td><td><div id="drophere1"><div id="date1000"><div draggable="true" id="box1" class="dragme">
     box1<br />
     Try dragging me. Make sure you have a look at the basic style sheet to see what to do.       
  </div> drop here </div></div></td></tr>
     <tr><td>002</td><td><div id="drophere2"><div id="date2000"><div draggable="true" id="box2" class="dragme">
     box2<br />
     Try dragging me. Make sure you have a look at the basic style sheet to see what to do.       
  </div> drop here </div></div></td></tr>
     <tr><td>003</td><td><div id="drophere3"><div id="date3000"><div draggable="true" id="box3" class="dragme">
     box3<br />
     Try dragging me. Make sure you have a look at the basic style sheet to see what to do.       
  </div> drop here </div></div></td></tr>
     <tr><td>004</td><td><div id="drophere4"><div id="date4000"> drop here </div></div></td></tr>
  </table>
    
  <script>
     function testme (event, box1, box2) {
       box1.appendChild(box2); 
       
       window.alert ('You dropped '+box2.id+' on '+box1.id);
     }
  </script>
  <?php
    require_once ("PHP4sackinclude.php");
    require_once ("PHP4sack.php");
    //here we say which div we want to initialize
    echo sack_dragevent ($divid="box1", $divtargets="drophere1,drophere2,drophere3,drophere4", $javascriptfunction="testme");
    //notice on this one it can only drop on the second target
    echo sack_dragevent ($divid="box2", $divtargets="drophere1,drophere2,drophere3,drophere4", $javascriptfunction="testme");
  ?>
  </form>
</body>
</html>