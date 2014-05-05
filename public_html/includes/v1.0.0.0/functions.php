<?php
/*
Script Name: TINA4Grid
Purpose:

Created By: 
Created Date: 
*/
//A crud handling grid based on an sql query works with an AJAX handler to get the data.
//It will need the normal headers to make things work properly
class TINA4GRID {
  private $CDE;
  private $request;
  private $title;
  private $tablename;
  private $columns;
  private $primarycolumn;
  private $permissions;
  private $instancename;
  private $rows;
  private $showsearch;
  private $showfooter;
  private $showcheckbox;
  private $groupby;
  private $postevent;
  private $inputprefix;
  private $buttoncaptions;
  private $datefields;
  private $passwordfields;
    
  //constructor function to setup the grid
  function __construct ($CDE, 
                        $request, 
						$title,
						$tablename, 
						$columns, 
						$primarycolumn, 
						$permissions="111111",
						$instancename="grid", 
						$rows=20, 
						$showsearch=true, 
						$showfooter=true,
						$showcheckbox=true,
						$groupby="",
						$custombuttons="",
						$onlyshowcustom=false,
						$customform="",
						$postevent="document.forms[0].submit();",
						$inputprefix="edt",
						$formcolumns="",
						$buttoncaptions = ""
						) {
    $this->CDE = $CDE;
	$this->request = $request;
	$this->title = $title;
	$this->tablename = $tablename;
	$this->columns = $columns;
	$this->primarycolumn = $primarycolumn;
	$this->permissions = $permissions;
	$this->instancename = $instancename;
	$this->rows = $rows;
	$this->showsearch = $showsearch;
	$this->showfooter = $showfooter;
	$this->showcheckbox = $showcheckbox;
	$this->groupby = $groupby;
	$this->custombuttons = $custombuttons;
	$this->onlyshowcustom = $onlyshowcustom;
	$this->customform = $customform;
	$this->postevent = $postevent;
	$this->inputprefix = $inputprefix;
	
	if ($formcolumns == "") {
	  $this->formcolumns = $this->columns;
	}
	
	if ($buttoncaptions == "") {
	  $this->buttoncaptions = array ("Add", "Update", "Delete", "PDF", "Excel", "Cancel");
	}
	
	//SOrt out the field types
	$datefields = array();
	$passwordfields = array();
	foreach ($this->formcolumns as $fid => $formcolumn) {
	  if ( strtolower (trim ($formcolumn["type"])) == "date" || strtolower (trim ($formcolumn["type"])) == "timestamp" ) {
	    $datefields[] = $formcolumn["name"];
	  }
	   else
	  if ( strtolower (trim ($formcolumn["type"])) == "password" ) {
	    $passwordfields[] = $formcolumn["name"];
	  } 
	}
	
	$this->datefields = implode (",", $datefields);
	$this->passwordfields = implode (",", $passwordfields);
	
	
  }

  
  function __toString () {
    return $this->draw();
  }
  
  
  function form ( $action, $primarykey ) {
    $html = "";
	
	//For the records to save we need to prefix each record with a number
	//Naming convention is prefixFIELDNAME[pkey]
	foreach ( $primarykey  as $pid => $pkvalue ) {
		if ($action == "update") {
		  $sqlselect = "select * from {$this->tablename} where $this->primarycolumn = '{$pkvalue}'";
		}
		  else {
		  $sqlselect = "select * from {$this->tablename} where $this->primarycolumn = '-{$pkvalue}-'";
		}  


		$record = $this->CDE->get_record ($sqlselect , $this->inputprefix, $rowtype = 2, $fetchblob = true );

		$formfields = array ();
        $formfields [] = input (array ("name" => $this->inputprefix."PK[{$pid}]", "type" => "hidden"), $pkvalue ); 
		foreach ( $this->formcolumns as $fcid => $formcolumn ) {
		  //determine the input type
		  
		  $fieldname = $this->inputprefix.strtoupper ($formcolumn["name"]);
		  
		  $inputtype = "text";
		  
		  if (strtolower (trim($formcolumn["type"])) == "date") {
		      if ($action == "insert") {
			    $record[$fieldname] = $this->CDE->translate_date ( date("Y-m-d"), "YYYY-mm-dd", $this->CDE->outputdateformat ); 
			  }
			  $inputtype = "date";
		  }
		      else
		  if (strtolower (trim($formcolumn["type"])) == "timestamp" || strtolower (trim($formcolumn["type"])) == "datetime" ) {
		      if ($action == "insert") {
			    $record[$fieldname] = $this->CDE->translate_date ( date("Y-m-d H:i:s"), "YYYY-mm-dd H:i:s", $this->CDE->outputdateformat ); 
			  }
			  $inputtype = "datetime";
		  }	
		    else {
			if (isset ( $formcolumn ["type"] )) {
			  $inputtype = strtolower( trim( $formcolumn ["type"] ) );
			}
		  }	
		  
		  
		  
		  

		  $formfields[] = label ( array ("class" => "labelinput", "for" => $pid."_".$fieldname) , span ( $formcolumn["alias"] ), 
								input ( array ( "class" => "forminput", "name" => $fieldname."[{$pid}]", "id" => $pid."_".$fieldname, "type" => $inputtype), $record[$fieldname] ) );
		}
  	    $records[] = shape ( h4 ( "Record ({$pkvalue}) ".($pid+1) ), $formfields);
	
	}
	
	$nextevent = $action."post";

    $nextcaption = ($action == "insert") ? $this->buttoncaptions[0]  : $this->buttoncaptions[1] ;

	$buttons = array ();
	$buttons [] = input (array ("type" => "button", "class" => "button", "onclick" => "set{$this->instancename}action('{$nextevent}'); {$this->postevent} "), $nextcaption);
	$buttons [] = input (array ("type" => "button", "class" => "button", "onclick" => "set{$this->instancename}action(''); {$this->postevent} "), $this->buttoncaptions[5]);

	$html .= div ( array ("class" => "gridform"),  h1 ( ucwords($action) ), $buttons, $records, $buttons );
	
    return $html;
  }
  
  
  function title () {
    $offset = 0;
    if ($this->showcheckbox) {
	  $offset++;
	}
    return tr ( th ( array ("class" => "gridtitle", "colspan" => count ( $this->columns ) + $offset ),  $this->title ) ); 
  }
  
