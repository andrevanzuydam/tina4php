#!/usr/bin/php -q
<?php
/*
Using daemon code from Kevin <kevin@vanzonneveld.net>
copyright 2008 Kevin van Zonneveld

*/


$workingpath = realpath(dirname(__FILE__));



//Connection to the main database for the system
require_once ("connection.php");
global $_CDEDEV_SESSION_NAME;
//connection to the DEV database
$DEV = new CDESimple ($workingpath."/database/cde.db", "", "", "sqlite3", false, "dd/mm/YYYY");

//get the release
$release = file_get_contents ($workingpath."/release.inc");

// Allowed arguments & their defaults
$runmode = array(
    'no-daemon' => false,
    'help' => false,
    'write-initd' => false,
);

// Scan command line attributes for allowed arguments
foreach ($argv as $k=>$arg) {
    if (substr($arg, 0, 2) == '--' && isset($runmode[substr($arg, 2)])) {
        $runmode[substr($arg, 2)] = true;
    }
}

// Help mode. Shows allowed arguments and quit directly
if ($runmode['help'] == true) {
    echo 'Usage: '.$argv[0].' [runmode]' . "\n";
    echo 'Available runmodes:' . "\n";
    foreach ($runmode as $runmod=>$val) {
        echo ' --'.$runmod . "\n";
    }
    die();
}

require_once $workingpath."/daemon/Daemon.php";

if (file_exists ($workingpath."/includes/v{$release}/functions.php")) {
  require_once $workingpath."/includes/v{$release}/functions.php";
}

// Setup
$options = array(
    'appName' => strtolower('Daemon'.$_CDEDEV_SESSION_NAME),
    'appDir' => dirname(__FILE__),
    'appDescription' => 'Runs TINA4 daemons based on scripts flagged for deployment',
    'authorName' => 'Andre van Zuydam',
    'authorEmail' => 'andre@spiceware.co.za',
    'sysMaxExecutionTime' => '0',
    'sysMaxInputTime' => '0',
    'sysMemoryLimit' => '1024M',
);

System_Daemon::setOptions($options);
System_Daemon::log(System_Daemon::LOG_INFO, $_CDEDEV_SESSION_NAME." TINA4 daemon starting, make sure you have the right permissions to run this");

if (!$runmode['no-daemon']) {
  System_Daemon::start();
  System_Daemon::log(System_Daemon::LOG_INFO, $_CDEDEV_SESSION_NAME." TINA4 daemon running!"); 
}

$stopDaemon = false;

while (!System_Daemon::isDying() && !$stopDaemon) {
  //pull and eval code from the environment
  $sql = "select * from tblscript where scripttype = 'api' and release = 'v{$release}' and status = 2";
  $scripts = $DEV->get_row ($sql); 

  if (count ($scripts) > 0) {  
    foreach ($scripts as $sid => $script) {
      System_Daemon::log(System_Daemon::LOG_INFO, "Running ".$script->SCRIPTNAME);     
      eval ($script->CONTENT);
      System_Daemon::iterate(2); 
    }
  }
  
  System_Daemon::iterate(2);
}

System_Daemon::stop();
