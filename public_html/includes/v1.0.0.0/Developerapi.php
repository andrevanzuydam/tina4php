<?php
/*
Script Name: Developerapi
Purpose:
To enable developing to happen
Created By: Andre van Zuydam
Created Date: 2014-03-12 17:19:32
*/
class dev_sql {
   public $CDE;
   public $DEV;
   public $request;
   
  function __construct ($DEV, $request, $CDE, $contextprefix) {
     $this->DEV = $DEV;
     $this->CDE = $CDE->CDE;
     $this->request = $request;       
	 $this->contextprefix = $contextprefix;
  }

  function __toString () {
    return $this->display(); 
  }

  function get_nodes ($CDE, $prefix="") {
     $database = $CDE->get_database ();
	 $tablenodes = array();
	 //echo "<pre>".print_r ($database, 1)."</pre>";
	 foreach ($database as $tablename => $columns) {
	 	$columnnodes = array();
		foreach ($columns as $cid => $column) {
		  $columnnodes[] = w2ui_sidebar_node ($prefix."_-".$tablename."_-".$column["field"], $column["field"] , $img="fa-star");
		}
	
	   $tablenodes[] = w2ui_sidebar_node ($prefix."_-".$tablename."_-", $tablename, $img="fa-star", $group=false, $expanded=false, $columnnodes); 	
	 }
	 
	 return $tablenodes;
  }


  function display () {
    
    $appnodes = $this->get_nodes ($this->CDE, "app");	
	$devnodes = $this->get_nodes ($this->DEV, "dev");
	
	$nodes[] = w2ui_sidebar_node ($name="datadb", $caption="Application Database", $img="fa-star", $group=true, $expanded=true, $appnodes); 
	$nodes[] = w2ui_sidebar_node ($name="devdb", $caption="Development Database", $img="fa-star", $group=true, $expanded=true, $devnodes); 
	
	$sidebar = w2ui_sidebar ($name="navsidebar", $width="300px", $height="100%", $nodes, "call_cdeajax ('ajaxProcessSQL', 'content', false, { metatarget : event.target } );"); 
		
	$html .=  '<link rel="stylesheet" href="'.$this->contextprefix.'/codemirror/lib/codemirror.css">
					<link rel="stylesheet" href="'.$this->contextprefix.'/codemirror/theme/monokai.css">
                    <script src="'.$this->contextprefix.'/codemirror/lib/codemirror.js"></script>
					<script src="'.$this->contextprefix.'/codemirror/mode/mysql/mysql.js"></script>';	
		
	$html .= div(array("style" => "height:800px"),
	                 div(array("id"=>"sidebar", "style"=>"float:left; width: 300px; height:100%;"), $sidebar),
				     div(array("id"=>"content", "style"=>"float:left; width:75%; height: 100%; margin-left:1em;") )
				 );
				 
   
	
    return $html;    
  }

}


class dev_release  {
  public $CDE;
  public $request;
  public $cdedev;
  public $contextprefix;
  

  function dev_release ($CDE, $request, $cdedev, $contextprefix) {
	  $this->CDE = $CDE;
	  $this->request = $request;
      $this->cdedev = $cdedev;	
	  $this->contextprefix = $contextprefix;
     
   }	
	
	function __toString() {
		return $this->draw();	
	}
	
	function draw () {
	  $CDE = $this->CDE;
	  $request = $this->request;
	  $cdedev = $this->cdedev;
	  
	  $html = $CDE->switchid ("releaseid");
	  
	  switch ($request["releaseid"]) {
	    default:
		  if ($request["moduleprocessid"] == 200) {
		    $html .=  "<b> Choose Style Sheet</b><br />";
			if ($this->request["csstype"] == "") {
			  $this->request["csstype"] = "css";
			}
			$html .= $CDE->select ("csstype", 300, "Choose a css type", "array", "css,Default|csssmpl,Smartphones (Portrait & Landscape)|csssml,Smartphones(Landscape)|csssmp,Smartphones(Portrait)|cssipadpl,iPads(Portrait & Landscape)|cssipadl,iPads(Landscape)|cssipadp,iPads(Portrait)|csslaptop,Desktops & Laptops|csslarge,Large Screens|cssiphone,iPhone4 & iPhone5", $this->request["csstype"], "onchange=\"document.forms[0].submit();\"")."<br />";  	  
		  }
	      $html .= "<b> Choose Release </b><br />";
	      $html .= $CDE->select ("release", 140, "Choose a release version", "sql", "select distinct release, release as data from tblscript order by release desc", $this->request["release"], "onchange=\"document.forms[0].submit();\"")."<br />";
	     
		  $_REQUEST["edtOption"] = 1;
		  
		  
		  $html .= $CDE->input("btnOption", 0, "Major", "", "radio", "1",  $event="")."Major";
	      $html .= $CDE->input("btnOption", 0, "Minor", "", "radio", "2", $event=" checked ")."Minor <br />";
	      $html .= $CDE->input("btnRelease", 110, "Click here to do a release", "", "button", "New Version", $event="onclick=\"setreleaseid(100, true);\"")."<br />"; 
		  $html .= $CDE->input("btnUnRelease", 110, "Click here to roll back a release", "", "button", "Delete Version", $event="onclick=\"if (confirm('This will perform a roll back of a release but not delete the files for the release. Continue?')) { setreleaseid(200, true); }\"")."<br />";
		  $html .= $CDE->input("btnMakeRelease", 110, "Click here to make this the current release", "", "button", "Make Release", $event="onclick=\"if (confirm('This will set the version of the web application to this version. Continue?')) { setreleaseid(300, true); }\""); 
		 
		  if ($request["releaseNOTE"] != "") {
		    //add into the database
			 $newid = $CDE->get_next_id ("tblreleasenote", "releasenoteid");
			 $sqlinsert = "insert into tblreleasenote (releasenoteid,releasenote,createdby,datecrt,release)
	                             values (?,?,?,?,?)";
	         $CDE->exec ($sqlinsert, $newid, $request["releaseNOTE"],$_SESSION["devusername"], date("Y-m-d H:i"), $this->request["release"]);
		  }
		  
		  $_REQUEST["releaseNOTE"] = "";

		  /*
		  $html .= "<br><b>Release Notes </b><br />";
		  $html .= $CDE->input("releaseNOTE", 200, "Release Note", "", "text", "", $event="");
		  $html .= $CDE->input("btnAddNote", 30, "Click here to add a release note", "", "button", "Add", $event="onclick=\"document.forms[0].submit();\""); 
		  $sqlselect = "select * from tblreleasenote where release = '{$this->request["release"]}' order by datecrt desc";
		  $releasenotes = $CDE->get_row ($sqlselect);
		  
		  $html .= "<div style=\"width: 200px; overflow: auto; height: 200px;\">";
		  $html .= "<pre>";
		  foreach ($releasenotes as $rid => $releasenote) {
		    $html .= "{$releasenote->DATECRT}: {$releasenote->RELEASENOTE}\n";
		  }
		  $html .= "</pre>";
		  $html .= "</div>";
		  
		  */
		  
		  
		  
		  
		 
		  $html .= "<br><b> Search Code </b><br>";
		  $html .= $CDE->input("srchText", 0, "Search code", "", "text", "", $event="")."<br>";
		  $html .= $CDE->input("btnSearch", 110, "Click here to search for some text", "", "button", "Search", $event="onclick=\"document.forms[0].submit();\""); 
		  $html .= "<div id=\"saveScript\"></div>";
		  
		  if ($request["srchText"] != "") {
		     $sqlsearch = "select s.*, (select interfaceid from tblinterface where name = s.interface) as interfaceid from tblscript s where content like '%{$request["srchText"]}%' and release = '{$_REQUEST["release"]}'";
			 $result = $CDE->get_row ($sqlsearch);
			 $html .= "<ul>";
			 foreach ($result as $sid => $script) {
			   //get the lines with that text on
			   $content = explode ("\n", $script->CONTENT);
			   $linecount = "";
			   foreach ($content as $cid => $line ) {
			     if (strpos ($line, $request["srchText"]) !== false) {
				   $linecount .= "(".($cid+1).") ".htmlentities ($line)."<br>";
				 }
			   }
			   
			   
			   $html .= "<li><a href=\"http://{$_SERVER["HTTP_HOST"]}/?release={$_REQUEST["release"]}&interface=Developer&interfaceid={$script->INTERFACEID}&scriptid={$script->SCRIPTID}&scriptprocessid=110{$extra}\" target=\"_blank\">{$script->SCRIPTNAME} {$script->INTERFACE} {$script->CATEGORY}</a><br>{$linecount}</li>";
			 }
			 $html .= "</ul>";
		  }
		  
		  $html .=  "<br>";
		  

		  
		break;
		case 100:
		   $release = $cdedev->do_release ($request["btnOption"] != 1); 
		   $html .= "<script> window.alert ('$release has been created.'); setreleaseid (0, true); </script>";
		break;
		case 200:
		  if ($request["release"] != "v1.0.0.0") {
		    $CDE->exec ("delete from tblscript where release = '{$request["release"]}'");
			$html .= "<script> window.alert ('{$request["release"]} has been removed.'); setreleaseid (0, true); </script>";
		  }
		    else {
			 $html .= "<script> window.alert ('Cannot roll back a core release !'); setreleaseid (0, true); </script>";
		  }
		break;
		case 300:
		   $release = substr($request["release"], 1);
		   file_put_contents ("release.inc", $release);
		   $html .= "<script> window.alert ('{$request["release"]} is now current !'); setreleaseid (0, true); </script>";
		break;
	  }
	  return $html;
	}

}

