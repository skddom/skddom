<?php

/**
 * Функция перевода русских букв в латинницу
 *
 * @param string $text строка;
 * @param bool $use_url_rules использовать фильтр для URL;
 * @return string строка;
 */
function nc_transliterate($text, $use_url_rules = false) {

    $tr = array("А" => "A", "а" => "a", "Б" => "B", "б" => "b",
            "В" => "V", "в" => "v", "Г" => "G", "г" => "g",
            "Д" => "D", "д" => "d", "Е" => "E", "е" => "e",
            "Ё" => "E", "ё" => "e", "Ж" => "Zh", "ж" => "zh",
            "З" => "Z", "з" => "z", "И" => "I", "и" => "i",
            "Й" => "Y", "й" => "y", "КС" => "X", "кс" => "x",
            "К" => "K", "к" => "k", "Л" => "L", "л" => "l",
            "М" => "M", "м" => "m", "Н" => "N", "н" => "n",
            "О" => "O", "о" => "o", "П" => "P", "п" => "p",
            "Р" => "R", "р" => "r", "С" => "S", "с" => "s",
            "Т" => "T", "т" => "t", "У" => "U", "у" => "u",
            "Ф" => "F", "ф" => "f", "Х" => "H", "х" => "h",
            "Ц" => "Ts", "ц" => "ts", "Ч" => "Ch", "ч" => "ch",
            "Ш" => "Sh", "ш" => "sh", "Щ" => "Sch", "щ" => "sch",
            "Ы" => "Y", "ы" => "y", "Ь" => "'", "ь" => "'",
            "Э" => "E", "э" => "e", "Ъ" => "'", "ъ" => "'",
            "Ю" => "Yu", "ю" => "yu", "Я" => "Ya", "я" => "ya");

    $tr_text = strtr($text, $tr);
    if ($use_url_rules) {
      $tr_text = strtolower(trim($tr_text));
      $tr_text = preg_replace('/[^\w\d\-\ ]/', '', $tr_text);
      $tr_text = str_replace(' ', '-', $tr_text);
      $tr_text = preg_replace('/\-{2,}/', '-', $tr_text);
    }
    return $tr_text;
}

// Include deprecated strings if $NC_DEPRECATED_DISABLED is set to 0
if (isset($NC_DEPRECATED_DISABLED) && $NC_DEPRECATED_DISABLED==0) {
	$deprecated_file = preg_replace('/\.php/', '_old.php', __FILE__);
	if (file_exists($deprecated_file)) {
		include_once $deprecated_file;
	}
}

# MAIN
define("MAIN_DIR", "ltr");
define("MAIN_LANG", "en");
define("MAIN_NAME", "English");
define("MAIN_ENCODING", "UTF-8");
define("MAIN_EMAIL_ENCODING", "ISO-8859-1");
define("NETCAT_RUALPHABET", "а-яА-ЯёЁ");

define("NETCAT_TREE_SITEMAP", "Site map");
define("NETCAT_TREE_MODULES", "Modules");
define("NETCAT_TREE_USERS", "Users");

define("NETCAT_TREE_PLUS_TITLE", "Expand list");
define("NETCAT_TREE_MINUS_TITLE", "Collapse list");

define("NETCAT_TREE_QUICK_SEARCH", "Quick search");

// Tabs
define("NETCAT_TAB_REFRESH", "Refresh");

define("STRUCTURE_TAB_SUBCLASS_ADD", "Add data component");
define("STRUCTURE_TAB_INFO", "Information");
define("STRUCTURE_TAB_SETTINGS", "Settings");
define("STRUCTURE_TAB_USED_SUBCLASSES", "Used components");
define("STRUCTURE_TAB_EDIT", "Edit");
define("STRUCTURE_TAB_PREVIEW", "View");
define("STRUCTURE_TAB_PREVIEW_SITE", "View site");


define("CLASS_TAB_INFO", "Information");
define("CLASS_TAB_EDIT", "Edit");
define("CLASS_TAB_CUSTOM_ACTION", "Custom action");
define("CLASS_TAB_CUSTOM_ADD", "Add");
define("CLASS_TAB_CUSTOM_EDIT", "Edit");
define("CLASS_TAB_CUSTOM_DELETE", "Delete");
define("CLASS_TAB_CUSTOM_SEARCH", "Search");

# BeginHtml
define("BEGINHTML_TITLE", "Administration");
define("BEGINHTML_USER", "User");
define("BEGINHTML_VERSION", "version");
define("BEGINHTML_PERM_GUEST", "guest");
define("BEGINHTML_PERM_DIRECTOR", "director");
define("BEGINHTML_PERM_SUPERVISOR", "supervisor");
define("BEGINHTML_PERM_CATALOGUEADMIN", "site administrator");
define("BEGINHTML_PERM_SUBDIVISIONADMIN", "section administrator");
define("BEGINHTML_PERM_SUBCLASSADMIN", "section content template administrator");
define("BEGINHTML_PERM_CLASSIFICATORADMIN", "classificator administrator");
define("BEGINHTML_PERM_MODERATOR", "moderator");

define("BEGINHTML_LOGOUT", "log out");
define("BEGINHTML_LOGOUT_OK", "Session is closed");
define("BEGINHTML_LOGOUT_RELOGIN", "Re-login");
define("BEGINHTML_LOGOUT_IE", "For end of a session close all Internet browsers.");


define("BEGINHTML_ALARMON", "Unread system messages");
define("BEGINHTML_ALARMOFF", "System messages: no unread");
define("BEGINHTML_ALARMVIEW", "Read system message");
define("BEGINHTML_HELPNOTE", "Help");

# EndHTML
define("ENDHTML_NETCAT", "NetCat");

# Common
define("NETCAT_ADMIN_DELETE_SELECTED", "Delete the selected section");
define("NETCAT_SELECT_SUBCLASS_DESCRIPTION", "There are several &quot;%s&quot; section components in the %s subdivision.<br />
  Select the destination section content template by clicking on its name.<br />");

# INDEX PAGE
define("SECTION_INDEX_SITES_SETTINGS", "Sites settings");
define("SECTION_INDEX_MODULES_MUSTHAVE", "you don't have");
define("SECTION_INDEX_MODULES_DESCRIPTION", "description");
define("SECTION_INDEX_MODULES_TRANSITION", "Transition to older version");
define("DASHBOARD_WIDGET", "Dashboard");
define("DASHBOARD_ADD_WIDGET", "Add widget");
define("DASHBOARD_DEFAULT_WIDGET", "Default widget");
define("DASHBOARD_WIDGET_SYS_NETCAT", "About system");
define("DASHBOARD_WIDGET_MOD_AUTH", "User statistics");
define("DASHBOARD_UPDATES_EXISTS", "Updates exists");
define("DASHBOARD_UPDATES_DONT_EXISTS", "No updates");
define("DASHBOARD_DONT_ACTIVE", "Dont active");
define("DASHBOARD_TODAY", "today");
define("DASHBOARD_YESTERDAY", "yesterday");
define("DASHBOARD_PER_WEEK", "per week");
define("DASHBOARD_WAITING", "waiting");

# MODULES LIST
define("NETCAT_MODULE_DEFAULT", "Developer interface");
define("NETCAT_MODULE_AUTH", "User Interface");
define("NETCAT_MODULE_SEARCH", "Site search");
define("NETCAT_MODULE_SERCH", "Site search (old version)");
define("NETCAT_MODULE_POLL", "Polls");
define("NETCAT_MODULE_ESHOP", "E-Shop (old)");
define("NETCAT_MODULE_STATS", "Statistics");
define("NETCAT_MODULE_SUBSCRIBE", "Subscribe");
define("NETCAT_MODULE_BANNER", "Advertising managment");
define("NETCAT_MODULE_FORUM", "Forum");
define("NETCAT_MODULE_FORUM2", "Forum v2");
define("NETCAT_MODULE_NETSHOP", "E-Shop");
define("NETCAT_MODULE_LINKS", "Link manager");
define("NETCAT_MODULE_CAPTCHA", "CAPTCHA");
define("NETCAT_MODULE_TAGSCLOUD", "Tag Cloud");
define("NETCAT_MODULE_BLOG", "Blog");
define("NETCAT_MODULE_CALENDAR", "Calendar");
define("NETCAT_MODULE_COMMENTS", "Comments");
define("NETCAT_MODULE_LOGGING", "Logging");
define("NETCAT_MODULE_FILEMANAGER", "File manager");
define("NETCAT_MODULE_CACHE", "Cache");
define("NETCAT_MODULE_MINISHOP", "Minishop");
define("NETCAT_MODULE_ROUTING", "Routing");

define("NETCAT_MODULE_NETSHOP_MODULEUNCHECKED", "Module \"NetShop\" not installed");
# /MODULES LIST

define("SECTION_INDEX_USER_STRUCT_CLASSIFICATOR", "Classificators");

define("SECTION_INDEX_USER_RIGHTS_TYPE", "User Roles");
define("SECTION_INDEX_USER_RIGHTS_RIGHTS", "Permissions");

define("SECTION_INDEX_USER_USER_MAIL", "Email users");
define("SECTION_INDEX_USER_SUBSCRIBERS", "User subscribers");

define("SECTION_INDEX_DEV_CLASSES", "Components");
define("SECTION_INDEX_DEV_CLASS_TEMPLATES", "Component templates");
define("SECTION_INDEX_DEV_TEMPLATES", "Design templates");


define("SECTION_INDEX_ADMIN_PATCHES_INFO", "System information");
define("SECTION_INDEX_ADMIN_PATCHES_INFO_VERSION", "System version");
define("SECTION_INDEX_ADMIN_PATCHES_INFO_REDACTION", "System redaction");
define("SECTION_INDEX_ADMIN_PATCHES_INFO_LAST_PATCH", "Last patch number");
define("SECTION_INDEX_ADMIN_PATCHES_INFO_LAST_PATCH_DATE", "Last system patch check");
define("SECTION_INDEX_ADMIN_PATCHES_INFO_CHECK_PATCH", "Check for updates now");

define("SECTION_INDEX_REPORTS_STATS", "General statistics");
define("SECTION_INDEX_REPORTS_SYSTEM", "System messages");



# SECTION CONTROL
define("SECTION_CONTROL_CONTENT_CATALOGUE", "Sites");
define("SECTION_CONTROL_CONTENT_FAVORITES", "Quick edit");
define("SECTION_CONTROL_CONTENT_CLASSIFICATOR", "Classificators");

# SECTION USER
define("SECTION_CONTROL_USER", "Users");
define("SECTION_CONTROL_USER_LIST", "List of users");
define("SECTION_CONTROL_USER_PERMISSIONS", "Users and permissions");
define("SECTION_CONTROL_USER_GROUP", "User groups");
define("SECTION_CONTROL_USER_MAIL", "Email users");

# SECTION CLASS
define("SECTION_CONTROL_CLASS", "Components");
define("CONTROL_CLASS_USE_CAPTCHA", "protect add form with image");
define("CONTROL_CLASS_CACHE_FOR_AUTH", "caching authorization");
define("CONTROL_CLASS_CACHE_FOR_AUTH_NONE", "Not used");
define("CONTROL_CLASS_CACHE_FOR_AUTH_USER", "Each user");
define("CONTROL_CLASS_CACHE_FOR_AUTH_GROUP", "User main group");
define("CONTROL_CLASS_CACHE_FOR_AUTH_DESCRIPTION", "If you need to display unique data to each user in component, this option allows you to select the required conditions.");
define("CLASSIFICATOR_TYPEOFDATA_MULTIFILE", "Multifile upload");
define("CLASSIFICATOR_TYPEOFDATA_NOONE", "no one");
define("CONTROL_CLASS_ADMIN", "Administration");
define("CONTROL_CLASS_CONTROL", "Control");
define("CONTROL_CLASS_FIELDSLIST", "Fields");
define("CONTROL_CLASS_CLASS_GOTOFIELDS", "Go to component fields");
define("CONTROL_CLASS_CLASSFORM_ADDITIONAL_INFO", "Additional information");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SORTNOTE", "Field_name_1[ DESC][, Field_name_2[ DESC]][, ...]<br>DESC - decrease sort");
define("CONTROL_CLASS_CLASS_SHOW_VAR_FUNC_LIST", "Show variables and functions list");
define("CONTROL_CLASS_CLASS_SHOW_VAR_LIST", "Show variables list");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_AUTODEL", "Delete objects in");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_AUTODELEND", "days after addition");
define("CONTROL_CLASS_CLASS_FORMS_YES", "Yes");
define("CONTROL_CLASS_CLASS_FORMS_NO", "No");
define("CONTROL_CLASS_CLASS_FORMS_ADDFORM", "Alternate object addition form");
define("CONTROL_CLASS_CLASS_FORMS_ADDFORM_GEN", "generate form code");
define("CONTROL_CLASS_CLASS_FORMS_ADDRULES", "Object addition conditions");
define("CONTROL_CLASS_CLASS_FORMS_ADDCOND_GEN", "generate condition code");
define("CONTROL_CLASS_CLASS_FORMS_ADDLASTACTION", "Action after addition of object");
define("CONTROL_CLASS_CLASS_FORMS_ADDACTION_GEN", "generate action code");
define("CONTROL_CLASS_CLASS_FORMS_EDITFORM", "Alternate object modification form");
define("CONTROL_CLASS_CLASS_FORMS_EDITFORM_GEN", "generate form code");
define("CONTROL_CLASS_CLASS_FORMS_EDITRULES", "Object modification conditions");
define("CONTROL_CLASS_CLASS_FORMS_EDITCOND_GEN", "generate condition code");
define("CONTROL_CLASS_CLASS_FORMS_EDITLASTACTION", "Action after modification of object");
define("CONTROL_CLASS_CLASS_FORMS_EDITACTION_GEN", "generate action code");
define("CONTROL_CLASS_CLASS_FORMS_ONONACTION", "Action after turning object ON or OFF");
define("CONTROL_CLASS_CLASS_FORMS_CHECKACTION_GEN", "generate action code");
define("CONTROL_CLASS_CLASS_FORMS_DELETEFORM", "Alternate object deleting form");
define("CONTROL_CLASS_CLASS_FORMS_DELETERULES", "Object deleting conditions");
define("CONTROL_CLASS_CLASS_FORMS_ONDELACTION", "Action after deleting object");
define("CONTROL_CLASS_CLASS_FORMS_DELETEACTION_GEN", "generate action code");
define("CONTROL_CLASS_CLASS_FORMS_QSEARCH", "Search form in object list");
define("CONTROL_CLASS_CLASS_FORMS_QSEARCH_GEN", "generate form code");
define("CONTROL_CLASS_CLASS_FORMS_SEARCH", "Search form (individual page)");
define("CONTROL_CLASS_CLASS_FORMS_SEARCH_GEN", "generate form code");
define("CONTROL_CLASS_CLASS_FORMS_MAILRULES", "Subscription conditions");
define("CONTROL_CLASS_CLASS_FORMS_MAILTEXT", "Email template for subscribers");
define("CONTROL_CLASS_CLASS_FORMS_QSEARCH_GEN_WARN", "Field \\\"".CONTROL_CLASS_CLASS_FORMS_QSEARCH."\\\" not empty! Replace old text?");
define("CONTROL_CLASS_CLASS_FORMS_SEARCH_GEN_WARN", "Field \\\"".CONTROL_CLASS_CLASS_FORMS_SEARCH."\\\" not empty! Replace old text?");
define("CONTROL_CLASS_CLASS_FORMS_ADDFORM_GEN_WARN", "Field \\\"".CONTROL_CLASS_CLASS_FORMS_ADDFORM."\\\" not empty! Replace old text?");
define("CONTROL_CLASS_CLASS_FORMS_EDITFORM_GEN_WARN", "Field \\\"".CONTROL_CLASS_CLASS_FORMS_EDITFORM."\\\" not empty! Replace old text?");
define("CONTROL_CLASS_CLASS_FORMS_ADDCOND_GEN_WARN", "Field \\\"".CONTROL_CLASS_CLASS_FORMS_ADDRULES."\\\" not empty! Replace old text?");
define("CONTROL_CLASS_CLASS_FORMS_EDITCOND_GEN_WARN", "Field \\\"".CONTROL_CLASS_CLASS_FORMS_EDITRULES."\\\" not empty! Replace old text?");
define("CONTROL_CLASS_CLASS_FORMS_ADDACTION_GEN_WARN", "Field \\\"".CONTROL_CLASS_CLASS_FORMS_ADDLASTACTION."\\\" not empty! Replace old text?");
define("CONTROL_CLASS_CLASS_FORMS_EDITACTION_GEN_WARN", "Field \\\"".CONTROL_CLASS_CLASS_FORMS_EDITLASTACTION."\\\" not empty! Replace old text?");
define("CONTROL_CLASS_CLASS_FORMS_CHECKACTION_GEN_WARN", "Field \\\"".CONTROL_CLASS_CLASS_FORMS_ONONACTION."\\\" not empty! Replace old text?");
define("CONTROL_CLASS_CLASS_FORMS_DELETEACTION_GEN_WARN", "Field \\\"".CONTROL_CLASS_CLASS_FORMS_ONDELACTION."\\\" not empty! Replace old text?");
define("CONTROL_CLASS_CUSTOM_SETTINGS_ISNOTSET", "There is no view settings template in current component.");
define("CONTROL_CLASS_CUSTOM_SETTINGS_INHERIT_FROM_PARENT", "View settings are defined in the component.");

# SECTION WIDGET
define("WIDGETS", "Widget List");
define("WIDGETS_LIST_IMPORT", "Import");
define("WIDGETS_LIST_ADD", "Add");
define("WIDGETS_PARAMS", "Parameters");
define("SECTION_INDEX_DEV_WIDGET", "Widget-class");
define("CONTROL_WIDGETCLASS_ADD", "Add widget");
define("WIDGET_LIST_NAME", "Name");
define("WIDGET_LIST_CATEGORY", "Category");
define("WIDGET_LIST_ALL", "All");
define("WIDGET_LIST_GO", "Continue");
define("WIDGET_LIST_FIELDS", "Fields");
define("WIDGET_LIST_DELETE", "Delete");
define("WIDGET_LIST_DELETE_WIDGETCLASS", "Widget-class:");
define("WIDGET_LIST_DELETE_WIDGET", "Widget List:");
define("WIDGET_LIST_EDIT", "Edit");
define("WIDGET_LIST_AT", "Action templates");
define("WIDGET_LIST_ADDWIDGET", "Add widget-class");
define("WIDGET_LIST_DELETE_SELECTED", "Delete selected");
define("WIDGET_LIST_ERROR_DELETE", "First, select a widget-class to remove");
define("WIDGET_LIST_INSERT_CODE", "embed");
define("WIDGET_LIST_INSERT_CODE_CLASS", "Embed for template/class");
define("WIDGET_LIST_INSERT_CODE_TEXT", "Embed for text");
define("WIDGET_LIST_LOAD", "Loading...");
define("WIDGET_LIST_PREVIEW", "Preview");
define("WIDGET_LIST_EXPORT", "Export widget-class into file");
define("WIDGET_ADD_CREATENEW", "Create new widget-class &quot;from scratch&quot;");
define("WIDGET_ADD_CONTINUE", "Continue");
define("WIDGET_ADD_CREATENEW_BASICOLD", "Create a new widget-class of the existing");
define("WIDGET_ADD_NAME", "Name");
define("WIDGET_ADD_KEYWORD", "Keyword");
define("WIDGET_ADD_UPDATE", "Update widget list every N minutes (0 - do not update)");
define("WIDGET_ADD_NEWGROUP", "New group");
define("WIDGET_ADD_DESCRIPTION", "Widget-class description");
define("WIDGET_ADD_OBJECTVIEW", "Template view");
define("WIDGET_ADD_PAGEBODY", "Display object");
define("WIDGET_ADD_DOPL", "Extra");
define("WIDGET_ADD_DEVELOP", "In the pipeline");
define("WIDGET_ADD_SYSTEM", "System preferences");
define("WIDGETCLASS_ADD_ADD", "Add widget-class");
define("WIDGET_ADD_ADD", "Add widget");
define("WIDGET_ADD_ERROR_NAME", "Input widget-class name");
define("WIDGET_ADD_ERROR_KEYWORD", "Input keyword");
define("WIDGET_ADD_ERROR_KEYWORD_EXIST", "Keyword must be unique");
define("WIDGET_ADD_WK", "Widget-class");
define("WIDGET_ADD_OK", "Widget successfully added");
define("WIDGET_ADD_DISALLOW", "Disallow embedding into the object");
define("WIDGET_IS_STATIC", "Static content");
define("WIDGET_EDIT_SAVE", "Save");
define("WIDGET_EDIT_OK", "Changes saved");
define("WIDGET_INFO_DESCRIPTION", "Widget-class description");
define("WIDGET_INFO_DESCRIPTION_NONE", "No description available");
define("WIDGET_INFO_INSERT", "Embed for template/class");
define("WIDGET_INFO_INSERT_TEXT", "Embed for text");
define("WIDGET_INFO_PREVIEW", "Preview");
define("WIDGET_DELETE_WARNING", "Warning: widget-class%s will be deleted.");
define("WIDGET_DELETE_CONFIRMDELETE", "Confirm delete");
define("WIDGET_DELETE", "Warning: Widget will be deleted.");
define("WIDGET_ACTION_ADDFORM", "Alternative adding form");
define("WIDGET_ACTION_EDITFORM", "Alternative edit form");
define("WIDGET_ACTION_BEFORE_SAVE", "Before save action");
define("WIDGET_ACTION_AFTER_SAVE", "After save action");
define("WIDGET_IMPORT", "Import");
define("WIDGET_IMPORT_TAB", "Import");
define("WIDGET_IMPORT_CHOICE", "Select the file");
define("WIDGET_IMPORT_ERROR", "File import error");
define("WIDGET_IMPORT_OK", "Widget-class imported successfully");

define("SECTION_CONTROL_WIDGET", "Widget List");
define("SECTION_CONTROL_WIDGETCLASS", "Widget-classes");
define("SECTION_CONTROL_WIDGET_LIST", "Widgets List");
define("CONTROL_WIDGET_ACTIONS_EDIT", "Editing");
define("CONTROL_WIDGET_NONE", "No widgets found");
define("TOOLS_WIDGET", "Widget List");
define("CONTROL_WIDGET_ADD_ACTION", "Adding widget");
define("CONTROL_WIDGETCLASS_ADD_ACTION", "Adding widget-class");
define("SECTION_INDEX_DEV_WIDGETS", "Widget List");
define("CONTROL_WIDGETCLASS_IMPORT", "Widget import");
define("CONTROL_WIDGETCLASS_FILES_PATH", "Files dir <a href='%s'>%s</a>");

define("WIDGET_TAB_INFO", "Information");
define("WIDGET_TAB_EDIT", "Edit widget-class");
define("WIDGET_TAB_CUSTOM_ACTION", "Action templates");
define("NETCAT_REMIND_SAVE_TEXT", "Exit without saving?");
define("NETCAT_REMIND_SAVE_SAVE", "Save");
define("SECTION_SECTIONS_INSTRUMENTS_WIDGETS", "Widget List");

# SECTION TEMPLATE
define("SECTION_CONTROL_TEMPLATE_SHOW", "Templates");

# SECTIONS OPTIONS
define("SECTION_SECTIONS_OPTIONS", "Generel settings");
define("SECTION_SECTIONS_OPTIONS_MODULE_LIST", "Module manager");
define("SECTION_SECTIONS_OPTIONS_WYSIWYG", "WYSIWYG settings");
define("SECTION_SECTIONS_OPTIONS_SYSTEM", "System tables");
define("SECTION_SECTIONS_OPTIONS_SECURITY", "Security");

# SECTIONS OPTIONS
define("SECTION_SECTIONS_INSTRUMENTS_SQL", "Execute SQL-query");
define("SECTION_SECTIONS_INSTRUMENTS_TRASH", "Trash bin");
define("SECTION_SECTIONS_INSTRUMENTS_CRON", "Task manager");
define("SECTION_SECTIONS_INSTRUMENTS_HTML", "WYSIWYG editor");
define("SECTION_SECTIONS_INSTRUMENTS_SITEINFO", "SEO");

# SECTIONS MODDING
define("SECTION_SECTIONS_MODDING_ARHIVES", "Backups");

define("SECTION_SECTIONS_DATA_BACKUP", "Data backup");

# REPORTS
define("SECTION_REPORTS_TOTAL", "General statistics");
define("SECTION_REPORTS_SYSMESSAGES", "System messages");

# SUPPORT

# ABOUT
define("SECTION_ABOUT_TITLE", "About");
define("SECTION_ABOUT_HEADER", "About");
define("SECTION_ABOUT_BODY", "NetCat content management system <font color=%s>%s</font> version %s. All rights reserved.<br><br>\nWeb-site: <a target=_blank href=https://netcat.ru>www.netcat.ru</a><br>\nEmail for support: <a href=mailto:support@netcat.ru>support@netcat.ru</a>\n<br><br>\nDeveloper: &laquo;NetCat&raquo; LLC<br>\nEmail: <a href=mailto:info@netcat.ru>info@netcat.ru</a><br>\n+7 (495) 783-6021<br>\n<a target=_blank href=https://netcat.ru>www.netcat.ru</a><br>");
define("SECTION_ABOUT_DEVELOPER", "Project developer");

// ARRAY-2-FORMS


# INDEX
define("CONTROL_CONTENT_CATALOUGE_SITE", "Sites");
define("CONTROL_CONTENT_CATALOUGE_ONESITE", "Site");
define("CONTROL_CONTENT_CATALOUGE_ADD", "adding");
define("CONTROL_CONTENT_CATALOUGE_SITEDELCONFIRM", "Confirm removal");
define("CONTROL_CONTENT_CATALOUGE_ADDSECTION", "Add section");
define("CONTROL_CONTENT_CATALOUGE_ADDSITE", "Add site");
define("CONTROL_CONTENT_CATALOUGE_SITEOPTIONS", "Site settings");

define("CONTROL_CONTENT_CATALOUGE_ERROR_CASETREE_ONE", "Site name can not be empty!");
define("CONTROL_CONTENT_CATALOUGE_ERROR_DUPLICATE_DOMAIN", "Duplicate site domain. Please change domain name.");
define("CONTROL_CONTENT_CATALOUGE_ERROR_CASETREE_THREE", "Domain name can contain only latin characters, numbers, hyphen or dot! Do not use only numbers.");
define("CONTROL_CONTENT_CATALOUGE_ERROR_DOMAIN_NOT_SET", "Domain name is not specified!");
define("CONTROL_CONTENT_CATALOUGE_ERROR_INCORRECT_DOMAIN", "Incorrect domain name!");
define("CONTROL_CONTENT_CATALOUGE_ERROR_INCORRECT_DOMAIN_FULLTEXT", "Please, check domain name. NetCat must be installed in the root directory of domain!");

