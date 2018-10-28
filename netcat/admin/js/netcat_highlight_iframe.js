
var atk_aeTextId_input = [
  ["TemplateHeader", 				"html"],
	["TemplateFooter", 				"html"],
  ["ListPrefix", 					  "html"],
  ["ListBody", 					    "html"],
  ["ListSuffix", 					  "html"],
  ["PageBody",					    "html"],
  ["Settings", 					    "php"],
  ["CustomSettings", 				"php"],
  ["AddTemplate", 				  "php"],
  ["AddCond", 					    "php"],
  ["AddActionTemplate", 		"php"],
  ["EditTemplate", 				  "php"],
  ["EditCond", 					    "php"],
  ["EditActionTemplate", 		"php"],
  ["CheckActionTemplate", 	"php"],
  ["DeleteTemplate",        "php"],
  ["DeleteCond",            "php"],
  ["DeleteActionTemplate",  "php"],
  ["FullSearchTemplate", 		"php"],
  ["SearchTemplate", 				"php"],
  ["SubscribeCond", 				"php"],
  ["SendCond",      				"php"],
  ["SubscribeAction", 		  "html"],
  ["Header",          		  "html"],
  ["Record",          		  "html"],
  ["Footer",          		  "html"],
  ["HTMLtext",					    "html"],
  ["Query", 						    "sql"],
  ["filemanager_edit",      "php"]
];


var atk_aeTextName_input = [ 	["Settings",					"php"],
								["f_CSS", 						"css"],
								["CustomSettings",				"php"]		];

var atk_SpecDisp = [ "TemplateHeader"
	,"TemplateFooter"
	,"ListPrefix"
	,"ListBody"
	,"ListSuffix"
	,"PageBody"
  ,"AddTemplate"
	,"AddActionTemplate"
	,"EditTemplate"
	,"EditActionTemplate"
	,"CheckActionTemplate"
	,"DeleteTemplate"
	,"DeleteActionTemplate"
	,"FullSearchTemplate"
	,"SearchTemplate"
	,"SubscribeTemplate"
	];

	Array.prototype.in_array = function(value){
		for (var key in this) {
			if (this[key] == value) { return true; } ;
		}
		return false;
	}

configure_editor();

EAL.prototype.window_loaded();
//EditAreaLoader.prototype.window_loaded();


var aeForm_edit = document.getElementsByTagName("FORM");
for( var i = 0 ; i < aeForm_edit.length ; i++ ){
	aeForm_edit[i].submitFirst = aeForm_edit[i].submit;
	aeForm_edit[i].onsubmit = (function(){return function(){
									for(var i in editAreas) {
										if(window.frames["frame_"+i] && editAreas[i]["displayed"]==true) {
											editAreas[i]["textarea"].value = window.frames["frame_"+i].editArea.textarea.value;
										}
									}
									return true;
								}})();
    // motherfuckers!
	aeForm_edit[i].submit = function(){
									this.onsubmit();
									if (typeof this.edit_area_replaced_submit != 'function' || this.edit_area_replaced_submit()) {
										this.submitFirst();
									}
								};
}

function configure_editor(){
	var aeText_input = document.getElementsByTagName("TEXTAREA");
	for( var i = 0 ; i < aeText_input.length ; i++ ){
		var id 		= aeText_input[i].id;
		var lang	= "php";
		var	conf	= false;
        var specdisp = false;


		if(id) {
			for(var key in atk_aeTextId_input){
				if( atk_aeTextId_input[key][0] === id ){

					lang = atk_aeTextId_input[key][1];
					conf = true;
					specdisp = atk_SpecDisp.in_array(id);
                    break;
				}
			}
		}


		if(!conf) {
			name = aeText_input[i].getAttribute("name");
			if(name) {
				for(var key in atk_aeTextName_input){
					if( atk_aeTextName_input[key][0] === name ){

						lang = atk_aeTextName_input[key][1];
						conf = true;

						for(var j = 0; j <= 32; j++) {
							id += String.fromCharCode( Math.floor(Math.random( ) * 26) + 65 )
						}
						aeText_input[i].id = id;
						break;
					}
				}
			}
		}


		// http://www.cdolivet.net/editarea/
		if(conf) {
			editAreaLoader.init({
				id: id
				,start_highlight: true
        ,smooth_selection: true
				,allow_resize: "y"
				,allow_toggle: true
				,language: (ADMIN_LANG != "ru" ? ADMIN_LANG : ( NC_CHARSET == "utf-8" ? "ru_utf8" : "ru_cp1251"))
				,syntax_selection_allow: "css,html,js,php,sql,xml"
				,syntax: lang
				,toolbar: ( ( id != "Query" ) ? "save, " : "") + "search, go_to_line, |, undo, redo, |, select_font, |, syntax_selection, |, change_smooth_selection, highlight, reset_highlight"
				,is_multi_files: false
				,save_callback: "save_editor_netcat"
				,show_line_colors: true
				,font_size: "9"
				,display: "later"
        ,debug : false
        ,spec_disp : specdisp
			});
		}
	}
}

function save_editor_netcat(id, content) {
	form = document.getElementById(id).form;
	form.onsubmit();
	formAsyncSave(form);
}

/*
var formAsyncSaveOriginal = formAsyncSave;
formAsyncSave = function(form, statusHandlers)
{
	form.onsubmit();
	return formAsyncSaveOriginal(form, statusHandlers);
}*/

function resize_all_editareas() {
  if (typeof editAreas == 'object' ) {
        for(var i in editAreas) {
          if(window.frames["frame_"+i] && editAreas[i]["displayed"]==true) {
            editAreaLoader.toggle(i);
            editAreaLoader.toggle(i);
          }
        }
      }
}