<?php

/* $Id: en.lang.php 8184 2012-10-10 12:20:57Z ewind $ */

define("NETCAT_MODULE_SEARCH_TITLE", "Site search");
define("NETCAT_MODULE_SEARCH_DESCRIPTION", "Indexing & site search.");

define("NETCAT_MODULE_SEARCH_EVENT", "Event of the search module.");

define("NETCAT_MODULE_SEARCH_ADMIN_INVALID_REQUEST", "Page cannot be displayed: bad request (parameter '%s')");

define("NETCAT_MODULE_SEARCH_ADMIN_INFO", "Information");
define("NETCAT_MODULE_SEARCH_ADMIN_INDEXING", "Indexing.");
define("NETCAT_MODULE_SEARCH_ADMIN_LISTS", "Lists");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTINGS", "Settings");

define("NETCAT_MODULE_SEARCH_ADMIN_QUERIES", "Requests");
define("NETCAT_MODULE_SEARCH_ADMIN_SYNONYMS", "Synonyms");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKENLINKS", "Broken links");

define("NETCAT_MODULE_SEARCH_ADMIN_GENERAL_SETTINGS", "General settings");
define("NETCAT_MODULE_SEARCH_ADMIN_VIEW_SETTINGS", "View settings");
define("NETCAT_MODULE_SEARCH_ADMIN_RULES_SETTINGS", "Rule settings");
define("NETCAT_MODULE_SEARCH_ADMIN_SYSTEM_SETTINGS", "System settings");

define("NETCAT_MODULE_SEARCH_ADMIN_STAT_CHECK_CRONTAB",
        "There are overdue tasks in indexing queue. Check that cron can run the indexing script.");

define("NETCAT_MODULE_SEARCH_WIDGET_BROKEN_LINKS", "broken<br>links");
define("NETCAT_MODULE_SEARCH_WIDGET_CHECK_CRONTAB", "tasks overdue");
define("NETCAT_MODULE_SEARCH_WIDGET_NO_RULES", "no indexing rules");
define("NETCAT_MODULE_SEARCH_WIDGET_CONFIGURATION_ERRORS", "configuration error");

define("NETCAT_MODULE_SEARCH_ADMIN_STAT_HEADER", "Statistics");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_NUM_DOCUMENTS", "Indexed pages");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_NUM_TERMS", "Indexed words");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_NUM_SITEMAP_URLS", "Number of pages in sitemap.xml");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_NUM_QUERIES_TODAY", "Number of queries to the search engine today");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_NUM_QUERIES_YESTERDAY", "Number of queries to the search engine yesterday");
define("NETCAT_MODULE_SEARCH_ADMIN_SHOW_QUERY_LOG", "show all");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_LAST_QUERIES", "Last queries to the search engine");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_MOST_POPULAR", "The most popular queries to the search engine");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_MOST_POPULAR_NO_RESULTS", "The most popular queries to the search engine with no results");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_INDEXING", "Indexing");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_INDEX", "Index");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_INDEXING_NOW", "Indexing is in progress");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_INDEX_IN_BACKGROUND", "in background");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_INDEX_IN_BROWSER", "in new window");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_INDEX_AREA", "Index area");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_INDEX_AREA_IN_BACKGROUND", "Start index in background");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_INDEX_AREA_IN_BROWSER", "Start index in new window");

define("NETCAT_MODULE_SEARCH_ADMIN_STATUS_DELETED", "The records are removed.");

define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_FILTER", "Query filter");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_FRAGMENT", "Fragment");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_TIME_PERIOD", "Time period");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_TIME_PERIOD_FROM", "from");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_TIME_PERIOD_TO", "to");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_TIME_PERIOD_CLEAR", "clear");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_RESULTS", "Results");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_RESULTS_ALL", "all results");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_RESULTS_NONE", "none");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_RESULTS_MATCHED", "matched results");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_SUBMIT_FILTER", "Show");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_PER_PAGE", "per %s");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_SORT_BY_RESULT_COUNT", "by result count");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_SORT_BY_TIME", "by time");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_SORT_BY_QUERY", "alphabetically");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_STRING", "Query");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_COUNT", "Count of queries");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_LAST_QUERY", "Last query");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_LAST_QUERY_TIME", "Time");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_LAST_QUERY_RESULT_COUNT", "Found");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_LAST_QUERY_USER", "IP, user");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_NO_ENTRIES", "There is no data for your request");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_PREV_PAGE", "Previous page");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_NEXT_PAGE", "Next page");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_BACK_TO_LIST", "Back to list");