define("CONTROL_CONTENT_CATALOUGE_SUCCESS_ADD", "Site successfully added!");
define("CONTROL_CONTENT_CATALOUGE_SUCCESS_EDIT", "Site settings successfully modified!");
define("CONTROL_CONTENT_CATALOUGE_SUCCESS_DELETE", "Site successfully deleted!");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MAININFO", "General Information");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NAME", "Name");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DOMAIN", "Domain");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_CATALOGUEFORM_LANG", "Site language");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MIRRORS", "Mirrors (one per row)");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_OFFLINE", "Show when site is offline");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ROBOTS", "Robots.txt file contents");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ROBOTS_DONT_CHANGE", "Do not change this section");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_TEMPLATE", "Design template");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_TITLEPAGE", "Index section");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NOTFOUND", "&quot;Page not found&quot; section (error 404)");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_TITLEPAGE_PAGE", "Index section");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NOTFOUND_PAGE", "Page not found");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_PRIORITY", "Priority");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ON", "turned on");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_HTTPS_ENABLED", "enable HTTPS");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_LABEL_COLOR", "Label color");

define("CONTROL_CONTENT_SITE_ADD_WITH_CONTENT", "new empty site");
define("CONTROL_CONTENT_SITE_CATEGORY", "Category");
define("CONTROL_CONTENT_SITE_CATEGORY_ANY", "any");
define("CONTROL_CONTENT_SITE_ADD_DATA_ERROR", "Unable to retrieve available sites list");
define("CONTROL_CONTENT_SITE_ADD_PREVIEW", "demo");
define("CONTROL_CONTENT_SITE_ADD_DOWNLOADING", "Downloading and deploying the site, please wait");
define("CONTROL_CONTENT_SITE_ADD_DOWNLOADING_ERROR", "Unable to download the site archive");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE", "Display type");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE_TRADITIONAL", "Traditional");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE_SHORTPAGE", "Shortpage");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE_LONGPAGE_VERTICAL", "Longpage");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE_LONGPAGE_HORIZONTAL", "Longpage horizontal");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ACCESS", "Access");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_USERS", "users");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_VIEW", "view");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_COMMENT", "comment");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_CHANGE", "modify");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SUBSCRIBE", "subscribe");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_EXTFIELDS", "Extra");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SAVE", "Save changes");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_WARNING_SITEDELETE_I", "&amp;s");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_WARNING_SITEDELETE_U", "");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_WARNING_SITEDELETE", "Warning: : site%s will be deleted%s.");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_CONFIRMDELETE", "Confirm removal");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_SETTINGS", "Mobile Settings");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SIMPLE", "Simple site");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE", "Mobile site");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ADAPTIVE", "Adaptive site");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_USE_RESS", "Use RESS technology");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_FOR", "Mobile version for the site");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_FOR_NOTICE", "available only for mobile sites");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_REDIRECT", "Use a forced redirection");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_NONE", "[no]");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_DELETE", "delete");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_CHANGE", "change");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_CRITERION", "Define mobility by: ");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_USERAGENT", "User-agent");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_SCREEN_RESOLUTION", "Screen resolution");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_ALL_CRITERION", "All criteria");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_CREATED", "Creation date");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_UPDATED", "Modification date");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SECTIONSCOUNT", "Subsection amount");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SITESTATUS", "Site status");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_ON", "on");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_OFF", "off");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_ADD", "add");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_USERS", "users");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_READACCESS", "Read access");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_ADDACCESS", "Write access");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_EDITACCESS", "Modify access");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SUBSCRIBEACCESS", "Subscribe access");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_PUBLISHACCESS", "Object publication");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_VIEW", "Browse");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_ADDING", "Write");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SEARCHING", "Search");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SUBSCRIBING", "Subscribe");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_EDIT", "Manage");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_DELETE", "Delete site");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SITE", "Site");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SUBSECTIONS", "Subsections");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_PRIORITY", "Priority");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_GOTO", "Go to");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_DELETE", "Delete");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_LIST", "list");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_TOOPTIONS", "change settings");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SHOW", "view the site");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_EDIT", "edit the content");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_NONE", "No site in project");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_ADDSITE", "Add new site");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SAVE", "Save changes");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_DBERROR", "Database error!");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SECTIONWASCREATED", "Section created: %s<br>");

# CONTROL CONTENT SUBDIVISION
define("CONTROL_CONTENT_SUBDIVISION_FAVORITES_TITLE", "Quick edit");
define("CONTROL_CONTENT_SUBDIVISION_FULL_TITLE", "Site tree");

# CONTROL CONTENT SUBDIVISION
define("CONTROL_CONTENT_SUBDIVISION_INDEX_SITES", "Sites");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_SECTIONS", "Sections");
define("CONTROL_CONTENT_SUBDIVISION_CLASS", "Component in subdivision");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_ADDSECTION", "New section");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_OPTIONSECTION", "Change settings");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_DELETECONFIRMATION", "Confirm removal");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_MOVESECTION", "Move section to");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_ERROR_THREE_NAME", "Enter Name!");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_ERROR_THREE_KEYWORD", "Invalid Keyword!");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_ERROR_THREE_PARENTSUB", "Parent subdivision is not selected!");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_ERROR", "Error adding section");


define("CONTROL_CONTENT_SUBDIVISION_SUCCESS_EDIT", "Section settings were saved.");


define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_CLASSLIST_SECTION", "Section components");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_CLASSLIST_SITE", "Site components");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ADDCLASS", "New component");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_OPTIONSCLASS", "Section component settings");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ADDCLASSSITE", "Add new component to site");

define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_NAME", "Infoblock name can not be empty");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_KEYWORD_INVALID", "The keyword contains forbidden symbols or is too long. It must contain letters, numbers and underscores only. Maximum keyword length is 64 characters.");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_KEYWORD", "Invalid Keyword!");

define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_SUCCESS_ADD", "Infoblock added successfully");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_ADD", "Error adding infoblock");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_SUCCESS_EDIT", "Infoblock successfully changed");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_EDIT", "Error changing infoblock");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_DELETE", "Error deleting infoblock");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_LIST_SUCCESS_EDIT", "Infoblock list successfully changed");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_LIST_ERROR_EDIT", "Error changing infoblock list");

define("CONTROL_CONTENT_SUBDIVISION_FIRST_SUBCLASS", "There are no infoblocks in this section.<br>There should be at least one in order to be able to add information to it.");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_SECTION", "Sections");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_SUBSECTIONS", "Subsections");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_GOTO", "Go to");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_NOONEFAVORITES", "No favorite sections.");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_TOOPTIONS", "change settings");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_TOOPTIONSSUBCLASS", "change component in subdivision");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_TOVIEW", "view the site");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_TOEDIT", "edit the content");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_PRIORITY", "Priority");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_DELETE", "Delete");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_NONE", "none");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LIST", "list");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ADD", "add");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_NOSECTIONS", "Current site has no sections.");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_NOSUBSECTIONS", "Current section has no subsections.");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ADDSECTION", "Add new section");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_CONTINUE", "Continue");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_SELECT_ROOT_SECTION", "Select root for new section");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_SAVE", "Save changes");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ADDFAVOTITES", "show this section in &quot;Favorites&quot;");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_USEEDITDESIGNTEMPLATE", "Use this template when edit objects");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA", "General Information");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_NAME", "Name");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_KEYWORD", "Keyword");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_EXTURL", "External URL");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_LANG", "Section language");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DTEMPLATE", "Design template");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DTEMPLATE_CS", "Custom settings");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DTEMPLATE_EDIT", "Edit template source");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DTEMPLATE_N", "Inherit");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_TURNON", "turn on");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_TURNOFF", "turn off");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_A_ADDSUBSECTION", "add subsection");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_A_REMSITE", "delete site");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_MULTI_SUB_CLASS", "Multiple conponents");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE", "Display type");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE_INHERIT", "Inherit");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE_TRADITIONAL", "Traditional");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE_SHORTPAGE", "Shortpage");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE_LONGPAGE_VERTICAL", "Longpage");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE_LONGPAGE_HORIZONTAL", "Longpage horizontal");

define("CONTROL_TEMPLATE_CUSTOM_SETTINGS_NOT_AVAILABLE", "The selected design template doesn't have additional settings.");
define("CONTROL_TEMPLATE_CUSTOM_SETTINGS", "Section view settings");
define("CONTROL_TEMPLATE_CUSTOM_SETTINGS_ISNOTSET", "There are no additional design template settings in current section.");
define("CONTROL_TEMPLATE_CUSTOM_SETTINGS_INHERITED_FROM_SITE", "Values of the parameters not set here will be inherited
        from the <a href='%s' target='_top'>site template settings</a>.");
define("CONTROL_TEMPLATE_CUSTOM_SETTINGS_INHERITED_FROM_FOLDER", "Values of the parameters not set here will be inherited
        from the <a href='%s' target='_top'>template settings of the &ldquo;%s&rdquo; section</a>.");

define("CONTROL_CUSTOM_SETTINGS_INHERIT", "inherit value from parent section");
define("CONTROL_CUSTOM_SETTINGS_OFF", "no");
define("CONTROL_CUSTOM_SETTINGS_ON", "yes");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_A_EDIT", "edit the content");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_A_KILL", "remove this section");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_A_VIEW", "browse the page");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_MSG_OK", "Section added.");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_A_ADDCLASSTOSECTION", "Add data template to section");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_A_BACKTOSECTIONLIST", "Back to section list");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_ERROR_NOCATALOGUE", "Catalogue does not exist.");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_ERROR_NOSUBDIVISION", "Section does not exist.");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_ERROR_NOSUBCLASS", "Sub class does not exist.");

define("CLASSIFICATOR_COMMENTS_DISABLE", "disable");
define("CLASSIFICATOR_COMMENTS_ENABLE", "enable");
define("CLASSIFICATOR_COMMENTS_NOREPLIED", "enable if not replies");

define("CONTROL_CONTENT_CATALOGUE_FUNCS_COMMENTS", "Comments");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_COMMENTS", "Comments");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_COMMENTS_ADD", "Add comments");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_COMMENTS_AUTHOR_EDIT", "Edit user's own comments");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_COMMENTS_AUTHOR_DELETE", "Delete user's own comments");

define("CONTROL_CONTENT_CATALOGUE_FUNCS_DEMO_MODE", "Demo mode");
define("CONTROL_CONTENT_CATALOGUE_FUNCS_DEMO_MODE_CHECKBOX", "Turn on site demo mode");

define("CONTROL_CONTENT_SUBCLASS_FUNCS_COMMENTS", "Comments");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS", "Access");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INFO_READ", "Read access");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INFO_ADD", "Write access");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INFO_EDIT", "Modify access");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INFO_SUBSCRIBE", "Subscribe access");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INFO_PUBLISH", "Object publication");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INHERIT", "inherit");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_PUBLISH", "Object publishing");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_USERS", "users");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_VIEW", "view");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_READ", "view");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_COMMENT", "comment");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_ADD", "add");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_WRITE", "add");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_EDIT", "edit");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_CHECKED", "checked");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_DELETE", "delete");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_SUBSCRIBE", "subscribe");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_ADVANCEDFIELDS", "Extra");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_OBJ_HOWSHOW", "Display settings");
define("CONTROL_CONTENT_SUBDIVISION_CUSTOM_SETTINGS_TEMPLATE", "Component's visual settings");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_OBJ_YES", "Yes");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_OBJ_NO", "No");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_INFO_UPDATED", "Modification date");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_INFO_CLASS_COUNT", "Component mount");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_INFO_STATUS", "Section status");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_INFO_SUBSECTIONS_COUNT", "Subsection amount");


define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_DELETE", "Delete section");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_ROOT", "Root");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_DELETE_CONFIRMATION", "Confirm removal");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_WARNING", "Warning: site%s will be deleted%s.");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_WARNING_ONE_MANY", "'s");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_WARNING_ONE_ONE", "");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_WARNING_TWO_MANY", "");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_WARNING_TWO_ONE", "");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_ERR_NOONESITE", "Specified site does not exist.");

define("CONTROL_CONTENT_SUBDIVISION_SYSTEM_FIELDS", "System");
define("CONTROL_CONTENT_SUBDIVISION_SYSTEM_FIELDS_NO", "No system fields");

define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_ALWAYS", "Always");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_HOURLY", "Hourly");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_DAILY", "Daily");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_WEEKLY", "Weekly");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_MONTHLY", "Monthly");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_YEARLY", "Yearly");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_NEVER", "Never");

define("CONTROL_CONTENT_SUBDIVISION_SEO_META", "SEO meta tags");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SMO_META", "SMO meta tags");
define("CONTROL_CONTENT_SUBDIVISION_SEO_INDEXING", "Indexing");
define("CONTROL_CONTENT_SUBDIVISION_SEO_CURRENT_VALUE", "Current value");
define("CONTROL_CONTENT_SUBDIVISION_SEO_VALUE_NOT_SETTINGS", "The value of %s on the page is different from what you entered. <a target='_blank' href='https://netcat.ru/developers/docs/seo/title-keywords-and-description/'>More</a>.");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_HEADER", "Last modified headline");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_NONE", "Don&#039;t send");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_YESTERDAY", "Send previous day");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_HOUR", "Send previous hour");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_CURRENT", "Send the current date");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_ACTUAL", "Send the actual date");
define("CONTROL_CONTENT_SUBDIVISION_SEO_DISALLOW_INDEXING", "Prevent indexing");
define("CONTROL_CONTENT_SUBDIVISION_SEO_DISALLOW_INDEXING_YES", "yes");
define("CONTROL_CONTENT_SUBDIVISION_SEO_DISALLOW_INDEXING_NO", "no");
define("CONTROL_CONTENT_SUBDIVISION_SEO_INCLUDE_IN_SITEMAP", "Include section in sitemap");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_PRIORITY", "Sitemap: priority");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ", "Sitemap: Change frequency");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_DELETE_SUCCESS", "Removing success.");

define("CONTROL_CONTENT_CLASS", "Class");
define("CONTROL_CONTENT_SUBCLASS_CLASSNAME", "Block name");
define("CONTROL_CONTENT_SUBCLASS_SHOW_ALL", "show all");
define("CONTROL_CONTENT_SUBCLASS_ONSECTION", "to section");
define("CONTROL_CONTENT_SUBCLASS_ONSITE", "to site");
define("CONTROL_CONTENT_SUBCLASS_MSG_NONE", "Section has no component.");
define("CONTROL_CONTENT_SUBCLASS_DEFAULTACTION", "Default action");
define("CONTROL_CONTENT_SUBCLASS_CREATIONDATE", "Template creation date %s");
define("CONTROL_CONTENT_SUBCLASS_UPDATEDATE", "Template modification update %s");
define("CONTROL_CONTENT_SUBCLASS_TOTALOBJECTS", "Total objects");
define("CONTROL_CONTENT_SUBCLASS_CLASSSTATUS", "Component status");
define("CONTROL_CONTENT_SUBCLASS_CHANGEPREFS", "Change component settings %s");
define("CONTROL_CONTENT_SUBCLASS_DELETECLASS", "Delete component %s");
define("CONTROL_CONTENT_SUBCLASS_ISNAKED", "Do not use design template");
define("CONTROL_CONTENT_SUBCLASS_SRCMIRROR", "Data source");
define("CONTROL_CONTENT_SUBCLASS_SRCMIRROR_NONE", "[no]");
define("CONTROL_CONTENT_SUBCLASS_SRCMIRROR_EDIT", "edit");
define("CONTROL_CONTENT_SUBCLASS_SRCMIRROR_DELETE", "delete");
define("CONTROL_CONTENT_SUBCLASS_TYPE", "Subclass type");
define("CONTROL_CONTENT_SUBCLASS_TYPE_SIMPLE", "simple");
define("CONTROL_CONTENT_SUBCLASS_TYPE_MIRROR", "mirror");
define("CONTROL_CONTENT_SUBCLASS_MIRROR", "Mirror subclass");
define("CONTROL_CONTENT_SUBCLASS_MULTI_TITLE", "Type of displaying");
define("CONTROL_CONTENT_SUBCLASS_MULTI_ONONEPAGE", "display on single page");
define("CONTROL_CONTENT_SUBCLASS_MULTI_ONTABS", "display in tabs");
define("CONTROL_CONTENT_SUBCLASS_MULTI_NONE", "display only first subclass");
define("CONTROL_CONTENT_SUBCLASS_EDIT_IN_PLACE", "This subclass data should be edited here: \"<a href='%s'>%s</a>\"");

define("CONTROL_SETTINGSFILE_TITLE_ADD", "New");
define("CONTROL_SETTINGSFILE_TITLE_EDIT", "Edit");
define("CONTROL_SETTINGSFILE_BASIC_REGCODE", "Registration code");
define("CONTROL_SETTINGSFILE_BASIC_MAIN", "Main info");
define("CONTROL_SETTINGSFILE_BASIC_MAIN_NAME", "Project name");

define("CONTROL_SETTINGSFILE_BASIC_EDIT_TEMPLATE", "Design template used to edit objects");
define("CONTROL_SETTINGSFILE_BASIC_EDIT_TEMPLATE_DEFAULT", "design template assigned to the edited section");

define("CONTROL_SETTINGSFILE_SHOW_EXCURSION", "Show excursion for current user");

define("CONTROL_SETTINGSFILE_BASIC_EMAILS", "Email sender");
define("CONTROL_SETTINGSFILE_BASIC_EMAILS_FILELD", "Field (with email) in user table");
define("CONTROL_SETTINGSFILE_BASIC_EMAILS_FROMNAME", "Sender name");
define("CONTROL_SETTINGSFILE_BASIC_EMAILS_FROMEMAIL", "Sender email");
define("CONTROL_SETTINGSFILE_BASIC_CHANGEDATA", "Change system settings");

define("CONTROL_SETTINGSFILE_BASIC_USE_SMTP", "Use SMTP");
define("CONTROL_SETTINGSFILE_BASIC_USE_SENDMAIL", "Use Sendmail");
define("CONTROL_SETTINGSFILE_BASIC_USE_MAIL", "Use mail() function");
define("CONTROL_SETTINGSFILE_BASIC_MAIL_PARAMETERS", "Additional parameters for sendmail (<code>%s</code> for sender address)");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_HOST", "SMTP host");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_PORT", "Port");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_AUTH_USE", "Use authorization");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_USERNAME", "Username");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_PASSWORD", "Password");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_ENCRYPTION", "Encryption");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_NOENCRYPTION", "None");
define("CONTROL_SETTINGSFILE_BASIC_SENDMAIL_COMMAND", "Sendmail command (example '/usr/sbin/sendmail -bs')");
define("CONTROL_SETTINGSFILE_BASIC_MAIL_TRANSPORT_HEADER", "Transport type");

define("CONTROL_SETTINGSFILE_AUTOSAVE", "\"Drafts\" settings");
define("CONTROL_SETTINGSFILE_AUTOSAVE_USE", "Use \"Drafts\" option");
define("CONTROL_SETTINGSFILE_AUTOSAVE_TYPE_KEYBOARD", "Save on keys above");
define("CONTROL_SETTINGSFILE_AUTOSAVE_TYPE_TIMER", "Save periodically");
define("CONTROL_SETTINGSFILE_AUTOSAVE_PERIOD", "Periodicity, sec");
define("CONTROL_SETTINGSFILE_AUTOSAVE_NO_ACTIVE", "Save while inactivity only");

define("CONTROL_SETTINGSFILE_INLINE_IMAGE_CROP", "Image crop settings");
define("CONTROL_SETTINGSFILE_INLINE_IMAGE_CROP_USE", "Use image crop");
define("CONTROL_SETTINGSFILE_INLINE_IMAGE_CROP_DIMENSIONS", "Predefined dimensions (Width x Height)");

define("CONTROL_SETTINGSFILE_DOCHANGE_ERROR_NAME", "Project name cannot be empty!");

define("NETCAT_AUTH_TYPE_LOGINPASSWORD", "by login");
define("NETCAT_AUTH_TYPE_TOKEN", "by token");
define("CONTROL_AUTH_HTML_CMS", "Content managment system");
define("CONTROL_AUTH_ON_ONE_SITE", "Authorize on site");
define("CONTROL_AUTH_ON_ALL_SITES", "On all sites");
define("CONTROL_AUTH_HTML_LOGIN", "Login");
define("CONTROL_AUTH_HTML_PASSWORD", "Password");
define("CONTROL_AUTH_HTML_PASSWORDCONFIRM", "Password again");
define("CONTROL_AUTH_HTML_SAVELOGIN", "Save login and password");
define("CONTROL_AUTH_HTML_LANG", "Language");
define("CONTROL_AUTH_HTML_AUTH", "Log In");
define("CONTROL_AUTH_HTML_BACK", "Back");
define("CONTROL_AUTH_FIELDS_NOT_EMPTY", "Fields \"".CONTROL_AUTH_HTML_LOGIN."\" and \"".CONTROL_AUTH_HTML_PASSWORD."\" must be not empty!");
define("CONTROL_AUTH_LOGIN_NOT_EMPTY", "Field \"".CONTROL_AUTH_HTML_LOGIN."\" must be not empty!");
define("CONTROL_AUTH_LOGIN_OR_PASSWORD_INCORRECT", "Authorize data incorrect!");
define("CONTROL_AUTH_PIN_INCORRECT", "PIN-code incorrect");
define("CONTROL_AUTH_TOKEN_PLUGIN_DONT_INSTALL", "Plugin doesn't installed");
define("CONTROL_AUTH_KEYPAIR_INCORRECT", "Error");
define("CONTROL_AUTH_USB_TOKEN_NOT_INSERTED", "Token not inserted");
define("CONTROL_AUTH_TOKEN_CURRENT_TOKENS", "Current authorized user tokens");
define("CONTROL_AUTH_TOKEN_NEW", "Authorize new token");
define("CONTROL_AUTH_TOKEN_PLUGIN_ERROR", "<a href='http://www.rutoken.ru/hotline/download/' target='_blank'>Browser plugin</a> for work with token is not installed");
define("CONTROL_AUTH_TOKEN_MISS", "Token missing");
define("CONTROL_AUTH_TOKEN_NEW_BUTTON", "Authorize");

define("CONTROL_AUTH_JS_REQUIRED", "To work in administration system you need to turn on javascript support.");

define("NETCAT_MODULE_AUTH_INSIDE_ADMIN_ACCESS", "Inside admin access");
define("CONTROL_AUTH_MSG_MUSTAUTH", "Enter valid Username and Password.");


define("CONTROL_FS_NAME_SIMPLE", "Simple");
define("CONTROL_FS_NAME_ORIGINAL", "Standard");
define("CONTROL_FS_NAME_PROTECTED", "Protected");

define("CONTROL_CLASS_CLASS_TEMPLATE", "Component mapping template");
define("CONTROL_CLASS_CLASS_TEMPLATE_CHANGE_LATER", "You can change other infoblock settings after subdivision is added.");
define("CONTROL_CLASS_CLASS_TEMPLATE_DEFAULT", "Default template");
define("CONTROL_CLASS_CLASS_TEMPLATE_EDIT_MODE", "Template to use in edit mode");
define("CONTROL_CLASS_CLASS_TEMPLATE_ADMIN_MODE", "Template to use in administrative mode");
define("CONTROL_CLASS_CLASS_TEMPLATE_EDIT_MODE_DONT_USE", "-- do not use custom template --");
define("CONTROL_CLASS_CLASS_TEMPLATE_ADD", "Add component template");
define("CONTROL_CLASS_CLASS_DONT_USE_TEMPLATE", "-- do not use template --");
define("CONTROL_CLASS_CLASS_TEMPLATE_ERROR_NAME", "Enter the name of the template component");
define("CONTROL_CLASS_CLASS_TEMPLATE_ERROR_NOT_FOUND", "Templates component missing");
define("CONTROL_CLASS_CLASS_TEMPLATE_DELETE_WARNING", "Note: instead of templates will be used by the main component \"%s\".");
define("CONTROL_CLASS_CLASS_TEMPLATE_NOT_FOUND", "Template %s not found!");
define("CONTROL_CLASS_CLASS_TEMPLATE_ERROR_ADD", "Error adding template component");
define("CONTROL_CLASS_CLASS_TEMPLATE_ERROR_EDIT", "Error editing a template component");
define("CONTROL_CLASS_CLASS_TEMPLATE_SUCCESS_ADD", "Template component successfully added");
define("CONTROL_CLASS_CLASS_TEMPLATE_SUCCESS_EDIT", "Template component successfully changed");
define("CONTROL_CLASS_CLASS_TEMPLATE_GROUP", "Components templates");
define("CONTROL_CLASS_CLASS_TEMPLATE_BUTTON_EDIT", "Edit");
define("CONTROL_CLASS_CLASS_TEMPLATES", "Component templates");
define("CLASS_TEMPLATE_TAB_EDIT", "Edit template");
define("CLASS_TEMPLATE_TAB_DELETE", "Delete template");
define("CLASS_TEMPLATE_TAB_INFO", "Information");

define("CONTROL_CLASS", "Components");
define("CONTROL_CLASS_ADD_ACTION", "Add component");
define("CONTROL_CLASS_DELETECOMMIT", "Confirm removal of content template");
define("CONTROL_CLASS_DOEDIT", "Edit component");
define("CONTROL_CLASS_CONTINUE", "Continue");
define("CONTROL_CLASS_NONE", "No components.");
define("CONTROL_CLASS_ADD", "New component");
define("CONTROL_CLASS_ADD_FS", "New component 5.0");
define("CONTROL_CLASS_CLASS", "Component");
define("CONTROL_CLASS_SYSTEM_TABLE", "System table");
define("CONTROL_CLASS_ACTIONS", "Component actions");
define("CONTROL_CLASS_FIELD", "Field");
define("CONTROL_CLASS_FIELDS", "Fields");
define("CONTROL_CLASS_FIELDS_COUNT", "Fields");
define("CONTROL_CLASS_CUSTOM", "Custom settings");
define("CONTROL_CLASS_DELETE", "Delete");
define("CONTROL_CLASS_NEWCLASS", "New template");
define("CONTROL_CLASS_TO_FS", "Class to the FS");

define("CONTROL_CLASS_FUNCS_SHOWCLASSLIST_ADDCLASS", "New component");
define("CONTROL_CLASS_FUNCS_SHOWCLASSLIST_IMPORTCLASS", "Import component");

define("CONTROL_CLASS_ACTIONS_VIEW", "view");
define("CONTROL_CLASS_ACTIONS_ADD", "add");
define("CONTROL_CLASS_ACTIONS_EDIT", "modify");
define("CONTROL_CLASS_ACTIONS_CHECKED", "checked in");
define("CONTROL_CLASS_ACTIONS_SEARCH", "search");
define("CONTROL_CLASS_ACTIONS_MAIL", "subscribe");
define("CONTROL_CLASS_ACTIONS_DELETE", "delete");
define("CONTROL_CLASS_ACTIONS_MODERATE", "moderate");
define("CONTROL_CLASS_ACTIONS_ADMIN", "administrate");

