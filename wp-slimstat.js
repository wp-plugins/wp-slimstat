function ss_te(e,t,n,r){SlimStat.ss_track(e,t,r)}function ss_track(e,t,n){SlimStat.ss_track(e,t,n)}function slimstat_plusone(e){SlimStat.send_to_server("ty=4&obr="+escape("#google-plus-"+e.state),true)}var SlimStat={_tid:-1,_base64_key_str:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",_plugins:{acrobat:{substrings:["Adobe","Acrobat"],active_x_strings:["AcroPDF.PDF","PDF.PDFCtrl.5"]},director:{substrings:["Shockwave","Director"],active_x_strings:["SWCtl.SWCtl"]},flash:{substrings:["Shockwave","Flash"],active_x_strings:["ShockwaveFlash.ShockwaveFlash"]},java:{substrings:["Java"],active_x_strings:["JavaWebStart.isInstalled"]},mediaplayer:{substrings:["Windows Media"],active_x_strings:["WMPlayer.OCX"]},quicktime:{substrings:["QuickTime"],active_x_strings:["QuickTime.QuickTime"]},real:{substrings:["RealPlayer"],active_x_strings:["rmocx.RealPlayer G2 Control","RealPlayer.RealPlayer(tm) ActiveX Control (32-bit)","RealVideo.RealVideo(tm) ActiveX Control (32-bit)"]},silverlight:{substrings:["Silverlight"],active_x_strings:["AgControl.AgControl"]}},_base64_encode:function(e){var t,n,r,i,s,o,u,a="",f=0;e=SlimStat._utf8_encode(e);while(f<e.length){t=e.charCodeAt(f++);n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=t>>2;s=(t&3)<<4|n>>4;o=(n&15)<<2|r>>6;u=r&63;if(isNaN(n)){o=u=64}else if(isNaN(r)){u=64}a=a+SlimStat._base64_key_str.charAt(i)+SlimStat._base64_key_str.charAt(s)+SlimStat._base64_key_str.charAt(o)+this._base64_key_str.charAt(u)}return a},_utf8_encode:function(e){var t,n,r="";e=e.replace(/\r\n/g,"\n");for(t=0;t<e.length;t++){n=e.charCodeAt(t);if(n<128){r+=String.fromCharCode(n)}else if(n>127&&n<2048){r+=String.fromCharCode(n>>6|192);r+=String.fromCharCode(n&63|128)}else{r+=String.fromCharCode(n>>12|224);r+=String.fromCharCode(n>>6&63|128);r+=String.fromCharCode(n&63|128)}}return r},_detect_single_plugin:function(e){var t,n,r,i,s;try{if(navigator.plugins){for(i in navigator.plugins){n=""+navigator.plugins[i].name+navigator.plugins[i].description;r=0;for(s in SlimStat._plugins[e].substrings){if(n.indexOf(SlimStat._plugins[e].substrings[s])!=-1){r++}}if(r==SlimStat._plugins[e].substrings.length){return true}}}}catch(o){}return false},_detect_single_plugin_ie:function(e){var t;for(t in SlimStat._plugins[e].active_x_strings){if(detect_active_x_control(SlimStat._plugins[e].active_x_strings[t]))return true}return false},detect_plugins:function(){var e,t="";for(e in SlimStat._plugins){if(SlimStat._detect_single_plugin(e)||plugins_detectable_with_vb&&SlimStat._detect_single_plugin_ie(e)){t+=e+"|"}}return t},has_smoothing:function(){if(typeof screen.fontSmoothingEnabled!="undefined"){return Number(screen.fontSmoothingEnabled)}else{try{var e=document.createElement("canvas");e.width="35";e.height="35";e.style.display="none";document.body.appendChild(e);var t=e.getContext("2d");t.textBaseline="top";t.font="32px Arial";t.fillStyle="black";t.strokeStyle="black";t.fillText("O",0,0);for(var n=8;n<=32;n++){for(var r=1;r<=32;r++){var i=t.getImageData(r,n,1,1).data;var s=i[3];if(s!=255&&s!=0)return 1}}return 0}catch(o){return 0}}},send_to_server:function(e,t){if(typeof slimstat_path=="undefined"||typeof slimstat_blog_id=="undefined"||typeof e=="undefined"){return false}if(typeof t=="undefined")var t=true;try{if(window.XMLHttpRequest){request=new XMLHttpRequest}else if(window.ActiveXObject){request=new ActiveXObject("Microsoft.XMLHTTP")}}catch(n){return false}if(request){var r="data="+SlimStat._base64_encode("bid="+slimstat_blog_id+"&is_slimstat=yes&"+e);if(typeof slimstat_tid=="undefined"){request.onreadystatechange=function(){if(request.readyState==4){parsed_tid=parseInt(request.responseText,16);if(!isNaN(parsed_tid)&&parsed_tid>0)SlimStat._tid=request.responseText}}}else SlimStat._tid=slimstat_tid;request.open("POST",slimstat_path+"/wp-slimstat-js.php",t);request.setRequestHeader("Content-type","application/x-www-form-urlencoded");request.send(r);return true}return false},ss_track:function(e,t,n){if(SlimStat._tid<0)return true;if(typeof e=="undefined")var e=window.event;var r=typeof t=="undefined"?0:parseInt(t);var i=[];var s=typeof e.target!="undefined"?e.target:typeof e.srcElement!="undefined"?e.srcElement:false;if(!s)return false;if(s.nodeType==3)s=s.parentNode;var o=false;var u=s.parentNode;var a="";var f=location.pathname;var l="";switch(s.nodeName){case"FORM":if(s.action.length>0)f=escape(s.action);break;case"INPUT":while(typeof u!="undefined"&&u.nodeName!="FORM"&&u.nodeName!="BODY")u=u.parentNode;if(typeof u.action!="undefined"&&u.action.length>0){f=escape(u.action);break};default:if(s.nodeName!="A"){if(typeof s.getAttribute=="function"&&s.getAttribute("id")!="undefined"&&s.getAttribute("id")!=null&&s.getAttribute("id").length>0){f=s.getAttribute("id");break}while(typeof s!="undefined"&&s.nodeName!="A"&&s.nodeName!="BODY")s=s.parentNode}if(typeof s.hash!="undefined"&&s.hash.length>0&&s.hostname==location.hostname){o=true;f=escape(s.hash)}else{a=typeof s.hostname!="undefined"?s.hostname:"";if(typeof s.href!="undefined"){f=escape(s.href)}}if(typeof s.getAttribute=="function"){if(s.getAttribute("title")!="undefined"&&s.getAttribute("title")!=null&&s.getAttribute("title").length>0)i.push("Title:"+s.getAttribute("title"));if(s.getAttribute("id")!="undefined"&&s.getAttribute("id")!=null&&s.getAttribute("id").length>0)i.push("ID:"+s.getAttribute("id"))}}l="&obd="+a+"&obr="+f;var c=-1;var h=-1;if(typeof e.pageX!="undefined"&&typeof e.pageY!="undefined"){c=e.pageX;h=e.pageY}else if(typeof e.clientX!="undefined"&&typeof e.clientY!="undefined"&&typeof document.body.scrollLeft!="undefined"&&typeof document.documentElement.scrollLeft!="undefined"&&typeof document.body.scrollTop!="undefined"&&typeof document.documentElement.scrollTop!="undefined"){c=e.clientX+document.body.scrollLeft+document.documentElement.scrollLeft;h=e.clientY+document.body.scrollTop+document.documentElement.scrollTop}if(c>0&&h>0)l+=(l.length>0?"&":"?")+"po="+c+","+h;i.push("Event:"+e.type);if(typeof n!="undefined"&&n.length>0)i.push(n);if(e.type!="click"&&typeof e.which!="undefined"){if(e.type=="keypress")i.push("Key:"+String.fromCharCode(parseInt(e.which)));else i.push("Type:"+e.which)}SlimStat.send_to_server("id="+SlimStat._tid+"&ty="+r+l+"&no="+escape(i.join(", ")),o);return true}};var plugins_detectable_with_vb=false;if(navigator.userAgent.indexOf("MSIE")!=-1&&navigator.userAgent.indexOf("Win")!=-1){document.writeln("<scr"+'ipt language="VBscript">');document.writeln("'do a one-time test for a version of VBScript that can handle this code");document.writeln("If ScriptEngineMajorVersion >= 2 then");document.writeln("  plugins_detectable_with_vb = True");document.writeln("End If");document.writeln("'this next function will detect most plugins");document.writeln("Function detect_active_x_control(active_x_name)");document.writeln("  on error resume next");document.writeln("  detect_active_x_control = False");document.writeln("  If plugins_detectable_with_vb Then");document.writeln("     detect_active_x_control = IsObject(CreateObject(active_x_name))");document.writeln("  End If");document.writeln("End Function");document.writeln("</scr"+"ipt>")}if(typeof slimstat_disable_outbound_tracking=="undefined"){var links_in_this_page=document.getElementsByTagName("a");for(var i=0;i<links_in_this_page.length;i++){if(links_in_this_page[i].hostname==location.hostname||links_in_this_page[i].href.indexOf("://")==-1||links_in_this_page[i].className.indexOf("noslimstat")!=-1){continue}if(links_in_this_page[i].addEventListener){links_in_this_page[i].addEventListener("click",function(e){return function(t){SlimStat.ss_track(t,0,"A:"+(e+1))}}(i),false)}else if(links_in_this_page[i].attachEvent){links_in_this_page[i].attachEvent("onclick",function(e){return function(t){SlimStat.ss_track(t,0,"A:"+(e+1))}}(i))}}}var current_data="";if(typeof slimstat_tid!="undefined"&&parseInt(slimstat_tid,16)>=0&&typeof slimstat_session_id!="undefined"){current_data="id="+slimstat_tid+"&sid="+slimstat_session_id}else{current_data="ci="+slimstat_ci+"&ref="+SlimStat._base64_encode(document.referrer)+"&res="+SlimStat._base64_encode(window.location.href)}if(current_data!="")SlimStat.send_to_server(current_data+"&sw="+(screen.width||window.innerWidth||document.documentElement.clientWidth||document.body.offsetWidth)+"&sh="+(screen.height||window.innerHeight||document.documentElement.clientHeight||document.body.offsetHeight)+"&cd="+screen.colorDepth+"&aa="+SlimStat.has_smoothing()+"&pl="+SlimStat.detect_plugins())