<?php
/*
Script Name: ajaxSaveScript
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
$_REQUEST["ajaxscriptname"] = "ajaxSaveScript";
//Script Saving for CDE
$DEV = new CDESimple ("../../../database/cde.db", "", "", "sqlite3", false, "dd/mm/YYYY");
require_once "../../../cdedev.php";
$cdedev = new cde_cdedev ($CDE, $_REQUEST, $outputpath="includes");
$script   = new dev_script ($DEV, $_REQUEST, $cdedev, $_REQUEST["interfaceid"]);
$script->update_script ($_REQUEST["scriptid"], $_REQUEST["edtScriptName"], $_REQUEST["edtScriptCategory"], $_REQUEST["edtScriptPurpose"], $_REQUEST["editcode"], $_REQUEST["edtScriptStatus"], $moduleid=0, $_SESSION["devusername"]);
echo date("d/m/Y h:i:s")."{$_REQUEST["edtScriptName"]} saved ....";

?>