define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_ALL_QUERIES", "Show all queries &laquo;<span class='q'>%s</span>&raquo;");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_OPEN_RESULTS_HINT", "Click on number in a column &laquo;Found&raquo; to see results of the query");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_OPEN_RESULTS_LINK_HINT", "go to search results");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_OPEN_LOG_LINK_HINT", "show all requests for this query");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_AREA", "Search area");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_AREA_INCLUDED", "Include in results");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_AREA_EXCLUDED", "Exclude from results");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_TIME", "Time");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_RESULTS_COUNT", "Found");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_IP", "IP");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_USER", "User");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_LOG_DISABLED", "Query log is disabled in <a href='%s' target='_top'>module settings</a>.");

define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION", "Extension");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSIONS", "Extensions");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_INTERFACE", "Interface of extension");
define("NETCAT_MODULE_SEARCH_ADMIN_SEARCH_PROVIDER", "Search provider");
define("NETCAT_MODULE_SEARCH_ADMIN_SEARCH_PROVIDER_ANY", "Any");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_ACTION", "Action");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_ACTION_ANY", "Search & indexing");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_ACTION_SEARCHING", "Search");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_ACTION_INDEXING", "Indexing");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_CLASS", "Class of extension");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_CONTENT_TYPE", "MIME-type");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_CONTENT_TYPE_ANY", "Any");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_PRIORITY", "Priority");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_ENABLED", "Extension is enabled");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSIONS_EMPTY_LIST", "There is no extension.");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSIONS_ADD", "Add extension");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSIONS_CONFIRM_DELETE_WARNING",
        "<h3>WARNING!</h3><p><b>Removing record about expansion can lead to wrong search results ".
        "or full loss of functionality of the search module.</b></p>Please, confirm action.");

define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSIONS_CONFIRM_DELETE_OK", "I am in sound mind and memory.");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_NOT_FOUND", "Extension (ID=%s) not found");

define("NETCAT_MODULE_SEARCH_ADMIN_SYNONYMS_FIELD_CAPTION", "Synonyms (one at row, upper-case)");
define("NETCAT_MODULE_SEARCH_ADMIN_SYNONYMS_DO_NOT_APPLY_FILTERS", "Don't process the entered words. (not recommended)");
define("NETCAT_MODULE_SEARCH_ADMIN_SYNONYMS_DO_NOT_APPLY_FILTERS_HELP",
        "<p>While saving synonyms list, filters for the current language will be applied to it. ".
        "For example, stop-words filter and the morphological analyser (or stemmer).</p>".
        "<p>Thus, words will be transformed to base forms and words ignored by search engine will be removed from the list.".
        "For example, list of words <code>GAMES; PLAYS</code> ".
        "at the enabled morphological analyser will be saved as <code>GAME; PLAY; PLAYS</code> ".
        "(a word <code>PLAYS</code> corresponds two base forms — an a verb and a noun).</p>".
        "<p>Note that by default synonyms are substituted in queries after a stop-words ".
        "filtration and transforming to the base form, ".
        "therefore enabling of the given option is <strong>not</strong> recommended. ");

define("NETCAT_MODULE_SEARCH_ADMIN_SYNONYM_LIST_MUST_HAVE_AT_LEAST_TWO_WORDS",
        "The list of synonyms must contain at least two words. If you have entered several words, ".
        "may be, some of them are in a stop-words list.");

define("NETCAT_MODULE_SEARCH_ADMIN_SYNONYM_SAVE_RESULT",
        "The list of words after transforming was saved as &laquo;%s&raquo;. If the result isn't ".
        "satisfactory, <a href='%s' target='_top'>edit the list of words</a>, and activate option ".
        "&laquo;Don't transform entered words&raquo; before saving.");


define("NETCAT_MODULE_SEARCH_ADMIN_RECORD_NOT_FOUND", "Invalid identifier (ID=%s)");
define("NETCAT_MODULE_SEARCH_ADMIN_LANGUAGE_ANY", "Any");
define("NETCAT_MODULE_SEARCH_ADMIN_LANGUAGE_ANY_LANGUAGE", "Any language");