/*
Script Name: CDE Developer Script
Purpose:
This script makes the development happen
Created By: Andre van Zuydam
Created Date: 
*/

class dev_script {
  public $CDE;
  public $request;
  public $cdedev;
  public $interfaceid;
  
  function dev_script ($CDE, $request, $cdedev, $interfaceid, $contextprefix) {
	  $this->CDE = $CDE;
	  $this->request = $request;
      $this->cdedev = $cdedev;	
      $this->interfaceid = $interfaceid;
	  $this->contextprefix = $contextprefix;
   }	
	
	function __toString() {
		return $this->draw();	
	}
	
	function insert_script ($scriptname, $content, $interface, $scripttype, $release, $createdby, $purpose="", $moduleid=0) {
    $DEV = $this->cdedev->DEV;
    $version = "1.0.0.0";
    
    $scriptid = $DEV->get_next_id ("tblscript", "scriptid");
    if ($scriptname == "") $scriptname = "New".$scriptid;
    $sqlinsert = "insert into tblscript (scriptid, scriptname, content, interface, scripttype, release, createdby, purpose, moduleid, version, status, datecrt)
                                 values (?       , ?         , ?      , ?        , ?         , ?      , ?        , ?      , ?,        ?,       ?, 'now')";
    
    $DEV->exec ($sqlinsert, $scriptid, $scriptname, $content, $interface, $scripttype, $release, $createdby, $purpose="", $moduleid=0, $version, 0);
  
    return $scriptid;
  }
  
  function check_code ($code, $root, $outputpath, $hostname) {
      $result = true;
      $filename = "check".rand(1000, 9999).".php";
      $testname = $root."/".$outputpath."/".$filename;
      
      file_put_contents ($testname, $code);
      
	  
      $error = file_get_contents ("http://".$hostname."/".$outputpath."/".$filename);     
      
	
      if (strpos ($error, "Parse error") !== false) {
        $result = false;
       echo "<script> window.alert ('". addslashes(str_replace ("\n", "", strip_tags($error)))."'); </script>";
      } 
      unlink ($testname);
      return $result;
    }
  	
  
  
  function update_script ($scriptid, $scriptname, $scriptcategory, $purpose, $content, $status, $moduleid, $lastuser, $deletefile=true, $removelock=true) {
    $DEV = $this->cdedev->DEV;
    $request = $this->request;
	
	
    if ($scriptid == "") {
      $interface = $DEV->get_value (0, "select * from tblinterface where interfaceid = {$this->interfaceid}");
      if ($request["moduleprocessid"] == 100) {
        $scriptname = $interface->NAME;
        $interfacename = $interface->NAME;
        $scripttype = "interface";
      }
        else 
      if ($request["moduleprocessid"] == 200) {  
        $scriptname = $interface->NAME.$request["csstype"];
        $interfacename = $interface->NAME;
        $scripttype = "css";  
      }
      $release = $request["release"];
      $scriptid = $this->insert_script ($scriptname, $content, $interfacename, $scripttype, $release, $_SESSION["devusername"], $purpose="", $moduleid=0);
   }
    //$content = str_replace ("'", "\'", $content);
	
    $script = $DEV->get_value (0, "select scriptname, scripttype, release, interface, status, version  from tblscript where scriptid = {$scriptid}");
	
	//add this script into the tblrevision
	$sqlrevision = "insert into tblrevision (scriptid, scriptname, purpose, interface, version, release, content, scripttype, status, createdby, editedby, lastuser, datemod, datecrt, moduleid) 
	                     select scriptid, scriptname, purpose, interface, version, release, content, scripttype, status, createdby, editedby, lastuser, datemod, datecrt, moduleid 
						 from tblscript where scriptid = {$scriptid}";
	$DEV->exec ($sqlrevision);
	
	
	$sqldelrevision = "delete from tblrevision where scriptid = {$scriptid} and version not in (
                             select version from tblrevision where scriptid = {$scriptid} order by version desc limit 30)";
	
	$DEV->exec ($sqldelrevision);
	
    $version = $script->VERSION;
	
	//fix this code here - very bad
	$noincrement = false;
	$version = explode (".", $version);
	foreach ($version as $vid => $ver) {
	  if ($vid > 0) {
	    if ($ver == 9) {
		  $version[$vid] = 0;
		  $version[$vid-1]++;
		  $noincrement = true;
		}
	  }
	}
	
	$version = $version[0].".".$version[1].".".$version[2].".".$version[3];
	if (!$noincrement) $version++;
	
	
    $sqlupdate = "update tblscript set scriptname = ?, scriptcategory = ?, purpose = ?, content = ?, status = ?, moduleid = ?, editedby = '', lastuser = ?, version = ?, datemod = 'now'
                         where scriptid = ?";
              
    $DEV->exec($sqlupdate, $scriptname, $scriptcategory, $purpose, $content, $status, $moduleid, $lastuser, $version, $scriptid);   
	
	if ($removelock) {
	  $DEV->exec ("delete from lnkscript_user where scriptid = ?", $scriptid);	
	}
	
    
    if ($deletefile && $content != "") {
     
	 $script = $DEV->get_value (0, "select scriptname, scripttype, release, interface, status  from tblscript where scriptid = {$scriptid}");
      if ($script->SCRIPTTYPE == "interface") {
        $scriptfilename = $this->cdedev->root."/".$this->cdedev->outputpath."/".$script->RELEASE."/".$script->SCRIPTNAME.".php"; 
      }
        else 
      if ($script->SCRIPTTYPE == "css") {
         $scriptfilename = $this->cdedev->root."/".$this->cdedev->outputpath."/".$script->RELEASE."/css/".$script->INTERFACE."css.css"; 
      }
        else 
      if ($script->SCRIPTTYPE == "api" || $script->SCRIPTTYPE == "global") {
	    
		if ($script->SCRIPTTYPE == "global" && $script->STATUS == 1) {
		  $scriptfilename = $this->cdedev->root."/".$this->cdedev->outputpath."/".$script->RELEASE."/functions.php";
		}
		 else
		if ($script->STATUS == 1) {
           $scriptfilename = $this->cdedev->root."/".$this->cdedev->outputpath."/".$script->RELEASE."/".$script->INTERFACE."api.php";      
        }
		  else {
		  $scriptfilename = $this->cdedev->root."/".$this->cdedev->outputpath."/".$script->RELEASE."/ajax/".$script->SCRIPTNAME.".php";      
		}  
	  } 
	 
	 if ($script->SCRIPTTYPE == "css") {
	    //echo "updating $scriptfilename";
		$this->cdedev->ainterface->get_css ($script->INTERFACE, $this->cdedev->root, $this->cdedev->outputpath, $this->cdedev->hostname);
		
	 }
	   else {
		 $apicontent = "";
		 if ($script->STATUS == 0 && $script->SCRIPTTYPE != "interface") {
		   $code = $content;
		   $content = "/*\nScript Name: {$script->SCRIPTNAME}\n";
           $content .= "Purpose:\n{$script->PURPOSE}\n";
           $content .= "Created By: {$script->CREATEDBY}\n";
           $content .= "Created Date: {$script->DATECRT}\n";
           $content .= "*/\n";
           $content .= "require_once ('".$this->cdedev->root."/cdesimple.php');\n";
           $content .= "require_once ('".$this->cdedev->root."/connection.php');\n";
		   $content .= "require_once ('".$this->cdedev->root."/shape.php');\n";
           $content .= "if (file_exists('".$this->cdedev->root."/".$this->cdedev->outputpath."/".$script->RELEASE."/functions.php')) {\n  require_once ('".$this->cdedev->root."/".$this->cdedev->outputpath."/".$script->RELEASE."/functions.php');\n}\n";
           $content .= "if (file_exists('".$this->cdedev->root."/".$this->cdedev->outputpath."/".$script->RELEASE."/{$script->INTERFACE}api.php')) { require_once ('".$this->cdedev->root."/".$this->cdedev->outputpath."/".$script->RELEASE."/{$script->INTERFACE}api.php'); }\n\n";
           $content .= 'session_name($_CDEDEV_SESSION_NAME);'."\n";
           $content .= 'session_start();'."\n";
		   $content .= '$_REQUEST["ajaxscriptname"] = "'.$script->SCRIPTNAME.'";'."\n";
		   $content .= $code."\n"; 
		   $content = '<?'.'php'."\n".$content."\n".'?'.'>';
		   $code = $content;
         }
		   else 
		 if ($script->STATUS == 1 && $script->SCRIPTTYPE != "interface")  {
		    //we need to recreate the whole api file
			$code =  '<?'.'php'."\n".$content."\n".'?'.'>';
			$sqlapi = "select * from tblscript where interface = '{$script->INTERFACE}' and release = '{$script->RELEASE}' and scripttype in ('api', 'global')  and scriptid <> {$scriptid} and status = 1";
			//error_log ($sqlapi);
			$apis = $DEV->get_row ($sqlapi);
			//current script
			
			$apicontent = "/*\nScript Name: {$script->SCRIPTNAME}\n";
            $apicontent .= "Purpose:\n{$script->PURPOSE}\n";
            $apicontent .= "Created By: {$script->CREATEDBY}\n";
            $apicontent .= "Created Date: {$script->DATECRT}\n";
            $apicontent .= "*/\n";
		    $apicontent .= $content."\n";
			
			foreach ($apis as $apiid => $api) {
			  $apicontent .= "/*\nScript Name: {$api->SCRIPTNAME}\n";
              $apicontent .= "Purpose:\n{$api->PURPOSE}\n";
              $apicontent .= "Created By: {$api->CREATEDBY}\n";
              $apicontent .= "Created Date: {$api->DATECRT}\n";
              $apicontent .= "*/\n";
			  $content = str_replace ("''", "'", $api->CONTENT);
              $apicontent .= $content."\n";
     		}
			
			
		 }
		   else {
		   $code =  '<?'.'php'."\n".$content."\n".'?'.'>'; 
		 }  
		 		 		 
		 if ($this->check_code ($code, $this->cdedev->root,$this->cdedev->outputpath, $this->cdedev->hostname) ) {
		     unlink ($scriptfilename);  
			 //output the new code
			 if (($script->STATUS == 0 || $script->STATUS == 1) && $script->SCRIPTTYPE != "interface") {
			   
			   if ($script->STATUS == 0) {
			     file_put_contents ($scriptfilename, $content);
			   }
			     else {
				  
				 if ($script->INTERFACE != "Developer") { 
				   $apicontent = '<?'.'php'."\n".$apicontent."\n".'?'.'>';
				   file_put_contents ($scriptfilename, $apicontent);
				 }
				 //error_log ($scriptfilename);
			   }	 
			 }  
		 } 
	 }
	
	  
    }
           