  function search () {
    $offset = 0;
    if ($this->showcheckbox) {
	  $offset++;
	}
    return tr ( td ( array ("class" => "search", "colspan" => count ( $this->columns ) + $offset ),  "Search" ) );
  }
  
  function buttons () {
    $offset = 0;
    if ($this->showcheckbox) {
	  $offset++;
	}
    $buttons = "";
	
	if ( $this->permissions[0] == 1 ) {
	  $buttons .= input (array ("type" => "button", "class" => "button", "onclick" => "set{$this->instancename}action('insert'); {$this->postevent} "), $this->buttoncaptions[0]);
    }
    
	if ( $this->permissions[1] == 1 ) {
	  $buttons .= input (array ("type" => "button", "class" => "button", "onclick" => "set{$this->instancename}action('update'); {$this->postevent} "), $this->buttoncaptions[1]);
	}
	
	if ( $this->permissions[2] == 1 ) {
	  $buttons .= input (array ("type" => "button", "class" => "button", "onclick" => "if ( confirm ( 'Delete ?' ) ) { set{$this->instancename}action('delete'); {$this->postevent} } "), $this->buttoncaptions[2]);
	}
	
	if ( $this->permissions[3] == 1 ) {
	  $buttons .= input (array ("type" => "button", "class" => "button", "onclick" => "set{$this->instancename}action('pdf'); {$this->postevent} "), $this->buttoncaptions[3]);
	}
	
	if ( $this->permissions[4] == 1 ) {
	  $buttons .= input (array ("type" => "button", "class" => "button", "onclick" => "set{$this->instancename}action('excel'); {$this->postevent} "), $this->buttoncaptions[4]);
	}
    return tr ( td ( array ("class" => "buttons", "colspan" => count ( $this->columns ) + $offset ), $buttons ) );
  }
  
  
  //Return the search information as well as the column headers
  function header () {
	if ( $this->showsearch) {
	  $search = $this->search();
	}
	
	$title = $this->title();
	
	$buttons = $this->buttons();
	
	//make the header
	$ths = array();
	//show check box 
	if ( $this->showcheckbox ) {
	  $ths[] = th ( array ("style" => "text-align: left"),  input ( array ("type" => "checkbox", "name" => "selall".$_REQUEST["instancename"]), 1 )  );
	}
	
	foreach ( $this->columns as $cid => $column ) {
	  $ths[] = th ( array ( "style" => "text-align: {$column["align"]}" ), $column["alias"] );
	}

    $html = shape ( $title, $search, $buttons, tr ( $ths ) );	
    return $html;
  }
  