define("NETCAT_MODULE_SEARCH_ADMIN_STOPWORDS", "Stop-words");
define("NETCAT_MODULE_SEARCH_ADMIN_STOPWORD", "Stop-word");
define("NETCAT_MODULE_SEARCH_ADMIN_STOPWORD_FIELD_CAPTION", "Stop-word (UPPER-CASE)");
define("NETCAT_MODULE_SEARCH_ADMIN_STOPWORD_HAS_NO_BASEFORM", "The entered word <code>%s</code> will be filtered ".
        "before applying stop-words filter (maybe it's too short). Do you want to save this word?");
define("NETCAT_MODULE_SEARCH_ADMIN_STOPWORD_HAS_ONE_BASEFORM", "The entered word <code>%s</code> has a base form <code>%s</code>.");
define("NETCAT_MODULE_SEARCH_ADMIN_STOPWORD_HAS_SEVERAL_BASEFORMS", "The entered word <code>%s</code> has several base forms.");
define("NETCAT_MODULE_SEARCH_ADMIN_STOPWORD_BASEFORM_QUESTION", "Which form of word do you want to save as stop-word?");
define("NETCAT_MODULE_SEARCH_ADMIN_STOPWORD_AS_ENTERED", "(entered variant &mdash; is not recommended)");

define("NETCAT_MODULE_SEARCH_ADMIN_RULE", "Rule");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_NAME", "Name");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_SITE", "Site");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA", "Area");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_SCHEDULE", "Schedule");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_MUST_HAVE_INTERVAL_TYPE", "The interval of schedule launching is not specified");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_TO_INDEX", "Index");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_WHOLE_SITE", "the whole site");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_AREAS", "site areas");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_FREQUENCY", "Frequency of re-indexing");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_DAILY", "every day at %s");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_EVERY_N_MINUTES", "every %s minutes, starting from %s");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_EVERY_N_HOURS", "every %s hours, begin at %s");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_EVERY_N_DAYS", "every %s days at %s");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_EVERY_X_DAY", "every %s of the month at %s");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_ON_REQUEST", "only on request");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_START_URL", "Start address");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_NONEXISTENT_SITE", "Invalid site address ID=%s)");

define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_DESCRIPTION_ALLSITES", "All sites");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_DESCRIPTION_SITE", "Site &laquo;<a href='%s' target='_top'>%s</a>&raquo;");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_DESCRIPTION_SUB_ONLY", "Only the main page of the division &laquo;<a href='%s' target='_top'>%s</a>&raquo;");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_DESCRIPTION_SUB_CHILDREN", "All pages of the division &laquo;<a href='%s' target='_top'>%s</a>&raquo;");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_DESCRIPTION_SUB_DESCENDANTS", "All pages and subdivisions of the division &laquo;<a href='%s' target='_top'>%s</a>&raquo;");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_DESCRIPTION_PAGE", "Page &laquo;<a href='%s' target='_blank'>%s</a>&raquo;");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_DESCRIPTION_INCLUDED", "Index");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_DESCRIPTION_EXCLUDED", "Don't index");

define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_MINUTE", "minute minutes");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_HOUR", "hour hours");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_DAY", "day days");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_EVERY_SEVERAL", "every");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_EVERY_SINGLE_MASCULINE", "every");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_EVERY_SINGLE_FEMININE", "every");

define("NETCAT_MODULE_SEARCH_ADMIN_NO_RULES", "There is no index rules. It is necessary to <a href='%s' target='_top'>create</a> at least one rule.");
define("NETCAT_MODULE_SEARCH_ADMIN_ADD_RULE", "Add rule");

define("NETCAT_MODULE_SEARCH_ADMIN_UNNAMED_RULE", "No name");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_SHOW_DETAILS", "more");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_LAST_RUN", "Last indexing");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_NEVER_RUN", "This rule has never launched");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_LAST_RUN_DURATION", "duration: ");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_LAST_RUN_NOT_FINISHED", "not finished");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_RUN_IN_BROWSER", "Index in realtime");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_RUN_IN_BACKGROUND", "Index in background");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_EDIT_LINK", "Edit");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_QUEUE_LOADING", "Loading...");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_QUEUED", "This rule will be launched in background");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_QUEUE_ERROR", "An error occurred when setting the rules in queue of re-indexing");