    return true;
  }
  
  function get_script ($scriptname, $interface, $release, $editedby="", $setid=true, $version="") {
    $DEV = $this->cdedev->DEV;
	
	if ($version == "") {
      $sqlscript = "select * from tblscript where scriptname = '{$scriptname}' and interface = '{$interface}' and release = '{$release}'";
    }
	  else {
	  $sqlscript = "select * from tblrevision where scriptname = '{$scriptname}' and interface = '{$interface}' and release = '{$release}' and version = '{$version}'"; 
	}  
	
	$content = $DEV->get_value (0, $sqlscript);
    if ($setid) {
      $this->scriptid = $content->SCRIPTID;
    }
    $content = $content->CONTENT;  
    $content = str_replace ("''", "'", $content);
    
    return $content;
  }
  
  function delete_script ($scriptid) {
    $DEV = $this->cdedev->DEV;
	
	$script = $DEV->get_value (0, "select scriptname, scripttype, release, interface  from tblscript where scriptid = {$scriptid}");
    if ($script->SCRIPTTYPE == "interface") {
        $scriptfilename = $this->cdedev->root."/".$this->cdedev->outputpath."/".$script->RELEASE."/".$script->SCRIPTNAME.".php"; 
      }
        else 
    if ($script->SCRIPTTYPE == "css") {
       $scriptfilename = $this->cdedev->root."/".$this->cdedev->outputpath."/".$script->RELEASE."/css/".$script->SCRIPTNAME.".css"; 
    }
        else 
    if ($script->SCRIPTTYPE == "api") {
       $scriptfilename = $this->cdedev->root."/".$this->cdedev->outputpath."/".$script->RELEASE."/".$script->INTERFACE."api.php";      
    } 
    unlink ($scriptfilename);   
	
    $sqldelete = "delete from tblscript where scriptid = {$scriptid} ";
	$DEV->exec ($sqldelete);
	
    return true;
  }
  
  function show_script ($scriptname, $interface, $release, $showtype="php", $editedby) {
		$request = $this->request;
		$cdedev = $this->cdedev;
		$DEV = $this->cdedev->DEV;
		$script = $DEV->get_value (0, "select * from tblscript where scriptname = '{$scriptname}' and interface = '{$interface}' and release = '{$release}' ");
    
    $request["editcode"] = "";
    
    if ($showtype == "php") {           
      $html .=  '
							<link rel="stylesheet" href="'.$this->contextprefix.'/codemirror/addon/fold/foldgutter.css" />
							<script src="'.$this->contextprefix.'/codemirror/addon/fold/foldcode.js"></script>
							<script src="'.$this->contextprefix.'/codemirror/addon/fold/foldgutter.js"></script>
							 <script src="'.$this->contextprefix.'/codemirror/addon/fold/brace-fold.js"></script>
							 <script src="'.$this->contextprefix.'/codemirror/addon/fold/xml-fold.js"></script>
							 <script src="'.$this->contextprefix.'/codemirror/addon/fold/markdown-fold.js"></script>
							 <script src="'.$this->contextprefix.'/codemirror/addon/fold/comment-fold.js"></script>
	  						 <script src="'.$this->contextprefix.'/codemirror/mode/xml/xml.js"></script>
							 <script src="'.$this->contextprefix.'/codemirror/mode/javascript/javascript.js"></script>
							 <script src="'.$this->contextprefix.'/codemirror/mode/css/css.js"></script>
							 <script src="'.$this->contextprefix.'/codemirror/mode/clike/clike.js"></script>
							 <script src="'.$this->contextprefix.'/codemirror/mode/php/php.js"></script>';
		  $mode = "text/x-php";
		}
		 else
		if ($showtype == "css") {
		  $html .=  '<script src="'.$this->contextprefix.'/codemirror/mode/css/css.js"></script>';
      $mode = "text/css"; 
    }

    
    $_REQUEST["editcode"] = "";
    $html .= "<table class=\"dataform\" style=\"width: 100%;\">";
	$_REQUEST["edtScriptName"] = $script->SCRIPTNAME;
	$_REQUEST["edtScriptCategory"] = $script->SCRIPTCATEGORY;
	$_REQUEST["edtScriptPurpose"] = $script->PURPOSE;
	
	if ($script->SCRIPTTYPE == "api" || $script->SCRIPTTYPE == "global") {
	  
	  $html .= "<tr><td>Script Name ({$script->SCRIPTID}) ".$DEV->input("edtScriptName",  $width=200, $alttext="Type in the name of the script", $compulsory="", $inputtype="text")." Category ".$DEV->input("edtScriptCategory",  $width=200, $alttext="Type in the name of the script category", $compulsory="", $inputtype="text").
	  " Script Type ".$DEV->select ("edtScriptStatus", 140,  "Choose Script Status", "array", "0,AJAX|1,Include|2,Daemon", $script->STATUS)." ".$DEV->select ("chooseVersion", 140, "Choose a version", "sql", "select distinct version, version||' '||datemod||' '||lastuser as data from tblrevision where scriptid = {$script->SCRIPTID} order by version desc", "", "onchange=\"document.forms[0].submit();\"")."</td></tr>";
	  
	  $sqlinsert = "insert into lnkscript_user (userid, scriptid, release, sessionid)
                      values (?, ?, ?, ?)";
	
	  $DEV->exec ($sqlinsert, $_SESSION["devuserid"], $script->SCRIPTID, $script->RELEASE, session_id());
	 
	  //check who else is working on this script
	   
	  $working = $DEV->get_row ("select * from tbluser where sessionid in (select sessionid from lnkscript_user where scriptid = {$script->SCRIPTID}) and userid <> {$_SESSION["devuserid"]}");
	  
	  $workhtml = "";
	  foreach ($working as $wid => $workuser) {
	    $workhtml .= "<span style=\"color: red; font-weight:bold\">{$workuser->NAME}</span>";
	  }
	  
	  $html .= "<tr><td>Purpose, Edited Currently by: {$workhtml}</td></tr>";
	  $html .= "<tr><td><textarea style=\"width:94%\" name=\"edtScriptPurpose\" cols=\"10\">{$_REQUEST["edtScriptPurpose"]}</textarea></td></tr>";
	  //$html .= " <tr><td></td></tr>";
	
	}
	  else {
	  $html .= "<tr><td>Previous Versions: ".$DEV->select ("chooseVersion", 140, "Choose a version", "sql", "select distinct version, version||' '||datemod as data from tblrevision where scriptid = {$script->SCRIPTID} order by version desc", "", "onchange=\"document.forms[0].submit();\"")."</td></tr>";
	  $html .= $DEV->input("edtScriptName",  $width=200, $alttext="", $compulsory="", $inputtype="hidden");
	  $html .= $DEV->input("edtScriptPurpose",  $width=200, $alttext="", $compulsory="", $inputtype="hidden");
	   
	}
	
	//$html .= "<tr><td style=\"width:200px\">Previous Versions</td><td></td></tr>";
	
    
	$html .= "<tr><td>";
	$html .= $DEV->input("btnUpdate",  $width=100, $alttext="Click here to save", $compulsory="", $inputtype="button", $value="Update", $event="onclick=\"document.forms[0].editcode.value = editor.getValue(); setscriptid('{$script->SCRIPTID}', false);  call_cdeajax('ajaxSaveScript', 'saveScript', false); \"");                  
	$html .= $DEV->input("btnSave",  $width=100, $alttext="Click here to save", $compulsory="", $inputtype="button", $value="Save & Close", $event="onclick=\"document.forms[0].editcode.value = editor.getValue(); setscriptid('{$script->SCRIPTID}', false);  setscriptprocessid (200, true); \"");                  
    $html .= $DEV->input("btnCancel",  $width=100, $alttext="Click here to cancel", $compulsory="", $inputtype="button", $value="Cancel", $event="onclick=\"document.forms[0].editcode.value = editor.getValue(); setscriptid('0', false);  setscriptprocessid (0, true); \"");                  
	
	$html .= $DEV->input("editcode",  $width=100, $alttext="", $compulsory="", $inputtype="hidden");
	//style=\"height: 500px;\"
    $html .= "<textarea  id=\"deveditor\">".htmlspecialchars($this->get_script ($scriptname, $interface, $release, $editedby, true, $_REQUEST["chooseVersion"]))."</textarea>";
    $html .= "<style> .activeline {background: #3399FF !important;} </style>";
	// Add this to automatically close brackets and quotes : autoCloseBrackets: true,
    $html .= '<script> 
	                  
	
	
	                    var editor = CodeMirror.fromTextArea(document.getElementById("deveditor"), {
                                      lineNumbers: true,
									  highlightSelectionMatches: true,
                                      matchBrackets: true,
                                      mode: "'.$mode.'",
                                      indentUnit: 4,
                                      indentWithTabs: true,
									  lineWrapping: true,
									  extraKeys: {"Ctrl-Q": function(cm){ cm.foldCode(cm.getCursor()); }, 
									              "Ctrl-/": function (cm) { cm.replaceSelection ("//'.$_SESSION["username"].':"); }, 
												  "Ctrl-.": function (cm) { cm.replaceSelection ("//'.$_SESSION["username"].'#:"); } 
												  },
								 	  foldGutter: true,
								  	  gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
									  
                                      enterMode: "keep",
							                        smartIndent: false,
                                      tabMode: "shift",
                                      theme: "monokai",
							                        onCursorActivity: function() {
            													  editor.setLineClass(hlLine, null, null);
            													  hlLine = editor.setLineClass(editor.getCursor().line, null, "activeline");
            												  }
                                    });
						            //var hlLine = editor.setLineClass(0, "activeline");
									var editorSize = screen.availHeight - 150;
									editor.setSize(null, editorSize);
                      </script>';
	
    
    $html .= $DEV->input("btnUpdate",  $width=100, $alttext="Click here to save", $compulsory="", $inputtype="button", $value="Update", $event="onclick=\"document.forms[0].editcode.value = editor.getValue(); setscriptid('{$this->scriptid}', false);  call_cdeajax('ajaxSaveScript', 'saveScript', false); \"");                  
	$html .= $DEV->input("btnSave",  $width=100, $alttext="Click here to save", $compulsory="", $inputtype="button", $value="Save & Close", $event="onclick=\"document.forms[0].editcode.value = editor.getValue(); setscriptid('{$this->scriptid}', false);  setscriptprocessid (200, true); \"");                  
    $html .= $DEV->input("btnCancel",  $width=100, $alttext="Click here to cancel", $compulsory="", $inputtype="button", $value="Cancel", $event="onclick=\"document.forms[0].editcode.value = editor.getValue(); setscriptid('0', false);  setscriptprocessid (0, true); \"");                  
	$html .= "</td></tr>"; 
	$html .= "</table>";
	
	
	
    $html .= "<script>document.title = '{$script->SCRIPTNAME} - {$script->RELEASE} ({$script->VERSION})';</script>";
    $content = str_replace ("''", "'", $script->CONTENT);
	$oldscript = $DEV->get_value (0, "select * from tblrevision where scriptname = '{$scriptname}' and interface = '{$interface}' and release = '{$release}'  order by version desc");
    $oldcontent = str_replace ("''", "'", $oldscript->CONTENT);
    $changes = arr_diff (explode("\n", $oldcontent), explode("\n", $content), 0);
    $html .= "<div>Changes<br /><pre>".html_entity_decode(print_r(htmlspecialchars($changes), 1))."</pre></div>";
   
   return $html;
  
  }
  
  function show_scripts ($interface, $scripttype, $moduleid=0) {
    $DEV = $this->cdedev->DEV;
    $request = $this->request;
    
    $sqlscript = "select * from tblscript where release = '{$request["release"]}' and interface = '{$interface}' and scripttype = '{$scripttype}' and moduleid = {$moduleid} order by scriptcategory, datemod desc";
    $scripts = $DEV->get_row ($sqlscript);
    
   
    $html .= "<div id=\"scriptaccordion\">";
	$html .= "<h3>General Scripts</h3><div style=\"height: auto\">";
	$html .= "<table   class=\"datatable\">\n";
	$html .= "<tr><th>Script Id</th><th>Version</th><th>Script Name</th><th>Purpose</th><th>Script Type</th><th>Created By</th><th>Last User</th><th>Edited By</th><th>Date Created</th><th>Date Modified</th><th>".$DEV->input ("btnAdd", 60, "Click here to add a script", "", "button", $value="Insert", "onclick=\"setscriptid('', false); setscriptprocessid(100, true);\"")."</th></tr>";
    
	

 
    $altcolor = "#FFFFFF";
	$oldcategory = "";
	foreach ($scripts as $sid => $script) {
      $editbutton = $DEV->input ("btnEdit", 60, "Click here to add a script", "", "button", $value="Edit", "onclick=\"setscriptid('$script->SCRIPTID', false); setscriptprocessid(110, true);\"");
      $deletebutton = $DEV->input ("btnDelete", 60, "Click here to delete a script", "", "button", $value="Delete", "onclick=\"if (confirm('Delete {$script->SCRIPTNAME} ?')) { setscriptid('$script->SCRIPTID', false); setscriptprocessid(300, true); } \"");
     
	 if ($altcolor == "") {
	   $altcolor = "#FFFFFF";
	   $style = "";
	 }
	   else {
	   $style = "style=\"background-color: #FFFFFF\"";
	   $altcolor = "";
	 }  
	 if ($oldcategory != $script->SCRIPTCATEGORY) {
	   $oldcategory = $script->SCRIPTCATEGORY;
	  $html .= "</table></div>"; 
	  $html .= "<h3>$script->SCRIPTCATEGORY</h3><div>";
	  $html .= "<table   class=\"datatable\">\n";
	  $html .= "<tr><th>Script Id</th><th>Version</th><th>Script Name</th><th>Purpose</th><th>Script Type</th><th>Created By</th><th>Last User</th><th>Edited By</th><th>Date Created</th><th>Date Modified</th><th>".$DEV->input ("btnAdd", 60, "Click here to add a script", "", "button", $value="Insert", "onclick=\"setscriptid('', false); setscriptprocessid(100, true);\"")."</th></tr>";
     }
	 $html .= "<tr {$style}><td>{$script->SCRIPTID}</td><td>{$script->VERSION}</td><td><a href=\"javascript:void(0);\" onclick=\"setscriptid('$script->SCRIPTID', false); setscriptprocessid(110, true);\">{$script->SCRIPTNAME}</a></td><td>{$script->PURPOSE}</td><td>{$script->SCRIPTTYPE}</td><td>{$script->CREATEDBY}</td><td>{$script->LASTUSER}</td><td>{$script->EDITEDBY}</td><td>{$script->DATECRT}</td><td>{$script->DATEMOD}</td><td>{$editbutton} {$deletebutton}</td></tr>";    
    }
    
    $html .= "</table></div></div>";
    $html .= '<script> $(function() {
								$("#scriptaccordion").accordion({collapsible: true, heightStyle: "content", active: false});
								}); </script>';
    return $html;
  }
  
  
  function draw () {
    $DEV = $this->cdedev->DEV;
		$request = $this->request;
		$cdedev = $this->cdedev;
		$interface = $DEV->get_value (0, "select * from tblinterface where interfaceid = {$this->interfaceid}");
    $html = "";
                          
    //get the php4 sack include going
		require_once ($cdedev->root."/php4sack/PHP4sack.php");
		        
		$html .= $DEV->switchid ("scriptprocessid"); 
		$html .= $DEV->switchid ("scriptid");
    
    switch ($_REQUEST["scriptprocessid"]) { 
      //add / edit a script
      case 100:
        if ($scriptid == "") {
		  if ($request["moduleprocessid"] == 400) {
		    $interface = "";
			$scripttype =  "global";
		  }
		    else {
			$interface = $interface->NAME;
			$scripttype =  "api";
		  }	
          $scriptid = $this->insert_script ($scriptname="", $content="", $interface, $scripttype, $request["release"], $_SESSION["devusername"], $purpose="", $moduleid=0);     
          $html .= setscriptid($scriptid, false);
          $html .= setscriptprocessid(110, true);
        }
          else {
          $html .= setscriptprocessid(110, true);  
        }
      break;
      case 110:
        //show the script
        $script = $DEV->get_value (0, "select * from tblscript where scriptid = {$request["scriptid"]}");
        $html .= $this->show_script ($script->SCRIPTNAME, $interface->NAME, $request["release"], "php"); 
      break;
      //save a script
      case 200:
        //print_r ($request);
		
        $this->update_script ($request["scriptid"], $request["edtScriptName"], $request["edtScriptCategory"], $request["edtScriptPurpose"], $request["editcode"], $request["edtScriptStatus"], $moduleid=0, $_SESSION["devusername"]);
        $html .= setscriptid('', false);
		$html .= setscriptprocessid (0, true);
      break;
      //delete a script
      case 300:
	     $this->delete_script ($request["scriptid"]);
		 $html .= setscriptid('', false);
         $html .= setscriptprocessid (0, true);
      break;
      default:      
  		  //load the main interface script        
       if ($request["moduleprocessid"] == 100 || $request["scriptprocessid"] == "") {        
    		  $html .= $this->show_script ($interface->NAME, $interface->NAME, $request["release"], "php");        
        }
          else 
        if ($request["moduleprocessid"] == 200) {
		  if ($request["csstype"] == "") {
		    $request["csstype"] = "css";
		  }
		  
          $html .= $this->show_script ($interface->NAME.$request["csstype"], $interface->NAME, $request["release"], "css");  
        }
          else 
        if ($request["moduleprocessid"] == 300) {
          $html .= $this->show_scripts ($interface->NAME, "api");
        }
		  else 
		if ($request["moduleprocessid"] == 400) {
		  $html .= $DEV->switchid("moduleprocessid");
		  $html .= $this->show_scripts ("", "global");
		}
		if ($request["moduleprocessid"] == 500) {
		  $html .= new MenuBuilder($this->cdedev->CDE);
		}
      break;
    }
    return $html;
  }
}

