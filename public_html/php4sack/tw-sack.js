/* Simple AJAX Code-Kit (SACK) v1.6.1 */
/* ©2005 Gregory Wild-Smith */
/* www.twilightuniverse.com */
/* Software licenced under a modified X11 licence,
   see documentation or authors website for more details 
   
   parseScript + fireEvent modified and added by 
   Andre van Zuydam
   www.spiceware.co.za
*/

function recursive_offset (aobj) {
   var currOffset = {
       x: 0,
       y: 0
   } 
   var newOffset = {
       x: 0,
       y: 0
   }    
   if (aobj !== null) {
       var dontcount = false;
       if (aobj.style !== undefined) {
         if (aobj.style.display == 'none') dontcount = true;
       }  
      
       //currOffset.y += window.pageYOffset;       
 
       if (!dontcount) { 
         //console.log (aobj.style.display); 
         if (aobj.scrollLeft) { 
           currOffset.x += aobj.scrollLeft;
         }
       if (aobj.scrollTop) { 
         currOffset.y += aobj.scrollTop;
       } 
       if (aobj.offsetLeft) { 
         currOffset.x -= aobj.offsetLeft;
       }
       
       if (aobj.offsetTop) { 
         currOffset.y -= aobj.offsetTop;
       }
      } 
         
       if (aobj.parentNode !== undefined) { 
          newOffset = recursive_offset(aobj.parentNode);   
       }
          
       //console.log (currOffset);
       //console.log (newOffset); 
       currOffset.x = (currOffset.x + newOffset.x);
       currOffset.y = (currOffset.y + newOffset.y); 
   }
   return currOffset;
}

function parseScript(_source) {
		var source = _source;
		var scripts = new Array();
		
		// Strip out tags
		while(source.toLowerCase().indexOf("<script") > -1 || source.toLowerCase().indexOf("</script") > -1) {
			var s = source.toLowerCase().indexOf("<script");
			var s_e = source.indexOf(">", s);
			var e = source.toLowerCase().indexOf("</script", s);
			var e_e = source.indexOf(">", e);
			
			// Add to scripts array
			scripts.push(source.substring(s_e+1, e));
			// Strip from source
			source = source.substring(0, s) + source.substring(e_e+1);
		}
		
		// Loop through every script collected and eval it
	  	for(var i=0; i<scripts.length; i++) {
			try {
			  if (scripts[i] != '')
			  { 	    
			    try  {          //IE
			          execScript(scripts[i]);   
          }
          catch(ex)           //Firefox
          {
            window.eval(scripts[i]);
          }   
                
				}  
			}
			catch(e) {
				// do what you want here when a script fails
			 //	window.alert('Script failed to run - '+scripts[i]);
			  if (e instanceof SyntaxError) console.log (e.message+' - '+scripts[i]);
                        }
		}
  	// Return the cleaned source
		return source;
}

function fireEvent(element,event){
    if (document.createEventObject){
        // dispatch for IE
        var evt = document.createEventObject();
        return element.fireEvent('on'+event,evt)
    }
    else{
        // dispatch for firefox + others
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent(event, true, true ); // event type,bubbling,cancelable
        return !element.dispatchEvent(evt);
    }
}

