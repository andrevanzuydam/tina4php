<?php
/*
  Library to implement tw-sack.js in php
*/
/**********************************************************
 Name : sack_components
 Description : Components for SACK
 Revision : 1.05
 Created By : andre
 Last Modified by Andre van Zuydam on 09/08/2010 08:32:43
**********************************************************/
if (!function_exists("sack_input"))
{
  function sack_input ($type="div", //could be any input type but buttons are not going to work practically!! 
                       $name, 
                       $script, 
                       $target, 
                       $params, 
                       $event="OnClick", 
                       $loadinghtml="Loading...", 
                       $runevent=false, 
                       $extraevent)
  {
    //require_once ("PHP4sackinclude.php"); //include the javascript script for tw-sack.js 
    $output = '
    <script type="text/javascript">
    //var ajax'.$name.' = new sack();
    function when'.$name.'Loading()
    {
  	  var e = document.getElementById("'.$target.'"); 
  	  e.innerHTML = "'.str_replace('"', '\"', $loadinghtml).'";
    }
   
   //A function to get values from check boxes or other array variables
   function getValues ( form, name ) {
      var values = [];
      for(n=0;n < form.length;n++){
        if(form[n].name == name && form[n].checked){
          values.push(escape(form[n].value)); //translate for safe passing
        }
      }
      return values;
    }
    
    function Init'.$name.'(){
      var ajax'.$name.' = new sack();
      if (document.forms)
      {	
        for (iform = 0; iform < document.forms.length; iform++) {
          var form = document.forms[iform];
      ';
      
     //go through each form on the document and pass the values to ajax specified by params  	
     if ($params)
     {    
       $params = explode (",", $params);
       foreach ($params as $id => $var)
       {
         $var = trim($var);
         $output .=  '
                      if (getValues(form, \''.$var.'[]\') != "") {
                        ajax'.$name.'.setVar("'.$var.'", getValues(form, \''.$var.'[]\'));
                      }
                        else
                      if (form.'.$var.') { 
                          ajax'.$name.'.setVar("'.$var.'", escape(form.'.$var.'.value)); //escape added here to translate special chars in posting
                      }'."\n";   
       
      }   
    }  
    $output .= '	
        }
      }';
          
    $output .= '	
      ajax'.$name.'.requestFile = "'.$script.'";
    	if (form)
    	{
        ajax'.$name.'.method = form.method;
      }  
    	ajax'.$name.'.element = "'.$target.'";
    	ajax'.$name.'.onLoading = when'.$name.'Loading;
    	ajax'.$name.'.runAJAX();
    }
    </script> ';
     
    if ($type == "div")
    { 
      $output .= "<div id=\"$name\" name=\"$name\" $event=\"Init".$name."();\"> </div>";
    }   
      else
    {
      $output .= "<input id=\"$name\" name=\"$name\" type=\"$type\" $event=\"Init".$name."();\" $extraevent />";
    }   
    
    //Check if the default event must be run
    if ($runevent)
    {
      $output .= "<script>Init".$name."();</script>";
    }
   
  
    return $output; 
  }
}

//dragging events
if (!function_exists ("sack_dragevent")) {
  function sack_dragevent ($divid="", $divtargets, $javascriptfunction="") {
    $output = "<script>create_dragevent ('{$divid}', '{$divtargets}', '{$javascriptfunction}');</script>";    
    return $output;
  }  
}
?>