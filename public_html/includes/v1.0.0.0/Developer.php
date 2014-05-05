<?php
if (file_exists('C:\Users\Andre\Documents\GitHub\tina4php\public_html/includes/v1.0.0.0/Developerapi.php')) { require_once ('C:\Users\Andre\Documents\GitHub\tina4php\public_html/includes/v1.0.0.0/Developerapi.php'); }
class Developer_int extends cde_interface {
    function get_interface ($CDE, $go, $request, $cdedev) {
      $DEV = $cdedev->DEV;
      //iterate values into $html variable to return things from the interface
      //overload the header and footer function to get your own header and footer going.
      //Development interface
//Andre#:Testing the development interface
$scripttitle = $_SERVER["HTTP_HOST"];
if(isset($request["edtScriptName"])){
	if($request["edtScriptName"] != ""){
		$scripttitle .= " ".$request["edtScriptName"];
	}
}

//removing the following scripts will break the developement environment!
$linkedsrc = "<script type=\"text/javascript\" src=\"{$this->contextprefix}/php4sack/tw-sack.js\"></script>
			       <link rel=\"stylesheet\" href=\"{$this->contextprefix}/codemirror/lib/codemirror.css\">
				   <link rel=\"stylesheet\" href=\"{$this->contextprefix}/codemirror/theme/monokai.css\">
				   <link rel=\"stylesheet\" href=\"{$this->contextprefix}/codemirror/addon/dialog/dialog.css\">
                   <script src=\"{$this->contextprefix}/codemirror/lib/codemirror.js\"></script>
				   <script src=\"{$this->contextprefix}/codemirror/addon/edit/closebrackets.js\"></script>
				   <script src=\"{$this->contextprefix}/codemirror/addon/edit/matchbrackets.js\"></script>
				   <script src=\"{$this->contextprefix}/codemirror/addon/search/searchcursor.js\"></script>
				   <script src=\"{$this->contextprefix}/codemirror/addon/search/search.js\"></script>
				   <script src=\"{$this->contextprefix}/codemirror/addon/dialog/dialog.js\"></script>
				   <script src=\"{$this->contextprefix}/codemirror/addon/search/match-highlighter.js\"></script>
				   <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\" />
				   <meta name=\"apple-mobile-web-app-capable\" content=\"yes\">
				   <meta name=\"apple-mobile-web-app-status-bar-style\" content=\"black-translucent\" />
				   <script type=\"text/javascript\" src=\"{$this->contextprefix}/jquery/js/jquery-1.9.1.js\"></script>
				   <script type=\"text/javascript\" src=\"{$this->contextprefix}/jquery/js/jquery-ui-1.10.3.custom.js\"></script>
				   <script type=\"text/javascript\" src=\"{$this->contextprefix}/jquery/js/touch-punch.js\"></script>
				   <script type=\"text/javascript\" src=\"{$this->contextprefix}/jquery/js/jqtree.js\"></script>
				   <script type=\"text/javascript\" src=\"{$this->contextprefix}/jquery/js/w2ui-1.3.js\"></script>				  
				   <link href=\"{$this->contextprefix}/jquery/css/ui-lightness/jquery-ui-1.10.3.custom.css\" rel=\"stylesheet\" />
				   <link href=\"{$this->contextprefix}/jquery/css/ui-lightness/w2ui-1.3.css\" rel=\"stylesheet\" />
				   <script type=\"text/javascript\" src=\"{$this->contextprefix}/ckeditor/ckeditor.js\"></script>
				   <meta charset=\"utf-8\">";
					
					
				   
			   
$html = $this->html_header("CDE-Developer - {$scripttitle}", "Developercss", "", "", $linkedsrc, $bodyevent = "", $formevent = "onsubmit=\"return false\"");
$html .= $this->html_ajaxhandler();
$html .= $CDE->switchid ("developid");
$html .= $CDE->switchid ("navigateid");

if (!isset ($_SESSION["devloggedin"])) {
	$_SESSION["devloggedin"] = 0;
}

function show_login ($CDE) {
	$buttonevent = "onclick=\"setdevelopid(200, true);\"";
	$html .= "<div id=\"login\">
<p>Development Login</p>	 
<label for=\"edtEmail\">Email:</label>
".$CDE->input("edtEmail", 200, "Username")."<br />
<label for=\"edtPassword\">Password:</label>
".$CDE->input("edtPassword", 100, "Password", "", "password")." <br />
".$CDE->input("edtSubmit", "", "", "", "submit", "Login", $buttonevent)."  	 			
</div>";
	return $html;
}


switch ($request["developid"]) {
	case 100:
	$html .= show_login($CDE);
	break;
	case 500:
	unset($_SESSION["devloggedin"]);
	session_write_close();
	$html .= setdevelopid(0, true);
	break;
	case 200:
	$sqluser = "select * from tbluser where email = '{$request["edtEmail"]}' and status = 1";
	$user = $DEV->get_value (0, $sqluser);
	
	if ($user->EMAIL != "" && crypt ($request["edtPassword"], $user->PASSWD) == $user->PASSWD) {
		$_SESSION["devloggedin"] = 1;
		$_SESSION["devuserid"] = $user->USERID;
		$_SESSION["devusername"] = $user->NAME;
		$_SESSION["devemail"] = $user->EMAIL;
		
		//update the table
		
		$sqlupdate = "update tbluser set sessionid = ? where userid = ?";
		$DEV->exec ($sqlupdate, session_id(), $user->USERID);
		
		session_write_close();
		$html .= setdevelopid (0, true);
	}
	else {
		$html .= setdevelopid (100, true);
	}  
	break;
	default:
	if ($_SESSION["devloggedin"] != 1) {
		$html .= setdevelopid (100, true);
	}
	else {
	
	    if ($_REQUEST["content"] == "openscript") {
		
		  $_REQUEST["moduleprocessid"] = 300;
		  $_REQUEST["scriptprocessid"] = 110;
		  if ($_REQUEST["interfaceid"] == 9999999) {
		    $_REQUEST["interfaceid"] = "";
		  }
		
		}
		 else 
		if ($_REQUEST["content"] == "scriptoverview") {
		  $_REQUEST["navigateid"] = 700;
		} 
		 
		 else
		if ($_REQUEST["content"] == "deletescript") {
		  $DEV->exec ("delete from tblscript where scriptid = {$_REQUEST["scriptid"]}"); 
		  
		  echo script ("location.href = '/release/{$_REQUEST["release"]}/interface/Developer/';");
		 
		} 
		
		
		$html .= "<nav class=\"navmain\">
<a href=\"javascript:void(0)\" onclick=\"setnavigateid(100, true);\">Interfaces</a>
<a  href=\"javascript:void(0)\" onclick=\"setnavigateid(200, true);\">Database Admin</a>
<a  href=\"javascript:void(0)\" onclick=\"if (document.forms[0].scriptid) { setscriptprocessid('0', false); setscriptid('0', false); } setnavigateid(300, true);\">Functions</a>
<a  href=\"javascript:void(0)\" onclick=\"setnavigateid(400, true);\">Users</a>
<a  href=\"javascript:void(0)\" onclick=\"setnavigateid(600, true);\">Documentation</a>
<a  href=\"javascript:void(0)\" onclick=\"setnavigateid(700, true);\">Code Map</a>
<a  href=\"javascript:void(0)\" onclick=\"setnavigateid(800, true);\">Update Environment</a>
<a  href=\"javascript:void(0)\" onclick=\"setdevelopid(500, true);\">Logout</a>{$_SESSION["devusername"]}</nav>";
		
		$html .= "<div class=\"main\">"; 
		
		switch ($_REQUEST["navigateid"]) {
			case 600:
			  $html .= "<iframe src=\"/documentation/doku.php\" width=\"100%\" height=\"800px\"></iframe>";			
			break;
			case 700:
			  $codemap = "".new TCodeMap ($DEV, $request, $this->cdedev);
			  $html .= div ( array ("style" => "padding: 30px;"),  $codemap );
			break;
			case 200:
			$request["moduleprocessid"] = 500;
			if ($request["moduleprocessid"] != 500) {
				
				$request["scriptprocessid"] = 0;
				$request["scriptid"] = '';
			}	 
			$script = new dev_sql ($DEV, $request, $this->cdedev, $this->contextprefix);
			//load the module view
			$html .= "<div>".$script."</div>";
			break;
			case 300:
			$request["moduleprocessid"] = 400;
			
			
			$script = new dev_script ($DEV, $request, $this->cdedev, "",  $this->contextprefix);
			
			//load the module view
			$html .= "<div>".$script."</div>";
			break;
			case 400:
			
			$usermodule = new dev_user ($DEV, $request);
			$html .= "<div>".$usermodule."</div>";
			break;
			case 800:
			  $html .= new TUpdateMe ($DEV, $request, $this->cdedev);
			break;
			default:
			//call up the interfaces screen 
			$interface = new dev_interface ($DEV, $request); 
			$html .= "<nav class=\"navinterface\">".$interface."</nav>";
			
			if (!isset($request["interfaceid"])) {
				$interfaceid = $DEV->get_value (0, "select * from tblinterface order by name");
				$request["interfaceid"] = $interfaceid->INTERFACEID;
			}
			
			//determine what to load here
			$module = new dev_module ($DEV, $request, $request["interfaceid"], $this->cdedev) ;
			$script = new dev_script ($DEV, $request, $this->cdedev, $request["interfaceid"], $this->contextprefix);
			
			//load the module view
			$html .= "<div class=\"sideBar\">".$module."</div><div class=\"scriptArea\">".$script."</div>";
			
			break; 
			
			
		}
		
		$html .= "</div>"; 
		
	}
	
	
	break;
}
$html .= $this->html_footer(false, false);
    return $html;
  }
}
?>