class dev_module {
    public $CDE;
  	public $request;
  	public $interfaceid;
	public $cdedev;
  	
  	function dev_module ($CDE, $request, $interfaceid, $cdedev) {
  	  $this->CDE = $CDE;
  	  $this->request = $request;
  	  $this->interfaceid = $interfaceid;
	  $this->cdedev = $cdedev;
  	}
  	
  	function __toString () {
  	  return $this->draw();
  	}
  	
  	function draw() {
  	  $CDE = $this->CDE;
  	  $request = $this->request;
  	  $interfaceid = $this->interfaceid;
	  $cdedev = $this->cdedev;
  	  
  	  if (!isset($_SESSION["username"])) {
        $_SESSION["username"] = "Andre";      
      }
  	  
  	  $interface = $CDE->get_value (0, "select * from tblinterface where interfaceid = {$interfaceid}");
  	  
  	  $html = $CDE->switchid("moduleprocessid");
  	  
  	  switch ($request["moduleprocessid"]) {
  		  default:
  			  $html .= "<table  class=\"moduletable\">";
  			  $html .= "<tr><th>Interface: {$interface->NAME} </th></tr>";
  			  $html .= "<tr><td><b>Created By :</b> {$interface->CREATEDBY} on {$interface->DATECRT}</th></tr>";
  			  $html .= "<tr><td>Purpose </td></tr>";
  			  $html .= "<tr><td><textarea name=\"interfacepurpose\" style=\"width:240px;\">{$interface->PURPOSE}</textarea></td></tr>";
  			  $prescript = "setscriptprocessid(0, false); setscriptid('', false);";
  			  $html .= "<tr><th>Layout [<a href=\"javascript:void(0);\" onclick=\"{$prescript} setmoduleprocessid(100, true)\">Code</a>] [<a href=\"javascript:void(0);\" onclick=\"{$prescript} setmoduleprocessid(200, true)\">SCSS</a>] [<a href=\"javascript:void(0);\" onclick=\"{$prescript} setmoduleprocessid(300, true)\">Scripts</a>] [<a href=\"javascript:void(0);\" onclick=\"{$prescript} setmoduleprocessid(500, true)\">Menu</a>]</th></tr>";
			  $release = new dev_release($CDE, $request, $cdedev);
  			  $html .= "<tr><td>".$release."</td></tr>";
  			  $html .= "</table>";
  		  break;
  	  }
  	  
  	  return $html;
  	}
  		
  }	