define("CONTROL_CLASS_INFO_ADDSLASHES", "Remember to <a href='#' onclick=\"window.open('".$ADMIN_PATH."template/converter.php', 'converter','width=600,height=410,status=no,resizable=yes');\">add slashes to template code</a>.");
define("CONTROL_CLASS_ERRORS_DB", "Database select error!");
define("CONTROL_CLASS_CLASS_NAME", "Name");
define("CONTROL_CLASS_CLASS_KEYWORD", "Keyword");
define("CONTROL_CLASS_CLASS_OBJECT_NAME_SINGULAR", "Object name in singular («add one <em>what</em>?»)");
define("CONTROL_CLASS_CLASS_OBJECT_NAME_PLURAL", "Object name in plural («delete all <em>what</em>?»)");
define("CONTROL_CLASS_CLASS_GROUPS", "Component groups");
define("CONTROL_CLASS_CLASS_NO_GROUP", "Without group");
define("CONTROL_CLASS_CLASS_OBJECTSLIST", "Object list template");
define("CONTROL_CLASS_CLASS_DESCRIPTION", "Component description");
define("CONTROL_CLASS_CLASS_SETTINGS", "Component settings");
define("CONTROL_SCLASS_ACTION", "Template actions");
define("CONTROL_SCLASS_TABLE", "Table");
define("CONTROL_SCLASS_TABLE_NAME", "Table name");
define("CONTROL_SCLASS_LISTING_NAME", "List name");
define("CONTROL_CLASS_CLASSFORM_INFO_FOR_NEWCLASS", "Component's information");
define("CONTROL_CLASS_CLASSFORM_MAININFO", "Main information");
define("CONTROL_CLASS_CLASSFORM_TEMPLATE_PATH", "Files dir <a href='%s'>%s</a>");
define("CONTROL_CLASS_SITE_STYLES", "Styles for use on the site");
define("CONTROL_CLASS_SITE_STYLES_DISABLED_WARNING", "This component works in NetCat 5.6 compatibility mode. Component stylesheets are disabled.");
define("CONTROL_CLASS_SITE_STYLES_ENABLE_BUTTON", "Enable component template stylesheets");
define("CONTROL_CLASS_SITE_STYLES_ENABLE_WARNING",
    "When compatibility mode is off, additional markup (<code>&lt;div&gt;</code> wrapper) 
    will be added to the output of the following blocks:
    <ul><li>object lists from the infoblocks, 
    <li>main part of the object full view page, 
    <li>add, edit and search forms.</ul>
    It is possible that site templates CSS rules may require appropriate modifications
    if the compatibility mode is disabled.");
define("CONTROL_CLASS_SITE_STYLES_DOCS_LINK", "See details <a href='%s' target='_blank'>in the manual</a>.");
define("CONTROL_CLASS_MULTIPLE_MODE_SWITCH", "Component is optimized for use in multiple infoblock view mode");
define("CONTROL_CLASS_TEMPLATE_MULTIPLE_MODE_SWITCH", "Component template is optimized for use in multiple infoblock view mode");
define("CONTROL_CLASS_LIST_PREVIEW", "Object list preview image (.png)");
define("CONTROL_CLASS_LIST_PREVIEW_NONE", "No preview image");

define("CONTROL_CLASS_KEYWORD_ONLY_DIGITS", "Keyword cannot consist of digits only");
define("CONTROL_CLASS_KEYWORD_TOO_LONG", "Keyword cannot be longer than %d characters");
define("CONTROL_CLASS_KEYWORD_INVALID_CHARACTERS", "Keyword can contain only latin letters, digits and underscores");
define("CONTROL_CLASS_KEYWORD_NON_UNIQUE", "Keyword &ldquo;%s&rdquo; is already assigned to the &ldquo;%s&rdquo; component");
define("CONTROL_CLASS_KEYWORD_TEMPLATE_NON_UNIQUE", "Keyword &ldquo;%s&rdquo; is already assigned to the &ldquo;%s&rdquo; template");
define("CONTROL_CLASS_KEYWORD_RESERVED", "Cannot use &ldquo;%s&rdquo; as a keyword, because it is a reserved word");

define("CONTROL_CLASS_CLASSFORM_CHECK_ERROR", "<div style='color:red;'>Error into the component's field &laquo;<i>%s</i>&raquo;.</div>");

define("CONTROL_CLASS_CLASS_OBJECTSLIST_PREFIX", "Object list prefix");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_BODY", "Object in list");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SUFFIX", "Object list suffix");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOW", "List");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ", "objects per page");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOW_NUM", "Objects per page");
define("CONTROL_CLASS_CLASS_MIN_RECORDS", "Minimum number of objects in an infoblock");
define("CONTROL_CLASS_CLASS_MAX_RECORDS", "Maximum number of objects in an infoblock");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SORT", "Sort objects by field(s)");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_TITLE", "Title");

define("CONTROL_CLASS_CLASS_OBJECTSLIST_WRONG_NC_CTPL", "nc_ctpl passed to nc_object_list(%s, %s) is wrong(%s). ");
define("CONTROL_CLASS_CLASS_OBJECTFULL_WRONG_NC_CTPL", "Wrong nc_ctpl - %s. ");

define("CONTROL_CLASS_CLASS_OBJECTVIEW", "Single object at individual page template");

define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_DOPL", "Extra");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_CACHE", "Cache");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_SYSTEM", "Extra settings");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_BR", "replace line breaks with &lt;BR&gt;");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_HTML", "allow HTML tags");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_PAGETITLE", "Page header");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_USEASALT", "Use as alternate title");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_PAGEBODY", "Object template");
define("CONTROL_CLASS_CLASS_CREATENEW_BASICOLD", "Create new component based on extended component");
define("CONTROL_CLASS_CLASS_CREATENEW_CLEARNEW", "Create new component");
define("CONTROL_CLASS_CLASS_DELETE_WARNING", "Warning: component%s will be deleted.");
define("CONTROL_CLASS_CLASS_NOT_FOUND", "Component %s not found!");

define("CONTROL_CLASS_CUSTOM_SETTINGS_TEMPLATE", "View settings component");
define("CONTROL_CLASS_CUSTOM_SETTINGS_PARAMETER", "Parameter");
define("CONTROL_CLASS_CUSTOM_SETTINGS_DEFAULT", "Default value");
define("CONTROL_CLASS_CUSTOM_SETTINGS_VALUE", "Local value");
define("CONTROL_CLASS_CUSTOM_SETTINGS_HAS_ERROR", "There are errors in some fields. Please fix the values and submit the form again.");

define("CONTROL_CLASS_IMPORT", "Import");
define("CONTROL_CLASS_IMPORTS", "Import");
define("CONTROL_CLASS_IMPORT_UPLOAD", "Upload");
define("CONTROL_CLASS_IMPORT_ERROR_NOTUPLOADED", "The file is not uploaded.");
define("CONTROL_CLASS_IMPORT_ERROR_CANNOTBEINSTALLED", "Cannot install component.");
define("CONTROL_CLASS_IMPORT_ERROR_VERSION_ID", "Component version %s, current system version %s.");
define("CONTROL_CLASS_IMPORT_ERROR_NO_VERSION_ID", "System version could not be defined or wrong file format.");
define("CONTROL_CLASS_IMPORT_ERROR_NO_FILES", "Templates file data is missing.");
define("CONTROL_CLASS_IMPORT_ERROR_CLASS_IMPORT", "Error creating component.");
define("CONTROL_CLASS_IMPORT_ERROR_CLASS_TEMPLATE_IMPORT", "Error creating template component.");
define("CONTROL_CLASS_IMPORT_ERROR_MESSAGE_TABLE", "Error creating data table.");
define("CONTROL_CLASS_IMPORT_ERROR_FIELD", "Error creating component fields.");

define("CONTROL_CLASS_CONVERT", "Component conversion");
define("CONTROL_CLASS_CONVERT_BUTTON", "Convert v4 -> v5");
define("CONTROL_CLASS_CONVERT_BUTTON_UNDO", "Undo conversion");
define("CONTROL_CLASS_CONVERT_DB_ERROR", "Error editing component data table");
define("CONTROL_CLASS_CONVERT_OK", "Successful conversion");
define("CONTROL_CLASS_CONVERT_OK_GOEDIT", "Go to component edit screen");
define("CONTROL_CLASS_CONVERT_CLASSLIST_TITLE", "Components to convert");
define("CONTROL_CLASS_CONVERT_CLASSLIST_TITLE_UNDO", "Conversion will be undone for the following components");
define("CONTROL_CLASS_CONVERT_CLASSFOLDERS_TITLE", "Folders containing 5.0 template files, and v4 template backup files(class_40_backup.html) will be created");
define("CONTROL_CLASS_CONVERT_CLASSFOLDERS_TITLE_UNDO", "Folders can be deleted");
define("CONTROL_CLASS_CONVERT_NOTICE", "Syntax errors may appear after component conversion.
    We strongly recommend to temporarily disable your web-site.");
define("CONTROL_CLASS_CONVERT_NOTICE_UNDO", "After the conversion cancellation, component will return to it's initial state. Any changes made in 5.0 mode will be lost!");
define("CONTROL_CLASS_CONVERT_UNDO_FILE_ERROR","No data to restore");

define("CONTROL_CLASS_NEWGROUP", "new group");
define("CONTROL_CLASS_EXPORT", "Export component to file");
define("CONTROL_CLASS_AUXILIARY_SWITCH", "Is an auxiliary (non-content) component");
define("CONTROL_CLASS_AUXILIARY", "(auxiliary)");

define("CONTROL_CLASS_COMPONENT_TEMPLATE_FOR_RSS_DOESNT_EXIST", "Rss-feed %s not available because there is no template component for the rss");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_FOR_XML_DOESNT_EXIST", "Xml-unloading %s not available because there is no template component for xml");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_FOR_TRASH_DOESNT_EXIST", "Trash can not available because there is no template component for trash can");

define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE", "Type");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_CLASSTEMPLATE", "Template component type");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_MULTI_EDIT", "Multiple edit");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_RSS", "RSS");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_XML", "XML-unloading");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_TRASH", "Trash can");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_USEFUL", "Useful");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_INSIDE_ADMIN", "Administrative mode");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_ADMIN_MODE", "Edit mode");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_TITLE", "For title page");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_MOBILE", "Mobile");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_RESPONSIVE", "Responsive");

define("CONTROL_CLASS_COMPONENT_TEMPLATE_BASE_AUTO", "Automatically");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_BASE_EMPTY", "Empty");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_ADD_PARAMETRS", "Component template add parameters");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATE_BASE", "Create component template based on");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATE_FOR_TRASH", "Create template for trash can");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATE_FOR_RSS", "Create template for rss");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATE_FOR_XML", "Create template for xml");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TURN_ON_RSS", "On rss-feed");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TURN_ON_XML", "On xml-unloading");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_VIEW", "View");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_EDIT", "Edit");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_ERROR", "Template can not be created");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_RETURN_TO_SUB", "Return</a> to subdivision settings");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_RETURN_TO_TRASH", "Return</a> to trash bin");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_USEFUL", "Template component successfully created");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_RSS", "Template component for RSS successfully created");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_XML", "Template component for XML successfully created");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_TRASH", "Template component for trash bin successfully created");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_INSIDE_ADMIN", "Template component to edit mode successfully created");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_ADMIN_MODE", "Template component for administrative operations successfully created");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_TITLE", "Template component for title page successfully created");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_MOBILE", "Template for mobile site successfully created");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_MULTI_EDIT", "Multi-edit component template successfully created");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_RESPONSIVE", "Component template for responsive site successfully created");

define("CONTROL_CLASS_DEFAULT_CHANGE", "Default class can be changed in site settings");

define("CONTROL_CONTENT_CLASS_SUCCESS_ADD", "Component added successfully");
define("CONTROL_CONTENT_CLASS_ERROR_ADD", "Error adding component");
define("CONTROL_CONTENT_CLASS_ERROR_NAME", "Enter component Name");
define("CONTROL_CONTENT_CLASS_GROUP_ERROR_NAME", "Group name must not begin from digit");
define("CONTROL_CONTENT_CLASS_SUCCESS_EDIT", "Component successfully changed");
define("CONTROL_CONTENT_CLASS_ERROR_EDIT", "Error changing component");

#TYPE OF DATA
define("CLASSIFICATOR_TYPEOFDATA_STRING", "String");
define("CLASSIFICATOR_TYPEOFDATA_INTEGER", "Integer");
define("CLASSIFICATOR_TYPEOFDATA_TEXTBOX", "Text box");
define("CLASSIFICATOR_TYPEOFDATA_LIST", "Classificator");
define("CLASSIFICATOR_TYPEOFDATA_BOOLEAN", "Boolean (true or false)");
define("CLASSIFICATOR_TYPEOFDATA_FILE", "File");
define("CLASSIFICATOR_TYPEOFDATA_FLOAT", "Float");
define("CLASSIFICATOR_TYPEOFDATA_DATETIME", "Date and time");
define("CLASSIFICATOR_TYPEOFDATA_RELATION", "Relation to other object");
define("CLASSIFICATOR_TYPEOFDATA_MULTILIST", "MultiClassificator");
define("CLASSIFICATOR_TYPEOFDATA_OPENID", "OpenID");

define("CLASSIFICATOR_TYPEOFFILESYSTEM", "Type of filesystem");

define("CLASSIFICATOR_TYPEOFEDIT_ALL", "all users");
define("CLASSIFICATOR_TYPEOFDATA_ADMINS", "administrators");

define("CLASSIFICATOR_TYPEOFMODERATION_RIGHTAWAY", "after addition");
define("CLASSIFICATOR_TYPEOFMODERATION_MODERATION", "after moderation check");

define("CLASSIFICATOR_USERGROUP_ALL", "all");
define("CLASSIFICATOR_USERGROUP_REGISTERED", "registered");
define("CLASSIFICATOR_USERGROUP_AUTHORIZED", "authorized");

define("CONTROL_TEMPLATE_CLASSIFICATOR", "Add slashes");
define("CONTROL_TEMPLATE_CLASSIFICATOR_EKRAN", "Add slashes");
define("CONTROL_TEMPLATE_CLASSIFICATOR_RES", "Result");

define("CONTROL_FIELD_LIST_NAME", "Name");
define("CONTROL_FIELD_LIST_NAMELAT", "Name");
define("CONTROL_FIELD_LIST_DESCRIPTION", "Description");
define("CONTROL_FIELD_LIST_ADD", "New field");
define("CONTROL_FIELD_LIST_CHANGE", "Save changes");
define("CONTROL_FIELD_LIST_DELETE", "Delete field");
define("CONTROL_FIELD_ADDING", "New field");
define("CONTROL_FIELD_EDITING", "Edit field settings");
define("CONTROL_FIELD_DELETING", "Field deleting");
define("CONTROL_FIELD_FIELDS", "Fields");
define("CONTROL_FIELD_LIST_NONE", "No fields.");
define("CONTROL_FIELD_ONE_FORMAT", "Format");
define("CONTROL_FIELD_ONE_FORMAT_NONE", "none");
define("CONTROL_FIELD_ONE_FORMAT_EMAIL", "email");
define("CONTROL_FIELD_ONE_FORMAT_URL", "URL");
define("CONTROL_FIELD_ONE_FORMAT_PASSWORD", "password");
define("CONTROL_FIELD_ONE_FORMAT_PHONE", "phone");
define("CONTROL_FIELD_ONE_FORMAT_TAGS", "tags");
define("CONTROL_FIELD_ONE_PROTECT_EMAIL", "protect on output");
define("CONTROL_FIELD_ONE_EXTENSION", "Connected field");
define("CONTROL_FIELD_ONE_MUSTBE", "cannot be empty");
define("CONTROL_FIELD_ONE_INDEX", "ability to search by this field");
define("CONTROL_FIELD_ONE_IN_TABLE_VIEW", "show in table view mode");
define("CONTROL_FIELD_ONE_INHERITANCE", "inherit field value");
define("CONTROL_FIELD_ONE_DEFAULT", "Default state (sets if field has no value)");
define("CONTROL_FIELD_ONE_DEFAULT_NOTE", "for all types of fields except &quot;".CLASSIFICATOR_TYPEOFDATA_TEXTBOX."&quot;, &quot;".CLASSIFICATOR_TYPEOFDATA_FILE."&quot;, &quot;".CLASSIFICATOR_TYPEOFDATA_DATETIME."&quot;, &quot;".CLASSIFICATOR_TYPEOFDATA_MULTILIST."&quot;");
define("CONTROL_FIELD_ONE_FTYPE", "Type");
define("CONTROL_FIELD_ONE_ACCESS", "Access type");
define("CONTROL_FIELD_ONE_RESERVED", "This field name is reserved!");
define("CONTROL_FIELD_NAME_ERROR", 'Invalid field name!');
define('CONTROL_FIELD_DIGIT_ERROR', 'The field name can not begin with numbers');
define('CONTROL_FIELD_DB_ERROR', 'DB error'); //TODO
define('CONTROL_FIELD_EXITS_ERROR', 'Field already exists');
define('CONTROL_FIELD_FORMAT_ERROR', 'Incorrect field format');
define("CONTROL_FIELD_MSG_ADDED", "Field was added successfully");
define("CONTROL_FIELD_MSG_EDITED", "Field modification was successful");
define("CONTROL_FIELD_MSG_DELETED_ONE", "Field was deleted successfully");
define("CONTROL_FIELD_MSG_DELETED_MANY", "Fields were deleted successfully");
define("CONTROL_FIELD_MSG_CONFIRM_REMOVAL_ONE", "Attention: field will be deleted.");
define("CONTROL_FIELD_MSG_CONFIRM_REMOVAL_MANY", "Attention: fields will be deleted.");
define("CONTROL_FIELD_MSG_FIELDS_CHANGED", "Fields priorities changed.");
define("CONTROL_FIELD_CONFIRM_REMOVAL", "Confirm removal");
define('CONTROL_FIELD__EDITOR_EMBED_TO_FIELD', 'Embed editor into text area field');
define('CONTROL_FIELD__TEXTAREA_SIZE', 'Size of textarea');
define('CONTROL_FIELD_HEIGHT', 'height');
define('CONTROL_FIELD_WIDTH', 'width');
define('CONTROL_FIELD_ATTACHMENT', 'attachment');
define('CONTROL_FIELD_DOWNLOAD_COUNT', 'count the number of downloads');
define('CONTROL_FIELD_PANELS', 'Use CKEditor panels set');
define('CONTROL_FIELD_PANELS_DEFAULT', 'Default');
define('CONTROL_FIELD_TYPO', 'typo');
define('CONTROL_FIELD_TYPO_BUTTON', 'Typo text');
define('CONTROL_FIELD_BBCODE_ENABLED', 'Enable bb-code');
define('CONTROL_FIELD_USE_CALENDAR', 'Using calendar for select date');
define('CONTROL_FIELD_FILE_UPLOADS_LIMITS', 'Your PHP configuration has the following limitations on uploading files:');
define('CONTROL_FIELD_FILE_POSTMAXSIZE', 'max size of post data allowed');
define('CONTROL_FIELD_FILE_UPLOADMAXFILESIZE', 'the maximum size of an uploaded file');
define('CONTROL_FIELD_FILE_MAXFILEUPLOADS', 'the maximum number of files allowed to be uploaded simultaneously');
define('CONTROL_FIELD_MULTIFIELD_USE_IMAGE_RESIZE', 'Use image resize');
define('CONTROL_FIELD_MULTIFIELD_USE_IMAGE_CROP', 'Use image crop');
define('CONTROL_FIELD_MULTIFIELD_CROP_IGNORE', 'Don\t crop if image is smaller than filled size');
define('CONTROL_FIELD_MULTIFIELD_USE_IMAGE_PREVIEW', 'Create image preview');
define('CONTROL_FIELD_MULTIFIELD_USE_PREVIEW_RESIZE', 'Use preview resize');
define('CONTROL_FIELD_MULTIFIELD_PREVIEW_USE_IMAGE_CROP', 'Use preview crop');
define('CONTROL_FIELD_MULTIFIELD_PREVIEW_CROP_IGNORE', 'Don\t crop if preview is smaller than filled size');
define('CONTROL_FIELD_MULTIFIELD_IMAGE_WIDTH', 'Width');
define('CONTROL_FIELD_MULTIFIELD_IMAGE_HEIGHT', 'Height');
define('CONTROL_FIELD_MULTIFIELD_CROP_CENTER', 'By center');
define('CONTROL_FIELD_MULTIFIELD_CROP_COORD', 'By coordinates');
define('CONTROL_FIELD_MULTIFIELD_MIN', 'Min');
define('CONTROL_FIELD_MULTIFIELD_MAX', 'Max');
define('CONTROL_FIELD_MULTIFIELD_MINMAX', 'Limit the number of files available for download');
define('CONTROL_FIELD_USE_TRANSLITERATION', 'Transliteration');
define('CONTROL_FIELD_TRANSLITERATION_FIELD', 'Transliteration result field');
define('CONTROL_FIELD_USE_URL_RULES', 'Use URL syntax rules');
define('CONTROL_FIELD_FILE_WRONG_GD', 'Server not including the extension GD2, resize and cropping images will not work');


# SYS
define("TOOLS_SYSTABLE_SITES", "Sites");
define("TOOLS_SYSTABLE_SECTIONS", "Sections");
define("TOOLS_SYSTABLE_USERS", "Users");
define("TOOLS_SYSTABLE_TEMPLATE", "Templates");


#DATABACKUP
define("TOOLS_DATA_BACKUP",                            "Import/Export data");
define("TOOLS_DATA_BACKUP_IMPORT_FILE",                "Import file (*.tgz)");
define("TOOLS_DATA_BACKUP_IMPORT_COMPLETE",            "Import complete!");
define("TOOLS_DATA_BACKUP_IMPORT_ERROR",               "Import error!");
define("TOOLS_DATA_BACKUP_IMPORT_DUPLICATE_KEY_ERROR", "Duplicate primary keys for table.");
define("TOOLS_DATA_BACKUP_EXPORT_COMPLETE",            "Export complete!");
define("TOOLS_DATA_BACKUP_INCOMPATIBLE_VERSION",       "Import file has incompatible format. Please update your copy of NetCat.");
define("TOOLS_DATA_SAVE_IDS",                          "Save id");
define("TOOLS_DATA_BACKUP_SYSTEM",                     "System");
define("TOOLS_DATA_BACKUP_DATATYPE",                   "Data type");
define("TOOLS_DATA_BACKUP_INSERT_OBJECTS",             "Added database records");
define("TOOLS_DATA_BACKUP_CREATE_TABLES",              "Created database tables");
define("TOOLS_DATA_BACKUP_COPIED_FILES",               "Added files/folders");
define("TOOLS_DATA_BACKUP_SKIPPED_FILES",              "Skipped files/folders");
define("TOOLS_DATA_BACKUP_REPLACED_FILES",             "Replaced files/folders");
define("TOOLS_DATA_BACKUP_EXPORT_DATE",                "Export date");
define("TOOLS_DATA_BACKUP_USED_SPACE",                 "used");
define("TOOLS_DATA_BACKUP_SPACE_FROM",                 "from");

define("TOOLS_DATA_BACKUP_DELETE_ALL_CONFIRMATION", "Delete all files?");

define("TOOLS_EXPORT",                  "Export");
define("TOOLS_IMPORT",                  "Import");
define("TOOLS_DOWNLOAD",                "Download");
define("TOOLS_DATA_BACKUP_GOTO_OBJECT", "Go to imported object");



define("TOOLS_MODULES", "Modules");
define("TOOLS_MODULES_LIST", "Module list");
define("TOOLS_MODULES_INSTALLEDMODULE", "Installed module");
define("TOOLS_MODULES_ERR_INSTALL", "Cannot install");
define("TOOLS_MODULES_ERR_UNINSTALL", "Cannot uninstall");
define("TOOLS_MODULES_ERR_CANTOPEN", "Cannot open a file");
define("TOOLS_MODULES_ERR_PATCH", "Required patch #");
define("TOOLS_MODULES_ERR_VERSION", "Module is not for current version");
define("TOOLS_MODULES_ERR_INSTALLED", "Module already installed");
define("TOOLS_MODULES_ERR_ITEMS", "Error");
define("TOOLS_MODULES_ERR_DURINGINSTALL", "Error processed during installation");
define("TOOLS_MODULES_ERR_NOTUPLOADED", "File not uploaded");
define("TOOLS_MODULES_ERR_EXTRACT", "Error while module archive extracting.Please try to extract archive to $TMP_FOLDER on your server, and run module install once again.<br />");

define("TOOLS_MODULES_MOD_NAME", "Module");
define("TOOLS_MODULES_MOD_PREFS", "Settings");
define("TOOLS_MODULES_MOD_GOINSTALL", "Finish installation");
define("TOOLS_MODULES_MOD_EDIT", "change settings");
define("TOOLS_MODULES_MOD_LOCAL", "Install from local disc");
define("TOOLS_MODULES_MOD_INSTALL", "Module installation");
define("TOOLS_MODULES_MSG_CHOISESECTION", "To complete installation allow system to create necessary sections. Select parent section to create these sections in.");
define("TOOLS_MODULES_PREFS_SAVED", "Module settings saved");
define("TOOLS_MODULES_PREFS_ERROR", "Error while saving module settings");

# PATCH
define("TOOLS_PATCH", "Updates");
define("TOOLS_PATCH_INSTRUCTION_TAB", "Instruction");
define("TOOLS_PATCH_INSTRUCTION", "Update instruction");
define("TOOLS_PATCH_CHEKING", "Check for new patch");
define("TOOLS_PATCH_MSG_OK", "All existent patches are installed");
define("TOOLS_PATCH_MSG_NOCONNECTION", "Unable to connect to the update server. The presence of the new updates can be found on <a href='https://partners.netcat.ru/forclients/update/' target='_blank'> our site </a>");
define("TOOLS_PATCH_ERR_CANTINSTALL", "Cannot install patch");
define("TOOLS_PATCH_INSTALL_LOCAL", "Patch installation");
define("TOOLS_PATCH_INSTALL_ONLINE", "Online installation");
define("TOOLS_PATCH_INFO_NOTINSTALLED", "Patch is not installed");
define("TOOLS_PATCH_INFO_LASTCHECK", "Last system patch check");
define("TOOLS_PATCH_INFO_REFRESH", "check now");
define("TOOLS_PATCH_INFO_DOWNLOAD", "download");
define("TOOLS_PATCH_ERR_EXTRACT", "Error while patch extracting.Please try to extract patch archive to $TMP_FOLDER on your server, and run patch install once again.<br />");
define("TOOLS_PATCH_ERROR_TMP_FOLDER_NOT_WRITABLE", "Set writing permissiions for directory $TMP_FOLDER.<br><small>(%DIR not available for writing)</small>");
define("TOOLS_PATCH_ERROR_FILELIST_NOT_WRITABLE", "Some files for update not writable.");
define("TOOLS_PATCH_ERROR_AUTOINSTALL", "Automatic installation can't be executed, install the update manually.");
define("TOOLS_PATCH_ERROR_UPDATE_SERVER_NOT_AVAILABLE", "Update server not available, try later.<br />" .
    "If the connection to the Internet is only available via a proxy server, " .
    "<a href='{$nc_core->ADMIN_PATH}#system.edit' target='_top'>check its settings</a>.");
