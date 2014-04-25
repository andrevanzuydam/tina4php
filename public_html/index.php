<?php
require_once "cdesimple.php";
require_once "connection.php";
session_name ($_CDEDEV_SESSION_NAME);
session_start();

require "cdedev.php";

if (!isset($_REQUEST["release"])) {
  $release = file_get_contents ("release.inc");
  $_REQUEST["release"] = "v".$release;
}

echo $cdedev = new cde_cdedev ($CDE, $_REQUEST, $outputpath="includes");
?>