<?php
   require_once ("shape.php"); 
  
  /* The module for the CDE developer engine which handles the deployment of an application
     Written by : Andre van Zuydam
     
     
    The cde_cdedev class will always expect the following variables to be set
    $_REQUEST["release"] 
    $_REQUEST["interface"]   
                                   
  */
  class dev_interface {
  	public $CDE;
  	public $request;
  		
  	function dev_interface ($CDE, $request) {
  		$this->CDE = $CDE;
  		$this->request = $request;	
  	}	
  	
  	function __toString() {
  		return $this->draw();	
  	}
  	
  	function insert_interface ($name="", $purpose="", $username="Admin") {
  		$CDE = $this->CDE;
  		$interfaceid = $CDE->get_next_id ("tblinterface", "interfaceid");
  		$sqlinsert = "insert into tblinterface (interfaceid, name, purpose, datecrt, createdby)
  						values ({$interfaceid}, '{$name}', '', 'now', '{$username}')";
  		$CDE->exec ($sqlinsert); 
  		return $interfaceid;
  	}
  	
  	function delete_interface ($interfaceid) {
  	  $CDE = $this->CDE;
  	  $sqldelete = "delete from tblinterface where interfaceid = {$interfaceid}";
  	  $CDE->exec ($sqldelete);
  	  return true;
  	}
  	
  	function get_interfaces () {
  		$CDE = $this->CDE;
  		$sqlselect = "select * from tblinterface order by name"; 	
  		$interfaces = $CDE->get_row($sqlselect);				
  		return $interfaces;
  	}	
    
    function compile_code ($scriptcontent) {
      //this will compile the coffee script and html to usable code
  	  //get rid of the nonsense
        
      $tempcode = explode ("/*"."<CS>"."*/", $scriptcontent);  
          
  	  if (file_exists (dirname(__FILE__)."/coffeescript/Init.php") && count ($tempcode) > 1 ) {
        require_once dirname(__FILE__)."/coffeescript/Init.php";
        Coffeescript\Init::load();
      
        $othercode = "";
        $compilecode = "";
        foreach ($tempcode as $tid => $code) {
          if ($tid > 0) {
            $coffeescript = explode ("/*"."</CS>"."*/", $code);
            
            $compilecode = $coffeescript[0];
            
            //trim the code to just have the javascript
                        
            
            if (trim($compilecode) != "") {
              try {
                $randomfile =  "test".rand(1000,9999).".coffee";
                file_put_contents ($randomfile, $compilecode);
                $javascript = "\n".'$html .= "\n<script>'.addslashes(CoffeeScript\Compiler::compile($compilecode, array("filename"=>"test.coffee", "bare" => true))).'</script>";';
                unlink ($randomfile);
              } 
              catch (Exception $e)
              {
                echo $e->getMessage();
                $javascript = "";
              }
            }
            
            $othercode .= "\n".$javascript."\n".$coffeescript[1];
                       
          }
            else {
            $othercode .= $code;
          }
        }
        //turn the coffeescript stuff into javascript
      }
       else {
        $othercode = $scriptcontent;  
      }
      return $othercode;
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
    
    function get_css ($interfacename, $root, $outputpath, $hostname ) {
      $CDE = $this->CDE;
      $release = $this->request["release"]; 
      //CSS functionality
      $sqlcss = "select * from tblscript where scriptname = '{$interfacename}css' and interface = '{$interfacename}' and release = '{$release}' and scripttype = 'css'";
      $css = $CDE->get_value (0, $sqlcss);
      
      //echo $root."scssphp/scss.inc.php";
      if (file_exists ($root."/scssphp/scss.inc.php")) {
        require_once $root."/scssphp/scss.inc.php";
        $sassenabled = true;
        $scss = new scssc();
      } 
      
      if ($sassenabled) {
        $css->CONTENT = $scss->compile ($css->CONTENT);
      } 

      $sqlcss = "select * from tblscript where scriptname = '{$interfacename}csssmpl' and interface = '{$interfacename}' and release = '{$release}' and scripttype = 'css'";
      $csssmpl = $CDE->get_value (0, $sqlcss);

      if ($sassenabled) {
        $csssmpl->CONTENT = $scss->compile ($csssmpl->CONTENT);
      }
          

      $sqlcss = "select * from tblscript where scriptname = '{$interfacename}csssml' and interface = '{$interfacename}' and release = '{$release}' and scripttype = 'css'";
      $csssml = $CDE->get_value (0, $sqlcss);

      if ($sassenabled) {
        $csssml->CONTENT = $scss->compile ($csssml->CONTENT);
      }


      $sqlcss = "select * from tblscript where scriptname = '{$interfacename}csssmp' and interface = '{$interfacename}' and release = '{$release}' and scripttype = 'css'";
      $csssmp = $CDE->get_value (0, $sqlcss);
      
      if ($sassenabled) {
        $csssmp->CONTENT = $scss->compile ($csssmp->CONTENT);
      }

      $sqlcss = "select * from tblscript where scriptname = '{$interfacename}cssipadpl' and interface = '{$interfacename}' and release = '{$release}' and scripttype = 'css'";
      $cssipadpl = $CDE->get_value (0, $sqlcss);
      
      if ($sassenabled) {
        $cssipadpl->CONTENT = $scss->compile ($cssipadpl->CONTENT);
      }

      $sqlcss = "select * from tblscript where scriptname = '{$interfacename}cssipadl' and interface = '{$interfacename}' and release = '{$release}' and scripttype = 'css'";
      $cssipadl = $CDE->get_value (0, $sqlcss);
      
      if ($sassenabled) {
        $cssipadl->CONTENT = $scss->compile ($cssipadl->CONTENT);
      }

      $sqlcss = "select * from tblscript where scriptname = '{$interfacename}cssipadp' and interface = '{$interfacename}' and release = '{$release}' and scripttype = 'css'";
      $cssipadp = $CDE->get_value (0, $sqlcss);
      
      if ($sassenabled) {
        $cssipadp->CONTENT = $scss->compile ($cssipadp->CONTENT);
      }

      $sqlcss = "select * from tblscript where scriptname = '{$interfacename}csslaptop' and interface = '{$interfacename}' and release = '{$release}' and scripttype = 'css'";
      $csslaptop = $CDE->get_value (0, $sqlcss);
      
      if ($sassenabled) {
        $csslaptop->CONTENT = $scss->compile ($csslaptop->CONTENT);
      }

      $sqlcss = "select * from tblscript where scriptname = '{$interfacename}csslarge' and interface = '{$interfacename}' and release = '{$release}' and scripttype = 'css'";
      $csslarge = $CDE->get_value (0, $sqlcss);
      
      if ($sassenabled) {
        $csslarge->CONTENT = $scss->compile ($csslarge->CONTENT);
      }
 
      $sqlcss = "select * from tblscript where scriptname = '{$interfacename}cssiphone' and interface = '{$interfacename}' and release = '{$release}' and scripttype = 'css'";
      $cssiphone = $CDE->get_value (0, $sqlcss);
      
      if ($sassenabled) {
        $cssiphone->CONTENT = $scss->compile ($cssiphone->CONTENT);
      }   
      
      //make the css output
      $cssfilename = $root."/".$outputpath."/".$release."/css/{$interfacename}css.css";
      if (trim($css->CONTENT) != "") {
        $globalcss = $css->CONTENT."\n";
        $globalcss .= "@media only screen\n";
        $globalcss .= "and (min-device-width : 320px)\n";
        $globalcss .= "and (max-device-width : 480px) {\n";
        $globalcss .= $csssmpl->CONTENT."\n";
        $globalcss .= "}\n";   


        $globalcss .= "@media only screen\n";
        $globalcss .= "and (min-width : 321px) {\n";
        $globalcss .= $csssml->CONTENT."\n";
        $globalcss .= "}\n";

        $globalcss .= "@media only screen\n";
        $globalcss .= "and (max-width : 320px) {\n";
        $globalcss .= $csssmp->CONTENT."\n";
        $globalcss .= "}\n"; 

        $globalcss .= "@media only screen\n";
        $globalcss .= "and (min-device-width : 768px)\n";
        $globalcss .= "and (max-device-width : 1024px) {\n";
        $globalcss .= $cssipadpl->CONTENT."\n";
        $globalcss .= "}\n"; 
      
        $globalcss .= "@media only screen\n";
        $globalcss .= "and (min-device-width : 768px)\n";
        $globalcss .= "and (max-device-width : 1024px)\n";
        $globalcss .= "and (orientation : landscape) {\n";
        $globalcss .= $cssipadl->CONTENT."\n";
        $globalcss .= "}\n"; 

        $globalcss .= "@media only screen\n";
        $globalcss .= "and (min-device-width : 768px)\n";
        $globalcss .= "and (max-device-width : 1024px)\n";
        $globalcss .= "and (orientation : portrait) {\n";
        $globalcss .= $cssipadp->CONTENT."\n";
        $globalcss .= "}\n"; 
 
 
        $globalcss .= "@media only screen\n"; 
        $globalcss .= "and (min-width : 1224px) {\n";
        $globalcss .= $csslaptop->CONTENT."\n";
        $globalcss .= "}\n";

        $globalcss .= "@media only screen\n";
        $globalcss .= "and (min-width : 1824px) {\n";
        $globalcss .= $csslarge->CONTENT."\n";
        $globalcss .= "}\n";

        $globalcss .= "@media only screen and (-webkit-min-device-pixel-ratio : 1.5),\n";
        $globalcss .= "only screen and (min-device-pixel-ratio : 1.5) {\n";
        $globalcss .= $cssiphone->CONTENT."\n";
        $globalcss .= "}\n";
 
        $csscontent = str_replace ("''", "'", $globalcss);
        
        if (!file_exists($cssfilename)) {
           mkdir ($root."/".$outputpath."/".$release."/css", 0755, true);
        }
        
        file_put_contents ($cssfilename, slib_compress_script ($csscontent));
           
        
      }
    }
    
  	function get_interface ($interfacename, $root, $outputpath, $hostname) {
      $CDE = $this->CDE;
      $release = $this->request["release"];
      
      
  		$sqlselect = "select * from tblinterface where name = '{$interfacename}'"; 
      $interface = $CDE->get_value(0, $sqlselect);	
      
      //output the css file & the script file  for the current release, current version indicated by status = 1 and create the file
      $sqlscript = "select * from tblscript where scriptname = '{$interfacename}' and interface = '{$interfacename}' and release = '{$release}' and scripttype = 'interface'";
      $script = $CDE->get_value (0, $sqlscript);
     
      
      
       
      $sqlapi = "select * from tblscript where interface = '{$interfacename}' and release = '{$release}' and scripttype = 'api'";
      $apis = $CDE->get_row ($sqlapi);
           
      
      $apicontent = "";
      foreach ($apis   as $apiid => $api) {
        if ($api->STATUS == 1) {
          $apicontent .= "/*\nScript Name: {$api->SCRIPTNAME}\n";
          $apicontent .= "Purpose:\n{$api->PURPOSE}\n";
          $apicontent .= "Created By: {$api->CREATEDBY}\n";
          $apicontent .= "Created Date: {$api->DATECRT}\n";
          $apicontent .= "*/\n";
          $apicontent .= $this->compile_code($api->CONTENT)."\n";    
        }
          else {
          
          $scriptname = $root."/".$outputpath."/".$release."/ajax/{$api->SCRIPTNAME}.php"; 
           
          $content = "/*\nScript Name: {$api->SCRIPTNAME}\n";
          $content .= "Purpose:\n{$api->PURPOSE}\n";
          $content .= "Created By: {$api->CREATEDBY}\n";
          $content .= "Created Date: {$api->DATECRT}\n";
          $content .= "*/\n";
          
          $content .= "require_once ('".$root."/cdesimple.php');\n";
          $content .= "require_once ('".$root."/connection.php');\n";
          $content .= "require_once ('".$root."/shape.php');\n";
          $content .= "if (file_exists('".$root."/".$outputpath."/".$release."/functions.php')) {\n  require_once ('".$root."/".$outputpath."/".$release."/functions.php');\n}\n";
          $content .= "if (file_exists('".$root."/".$outputpath."/".$release."/{$interface->NAME}api.php')) { require_once ('".$root."/".$outputpath."/".$release."/{$interface->NAME}api.php'); }\n\n";
          $content .= 'session_name($_CDEDEV_SESSION_NAME);'."\n";
          $content .= 'session_start();'."\n";
          $content .= '$_REQUEST["ajaxscriptname"] = "'.$api->SCRIPTNAME.'";'."\n";
          $content .= $api->CONTENT."\n";
          $content = str_replace ("''", "'", $content);
          $precontent = $content;
          $content = '<?'.'php'."\n".$this->compile_code($content)."\n".'?'.'>'; 
          if (!file_exists($scriptname) && $this->check_code ($content, $root, $outputpath, $hostname)) {
            mkdir ($root."/".$outputpath."/".$release."/ajax", 0755, true);
            file_put_contents ($scriptname, $content);
          } 
        }    
      }
      
      //global includes
      $sqlapi = "select * from tblscript where interface = '' and release = '{$release}' and scripttype = 'global'";
      $apis = $CDE->get_row ($sqlapi);
      $globalcontent = "";
      foreach ($apis   as $apiid => $api) {
        if ($api->STATUS == 1) {
          $globalcontent .= "/*\nScript Name: {$api->SCRIPTNAME}\n";
          $globalcontent .= "Purpose:\n{$api->PURPOSE}\n";
          $globalcontent .= "Created By: {$api->CREATEDBY}\n";
          $globalcontent .= "Created Date: {$api->DATECRT}\n";
          $globalcontent .= "*/\n";
          $globalcontent .= $this->compile_code($api->CONTENT)."\n";    
        }
          else {
          
          $scriptname = $root."/".$outputpath."/".$release."/ajax/{$api->SCRIPTNAME}.php"; 
           
          $content = "/*\nScript Name: {$api->SCRIPTNAME}\n";
          $content .= "Purpose:\n{$api->PURPOSE}\n";
          $content .= "Created By: {$api->CREATEDBY}\n";
          $content .= "Created Date: {$api->DATECRT}\n";
          $content .= "*/\n";
          $content .= "require_once ('".$root."/cdesimple.php');\n";
          $content .= "require_once ('".$root."/connection.php');\n";
          $content .= "if (file_exists('".$root."/".$outputpath."/".$release."/functions.php')) {\n  require_once ('".$root."/".$outputpath."/".$release."/functions.php'); \n}\n";
          $content .= "if (file_exists('".$root."/".$outputpath."/".$release."/{$interface->NAME}api.php')) { require_once ('".$root."/".$outputpath."/".$release."/{$interface->NAME}api.php'); }\n\n";
          $content .= 'session_name($_CDEDEV_SESSION_NAME);'."\n";
          $content .= 'session_start();'."\n";
          $content .= $api->CONTENT."\n";
          $content = str_replace ("''", "'", $content);
          $precontent = $content;
          $content = '<?'.'php'."\n".$this->compile_code($content)."\n".'?'.'>'; 
          if (!file_exists($scriptname) && $this->check_code ($content, $root, $outputpath, $hostname)) {
            mkdir ($root."/".$outputpath."/".$release."/ajax", 0755, true);
            file_put_contents ($scriptname, $content);
          } 
        }    
      }
      
      
      
      //clean up quotes add tags
      $apifilename = $root."/".$outputpath."/".$release."/{$interface->NAME}api.php";
      if (trim ($apicontent) != "") {
        //$apicontent = str_replace ("\'", "'", $apicontent);
        $apicontent = str_replace ("''", "'", $apicontent);
        $precode = $apicontent;
        $apicontent = '<?'.'php'."\n".$this->compile_code($apicontent)."\n".'?'.'>';
        //output to the relevant location
        if (!file_exists($apifilename) && $this->check_code ($apicontent, $root, $outputpath, $hostname)) {
           mkdir ($root."/".$outputpath."/".$release, 0755, true);
           file_put_contents ($apifilename, $apicontent);
        }
      }
      
      //global functions
      $functionsfilename = $root."/".$outputpath."/".$release."/functions.php";
      if (trim ($globalcontent) != "") {
        //$globalcontent = str_replace ("\'", "'", $globalcontent);
        $globalcontent = str_replace ("''", "'", $globalcontent);
        $precode = $globalcontent;
        $globalcontent = '<?'.'php'."\n".$this->compile_code($globalcontent)."\n".'?'.'>';
        //output to the relevant location
        if (!file_exists($functionsfilename) && $this->check_code ($globalcontent, $root, $outputpath, $hostname)) {
           mkdir ($root."/".$outputpath."/".$release, 0755, true);
           file_put_contents ($functionsfilename, $globalcontent);
        }
      }
      
      
      $this->get_css ($interfacename, $root, $outputpath, $hostname );
      
      
      $scriptfilename = $root."/".$outputpath."/".$release."/{$interface->NAME}.php";
      if (trim($script->CONTENT)) {
        //$scriptcontent = str_replace ("\'", "'", $script->CONTENT);
        $scriptcontent = str_replace ("''", "'", $this->compile_code($script->CONTENT));
        //we dont expose the user to the class or main function, they only need to code
        $header = '<?'.'php'."\n".'if (file_exists(\''.$apifilename.'\')) { require_once (\''.$apifilename.'\'); }'."\n".'class '.$interface->NAME.'_int extends cde_interface {
    function get_interface ($CDE, $go, $request, $cdedev) {
      $DEV = $cdedev->DEV;
      //iterate values into $html variable to return things from the interface
      //overload the header and footer function to get your own header and footer going.
      ';
        $footer = "\n".'    return $html;'."\n".'  }'."\n".'}'."\n".'?'.'>';
        $scriptcontent = $header.$scriptcontent.$footer;
        if (!file_exists($scriptfilename) && $this->check_code ($scriptcontent, $root, $outputpath, $hostname)) {
           mkdir ($root."/".$outputpath."/".$release, 0755, true);
           file_put_contents ($scriptfilename, $scriptcontent);
        }
      }
        
      return $interface;
    }
  	
  	function draw_interface ($interface) {
  		$html .= "<a class=\"interface\" href=\"javascript:void(0)\" onclick=\"setinterfaceid({$interface->INTERFACEID}, true)\"> {$interface->NAME} </a>";
  		$html .= "<a class=\"interfacedel\" href=\"javascript:void(0)\" onclick=\"if (confirm('All related code will be removed also! Delete interface {$interface->NAME} ?')) { setinterfaceid({$interface->INTERFACEID}, false); setinterfaceprocessid(200, true); } \">  - </a>";
  		return $html;	  
  	}
  	
  	function draw () {
  		$CDE = $this->CDE;
  		$request = $this->request;
  		
  		$html  = $CDE->switchid("interfaceprocessid");
  		$html .= $CDE->switchid ("interfaceid");
  		switch ($request["interfaceprocessid"]) {
  			case 100:
  			  $this->insert_interface ($request["edtInterfaceName"]);	
  			  $html .= setinterfaceprocessid (0, true);
  			break;
  			case 200:
  		     $this->delete_interface ($request["interfaceid"]);	
  			   unset($request["interfaceid"]);
  			   $html .=  setinterfaceprocessid (0, true);
  			break;
  			default:
  			  $interfaces = $this->get_interfaces();
  			  foreach ($interfaces as $iid => $interface) {
  				  $html .= $this->draw_interface ($interface);  
  			  }	  
  			  $html .= $CDE->input ("edtInterfaceName", 140, "Interface Name", "", "text", $value="");	
  			  $html .= $CDE->input("btnAdd",  $width=50, $alttext="Click here to add an interface", $compulsory="", $inputtype="button", $value="Add", $event="onclick=\"setinterfaceprocessid(100, true);\"");	
  			break;
  		}
  		
  		return $html;
  	}	
  }

  class cde_cdedev {
    public $CDE;
    public $outputpath = "includes";
    public $request;
    public $root;
    public $hostname;
    
    
    
    
    function create_tables ($CDE, $firsttime=false) {
	 
	    $interfacetable = "create table if not exists tblinterface (
                             interfaceid integer default 0 not null,
                             name varchar (200) default '',
                             purpose blob default null,
                             datecrt timestamp default null,
                             createdby varchar (200) default '',
                       primary key (interfaceid)
                    )";
		
	    $CDE->exec ($interfacetable);
		
      $usertable = "create table if not exists tbluser (
                      userid integer default 0 not null,
                      name varchar (200) default '',
                      email varchar (200) default '',
                      passwd varchar (255) default '".crypt("changeme")."',
                      status integer default 0 not null,
                      datecrt date default null,
                      sessionid varchar (200) default '',
                      primary key (userid)
                    )";
    
      $CDE->exec($usertable);
      
      $lnkscriptuser = "create table if not exists lnkscript_user (
                          scriptid integer default 0 not null references tblscript (scriptid) on update cascade on delete cascade,
                          userid integer default 0 not null references tbluser (userid) on update cascade on delete cascade,
                          release varchar (20) default '',
                          sessionid varchar (200) default '',
                          primary key (scriptid, userid, release)                          
                        )";
      
      $CDE->exec($lnkscriptuser);
      
      $moduletable = "create table if not exists tblmodule (
                        moduleid integer default 0 not null,
                        modulename varchar (200) default '',
                        purpose blob default null,
                        parentid integer default 0 not null,
                        interface varchar (200) default '',
                        primary key (moduleid, interface)
                     )";
                     
      $CDE->exec($moduletable);
     
      $scripttable = "create table if not exists tblscript (
                        scriptid integer default 0 not null,
                        scriptname varchar (200) default '' not null,
                        scriptcategory varchar (200) default '' not null,
                        purpose blob default null,
                        interface varchar (200) default '' not null,
                        version varchar (100) default '' not null,
                        release varchar (100) default '' not null,
                        content blob default null,
                        scripttype varchar (20) default '',
                        status integer default 0,
                        createdby varchar (200) default '',
                        editedby varchar (200) default '',
                        lastuser varchar (200) default '',
                        datemod timestamp default null,
                        datecrt timestamp default null,
                        moduleid integer default 0 not null,
                        primary key (scriptname, interface, release, version)
                      )";
      
      $CDE->exec ($scripttable);
      
       $revisiontable = "create table if not exists tblrevision (
                        scriptid integer default 0 not null,
                        scriptname varchar (200) default '' not null,
                        purpose blob default null,
                        interface varchar (200) default '' not null,
                        version varchar (100) default '' not null,
                        release varchar (100) default '' not null,
                        content blob default null,
                        scripttype varchar (20) default '',
                        status integer default 0,
                        createdby varchar (200) default '',
                        editedby varchar (200) default '',
                        lastuser varchar (200) default '',
                        datemod timestamp default null,
                        datecrt timestamp default null,
                        moduleid integer default 0 not null,
                        primary key (scriptname, interface, release, version)
                      )";
      
      $CDE->exec ($revisiontable);
      
      $releasetable = "create table if not exists tblrelease (
                         releaseid integer default 0 not null,
                         release varchar (100) default '',
                         purpose blob default null,
                         releasenotes blob default null,
                         status integer default 0,
                         datecrt timestamp default null,
                         primary key (releaseid)
                      )";
                      
      $CDE->exec ($releasetable);  
      
      //add the basic code into the develop environment to write the stuff
      function insert_script ($CDE, $scriptname, $content, $interface, $scripttype, $release, $createdby, $purpose="", $moduleid=0, $version="1.0.0.0", $status=1, $scriptcategory="") {
        //$version = "1.0.0.0";
        $scriptid = $CDE->get_next_id ("tblscript", "scriptid");
        if ($scriptname == "") $scriptname = "New".$scriptid;
        $sqlinsert = "insert into tblscript (scriptid, scriptname, scriptcategory, content, interface, scripttype, release, createdby, purpose, moduleid, version, status, datecrt)
                                     values (?       , ?         ,              ?,  ?      , ?        , ?         , ?      , ?        , ?      , ?,        ?,       ?, 'now')";
        
        $CDE->exec ($sqlinsert, $scriptid, $scriptname, $scriptcategory, $content, $interface, $scripttype, $release, $createdby, $purpose, $moduleid=0, $version, $status);
      
        return $scriptid;
      }
      
      if (file_exists($scriptfilename = $this->root."/".$this->outputpath."/Developerapi.first.php")) {
        $firsttime = true;      
      }
      
      if ($firsttime) {
        $release = "v1.0.0.0";
        $CDE->exec ("insert into tblinterface (interfaceid, name, purpose, datecrt) values (0, 'Developer', 'Write applications', 'now')");
        $CDE->exec ("insert into tblinterface (interfaceid, name, purpose, datecrt) values (1, 'Public', 'What the outside user will see', 'now')");
        //echo "insert into tbluser (userid, name, email, passwd, status, datecrt) values (0, 'Admin', 'admin', '".crypt('admin')."', 1, 'now')";
        $CDE->exec ("insert into tbluser (userid, name, email, passwd, status, datecrt) values (0, 'Admin', 'admin', '".crypt('admin')."', 1, 'now')");
      
        $scriptfilename = $this->root."/".$this->outputpath."/ajaxSaveScript.first.php";
        insert_script ($CDE, $scriptname="ajaxSaveScript", file_get_contents ($scriptfilename), $interface="Developer", $scripttype="api", $release="v1.0.0.0", $createdby="Andre van Zuydam", $purpose="To enable developing to happen", $moduleid=0, $version="1.0.0.0", 0);
        unlink ($scriptfilename);
        
        $scriptfilename = $this->root."/".$this->outputpath."/ajaxProcessSQL.first.php";
        insert_script ($CDE, $scriptname="ajaxProcessSQL", file_get_contents ($scriptfilename), $interface="Developer", $scripttype="api", $release="v1.0.0.0", $createdby="Andre van Zuydam", $purpose="To enable developing to happen", $moduleid=0, $version="1.0.0.0", 0);
        unlink ($scriptfilename);
        
        $scriptfilename = $this->root."/".$this->outputpath."/Developerapi.first.php";
        insert_script ($CDE, $scriptname="Developerapi", file_get_contents ($scriptfilename), $interface="Developer", $scripttype="api", $release="v1.0.0.0", $createdby="Andre van Zuydam", $purpose="To enable developing to happen", $moduleid=0);
        unlink ($scriptfilename);
        
        $scriptfilename = $this->root."/".$this->outputpath."/css/Developercss.css";
        insert_script ($CDE, $scriptname="Developercss", file_get_contents ($scriptfilename), $interface="Developer", $scripttype="css", $release="v1.0.0.0", $createdby="Andre van Zuydam", $purpose="To enable developing to happen", $moduleid=0);
        unlink ($scriptfilename);
        
        $scriptfilename = $this->root."/".$this->outputpath."/Developer.first.php";
        insert_script ($CDE, $scriptname="Developer", file_get_contents ($scriptfilename), $interface="Developer", $scripttype="interface", $release="v1.0.0.0", $createdby="Andre van Zuydam", $purpose="To enable developing to happen", $moduleid=0);
        unlink ($scriptfilename);
        unlink ($this->root."/".$this->outputpath."/css");
      }
    }
    
    
    function cde_cdedev ($CDE, $request, $outputpath="includes") {
      $this->CDE = $CDE;
      $this->createform = $createform;
      $this->request = $request;
      $this->outputpath = $outputpath;  
      $this->root = dirname(__FILE__);
      $this->hostname = $_SERVER["SERVER_NAME"]; 
      $this->browser = "";      
       
      
      //create the database connection
      if (!file_exists ($this->root."/database/cde.db")) {
        mkdir ($this->root."/database", 0755, true);
      } 
      
      
      //create the database tables
      $DEV = new CDESimple ($this->root."/database/cde.db", "", "", "sqlite3", false, "dd/mm/YYYY");
      $this->create_tables ($DEV);
      $this->DEV = $DEV;
      $this->ainterface = new dev_interface ($this->DEV, $request);
    }
    
    function __toString () {
      return $this->draw();
    }
    
    /* The core output functio which does all the work */
    
    function draw () {
      $CDE = $this->CDE;
      $request = $this->request;
      $html = "";
      /* require all the interfaces accordingly */
      if (!isset($this->request["interface"])) $this->request["interface"] = "Public";
      
      //compile the includes for the interface
      
      $interface = $this->ainterface->get_interface ($this->request["interface"], $this->root, $this->outputpath, $this->hostname);
       
      //global functions
      $functionspath = $this->outputpath."/".$this->request["release"]."/functions.php";
      if (file_exists($functionspath)) {
        require_once ($functionspath);
      }  
       
       
      if ($interface->NAME != "") {  
        $interfacepath = $this->outputpath."/".$this->request["release"]."/".$this->request["interface"].".php";
        if (file_exists ($interfacepath)) {
          require_once ($interfacepath);
          eval ('$html .= new '.$this->request["interface"].'_int ($CDE, $this->request["go"], $this->request, $this);');
        }
          else {
          $html .= "Interface requires coding";  
        }
      }
        else {
        $html .= "Interface unknown.";
      }
      return $html;
    }
    
    //this must output all the scripts to the relevant includes folder with release version
    // it should update a release file so the application knows the default release;
    function do_release ($minor=true) {
      $CDE = $this->DEV;
      $current = $this->request["release"];
      
      //copy all the scripts over to the new version
      $release = file_get_contents ("release.inc"); //example 1.0.0.0
      
      if ($minor) {
        $release++;
      } else {
        $major = substr($release, 0, strpos($release,"."));
        $major++;
        $release = $major.".0.0.0";
      }    
      
      $scripts = $CDE->get_row ("select * from tblscript where release = '{$current}'");
      foreach ($scripts as $sid => $script) {
        $script->CONTENT = str_replace ("''", "'", $script->CONTENT);
        insert_script ($CDE, $script->SCRIPTNAME, $script->CONTENT, $script->INTERFACE, $script->SCRIPTTYPE, "v".$release, $script->CREATEDBY, $script->PURPOSE, $moduleid=0, $script->VERSION, $script->STATUS, $script->SCRIPTCATEGORY);
      }
      
      return $release;    
    }
    
    
    
  }
  
  //This is the base interface class which all views are derived from for CDE
  class cde_interface {
    public $CDE;
    public $go;
    public $request;
    public $cdedev;
    public $contextprefix;
    
    function __construct ($CDE, $go, $request, $cdedev) {
      $this->CDE = $CDE;
      $this->go = $go;
      $this->request = $request;  
      $this->cdedev = $cdedev;
      $this->contextprefix = str_replace ( "/index.php", "", $_SERVER["SCRIPT_NAME"] );
      
     // print_r ($_SERVER);
    } 
    
    function __toString() {
      return $this->get_interface ($this->CDE, $this->go, $this->request, $this->cdedev); 
    }
    
    function get_interface ($CDE, $go, $request, $cdedev) {
      return $html;
    }
    
    function html_header ($title="replace this", $stylesheet="default", $javascript = "", $metadescription = "", $linkedsrc = "", $bodyevent = "", $formevent = ""){
      $outputpath = $this->cdedev->outputpath;
      $release = $this->cdedev->request["release"];
    
  	  $html = "<!DOCTYPE html>\n<head>\n";
	    
      if($metadescription != ""){
	      $html .= "<meta name=\"description\" content=\"{$metadescription}\" />\n";
	    }
		
	    //$html .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=9\" />\n";
          $html .= "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=9; IE=8; IE=7; IE=EDGE\" />\n"; 	
  	  if($stylesheet != ""){		
  		  $html .= "<link type=\"text/css\" href=\"{$this->contextprefix}/{$outputpath}/{$release}/css/{$stylesheet}.css\" rel=\"stylesheet\" media=\"screen\" />\n";
  	  }
  	
  	  if($javascript != ""){
  		  $html .= "<script type=\"text/javascript\" src=\"{$this->contextprefix}/{$outputpath}/{$release}/js/{$javascript}.js\">\n</script>\n";
  	  }
  	 
      if($linkedsrc != ""){
  	  	$html .= $linkedsrc;
  	  }
  	
  	  $html .= "<title>{$title}</title>\n</head>\n<body {$bodyevent}>\n<form id=\"form\" method=\"POST\" enctype=\"multipart/form-data\" {$formevent}>\n";
  	  $html .= "<input type=\"hidden\" name=\"interface\" value=\"{$this->request["interface"]}\" />";
  		
  	  return $html;
    }
    
    function html_header_noform ($title="replace this", $stylesheet="default", $javascript = "", $metadescription = "", $linkedsrc = "", $bodyevent = "", $formevent = ""){
      $outputpath = $this->cdedev->outputpath;
      $release = $this->cdedev->request["release"];
    
  	  $html = "<!DOCTYPE html>\n<head>\n";
	    
      if($metadescription != ""){
	      $html .= "<meta name=\"description\" content=\"{$metadescription}\" />\n";
	    }
		
	    //$html .= "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n<meta http-equiv=\"X-UA-Compatible\" content=\"IE=9\" />\n";
          $html .= "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=9; IE=8; IE=7; IE=EDGE\" />\n"; 	
  	  if($stylesheet != ""){		
  		  $html .= "<link type=\"text/css\" href=\"{$this->contextprefix}/{$outputpath}/{$release}/css/{$stylesheet}.css\" rel=\"stylesheet\" media=\"screen\" />\n";
  	  }
  	
  	  if($javascript != ""){
  		  $html .= "<script type=\"text/javascript\" src=\"{$this->contextprefix}/{$outputpath}/{$release}/js/{$javascript}.js\">\n</script>\n";
  	  }
  	 
      if($linkedsrc != ""){
  	  	$html .= $linkedsrc;
  	  }
  	
  	  $html .= "<title>{$title}</title>\n</head>\n<body {$bodyevent}>\n";
  	  $html .= "<input type=\"hidden\" name=\"interface\" value=\"{$this->request["interface"]}\" />";
  		
  	  return $html;
    }
    
    
    
    function html_ajaxhandler ($name="cdeajax") {
      $root = $this->cdedev->root;
      $outputpath = $this->cdedev->outputpath;
      $request = $this->request;
      
      $scriptpath = $outputpath."/".$request["release"]."/ajax/";
      
      if (file_exists($this->cdedev->root."/php4sack/PHP4sack.php")) {  
        $html .= "<script type=\"text/javascript\" src=\"{$this->contextprefix}/php4sack/tw-sack.js\"></script>";
        require_once("php4sack/PHP4sack.php");  
        //make the ajax handler div + functions
        $html .= script (slib_compress_script ("
        function getValues ( form, name ) {
          var values = [];
          for(n=0;n < form.length;n++){
            if(form[n].name == name && form[n].checked){
              values.push(form[n].value);
            }
          }
          return values;
        }
        
        function call_{$name} (scriptname, scripttarget, scrollwindow, params) {
         if(typeof(scrollwindow)==='undefined') scrollwindow = false;
         if(typeof(params)==='undefined') params = null;

         if(scrollwindow){
          window.scrollTo(0,0);
         }
          //go through all the form elements and pass them to ajax
          var {$name} = new sack();
          if (document.forms) {	
            for (iform = 0; iform < document.forms.length; iform++) {
              var form = document.forms[iform];
              var e = form.elements;
              for ( var elem, i = 0; ( elem = e[i] ); i++ ) {
                if(elem.type == 'checkbox'){
                	if(elem.checked){
                	  var oldvalue = elem.value;
                          if (elem.value === undefined) {
                         	elem.value = 1;
                          } 
                	}
                          else {
                            var oldvalue = elem.value;
                            elem.value = 0;
                          
                          }
                }
                else if (elem.type == 'radio') {
                  
                  if (elem.checked) {
                    //do nothing
                  }  
                    else {
                    var oldvalue = elem.value;
                    elem.value = '!---!---!';
                  }
                }

                if (elem.value != '!---!---!') {
                    {$name}.setVar(elem.name, encodeURIComponent(elem.value));
                 } 
                 else {
                  elem.value = oldvalue; 
                }
                if (elem.type == 'checkbox') {
                  elem.value = oldvalue;
                }
                             
              }
            }   
          }
          
          if (params !== null) {
            for (key in params) {
              {$name}.setVar(key, encodeURIComponent(params[key]));  
            }      
          } 
 
          {$name}.requestFile = '".$this->contextprefix."/".$scriptpath."'+scriptname+'.php';
        	if (form)
        	{
            {$name}.method = form.method;
          }  
        	{$name}.element = scripttarget;
        	{$name}.runAJAX();
        }
        "));
      }
        else {
        $html = "PHP4sack is not available, please download from Sourceforge and input in the folder";  
      }
      
      return $html;
    }
    
    function refreshcss () {
      return script (slib_compress_script ("(function() {
                                	var phpjs = {
                                		array_filter: function( arr, func )
                                		{
                                			var retObj = {}; 
                                			for ( var k in arr )
                                			{
                                				if ( func( arr[ k ] ) )
                                				{
                                					retObj[ k ] = arr[ k ];
                                				}
                                			}
                                			return retObj;
                                		},
                                		filemtime: function( file )
                                		{
                                			var headers = this.get_headers( file, 1 );
                                			return ( headers && headers[ 'Last-Modified' ] && Date.parse( headers[ 'Last-Modified' ] ) / 1000 ) || false;
                                	    },
                                	    get_headers: function( url, format )
                                	    {
                                			var req = window.ActiveXObject ? new ActiveXObject( 'Microsoft.XMLHTTP' ) : new XMLHttpRequest();
                                			if ( !req )
                                			{
                                				throw new Error('XMLHttpRequest not supported.');
                                			}
                                
                                			var tmp, headers, pair, i, j = 0;
                                			try
                                			{
                                				req.open( 'HEAD', url, false );
                                				req.send( null ); 
                                				if ( req.readyState < 3 )
                                				{
                                					return false;
                                				}
                                				tmp = req.getAllResponseHeaders();
                                				tmp = tmp.split('\\n');
                                        tmp = this.array_filter( tmp, function( value )
                                				{
                                					return value.toString().substring( 1 ) !== '';
                                				});
                                				headers = format ? {} : [];
                                	
                                				for ( i in tmp )
                                				{
                                					if ( format )
                                					{
                                						pair = tmp[i].toString().split(':');
                                						headers[ pair.splice( 0, 1 ) ] = pair.join( ':' ).substring( 1 );
                                					}
                                					else
                                					{
                                						headers[ j++ ] = tmp[ i ];
                                					}
                                				}
                                	
                                				return headers;
                                			}
                                			catch ( err )
                                			{
                                				return false;
                                			}
                                		}
                                	};
                                
                                	var cssRefresh = function() {
                                		this.reloadFile = function( links )
                                		{
                                			for ( var a = 0, l = links.length; a < l; a++ )
                                			{
                                				var link = links[ a ],
                                					newTime = phpjs.filemtime( this.getRandom( link.href ) );
                                
                                				//	has been checked before
                                				if ( link.last )
                                				{
                                					//	has been changed
                                					if ( link.last != newTime )
                                					{
                                						//	reload
                                						link.elem.setAttribute( 'href', this.getRandom( link.href ) );
                                					}
                                				}
                                
                                				//	set last time checked
                                				link.last = newTime;
                                			}
                                			setTimeout( function()
                                			{
                                				this.reloadFile( links );
                                			}, 1000 );
                                		};
                                
                                		this.getHref = function( f )
                                		{
                                			return f.getAttribute( 'href' ).split( '?' )[ 0 ];
                                		};
                                		this.getRandom = function( f )
                                		{
                                			return f + '?x=' + Math.random();
                                		};
                                
                                
                                		var files = document.getElementsByTagName( 'link' ),
                                			links = [];
                                
                                		for ( var a = 0, l = files.length; a < l; a++ )
                                		{			
                                			var elem = files[ a ],
                                				rel = elem.rel;
                                			if ( typeof rel != 'string' || rel.length == 0 || rel == 'stylesheet' )
                                			{
                                				links.push({
                                					'elem' : elem,
                                					'href' : this.getHref( elem ),
                                					'last' : false
                                				});
                                			}
                                		}
                                		this.reloadFile( links );
                                	};
                                	cssRefresh();
                                })();"));
    
    }
    
    function html_footer ($debug=false, $cssdesign=false){
      if ($debug) {
        $debugdata = "<pre>".print_r ($_REQUEST, 1)."</pre>";
        $debugdata .= "<pre>".print_r ($_SESSION, 1)."</pre>";
        $debugdata .= "<pre>".print_r ($_COOKIE, 1)."</pre>";
      }
      
      if ($cssdesign) {
        
        if ($_REQUEST["interface"] != "Developer") {
          $script = $this->refreshcss();
        }
      }
      
    	$html = "  </form>\n{$debugdata}{$script}</body>\n</html>";
    	return $html;
    }
    
    function html_footer_noform ($debug=false){
      if ($debug) {
        $debugdata = "<pre>".print_r ($_REQUEST, 1)."</pre>";
        $debugdata .= "<pre>".print_r ($_SESSION, 1)."</pre>";
        $debugdata .= "<pre>".print_r ($_COOKIE, 1)."</pre>";
      }
      
      if ($cssdesign) {
        
        if ($_REQUEST["interface"] != "Developer") {
          $script = $this->refreshcss();
        }
      }
      
    	$html = "\n{$debugdata}{$script}</body>\n</html>";
    	return $html;
    }
    
    
  }
    
?>