<?php
/*
Script Name: TINA4AJAX
Purpose:

Created By: 
Created Date: 
*/
require_once ('C:\Users\Andre\Documents\GitHub\tina4php\public_html/cdesimple.php');
require_once ('C:\Users\Andre\Documents\GitHub\tina4php\public_html/connection.php');
require_once ('C:\Users\Andre\Documents\GitHub\tina4php\public_html/shape.php');
if (file_exists('C:\Users\Andre\Documents\GitHub\tina4php\public_html/includes/v1.0.0.0/functions.php')) {
  require_once ('C:\Users\Andre\Documents\GitHub\tina4php\public_html/includes/v1.0.0.0/functions.php');
}
if (file_exists('C:\Users\Andre\Documents\GitHub\tina4php\public_html/includes/v1.0.0.0/api.php')) { require_once ('C:\Users\Andre\Documents\GitHub\tina4php\public_html/includes/v1.0.0.0/api.php'); }

session_name($_CDEDEV_SESSION_NAME);
session_start();
$_REQUEST["ajaxscriptname"] = "TINA4AJAX";
$html = "";
//Andre: This functionality below serves as a way to prevent anyone from taking data from your server via an AJAX call
$password = "tina4" ;

if ( crypt ( $password, $_REQUEST["passwordhash"]) == $_REQUEST["passwordhash"] ) {
  //Andre: We can make things happen here
  $columns = json_decode ( $_REQUEST["columns"] );
  
  foreach ($columns as $cid => $column ) {
    $fields[] = $column->name;
  }
  
  $fields = implode ( ",", $fields );
  
  $sql = "select {$fields}, {$_REQUEST["primarycolumn"]} as PK from {$_REQUEST["tablename"]} t ";
  $records = $CDE->get_row ( $sql );
  
  
  //get the rows for the table
  $trs = array ();
  foreach ($records as $rid => $record ) {
    //pass back the records on the system
	$tds = array();
	
	if ( $_REQUEST["showcheckbox"] ) {
	  $tds[] = td ( input ( array ("type" => "checkbox", "name" => "sel".$_REQUEST["instancename"]."[]"), $record->PK )  );
	}
	foreach ($columns as $cid => $column ) {
	  $column->name = strtoupper ( $column->name );
	  eval (' $tds[] =  td ( array ( "style" => "text-align: '.$column->align.'"), $record->'.$column->name.'."&nbsp;"  ); ');
	}
	
	$originalpk = $record->PK;
	$record->PK = str_replace ("'", "", $record->PK);
	$record->PK = str_replace (" ", "_", $record->PK);
	
	//add the new row
	$trs[] = tr ( array ( "id" => "row{$_REQUEST["instancename"]}{$record->PK}",
	                      "onmouseover" => "if ( this.className != 'rowselected' ) { this.className = 'rowhover'; }", 
	                      "onmouseout" => "if ( this.className == 'rowhover') { this.className = ''; } ", 
						  "onclick" => "if ( this.className != 'rowselected') { if (lastid{$_REQUEST["instancename"]} !== undefined) { document.getElementById (lastid{$_REQUEST["instancename"]}).className = '';  } selectedpk{$_REQUEST["instancename"]} = '".stripcslashes ($originalpk)."'; set{$_REQUEST["instancename"]}selected ( '{$originalpk}' );    lastid{$_REQUEST["instancename"]} = 'row{$_REQUEST["instancename"]}{$record->PK}';  this.className = 'rowselected'; } else { set{$_REQUEST["instancename"]}selected ( '' );  selectedpk{$_REQUEST["instancename"]} = ''; this.className = '';  } ") ,
						  $tds ); 
  }
  
  $html .= shape ( $trs );  

}
 else {
  $html = b ( "Error reading the data" );
}

echo $html;


?>