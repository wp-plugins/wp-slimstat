// If XMLHttpRequest is not defined, we need to create it
if (typeof XMLHttpRequest == "undefined") {
	XMLHttpRequest = function () {
		try { return new ActiveXObject("Msxml2.XMLHTTP.6.0"); }
		catch (e1) {}
		try { return new ActiveXObject("Msxml2.XMLHTTP.3.0"); }
		catch (e2) {}
		try { return new ActiveXObject("Msxml2.XMLHTTP"); }
		catch (e3) {}
		//Microsoft.XMLHTTP points to Msxml2.XMLHTTP.3.0 and is redundant
		throw new Error("This browser does not support XMLHttpRequest.");
	};
}

function slimstat_detect_plugin(substrs) {
	if (navigator.plugins) {
		for (var i = 0; i < navigator.plugins.length; i++) {
			var plugin = navigator.plugins[i];
			var haystack = plugin.name + plugin.description;
			var found = 0;

			for (var j = 0; j < substrs.length; j++) {
				if (haystack.indexOf(substrs[j]) != -1) {
					found++;
				}
			}
			
			if (found == substrs.length) {
				return true;
			}
		}
	}
	return false;
}

// Sends an asynchronous request to the server 
function slimstat_record_event(url){
	var slimstat_request = false;
	try {
		slimstat_request = new XMLHttpRequest();
	} catch (failed) {
		slimstat_request = false;
	}
	if (slimstat_request) {
		slimstat_request.open('GET', url, true);
		slimstat_request.send(null);
	}
}

// This function will be 'attached' to all external links
function slimstat_track_link(event){
	var element;
	if (!event) var event = window.event;
	if (event.target) element = event.target;
	else if (event.srcElement) element = event.srcElement;
	if (element.nodeType == 3) // defeat Safari bug
		element = element.parentNode;

	if (element){
		while (element.tagName != "A") element = element.parentNode;
		document_location = element.href;
		slimstat_info = "?obd=" + element.hostname + "&obr=" + element.pathname;
	}
	else{
		document_location = this.href;
		slimstat_info = "?obd=" + this.hostname + "&obr=" + this.pathname;
	}
	slimstat_info += "&ty=0"; // type=0 stands for outbound link
	slimstat_info += "&id="+slimstat_tid;
	slimstat_info += "&sid="+slimstat_session_id;
	slimstat_info += "&go=n"; // Avoid server-side redirect
	slimstat_url = slimstat_path+'/wp-slimstat-js.php'+slimstat_info;
	
	slimstat_record_event(slimstat_url);
	
	// Wait 300 ms
	var date = new Date(); var curDate = null;
	do { curDate = new Date(); } 
	while(curDate-date < 300); 
}

function ss_te(event, code, load_target){
	// Handle Optional parameters
	if (typeof code == 'undefined' || code == 0) return 0;
	if (typeof load_target == 'undefined') var load_target = true;
	
	slimstat_info = "?ty="+code; 
	slimstat_info += "&id="+slimstat_tid;
	slimstat_info += "&sid="+slimstat_session_id;
	slimstat_info += "&go=n"; // Avoid server-side redirect

	if (load_target){
		if (!event) var event = window.event;
		var element;

		if (event.target)
			element = event.target;
		else if (event.srcElement)
			element = event.srcElement;
		if (element.nodeType == 3) // defeat Safari bug
			element = element.parentNode;

		if (element){
			while (element.tagName != "A")
				element = element.parentNode;
			document_location = element.href;
			if (typeof element.hostname == 'undefined' || typeof element.pathname == 'undefined') return 0;
			slimstat_info += "&obd=" + element.hostname + "&obr=" + element.pathname;
		}
		else{
			document_location = this.href;
			if (typeof this.hostname == 'undefined' || typeof this.pathname == 'undefined') return 0;
			slimstat_info += "&obd=" + this.hostname + "&obr=" + this.pathname;
		}

		// This is necessary to give the browser some time to elaborate the request
		setTimeout('document.location = "' + document_location + '"', 500);
	}
	else{
		slimstat_info += "&obd=" + document.location.hostname + "&obr=" + document.location.pathname;
	}

	slimstat_url = slimstat_path+'/wp-slimstat-js.php'+slimstat_info;

	slimstat_record_event(slimstat_url);
	if (event.preventDefault)
		event.preventDefault();
	else
		event.returnValue = false;
}