define("TOOLS_PATCH_ERROR_UPDATE_FILE_NOT_AVAILABLE", "Update file not available, try later. if the error is repeated, please contact support");
define("TOOLS_PATCH_DOWNLOAD_LINK_DESCRIPTION", "Patch file download link");
define("TOOLS_PATCH_IS_WRITABLE", "Write access");

# patch after install information
define("TOOLS_PATCH_INFO_FILES_COPIED", "[%COUNT] files copied.");
define("TOOLS_PATCH_INFO_QUERIES_EXEC", "[%COUNT] MySQL queries executed.");
define("TOOLS_PATCH_INFO_SYMLINKS_EXEC", "[%COUNT] symlinks created.");

define("TOOLS_PATCH_LIST_DATE", "Installation date");
define("TOOLS_PATCH_ERROR", "Error");
define("TOOLS_PATCH_ERROR_DURINGINSTALL", "Error processed during installation");
define("TOOLS_PATCH_INSTALLED", "Patch installed");
define("TOOLS_PATCH_INVALIDVERSION", "Patch is not for current version. Installed %EXIST, need for patch %REQUIRE.");
define("TOOLS_PATCH_ALREDYINSTALLED", "Patch already installed");

define("TOOLS_PATCH_NOTAVAIL_DEMO", "Patches are not available in the demo version");
define("NETCAT_DEMO_NOTICE", "Content Management System NetCat %s DEMO");
define("NETCAT_PERSONAL_MODULE_DESCRIPTION", "The possibility of connecting additional modules exist only in full version.<br />
                                              To assess the functional which you are missing in a module you can by downloading the version where he presented.<br />
                                              <a href='https://netcat.ru/products/editions/compare/' target='_blank'>Table</a> compare editions. ");

#UPGRADE
define("TOOLS_UPGRADE_ERR_NO_PRODUCTNUMBER", "No production number in system");
define("TOOLS_UPGRADE_ERR_INVALID_PRODUCTNUMBER", "Production number is invalid. Check your license number again");
define("TOOLS_UPGRADE_ERR_NO_MATCH_HOST", "Activation key is invalid. You may be using unlicensed copy of the system");
define("TOOLS_UPGRADE_ERR_NO_ORDER", "There was no order for this license to upgrade system.");
define("TOOLS_UPGRADE_ERR_NOT_PAID", "The order for system upgrade is not paid on netcat.ru.");

#ACTIVATION
define("TOOLS_ACTIVATION", "Activation system");
define("TOOLS_ACTIVATION_INSTRUCTION", 'Activation system instruction');
define("TOOLS_ACTIVATION_VERB", "Activation");
define("TOOLS_ACTIVATION_OK", "Activation was successful");
define("TOOLS_ACTIVATION_OWNER", "License owner");
define("TOOLS_ACTIVATION_LICENSE", "License number");
define("TOOLS_ACTIVATION_CODE", "Activation code");
define("TOOLS_ACTIVATION_ALREADY_ACTIVE", "System is activated");
define("TOOLS_ACTIVATION_INPUT_KEY_CODE", "Enter a registration code and an activation key");
define("TOOLS_ACTIVATION_INVALID_KEY_CODE", "Invalid a registration code or an activation key");
define("TOOLS_ACTIVATION_DAY", "The validity of the demo version expires after %DAY days");
define("TOOLS_ACTIVATION_FORM", "To activate the system you need to enter your registration code and activation key that you will receive after <a href='https://netcat.ru/products/editions/' target='_blank'>purchase</a>");
define("TOOLS_ACTIVATION_DESC", "In full version:
<ul>
<li> open source;</li>
<li> Unlimited period of licence validity;</li>
<li> To add necessary functionality, you can upgrade your redaction;</li>
<li> Automatic installation of updates;</li>
<li> Annual free online support.</li>
</ul>");
//define("TOOLS_ACTIVATION_DEMO_DISABLED", "Ability to update only exists in full version .<br />");
define("TOOLS_ACTIVATION_REMIND_UNCOMPLETED", "Complete activation process &laquo;<a href='%s'>Activation system</a>&raquo;.");

define("TOOLS_ACTIVATION_FORM_ERR_MANDATORY", "Please fill in all required fields");
define("TOOLS_ACTIVATION_FORM_ERR_ORG_EMAIL", "The format of the email address is incorrect");
define("TOOLS_ACTIVATION_FORM_ERR_PERSON_EMAIL", "The format of the email address is incorrect");
define("TOOLS_ACTIVATION_FORM_ERR_PRIMARY_EMAIL", "The format of the email address is incorrect");
define("TOOLS_ACTIVATION_FORM_ERR_ADDIT_EMAIL", "The format of the additional email address is incorrect");
define("TOOLS_ACTIVATION_FORM_ERR_INN", "ITN should contain 10 or 12 digits");

define("TOOLS_ACTIVATION_PLEASE_CHECK", "<p>Attention! Please, check all the data carefully. According to the License agreement, license owner data couldn't be changed later.</p>
Fill in the following fields:<br />");
define("TOOLS_ACTIVATION_FLD_OWNER", "License owner");
define("TOOLS_ACTIVATION_FLD_PHIS", "Individual");
define("TOOLS_ACTIVATION_FLD_UR", "Legal person");
define("TOOLS_ACTIVATION_FLD_NAME", "Full name");
define("TOOLS_ACTIVATION_FLD_CITY", "City");
define("TOOLS_ACTIVATION_FLD_PHIS_PHONE", "Phone");
define("TOOLS_ACTIVATION_FLD_PRIMARY_EMAIL", "Email");
define("TOOLS_ACTIVATION_FLD_ADDIT_EMAIL", "Additional email");
define("TOOLS_ACTIVATION_FLD_ORGANIZATION", "Organization");
define("TOOLS_ACTIVATION_FLD_INN", "INN");
define("TOOLS_ACTIVATION_FLD_ADDRESS", "Address");
define("TOOLS_ACTIVATION_FLD_ORG_EMAIL", "Email");
define("TOOLS_ACTIVATION_FLD_PHONE", "Phone");
define("TOOLS_ACTIVATION_FLD_CEO", "CEO name");
define("TOOLS_ACTIVATION_FLD_PERSON", "Сontact name");
define("TOOLS_ACTIVATION_FLD_PERSON_EMAIL", "Contact email");
define("TOOLS_ACTIVATION_FLD_PERSON_PHONE", "Contact phone");

define("REPORTS", "General statistics");
define("REPORTS_SECTIONS", "%d section(s) (turned off: %d)");
define("REPORTS_USERS", "%d user(s) (turned off: %d)");
define("REPORTS_LAST_NAME", "Name");
define("REPORTS_CLASS", "Component statistics");
define("REPORTS_STAT_CLASS_CLEAR", "Clear");
define("REPORTS_STAT_CLASS_CLEARED", "Object deleted");
define("REPORTS_STAT_CLASS_CONFIRM", "todo");
define("REPORTS_STAT_CLASS_CONFIRM_OK", "Next");
define("REPORTS_STAT_CLASS_NOT_CC", "todo");
define("REPORTS_STAT_CLASS_DOGET", "Perform selection");
define("REPORTS_STAT_CLASS_USE", "Used");
define("REPORTS_STAT_CLASS_NOTUSE", "Unused");

define("REPORTS_SYSMSG_MSG", "Message");
define("REPORTS_SYSMSG_DATE", "Date");
define("REPORTS_SYSMSG_NONE", "No system messages. ");
define("REPORTS_SYSMSG_MARK", "Mark as read");
define("REPORTS_SYSMSG_TOTAL", "Total");
define("REPORTS_SYSMSG_BACK", "Back to the list");

define("SUPPORT", "Contact developer");
define("SUPPORT_HELP_MESSAGE", "
Technical support is available only to registered NetCat users.<br />
To get help from the system developer you should:
<ol>
 <li style='padding-bottom:10px'><a target=_blank href='https://netcat.ru/forclients/copies/'>Register your NetCat copy</a>.
 <li style='padding-bottom:10px'>Since data you've entered had been verified, you'll be able to
   open and track your questions in our <a target=_blank href='https://netcat.ru/forclients/helpdesk/'>helpdesk</a>.
 </li>
</ol>
");

define("TOOLS_SQL", "SQL command line");
define("TOOLS_SQL_ERR_NOQUERY", "Enter a query!");
define("TOOLS_SQL_SEND", "Query");
define("TOOLS_SQL_OK", "Query result");
define("TOOLS_SQL_TOTROWS", "Rows of query correspond");
define("TOOLS_SQL_HELP", "Query example");
define("TOOLS_SQL_HISTORY", "Last 15 queries");
define("TOOLS_SQL_HELP_EXPLAIN", "showing field list of table %s");
define("TOOLS_SQL_HELP_SELECT", "showing a row number in table %s");
define("TOOLS_SQL_HELP_SHOW", "showing a table list");
define("TOOLS_SQL_HELP_DOCS", "For more information check MySQL documentation at URL:<br>\n<a target=_blank href=http://dev.mysql.com/doc/mysql/en/>http://dev.mysql.com/doc/mysql/en/</a>");
define("TOOLS_SQL_BENCHMARK", "Query execution time");
define("TOOLS_SQL_ERR_OPEN_FILE", "Can't open sql-file: %s");
define("TOOLS_SQL_ERR_FILE_QUERY", "MySQL query error into the file %s, error: %s");

define("NETCAT_TRASH_SIZEINFO", "Size of trash bin - %s. <br />Limit - %s РњР‘.");
define("NETCAT_TRASH_NOMESSAGES", "Trash bin is empty");
define("NETCAT_TRASH_MESSAGES_SK1", "object");
define("NETCAT_TRASH_MESSAGES_SK2", "objects");
define("NETCAT_TRASH_MESSAGES_SK3", "objects");
define("NETCAT_TRASH_RECOVERED_SK1", "Recovered");
define("NETCAT_TRASH_RECOVERED_SK2", "Recovered");
define("NETCAT_TRASH_RECOVERED_SK3", "Recovered");
define("NETCAT_TRASH_RECOVERY", "Recovery");
define("NETCAT_TRASH_DELETE_FROM_TRASH", "Delete from trash");
define("NETCAT_TRASH_OBJECT_WERE_DELETED_TRASHBIN_FULL", "rash bin is fullT");
define("NETCAT_TRASH_OBJECT_IN_TRASHBIN_AND_CANCEL", "Object is moved in <a href='%s'>trash</a>. <a href='%s'>Cancel</a>");
define("NETCAT_TRASH_TRASHBIN_DISABLED", "Trash bin is turned off");
define("NETCAT_TRASH_EDIT_SETTINGS", "Edit settings");
define("NETCAT_TRASH_OBJECT_NOT_FOUND", "Object not found");
define("NETCAT_TRASH_TRASHBIN", "Trash bin");
define("NETCAT_TRASH_PRERECOVERYSUB_INFO", "Some of the recovered objects are in the section, which is now gone.");
define("NETCAT_TRASH_PRERECOVERYSUB_CHECKED", "checked");
define("NETCAT_TRASH_PRERECOVERYSUB_NAME", "Name");
define("NETCAT_TRASH_PRERECOVERYSUB_KEYWORD", "Keyword");
define("NETCAT_TRASH_PRERECOVERYSUB_PARENT", "Parent");
define("NETCAT_TRASH_PRERECOVERYSUB_ROOT", "Root");
define("NETCAT_TRASH_PRERECOVERYSUB_NEXT", "Next");
define("NETCAT_TRASH_FILTER", "Filter");
define("NETCAT_TRASH_FILTER_DATE_FROM", "From");
define("NETCAT_TRASH_FILTER_DATE_TO", "To");
define("NETCAT_TRASH_FILTER_DATE_FORMAT", "dd-mm-yyyy h:i");
define("NETCAT_TRASH_FILTER_SUBDIVISION", "Section");
define("NETCAT_TRASH_FILTER_COMPONENT", "Component");
define("NETCAT_TRASH_FILTER_ALL", "all");
define("NETCAT_TRASH_FILTER_APPLY", "apply");
define("NETCAT_TRASH_FILE_DOEST_EXIST", "File %s not found");
define("NETCAT_TRASH_FOLDER_FAIL", "Folder %s doesn't exists");
define("NETCAT_TRASH_ERROR_RELOAD_PAGE", "<a href='index.php'>Reload page</a>");
define("NETCAT_TRASH_TRASHBIN_IS_FULL", "Trash bin is full");
define("NETCAT_TRASH_TEMPLATE_DOESNT_EXIST", "Template for trash bin doest't exist");
define("NETCAT_TRASH_IDENTIFICATOR", "ID");
define("NETCAT_TRASH_USER_IDENTIFICATOR", "User ID");

# USERS
define("CONTROL_USER_GROUPS", "User groups");
define("CONTROL_USER_FUNCS_ALLUSERS", "all");
define("CONTROL_USER_FUNCS_ONUSERS", "turned on");
define("CONTROL_USER_FUNCS_OFFUSERS", "turned off");
define("CONTROL_USER_FUNCS_DOGET", "Perform selection");
define("CONTROL_USER_FUNCS_VIEWCONTROL", "View");
define("CONTROL_USER_FUNCS_SORTBY", "Sort by");
define("CONTROL_USER_FUNCS_USER_NUMBER_ON_THE_PAGE", "users on page.");
define("CONTROL_USER_FUNCS_SORT_ORDER", "Sort order");
define("CONTROL_USER_FUNCS_SORT_ORDER_ACS", "Acs");
define("CONTROL_USER_FUNCS_SORT_ORDER_DESC", "Desc");
define("CONTROL_USER_FUNCS_PREV_PAGE", "previous page");
define("CONTROL_USER_FUNC_CONFIRM_DEL", "Confirm deleting user");
define("CONTROL_USER_FUNC_CONFIRM_DEL_OK", "Confirm");
define("CONTROL_USER_FUNC_CONFIRM_DEL_NOT_USER", "Not users");
define("CONTROL_USER_FUNC_GROUP_ERROR", "Group is not selected");
define("CONTROL_USER", "User");
define("CONTROL_USER_FUNCS_EDITACCESSRIGHT", "Change user rights");
define("CONTROL_USER_ACTIONS", "Actions");
define("CONTROL_USER_RIGHTS", "Permissions");
define("CONTROL_USER_ERROR_NEWPASS_IS_CURRENT", "New password such as a current!");
define("CONTROL_USER_CHANGEPASS", "change password");
define("CONTROL_USER_EDIT", "edit");
define("CONTROL_USER_REG", "New user");
define("CONTROL_USER_NEWPASSWORD", "New password");
define("CONTROL_USER_NEWPASSWORDAGAIN", "New password again");
define("CONTROL_USER_MSG_USERNOTFOUND", "No users found.");
define("CONTROL_USER_GROUP", "Group");
define("CONTROL_USER_GROUP_MEMBERS", "Members");
define("CONTROL_USER_GROUP_NOMEMBERS", "No members");
define("CONTROL_USER_GROUP_TOTAL", "total");
define("CONTROL_USER_GROUPNAME", "Group name");
define("CONTROL_USER_ERROR_GROUPNAME_IS_EMPTY", "Group name can't be empty!");
define("CONTROL_USER_ADDNEWGROUP", "Add group");
define("CONTROL_USER_CHANGERIGHTS", "User access rights");
define("CONTROL_USER_NEW_ADDED", "User was added");
define("CONTROL_USER_NEW_NOTADDED", "User was not added");
define("CONTROL_USER_EDITSUCCESS", "User is edited success");
define("CONTROL_USER_REGISTER", "Register new user");
define("CONTROL_USER_GROUPS_ADD", "Add group");
define("CONTROL_USER_GROUPS_EDIT", "Edit group");
define("CONTROL_USER_ACESSRIGHTS", "access rights");
define("CONTROL_USER_USERSANDRIGHTS", "Users and permissions");
define("CONTROL_USER_PASSCHANGE", "Change password");
define("CONTROL_USER_CATALOGUESWITCH", "Site selection");
define("CONTROL_USER_SECTIONSWITCH", "Section selection");
define("CONTROL_USER_TITLE_USERINFOEDIT", "Edit user information");
define("CONTROL_USER_TITLE_PASSWORDCHANGE", "Change a user password");
define("CONTROL_USER_ERROR_EMPTYPASS", "Password cannot be empty!");
define("CONTROL_USER_ERROR_NOTCANGEPASS", "Password is not changed. Error!");
define("CONTROL_USER_OK_CHANGEDPASS", "Password is successfully changed.");
define("CONTROL_USER_ERROR_RETRY", "Try again!");
define("CONTROL_USER_ERROR_PASSDIFF", "Entered passwords did not match!");
define("CONTROL_USER_MAIL", "Mailing list");
define("CONTROL_USER_MAIL_TITLE_COMPOSE", "Send a email");
define("CONTROL_USER_MAIL_GROUP", "User group");
define("CONTROL_USER_MAIL_ALLGROUPS", "All groups");
define("CONTROL_USER_MAIL_FROM", "Sender");
define("CONTROL_USER_MAIL_BODY", "Mail body");
define("CONTROL_USER_MAIL_ADDATTACHMENT", "attach a file");
define("CONTROL_USER_MAIL_SEND", "Send");
define("CONTROL_USER_MAIL_ERROR_EMAILFIELD", "Email field is not set.");
define("CONTROL_USER_MAIL_OK", "Message has been sent to all users");
define("CONTROL_USER_MAIL_ERROR_NOONEEMAIL", "You forgot to specify email.");
define("CONTROL_USER_MAIL_ATTCHAMENT", "Attach a file");
define("CONTROL_USER_MAIL_ERROR_ONE", "Cannot mail: in <a href=".$ADMIN_PATH."settings.php?phase=1>system settings</a> is not set a user email field.");
define("CONTROL_USER_MAIL_ERROR_TWO", "Cannot mail: in <a href=".$ADMIN_PATH."settings.php?phase=1>system settings</a> is  not set a From name.");
define("CONTROL_USER_MAIL_ERROR_THREE", "Cannot mail: in <a href=".$ADMIN_PATH."settings.php?phase=1>system settings</a> is  not set a From email.");
define("CONTROL_USER_MAIL_ERROR_NOBODY", "You forgot to specify email massage.");
define("CONTROL_USER_MAIL_CHANGE", "change");
define("CONTROL_USER_MAIL_CONTENT", "Mail content");
define("CONTROL_USER_MAIL_SUBJECT", "Mail subject");
define("CONTROL_USER_MAIL_RULES", "Conditions of dispatch");
define("CONTROL_USER_FUNCS_USERSGET", "Selection of users");
define("CONTROL_USER_FUNCS_USERSGET_EXT", "Extended search");
define("CONTROL_USER_FUNCS_SEARCHEDUSER", "Found users");
define("CONTROL_USER_FUNCS_USERCOUNT", "Total Users");
define("CONTROL_USER_FUNCS_ADDUSER", "Add new user");
define("CONTROL_USER_FUNCS_NORIGHTS", "The user has no rights.");
define("CONTROL_USER_FUNCS_GROUP_NORIGHTS", "The group has no rights.");
define("CONTROL_USER_RIGHTS_ITEM", "Item");
define("CONTROL_USER_RIGHTS_TYPE_OF_RIGHT", "User Roles");
define("CONTROL_USER_RIGHTS_GUESTONE", "Guest");
define("CONTROL_USER_RIGHTS_DIRECTOR", "Director");
define("CONTROL_USER_RIGHTS_SUPERVISOR", "Supervisor");
define("CONTROL_USER_RIGHTS_SITEADMIN", "Access to Site");
define("CONTROL_USER_RIGHTS_CATALOGUEADMINALL", "All site editor");
define("CONTROL_USER_RIGHTS_SUBDIVISIONADMIN", "Access to Section");
define("CONTROL_USER_RIGHTS_SUBCLASSADMIN", "Access to Component");
define("CONTROL_USER_RIGHTS_SUBCLASSADMINS", "Section component editor");
define("CONTROL_USER_RIGHTS_CLASSIFICATORADMIN", "Classificator administrator");
define("CONTROL_USER_RIGHTS_CLASSIFICATORADMINALL", "All classificators administrator");
define("CONTROL_USER_RIGHTS_EDITOR", "Editor");
define("CONTROL_USER_RIGHTS_SUBSCRIBER", "Subscriber");
define("CONTROL_USER_RIGHTS_MODERATOR", "Moderator");
define("CONTROL_USER_RIGHTS_BAN", "Ban");
define("CONTROL_USER_RIGHTS_SITE", "Ban on site");
define("CONTROL_USER_RIGHTS_SITEALL", "Ban on all sites");
define("CONTROL_USER_RIGHTS_SUB", "Ban on section");
define("CONTROL_USER_RIGHTS_CC", "Ban on section component");
define("CONTROL_USER_RIGHTS_LOAD", "Loading");
define("CONTROL_USER_RIGHT_ADDNEWRIGHTS", "Add Permissions");
define("CONTROL_USER_RIGHT_ADDPERM", "Add new permission to user");
define("CONTROL_USER_RIGHT_ADDPERM_GROUP", "Add new permission to group");
define("CONTROL_USER_FUNCS_FROMCAT", "from catalogue");
define("CONTROL_USER_FUNCS_FROMSEC", "from section");
define("CONTROL_USER_FUNCS_ADDNEWRIGHTS", "Add Permissions");
define("CONTROL_USER_FUNCS_ERR_CANTREMGROUP", "Cannot delete group %s. Error!");
define("CONTROL_USER_SELECTSITE", "Choose a site");
define("CONTROL_USER_SELECTSECTION", "Choose section");
define("CONTROL_USER_NOONESECSINSITE", "Site has no one section.");
define("CONTROL_USER_FUNCS_CLASSINSECTION", "Section first infoblock");
define("CONTROL_USER_RIGHTS_ERR_CANTREMPRIV", "Cannot delete the privilege. Error!");
define("CONTROL_USER_RIGHTS_UPDATED_OK", "User rights updated.");
define("CONTROL_USER_RIGHTS_ERROR_NOSELECTED", "Item not selected");
define("CONTROL_USER_RIGHTS_ERROR_DATA", "Error in date");
define("CONTROL_USER_RIGHTS_ERROR_DB", "Error");
define("CONTROL_USER_RIGHTS_ERROR_POSSIBILITY", "Not selected opportunity");
define("CONTROL_USER_RIGHTS_ERROR_NOTSITE", "Select site");
define("CONTROL_USER_RIGHTS_ERROR_NOTSUB", "Select subdivision");
define("CONTROL_USER_RIGHTS_ERROR_NOTCCINSUB", "This section has no components");
define("CONTROL_USER_RIGHTS_ERROR_NOTTYPEOFRIGHT", "Select type of right");
define("CONTROL_USER_RIGHTS_ERROR_START", "Error in start date of permission");
define("CONTROL_USER_RIGHTS_ERROR_END", "An error in the date of expiry of rights");
define("CONTROL_USER_RIGHTS_ERROR_STARTEND", "End time of the rights can not be earlier than the beginning");
define("CONTROL_USER_RIGHTS_ERROR_GUEST", "Error");
define("CONTROL_USER_RIGHTS_ADDED", "User rights was added");
define("CONTROL_USER_RIGHTS_LIVETIME", "Lifetime");
define("CONTROL_USER_RIGHTS_UNLIMITED", "unlimited");
define("CONTROL_USER_RIGHTS_NONLIMITED", "unlimited");
define("CONTROL_USER_RIGHTS_LIMITED", "limited");
define("CONTROL_USER_RIGHTS_STARTING_OPERATIONS", "Activate permissions");
define("CONTROL_USER_RIGHTS_FINISHING_OPERATIONS", "Deactivate permissions");
define("CONTROL_USER_RIGHTS_NOW", "now");
define("CONTROL_USER_RIGHTS_ACROSS", "in");
define("CONTROL_USER_RIGHTS_ACROSS_MINUTES", "minutes");
define("CONTROL_USER_RIGHTS_ACROSS_HOURS", "hours");
define("CONTROL_USER_RIGHTS_ACROSS_DAYS", "days");
define("CONTROL_USER_RIGHTS_ACROSS_MONTHS", "months");
define("CONTROL_USER_RIGHTS_RIGHT", "Permissions");
define("CONTROL_USER_RIGHTS_CONTROL_ADD", "Add");
define("CONTROL_USER_RIGHTS_CONTROL_EDIT", "Edit");
define("CONTROL_USER_RIGHTS_CONTROL_DELETE", "Delete");
define("CONTROL_USER_RIGHTS_CONTROL_HELP", "Help");
define("CONTROL_USER_USERS_MOVED_SUCCESSFULLY", "Users moved successfully");
define("CONTROL_USER_SELECT_GROUP_TO_MOVE", "Select groups");
define("CONTROL_USER_SELECTSITEALL", "All sites");

# TEMPLATE
define("CONTROL_TEMPLATE", "Design templates");
define("CONTROL_TEMPLATE_ADD", "Add new template");
define("CONTROL_TEMPLATE_EDIT", "Template edit");
define("CONTROL_TEMPLATE_DELETE", "Template delete");
define("CONTROL_TEMPLATE_OPT_ADD", "add options");
define("CONTROL_TEMPLATE_OPT_EDIT", "edit options");
define("CONTROL_TEMPLATE_ERR_NAME", "Specify the name of a design template name.");
define("CONTROL_TEMPLATE_INFO_CONVERT", "Remember to <a href='#' onclick=\"window.open('".$ADMIN_PATH."template/converter.php', 'converter','width=600,height=410,status=no,resizable=yes');\">add slashes</a>.");
define("CONTROL_TEMPLATE_TEPL_NAME", "Name");
define("CONTROL_TEMPLATE_TEPL_MENU", "Navigation templates");
define("CONTROL_TEMPLATE_TEPL_HEADER", "Header");
define("CONTROL_TEMPLATE_TEPL_FOOTER", "Footer");
define("CONTROL_TEMPLATE_TEPL_CREATE", "Add template design");
define("CONTROL_TEMPLATE_NOT_FOUND", "Design template %s not found!");
define("CONTROL_TEMPLATE_ERR_USED_IN_SITE", "This template is used in following sites:");
define("CONTROL_TEMPLATE_ERR_USED_IN_SUB", "This template is used in following sections:");
define("CONTROL_TEMPLATE_ERR_CANTDEL", "Cannot delete design template");
define("CONTROL_TEMPLATE_INFO_DELETE", "You want deleting template");
define("CONTROL_TEMPLATE_INFO_DELETE_SOME", "These templates will be deleted");
define("CONTROL_TEMPLATE_DELETED", "Template was deleted");
define("CONTROL_TEMPLATE_ADDLINK", "New template");
define("CONTROL_TEMPLATE_REMOVETHIS", "Delete this template");
define("CONTROL_TEMPLATE_PREF_EDIT", "edit setting");
define("CONTROL_TEMPLATE_NONE", "No templates found");
define("CONTROL_TEMPLATE_TEPL_IMPORT", "Import template");
define("CONTROL_TEMPLATE_IMPORT", "Import template");
define("CONTROL_TEMPLATE_IMPORT_UPLOAD", "Upload");
define("CONTROL_TEMPLATE_IMPORT_SELECT", "Choose a template for import (all child templates are included)");
define("CONTROL_TEMPLATE_IMPORT_CONTINUE", "Continue");
define("CONTROL_TEMPLATE_IMPORT_ERROR_NOTUPLOADED", "Error importing template");
define("CONTROL_TEMPLATE_IMPORT_ERROR_SQL", "Error inserting SQL");
define("CONTROL_TEMPLATE_IMPORT_ERROR_EXTRACT", "Error extracting file %s to %s");
define("CONTROL_TEMPLATE_IMPORT_ERROR_MOVE", "Error copying files from %s to %s");
define("CONTROL_TEMPLATE_IMPORT_SUCCESS", "Template import succeeded");
define("CONTROL_TEMPLATE_EXPORT", "Export template to file");
define("CONTROL_TEMPLATE_FILES_PATH", "Files dir <a href='%s'>%s</a>");
define("CONTROL_TEMPLATE_PARTIALS", "Partials");
define("CONTROL_TEMPLATE_PARTIALS_NEW", "New partial");
define("CONTROL_TEMPLATE_PARTIALS_ADD", "Add partial");
define("CONTROL_TEMPLATE_PARTIALS_REMOVE", "Remove partial");
define("CONTROL_TEMPLATE_PARTIALS_KEYWORD_FIELD", "Keyword (english)");
define("CONTROL_TEMPLATE_PARTIALS_KEYWORD_FIELD_ERROR", "Keyword can contain only latin characters");
define("CONTROL_TEMPLATE_PARTIALS_KEYWORD_FIELD_REQUIRED_ERROR", "Keyword is required");
define("CONTROL_TEMPLATE_PARTIALS_DESCRIPTION_FIELD", "Name");
define("CONTROL_TEMPLATE_PARTIALS_ENABLE_ASYNC_LOAD_FIELD", "enable asynchronous load");
define("CONTROL_TEMPLATE_PARTIALS_SOURCE_FIELD", "Partial source");
define("CONTROL_TEMPLATE_PARTIALS_EXISTS_ERROR", "Partial already exists");
define("CONTROL_TEMPLATE_PARTIALS_NOT_EXISTS", "Template (partial) not exists");
define("CONTROL_TEMPLATE_BASE_TEMPLATE", "Create template from base template");
define("CONTROL_TEMPLATE_BASE_TEMPLATE_CREATE_FROM_SCRATCH", "Create template from scratch");
define("CONTROL_TEMPLATE_CONTINUE", "Continue");

define("CONTROL_TEMPLATE_KEYWORD", "Keyword");
define("CONTROL_TEMPLATE_KEYWORD_ONLY_DIGITS", "Keyword cannot consist of digits only");
define("CONTROL_TEMPLATE_KEYWORD_TOO_LONG", "Keyword cannot be longer than %d characters");
define("CONTROL_TEMPLATE_KEYWORD_INVALID_CHARACTERS", "Keyword can contain only latin letters, digits and underscores");
define("CONTROL_TEMPLATE_KEYWORD_NON_UNIQUE", "Keyword &ldquo;%s&rdquo; is already assigned to the &ldquo;%d. %s&rdquo; template");
define("CONTROL_TEMPLATE_KEYWORD_RESERVED", "Cannot use &ldquo;%s&rdquo; as a keyword, because it is a reserved word");
define("CONTROL_TEMPLATE_KEYWORD_PATH_EXISTS", "Cannot use &ldquo;%s&rdquo; as a keyword, because a folder with this name already exists");

# CLASSIFICATORS
define("CONTENT_CLASSIFICATORS", "Classificators");
define("CONTENT_CLASSIFICATORS_NAMEONE", "Classificator");
define("CONTENT_CLASSIFICATORS_NAMEALL", "All classificators");
define("CONTENT_CLASSIFICATORS_ELEMENTS", "elements");
define("CONTENT_CLASSIFICATORS_ELEMENT", "Element");
define("CONTENT_CLASSIFICATORS_ELEMENT_NAME", "Element name");
define("CONTENT_CLASSIFICATORS_ELEMENT_VALUE", "Added value");
define("CONTENT_CLASSIFICATORS_ELEMENTS_ADDONE", "Add element");
define("CONTENT_CLASSIFICATORS_ELEMENTS_ADD", "Add element");
define("CONTENT_CLASSIFICATORS_ELEMENTS_ADD_SUCCESS", "Element successfully added");
define("CONTENT_CLASSIFICATORS_ELEMENTS_EDIT", "Edit element");
define("CONTENT_CLASSIFICATORS_LIST_ADD", "Add classificator");
define("CONTENT_CLASSIFICATORS_LIST_EDIT", "Edit classificator");
define("CONTENT_CLASSIFICATORS_LIST_DELETE", "Delete classificator");
define("CONTENT_CLASSIFICATORS_LIST_DELETE_SELECTED", "Delete selected");
define("CONTENT_CLASSIFICATORS_ERR_NONE", "Project has no classificators.");
define("CONTENT_CLASSIFICATORS_ERR_ELEMENTNONE", "Classificator has no elements.");
define("CONTENT_CLASSIFICATORS_ERR_SYSDEL", "You can't remove a element from system classificator");
define("CONTENT_CLASSIFICATORS_ERR_EDITI_GUESTRIGHTS", "You have no permission to edit element in classificator!");
define("CONTENT_CLASSIFICATORS_ERROR_NAME", "Enter classificator name!");
define("CONTENT_CLASSIFICATORS_ERROR_FILE_NAME", "Select CVS-file");
define("CONTENT_CLASSIFICATORS_ERROR_KEYWORD", "Enter classificator keyword name!");
define("CONTENT_CLASSIFICATORS_ERROR_KEYWORDINV", "Classificator keyword name can contain only latin characters");
define("CONTENT_CLASSIFICATORS_ERROR_KEYWORDFL", "Classificator keyword name can contain only latin characters!");
define("CONTENT_CLASSIFICATORS_ERROR_KEYWORDAE", "Classificator with such keyword name already exists!");
define("CONTENT_CLASSIFICATORS_ERROR_KEYWORDREZ", "This word is reserved!");
define("CONTENT_CLASSIFICATORS_ADDLIST", "New classificator");
define("CONTENT_CLASSIFICATORS_ADD_KEYWORD", "Table name");
define("CONTENT_CLASSIFICATORS_SAVE", "Save changes");
define("CONTENT_CLASSIFICATORS_NO_NAME", "(no name)");
define("CLASSIFICATORS_SORT_HEADER", "Sorting pattern");
define("CLASSIFICATORS_SORT_PRIORITY_HEADER", "Priority");
define("CLASSIFICATORS_SORT_TYPE_ID", "ID");
define("CLASSIFICATORS_SORT_TYPE_NAME", "Element");
define("CLASSIFICATORS_SORT_TYPE_PRIORITY", "Priority");
define("CLASSIFICATORS_SORT_DIRECTION", "Sorting direction");
define("CLASSIFICATORS_SORT_ASCENDING", "ASC");
define("CLASSIFICATORS_SORT_DESCENDING", "DESC");
define("CLASSIFICATORS_IMPORT_HEADER", "Import");
define("CLASSIFICATORS_IMPORT_BUTTON", "Import");
define("CLASSIFICATORS_IMPORT_FILE", "CSV-file (*)");
define("CLASSIFICATORS_IMPORT_DESCRIPTION", "If imported file has only one column, this column will be considered as Element field, if it has two columns - first will be considered as Element and another as Priority field.");
define("CLASSIFICATORS_SUCCESS_DELETEONE", "Classificator successfully deleted.");
define("CLASSIFICATORS_SUCCESS_DELETE", "Classificators successfully deleted.");
define("CLASSIFICATORS_SUCCESS_ADD", "Classificator successfully added.");
define("CLASSIFICATORS_SUCCESS_EDIT", "Classificator successfully changed.");
define("CLASSIFICATORS_ERROR_DELETEONE_SYS", "Classificator %s is system, deletion forbidden.");
define("CLASSIFICATORS_ERROR_ADD", "Cannot add classificator.");
define("CLASSIFICATORS_ERROR_EDIT", "Cannot edit classificator.");

# TOOLS HTML
define("TOOLS_HTML", "HTML-editor");
define("TOOLS_HTML_INFO", "Edit in WYSIWYG HTML-editor");

define("TOOLS_DUMP", "Project backups");
define("TOOLS_DUMP_CREATE", "Create backup");
define("TOOLS_DUMP_CREATED", "Backup created: %FILE.");
define("TOOLS_DUMP_CREATION_FAILED", "Backup creation failed.");
define("TOOLS_DUMP_DELETED", "File %FILE deleted.");
define("TOOLS_DUMP_RESTORE", "Restore backup");
define("TOOLS_DUMP_MSG_RESTORED", "Backup restored.");
define("TOOLS_DUMP_INC_TITLE", "Restore backup from local drive");
define("TOOLS_DUMP_INC_DORESTORE", "Restore");
define("TOOLS_DUMP_INC_DBDUMP", "database dump");
define("TOOLS_DUMP_INC_FOLDER", "folder content");
define("TOOLS_DUMP_ERROR_CANTDELETE", "Error! Cannot delete %FILE.");
define("TOOLS_DUMP_INC_ARCHIVE", "Archive name");
define("TOOLS_DUMP_INC_DATE", "Date");
define("TOOLS_DUMP_INC_SIZE", "Size");
define("TOOLS_DUMP_INC_DOWNLOAD", "download");
define("TOOLS_DUMP_NOONE", "No backups yet.");
define("TOOLS_DUMP_DATE", "Backup date");
define("TOOLS_DUMP_SIZE", "Size");
define("TOOLS_DUMP_CREATEAP", "Create project archive");
define("TOOLS_DUMP_CONFIRM", "Confirm project archive creation");
define("TOOLS_DUMP_BACKUPLIST_HEADER", "Available backups");
define("TOOLS_DUMP_CREATE_HEADER", "Create new archive");
define("TOOLS_DUMP_CREATE_OPT_FULL", "Complete archive (includes all files, database dump and restore script)");
define("TOOLS_DUMP_CREATE_OPT_DATA", "Project data (images, netcat_templates, modules, netcat_files directories and database dump");
define("TOOLS_DUMP_CREATE_OPT_SQL", "Database only");
define("TOOLS_DUMP_CREATE_SUBMIT", "Create new backup");
define("TOOLS_DUMP_REMOVE_SELECTED", "Remove selected backups");
define("TOOLS_DUMP_CREATE_WAIT", "Creating the backup archive. Please wait.");
define("TOOLS_DUMP_RESTORE_WAIT", "Restoring data from the backup archive. Please wait.");
define("TOOLS_DUMP_CONNECTION_LOST", "Lost connection to the server. If the requested action was not completed, %s.");
define("TOOLS_DUMP_CONNECTION_LOST_SYSTEM_TAR", "try enabling system tar utility execution from PHP");
define("TOOLS_DUMP_CONNECTION_LOST_INCREASE_PHP_LIMITS", "check PHP error log, and try to increase PHP memory limit, web server timeouts and server resource limits");
define("TOOLS_DUMP_CONNECTION_LOST_INCREASE_SERVER_LIMITS", "try to increase web server timeouts and server resource limits");
define("TOOLS_DUMP_CONNECTION_LOST_GO_BACK", "Back");

define("TOOLS_REDIRECT", "Redirect");
define("TOOLS_REDIRECT_OLDURL", "Old URL");
define("TOOLS_REDIRECT_NEWURL", "New URL");
define("TOOLS_REDIRECT_OLDLINK", "Old URL");
define("TOOLS_REDIRECT_NEWLINK", "New URL");
define("TOOLS_REDIRECT_HEADER", "Header");
define("TOOLS_REDIRECT_HEADERSEND", "Header");
define("TOOLS_REDIRECT_SETTINGS", "Settings");
define("TOOLS_REDIRECT_CHANGEINFO", "Change");
define("TOOLS_REDIRECT_NONE", "No redirects.");
define("TOOLS_REDIRECT_ADD", "New redirect");
define("TOOLS_REDIRECT_EDIT", "Edit redirect");
define("TOOLS_REDIRECT_ADDONLY", "Add redirect");
define("TOOLS_REDIRECT_CANTBEEMPTY", "This fields can't be empty!");
define("TOOLS_REDIRECT_OLDURL_MUST_BE_UNIQUE", "Same redirect Old URL exist - <a href='".nc_core('NETCAT_FOLDER')."action.php?ctrl=admin.redirect&action=edit&id=%s'>go to</a>");
define("TOOLS_REDIRECT_DISABLED", "In the configuration file tools \"Forward\" disabled. <br /> To include it, edit the file vars.inc.php value of \$NC_REDIRECT_DISABLED to 0.");
define("TOOLS_REDIRECT_GROUP", "Group");
define("TOOLS_REDIRECT_GROUP_NAME", "Group name");
define("TOOLS_REDIRECT_GROUP_ADD", "Add group");
define("TOOLS_REDIRECT_GROUP_EDIT", "Edit group");
define("TOOLS_REDIRECT_GROUP_DELETE", "Delete group");
define("TOOLS_REDIRECT_BACK", "Back");
define("TOOLS_REDIRECT_SAVE_OK", "Redirect saved");
define("TOOLS_REDIRECT_GROUP_SAVE_OK", "Group saved");
define("TOOLS_REDIRECT_SAVE_ERROR", "Can't save");
define("TOOLS_REDIRECT_DELETE", "Delete");
define("TOOLS_REDIRECT_DELETE_CONFIRM_REDIRECTS", "Redirects will be deleted:");
define("TOOLS_REDIRECT_DELETE_CONFIRM_GROUP", "Group &quot;%s&quot; will be deleted, including redirects:");
define("TOOLS_REDIRECT_DELETE_OK", "Deleted");
define("TOOLS_REDIRECT_STATUS", "Status");
define("TOOLS_REDIRECT_SAVE", "Save");
define("TOOLS_REDIRECT_MOVE", "Move to group");
define("TOOLS_REDIRECT_MOVE_CONFIRM_REDIRECTS", "Redirects will be moved:");
define("TOOLS_REDIRECT_MOVE_OK", "Moved");
define("TOOLS_REDIRECT_NOTHING_SELECTED", "No redirects selected");
define("TOOLS_REDIRECT_IMPORT", "Import");
define("TOOLS_REDIRECT_FIELDS", "Redirect fields");
define("TOOLS_REDIRECT_CONTINUE", "Continue");
define("TOOLS_REDIRECT_DO_IMPORT", "Import");
define("TOOLS_REDIRECT_MOVE_TITLE", "Redirects move");
define("TOOLS_REDIRECT_DELETE_TITLE", "Redirects deletion");
define("TOOLS_REDIRECT_IMPORT_TITLE", "Redirects import");

define("TOOLS_CRON", "Task manager");
define("TOOLS_CRON_MINUTES", "Minutes");
define("TOOLS_CRON_HOURS", "Hours");
define("TOOLS_CRON_DAYS", "Days");
define("TOOLS_CRON_MONTHS", "Months");
define("TOOLS_CRON_LANCHED", "Last Launch");
define("TOOLS_CRON_SCRIPTURL", "Script URL");
define("TOOLS_CRON_ADDLINK", "Add task");
define("TOOLS_CRON_CHANGE", "Settings");
define("TOOLS_CRON_NOTASKS", "No tasks.");
define("TOOLS_CRON_WRONG_DOMAIN", "Domain, specified in crontab.php (%s), not matches to current (%s), tasks may not work! Check settings as in <a href='https://netcat.ru/developers/docs/system-tools/task-management/' TARGET='_blank'>documentation</a>.");

define("TOOLS_COPYSUB", "Copy sub");
define("TOOLS_COPYSUB_COPY", "Copy");
define("TOOLS_COPYSUB_COPY_SUCCESS", "Copying successful");
define("TOOLS_COPYSUB_SOURCE", "Source");
define("TOOLS_COPYSUB_DESTINATION", "Destination");
define("TOOLS_COPYSUB_ACTION", "Action");
define("TOOLS_COPYSUB_COPY_SITE", "Copy site");
define("TOOLS_COPYSUB_COPY_SUB", "Copy sub");
define("TOOLS_COPYSUB_COPY_SUB_LOWER", "copy sub");
define("TOOLS_COPYSUB_SITE", "Site");
define("TOOLS_COPYSUB_SUB", "Sub");
define("TOOLS_COPYSUB_KEYWORD_SUB", "Section keyword");
define("TOOLS_COPYSUB_NAME_CC", "Component name");
define("TOOLS_COPYSUB_KEYWORD_CC", "Component keyword");
define("TOOLS_COPYSUB_TEMPLATE_NAME", "Template name");
define("TOOLS_COPYSUB_SETTINGS", "Copy settings");
define("TOOLS_COPYSUB_COPY_WITH_CHILD", "copy sub");
define("TOOLS_COPYSUB_COPY_WITH_CC", "copy components in sub");
define("TOOLS_COPYSUB_COPY_WITH_OBJECT", "copy objects");
define("TOOLS_COPYSUB_ERROR_KEYWORD_EXIST", "Sub with the keyword already exists");
define("TOOLS_COPYSUB_ERROR_LEVEL_COUNT", "You can not copy a section in its own sub");
define("TOOLS_COPYSUB_ERROR_PARAM", "Invalid parameters");
define("TOOLS_COPYSUB_ERROR_SITE_NOT_FOUND", "Site not found");

# TOOLS TRASH
define("TOOLS_TRASH", "Trash bin");
define("TOOLS_TRASH_CLEAN", "Clean trash bin");

# MODERATION SECTION
define("NETCAT_MODERATION_NO_OBJECTS_IN_SUBCLASS", "There are no data in this component.");

define("NETCAT_MODERATION_ERROR_NORIGHTS", " You have no permission to perform an operation!");
define("NETCAT_MODERATION_ERROR_NORIGHT", "You have no permission to perform an operation!");
define("NETCAT_MODERATION_ERROR_NORIGHTGUEST", "Guest can not perform this operation");
define("NETCAT_MODERATION_ERROR_NOOBJADD", "Cannot add object.");
define("NETCAT_MODERATION_ERROR_NOOBJCHANGE", "Cannot change object.");
define("NETCAT_MODERATION_MSG_OBJADD", "Object added.");
define("NETCAT_MODERATION_MSG_OBJADDMOD", "Object will be added after moderation.");
define("NETCAT_MODERATION_MSG_OBJCHANGED", "Object changed.");
define("NETCAT_MODERATION_MSG_OBJDELETED", "Object deleted.");
define("NETCAT_MODERATION_FILES_UPLOADED", "Uploaded");
define("NETCAT_MODERATION_FILES_DELETE", "delete file");
define("NETCAT_MODERATION_LISTS_CHOOSE", "-- choose --");
define("NETCAT_MODERATION_RADIO_EMPTY", "No answer");
define("NETCAT_MODERATION_PRIORITY", "Object priority");
define("NETCAT_MODERATION_TURNON", "turn on");
define("NETCAT_MODERATION_OBJADDED", "Object addition");
define("NETCAT_MODERATION_OBJUPDATED", "Object change");
define("NETCAT_MODERATION_MSG_OBJSDELETED", "Objects deleted");
define("NETCAT_MODERATION_OBJ_ON", "on");
define("NETCAT_MODERATION_OBJ_OFF", "off");
define("NETCAT_MODERATION_OBJECT", "Object");
define("NETCAT_MODERATION_MORE", "more");
define("NETCAT_MODERATION_MOVE_UP", "Move up");
define("NETCAT_MODERATION_MOVE_DOWN", "Move down");
define("NETCAT_MODERATION_BLOCK_SETTINGS", "Block settings");
define("NETCAT_MODERATION_DELETE_BLOCK", "Remove this block");
define("NETCAT_MODERATION_ADD_BLOCK", "Add a block");
define("NETCAT_MODERATION_ADD_BLOCK_TITLE", "New block");
define("NETCAT_MODERATION_ADD_OBJECT", "Add");
define("NETCAT_MODERATION_ADD_OBJECT_DEFAULT", "element");
define("NETCAT_MODERATION_REMOVE_INFOBLOCK_CONFIRMATION_HEADER", "Page block removal");
define("NETCAT_MODERATION_REMOVE_INFOBLOCK_CONFIRMATION_BODY", "Block and its content will be removed from the page. Press &ldquo;Delete&rdquo; to confirm this action");
define("NETCAT_MODERATION_COMPONENT_SEARCH_BY_NAME", "search by name");
define("NETCAT_MODERATION_CLEAR_FIELD", "reset");
define("NETCAT_MODERATION_COMPONENT_NO_TEMPLATE", "Base component template");
define("NETCAT_MODERATION_COMPONENT_TEMPLATE", "Template");
define("NETCAT_MODERATION_COMPONENT_TEMPLATES", "Templates");
define("NETCAT_MODERATION_COMPONENT_TEMPLATE_PREV", "previous template");
define("NETCAT_MODERATION_COMPONENT_TEMPLATE_NEXT", "next template");
define("NETCAT_MODERATION_COPY_BLOCK", "Copy");
define("NETCAT_MODERATION_PASTE_BLOCK", "Paste copied block here");
define("NETCAT_MODERATION_REMOVE_IMAGE", "Remove image");
define("NETCAT_MODERATION_REPLACE_IMAGE", "Replace image");

define("NETCAT_MODERATION_WARN_COMMITDELETION", "Confirm removal of object #%s");
define("NETCAT_MODERATION_WARN_COMMITDELETIONINCLASS", "Confirm removal of object in component #%s");

define("NETCAT_MODERATION_PASSWORD", "Password (*)");
define("NETCAT_MODERATION_PASSWORDAGAIN", "Enter password again");
define("NETCAT_MODERATION_INFO_REQFIELDS", "Fields highlighted with (*) cannot be empty.");
define("NETCAT_MODERATION_BUTTON_ADD", "Add");
define("NETCAT_MODERATION_BUTTON_CHANGE", "Save changes");
define("NETCAT_MODERATION_BUTTON_RESET", "Reset");

define("NETCAT_MODERATION_COMMON_KILLALL", "Delete objects");
define("NETCAT_MODERATION_COMMON_KILLONE", "Delete object");

define("NETCAT_MODERATION_MULTIFILE_SIZE", "Files exceeding allowed file size (%SIZE) in <em>%NAME</em>");
define("NETCAT_MODERATION_MULTIFILE_TYPE", "Files of wrong type in <em>%NAME</em>");
define("NETCAT_MODERATION_MULTIFILE_MIN_COUNT", "At least %FILES must be uploaded to <em>%NAME</em>");
define("NETCAT_MODERATION_MULTIFILE_MAX_COUNT", "No more than %FILES can be uploaded to <em>%NAME</em>");
define("NETCAT_MODERATION_MULTIFILE_COUNT_FILES", "file,files");
define("NETCAT_MODERATION_MULTIFILE_DEFAULT_CUSTOM_NAME_CAPTION", "file description");
define("NETCAT_MODERATION_ADD", "add");

define("NETCAT_MODERATION_MSG_ONE", "Field %NAME is required.");
define("NETCAT_MODERATION_MSG_TWO", "Wrong value type in <em>%NAME</em>.");
define("NETCAT_MODERATION_MSG_SIX", "It is necessary to upload a file <em>%NAME</em>.");
define("NETCAT_MODERATION_MSG_SEVEN", "File <em>%NAME</em> is too big.");
define("NETCAT_MODERATION_MSG_EIGHT", "Wrong file format for <em>%NAME</em>.");
define("NETCAT_MODERATION_MSG_TWENTYONE", "Invalid keyword.");
define("NETCAT_MODERATION_MSG_RETRYPASS", "Re-enter password");
define("NETCAT_MODERATION_MSG_PASSMIN", "Minimal password length is %s symbols.");
define("NETCAT_MODERATION_MSG_NEED_AGREED", "Agree");
define("NETCAT_MODERATION_MSG_LOGINALREADY", "Login %s is already in use");
define("NETCAT_MODERATION_MSG_LOGININCORRECT", "Login incorrect");
define("NETCAT_MODERATION_BACKTOSECTION", "Return back to section");

define("NETCAT_MODERATION_ISON", "Turned on");
define("NETCAT_MODERATION_ISOFF", "Turned off");
define("NETCAT_MODERATION_OBJISON", "Object turned on");
define("NETCAT_MODERATION_OBJISOFF", "Object turned off");
define("NETCAT_MODERATION_OBJSAREON", "Objects turned on");
define("NETCAT_MODERATION_OBJSAREOFF", "Objects turned off");
define("NETCAT_MODERATION_CHANGED", "ID of the changed user");
define("NETCAT_MODERATION_CHANGE", "Edit");
define("NETCAT_MODERATION_DELETE", "Delete");
define("NETCAT_MODERATION_TURNTOON", "Turn on");
define("NETCAT_MODERATION_TURNTOOFF", "Turn off");
define("NETCAT_MODERATION_ID", "Identifier");
define("NETCAT_MODERATION_COPY_OBJECT", "Copy / move");

define("NETCAT_MODERATION_REMALL", "Delete all");
define("NETCAT_MODERATION_DELETESELECTED", "Delete selected");
define("NETCAT_MODERATION_SELECTEDON", "Turn selected objects on");
define("NETCAT_MODERATION_SELECTEDOFF", "Turn selected objects off");
define("NETCAT_MODERATION_NOTSELECTEDOBJ", "Objects are not selected");
define("NETCAT_MODERATION_APPLY_CHANGES_TITLE", "Apply changes?");
define("NETCAT_MODERATION_APPLY_CHANGES_TEXT", "Do you really want to apply changes?");
define("NETCAT_MODERATION_CLASSID", "Number of component in section");
define("NETCAT_MODERATION_ADDEDON", "ID of the added user");

define("NETCAT_MODERATION_MOD_NOANSWER", "no value");
define("NETCAT_MODERATION_MOD_DON", " to ");
define("NETCAT_MODERATION_MOD_FROM", " from ");
define("NETCAT_MODERATION_MODA", "--------- No value ---------");

define("NETCAT_MODERATION_FILTER", "Filter");
define("NETCAT_MODERATION_TITLE", "Title");
define("NETCAT_MODERATION_DESCRIPTION", "Description");

define("NETCAT_MODERATION_TRASHED_OBJECTS", "Trashed objects");
define("NETCAT_MODERATION_TRASHED_OBJECTS_RESTORE", "Restore object");

define("NETCAT_MODERATION_NO_RELATED", "[none]");
define("NETCAT_MODERATION_RELATED_INEXISTENT", "[nonexistent object ID=%s]");
define("NETCAT_MODERATION_CHANGE_RELATED", "change");
define("NETCAT_MODERATION_REMOVE_RELATED", "remove");
define("NETCAT_MODERATION_SELECT_RELATED", "select");
define("NETCAT_MODERATION_COPY_HERE_RELATED", "Copy here");
define("NETCAT_MODERATION_MOVE_HERE_RELATED", "Move here");
define("NETCAT_MODERATION_CONFIRM_COPY_RELATED", "Confirm action");
define("NETCAT_MODERATION_RELATED_POPUP_TITLE", "Select related item for '%s'");
define("NETCAT_MODERATION_RELATED_NO_CONCRETE_CLASS_IN_SUB", "There are no &quot;%s&quot; components in this section.");
define("NETCAT_MODERATION_RELATED_NO_ANY_CLASS_IN_SUB", "There are no components in this section.");
define("NETCAT_MODERATION_RELATED_ERROR_SAVING", "Cannot save the value you've selected (probably the main object form was closed). Please try to select value once again.");
define("NETCAT_MODERATION_COPY_SUCCESS", "Object successfully copied");
define("NETCAT_MODERATION_MOVE_SUCCESS", "Object successfully moved");


define("NETCAT_MODERATION_SEO_TITLE", "Title");
define("NETCAT_MODERATION_SEO_KEYWORDS", "SEO keywords");
define("NETCAT_MODERATION_SEO_DESCRIPTION", "SEO description");

define("NETCAT_MODERATION_SMO_TITLE", "SMO title");
define("NETCAT_MODERATION_SMO_TITLE_HELPER", "Will header article when posting links to a page on Facebook or VKontakte");
define("NETCAT_MODERATION_SMO_DESCRIPTION", "SMO description");
define("NETCAT_MODERATION_SMO_DESCRIPTION_HELPER", "Will the text of the article when posting links to a page on Facebook or VKontakte");
define("NETCAT_MODERATION_SMO_IMAGE", "SMO image");

define("NETCAT_MODERATION_STANDART_FIELD_USER_ID", "User ID");
define("NETCAT_MODERATION_STANDART_FIELD_USER", "User");
define("NETCAT_MODERATION_STANDART_FIELD_PRIORITY", "Priority");
define("NETCAT_MODERATION_STANDART_FIELD_KEYWORD", "Keyword");
define("NETCAT_MODERATION_STANDART_FIELD_NC_TITLE", "SEO Meta Title");
define("NETCAT_MODERATION_STANDART_FIELD_NC_KEYWORDS", "SEO Meta Keywords");
define("NETCAT_MODERATION_STANDART_FIELD_NC_DESCRIPTION", "SEO Meta Description");
define("NETCAT_MODERATION_STANDART_FIELD_NC_SMO_TITLE", "SMO Meta Title");
define("NETCAT_MODERATION_STANDART_FIELD_NC_SMO_DESCRIPTION", "SMO Meta Description");
define("NETCAT_MODERATION_STANDART_FIELD_NC_SMO_IMAGE", "SMO Meta Image");
define("NETCAT_MODERATION_STANDART_FIELD_IP", "IP");
define("NETCAT_MODERATION_STANDART_FIELD_USER_AGENT", "User agent");
define("NETCAT_MODERATION_STANDART_FIELD_CREATED", "Created");
define("NETCAT_MODERATION_STANDART_FIELD_LAST_UPDATED", "Updated");
define("NETCAT_MODERATION_STANDART_FIELD_LAST_USER_ID", "Last user ID");
define("NETCAT_MODERATION_STANDART_FIELD_LAST_USER", "Last user");
define("NETCAT_MODERATION_STANDART_FIELD_LAST_IP", "Last IP");
define("NETCAT_MODERATION_STANDART_FIELD_LAST_USER_AGENT", "Last user agent");

define("NETCAT_MODERATION_VERSION", "draft");
define("NETCAT_MODERATION_VERSION_NOT_FOUND", "draft not found");
define("NETCAT_SAVE_DRAFT", "Save draft");

# MODULE
define("NETCAT_MODULES", "Modules");
define("NETCAT_MODULES_TUNING", "Module settings");
define("NETCAT_MODULES_PARAM", "Condition");
define("NETCAT_MODULES_VALUE", "Value");
define("NETCAT_MODULES_ADDPARAM", "Add condition");
define("NETCAT_MODULE_INSTALLCOMPLIED", "Module installation is complete.");
define("NETCAT_MODULE_ALWAYS_LOAD", "Always load");
define("NETCAT_MODULE_ONOFF", "On/off");
define("NETCAT_MODULE_MODULE_UNCHECKED", "The module is off, his adjusting is not possible. You can switch on the module  in the <a href='".$ADMIN_PATH."modules/index.php'> list of modules. </a>");

# MODULE DEFAULT
define("NETCAT_MODULE_DEFAULT_DESCRIPTION", "You can place your own functions in  ".$SUB_FOLDER.$HTTP_ROOT_PATH."modules/default/function.inc.php. You may create a own scripts integrated with CMS. You may find a sample script in ".$SUB_FOLDER.$HTTP_ROOT_PATH."modules/default/index.php. In the bottom field you may define a module environment variables.");

#CODE MIRROR
define('NETCAT_SETTINGS_CODEMIRROR', 'Code Mirror');
define('NETCAT_SETTINGS_CODEMIRROR_EMBEDED', 'Embeded');
define('NETCAT_SETTINGS_CODEMIRROR_EMBEDED_ON', 'Yes');
define('NETCAT_SETTINGS_CODEMIRROR_DEFAULT', 'Highlight on default');
define('NETCAT_SETTINGS_CODEMIRROR_DEFAULT_ON', 'Yes');
define('NETCAT_SETTINGS_CODEMIRROR_AUTOCOMPLETE', 'Autocomplete');
define('NETCAT_SETTINGS_CODEMIRROR_AUTOCOMPLETE_ON', 'Yes');
define('NETCAT_SETTINGS_CODEMIRROR_HELP', 'Doc dialog box');
define('NETCAT_SETTINGS_CODEMIRROR_HELP_ON', 'Yes');
define('NETCAT_SETTINGS_CODEMIRROR_ENABLE', 'Enable editor');
define('NETCAT_SETTINGS_CODEMIRROR_SWITCH', 'Switch editor');
define('NETCAT_SETTINGS_CODEMIRROR_WRAP', 'Wrap lines');
define('NETCAT_SETTINGS_CODEMIRROR_FULLSCREEN', 'Fullscreen');

define('NETCAT_SETTINGS_DRAG', 'Drag and drop of elements (subdivisions, infoblocks, objects, components etc)');
define('NETCAT_SETTINGS_DRAG_SILENT', 'move without confirmation');
define('NETCAT_SETTINGS_DRAG_CONFIRM', 'ask for a confirmation before moving');
define('NETCAT_SETTINGS_DRAG_DISABLED', 'disable drag and drop');

# EDITOR
define('NETCAT_SETTINGS_EDITOR', 'Editor functions');
define('NETCAT_SETTINGS_EDITOR_TYPE', 'Type of HTML-editor');
define('NETCAT_SETTINGS_EDITOR_FCKEDITOR', 'FCKeditor');
define('NETCAT_SETTINGS_EDITOR_CKEDITOR', 'CKEditor');
define('NETCAT_SETTINGS_EDITOR_TINYMCE', 'TinyMCE');
define('NETCAT_SETTINGS_EDITOR_CKEDITOR_FILE_SYSTEM', 'Divide loadable files into user&#039;s individual directories');
define('NETCAT_SETTINGS_EDITOR_CKEDITOR_CYRILIC_FOLDER', 'Allow cyrillic symbols in folder names in file manager (CKEditor)');
define('NETCAT_SETTINGS_EDITOR_CKEDITOR_CONTENT_FILTER', 'Enable <a href="http://docs.ckeditor.com/#!/guide/dev_advanced_content_filter" target="_blank">content filter</a> (CKEditor)');
define('NETCAT_SETTINGS_EDITOR_EMBED_ON', 'Yes');
define('NETCAT_SETTINGS_EDITOR_EMBED_TO_FIELD', 'Embed editor into text area field');
define('NETCAT_SETTINGS_EDITOR_SEND', 'Send');
define('NETCAT_SETTINGS_EDITOR_STYLES_SAVE', 'Save changes');
define('NETCAT_SETTINGS_EDITOR_STYLES', 'Styles for editor');
define('NETCAT_SETTINGS_EDITOR_SKINS', 'Theme');
define('NETCAT_SETTINGS_EDITOR_ENTER_MODE', 'Enter key mode');
define('NETCAT_SETTINGS_EDITOR_ENTER_MODE_P', 'P tag');
define('NETCAT_SETTINGS_EDITOR_ENTER_MODE_BR', 'BR tag');
define('NETCAT_SETTINGS_EDITOR_ENTER_MODE_DIV', 'DIV tag');
define('NETCAT_SETTINGS_EDITOR_SAVE', 'Settings updated successfully');
define('NETCAT_SETTINGS_EDITOR_KEYCODE', 'You can save this form by pressing Ctrl + %s; page reload (Ctrl + F5) is required');

define('NETCAT_SEARCH_FIND_IT', 'Search');
define('NETCAT_SEARCH_ERROR', 'Search error.');

# JS settings
define('NETCAT_SETTINGS_JS', 'Scripts loader');
define('NETCAT_SETTINGS_JS_FUNC_NC_JS', 'Function nc_js()');
define('NETCAT_SETTINGS_JS_LOAD_JQUERY_DOLLAR', 'Load jQuery\'s object $');
define('NETCAT_SETTINGS_JS_LOAD_JQUERY_EXTENSIONS_ALWAYS', 'Always load jQuery\'s extensions');
define('NETCAT_SETTINGS_JS_LOAD_MODULES_SCRIPTS', 'Preload module\'s scripts');
define('NETCAT_SETTINGS_MINIFY_STATIC_FILES', 'Minify CSS and JS files in admin panel');

define('NETCAT_SETTINGS_TRASHBIN', 'Trash bin');
define('NETCAT_SETTINGS_TRASHBIN_USE', 'Use trash bin');

#Components
define('NETCAT_SETTINGS_COMPONENTS', 'Components');
define('NETCAT_SETTINGS_REMIND_SAVE', 'Remind to save (page refresh required Ctrl + F5)');
define('NETCAT_SETTINGS_PACKET_OPERATIONS', 'Enable packet operations with objects');
define('NETCAT_SETTINGS_TEXTAREA_RESIZE', 'Enable resizing of textarea on component edit pages');

define('NETCAT_SETTINGS_QUICKBAR', 'Netcat QuickBar');
define('NETCAT_SETTINGS_QUICKBAR_ENABLE', 'Enable for permitted users');
define('NETCAT_SETTINGS_QUICKBAR_ON', 'Yes');

# ALT ADMIN BLOCKS
define('NETCAT_SETTINGS_ALTBLOCKS', 'Alternative blocks of administration');
define('NETCAT_SETTINGS_ALTBLOCKS_ON', 'Yes');
define('NETCAT_SETTINGS_ALTBLOCKS_TEXT', 'User alternative blocks of administration');
define('NETCAT_SETTINGS_ALTBLOCKS_PARAMS', 'Additional parameters for delete (begin with &)');

define('NETCAT_SETTINGS_HTTP_PROXY', 'Use HTTP proxy server to connect to the update server');
define('NETCAT_SETTINGS_HTTP_PROXY_HOST', 'Proxy server IP address');
define('NETCAT_SETTINGS_HTTP_PROXY_PORT', 'Port');
define('NETCAT_SETTINGS_HTTP_PROXY_USER', 'User');
define('NETCAT_SETTINGS_HTTP_PROXY_PASSWORD', 'Password');

define('NETCAT_SETTINGS_USETOKEN', 'Use token');
define('NETCAT_SETTINGS_USETOKEN_ADD', 'add');
define('NETCAT_SETTINGS_USETOKEN_EDIT', 'edit');
define('NETCAT_SETTINGS_USETOKEN_DROP', 'drop');

define('NETCAT_SETTINGS_OBJECTS_FULLINK', 'Objects full view');
define("CONTROL_SETTINGSFILE_CHANGE_EMAILS_FIELD", "Field (with email) in user table");
define("CONTROL_SETTINGSFILE_CHANGE_EMAILS_NONE", "Field is undefined");
define('NETCAT_SETTINGS_CODEMIRROR_EMBEDED_OFF', 'No');
define('NETCAT_SETTINGS_CODEMIRROR_DEFAULT_OFF', 'No');
define('NETCAT_SETTINGS_CODEMIRROR_AUTOCOMPLETE_OFF', 'No');
define('NETCAT_SETTINGS_CODEMIRROR_HELP_OFF', 'No');
define('NETCAT_SETTINGS_INLINE_EDIT_CONFIRMATION', 'Confirm inline editor changes');
define('NETCAT_SETTINGS_INLINE_EDIT_CONFIRMATION_ON', 'Confirmation of inline editor changes enabled');
define('NETCAT_SETTINGS_INLINE_EDIT_CONFIRMATION_OFF', 'Confirmation of inline editor changes disabled');
define('NETCAT_SETTINGS_EDITOR_EMBEDED', 'Embed editor into text area field');
define('NETCAT_SETTINGS_EDITOR_EMBED_OFF', 'No');
define('NETCAT_SETTINGS_EDITOR_STYLES_CANCEL', 'Cancel');
define('NETCAT_SETTINGS_TRASHBIN_MAXSIZE', 'Maximum size of trash bin');
define('NETCAT_SETTINGS_REMIND_SAVE_INFO', 'Remind to save');
define('NETCAT_SETTINGS_PACKET_OPERATIONS_INFO', 'Enable packet operations with objects');
define('NETCAT_SETTINGS_TEXTAREA_RESIZE_INFO', 'Enable resizing of textarea on component edit pages');
define('NETCAT_SETTINGS_QUICKBAR_OFF', 'No');
define('NETCAT_SETTINGS_ALTBLOCKS_OFF', 'No');


# Export / Import
define('NETCAT_IMPORT_FIELD', 'XML file for import');


define('NETCAT_SITEINFO_LINK', 'View the site info');

define('NETCAT_FILEUPLOAD_ERROR', 'Error! You don\'t have permissions to access %s on this server.');


define("NETCAT_HTTP_REQUEST_SAVING", "Sending to server...");
define("NETCAT_HTTP_REQUEST_SAVED", "Changes were saved");
define("NETCAT_HTTP_REQUEST_ERROR", "Error saving data (<a href='javascript:showFormSaveError()'>details</a>)");
define("NETCAT_HTTP_REQUEST_HINT", "Press Ctrl + %s to save this form without page reload");

# Index page menu
define("SECTION_INDEX_MENU_TITLE", "Main menu");
define("SECTION_INDEX_MENU_HOME", "home");
define("SECTION_INDEX_MENU_SITE", "site");
define("SECTION_INDEX_MENU_DEVELOPMENT", "development");
define("SECTION_INDEX_MENU_TOOLS", "tools");
define("SECTION_INDEX_MENU_SETTINGS", "settings");
define("SECTION_INDEX_MENU_HELP", "help");

define("SECTION_INDEX_HELP_SUBMENU_HELP", "NetCat help");
define("SECTION_INDEX_HELP_SUBMENU_DOC", "Documentation");
define("SECTION_INDEX_HELP_SUBMENU_HELPDESC", "Online-help");
define("SECTION_INDEX_HELP_SUBMENU_FORUM", "Forum");
define("SECTION_INDEX_HELP_SUBMENU_BASE", "Knowledge base");
define("SECTION_INDEX_HELP_SUBMENU_ABOUT", "About");

define("SECTION_INDEX_SITE_LIST", "Sites");

define("SECTION_INDEX_WIZARD_SUBMENU_CLASS", "Component wizard");
define("SECTION_INDEX_WIZARD_SUBMENU_SITE", "Site wizard");

define("SECTION_INDEX_FAVORITE_ANOTHER_SUB", "Another section...");
define("SECTION_INDEX_FAVORITE_ADD", "Add into current menu");
define("SECTION_INDEX_FAVORITE_LIST", "Edit menu");
define("SECTION_INDEX_FAVORITE_SETTINGS", "Settings");

define("SECTION_INDEX_WELCOME", "Welcome");
define("SECTION_INDEX_WELCOME_MESSAGE", "Hello, %s!<br>You are in management system of &laquo;%s&raquo; project.<br>Your role is: %s.");
define("SECTION_INDEX_TITLE", "Content Management System NetCat");

# SITE
## TABS
define("SITE_TAB_SITEMAP", "Site map");
define("SITE_TAB_SETTINGS", "Settings");
define("SITE_TAB_SEO", "SEO-analysis");
define("SITE_TAB_STATS", "Statistics");
## TOOLBAR
define("SITE_TOOLBAR_INFO", "General Information");
define("SITE_TOOLBAR_SUBLIST", "Subdivision list");


# SUBDIVISION
## TABS
## TOOLBAR
define("SUBDIVISION_TAB_INFO_TOOLBAR_INFO", "Subdivision information");
define("SUBDIVISION_TAB_INFO_TOOLBAR_SUBLIST", "Subdivisions list");
define("SUBDIVISION_TAB_INFO_TOOLBAR_CCLIST", "Components list");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST", "Users");
define("SUBDIVISION_TAB_INFO_TOOLBAR_EDIT_EDIT", "Main");
define("SUBDIVISION_TAB_INFO_TOOLBAR_EDIT_DESIGN", "Design");
define("SUBDIVISION_TAB_INFO_TOOLBAR_EDIT_SEO", "SEO/SMO");
define("SUBDIVISION_TAB_INFO_TOOLBAR_EDIT_SYSTEM", "System");
define("SUBDIVISION_TAB_INFO_TOOLBAR_EDIT_FIELDS", "Fields");

## BUTTONS
define("SUBDIVISION_TAB_PREVIEW_BUTTON_PREVIEW", "Preview in new window");

define("SITE_SITEMAP_SEARCH", "Site map search");
define("SITE_SITEMAP_SEARCH_NOT_FOUND", "Not found");

## TEXT
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_READ_ACCESS", "Read access");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_COMMENT_ACCESS", "Comment access");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_WRITE_ACCESS", "Write access");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_EDIT_ACCESS", "Edit access");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_SUBSCRIBE_ACCESS", "Subscribe access");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_MODERATORS", "Moderators");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_ADMINS", "Administrators");

define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_ALL_USERS", "All users");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_REGISTERED_USERS", "Registered users");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_PRIVILEGED_USERS", "Privileged users");

