<?php
/*
  Library to implement tw-sack.js in php
*/
/**********************************************************
 Name : include sack js
 Description : Components for SACK
 Revision : 1.05
 Created By : andre
 Last Modified by Andre van Zuydam on 09/08/2010 08:32:43
**********************************************************/
$dir = dirname(__FILE__);
$dir = str_replace ($_SERVER["DOCUMENT_ROOT"], "", $dir);

echo '<script type="text/javascript" src="'.$dir.'/tw-sack.js"></script>';
?>