  //Return the totals on the bottom as well as the navigation for the grid
  function footer () {
  
    $buttons = $this->buttons ();
	//footerheaders
	$tfs = array();
	if ( $this->showfooter ) {
	  
	}
	$html = shape ( $buttons, tr ( $tfs ) );
    
	return $html;
  }
  
  function grid_switch () {
    $html = "";
	
	$selected = array ();
	if (is_array ( $_REQUEST["sel".$this->instancename] ) ) {
	  $selected = $_REQUEST["sel".$this->instancename];		
	}
	  else {
	  $selected[] = $_REQUEST[$this->instancename."selected"]; 
	}  
	
	//Double switch method - this one runs the updates and inserts -
	switch ($_REQUEST["{$this->instancename}action"]) {
	 case "insertpost":
	   //see if we should generate a primary key
	   $genkey = false;
	   
	   foreach ($_REQUEST as $name => $value) {
	     if ( substr( $name, 0, strlen ( $this->inputprefix ) ) == $this->inputprefix ) {
              $colname = strtoupper( substr( $name, strlen ( $this->inputprefix ), strlen( $name ) - strlen ( $this->inputprefix ) ) );
			  
			  //the value must be blank - also we need to get rid of the blank value
			  if (strtoupper (trim($colname)) == strtoupper (trim($this->primarycolumn)) && $value[0] == "") {
			    $genkey = true;
				unset ($_REQUEST[$name]);
 		      }
			  
         } 
	   }
	   
	   unset ($_REQUEST[$this->inputprefix."PK"]);
	   
	   $this->CDE->get_insert_sql( $this->inputprefix, //Field prefix as discussed above 
										$this->tablename, //Name of the tablename
										$this->primarycolumn, //Field name of the primary key
										$genkey, //Generate a new number using inbuilt get_next_id 
										$requestvar = "", //Request variable to populate with new id for post processing
										$this->passwordfields, //Fields that may be crypted automatically
										$this->datefields, //Fields that may be seen as date fields and converted accordingly
										$exec = true,
										$arrayindex = 0 );
										
	   $this->lastrowid = $this->CDE->lastrowid; 
	 break;
	 case "updatepost":
	    //go through each instance of the primary keys and run an update
		$pks = $_REQUEST[$this->inputprefix."PK"];
		unset ($_REQUEST[$this->inputprefix."PK"]);
	    foreach ($pks  as $pid => $pk ) {
		  
		  $this->CDE->get_update_sql( $this->inputprefix, //Field prefix as discussed above 
                                      $this->tablename , //Name of the tablename
                                      $this->primarycolumn, //Field name of the primary key
                                      $pk, //Index 
                                      $requestvar = "", //Request variable to populate with new id for post processing
                                      $this->passwordfields, //Fields that may be crypted automatically
                                      $this->datefields, 
	                                  $exec = true,
									  $pid);  
		  
		  
		}
		
	  break;
	}  
	
	
    switch ($_REQUEST["{$this->instancename}action"]) {
	  case "insert":
	    $selected = array (0);
	    if ($this->customform == "") {
		  $html .= $this->form ( $_REQUEST["{$this->instancename}action"], $selected );		
		}
		  else {
		  $params = array ($this->CDE, $_REQUEST["{$this->instancename}action"], $selected, $this->inputprefix);  
		  $html .= call_user_func_array ($this->customform, $params);  
		}	  
      break;
	  case "update":
	    if ($this->customform == "") {
		  $html .= $this->form ( $_REQUEST["{$this->instancename}action"], $selected );		
		}
		  else {
		  $params = array ($this->CDE, $_REQUEST["{$this->instancename}action"], $selected, $this->inputprefix);  
		  $html .= call_user_func_array ($this->customform, $params);  
		}	  
	  break;
	  case "delete":
	    
		foreach ( $selected as $sid => $svalue ) {
	      $sqldelete = "delete from {$this->tablename} where {$this->primarycolumn} = '".$svalue."'";
		  $error = $this->CDE->exec ( $sqldelete ); 
		}
		
		$this->CDE->commit();
		$html .= script ( "set{$this->instancename}action(''); {$this->postevent} " ); 
	  break;
	  default:
	    //make the grid layout
	    $html .= table ( array ("class" => "tina4grid", "cellpadding" => "0", "cellspacing" => "0"), 
	                   thead ( $this->header () ) , 
					   tbody ( array ( "id" => "data{$this->instancename}") ), 
					   tfoot ( $this->footer () ) );
	    //Script to call javascript or AJAX to load the information into the grid
		$html .= script ( "call_cdeajax ( 'TINA4AJAX', 'data{$this->instancename}', false, { instancename: '{$this->instancename}', columns: '".json_encode ($this->columns)."', passwordhash: '".crypt($_SESSION["TINA4GRIDPASSWORD"])."', tablename: '{$this->tablename}', primarycolumn: '{$this->primarycolumn}', showcheckbox : {$this->showcheckbox} } ); ");
	  break;
	
	}
	
	return $html;
	
  }
  