function sack(file) {
	this.xmlhttp = null;

	this.resetData = function() {
		this.method = "POST";
  		this.queryStringSeparator = "?";
		this.argumentSeparator = "&";
		this.URLString = "";
		this.encodeURIString = false;
  		this.execute = false;
  		this.element = null;
		this.elementObj = null;
		this.requestFile = file;
		this.vars = new Object();
		this.responseStatus = new Array(2);
  	};

	this.resetFunctions = function() {
  		this.onLoading = function() { };
  		this.onLoaded = function() { };
  		this.onInteractive = function() { };
  		this.onCompletion = function() { };
  		this.onError = function() { };
		this.onFail = function() { };
	};

	this.reset = function() {
		this.resetFunctions();
		this.resetData();
	};

	this.createAJAX = function() {
		try {
			this.xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e1) {
			try {
				this.xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e2) {
				this.xmlhttp = null;
			}
		}

		if (! this.xmlhttp) {
			if (typeof XMLHttpRequest != "undefined") {
				this.xmlhttp = new XMLHttpRequest();
			} else {
				this.failed = true;
			}
		}
	};

	this.setVar = function(name, value){
		this.vars[name] = Array(value, false);
	};

	this.encVar = function(name, value, returnvars) {
		if (true == returnvars) {
			return Array(encodeURIComponent(name), encodeURIComponent(value));
		} else {
			this.vars[encodeURIComponent(name)] = Array(encodeURIComponent(value), true);
		}
	}

	this.processURLString = function(string, encode) {
		encoded = encodeURIComponent(this.argumentSeparator);
		regexp = new RegExp(this.argumentSeparator + "|" + encoded);
		varArray = string.split(regexp);
		for (i = 0; i < varArray.length; i++){
			urlVars = varArray[i].split("=");
			if (true == encode){
				this.encVar(urlVars[0], urlVars[1]);
			} else {
				this.setVar(urlVars[0], urlVars[1]);
			}
		}
	}

	this.createURLString = function(urlstring) {
		if (this.encodeURIString && this.URLString.length) {
			this.processURLString(this.URLString, true);
		}

		if (urlstring) {
			if (this.URLString.length) {
				this.URLString += this.argumentSeparator + urlstring;
			} else {
				this.URLString = urlstring;
			}
		}

		// prevents caching of URLString
		this.setVar("rndval", new Date().getTime());

		urlstringtemp = new Array();
		for (key in this.vars) {
			if (false == this.vars[key][1] && true == this.encodeURIString) {
				encoded = this.encVar(key, this.vars[key][0], true);
				delete this.vars[key];
				this.vars[encoded[0]] = Array(encoded[1], true);
				key = encoded[0];
			}

			urlstringtemp[urlstringtemp.length] = key + "=" + this.vars[key][0];
		}
		if (urlstring){
			this.URLString += this.argumentSeparator + urlstringtemp.join(this.argumentSeparator);
		} else {
			this.URLString += urlstringtemp.join(this.argumentSeparator);
		}
	}

	this.runResponse = function() {
		eval(this.response);
	}

	this.runAJAX = function(urlstring) {
		if (this.failed) {
			this.onFail();
		} else {
			this.createURLString(urlstring);
			if (this.element) {
				this.elementObj = document.getElementById(this.element);
			}
			if (this.xmlhttp) {
				var self = this;
				
        if (this.method == "GET") {
					totalurlstring = this.requestFile + this.queryStringSeparator + this.URLString;
					this.xmlhttp.open(this.method, totalurlstring, true);
				} else {
				  
          if (this.method)
          {          
				    this.xmlhttp.open(this.method, this.requestFile, true);
				  }
            else
          {
            window.alert ('No method specified for form');
          }    
					try {
						this.xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
					} catch (e) { window.alert('Hello'); }
				}

				this.xmlhttp.onreadystatechange = function() {
					switch (self.xmlhttp.readyState) {
						case 1:
							self.onLoading();
							break;
						case 2:
							self.onLoaded();
							break;
						case 3:
							self.onInteractive();
							break;
						case 4:
							self.response = self.xmlhttp.responseText;
							self.responseXML = self.xmlhttp.responseXML;
							self.responseStatus[0] = self.xmlhttp.status;
							self.responseStatus[1] = self.xmlhttp.statusText;

							if (self.execute) {
								self.runResponse();
							}

							if (self.elementObj) {
								elemNodeName = self.elementObj.nodeName;
								elemNodeName.toLowerCase();
								if (elemNodeName == "input"
								|| elemNodeName == "select"
								|| elemNodeName == "option"
								|| elemNodeName == "textarea") {
									self.elementObj.value = self.response;
								} else {
									self.elementObj.innerHTML =  self.response;
								}
							}
							if (self.responseStatus[0] == "200") {
								self.onCompletion();
								parseScript(self.response);
							} else {
								self.onError();
							}

							self.URLString = "";
							break;
					}
				};

				this.xmlhttp.send(this.URLString);
			}
		}
	};

	this.reset();
	this.createAJAX();
}


