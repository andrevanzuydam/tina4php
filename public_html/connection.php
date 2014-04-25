<?php
   require_once "cdesimple.php";
   $_CDEDEV_VERSION = "1.0.0.0";
   $_CDEDEV_PATH = "includes/v".$_CDEDEV_VERSION."/";
   global $_CDEDEV_PATH;
   $_CDEDEV_DATABASE = dirname(__FILE__)."/application.db";
   $_CDEDEV_DATABASE_USER = "";
   $_CDEDEV_DATABASE_PASSWORD = "";
   $_CDEDEV_DATABASE_TYPE = "sqlite3";
   global $CDE;
   //$DBCONNECT2 = new CDESimple ($_CDEDEV_DATABASE, $_CDEDEV_DATABASE_USER, $_CDEDEV_DATABASE_PASSWORD, $_CDEDEV_DATABASE_TYPE, false, "YYYY-mm-dd");
   
   $CDE = new CDESimple ($_CDEDEV_DATABASE, $_CDEDEV_DATABASE_USER, $_CDEDEV_DATABASE_PASSWORD, $_CDEDEV_DATABASE_TYPE, false, "YYYY-mm-dd");
   
   //attach secondary connections
   //$CDE->DBCONNECT2 = $DBCONNECT2;
   
   $_CDEDEV_SESSION_NAME = "TINA4PHP";
   
   
?>