# CLASS WIZARD

define("WIZARD_CLASS_FORM_SUBDIVISION_PARENTSUB", "Parent subdivision");

define("WIZARD_PARENTSUB_SELECT_POPUP_TITLE", "Parent subdivision selection");

define("WIZARD_CLASS_FORM_SUBDIVISION_SELECT_PARENTSUB", "select parent subdivision");
define("WIZARD_CLASS_FORM_SUBDIVISION_SELECT_SUBDIVISION", "select subdivision");
define("WIZARD_CLASS_FORM_SUBDIVISION_DELETE", "delete");

define("WIZARD_CLASS_FORM_START_TEMPLATE_TYPE", "Template type");
define("WIZARD_CLASS_FORM_START_TEMPLATE_TYPE_SINGLE", "Single object on the page");
define("WIZARD_CLASS_FORM_START_TEMPLATE_TYPE_LIST", "Object list");
define("WIZARD_CLASS_FORM_START_TEMPLATE_TYPE_SEARCH", "Object list search");
define("WIZARD_CLASS_FORM_START_TEMPLATE_TYPE_FORM", "Web form");

define("WIZARD_CLASS_FORM_SETTINGS_NO_SETTINGS", "There are no settings for this type of template.");
define("WIZARD_CLASS_FORM_SETTINGS_FIELDS_FOR_OBJECT_LIST", "Fields for object list");
define("WIZARD_CLASS_FORM_SETTINGS_SORT_OBJECT_BY_FIELD", "Sort objects by field");
define("WIZARD_CLASS_FORM_SETTINGS_SORT_ASC", "Ascending");
define("WIZARD_CLASS_FORM_SETTINGS_SORT_DESC", "Descending");
define("WIZARD_CLASS_FORM_SETTINGS_PAGE_NAVIGATION", "Page navigation");
define("WIZARD_CLASS_FORM_SETTINGS_PAGE_NAVIGATION_BY_NEXT_PREV", "Page navigation by next and previous links");
define("WIZARD_CLASS_FORM_SETTINGS_PAGE_NAVIGATION_BY_PAGE_NUMBER", "Page navigation by page numbers");
define("WIZARD_CLASS_FORM_SETTINGS_PAGE_NAVIGATION_BY_BOTH", "Page navigation by both links and page numbers");
define("WIZARD_CLASS_FORM_SETTINGS_LOCATION_OF_NAVIGATION", "Location of page navigation");
define("WIZARD_CLASS_FORM_SETTINGS_LOCATION_TOP", "Page top");
define("WIZARD_CLASS_FORM_SETTINGS_LOCATION_BOTTOM", "Page bottom");
define("WIZARD_CLASS_FORM_SETTINGS_LOCATION_BOTH", "Both page top and page bottom");
define("WIZARD_CLASS_FORM_SETTINGS_LIST_TYPE", "List");
define("WIZARD_CLASS_FORM_SETTINGS_TABLE_TYPE", "Table");
define("WIZARD_CLASS_FORM_SETTINGS_LIST_DELIMITER_TYPE", "List delimiter type");
define("WIZARD_CLASS_FORM_SETTINGS_TABLE_TYPE_SETTINGS", "Table type settings");
define("WIZARD_CLASS_FORM_SETTINGS_TABLE_BACKGROUND", "Alternate table rows background");
define("WIZARD_CLASS_FORM_SETTINGS_TABLE_BORDER", "Table border");
define("WIZARD_CLASS_FORM_SETTINGS_FULL_PAGE", "Full information page");
define("WIZARD_CLASS_FORM_SETTINGS_FULL_PAGE_LINK_TYPE", "Full information page link type");
define("WIZARD_CLASS_FORM_SETTINGS_CHECK_FIELDS_TO_FULL_PAGE", "Check fields that would link to full information page.");