// Track Google+1 clicks
function slimstat_plusone(obj){
	if (obj.state == 'off')
		ss_te(obj, 4, false);
	else
		ss_te(obj, 3, false);
}

// Hide the link to WP SlimStat
if (document.getElementById('statsbywpslimstat')) document.getElementById('statsbywpslimstat').style.display = 'none';

// Here we write out the VBScript block for MSIE Windows
var detectableWithVB = false;
if ((navigator.userAgent.indexOf('MSIE') != -1) && (navigator.userAgent.indexOf('Win') != -1)) {
    document.writeln('<scr' + 'ipt language="VBscript">');

    document.writeln('\'do a one-time test for a version of VBScript that can handle this code');
    document.writeln('detectableWithVB = False');
    document.writeln('If ScriptEngineMajorVersion >= 2 then');
    document.writeln('  detectableWithVB = True');
    document.writeln('End If');

    document.writeln('\'this next function will detect most plugins');
    document.writeln('Function detectActiveXControl(activeXControlName)');
    document.writeln('  on error resume next');
    document.writeln('  detectActiveXControl = False');
    document.writeln('  If detectableWithVB Then');
    document.writeln('     detectActiveXControl = IsObject(CreateObject(activeXControlName))');
    document.writeln('  End If');
    document.writeln('End Function');

    document.writeln('</scr' + 'ipt>');
	
	function slimstat_detectActiveXControl(progIds){
		for (var i = 0; i < progIds.length; i++) {
			if (detectActiveXControl(progIds[i])) return true;
		}
		return false;
	}
}

// Attach an event listener to all external links
var links_for_this_page = document.getElementsByTagName("a");
for (i=0;i<links_for_this_page.length;i++) {
	if ( links_for_this_page[i].hostname != location.host && links_for_this_page[i].href.indexOf('http://') >= 0 && links_for_this_page[i].onclick == null ){
		if (links_for_this_page[i].addEventListener) links_for_this_page[i].addEventListener("click", slimstat_track_link, false);
		else if (links_for_this_page[i].attachEvent) links_for_this_page[i].attachEvent("onclick", slimstat_track_link);
	} 
}

// List of plugins WP SlimStat can detect
var slimstat_plugins = {
	java: { substrs: [ "Java" ], progIds: [ "JavaWebStart.isInstalled" ] },
	acrobat: { substrs: [ "Adobe", "Acrobat" ], progIds: [ "AcroPDF.PDF", "PDF.PDFCtrl.5" ] },
	flash: { substrs: [ "Shockwave", "Flash" ], progIds: [ "ShockwaveFlash.ShockwaveFlash" ] },
	director: { substrs: [ "Shockwave", "Director" ], progIds: [ "SWCtl.SWCtl" ] },
	real: { substrs: [ "RealPlayer" ], progIds: [ "rmocx.RealPlayer G2 Control", "RealPlayer.RealPlayer(tm) ActiveX Control (32-bit)", "RealVideo.RealVideo(tm) ActiveX Control (32-bit)" ] },
	mediaplayer: { substrs: [ "Windows Media" ], progIds: [ "WMPlayer.OCX" ] },
	silverlight: { substrs: [ "Silverlight" ], progIds: [ "AgControl.AgControl" ] }
};

// Screen resolution
var slimstat_uniwin = {
	width: window.innerWidth || document.documentElement.clientWidth
		|| document.body.offsetWidth,
	height: window.innerHeight || document.documentElement.clientHeight
		|| document.body.offsetHeight
};

// Gather all the information
slimstat_info = "?sw="+screen.width;
slimstat_info += "&sh="+screen.height;
slimstat_info += "&cd="+screen.colorDepth;
slimstat_info += "&aa="+(screen.fontSmoothingEnabled?'1':'0');
slimstat_info += "&id="+slimstat_tid;
slimstat_info += "&ty=0";
slimstat_info += "&sid="+slimstat_session_id;
slimstat_info += "&bid="+slimstat_blog_id;
slimstat_info += "&pl=";

for (var slimstat_alias in slimstat_plugins) {
	var slimstat_plugin = slimstat_plugins[slimstat_alias];
	if (slimstat_detect_plugin(slimstat_plugin.substrs) ||
		(detectableWithVB && slimstat_detectActiveXControl(slimstat_plugin.progIds)) ){
		slimstat_info += slimstat_alias +"|";
	}
}
slimstat_url = slimstat_path+'/wp-slimstat-js.php'+slimstat_info;
slimstat_record_event(slimstat_url);