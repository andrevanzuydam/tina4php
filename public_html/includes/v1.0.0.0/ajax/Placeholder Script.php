<?php
/*
Script Name: Placeholder Script
Purpose:
Please dont remove this script as you will mess up the development environment for updates
Created By: Admin
Created Date: 2014-04-25 12:47:05
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
$_REQUEST["ajaxscriptname"] = "Placeholder Script";
//Andre: Remove this script at your own peril

?>