/*
Script Name: Users Interface
Purpose:
Interface to allow for the adding of users into the development environment
Created By: Andre
Created Date: 2012-08-21 07:28:03
*/
class dev_user {
  public $CDE;
  public $request;
  
  function dev_user ($CDE, $request) {
    $this->CDE = $CDE;
	$this->request = $request;
  }
  
  function __toString () {
    return $this->draw();
  }
  
  function show_users () {
    $CDE = $this->CDE;
	$sqlusers = "select * from tbluser order by name";
	$users = $CDE->get_row ($sqlusers);
	
	$html .= "<table class=\"datatable\">";
	$html .= "<tr><th>User Id</th><th>Name</th><th>Email</th><th>Status</th><th>Edit/Delete</th></tr>";
	foreach ($users as $uid => $user) {
	  
	  if ($user->STATUS == 0) {
	    $userstatus = "Disabled";
	  }
	    else {
		$userstatus = "Enabled";
	  }
	  
	  $editbutton = $CDE->input ("btnEdit", 60, "Click here to edit a user", "", "button", $value="Edit", "onclick=\"setuserid('$user->USERID', false); setuserprocessid(110, true);\"");
      $deletebutton = $CDE->input ("btnDelete", 60, "Click here to delete a script", "", "button", $value="Delete", "onclick=\"if (confirm('Delete {$user->NAME} ?')) { setuserid('$user->USERID', false); setuserprocessid(300, true); } \"");
     
	  $html .= "<tr><td>{$user->USERID}</td><td>{$user->NAME}</td><td>{$user->EMAIL}</td><td>{$userstatus}</td><td>{$editbutton}{$deletebutton}</td></tr>";
	}
	
	$insertbutton = $CDE->input ("btnInsert", 60, "Click here to add a user", "", "button", $value="Add", "onclick=\"setuserid('', false); setuserprocessid(100, true);\"");
   	$html .= "<tr><td colspan=\"5\" style=\"text-align: right;\">{$insertbutton}</td></tr>";
	$html .= "</table>";
	
	return $html;
  
  }
  
  //returns userid
  function insert_user ($name, $email, $password) {
    $CDE = $this->CDE;
	$userid = $CDE->get_next_id ("tbluser", "userid");
	if ($name == "") {
	  $name = "User".$userid;
	}
	$sqlinsert = "insert into tbluser (userid, name, email, passwd, status, datecrt)
	                  				values (? 		, ?		, ?		, ?			, ?		 , 'now')";
    $CDE->exec ($sqlinsert, $userid, $name, $email, crypt($password), 0);
	return $userid;
  }
  
  function update_user ($userid, $name, $email, $password, $status) {
    $CDE = $this->CDE;
	$request = $CDE->request;
	
	if ($password != "") {
	  $password = crypt($password);
	  $sqlupdate = "update tbluser set name = ?, email =  ?, passwd = ?, status = ? where userid = ?  ";
      $CDE->exec ($sqlupdate, $name, $email, $password, $status, $userid);
	}
	  else {
	  $sqlupdate = "update tbluser set name = ?, email =  ?, status = ? where userid = ?  ";
      $CDE->exec ($sqlupdate, $name, $email, $status, $userid);
	}  
	
	return true;
  }
  
  function delete_user ($userid) {
    $CDE = $this->CDE;
	$sqldelete = "delete from tbluser where userid = ?";
	$CDE->exec ($sqldelete, $userid);
    return true;
  }
  
  function show_user ($userid) {
    $CDE = $this->CDE;
	$request = $this->request;
	
	$sqluser = "select * from tbluser where userid = {$userid}";
	$user = $CDE->get_value (0, $sqluser);
	
    $html .= "<table class=\"dataform\">";
	$html .= " <tr><th colspan=\"2\">Edit User</th></tr>";
	$html .= " <tr><td>Name</td><td>".$CDE->input ("edtName", 200, "User name", "", "text", $user->NAME, "")."</td></tr>";	
	$html .= " <tr><td>Email</td><td>".$CDE->input ("edtEmail", 200, "Email Address", "", "text", $user->EMAIL, "")."</td></tr>";	
	$html .= " <tr><td>Password</td><td>".$CDE->input ("edtPasswd", 200, "Password", "", "password", "", "")."</td></tr>";
	$html .= " <tr><td>Status</td><td>".$CDE->select ("edtStatus", 200,  "Choose status for developer", "array", "0,Disabled|1,Enabled", $user->STATUS)."</td></tr>";
	
	$okbutton = $CDE->input ("btnOk", 100, "Click here to accept the data", "", "button", $value="OK", "onclick=\"setuserid('$userid', false); setuserprocessid(200, true);\"");
    $canbutton = $CDE->input ("btnCancel", 80, "Click here to cancel the data", "", "button", $value="Cancel", "onclick=\"setuserid('', false); setuserprocessid(0, true);\"");
  
    $html .= "<tr><td colspan=\"2\" style=\"text-align:right;\"> {$okbutton} {$canbutton} </td></tr>";
    $html .= "</table>"; 
    return $html;
  }
  
