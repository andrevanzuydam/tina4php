<?php
if (file_exists('C:\Users\Andre\Documents\GitHub\tina4php\public_html/includes/v1.0.0.0/Exampleapi.php')) { require_once ('C:\Users\Andre\Documents\GitHub\tina4php\public_html/includes/v1.0.0.0/Exampleapi.php'); }
class Example_int extends cde_interface {
    function get_interface ($CDE, $go, $request, $cdedev) {
      $DEV = $cdedev->DEV;
      //iterate values into $html variable to return things from the interface
      //overload the header and footer function to get your own header and footer going.
      //Andre: This example will use the SQLite3 database called application.db which is found in the web root

//Andre: The headers here allow for things to automate your html headers with the includes needed
$html .= $this->html_header ("Example of CRUD management", "Examplecss");
$html .= $this->html_ajaxhandler();


//Andre: We need to create a simple table first with some examples of data we will be using, most important is to have an example of an integer, numeric, data and text

$sqlcreate = "create table if not exists tbluser (
  userid integer primary key autoincrement,
  firstname varchar (200) default '',
  lastname varchar (200) default '',
  noofprojects integer default 0,
  rateperhour numeric (18,2) default 20.00,
  dateofbirth date, 
  datecreated datetime 
)";

//Andre: Use CDE to create the table
//$CDE->exec ( $sqlcreate );

//Andre: See if we get any errors
//$html .= $CDE->get_lasterror();

$lastnames = array ( "Smith", "Hill", "Black", "White", "Green", "Underhill" );
$firstnames = array ( "Andre", "Zoe", "Julian", "Valiant", "Berty", "Cliff" );

$sqlinsert = "insert into tbluser (firstname, lastname, noofprojects, rateperhour) 
                           values (        ?,        ?,            ?,           ?)";

//$CDE->exec ( $sqlinsert, $firstnames[rand(0, count($firstnames)-1)], $lastnames[rand(0, count($lastnames)-1)], rand (0, 10), rand (20, 40) );

//$CDE->exec ("delete from tbluser");
//Andre: Set the session grid password to make sure the data returned by the grid is uniform - only needs to be done once
//Make sure it mataches the passwordhash setting in the TINA4AJAX script - if you don't need security don't worry
$_SESSION["TINA4GRIDPASSWORD"] = "tina4";

$sqldelete = "delete from tbluser";

//$CDE->exec ( $sqldelete );

//Andre: Initialize the CRUD manager for the above table

$columns[] = array ( "name" => "userid", "alias" => "ID", "align" => "left", "type" => "integer"  );
$columns[] = array ( "name" => "firstname", "alias" => "First Name", "align" => "left", "type" => "text"  );
$columns[] = array ( "name" => "lastname", "alias" => "Last Name", "align" => "left", "type" => "text" );
$columns[] = array ( "name" => "noofprojects", "alias" => "No of Projects", "align" => "right", "type" => "number" );
$columns[] = array ( "name" => "rateperhour", "alias" => "Rate per Hour", "align" => "right", "type" => "number" );
$columns[] = array ( "name" => "dateofbirth", "alias" => "Date of Birth", "align" => "left", "type" => "date" );
$columns[] = array ( "name" => "datecreated", "alias" => "Date Created", "align" => "left", "type" => "timestamp" );


$html .= new TINA4Grid ($CDE, 
                        $_REQUEST, //post variables
						$title="CRUD Grid",
						$tablename="tbluser", 
						$columns, 
						$primarycolumn="userid", 
						$permissions="11111", //insert, update, view, delete, pdf, excel
						$instancename="grid", 
						$rows=20, 
						$showsearch=true, 
						$showfooter=true   

         );
		 
//Andre: This footer is for debugging, the first parameter displays session and request information, the second is to refresh SASS without reloading the screen.
$html .= $this->html_footer(true, false);		 
    return $html;
  }
}
?>