define("NETCAT_MODULE_SEARCH_ADMIN_RULE_STATISTICS", "Total re-indexed documents: %d, deleted documents: %d, checked links: %d");

define("NETCAT_MODULE_SEARCH_ADMIN_INDEXING_TITLE", "NetCat / Site indexing");
define("NETCAT_MODULE_SEARCH_ADMIN_INDEXING_IN_PROGRESS_ERROR",
        "Re-indexing of the area &laquo;%s&raquo; is in progress.<br />".
        "Launching of the other re-indexing process is impossible. Try again later.");
define("NETCAT_MODULE_SEARCH_ADMIN_INDEXING_IN_PROGRESS", "Re-indexing is in progress. Don't close this window before the process is completed.");
define("NETCAT_MODULE_SEARCH_ADMIN_INDEXING_DONE", "Indexing is completed.");
define("NETCAT_MODULE_SEARCH_ADMIN_INDEXING_DONE_STATS",
        "Time spent: %s<br />".
        "Indexed documents: %d<br />".
        "Checked links: %d<br />".
        "Links to non-existent documents: %d<br />".
        "Deleted documents: %d<br />");

define("NETCAT_MODULE_SEARCH_ADMIN_RESULTS_MANY", "Showed results %d&mdash;%d of %d");
define("NETCAT_MODULE_SEARCH_ADMIN_RESULTS_ONE", "Showed result %d of %d");

define("NETCAT_MODULE_SEARCH_ADMIN_MINUTES", "%d min");
define("NETCAT_MODULE_SEARCH_ADMIN_HOURS_MINUTES", "%d h %d min");

define("NETCAT_MODULE_SEARCH_ADMIN_BULLET", "&mdash;");

define("NETCAT_MODULE_SEARCH_ADMIN_BACK", "Back");
define("NETCAT_MODULE_SEARCH_ADMIN_ADD", "Add");
define("NETCAT_MODULE_SEARCH_ADMIN_SAVE", "Save");
define("NETCAT_MODULE_SEARCH_ADMIN_EDIT", "Edit");
define("NETCAT_MODULE_SEARCH_ADMIN_COPY", "Copy");
define("NETCAT_MODULE_SEARCH_ADMIN_DELETE", "Delete");
define("NETCAT_MODULE_SEARCH_ADMIN_DELETE_SELECTED", "Delete selected");
define("NETCAT_MODULE_SEARCH_ADMIN_ID", "ID");
define("NETCAT_MODULE_SEARCH_ADMIN_ACTIONS", "Actions");

define("NETCAT_MODULE_SEARCH_ADMIN_FILTER", "Find");
define("NETCAT_MODULE_SEARCH_ADMIN_FILTER_RESET", "Clear filter");

define("NETCAT_MODULE_SEARCH_ADMIN_LANGUAGE", "Language");
define("NETCAT_MODULE_SEARCH_ADMIN_EMPTY_LIST", "The list is empty");

define("NETCAT_MODULE_SEARCH_ADMIN_SAVE_OK", "Data saved");
define("NETCAT_MODULE_SEARCH_ADMIN_SAVE_ERROR", "An error occurred while saving");

define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_SEARCH_DISABLED",
        "Site search is disabled. It is necessary to<a href='%s' target='%s'>enable indexing and site search</a> for indexing.");

define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_INDEXING", "Index preferences");

define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_ENABLE_SEARCH", "enable indexing and site search");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_DISABLE_SECTION_INDEXING", "Disable page indexing by regular expressions");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_MINIMUM_WORD_LENGTH", "Min word length for indexing is %s letters");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_USE_STOPWORDS", "use <a href='%s' target='_top'>stop-words</a> lists");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_USE_ROBOTS_TXT", "Use robots.txt instructions");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_MAX_DOCUMENT_LENGTH", "Maximum page length %s bytes");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CRAWLER_DELAY", "Pause between queries to search engine is %s sec");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_IGNORE_NUMBERS", "don't take into consideration digits in indexing and search");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CHECK_LINKS", "check out links on the site");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CHECK_EXTERNAL_LINKS", "check out external links");

