<?php
/*
Script Name: ajaxProcessSQL
Purpose:
To enable developing to happen
Created By: Andre van Zuydam
Created Date: 2014-03-12 17:19:32
*/
require_once ('C:\Users\Andre\Documents\GitHub\tina4php\public_html/cdesimple.php');
require_once ('C:\Users\Andre\Documents\GitHub\tina4php\public_html/connection.php');
require_once ('C:\Users\Andre\Documents\GitHub\tina4php\public_html/shape.php');
if (file_exists('C:\Users\Andre\Documents\GitHub\tina4php\public_html/includes/v1.0.0.0/functions.php')) {
  require_once ('C:\Users\Andre\Documents\GitHub\tina4php\public_html/includes/v1.0.0.0/functions.php');
}
if (file_exists('C:\Users\Andre\Documents\GitHub\tina4php\public_html/includes/v1.0.0.0/Developerapi.php')) { require_once ('C:\Users\Andre\Documents\GitHub\tina4php\public_html/includes/v1.0.0.0/Developerapi.php'); }

session_name($_CDEDEV_SESSION_NAME);
session_start();
$_REQUEST["ajaxscriptname"] = "ajaxProcessSQL";
if ($_REQUEST["processSQL"] != "execute") {
	if ($_REQUEST["metatarget"] != "") {
	  $metadata = explode ("_-", $_REQUEST["metatarget"]);
	  
	  if ($metadata[1] && $metadata[2] == "") {
		$_REQUEST["fielddata"] = "";
		$_REQUEST["tabledata"] =  $metadata[1];
		$_REQUEST["targetdatabase"] =  $metadata[0];
	  }
	  
	  if ($metadata[2] != "") {
		$_REQUEST["fielddata"] .= $metadata[2].",";
	  }
	  
	}
	
	$_REQUEST["SQL"] = "select ".substr($_REQUEST["fielddata"],0,-1)." from ".$_REQUEST["tabledata"];
}

$html .= $CDE->switchid ("processSQL");
$html .= textarea (array("name" => "fielddata", "style" => "display:none"), $_REQUEST["fielddata"]);
$html .= textarea (array("name" => "tabledata", "style" => "display:none"), $_REQUEST["tabledata"]);
$html .= textarea (array("name" => "targetdatabase", "style" => "display:none"), $_REQUEST["targetdatabase"]);

$html .= "<b>Execute SQL Statements</b><br>";   

	

$code = htmlspecialchars ($_REQUEST["SQL"]);

$html .= "<textarea id=\"SQL\" name=\"SQL\" >".$code."</textarea>";	
$html .= '<script> var editor = CodeMirror.fromTextArea(document.getElementById("SQL"), {
			height: 800,
                                      lineNumbers: true,
                                      matchBrackets: true,
                                      mode: "text/x-mysql",
                                      indentUnit: 4,
                                      indentWithTabs: true,
                                      enterMode: "keep",
							                        smartIndent: false,
                                      tabMode: "shift",
                                      theme: "rubyblue",
							                        onCursorActivity: function() {
            													  editor.setLineClass(hlLine, null, null);
            													  hlLine = editor.setLineClass(editor.getCursor().line, null, "activeline");
            												  }
                                    });
						            var hlLine = editor.setLineClass(0, "activeline");
                      </script>';

//targetting which database
$html .= input (array("type" => "button", "style" => "width: 100px", "onclick" => " document.getElementById('SQL').value = editor.getValue();   setprocessSQL('execute');   call_cdeajax('ajaxProcessSQL', 'content', false)"), "Execute SQL");

if ($_REQUEST["targetdatabase"] == "app") {
  $TARGET = $CDE;
}
 else {
 //Script Saving for CDE
 $TARGET = new CDESimple ("../../../database/cde.db", "", "", "sqlite3", false, "dd/mm/YYYY");
}
$result = $TARGET->get_records ($_REQUEST["SQL"]);

if ($result) {
	$randomname = "output".rand(1000,9999).".json";
	
	mkdir ("../../../tmp/", 0755, true);
	file_put_contents ("../../../tmp/{$randomname}", w2ui_parse_data ($result, $TARGET->fieldinfo));
	
	foreach ($TARGET->fieldinfo as $fid => $field) {
	  $columns[] = w2ui_column ($field["name"], $field["name"],  $size="20", $alignment="left", $sortable=true, $resizable=true);
	}
	
	$html .= w2ui_grid ($title="Results", 
									$name="resultgrid", 
									$columns, 
									$datapath="tmp/".$randomname, 
									$width="100%", 
									$height="400px", 
									$hastoolbar=true, 
									$hasfooter=true, 
									$multiselect=false, 
									$onclick="");
}
  else {
  $html .= $CDE->get_error();
  $html .= pre (print_r ($CDE->lasterror, 1));
}

$html .= script ("setprocessSQL('none');");

echo $html;

?>