define("WIZARD_CLASS_FORM_SETTINGS_FIELDS_FOR_OBJECT_SEARCH", "Object search fields");

define("WIZARD_CLASS_FORM_SETTINGS_FEEDBACK_FIELDS_SETTINGS", "Feedback fields settings");
define("WIZARD_CLASS_FORM_SETTINGS_FEEDBACK_SENDER_NAME", "Sender name");
define("WIZARD_CLASS_FORM_SETTINGS_FEEDBACK_SENDER_MAIL", "Sender Email");
define("WIZARD_CLASS_FORM_SETTINGS_FEEDBACK_SUBJECT", "Subject");

## TABS
define("WIZARD_CLASS_TAB_SELECT_TEMPLATE_TYPE", "Select template type");
define("WIZARD_CLASS_TAB_ADDING_TEMPLATE_FIELDS", "Adding template fields");
define("WIZARD_CLASS_TAB_TEMPLATE_SETTINGS", "Template settings");
define("WIZARD_CLASS_TAB_SUBSEQUENT_ACTION", "Subsequent action");
define("WIZARD_CLASS_TAB_CREATION_SUBDIVISION_WITH_NEW_TEMPLATE", "Creation subdivision with new template");
define("WIZARD_CLASS_TAB_ADDING_TEMPLATE_TO_EXISTENT_SUBDIVISION", "Adding template to existent subdivision");

## BUTTONS
define("WIZARD_CLASS_BUTTON_NEXT_TO_ADDING_FIELDS", "Next to adding fields");
define("WIZARD_CLASS_BUTTON_FINISH_ADDING_FIELDS", "Finish adding fields");
define("WIZARD_CLASS_BUTTON_SAVE_SETTINGS", "Save settings");
define("WIZARD_CLASS_BUTTON_ADDING_SUBDIVISION_WITH_NEW_TEMPLATE", "Add subdivision with new template");
define("WIZARD_CLASS_BUTTON_CREATE_SUBDIVISION_WITH_NEW_TEMPLATE", "Create subdivision with new template");
define("WIZARD_CLASS_BUTTON_NEXT_TO_SUBDIVISION_SELECTION", "Next to subdivision selection");

## LINKS
define("WIZARD_CLASS_LINKS_VIEW_TEMPLATE_CODE", "View template code");
define("WIZARD_CLASS_LINKS_CREATE_SUBDIVISION_WITH_NEW_TEMPLATE", "Create subdivision with new template");
define("WIZARD_CLASS_LINKS_ADD_TEMPLATE_TO_EXISTENT_SUBDIVISION", "Add template to existent subdivision");
define("WIZARD_CLASS_LINKS_CREATE_NEW_TEMPLATE", "Create new template");

define("WIZARD_CLASS_LINKS_RETURN_TO_FIELDS_ADDING", "Return to fields adding");

## COMMON
define("WIZARD_CLASS_STEP", "Step");
define("WIZARD_CLASS_STEP_FROM", "from");

define("WIZARD_CLASS_DEFAULT", "default");

define("WIZARD_CLASS_ERROR_NO_FIELDS", "You must add one field in template at least!");

# SITE WIZARD
define("WIZARD_SITE_FORM_WHICH_MODULES", "Which modules do you want to use?");

## TABS
define("WIZARD_SITE_TAB_NEW_SITE_CREATION", "Creation of new site");
define("WIZARD_SITE_TAB_NEW_SITE_ADD_SUB", "Adding subdivisions");

## BUTTONS
define("WIZARD_SITE_BUTTON_ADD_SUBS", "Add subdivisions");
define("WIZARD_SITE_BUTTON_FINISH_ADD_SUBS", "Finish adding");

## COMMON
define("WIZARD_SITE_STEP", "Step");
define("WIZARD_SITE_STEP_FROM", "from");
define("WIZARD_SITE_STEP_TWO_DESCRIPTION", "Creation of service divisions. There must be front page and &#034;not found&#034; page on every site. You may leave these fields without changing.");

#DEMO MODE
define("DEMO_MODE_ADMIN_INDEX_MESSAGE", "Site \"%s\" is in demo-mode, you can turn it off in <a href='%s'>system site settings</a>.");
define("DEMO_MODE_FRONT_INDEX_MESSAGE_GUEST", "It's demo site only for demonstration.");
define("DEMO_MODE_FRONT_INDEX_MESSAGE_ADMIN", "This site is in demo-mode, you can turn it off <a href='%s'>in settings panel</a>.");
define("DEMO_MODE_FRONT_INDEX_MESSAGE_CLOSE", "Close");

# FAVORITE
## HEADER TEXT
define("FAVORITE_HEADERTEXT", "Favorites");