define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_CORRECTION", "Correction of queries, which gave no results");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_CORRECTION_ENABLED", "Correct queries which gave no results");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CORRECTION_PHRASES", "search phrases ascommon set of words");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CORRECTION_BREAK_WORDS_UP", "search missing spaces");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CORRECTION_LAYOUT", "correct keyboard layout");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CORRECTION_FUZZY",
        "use fuzzy search for words not in the dictionary".
        "(<a href='#fuzzy' class='internal on_page'>fuzzy search</a> must be turned on)");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CORRECTION_MAXIMUM_QUERY_LENGTH", "Max query length for correction is %s words");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CORRECTION_SIMILARITY_FACTOR", "Similarity factor used for query correction: %s");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG", "History of queries");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_ENABLED", "Turn on history of queries");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE", "Delete queries");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE_NEVER", "never");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE_BEFORE", "before");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE_BEFORE_HOURS", "hours");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE_BEFORE_DAYS", "days");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE_BEFORE_MONTHS", "month");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE_NOW", "Clear list of queries");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE_NOW_EVERYTHING", "everything");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE_NOW_SUBMIT", "Clear");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGED", "The history of queries is cleared.");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_FEATURES", "User search queries");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_DEFAULT_OPERATOR", "Default operator");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_DEFAULT_OPERATOR_AND", "logical AND");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_DEFAULT_OPERATOR_OR", "logical OR");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_TERM_BOOST", "allow to specify the weight of words");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_PROXIMITY_SEARCH", "allow to search with specifying the distance between words");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_WILDCARD_SEARCH", "allow to search by template");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_RANGE_SEARCH", "allow to search by interval");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_FUZZY_SEARCH", "allow fuzzy search");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_FIELD_SEARCH", "allow to search by field");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_FIELD_INVALID_NAME", "The name of field is incorrect.\n\n".
        "The name of field must contain only Latin letters, digits and underscore ".
        "and it can't concur with reserved names (title, content, last_updated, language).");

define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_RESULTS", "Results display");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_SHOW_MATCHED_FRAGMENT", "display text fragment in the list of found pages");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_HIGHLIGHT_MATCHED", "make  words of the query bold");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ALLOW_FIELD_SORT", "enable sort by fields (by date/time)");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_OPEN_LINKS_IN_NEW_WINDOW", "open links in a new window");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_RESULTS_TITLE_WORD_COUNT", "Max words count in the title of results");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_RESULTS_FRAGMENT_WORD_COUNT", "Min words count in a text fragment");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_MAX_PREVIEW_TEXT_LENGTH", "Maximum length of the text to store for the highlighting");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_KBYTES", "Kbytes");

define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ADVANCED_SEARCH", "Advanced search");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ENABLE_ADVANCED_SEARCH_FORM", "enable advanced search");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ADVANCED_SEARCH_FORM_OPTIONS", "Include fields in a form of advanced search");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ADVANCED_SEARCH_EXCLUDE", "exclude pages with words");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ADVANCED_SEARCH_FIELD", "arrangement of the words");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ADVANCED_SEARCH_DATETIME", "update date");

define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_QUERY_SUGGEST", "Auto-fill query string");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_QUERY_SUGGEST_TITLES", "titles of pages");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_QUERY_SUGGEST_QUERIES", "queries");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_QUERY_SUGGEST_DISABLED", "don't apply");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_QUERY_SUGGEST_MINIMUM_LENGTH", "Min length of query to trigger auto-fill");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_QUERY_SUGGEST_NUMBER_OF_HITS", "Number of results in the drop-down list");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_QUERY_SUGGEST_TITLES_SEARCH_IN_INDEX", "search for all possible forms of the words");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_QUERY_SUGGEST_TITLES_SEARCH_AS_PHRASE", "fixed word order (search as a phrase)");

define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_FORM_TEMPLATES", "Templates for displaying a search form by default");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ADVANCED_FORM_TEMPLATE", "Template for displaying advanced search form");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_FORM_TEMPLATES_MOBILE", "Templates for displaying a search form by default (mobile)");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ADVANCED_FORM_TEMPLATE_MOBILE", "Template for displaying advanced search form (mobile)");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_FORM_TEMPLATES_RESPONSIVE", "Templates for displaying a search form by default (responsive)");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ADVANCED_FORM_TEMPLATE_RESPONSIVE", "Template for displaying advanced search form (responsive)");

define("NETCAT_MODULE_SEARCH_ADMIN_SETTINGS_SAVE", "Save preferences");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTINGS_SAVED", "Preferences are saved.");