  function draw () {
    $CDE = $this->CDE;
	$request = $this->request;
	$html = "";
	$html .= $CDE->switchid ("userid");
	$html .= $CDE->switchid ("userprocessid");
	//$html .= "User Interface";
	switch ($request["userprocessid"]) {
	  case 100:
	    if ($request["userid"] == "") {
		  $userid = $this->insert_user ("", "", "");
		  $html .= setuserid ($userid, false);
		  $html .= setuserprocessid (110, true);
		}
	  break;
	  case 110:
	    //show the user
	    $html .= $this->show_user($request["userid"]);
	  break;
	  case 200:
	    //save the user
		$this->update_user ($request["userid"], $request["edtName"], $request["edtEmail"], $request["edtPasswd"], $request["edtStatus"]);
		$html .= setuserid('', false);
		$html .= setuserprocessid (0, true);
	  break;
	  case 300:
	    $this->delete_user ($request["userid"]);
	    $html .= setuserid('', false);
		$html .= setuserprocessid (0, true);
	  break;
	  default:
	     $html .= $this->show_users();
	  break;
	}
	
	
	return $html;
  
  }
  



}


/**
        Diff implemented in pure php, written from scratch.
        Copyright (C) 2003  Daniel Unterberger <diff.phpnet@holomind.de>
           
        This program is free software; you can redistribute it and/or
        modify it under the terms of the GNU General Public License
        as published by the Free Software Foundation; either version 2
        of the License, or (at your option) any later version.
        
        This program is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        GNU General Public License for more details.
        
        You should have received a copy of the GNU General Public License
        along with this program; if not, write to the Free Software
        Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
        
        http://www.gnu.org/licenses/gpl.html

        About:
        I searched a function to compare arrays and the array_diff()
        was not specific enough. It ignores the order of the array-values.
        So I reimplemented the diff-function which is found on unix-systems
        but this you can use directly in your code and adopt for your needs.
        Simply adopt the formatline-function. with the third-parameter of arr_diff()
        you can hide matching lines. Hope someone has use for this.

        Contact: d.u.diff@holomind.de <daniel unterberger>
    **/

    function arr_diff( $f1 , $f2 , $show_equal = 0 )
    {
	   
        $c1         = 0 ;                   # current line of left
        $c2         = 0 ;                   # current line of right
        $max1       = count( $f1 ) ;        # maximal lines of left
        $max2       = count( $f2 ) ;        # maximal lines of right
        $outcount   = 0;                    # output counter
        $hit1       = "" ;                  # hit in left
        $hit2       = "" ;                  # hit in right

        while ( 
                $c1 < $max1                 # have next line in left
                and                 
                $c2 < $max2                 # have next line in right
                and 
                ($stop++) < 1000            # don-t have more then 1000 ( loop-stopper )
                and 
                $outcount < 20              # output count is less then 20
              )
        {
            /**
            *   is the trimmed line of the current left and current right line
            *   the same ? then this is a hit (no difference)
            */  
            if ( trim( $f1[$c1] ) == trim ( $f2[$c2])  )    
            {
                /**
                *   add to output-string, if "show_equal" is enabled
                */
                $out    .= ($show_equal==1) 
                         ?  formatline ( ($c1) , ($c2), "=", $f1[ $c1 ] ) 
                         : "" ;
                /**
                *   increase the out-putcounter, if "show_equal" is enabled
                *   this ist more for demonstration purpose
                */
                if ( $show_equal == 1 )  
                { 
                    $outcount++ ; 
                }
                
                /**
                *   move the current-pointer in the left and right side
                */
                $c1 ++;
                $c2 ++;
            }

            /**
            *   the current lines are different so we search in parallel
            *   on each side for the next matching pair, we walk on both 
            *   sided at the same time comparing with the current-lines
            *   this should be most probable to find the next matching pair
            *   we only search in a distance of 10 lines, because then it
            *   is not the same function most of the time. other algos
            *   would be very complicated, to detect 'real' block movements.
            */
            else
            {
                
                $b      = "" ;
                $s1     = 0  ;      # search on left
                $s2     = 0  ;      # search on right
                $found  = 0  ;      # flag, found a matching pair
                $b1     = "" ;      
                $b2     = "" ;
                $fstop  = 0  ;      # distance of maximum search

                #fast search in on both sides for next match.
                while ( 
                        $found == 0             # search until we find a pair
                        and 
                        ( $c1 + $s1 <= $max1 )  # and we are inside of the left lines
                        and 
                        ( $c2 + $s2 <= $max2 )  # and we are inside of the right lines
                        and     
                        $fstop++  < 10          # and the distance is lower than 10 lines
                      )
                {

                    /**
                    *   test the left side for a hit
                    *
                    *   comparing current line with the searching line on the left
                    *   b1 is a buffer, which collects the line which not match, to 
                    *   show the differences later, if one line hits, this buffer will
                    *   be used, else it will be discarded later
                    */
                    #hit
                    if ( trim( $f1[$c1+$s1] ) == trim( $f2[$c2] )  )
                    {
                        $found  = 1   ;     # set flag to stop further search
                        $s2     = 0   ;     # reset right side search-pointer
                        $c2--         ;     # move back the current right, so next loop hits
                        $b      = $b1 ;     # set b=output (b)uffer
                    }
                    #no hit: move on
                    else
                    {
                        /**
                        *   prevent finding a line again, which would show wrong results
                        *
                        *   add the current line to leftbuffer, if this will be the hit
                        */
                        if ( $hit1[ ($c1 + $s1) . "_" . ($c2) ] != 1 )
                        {   
                            /**
                            *   add current search-line to diffence-buffer
                            */
                            $b1  .= formatline( ($c1 + $s1) , ($c2), "-", $f1[ $c1+$s1 ] );

                            /**
                            *   mark this line as 'searched' to prevent doubles. 
                            */
                            $hit1[ ($c1 + $s1) . "_" . $c2 ] = 1 ;
                        }
                    }



                    /**
                    *   test the right side for a hit
                    *
                    *   comparing current line with the searching line on the right
                    */
                    if ( trim ( $f1[$c1] ) == trim ( $f2[$c2+$s2])  )
                    {
                        $found  = 1   ;     # flag to stop search
                        $s1     = 0   ;     # reset pointer for search
                        $c1--         ;     # move current line back, so we hit next loop
                        $b      = $b2 ;     # get the buffered difference
                    }
                    else
                    {   
                        /**
                        *   prevent to find line again
                        */
                        if ( $hit2[ ($c1) . "_" . ( $c2 + $s2) ] != 1 )
                        {
                            /**
                            *   add current searchline to buffer
                            */
                            $b2   .= formatline ( ($c1) , ($c2 + $s2), "+", $f2[ $c2+$s2 ] );

                            /**
                            *   mark current line to prevent double-hits
                            */
                            $hit2[ ($c1) . "_" . ($c2 + $s2) ] = 1;
                        }

                     }

                    /**
                    *   search in bigger distance
                    *
                    *   increase the search-pointers (satelites) and try again
                    */
                    $s1++ ;     # increase left  search-pointer
                    $s2++ ;     # increase right search-pointer  
                }

                /**
                *   add line as different on both arrays (no match found)
                */
                if ( $found == 0 )
                {
                    $b  .= formatline ( ($c1) , ($c2), "-", $f1[ $c1 ] );
                    $b  .= formatline ( ($c1) , ($c2), "+", $f2[ $c2 ] );
                }

                /** 
                *   add current buffer to outputstring
                */
                $out        .= $b;
                $outcount++ ;       #increase outcounter

                $c1++  ;    #move currentline forward
                $c2++  ;    #move currentline forward

                /**
                *   comment the lines are tested quite fast, because 
                *   the current line always moves forward
                */

            } /*endif*/

        }/*endwhile*/

        return $out;

    }/*end func*/

    /**
    *   callback function to format the diffence-lines with your 'style'
    */
    function formatline( $nr1, $nr2, $stat, &$value )  #change to $value if problems
    {
        if ( trim( $value ) == "" )
        {
            return "";
        }

        switch ( $stat )
        {
            case "=":
                return $nr1. " : $nr2 : = ".htmlentities( $value ) ;
            break;

            case "+":
                return $nr1. " : $nr2 : + <font color='blue' >".htmlentities( $value )  ."</font>";
            break;

            case "-":
                return $nr1. " : $nr2 : - <font color='red' >".htmlentities( $value )  ."</font>";
            break;
        }

    }