//compliments of Felix King, a handler to remove events off properly
var Handler = (function(){
    var i = 1,
        listeners = {};

    return {
        addListener: function(element, event, handler, capture) {
            element.addEventListener(event, handler, capture);
            listeners[i] = {element: element, 
                             event: event, 
                             handler: handler, 
                             capture: capture};
            return i++;
        },
        removeListener: function(id) {
            if(id in listeners) {
                var h = listeners[id];
                h.element.removeEventListener(h.event, h.handler, h.capture);
            }
        }
    };
}());


var droplisteners = [];

function handleDragEnd(e) {
    if (e.preventDefault) {
        e.preventDefault(); // Necessary. Allows us to drop.
    }
    this.classList.remove('over'); // this / e.target is previous target element.
    
    //clean up the handlers
    create_droptargets(null, null, null, true); 
    return true;
}


function handleDragOver(e) {
  if (e.preventDefault) {
        e.preventDefault(); // Necessary. Allows us to drop.
  }
  if (window.event !== undefined) {
    window.event.returnValue = false;
  }
  return false;  
}

function handleDragEnter(e) {
  this.classList.remove('dropped');
  this.classList.add('over');
  if (window.event !== undefined) {
    window.event.returnValue = false;
  }
  
  return false;
}

function handleDragLeave(e) {
  this.classList.remove('dropped');
  this.classList.remove('over'); // this / e.target is previous target element.
}

function getBoxAwareness(obj){
	//console.log(obj);
	var boxModel = {};
		boxModel.topPos = obj.offsetTop;
		boxModel.bottomPos = parseInt(window.getComputedStyle(obj, null).height, 10) + boxModel.topPos;
		boxModel.leftPos = obj.offsetLeft;
		boxModel.rightPos = parseInt(window.getComputedStyle(obj, null).width, 10) + boxModel.leftPos;
	
	return boxModel;
}

function detectOver(aObj, bObj){
	var result = false;
	aObj = getBoxAwareness(aObj);
	bObj = getBoxAwareness(bObj);
	if(aObj.leftPos < bObj.rightPos && 
	   aObj.leftPos > bObj.leftPos && 
	   aObj.topPos < bObj.bottomPos &&
	   aObj.topPos > bObj.topPos){
		result = true;
	}
	return result;
}
var touchstarted = false;
var currentdragmebox = undefined;
var overtarget = undefined;
var touchdrop = false;

function create_droptargets(adiv, afrom, runfunction, remove) {
    if (remove === undefined) remove = false;
      
    //console.log (childnodes);
    var icountdivs = 0;    

    if (remove) {
      //zap all the little buggers
      for (var icount = 0; icount < droplisteners.length; icount++) {
         Handler.removeListener (droplisteners[icount]);      
      }
    }
      else {
      if (adiv != null) {
        childnodes = adiv.childNodes;
      }
      for (var iChild = 0; iChild < childnodes.length; iChild++) {
          if (childnodes[iChild].tagName == 'DIV') {
              icountdivs++;
              droplisteners[droplisteners.length+1] = Handler.addListener(childnodes[iChild], 'dragenter', handleDragEnter, false);
              droplisteners[droplisteners.length+1] = Handler.addListener(childnodes[iChild], 'dragover', handleDragOver, false);
              droplisteners[droplisteners.length+1] = Handler.addListener(childnodes[iChild], 'dragleave', handleDragLeave, false);
                            
              droplisteners[droplisteners.length+1] = Handler.addListener(childnodes[iChild], 'drop', function (e) {     
                  this.classList.add('dropped');
                  
                  if (e.stopPropagation) {
                      e.stopPropagation(); // Stops some browsers from redirecting.
                  }
                  if (e.preventDefault) {
                      e.preventDefault(); // Necessary. Allows us to drop.
                  }
                  try {
                    eval (runfunction+'(e,this,afrom);'); 
                  } catch (e) {
                    console.log ('Error'+e.lineNumber);    
                  }    
                  
              }, false);        
          }
      }
    } 
    
    return childnodes.length;
}