define("NETCAT_MODULE_SEARCH_ADMIN_SETTINGS_INCORRECT_PROVIDER_CLASS", "SearchProvider: class %s must implement <em>nc_search_provider</em> interface!");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTINGS_PROVIDER_CLASS_NOT_FOUND", "SearchProvider: class %s not found");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTINGS_PROVIDER_CLASS_INITIALIZATION_ERROR", "SearchProvider: error during %s initialization (%s)");

define("NETCAT_MODULE_SEARCH_ADMIN_FIELDS", "HTML-documents");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_DOCUMENT_AREAS", "Indexing area");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_DOCUMENT_CONTENT", "Area indexed for HTML-pages (the first matching rule is used)");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_DOCUMENT_NOINDEX", "Areas whose content is not indexed");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_TAG_WEIGHT", "Tags weight");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_TAGS", "Tags");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_TAGS_HAVE_WEIGHT", "have weight");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_TAGS_DELETE", "delete");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_TITLE_TAG_HAS_WEIGHT", "Tag <code>TITLE</code> has weight");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_FIELDS_SAVED",
        "The changes will take effect while the next reindexing. ".
        "<a href='%s' target='_top'>Go to &laquo;Indexing&raquo;</a>.");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION", "Data extraction");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_NAME", "Field name");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_QUERY", "The area of HTML-document");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_WEIGHT", "Weight");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_TYPE", "Data type");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_TYPE_STRING", "String");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_TYPE_INTEGER", "Integer");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_ADD_FIELD", "Add field");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_IS_INDEXED", "filed is available in the search");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_IS_SORTABLE", "enable sort by field");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_IS_NORMALIZED", "normalize text");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_IS_RETRIEVABLE", "value is available in search results");

define("NETCAT_MODULE_SEARCH_ADMIN_NO_BROKEN_LINKS", "While indexing has not been found broken links.");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKEN_LINK_GROUP_BY", "Group by");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKEN_LINK_GROUP_BY_URL", "links");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKEN_LINK_GROUP_BY_REFERRER", "by referring pages");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKEN_LINK_PREV_PAGE", "Previous page");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKEN_LINK_NEXT_PAGE", "Next page");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKEN_LINK_EDIT", "edit");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKEN_LINKS_MENU_ITEM", "Broken links");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKEN_LINKS_REFERRER_LIMIT", "Only first %s referring links are shown.");

define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG", "Events");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_FILTER", "Event log");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_SETTINGS", "Event log settings");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_SHOW_SETTINGS", "Show event log settings");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_LEVEL", "Store log of the following types of events");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_DELETE_PERIOD", "Keep logs for %s deys");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_DELETE_ALL", "Clear log");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_DELETED", "Event log is cleared");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_EMPTY", "Event log is empty");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_TIME", "Time");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_TYPE", "Event type");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_MESSAGE", "Message");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_PREV_PAGE", "Previous");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_NEXT_PAGE", "Next");

define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_ERROR", "An error occurred in a module");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_PHP_EXCEPTION", "Exception");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_PHP_ERROR", "PHP error");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_PHP_WARNING", "PHP warning");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_INDEXING_BEGIN_END", "Begin, end indexing");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_INDEXING_NO_SUB", "Impossible to determine the subdivision of the document");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_CRAWLER_REQUEST", "HTTP request");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_PARSER_DOCUMENT_BRIEF", "Brief information about the received document");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_PARSER_DOCUMENT_VERBOSE", "Full information about the received document");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_PARSER_DOCUMENT_LINKS", "Adding a link to the queue");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_SCHEDULER_START", "Schedule launching");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_INDEXING_CONTENT_ERROR", "Error occured while processing the received document");

// Общие
define("NETCAT_MODULE_SEARCH_DATETIME_FORMAT", "%d.%m.%Y %H:%M");

// Поиск на сайте

define("NETCAT_MODULE_SEARCH_SUBMIT_BUTTON_TEXT", "Find");
define("NETCAT_MODULE_SEARCH_ADVANCED_LINK_TEXT", "Advanced search");