# CRONTAB
##TABS
define("CRONTAB_TAB_LIST", "Task manager");
define("CRONTAB_TAB_ADD", "Add task");
define("CRONTAB_TAB_EDIT", "Edit task");

##TRASH
define("TRASH_TAB_LIST", "Trash bin");
define("TRASH_TAB_TITLE", "Trash list");
define("TRASH_TAB_SETTINGS", "Settings");

# REDIRECT
##TABS
define("REDIRECT_TAB_LIST", "Redirect");
define("REDIRECT_TAB_ADD", "Add redirect");
define("REDIRECT_TAB_EDIT", "Edit redirect");


# SYSTEM SETTINGS
##TABS
define("SYSTEMSETTINGS_TAB_LIST", "General Settings");
define("SYSTEMSETTINGS_TAB_ADD", "Edit system settings");
define("SYSTEMSETTINGS_SAVE_OK", "Settings saved");
define("SYSTEMSETTINGS_SAVE_ERROR", "Error");

# WYSIWYG SETTINGS
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TAB_SETTINGS", "Settings");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TAB_PANELS", "Panels");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TITLE_SETTINGS", "WYSIWYG settings");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TITLE_PANELS", "WYSIWYG editor panels");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TITLE_DELETE_CONFIRMATION", "Confirmation of deleting WYSIWYG editor panels");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TITLE_EDIT_FORM", " - WYSIWYG panel edit");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TITLE_ADD_FORM", "WYSIWYG panel add");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANEL_NOT_EXISTS", "Panel not exists");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_NO_PANELS", "No panels");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANEL_EDIT_SUCCESSFUL", "Panel updated successful");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANEL_ADD_SUCCESSFUL", "Panel added successful");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANELS_NOT_SELECTED", "Panels not selected");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANELS_DELETED", "Panels deleted successful");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANELS_DELETE_ERROR", "Panels not deleted");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_FILL_PANEL_NAME", "Fill panel name");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_SELECT_TOOLBAR", "Select at least one toolbar");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_BUTTON_DELETE_SELECTED", "Delete selected");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_BUTTON_CONFIRM_DELETE", "Confirm delete");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_BUTTON_CANCEL", "Cancel");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_BUTTON_EDIT_PANEL", "Edit panel");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_BUTTON_ADD_PANEL", "Add panel");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_PANEL_NAME", "Panel name");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_PANEL_PREVIEW", "Preview");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_DELETE", "Delete");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_ARE_YOU_REALLY_WANT_TO_DELETE_PANELS", "Are you really want to delete panels?");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_EDITOR_PANEL_FULL", "Full edit panel");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_EDITOR_PANEL_INLINE", "Inline edit panel");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_PANEL_NAME", "Panel name");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_SETTINGS", "Toolbars settings");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_MODE", "Document mode");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_DOCUMENT", "Document operations");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_TOOLS", "Tools");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_DOCTOOLS", "Templates");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_CLIPBOARD", "Clipboard");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_UNDO", "Undo actions");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_FIND", "Find and replace");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_SELECTION", "Selection");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_SPELLCHECKER", "Spellchecker");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_FORMS", "Forms");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_BASICSTYLES", "Basic styles");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_CLEANUP", "Format cleanup");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_LIST", "Lists");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_INDENT", "Indents");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_BLOCKS", "Text blocks");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_ALIGN", "Align");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_LINKS", "Links");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_INSERT", "Insert objects");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_STYLES", "Styles");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_COLORS", "Colors");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_OTHERS", "Others");

define("NETCAT_WYSIWYG_FCKEDITOR_SETTINGS_TITLE_SETTINGS", "WYSIWYG settings");

define("NETCAT_WYSIWYG_SETTINGS_PANEL_SETTINGS", "Panels settings");
define("NETCAT_WYSIWYG_SETTINGS_THIS_EDITOR_IS_USED_BY_DEFAULT", "This editor is used by default");
define("NETCAT_WYSIWYG_SETTINGS_USE_BY_DEFAULT", "Use this editor by default");
define("NETCAT_WYSIWYG_SETTINGS_BASIC_SETTINGS", "Basic settings");
define("NETCAT_WYSIWYG_SETTINGS_MESSAGE_EDITOR_ACTIVATED", "Editor activated successful");
define("NETCAT_WYSIWYG_SETTINGS_PANEL_NOT_SELECTED", "Not selected");
define("NETCAT_WYSIWYG_SETTINGS_BUTTON_SAVE", "Save");
define("NETCAT_WYSIWYG_SETTINGS_MESSAGE_SETTINGS_SAVED", "WYSIWYG settings saved");
define("NETCAT_WYSIWYG_SETTINGS_MESSAGE_SETTINGS_SAVE_ERROR", "WYSIWYG settings not saved");
define("NETCAT_WYSIWYG_SETTINGS_CONFIG_JS_SETTINGS", "config.js settings");
define("NETCAT_WYSIWYG_SETTINGS_CONFIG_JS_FILE", "config.js file");

define("NETCAT_WYSIWYG_EDITOR_OUTWORN", "This editor is out of date, be sure to switch to a different editor and delete directory %s from your server");

# Not Elsewhere Specified
define("NOT_ELSEWHERE_SPECIFIED", "Not Elsewhere Specified");
define("NOT_ADD_CLASS", "Do not add infoblock");


# BBcodes
define("NETCAT_BBCODE_SIZE", "Font size");
define("NETCAT_BBCODE_COLOR", "Font color");
define("NETCAT_BBCODE_SMILE", "Smiles");
define("NETCAT_BBCODE_B", "Bold");
define("NETCAT_BBCODE_I", "Italic");
define("NETCAT_BBCODE_U", "Underline");
define("NETCAT_BBCODE_S", "Strike");
define("NETCAT_BBCODE_LIST", "List element");
define("NETCAT_BBCODE_QUOTE", "Quote");
define("NETCAT_BBCODE_CODE", "Code");
define("NETCAT_BBCODE_IMG", "Picture");
define("NETCAT_BBCODE_URL", "Link");
define("NETCAT_BBCODE_CUT", "Cut text in listing");

define("NETCAT_BBCODE_QUOTE_USER", "wrote");
define("NETCAT_BBCODE_CUT_MORE", "read more");
define("NETCAT_BBCODE_SIZE_DEF", "Size");
define("NETCAT_BBCODE_ERROR_1", "wrong format BBcode:");
define("NETCAT_BBCODE_ERROR_2", "wrong format BBcodes:");

# Help messages for BBcode
define("NETCAT_BBCODE_HELP_SIZE", "Font size: [SIZE=8]small text[/SIZE]");
define("NETCAT_BBCODE_HELP_COLOR", "Font color: [COLOR=FF0000]text[/COLOR]");
define("NETCAT_BBCODE_HELP_SMILE", "Insert smile");
define("NETCAT_BBCODE_HELP_B", "Bold text: [B]text[/B]");
define("NETCAT_BBCODE_HELP_I", "Italic text: [I]text[/I]");
define("NETCAT_BBCODE_HELP_U", "Underline text: [U]text[/U]");
define("NETCAT_BBCODE_HELP_S", "Strike text: [S]text[/S]");
define("NETCAT_BBCODE_HELP_LIST", "List element: [LIST]text[/LIST]");
define("NETCAT_BBCODE_HELP_QUOTE", "Quote text: [QUOTE]text[/QUOTE]");
define("NETCAT_BBCODE_HELP_CODE", "Code display: [CODE]code[/CODE]");
define("NETCAT_BBCODE_HELP_URL", "Insert link");
define("NETCAT_BBCODE_HELP_URL_URL", "URL");
define("NETCAT_BBCODE_HELP_URL_DESC", "Description");
define("NETCAT_BBCODE_HELP_IMG", "Insert image");
define("NETCAT_BBCODE_HELP_IMG_URL", "Image URL");
define("NETCAT_BBCODE_HELP_CUT", "Cut big text in listing: [CUT=more]text[/CUT]");
define("NETCAT_BBCODE_HELP", "Tip: Styles can be applied quickly to selected text");

# Smiles
define("NETCAT_SMILE_SMILE", "smile");
define("NETCAT_SMILE_BIGSMILE", "big smile");
define("NETCAT_SMILE_GRIN", "grin");
define("NETCAT_SMILE_LAUGH", "laugh");
define("NETCAT_SMILE_PROUD", "proud");
#
define("NETCAT_SMILE_YES", "yes");
define("NETCAT_SMILE_WINK", "winked");
define("NETCAT_SMILE_COOL", "cool");
define("NETCAT_SMILE_ROLLEYES", "innocent");
define("NETCAT_SMILE_LOOKDOWN", "shame");
#
define("NETCAT_SMILE_SAD", "sad");
define("NETCAT_SMILE_SUSPICIOUS", "suspicious");
define("NETCAT_SMILE_ANGRY", "angry");
define("NETCAT_SMILE_SHAKEFIST", "threaten");
define("NETCAT_SMILE_STERN", "stern");
#
define("NETCAT_SMILE_KISS", "kiss");
define("NETCAT_SMILE_THINK", "think");
define("NETCAT_SMILE_THUMBSUP", "thumbs up");
define("NETCAT_SMILE_SICK", "sick");
define("NETCAT_SMILE_NO", "no");
#
define("NETCAT_SMILE_CANTLOOK", "can't look");
define("NETCAT_SMILE_DOH", "ooo");
define("NETCAT_SMILE_KNOCKEDOUT", "knocked out");
define("NETCAT_SMILE_EYEUP", "eye up");
define("NETCAT_SMILE_QUIET", "quiet");
#
define("NETCAT_SMILE_EVIL", "evil");
define("NETCAT_SMILE_UPSET", "upset");
define("NETCAT_SMILE_UNDECIDED", "undecided");
define("NETCAT_SMILE_CRY", "cry");
define("NETCAT_SMILE_UNSURE", "unsure");

# nc_bytes2size
define("NETCAT_SIZE_BYTES", " byte");
define("NETCAT_SIZE_KBYTES", " KB");
define("NETCAT_SIZE_MBYTES", " MB");
define("NETCAT_SIZE_GBYTES", " GB");

// quickBar
define("NETCAT_QUICKBAR_BUTTON_VIEWMODE", "View mode");
define("NETCAT_QUICKBAR_BUTTON_EDITMODE", "Edit mode");
define("NETCAT_QUICKBAR_BUTTON_EDITMODE_UNAVAILABLE_FOR_LONGPAGE", "Edit mode unavailable in longpage");
define("NETCAT_QUICKBAR_BUTTON_MORE", "more");
define("NETCAT_QUICKBAR_BUTTON_SUBDIVISION_SETTINGS", "Subdivision settings");
define("NETCAT_QUICKBAR_BUTTON_TEMPLATE_SETTINGS", "Template settings");
define("NETCAT_QUICKBAR_BUTTON_ADMIN", "Admin mode");

#SYNTAX EDITOR
define('NETCAT_SETTINGS_SYNTAXEDITOR', 'Online syntax highlighting');
define('NETCAT_SETTINGS_SYNTAXEDITOR_ENABLE', 'Enable system syntax highlighting (need frame reload Ctrl + F5)');

#SYNTAX CHECK

# LICENSE
define('NETCAT_SETTINGS_LICENSE', 'License');
define('NETCAT_SETTINGS_LICENSE_PRODUCT', 'Product number');

# NETCAT_DEBUG
define("NETCAT_DEBUG_ERROR_INSTRING", "Error in string : ");
define("NETCAT_DEBUG_BUTTON_CAPTION", "Debug");

# NETCAT_PREVIEW
define("NETCAT_PREVIEW_BUTTON_CAPTIONCLASS", "Class preview");
define("NETCAT_PREVIEW_BUTTON_CAPTIONTEMPLATE", "Template preview");

define("NETCAT_PREVIEW_BUTTON_CAPTIONADDFORM", "Add Form preview ");
define("NETCAT_PREVIEW_BUTTON_CAPTIONEDITFORM", "Edit Form preview");
define("NETCAT_PREVIEW_BUTTON_CAPTIONSEARCHFORM", "Search Form preview");

define("NETCAT_PREVIEW_ERROR_NODATA", "Error ! There are no data for generating preview mode or data too old");
define("NETCAT_PREVIEW_ERROR_WRONGDATA", "Error in preview data");
define("NETCAT_PREVIEW_ERROR_NOSUB", " There is no any subdivision with such class. Add at least one and preview mode will be available.");
define("NETCAT_PREVIEW_ERROR_NOMESSAGE", " There is no any object of such class. Add at least one object and preview mode will be available.");
define("NETCAT_PREVIEW_INFO_MORESUB", " There are some subdivisions with such class. Please choose one.");
define("NETCAT_PREVIEW_INFO_CHOOSESUB", " Select to preview the layout.");

# objects
define("NETCAT_FUNCTION_OBJECTS_LIST_SQL_ERROR_SUPERVISOR", "Error in SQL query into the nc_objects_list(%s, %s, \"%s\") function, %s");
define("NETCAT_FUNCTION_OBJECTS_LIST_SQL_ERROR_USER", "Error into the objects list function.");
define("NETCAT_FUNCTION_OBJECTS_LIST_CLASSIFICATOR_ERROR", "Classificator \"%s\" does't exist");
define("NETCAT_FUNCTION_OBJECTS_LIST_SQL_COLUMN_ERROR_UNKNOWN", "unknown column \"%s\" in field list");
define("NETCAT_FUNCTION_OBJECTS_LIST_SQL_COLUMN_ERROR_CLAUSE", "unknown column \"%s\" in order clause");
define("NETCAT_FUNCTION_OBJECTS_LIST_CC_ERROR", "Wrong parameter \$cc into the nc_objects_list(XX, %s, \"...\") function");
define("NETCAT_FUNCTION_LISTCLASSVARS_ERROR_SUPERVISOR", "Wrong parameter \$cc into the ListClassVars(%s) function");
define("NETCAT_FUNCTION_FULL_SQL_ERROR_USER", "Error in the function of the full object display .");

# events





// widgets events

define("NETCAT_TOKEN_INVALID", "Invalid confirmation key");

// seo
define("NETCAT_MODULE_AUDITOR_TITLE", "Site information");
define("NETCAT_MODULE_AUDITOR_NO_URL", "Please enter URL!");
define("NETCAT_MODULE_AUDITOR_WRONG_URL", "Wrong URL");
define("NETCAT_MODULE_AUDITOR_WAIT", "Loading data...");
define("NETCAT_MODULE_AUDITOR_DONE", "Loading of data is completed.");
define("NETCAT_MODULE_AUDITOR_URL", "Site URL");
define("NETCAT_MODULE_AUDITOR_GROUP_CY", "Citation index");
define("NETCAT_MODULE_AUDITOR_GROUP_INDEXED", "Number of indexed pages");
define("NETCAT_MODULE_AUDITOR_GROUP_LINKS", "Number of links to a site");
define("NETCAT_MODULE_AUDITOR_GROUP_CATALOGUE", "Presence in catalogues");
define("NETCAT_MODULE_AUDITOR_GROUP_STAT", "Statistics at the day");
define("NETCAT_MODULE_AUDITOR_GO", " Check ");
define("NETCAT_MODULE_AUDITOR_STOP", "Stop");
define("NETCAT_MODULE_AUDITOR_APORT", "Aport");
define("NETCAT_MODULE_AUDITOR_SPYLOG", "SpyLog");
define("NETCAT_MODULE_AUDITOR_YANDEX", "Yandex");

// РџРѕРґСЃРєР°Р·РєРё РІ СЃРїР»С‹РІР°СЋС‰Р°С… РѕРєРЅР°С…
define("NETCAT_HINT_COMPONENT_FIELD", "Fields of the component");
define("NETCAT_HINT_COMPONENT_SIZE", "Size");
define("NETCAT_HINT_COMPONENT_TYPE", "Type");
define("NETCAT_HINT_COMPONENT_ID", "ID");
define("NETCAT_HINT_COMPONENT_DAY", "The numerical value of the day");
define("NETCAT_HINT_COMPONENT_MONTH", "The numerical value of the month");
define("NETCAT_HINT_COMPONENT_YEAR", "The numerical value of the year");
define("NETCAT_HINT_COMPONENT_HOUR", "The numerical value of the hour");
define("NETCAT_HINT_COMPONENT_MINUTE", "The numerical value of the minute");
define("NETCAT_HINT_COMPONENT_SECONDS", "The numerical value of the second");
define("NETCAT_HINT_OBJECT_PARAMS", "Variables that containing properties of the current object");
define("NETCAT_HINT_CREATED_DESC", "details of time adding the object format &laquo;yyyy-mm-dd hh:mm:ss&raquo;");
define("NETCAT_HINT_LASTUPDATED_DESC", "details of the last change of the object in the format &laquo;yyyymmddhhmmss&raquo;");
define("NETCAT_HINT_MESSAGE_ID", "ID of the object");
define("NETCAT_HINT_USER_ID", "ID of the user who added the object");
define("NETCAT_HINT_CHECKED", "switched on or off site (0/1)");
define("NETCAT_HINT_IP", "IP-address of the user who added the object");
define("NETCAT_HINT_USER_AGENT", "value of the variable \$ HTTP_USER_AGENT for the user, who added an object");
define("NETCAT_HINT_LAST_USER_ID", "ID of the last user who modified the object");
define("NETCAT_HINT_LAST_USER_IP", "IP-address of the user who last changed the object");
define("NETCAT_HINT_LAST_USER_AGENT", "value of the variable \$ HTTP_USER_AGENT for the last user, who changed the object");
define("NETCAT_HINT_ADMIN_BUTTONS", "in administrative mode block contains status information about the record and links to action for this record&laquo;change&raquo;, &laquo;delete&raquo;, &laquo;turn on / off&raquo; (only in the field &laquo;Object list&raquo;)");
define("NETCAT_HINT_ADMIN_COMMONS", "in administrative mode block contains status information about the template and add link  to an object in the template section and remove all objects from the same template (only in the &laquo;Object in list&raquo;)");
define("NETCAT_HINT_FULL_LINK", "link to layout the complete withdrawal of this record");
define("NETCAT_HINT_FULL_DATE_LINK", "link to layout the full withdrawal from the date in the form of &laquo;.../2002/02/02/message_2.html &raquo;(installed if the template has a field type &laquo; Date and Time format &laquo;event&raquo;, otherwise variable is identical to the value of \$fullLink)");
define("NETCAT_HINT_EDIT_LINK", "link to edit an object");
define("NETCAT_HINT_DELETE_LINK", "link to remove an object");
define("NETCAT_HINT_DROP_LINK", "link to delete an object without asking");
define("NETCAT_HINT_CHECKED_LINK", "link to the on/off an object");
define("NETCAT_HINT_PREV_LINK", "link to the previous page in Listing template (if the current position in the list - its beginning, then the variable is empty)");
define("NETCAT_HINT_NEXT_LINK", "link to the next page in Listing template (if the current position in the list - its beginning, then the variable is empty)");
define("NETCAT_HINT_ROW_NUM", "record number in the order listed on the current page");
define("NETCAT_HINT_REC_NUM", "maximum number of entries displayed in the list");
define("NETCAT_HINT_TOT_ROWS", "the total number of entries in the list");
define("NETCAT_HINT_BEG_ROW", "record number (in order), which begins listing the list on this page");
define("NETCAT_HINT_END_ROW", "record number (in order), which ends with a list of listing on this page");
define("NETCAT_HINT_ADMIN_MODE", "true if the user is in the administrative mode");
define("NETCAT_HINT_SUB_HOST", "address the current domain as &laquo;www.example.com&raquo;");
define("NETCAT_HINT_SUB_LINK", "path to the current sub as &laquo;/about/pr/&raquo;");
define("NETCAT_HINT_CC_LINK", "link for the current component in the sub as &laquo;news.html&raquo;");
define("NETCAT_HINT_CATALOGUE_ID", "Number of the current directory");
define("NETCAT_HINT_SUB_ID", "Number of the current sub");
define("NETCAT_HINT_CC_ID", "Number of the current component in the sub");
define("NETCAT_HINT_CURRENT_CATALOGUE", "Contain the property values of the current directory");
define("NETCAT_HINT_CURRENT_SUB", "Contain the property values of the current sub");
define("NETCAT_HINT_CURRENT_CC", "It contains the property values of the current component in the sub");
define("NETCAT_HINT_CURRENT_USER", "Contain the property values of the current authorized user.");
define("NETCAT_HINT_IS_EVEN", "Checks the value parity");
define("NETCAT_HINT_OPT", "Function opt() prints \$string if \$flag - true");
define("NETCAT_HINT_OPT_CAES", "Function opt_case() prints \$string1, if \$flag true, and \$string2, if \$flag false");
define("NETCAT_HINT_LIST_QUERY", "The function is intended to perform SQL-query \$sql_query. To request a type of SELECT (or for other cases, invented by the developer) is used \$output_template to display the results of sampling. \$output_template is optional. <br /> Last parameter should contain a call to a hash array \$data, indices of which correspond to the table fields (dollar sign and double quotes must be escaped). \$divider to split the results output.");
define("NETCAT_HINT_NC_LIST_SELECT", "This function allows you to generate HTML lists of Lists NetCat");
define("NETCAT_HINT_NC_MESSAGE_LINK", "This function allows you to get the relative path to the object (without domain) by number (ID) of the site and number (ID) component, to which he belongs");
define("NETCAT_HINT_NC_FILE_PATH", "This function allows you to get the file path specified in a particular field, by number (ID) of the site and number (ID) component, to which he belongs");
define("NETCAT_HINT_BROWSE_MESSAGE", "The function displays a listing of pages");
define("NETCAT_HINT_NC_OBJECTS_LIST", "Displays contents of the component in the section \$cc partition \$sub with parameters \$params as parameters, fed to the scripts in the URL line");
define("NETCAT_HINT_RTFM", "All available variables and functions can be found in Developer's Guide.");
define("NETCAT_HINT_FUNCTION", "Functions.");
define("NETCAT_HINT_ARRAY", "Hash-arrays");
define("NETCAT_HINT_VARS_IN_COMPONENT_SCOPE", "Variables are available in all fields");
define("NETCAT_HINT_VARS_IN_LIST_SCOPE", "Variables available in the object list template");
define("NETCAT_HINT_FIELD_D", "DD");
define("NETCAT_HINT_FIELD_M", "MM");
define("NETCAT_HINT_FIELD_Y", "YYYY");
define("NETCAT_HINT_FIELD_H", "hh");
define("NETCAT_HINT_FIELD_MIN", "mm");
define("NETCAT_HINT_FIELD_S", "ss");

define("NETCAT_CUSTOM_ERROR_REQUIRED_INT", "Enter a integer");
define("NETCAT_CUSTOM_ERROR_REQUIRED_FLOAT", "Enter a float");
define("NETCAT_CUSTOM_ERROR_MIN_VALUE", "Min value: %s.");
define("NETCAT_CUSTOM_ERROR_MAX_VALUE", "Max value: %s.");
define("NETCAT_CUSTOM_PARAMETR_UPDATED", "Settings updated");
define("NETCAT_CUSTOM_PARAMETR_ADDED", "Parameter added");
define("NETCAT_CUSTOM_KEY", "key");
define("NETCAT_CUSTOM_VALUE", "value");
define("NETCAT_CUSTOM_USETTINGS", "Custom settings");
define("NETCAT_CUSTOM_NONE_SETTINGS", "None");
define("NETCAT_CUSTOM_ONCE_MAIN_SETTINGS", "Main settings");
define("NETCAT_CUSTOM_ONCE_FIELD_NAME", "Field name");
define("NETCAT_CUSTOM_ONCE_FIELD_DESC", "Description");
define("NETCAT_CUSTOM_ONCE_DEFAULT", "Default value (when field is not filled");
define("NETCAT_CUSTOM_ONCE_FIELD_INITIAL_VALUE_INFOBLOCK", "Initial value after infoblock creation");
define("NETCAT_CUSTOM_ONCE_FIELD_INITIAL_VALUE_SUBDIVISION", "Initial value after subdivision creation");
define("NETCAT_CUSTOM_ONCE_EXTEND", "Extend parameters");
define("NETCAT_CUSTOM_ONCE_EXTEND_REGEXP", "Regular expression");
define("NETCAT_CUSTOM_ONCE_EXTEND_ERROR", "Error message");
define("NETCAT_CUSTOM_ONCE_EXTEND_SIZE_L", "Input field width");
define("NETCAT_CUSTOM_ONCE_EXTEND_RESIZE_W", "Automatically resize field width");
define("NETCAT_CUSTOM_ONCE_EXTEND_RESIZE_H", "Automatically resize field height");
define("NETCAT_CUSTOM_ONCE_EXTEND_VIZRED", "Allow WYSIWYG editor");
define("NETCAT_CUSTOM_ONCE_EXTEND_BR", "Line break - &lt;br>");
define("NETCAT_CUSTOM_ONCE_EXTEND_SIZE_H", "Input field height");
define("NETCAT_CUSTOM_ONCE_SAVE", "Save");
define("NETCAT_CUSTOM_ONCE_ADD", "Add");
define("NETCAT_CUSTOM_ONCE_DROP", "Delete");
define("NETCAT_CUSTOM_ONCE_DROP_SELECTED", "Delete selected");
define("NETCAT_CUSTOM_ONCE_MANUAL_EDIT", "Manual edit");
define("NETCAT_CUSTOM_ONCE_ERROR_FIELD_NAME", "Field name can contain only latin characters");
define("NETCAT_CUSTOM_ONCE_ERROR_CAPTION", "Filed description");
define("NETCAT_CUSTOM_ONCE_ERROR_FIELD_EXISTS", "Such parameter already exists");
define("NETCAT_CUSTOM_ONCE_ERROR_QUERY", "SQL error");
define("NETCAT_CUSTOM_ONCE_ERROR_CLASSIFICATOR", "Classificator %s doesn't exist");
define("NETCAT_CUSTOM_ONCE_ERROR_CLASSIFICATOR_EMPTY", "Classificator %s is empty");
define("NETCAT_CUSTOM_TYPE", "Type");
define("NETCAT_CUSTOM_SUBTYPE", "Subtype");
define("NETCAT_CUSTOM_EX_MIN", "Min value");
define("NETCAT_CUSTOM_EX_MAX", "Max value");
define("NETCAT_CUSTOM_EX_QUERY", "SQL query");
define("NETCAT_CUSTOM_EX_CLASSIFICATOR", "List");
define("NETCAT_CUSTOM_EX_ELEMENTS", "Elements");
define("NETCAT_CUSTOM_TYPENAME_STRING", "String");
define("NETCAT_CUSTOM_TYPENAME_TEXTAREA", "Text");
define("NETCAT_CUSTOM_TYPENAME_CHECKBOX", "Boolean");
define("NETCAT_CUSTOM_TYPENAME_SELECT", "List");
define("NETCAT_CUSTOM_TYPENAME_SELECT_SQL", "Dynamic");
define("NETCAT_CUSTOM_TYPENAME_SELECT_STATIC", "Static");
define("NETCAT_CUSTOM_TYPENAME_SELECT_CLASSIFICATOR", "Classificator");
define("NETCAT_CUSTOM_TYPENAME_DIVIDER", "Divider");
define("NETCAT_CUSTOM_TYPENAME_INT", "Integer");
define("NETCAT_CUSTOM_TYPENAME_FLOAT", "Float");
define("NETCAT_CUSTOM_TYPENAME_DATETIME", "Date and time");
define("NETCAT_CUSTOM_TYPENAME_REL", "Relation");
define("NETCAT_CUSTOM_TYPENAME_REL_SUB", "Relation with subdivision");
define("NETCAT_CUSTOM_TYPENAME_REL_CC", "Relation with subclass");
define("NETCAT_CUSTOM_TYPENAME_REL_USER", "Relation with user");
define("NETCAT_CUSTOM_TYPENAME_REL_CLASS", "Relation with component");
define("NETCAT_CUSTOM_TYPENAME_FILE", "File");
define("NETCAT_CUSTOM_TYPENAME_FILE_ANY", "File");
define("NETCAT_CUSTOM_TYPENAME_FILE_IMAGE", "Image");
define("NETCAT_CUSTOM_TYPENAME_COLOR", "Color");
define("NETCAT_CUSTOM_TYPENAME_COLOR_TRANSPARENT", "No color");