/*
Script Name: TCodeMap
Purpose:
A visual mapping of all your code
Created By: Admin
Created Date: 2014-04-25 09:49:40
*/
//Andre#:New code map for development environment encapsulates todo lists and release notes
//this is a class to draw out the coding map
//Testing the updating of the system
class TCodeMap {
   private $CDE;
   private $request;
   private $cdedev;
 
   //This is the constructior
   function __construct ($CDE, $request, $cdedev) {
       $this->CDE = $CDE;
	   $this->request = $request;
	   $this->cdedev = $cdedev;
   }
   
   
   //This is the string function
   function __toString () {
      return $this->draw();   
   }
   
   
   //function to get the class names and methods
   function get_class_info ($aname) { 
	 
	 $classmethods = get_class_methods ($aname);
	 
	  foreach ($classmethods as $cid => $classmethod) {
         $arguments = get_classfunction_arguments ( $aname , $classmethod);
		 $listitems[] = li ( "<b> {$classmethod} </b> (".implode (", ", $arguments).")" );
	  }
	    
      return $listitems;
   }
   
   
	
	//function to get all the params from code, we are interested in class, function declarations, comments and comments with todos
	function get_php_code_elements ( $content ) {
	  
	  $functions = array();
	  $classes = array();
	  $comments = array();
	  $todos = array ();
	  $releasenotes = array();
	  $lookups = array ();
	  
	  
	  $content = '<'.'?php'."\n".$content."\n".'?'.'>';
	  $tokens = token_get_all ( $content );
	  
	  //print_r ($tokens);
	  //a function or a class that has a comment above its header has a * before the name
	  	  	  
	  foreach ($tokens as $tid => $token) {
	    
		if ($token[0] == T_FUNCTION) {
		  
		  $comment = "";
		  if ($tokens[$tid-2][0] == T_COMMENT || $tokens[$tid-1][0] == T_COMMENT  ) {
		    $comment = "*";
  	      }
		  		  
		  $i = $tid+1;
		  while ($tokens [$i][0] != T_STRING ) {
		    $i++;  
		  }
		  
		  $functions[] = $comment.$tokens [$i][1]."_|_".($tokens[$i][2]-1);
		 
		}
		 
		if ($token[0] == T_COMMENT) {
		 
		  $comments[] = htmlentities ($token [1]);
		  $lookups[$token[2]] = $token[1];
		} 
		   
		if ($token[0] == T_COMMENT) {
		 	 
		  if (stripos ($token[1], "todo:") !== false) {
		    $todos[] = $token [1]."_|_".($token[2]-1) ;
		  }
	  
		}
		
		if ($token[0] == T_COMMENT) {
		 	 
		  if (stripos ($token[1], "#:") !== false) {
		    $releasenotes[] =  $token [1];
		  }
	  
		}
		
	     if ($token[0] == T_CLASS) {
		  
		  $comment = "";
		  if ($tokens[$tid-2][0] == T_COMMENT || $tokens[$tid-1][0] == T_COMMENT  ) {
		    $comment = "*";
  	      }
		  
		  $i = $tid+1;
		  	  
		  while ($tokens [$i][0] != T_STRING ) {
		    $i++;  
		  }
		  $classes[] = $comment.$tokens [$i][1]."_|_".($tokens[$i][2]-1);
		  
		} 
		
		$lines = $token[2];
		
	  }
	  	  
	  return array ( "classes" => $classes, "functions" => $functions, "comments" => $comments, "todos" => $todos, "lookups" => $lookups, "lines" => $lines, "releasenotes" => $releasenotes ) ;
	}
	
   //Function to draw a picture of the code 
   function draw_script_picture ( $elements ) {
     
	 
	 foreach ($elements["classes"] as $fid => $class) {
	   
	   $class = explode ("_|_", $class);
	 
	   $color = "red";
	   if (strpos ($class[0], "*") !== false) {
	     $color = "green";
	   }
	   $class[0] = str_replace ("*", "", $class[0]);
	   $classes[] = div (array ("class" => "smallblock", "style" => "background: {$color}"), a ( array ("title" => "{$class[0]}", "href" => "#class".$fid ),  "K"));
	 }
	 
	 
	 foreach ($elements["functions"] as $fid => $func) {
	   
	   $func = explode ("_|_", $func);
	 
	   $color = "red";
	   if (strpos ($func[0], "*") !== false) {
	     $color = "green";
	   }
	   $func[0] = str_replace ("*", "", $func[0]);
	   $functions[] = div (array ("class" => "smallblock", "style" => "background: {$color}"), a ( array ("title" => "{$func[0]}", "href" => "#function".$fid ),  "F" ));
	 }
	 
	 foreach ($elements["todos"] as $fid => $todo) {
	   
	   $todo = explode ("_|_", $todo);
	 
	   $color = "lightblue";
	   
	   $todos[] = div (array ("class" => "smallblock", "style" => "background: {$color}"), a ( array ("title" => "{$todo[0]}", "href" => "#todo".$fid ), "T" ));
	 }
	 
	 foreach ($elements["comments"] as $fid => $comment) {
	   
	   
	 
	   $color = "lightblue";
	   
	   $comments[] = div (array ("class" => "smallblock", "style" => "background: {$color}"), a ( array ("title" => "{$comment}", "href" => "javascript:void(0);" ), "C" ));
	 }
	 
	 
	 
	 
	 
	 $params = array ("class" => "scriptholder");
	 $html .= div ( array ( "style" => "width: 270px; background: orange; "), div ($params, $classes ), div ($params, $functions ), div ( $params, $comments), div ($params, $todos) );
     
     return $html;
   }
	
	
   /* This is a draw function  a multiline comment   */
   function draw () {
     $html = "";
	 $html .= scss ( "  
	 	    
	     .block {
		   display: inline-block;
		   border: 1px solid orange;
		   width: 44px;
           height: 44px;		   
		   font-size: 10px;
		   text-align: center;
		 }
		 
		 .segment {
		   display: inline-block;
		   border: 1px solid black;
		   width: 20px;
           height: 20px;		   
		 }
		 
		 .smallblock {
		   display: block;
		   float: left;
		   border: 1px solid black;
		   width: 16px;
		   height: 18px;
		   margin: 1px;
		 }
		 
		 .scriptholder {
		   display: inline-block;
		   border : 2px solid green;
		   width: 132px;
		   text-align: center;
		   min-height: 64px;
		   padding: 2px;
		   margin-right: 2px;
		   
		 }
		 
	 " );
	 	 
	 if ($_REQUEST["content"] == "scriptoverview") {
	   $script = $this->CDE->get_value (0, "select s.*, (select interfaceid from tblinterface where name = s.interface ) as interfaceid from tblscript s where scriptid = {$_REQUEST["scriptid"]} ");
	   
	   
	   if ($script->INTERFACEID == "") {
	     $script->INTERFACEID = "9999999";
	   }
	   
	   $link = "http://{$_SERVER["HTTP_HOST"]}/?release={$_REQUEST["release"]}&interface=Developer&interfaceid=$script->INTERFACEID&scriptid={$script->SCRIPTID}&content=openscript";
	   
	   $deletebutton = "";
	   if (strpos ($script->SCRIPTNAME, "New") !== false ) {
	     $dellink = "http://{$_SERVER["HTTP_HOST"]}/release/{$_REQUEST["release"]}/interface/Developer/interfaceid/$script->INTERFACEID/scriptid/{$script->SCRIPTID}/deletescript/";
	     $deletebutton = input (array ("type" => "button", "onclick" => " location.href = '$dellink'; "), "X");
	   }
	   
	   $html .= h1 ( a ( array ("onclick" => "window.open ('$link'); "),  $script->SCRIPTNAME ), $deletebutton );
	   
	   
	   
	   $elements = $this->get_php_code_elements ( $script->CONTENT );
	   $classes = $elements["classes"];
	   $functions = $elements["functions"];
	   $comments = $elements["comments"];		 
	   $todos = $elements["todos"];	
	   $lookups = $elements["lookups"];
	   $allnotes = $elements["releasenotes"];
	    
	    
	   
	   //draw the element pictures
	   $html .= $this->draw_script_picture ($elements);
	   
	   foreach ( $functions as $fid => $func ) {
	       
		  $func = explode ("_|_", $func);
		  
		  $color = "red";
		  if (strpos ($func[0], "*") !== false) {
		    $color = "black";
		  }
		  
		  $comment = span ( array ("style" => "color: purple"), $lookups[$func[1]] );		  
		  $func = str_replace ("*", "", $func[0]." (line ".$func[1].")");
	       
	      $allfunctions[] = li ( array ("style" => "color : {$color};", "id" => "function".$fid), $comment.br().$func );		  		  
	   }
	   
	   if (count ($functions) > 0) {
	       $html .= h2 ("List of functions");
	       $html .= ul ( $allfunctions );
	   }
	   
	   if (count ($classes) > 0) {
		   $html .= h2 ("List of Classes"); 

		   foreach ($classes as $cid => $class) {
			 $class = explode ("_|_", $class);
			 $class = str_replace ("*", "", $class[0]);

			 $classinfo = $this->get_class_info ( $class );	
			 $html .= h2 (array ("id" => "class".$cid),  $class);
			 $html .= ul ($classinfo);
		   }
	   }
	   
	   	   
	   if (count ($todos) > 0) {
	   	  $html .= h2 ("List of Todos");
		  
		  foreach ($todos as $tid => $todo) {
		    $todo = explode ("_|_", $todo);
			
		    $alltodos = li ( array ("id" => "todo".$tid),  $todo[0]. " (line ".$todo[1].")" );		  
		  }
		  
		  $html .= ul ( $alltodos );
		  
	   }	   
	   
	 
	 }
	   else {
	 
	     $scripts = $this->CDE->get_row ("select scriptid, scriptname, purpose, content, (select interfaceid from tblinterface where name = s.interface ) as interfaceid, scripttype, status from tblscript s where release = '{$this->request["release"]}' order by scripttype, status");
	  
	     $icount = 0;
	     $scripttype = "";
		 
		 
	 
	     $allclasses = array ();
		 $allnotes = array ();
	     $lines = 0;
	     foreach ( $scripts as $sid => $script ) {
			 if ($script->SCRIPTTYPE != $scripttype) {
			   $scripttype = $script->SCRIPTTYPE;
			 }
			 
			 
             if ($script->CONTENT == "") {
			   $sqldelete = "delete from tblscript where scriptid = {$script->SCRIPTID}";
			   echo "deleteing script ".$script->SCRIPTNAME;
			   $this->CDE->exec ($sqldelete);
			 }
			 
			 

			 $elements = $this->get_php_code_elements ( $script->CONTENT );
			 $classes = $elements["classes"];
			 $functions = $elements["functions"];
			 $comments = $elements["comments"];		 
			 $todos = $elements["todos"];
			 $releasenotes = $elements["releasenotes"];
			 $lines += $elements["lines"];
			 
			 
			 $allnotes = array_merge ($allnotes, $releasenotes);
			 
		     
			

			 $link = "http://{$_SERVER["HTTP_HOST"]}/?release={$_REQUEST["release"]}&interface=Developer&scriptid={$script->SCRIPTID}&content=scriptoverview";

			 $params = array ( "class" => "segment" );
             
			 //$littleblocks = "<div class=\"segment\" > ".count ( $classes )." </div>"; 

			 $littleblocks = div ( $params, count ( $classes ) ). div ( $params, count( $functions) ). div ( $params, count ( $comments) ). div ( $params, count ($todos) );

			 $shell = div (array ("class" => "block", "style" => "background: green",  "onclick" => " window.open ('$link'); ",  "title" => $script->SCRIPTNAME  ) , $littleblocks  );

			 //if we have a todo we must always show it as blue to make a reminder
			 if (count ($todos) > 0 ) {
			   $shell->set_attribute ("style", "background: lightblue");		 
			 }
			  else {

			  if (count ($comments) == 0) {
			   $shell->set_attribute ("style", "background: red");		   		  
			  }

			  if (count ($comments) > 0) {
			   $shell->set_attribute ("style", "background: yellow");		   		  
			  }
                 
			  $icommented = 0;
			  $ifunctions = 0;
			  foreach ($functions as $fid => $func) {
				if (strpos ($func, "*") !== false) {
                  $icommented++;
				}
				$ifunctions++;
			  }
			  
			  $iclasscommented = 0;
			  $iclasses = 0;
			  foreach ($classes as $cid => $class) {
				if (strpos ($class, "*") !== false) {
                  $iclasscommented++;
				}
				$iclasses++;
			  }
			  
					  		  
			  if ($icommented == $ifunctions && count ($comments) >= $ifunctions && $iclasscommented == $iclasses ) {
			    $shell->set_attribute ("style", "background: green");		   		  
			  }


			 } 
		
			 $html .= $shell;
			 $icount++;
		 }


		 $html .= h3 (" {$icount} scripts in the system (".number_format ( $lines, 0, ".", ",")." lines of code) ") ;
		 
		   
		
	 }
	 
	 
	 $params = array ( "class" => "segment" );
	 $littleblocks = div ( $params, "K" ). div ( $params, "F" ). div ( $params, "C" ). div ( $params, "T" );
	 $shell = div (array ("class" => "block", "style" => "background: white",  "title" => "Mapping"  ) , $littleblocks  );
	 $html .= $shell;
	 $html .= ul (
	     li ("K => Classes"),
		   li ("F => Functions"),
		   li ("C => Comments"),
		   li ("T => Todo Comments")
	  );
	  
	 $html .= h1 ("Release Notes");
	 
	 $notes = array ();
	 foreach ($allnotes as $rid => $releasenote) {
	    $notes[] =  li ( $releasenote ); 
	 }
	  
	 $html .= ul ( $notes );
	 
     return $html;
   }
 
 
}
/*
Script Name: TUpdateMe
Purpose:
Script to run updates from master
Created By: Admin
Created Date: 2014-04-25 11:32:17
*/
//Andre: The updater class for the development environment
class TUpdateMe {
  public $CDE;
  public $request;
  public $cdedev;
  