define("NETCAT_MODULE_SEARCH_FIND_CAPTION", "I'm searching");
define("NETCAT_MODULE_SEARCH_EXCLUDE_CAPTION", "Exclude");
define("NETCAT_MODULE_SEARCH_FIELD_CAPTION", "Words");
define("NETCAT_MODULE_SEARCH_FIELD_ANY", "in any part of the page");
define("NETCAT_MODULE_SEARCH_FIELD_TITLE", "only in the title");
define("NETCAT_MODULE_SEARCH_TIME_CAPTION", "Date");
define("NETCAT_MODULE_SEARCH_TIME_ANY", "any");
define("NETCAT_MODULE_SEARCH_TIME_LAST", "last");
define("NETCAT_MODULE_SEARCH_TIME_LAST_HOURS", "hours");
define("NETCAT_MODULE_SEARCH_TIME_LAST_DAYS", "days");
define("NETCAT_MODULE_SEARCH_TIME_LAST_WEEKS", "weeks");
define("NETCAT_MODULE_SEARCH_TIME_LAST_MONTHS", "month");

define("NETCAT_MODULE_SEARCH_NO_RESULTS", "No results found.");
define("NETCAT_MODULE_SEARCH_RESULTS_RANGE", "Results %d&mdash;%d of %d");
define("NETCAT_MODULE_SEARCH_RESULTS_ONE", "Result %d of %d");
define("NETCAT_MODULE_SEARCH_RESULTS_PREV", "previous");
define("NETCAT_MODULE_SEARCH_RESULTS_NEXT", "next");
define("NETCAT_MODULE_SEARCH_SORT_BY", "Sort by: ");
define("NETCAT_MODULE_SEARCH_SORT_BY_LAST_MODIFIED", "modify date");
define("NETCAT_MODULE_SEARCH_SORT_BY_RELEVANCE", "relevance");
define("NETCAT_MODULE_SEARCH_QUERY_ERROR", "Incorrect query.");
define("NETCAT_MODULE_SEARCH_NO_TITLE", "Untitled");

// Подсказки при исправлении запросов
define("NETCAT_MODULE_SEARCH_CORRECTION_GENERIC", "Nothing found by query &laquo;<span class='nc_query'>%s</span>&raquo;. ".
        "<span class='nc_correction_suggesion'>Showing results by query &laquo;<span class='nc_query'>%s</span>&raquo;.</span>");

define("NETCAT_MODULE_SEARCH_CORRECTION_QUOTES", "Nothing found by query &laquo;<span class='nc_query'>%s</span>&raquo;. ".
        "<span class='nc_correction_suggesion'>Showing results by query without quotes: &laquo;<span class='nc_query'>%s</span>&raquo;.</span>");

define("NETCAT_MODULE_SEARCH_PAGES", "Pages");
define("NETCAT_MODULES_SEARCH_FROM", "of");

// Ошибки конфигурации PHP
define("NETCAT_MODULE_SEARCH_NO_DOM_ERROR", "The <a href='http://php.net/DOM'>Document Object Model</a> extension must be enabled.");
define("NETCAT_MODULE_SEARCH_NO_MB_EXTENSION_ERROR", "The <a href='http://php.net/mbstring'>mbstring</a> extension must be enabled.");
define("NETCAT_MODULE_SEARCH_MB_OVERLOAD_ENABLED_ERROR", "The search module ".
        "cannot work properly when string function overloading is enabled in ".
        "the <i>mbstring</i> extension. Please set <i>mbstring.func_overload</i> ".
        "option in the PHP configuration to <i>0</i>.");
define("NETCAT_MODULE_SEARCH_PCRE_UTF_ERROR", "PCRE library must be compiled with full UTF-8 support. ".
        "Please recompile PHP using PCRE library from the PHP distribution bundle ".
        "or PCRE library compiled with an <i>--enable-unicode-properties</i> option.");
define("NETCAT_MODULE_SEARCH_INDEX_DIRECTORY_NOT_WRITABLE_ERROR", "The <i>%s</i> folder must be writable. ".
        "Please check whether the path exists and its permissions.");
define("NETCAT_MODULE_SEARCH_CANNOT_OPEN_INDEX_ERROR", "Unable to open search index. ".
        "Please delete all files in the <i>%s</i> folder and reindex all sites.");

// Ошибки конфигурации сайтов
define("NETCAT_MODULE_SEARCH_SITE_WITHOUT_LANGUAGE_ERROR", "The site language is not specified in the settings of the &quot;%s&quot; site. That will cause incorrect search results on this site.");
define("NETCAT_MODULE_SEARCH_SITES_WITHOUT_LANGUAGE_ERROR", "The site language is not specified in the settings of the following sites: %s. That will cause incorrect search results on these sites.");
