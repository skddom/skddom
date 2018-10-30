bindEvent(window, "load", netcat31_window_onload);

function netcat31_window_onload() {
	bindEvent(document.getElementById("mainViewIframe"), "load", (function(ADMIN_PATH){return function() {
		
		// if contentDocument exists, W3C compliant (Mozilla) 
		if(document.getElementById("mainViewIframe").contentDocument) {
			
			var sources = document.getElementById("mainViewIframe").contentDocument.createElement("script");
			sources.charset = "UTF-8";
			sources.type = "text/javascript";
			sources.src = ADMIN_PATH + "js/edit_area/edit_area_full.js";
			document.getElementById("mainViewIframe").contentDocument.body.appendChild(sources);
			
			// init
			sources = document.getElementById("mainViewIframe").contentDocument.createElement("script");
			sources.charset = "UTF-8";
			sources.type = "text/javascript";
			sources.src = ADMIN_PATH + "js/netcat_highlight_iframe.js";
			document.getElementById("mainViewIframe").contentDocument.body.appendChild(sources);
		} else { 
		
			// IE 
			var sources = document.frames["mainViewIframe"].document.createElement("script");
			sources.charset = "UTF-8";
			sources.type = "text/javascript";
			sources.src = ADMIN_PATH + "js/edit_area/edit_area_full.js";
			document.frames["mainViewIframe"].document.body.appendChild(sources);
			
			sources = document.frames["mainViewIframe"].document.createElement("script");
			sources.charset = "UTF-8";
			sources.type = "text/javascript";
			sources.src = ADMIN_PATH + "js/netcat_highlight_iframe.js";
			document.frames["mainViewIframe"].document.body.appendChild(sources);
		}																								   
	};})(this.ADMIN_PATH));
}