function create_touchtarget(adiv){
	if(currentdragmebox !== undefined){
		for(var iChild = 0; iChild < adiv.childNodes.length; iChild++){
		  if(adiv.childNodes[iChild].tagName == 'DIV'){
   			if(detectOver(currentdragmebox, adiv.childNodes[iChild])){
   				adiv.childNodes[iChild].classList.remove('dropped');
   				adiv.childNodes[iChild].classList.add('over');
   				overtarget = adiv.childNodes[iChild];
   				break;
   			}
   		  	  else {
  		  		adiv.childNodes[iChild].classList.remove('over');
   		  		adiv.childNodes[iChild].classList.remove('dropped');
   		  		overtarget = undefined;
   			}
   		  }     
   		}
    }
    return overtarget;
}

var currtargets = {};
function create_dragevent(dragmebox, targets, runfunction) {
    var dragItems = document.querySelectorAll('div');
    //add an event to start dragging
    currtargets[document.getElementById(dragmebox).id] = targets;
    document.getElementById(dragmebox).setAttribute ('draggable', true);
    document.getElementById(dragmebox).addEventListener('dragend', handleDragEnd, false);
    
    document.getElementById(dragmebox).addEventListener('touchstart', function(event){
      touchstarted = true;
      currentdragmebox = this.cloneNode(true);
      currentdragmebox.style.position = 'absolute';
      document.body.appendChild(currentdragmebox);
    }, false);
    
    document.getElementById(dragmebox).addEventListener('dragstart', function (event) {
      // store the ID of the element, and collect it on the drop later on
      event.dataTransfer.dropEffect = 'all';
      event.dataTransfer.setData('Text', targets);
      
      var atargets = targets.split(',');
          
        for (var i = 0; i < dragItems.length; i++) {
            for (var itarget = 0; itarget < atargets.length; itarget++) {
                if (dragItems[i].id == atargets[itarget].trim()) {
                    create_droptargets(dragItems[i],this, runfunction, false);
                }
            }
        }
    }, false);
    
    document.getElementById(dragmebox).addEventListener('touchmove', function(event){
    	event.preventDefault();
    	var thistarget = undefined;
    	if(touchstarted === true){
    		currentdragmebox.style.left = event.touches[0].clientX+'px';
    		currentdragmebox.style.top = event.touches[0].clientY+'px';
    		var atargets = targets.split(',');
        	for (var i = 0; i < dragItems.length; i++) {
            	for (var itarget = 0; itarget < atargets.length; itarget++) {
                	if (dragItems[i].id == atargets[itarget].trim()) {
                   	  thistarget = create_touchtarget(dragItems[i]);
                	}
            	}
            	if(thistarget != undefined){
            		break;
            	}
        	}
    	}
    }, false);

    document.getElementById(dragmebox).addEventListener('touchend', function (event){
        if(touchstarted === true){
    		currentdragmebox.style.position = 'relative';
    		currentdragmebox.style.left = '';
    		currentdragmebox.style.top = '';
    		document.body.removeChild(currentdragmebox);
    		touchdrop = true;
    		touchstarted = false;
    		if(overtarget !== undefined){
    		  overtarget.classList.add('dropped');
    		  try {
               eval (runfunction+'(event, this, overtarget);'); 
              } catch (event) {
                console.log ('Error '+event.lineNumber);    
              }
              overtarget = undefined;
    		}
    	}
    	currentdragmebox = undefined;
    }, false);
    

    return true;
}