#exceptions
define("NETCAT_EXCEPTION_CLASS_DOESNT_EXIST", "Component %s not found");
#errors
define("NETCAT_ERROR_SQL", "Error in SQL-query.<br/>Query:%s<br/>Error:%s");
define("NETCAT_ERROR_DB_CONNECT", "Fatal error: cannot retrieve system settings. Please check database settings in configuration file.");
define("NETCAT_ERROR_UNABLE_TO_DELETE_FILES", "Unable to delete a file or directory %s");

#openstat

# admin notice
define("NETCAT_ADMIN_NOTICE_MORE", "More.");
define("NETCAT_ADMIN_NOTICE_PROLONG", "Prolong.");
define("NETCAT_ADMIN_NOTICE_LICENSE_ILLEGAL", "This copy NetCat is not licensed.");
define("NETCAT_ADMIN_NOTICE_LICENSE_MAYBE_ILLEGAL", "You may have used unlicensed copy NetCat.");
define("NETCAT_ADMIN_NOTICE_SECURITY_UPDATE_SYSTEM", "Came an important security update system.");
define("NETCAT_ADMIN_NOTICE_SUPPORT_EXPIRED", "Support for license %s expired.");
define("NETCAT_ADMIN_NOTICE_CRON", "You have not used the \"CronTasking\". <a href='https://netcat.ru/developers/docs/system-tools/task-management/' target='_blank'>More</a>");
define("NETCAT_ADMIN_NOTICE_RIGHTS", "Directory permissions are wrong");
define("NETCAT_ADMIN_NOTICE_SAFE_MODE", "Option php safe_mode is active. <a href='https://netcat.ru/adminhelp/safe-mode/' target='_blank'>More</a>");
define('NETCAT_ADMIN_DOMDocument_NOT_FOUND', 'DOMDocument PHP extension is not found, can not work basket');
define('NETCAT_ADMIN_TRASH_OBJECT_HAS_BEEN_REMOVED', 'object has been deleted');
define('NETCAT_ADMIN_TRASH_OBJECTS_REMOVED', 'objects removed');
define('NETCAT_ADMIN_TRASH_OBJECT_IS_REMOVED', 'object is removed');
define('NETCAT_ADMIN_TRASH_TRASH_HAS_BEEN_SUCCESSFULLY_CLEARNED', 'Cart has been successfully cleared');

define('NETCAT_FILE_NOT_FOUND', 'File %s not found');
define('NETCAT_DIR_NOT_FOUND', 'Dir %s not found');

define('NETCAT_TEMPLATE_FILE_NOT_FOUND', 'Template file not found');
define('NETCAT_TEMPLATE_DIR_DELETE_ERROR', 'It is not possible to delete %s');
define('NETCAT_CAN_NOT_WRITE_FILE', "Can't write file");
define('NETCAT_CAN_NOT_CREATE_FOLDER', "Can't create folder");


define('NETCAT_ADMIN_AUTH_PERM', 'Role:');
define('NETCAT_ADMIN_AUTH_CHANGE_PASS', 'Change password');
define('NETCAT_ADMIN_AUTH_LOGOUT', 'Logout');

define("CONTROL_BUTTON_CANCEL", "Cancel");

define("NETCAT_MESSAGE_FORM_MAIN", "Main settings");
define("NETCAT_MESSAGE_FORM_ADDITIONAL", "Additional settings");
define("NETCAT_EVENT_ADDCATALOGUE", "Adding a site");
define("NETCAT_EVENT_ADDSUBDIVISION", "Adding a subdivision");
define("NETCAT_EVENT_ADDSUBCLASS", "Adding a component into the subdivision");
define("NETCAT_EVENT_ADDCLASS", "Adding a component");
define("NETCAT_EVENT_ADDCLASSTEMPLATE", "Adding a components template");
define("NETCAT_EVENT_ADDMESSAGE", "Adding a message");
define("NETCAT_EVENT_ADDSYSTEMTABLE", "Adding a system table");
define("NETCAT_EVENT_ADDTEMPLATE", "Adding a template");
define("NETCAT_EVENT_ADDUSER", "Adding a user");
define("NETCAT_EVENT_ADDCOMMENT", "Adding comment");
define("NETCAT_EVENT_UPDATECATALOGUE", "Updating a site");
define("NETCAT_EVENT_UPDATESUBDIVISION", "Updating a subdivision");
define("NETCAT_EVENT_UPDATESUBCLASS", "Updating a component into the subdivision");
define("NETCAT_EVENT_UPDATECLASS", "Updating a component");
define("NETCAT_EVENT_UPDATECLASSTEMPLATE", "Updating a components template");
define("NETCAT_EVENT_UPDATEMESSAGE", "Updating a message");
define("NETCAT_EVENT_UPDATESYSTEMTABLE", "Updating a system table");
define("NETCAT_EVENT_UPDATETEMPLATE", "Updating a template");
define("NETCAT_EVENT_UPDATEUSER", "Updating a user");
define("NETCAT_EVENT_UPDATECOMMENT", "Updating a comment");
define("NETCAT_EVENT_DROPCATALOGUE", "Deleting a site");
define("NETCAT_EVENT_DROPSUBDIVISION", "Deleting a subdivision");
define("NETCAT_EVENT_DROPSUBCLASS", "Deleting a component from the subdivision");
define("NETCAT_EVENT_DROPCLASS", "Deleting a component");
define("NETCAT_EVENT_DROPCLASSTEMPLATE", "Deleting a components template");
define("NETCAT_EVENT_DROPMESSAGE", "Deleting a message");
define("NETCAT_EVENT_DROPSYSTEMTABLE", "Deleting a system table");
define("NETCAT_EVENT_DROPTEMPLATE", "Deleting a template");
define("NETCAT_EVENT_DROPUSER", "Deleting a user");
define("NETCAT_EVENT_DROPCOMMENT", "Deleting a comment");
define("NETCAT_EVENT_CHECKCOMMENT", "On comment");
define("NETCAT_EVENT_UNCHECKCOMMENT", "Off comment");
define("NETCAT_EVENT_CHECKMESSAGE", "On object");
define("NETCAT_EVENT_UNCHECKMESSAGE", "Off object");
define("NETCAT_EVENT_CHECKUSER", "On user");
define("NETCAT_EVENT_UNCHECKUSER", "Off user");
define("NETCAT_EVENT_CHECKSUBDIVISION", "On sub");
define("NETCAT_EVENT_UNCHECKSUBDIVISION", "Off sub");
define("NETCAT_EVENT_CHECKCATALOGUE", "On site");
define("NETCAT_EVENT_UNCHECKCATALOGUE", "Off site");
define("NETCAT_EVENT_CHECKSUBCLASS", "On component in sub");
define("NETCAT_EVENT_UNCHECKSUBCLASS", "Off component in sub");
define("NETCAT_EVENT_CHECKMODULE", "On module");
define("NETCAT_EVENT_UNCHECKMODULE", "Off module");
define("NETCAT_EVENT_AUTHORIZEUSER", "User authorization");
define("NETCAT_EVENT_ADDWIDGETCLASS", "Add widget-class");
define("NETCAT_EVENT_EDITWIDGETCLASS", "Edit widget-class");
define("NETCAT_EVENT_DROPWIDGETCLASS", "Drop widget-class");
define("NETCAT_EVENT_ADDWIDGET", "Add widget");
define("NETCAT_EVENT_EDITWIDGET", "Edit widget");
define("NETCAT_EVENT_DROPWIDGET", "Delete widget");

define("NETCAT_EVENT_ADDCATALOGUEPREP", "Adding a site: preparation");
define("NETCAT_EVENT_ADDSUBDIVISIONPREP", "Adding a subdivision: preparation");
define("NETCAT_EVENT_ADDSUBCLASSPREP", "Adding a component into the subdivision: preparation");
define("NETCAT_EVENT_ADDCLASSPREP", "Adding a component: preparation");
define("NETCAT_EVENT_ADDCLASSTEMPLATEPREP", "Adding a components template: preparation");
define("NETCAT_EVENT_ADDMESSAGEPREP", "Adding a message: preparation");
define("NETCAT_EVENT_ADDSYSTEMTABLEPREP", "Adding a system table: preparation");
define("NETCAT_EVENT_ADDTEMPLATEPREP", "Adding a template: preparation");
define("NETCAT_EVENT_ADDUSERPREP", "Adding a user: preparation");
define("NETCAT_EVENT_ADDCOMMENTPREP", "Adding comment: preparation");
define("NETCAT_EVENT_UPDATECATALOGUEPREP", "Updating a site: preparation");
define("NETCAT_EVENT_UPDATESUBDIVISIONPREP", "Updating a subdivision: preparation");
define("NETCAT_EVENT_UPDATESUBCLASSPREP", "Updating a component into the subdivision: preparation");
define("NETCAT_EVENT_UPDATECLASSPREP", "Updating a component: preparation");
define("NETCAT_EVENT_UPDATECLASSTEMPLATEPREP", "Updating a components template: preparation");
define("NETCAT_EVENT_UPDATEMESSAGEPREP", "Updating a message: preparation");
define("NETCAT_EVENT_UPDATESYSTEMTABLEPREP", "Updating a system table: preparation");
define("NETCAT_EVENT_UPDATETEMPLATEPREP", "Updating a template: preparation");
define("NETCAT_EVENT_UPDATEUSERPREP", "Updating a user: preparation");
define("NETCAT_EVENT_UPDATECOMMENTPREP", "Updating a comment: preparation");
define("NETCAT_EVENT_DROPCATALOGUEPREP", "Deleting a site: preparation");
define("NETCAT_EVENT_DROPSUBDIVISIONPREP", "Deleting a subdivision: preparation");
define("NETCAT_EVENT_DROPSUBCLASSPREP", "Deleting a component from the subdivision: preparation");
define("NETCAT_EVENT_DROPCLASSPREP", "Deleting a component: preparation");
define("NETCAT_EVENT_DROPCLASSTEMPLATEPREP", "Deleting a components template: preparation");
define("NETCAT_EVENT_DROPMESSAGEPREP", "Deleting a message: preparation");
define("NETCAT_EVENT_DROPSYSTEMTABLEPREP", "Deleting a system table: preparation");
define("NETCAT_EVENT_DROPTEMPLATEPREP", "Deleting a template: preparation");
define("NETCAT_EVENT_DROPUSERPREP", "Deleting a user: preparation");
define("NETCAT_EVENT_DROPCOMMENTPREP", "Deleting a comment: preparation");
define("NETCAT_EVENT_CHECKCOMMENTPREP", "On comment: preparation");
define("NETCAT_EVENT_UNCHECKCOMMENTPREP", "Off comment: preparation");
define("NETCAT_EVENT_CHECKMESSAGEPREP", "On object: preparation");
define("NETCAT_EVENT_UNCHECKMESSAGEPREP", "Off object: preparation");
define("NETCAT_EVENT_CHECKUSERPREP", "On user: preparation");
define("NETCAT_EVENT_UNCHECKUSERPREP", "Off user: preparation");
define("NETCAT_EVENT_CHECKSUBDIVISIONPREP", "On sub: preparation");
define("NETCAT_EVENT_UNCHECKSUBDIVISIONPREP", "Off sub: preparation");
define("NETCAT_EVENT_CHECKCATALOGUEPREP", "On site: preparation");
define("NETCAT_EVENT_UNCHECKCATALOGUEPREP", "Off site: preparation");
define("NETCAT_EVENT_CHECKSUBCLASSPREP", "On component in sub: preparation");
define("NETCAT_EVENT_UNCHECKSUBCLASSPREP", "Off component in sub: preparation");
define("NETCAT_EVENT_CHECKMODULEPREP", "On module: preparation");
define("NETCAT_EVENT_UNCHECKMODULEPREP", "Off module: preparation");
define("NETCAT_EVENT_AUTHORIZEUSERPREP", "User authorization: preparation");
define("NETCAT_EVENT_ADDWIDGETCLASSPREP", "Add widget-class: preparation");
define("NETCAT_EVENT_EDITWIDGETCLASSPREP", "Edit widget-class: preparation");
define("NETCAT_EVENT_DROPWIDGETCLASSPREP", "Drop widget-class: preparation");
define("NETCAT_EVENT_ADDWIDGETPREP", "Add widget: preparation");
define("NETCAT_EVENT_EDITWIDGETPREP", "Edit widget: preparation");
define("NETCAT_EVENT_DROPWIDGETPREP", "Delete widget: preparation");

define("TITLE_WEB", "Web template");
define("TITLE_MOBILE", "Mobile template");
define("TITLE_RESPONSIVE", "Responsive template");
define("TITLE_OLD", "Web template v4");

define("TOOLS_PATCH_INSTALL_ONLINE_NOTIFY", "Before installing the update is strongly recommended that you back up your system. Run the update process?");
define("TOOLS_PATCH_INFO_NEW", "New patch");
define("TOOLS_PATCH_INFO_NONEW", "No new patches.");
define("TOOLS_PATCH_BACKTOLIST", "Back to list of established updates");
define("TOOLS_PATCH_INFO_INSTALL", "Install patch");
define("TOOLS_PATCH_INFO_SYSTEM_MESSAGE", "New system message added, <a href='%LINK'>read message</a>.");
define("TOOLS_PATCH_ERROR_SET_FILEPERM_IN_HTTP_ROOT_PATH", "Set writing permissions for all files in $HTTP_ROOT_PATH directory.<br><small>(%FILE not available for writing)</small>");
define("TOOLS_PATCH_ERROR_SET_DIRPERM_IN_HTTP_ROOT_PATH", "Set writing permissions for directory $HTTP_ROOT_PATH and all subdirectories.<br><small>(%DIR not available for writing)</small>");
define("TOOLS_PATCH_FOR_CP1251", "Patch for single-byte version NetCat");
define("TOOLS_PATCH_FOR_UTF", "The patch for utf-version NetCat");
define("TOOLS_PATCH_ERROR_UNCORRECT_PHP_VERSION", "Next system version require PHP %NEED, at this time PHP %CURRENT.");
define("TOOLS_PATCH_INSTALEXT", "Patches installation is available under external interface");

define("SQL_CONSTRUCT_TITLE", "Query builder");
define("SQL_CONSTRUCT_CHOOSE_OP", "Choose an action");
define("SQL_CONSTRUCT_SELECT_TABLE", "Select data from table");
define("SQL_CONSTRUCT_SELECT_CC", "Select data from component");
define("SQL_CONSTRUCT_ENTER_CODE", "Enter registry number");
define("SQL_CONSTRUCT_VIEW_SETTINGS", "View system settings");
define("SQL_CONSTRUCT_TABLE_NAME", "Table name");
define("SQL_CONSTRUCT_FIELDS", "Fields");
define("SQL_CONSTRUCT_FIELDS_NOTE", "(optional, comma separated, no spaces)");
define("SQL_CONSTRUCT_CC_ID", "Cc ID");
define("SQL_CONSTRUCT_REGNUM", "License number");
define("SQL_CONSTRUCT_REGCODE", "Activation code");
define("SQL_CONSTRUCT_CHOOSE_MOD", "Select a module");
define("SQL_CONSTRUCT_GENERATE", "Generate query");

define("NETCAT_MAIL_ATTACHMENT_FORM_ATTACHMENTS", "Attachments:");
define("NETCAT_MAIL_ATTACHMENT_FORM_DELETE", "Delete");
define("NETCAT_MAIL_ATTACHMENT_FORM_FILENAME", "File title:");
define("NETCAT_MAIL_ATTACHMENT_FORM_ADD", "Add more");

define('NETCAT_DATEPICKER_CALENDAR_DATE_FORMAT', 'dd.mm.yyyy');
define('NETCAT_DATEPICKER_CALENDAR_DAYS', 'Sunday Monday Tuesday Wednesday Thursday Friday Saturday Sunday');
define('NETCAT_DATEPICKER_CALENDAR_DAYS_SHORT', 'Sun Mon Tue Wed Thu Fri Sat Sun');
define('NETCAT_DATEPICKER_CALENDAR_DAYS_MIN', 'Su Mo Tu We Th Fr Sa Su');
define('NETCAT_DATEPICKER_CALENDAR_MONTHS', 'January February March April May June July August September October November December');
define('NETCAT_DATEPICKER_CALENDAR_MONTHS_SHORT', 'Jan Feb Mar Apr May Jun Jul Aug Sep Oct Nov Dec');
define('NETCAT_DATEPICKER_CALENDAR_TODAY', 'Today');

define('TOOLS_CSV', 'CSV Export/Import');
define('TOOLS_CSV_EXPORT', 'Export CSV');
define('TOOLS_CSV_IMPORT', 'Import CSV');
define('TOOLS_CSV_EXPORT_TYPE', 'Export Source');
define('TOOLS_CSV_EXPORT_TYPE_SUBCLASS', 'Data component');
define('TOOLS_CSV_EXPORT_TYPE_COMPONENT', 'Component');
define('TOOLS_CSV_SELECT_SITE', 'Select catalogue');
define('TOOLS_CSV_SELECT_SUBDIVISION', 'Select subdivision');
define('TOOLS_CSV_SELECT_SUBCLASS', 'Select subclass');
define('TOOLS_CSV_SELECT_COMPONENT', 'Select data component');
define('TOOLS_CSV_SUBCLASSES_NOT_FOUND', 'Data components not found');
define('TOOLS_CSV_NOT_SELECTED', 'Not selected');
define('TOOLS_CSV_CREATE_EXPORT', 'Export');
define('TOOLS_CSV_RECORD_ID', 'Record identifier in the file');
define('TOOLS_CSV_PARENT_RECORD_ID', 'Parent record identifier');

define('TOOLS_CSV_SELECT_SETTINGS', 'CSV settings');

define('TOOLS_CSV_OPT_ENCLOSED', 'Columns enclosed by');
define('TOOLS_CSV_OPT_ESCAPED', 'Columns escaped by');
define('TOOLS_CSV_OPT_SEPARATOR', 'Columns terminated by');
define('TOOLS_CSV_OPT_CHARSET', 'Charset');
define('TOOLS_CSV_OPT_CHARSET_UTF8', 'Unicode (utf-8)');
define('TOOLS_CSV_OPT_CHARSET_CP1251', 'Microsoft Excel (windows-1251)');
define('TOOLS_CSV_OPT_NULL', 'Replace NULL with');
define('TOOLS_CSV_OPT_LISTS', '<nobr>Values from the classifier fields</nobr>');
define('TOOLS_CSV_OPT_LISTS_NAME', 'element name');
define('TOOLS_CSV_OPT_LISTS_VALUE', 'additional value (field.Value)');
define('TOOLS_CSV_EXPORT_DONE', 'Export done. You can download file by link <a href="%s" target="_blank">%s</a>. To delete file click <a href="%s" target="_top">here</a>.');

define('TOOLS_CSV_MAPPING_HEADER', 'Fields mapping');
define('TOOLS_CSV_IMPORT_FILE', 'Import file (*.csv)');
define('TOOLS_CSV_IMPORT_RUN', 'Import');
define('TOOLS_CSV_IMPORT_FILE_NOT_FOUND', 'Import file not found');

define('TOOLS_CSV_COMPONENT_FIELD', 'Component field');
define('TOOLS_CSV_FILE_FIELD', 'CSV-file field');
define('TOOLS_CSV_FINISHED_HEADER', 'Import finished');
define('TOOLS_CSV_EXPORT_FINISHED_HEADER', 'Export finished');
define('TOOLS_CSV_IMPORT_SUCCESS', 'Import finished, rows added: ');
define('TOOLS_CSV_DELETE_FINISHED_HEADER', 'File deleted');
define('TOOLS_CSV_DELETE_FINISHED', 'File deleted successfully. <a href="%s" target="_top">Back to export</a>');
define('TOOLS_CSV_IMPORT_HISTORY', 'Import history');
define('TOOLS_CSV_HISTORY_ID', 'ID');
define('TOOLS_CSV_HISTORY_CREATED', 'Created');
define('TOOLS_CSV_HISTORY_ROLLBACK', 'Rollback');
define('TOOLS_CSV_HISTORY_EMPTY', 'Import history is empty');
define('TOOLS_CSV_HISTORY_CLASS_NAME', 'Component');
define('TOOLS_CSV_HISTORY_ROWS', 'Rows');
define('TOOLS_CSV_HISTORY_ROLLBACKED', 'Canceled');
define('TOOLS_CSV_ROLLBACK_FINISHED_HEADER', 'Rollback finished');
define('TOOLS_CSV_ROLLBACK_SUCCESS', 'Rollback finished successfully. Rows canceled: ');


define('WELCOME_SCREEN_TOOLTIP_SUPPORT',      'In case of difficulty you can refer to the NetCat documentation or get a response from our technical support.');
define('WELCOME_SCREEN_TOOLTIP_SIDEBAR',      'You can change main settings in the site management panel.');
define('WELCOME_SCREEN_TOOLTIP_SIDEBAR_SUBS', 'The site consists of sections, which will be shown here when you <a href="#site.add">create a site</a>. Black sections follow the structure of the site and gray sections are not displayed on the site, but they have service purpose.');
define('WELCOME_SCREEN_TOOLTIP_TRASH_WIDGET', 'To speed up your work you can customize the widgets. For example, in the "Trash bin" widget you can restore deleted objects.');
define('WELCOME_SCREEN_MODAL_TEXT', '<h2>Welcome to NetCat site management system!</h2>
    <p>For your convenience, we have assembled the most important operations on a separate page — <b>site management panel.</b> You can get to it by clicking on the name of your site in the "tree" on the left.</p>
    <p>Other site settings are made in the relevant sections of the administrative interface.</p>
    <p>Thank you very much for using our system and <b>good luck.</b></p>');
define('WELCOME_SCREEN_BTN_START', 'Start working');

define('NETCAT_FILTER_FIELD_MESSAGE_ID', 'Row ID');
define('NETCAT_FILTER_FIELD_CREATED', 'Created');
define('NETCAT_FILTER_FIELD_LAST_UPDATED', 'Last Updated');

define('NETCAT_FIELD_VALUE_INHERITED_FROM_SUBDIVISION', 'Value is inherited from the subdivision &ldquo;%s&rdquo;');
define('NETCAT_FIELD_VALUE_INHERITED_FROM_CATALOGUE', 'Value is inherited from the <a href="%s" target="_top">site properties</a>');
define('NETCAT_FIELD_VALUE_INHERITED_FROM_TEMPLATE', 'Value is inherited from the template &ldquo;%s&rdquo;');

define('NETCAT_USER_BREAK_ATTRIBUTE_NAMING_CONVENTION', 'Some of the attribute names are breaking <a href="https://www.w3.org/TR/html-markup/syntax.html#syntax-attributes" target="_blank">the naming convention</a> and were ignored: %s.');

define('NETCAT_SECURITY_SETTINGS', 'Site protection settings');
define('NETCAT_SECURITY_SETTINGS_SAVE', 'Apply');
define('NETCAT_SECURITY_SETTINGS_SAVED', 'Settings saved');
define('NETCAT_SECURITY_SETTINGS_USE_DEFAULT', 'use <a href="%s" target="_top">common settings</a>');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER', 'Incoming data filter');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE', 'Action on finding incoming data used for&nbsp;injection');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE_DISABLED', 'disable (do not check incoming data)');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE_LOG_ONLY', 'no action ');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE_RELOAD_ESCAPE_INPUT', 'escape incoming parameter and reload the page');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE_RELOAD_REMOVE_INPUT', 'reset incoming parameter and reload the page');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE_EXCEPTION', 'halt execution');

define('NETCAT_SECURITY_FILTER_NO_TOKENIZER', 'PHP code will not be checked because <i>tokenizer</i> extension is disabled.');
define('NETCAT_SECURITY_FILTER_EMAIL_ENABLED', 'Send notification when filter is triggered');
define('NETCAT_SECURITY_FILTER_EMAIL_PLACEHOLDER', 'Email address');
define('NETCAT_SECURITY_FILTER_EMAIL_SUBJECT', 'incoming data alert');
define('NETCAT_SECURITY_FILTER_EMAIL_PREFIX', 'Input filter was triggered on %s (%s).');
define('NETCAT_SECURITY_FILTER_EMAIL_INPUT_VALUE', 'Incoming parameter value – %s');
define('NETCAT_SECURITY_FILTER_EMAIL_CHECKED_STRING', 'String with unescaped input');
define('NETCAT_SECURITY_FILTER_EMAIL_IP', 'Remote IP-address');
define('NETCAT_SECURITY_FILTER_EMAIL_URL', 'Page URL');
define('NETCAT_SECURITY_FILTER_EMAIL_REFERER', 'Referring page URL');
define('NETCAT_SECURITY_FILTER_EMAIL_GET', 'GET parameters');
define('NETCAT_SECURITY_FILTER_EMAIL_POST', 'POST parameters');
define('NETCAT_SECURITY_FILTER_EMAIL_BACKTRACE', 'Call backtrace');
define('NETCAT_SECURITY_FILTER_EMAIL_SUFFIX', 'Please fix this vulnerability as soon as possible, because it can be exploited to hack your site.');
define('NETCAT_SECURITY_FILTERS_DISABLED', 'All incoming data filters are disabled.');

define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA', 'CAPTCHA logon form protection');
define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA_RECOMMEND_DEFAULT', '(we recommend to use same settings on all sites)');
define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA_MODE_DISABLED', 'disabled');
define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA_MODE_ALWAYS', 'always show CAPTCHA');
define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA_MODE_COUNT', 'show CAPTCHA after incorrect login or password');
define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA_ATTEMPTS', 'number of attempts without CAPTCHA');