  function draw () {
    
	$html = "";
	$html .= script ( "var lastid{$this->instancename}; 
	                   var selectedpk{$this->instancename}; 
	      
	
	                  ");
	$html .= $this->CDE->switchid ( "{$this->instancename}action" );
	$html .= $this->CDE->switchid ( "{$this->instancename}selected");
	
	$html .= $this->grid_switch();
	
	return $html;
  }

}
/*
Script Name: W2UI Components
Purpose:

Created By: Admin
Created Date: 2014-03-12 18:08:35
*/
   /*
  WU2i Form

  Example:
  
  
*/
function w2ui_form ($name, $title="My Form", $elements=null, $buttons=null,  $record=null, $style="", $page=0, $action="", $method="post", $savescript="", $noformelement=false) {



 if ($buttons != "") {
    $abuttons[] = div ( array("class" => "w2ui-buttons"), $buttons);
  }
  
  //$name .= rand (1000, 9999);
  if ($record != null) {
    
    $elements[] = textarea ( array("name" => "EDIT".$name, "style" => "display:none"), json_encode ( $record) );
  }
  
  if ($title != "")  {
    $aheader = div ( $title, array ( "class" => "w2ui-form-header" ) );
  }
  
  $pageno = "w2ui-page page-{$page}";
  
  $apage[] =   div ( array ( "class" => $pageno) , $elements		
					 );
	 
  $fields = "";
  
 // print_r ($elements);
  //see if we have an array of just an object, make object into an array for the walking
  if (is_object($elements)) {
    $newelement[] = $elements;
  }
    else {
    $newelement = $elements;
  }
  
  $newelement = array_flatten ($newelement);
  $lookups = array();
  
  
  foreach ($newelement as $eid => $element) {
    
	if ( is_object ($element) ){
		$children = $element->get_children();
		
		
		
				
		foreach ($children as $cid => $child) {
		     
		       if ($child->get_type() == "date" || $child->get_type() == "upload" || $child->get_type() == "float" || $child->get_type() == "money" ||$child->get_type() == "int" || $child->get_type() == "input" || $child->get_type() == "textarea"  || $child->get_type() == "checkbox" || $child->get_type() == "password" || $child->get_type() == "list" || $child->get_type() == "enum" || $child->get_type() == "select") {
					  $input = array();
					  $input["name"] = $child->get_name();
					  $type = $child->get_type();
					  if ($type == "input") $type = "text";
					  $input["type"] = $type; 
					  $items = $child->get_attribute ("items");
					  
					  if($type == "upload"){
					  	 $input["options"]["url"] = $child->get_attribute ("url");
						 $input["options"]["base64"] = true;
						 if($child->get_attribute ("hint")){
						 	$input["options"]["hint"] = $child->get_attribute ("hint");
						 }
					  }
					  if($child->get_type() == "date"){
					  	if($child->get_attribute("start")){
							$input["options"]["start"] = $child->get_attribute("start");
						}
						if($child->get_attribute("format")){
							$input["options"]["format"] = $child->get_attribute("format");
						}
					  }					  
					  if ($items != "") {
					   
						$input["options"]["items"] = "|item{$input["name"]}|";
						$lookups["item{$input["name"]}"] = $items;
						
						if ($type == "list") {			
					      $input["type"] =   "list";
						} else {
						  $input["type"] =   "enum";
						}
					  }
					  
					  
					  $input["required"] = $child->get_required();
					  $fields[] = $input;
					
				}
		
		}
	}
  }
  
  
  if ($fields != "") {
     $fields = "fields:".json_encode ($fields);
	 
	 //print_r ($lookups);
	 foreach ($lookups as $id => $value) {
	   $fields = str_replace ("\"|".$id."|\"", json_encode ($value), $fields); 
	 }
  }
  
 
  $script[] = script ( '$(function () {    if (w2ui["'.$name.'"] == undefined) { $("#'.$name.'").w2form( { id: "'.$name.'", name: "'.$name.'", focus: 0, header: "'.$title.'", '.$fields.' } ) } else {   } } )' );
  if ($record != null) {
    $script[] =  script ( 'w2ui["'.$name.'"].record = $.extend (true, {}, '.json_encode ($record).' );' );
    
  }
  

  $formname = "{$name}";
  $divs = div ( array("id" => $formname, "style" => $style ), $aheader,  $apage, $abuttons, $script );
  
  if ($noformelement) {
    $html .= $divs->compile_html();
  }  else {
    $html .= form (array ("name" => 'form'.$name, "action" => $action, "method" => $method, "enctype" => "multipart/form-data", "onsubmit" => "return false"), $divs);
  }	
  return $html;
}

/*
  WU2i Input
  
  Example:
  
*/
function w2ui_input ($title, $name, $placeholder, $params=null, $type="text", $standalone=false, $extraparams=null) {

   if (!$standalone && $title != "") {
     $elements[] = div (array("class" => "w2ui-label"), $title.":");
   }
   
   switch ($type) {
     case "text":
	 case "email":
	 case "password":
	 case "color":
	   $atype = $type;
	   if ($type == "color") $atype = "text";
       $input = input (array ("id" => $name, "name" => $name, "type" => $atype, "placeholder" => $placeholder), $params);
	 break;
	 case "date":
	 	$input = input (array("id" => $name, "name" => $name, "type" => "text", "placeholder" => $placeholder), $params);
		$input->set_type("date");
	 break;
	 case "enum":
	   $input = input (array ("id" => $name, "name" => $name, "type" => "text", "placeholder" => $placeholder), $params );
	 break;
	 case "list":
	   $input = select (array ("id" => $name, "name" => $name,  "placeholder" => $placeholder, "type" => "list"), $params );
	 break;
	 case "checkbox":
	   $input = input (array ("id" => $name, "name" => $name,  "type" => $type), $params );
	 break;
	 case "textarea":
	   $input = textarea (array ("name" => $name, "placeholder" => $placeholder), $value, $params);
	 break;
	 default:
	   $input = input (array ("id" => $name, "name" => $name, "type" => $type, "placeholder" => $placeholder), $params);
	   $input->set_type ($type);
	 break;
   }
   
   $scripts = array();
   if ($standalone) {
     $items = "";
     if ($type == "enum" || $type == "list") {
	    $items = ", items: ".json_encode ($params["items"]);
	 }
	 
	 $options = "";
	 if ($type == "date") {
	    if ($params["format"] != "") {
	     $options = ", format: '".$params["format"]."'";
	   }
	 }
	 if ($params["value"] != "") {
	   $value = "$('#{$name}').val('".$params["value"]."');";
	 } 
     $script = script ("$(function() {  $('#{$name}').w2field (  {  type: '{$type}'{$options}{$items}} ); {$value}  });");  
	 
	 return $title.$input.$script;
   }
    else {    
     $elements[] = div (array("class" => "w2ui-field"), $input, $scripts);
   }
   return $elements;	
}


/*
  WU2i Grid
  
  Example:
  
*/

function w2ui_grid ($title, $name, $columns, $datapath, $width="100%", $height="100%", $hastoolbar=true, $hasfooter=true, $multiselect=false, $onclick="") {
  
  $show = array();
  if ($hastoolbar) {
    $show["toolbar"] = true;
  }
  if ($hasfooter) {
    $show["footer"] = true;
  }	
  if ($multiselect) {
    $show["selectColumn"] = true;
  }
 
  $currentrecord = 0;
  if ($_REQUEST["sel".$name]) {
    $record = json_decode($_REQUEST["sel".$name]);
	$currentrecord = $record->recid;
  }
  
  $script = '
    $("#'.$name.'").w2destroy("'.$name.'");
    $("#'.$name.'").w2grid ({
	  name: "'.$name.'",
	  header: "'.$title.'",
	  multiSelect: true,
	  style: "height: '.$height.'; width: '.$width.'",
	  ';
	  
  if (count ($show) > 0) {
    $script .= 'show: '.json_encode ( $show ).',';
  }
  
  if (count ($columns) > 0 ) {
    $script .= 'columns: ';
	$gridcolumns = json_encode ( $columns );
	$gridcolumns = str_replace(array('_"', '"_'), '', $gridcolumns);
	$script .= $gridcolumns;
  }
  
  
  $script .= ',
      onClick: function (event) {
					var record = this.get(event.recid);
					console.log(record);
					var sel = this.getSelection();
					document.getElementById ("sel'.$name.'").value = JSON.stringify(record);
					 '.$onclick.' 
	             } ,		 
       onLoad: function (event) {
			event.onComplete = function () {
				// event actions
				this.select ('.$currentrecord.');
				var record = this.get('.$currentrecord.');
				var sel = this.getSelection();
				
				if(record !== undefined){
					document.getElementById ("sel'.$name.'").value = JSON.stringify(record);
					console.log (document.getElementById ("sel'.$name.'").value);
				}
			}
		}
	
	});
	
	w2ui["'.$name.'"].load("'.$datapath.'");
  ';
  $aname = "sel{$name}";
  $html = textarea (array ("id" => $aname, "name" => $aname, "style" => "display: none"), htmlentities($_REQUEST[$aname]) );
  $html .= div (array("id" => $name, 
                          "style" => "width: 100%; height: 350px;"
						 )
				);
  
  $html .= script ( $script );
  
  //mail ('andre@spiceware.co.za', 'Grid Code', $script);
  

  return $html;
}


/*W2UI FIELD FOR GRID =======================================*/
function w2ui_column ($caption, $fieldname="",  $size="", $alignment="left", $sortable=true, $resizable=true, $editable=null, $render=null) {
  $field = array();
  $field["field"] = $fieldname;
  $field["caption"] = $caption;
  if ($size != "") {
    $field["size"] = $size;
  }
  $field["sortable"] = $sortable;
  $field["resizable"] = $resizable;  
  $field["attr"] = "align={$alignment}";
  if($editable != null){
  	$field["editable"] = $editable;
  }
  if($render != null){
  	$field["render"] = "_".$render."_";
  }
  return $field;
}
/*END W2UI FIELD ===================================*/

/*W2UI FIELD FOR GRID =======================================*/
function w2ui_parse_data ($result, $fieldinfo) {
  $data = array();
  $data["total"] = count ($result);
   
  foreach ($result as $rid => $record) {
      $rec = array ();
	  $rec["recid"] = $rid+1;
	  foreach ($fieldinfo as $fid => $field) {
	    
	    $rec[$field["name"]] = htmlentities($record->$field["name"]);     
	  }
	  $data["records"][] = $rec;
	  
  }
  return json_encode ($data);
}
/*END W2UI FIELD ===================================*/


/*W2UI FIELD FOR GRID =======================================*/
function w2ui_popup ($caption, $elements, $width="800px", $height="600px", $showmax=false, $modal=false, $onopen="") {
  
  $params["title"] = $caption;
   
  $body = div ($elements);				
  
  $params["body"] = $body->compile_html();
  $params["width"] = $width;
  $params["height"] = $height;
  if($onopen != ""){
  	$params["onOpen"] = "_function(event){ ".$onopen." }_";
  }
  $params["showMax"] = $showmax;
  $params["modal"] = $modal;
  
  $script = "w2popup.open ( ".json_encode ($params)." );";
  $script = str_replace(array('_"', '"_'), '', $script);
  $html = script ($script);
  return $html;
}

/*END W2UI FIELD ===================================*/

/*W2UI TAB START =======================================*/
function w2ui_tabs ($name="tab", $tabs=array(), $width="100%", $onclick="", $active="", $onclose="") {
  $html  = div ( array("id" => $name, "style" => "width: {$width}" ) );
    
  $atabs = array ();
  
  foreach ($tabs as $tid => $tab) {
     $atab = array();
	 $atab["id"] = $name.$tid;
	 if ($active == "") $active = $atab["id"];
	 if ($active == $tid) $active = $atab["id"];	 
	 $atab["caption"] = $tab;
	 $atabs[] = $atab;
  }
  
  if($onclose != ""){
  	$onclose = ",onClose: function(event){ ".$onclose." }";
  }
  
  $html .= script ( "
                $(function ()  {
				     
					 if (w2ui['{$name}'] !== undefined) w2ui['{$name}'].destroy();
					 $('#{$name}').w2tabs({
					  name: '{$name}',
					  active: '{$active}',
					  tabs: ".json_encode ($atabs).",
					  onClick: function (event) {
					     {$onclick}
					  }
					  {$onclose}
				    });
				  }
				);
               " );
  
  
  return $html;
}

/*END W2UI TAB ===================================*/


/*W2UI NODE START =======================================*/

function w2ui_sidebar_node ($name="", $caption="Default", $img="fa-star", $group=false, $expanded=false, $subnodes=array()) {
  $node = array();
  $node["id"] = $name;
  $node["text"] = $caption;
  $node["group"] = $group;
  if ($group) {
    $node["img"] = $img;
  }
    else {
	$node["icon"] = $img; 
  }	
  $node["expanded"] = $expanded;
  if (count ($subnodes) > 0) {
    $node["nodes"] = $subnodes;
  }
  
  return $node;  
}
/*W2UI NODE END =======================================*/


/*W2UI SIDEBAR START =======================================*/
function w2ui_sidebar ($name="", $width="200px", $height="300px", $nodes, $onclick="") {
   $html = div (array ("id" => $name, "style" => "width: {$width}; height: {$height};"));
   $html .= script (
      " $(function () {
	      $('#{$name}').w2sidebar ({
		     name: '{$name}',
			 nodes: ".json_encode ($nodes)."
          });
          w2ui.{$name}.on ('*', function (event) { 
		    {$onclick}
		  }  
		  );		  
	    });
	  "   
   );  	
   return $html;
}
/*W2UI SIDEBAR END =======================================*/



/*CKEDITOR =======================================*/
function ckeditor ($name, $value="", $onblurevent="") {
  
   $html .= textarea (array("name" => $name, "id" => $name), $value);
   $html .= script ("CKEDITOR.replace('{$name}');
                               function ckeditor_{$name}data(){
							    if(CKEDITOR.instances.{$name}.getData() && document.getElementById('{$name}') !== null){
							        document.getElementById('{$name}').value = CKEDITOR.instances.{$name}.getData();
							    }
								return document.getElementById('{$name}').value;
						  }");
  if ($onblurevent != "") {
    $html .= script ( "CKEDITOR.instances.{$name}.on ('blur', function() { ".str_replace ("\n", " ", $onblurevent)." }); ");
  }
						  
   return $html;
}
/*END CKEDITOR ===================================*/


/*W2UI OPTIONS FOR LIST/ENUM =======================================*/
function w2ui_items ( $CDE, $lookupsql ) {
	$lookuprow = $CDE-> get_row ( $lookupsql , 1 );
	$lookupitems = array("");
	foreach ( $lookuprow as $irow=>$row){
		$lookupitems[$row[0]] = $row[1];
	}
	return $lookupitems;
}

/*END W2UI OPTIONS ===================================*/




?>