  //Andre: the constructor for the UpdateMe script
  function __construct ($CDE, $request, $cdedev) {
    $this->CDE = $CDE; //developer scripts
	$this->request = $request;
	$this->cdedev = $cdedev;
  }
  
  
  function __toString () {
    return $this->draw();
  }
  
  function draw () {
    $html = ""; 
    $html .= h3 ("Getting updates from server....");
	
	//Fetch the crossdatabase engine and shape
	$fetchfiles = file_get_contents ("http://tina4.com/master/?interface=Update&action=GetFiles");
	
	$fetchfiles = json_decode ($fetchfiles);
	
	
	
	$li = array ();
	
	foreach ($fetchfiles as $fid => $fetchfile) {
	  
	  $li[] = li ( "Fetched ".b ($fetchfile->filename). " ... "  );
	  
	  file_put_contents ( $fetchfile->filename, urldecode ($fetchfile->content) );
	
	}
	
	
	$li[] = li ( pre ( file_get_contents ("readme.txt") ) );
	

    //Get the update script first ----
	
	$fetchupdates = file_get_contents ("http://tina4.com/master/?interface=Update&action=GetUpdates");
	
	$fetchupdates = json_decode ($fetchupdates);
	
	
	
	
	foreach ($fetchupdates as $fid => $fetchupdate) {
	  
	  $script = $this->CDE->get_value (0, "select scriptid, scriptname, version, scriptcategory 
	     from tblscript where scriptid = {$fetchupdate->scriptid} and release = '{$this->request["release"]}' ");
	  
	  //print_r ( htmlentities ( urldecode ($fetchupdate->content) ) );
	  
	  if ( $script->SCRIPTNAME != $fetchupdate->scriptname ) {
	    //legacy - see if we can find some scripts which exist by the same name
		
		
		
		$script = $this->CDE->get_value (0, "select 
		                                       scriptid, scriptname, version, scriptcategory
	                                          from tblscript 
											 where scriptname = '{$fetchupdate->scriptname}'
											   and release = '{$this->request["release"]}' ");
	    
		//we have found a script
		if ($script->SCRIPTID != "" && $script->VERSION < $fetchupdate->version) {
		  //$cdedev = new cde_cdedev ($CDE, $_REQUEST, $outputpath="includes");
		    $li[] = li ( b ( $script->SCRIPTNAME. " (". $script->VERSION.")" ). " ... found and updated to {$fetchupdate->version} " );
			
		  $sqlupdate = "update tblscript set scriptname = '{$fetchupdate->scriptname}', scriptcategory = '{$fetchupdate->scriptcategory}', content = ?, version = '{$fetchupdate->version}' where scriptid = {$script->SCRIPTID}";
		    $this->CDE->exec ($sqlupdate, urldecode ($fetchupdate->content) );
		}
		  else {
		  //we must add this script into the current release
		  if ($script->SCRIPTID == "") {
		  
		  
 	        $li[] = li ( b ( $script->SCRIPTNAME. " (". $script->VERSION.")" ). " ... added to the system " );
		  
		  }
		
		}
		//make sure we don't have any scripts of this version to release
		
		
	  }
	    else
	  if ($script->VERSION < $fetchupdate->version) {
	    $li[] = li ( b ( $script->SCRIPTNAME. " (". $script->VERSION.")" ). " ... updated to ".$fetchupdate->version );
		
	    $sqlupdate = "update tblscript set scriptname = '{$fetchupdate->scriptname}', scriptcategory = ?, content = ?, version = ? where scriptid = {$script->SCRIPTID}";
		$this->CDE->exec ($sqlupdate, $fetchupdate->scriptcategory, urldecode ($fetchupdate->content), $fetchupdate->version );
		
	  }
	}
	
	
	if (count ($li) > 0) {
	  $html .= ul ($li );
	}
	  else {
	  $html .= h3 ( "No new updates, check back later" ); 
	  
	}
	
	return $html;
  
  }
  
  
}  

?>