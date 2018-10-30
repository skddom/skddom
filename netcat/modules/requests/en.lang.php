<?php

define("NETCAT_MODULE_REQUESTS", "Requests");
define("NETCAT_MODULE_REQUESTS_DESCRIPTION", "Module for viewing requests and creating forms.");

define("NETCAT_MODULE_REQUESTS_FORM_SETTINGS_FIELDS_HEADER", "Form fields");
define("NETCAT_MODULE_REQUESTS_FORM_POPUP_BUTTON_SETTINGS_HEADER", "Open form button");
define("NETCAT_MODULE_REQUESTS_FORM_SUBMIT_BUTTON_SETTINGS_HEADER", "Submit button");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_DEFAULT_TEXT", "Inquiry");
define("NETCAT_MODULE_REQUESTS_FORM_OPEN_POPUP_FORM", "Open the form");
define("NETCAT_MODULE_REQUESTS_FORM_HAS_NO_FIELDS", "No fields are selected for this form");
define("NETCAT_MODULE_REQUESTS_FORM_HEADER_CAPTION", "Form header");
define("NETCAT_MODULE_REQUESTS_FORM_TEXT_AFTER_HEADER_CAPTION", "Form sub-header");
define("NETCAT_MODULE_REQUESTS_FORM_NOTIFICATION_EMAIL_CAPTION", "E-mail data to (for all forms on the page)");
define("NETCAT_MODULE_REQUESTS_FORM_NOTIFICATION_EMAIL_PLACEHOLDER", "For example: mail1@example.com, mail2@example.com...");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_TEXT", "Text");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_COLOR", "Color");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_PRICE", "Show price");
define("NETCAT_MODULE_REQUESTS_FORM_SUBMIT_EVENT_CATEGORIES", "Google Analytics and Yandex.Metrika event categories on form submit");
define("NETCAT_MODULE_REQUESTS_FORM_SUBMIT_EVENT_LABELS", "Event labels on form submit");
define("NETCAT_MODULE_REQUESTS_FORM_OPEN_EVENT_CATEGORIES", "Google Analytics and Yandex.Metrika event categories on form opening");
define("NETCAT_MODULE_REQUESTS_FORM_OPEN_EVENT_LABELS", "Event labels on form opening");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_SCOPE_SUBDIVISION", "for the whole page");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_SCOPE_INFOBLOCK", "extra goals for this block");
define("NETCAT_MODULE_REQUESTS_FORM_SUBDIVISION_SYNC_HINT", "Fields will be set for all forms on the current page.");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_ACTION_HINT_OPEN_POPUP", "This button shows the form over the current page.");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_ACTION_HINT_CREATE_ORDER", "This button creates an order in the shop.");
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_ACTION_HINT_CREATE_REQUEST", "This button creates a request.");

define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_ACTION_HINT_STATS_DISABLED",
    "The 'Statistics' module is disabled, events will be not registered in Google Analytics and Yandex.Metrika.
    <a href='%s' target='_blank'>Change modules settings</a>"
);
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_ACTION_HINT_ANALYTICS_DISABLED",
    "Google Analytics and Yandex.Metrika handler is disabled in the 'Statistics' module settings, events will be not registered.
    <a href='%s' target='_blank'>Change settings</a>"
);
define("NETCAT_MODULE_REQUESTS_FORM_BUTTON_ACTION_HINT_ANALYTICS_NO_COUNTERS",
    "Neither Google Analytics nor Yandex.Metrika tracking code is set in the 'Statistics' module settings, events will be not registered.
    <a href='/netcat/admin/#module.stats.analytics' target='_blank'>Set tracking code</a>"
);

define("NETCAT_MODULE_REQUESTS_REQUEST_FILTER", "Фильтр заявок");

define("NETCAT_MODULE_REQUESTS_REQUEST_NEW", "new");
define("NETCAT_MODULE_REQUESTS_REQUEST_ANY", "any");

define("NETCAT_MODULE_REQUESTS_REQUEST_SEARCH", "ID, name, phone, E-mail or source");
define("NETCAT_MODULE_REQUESTS_DATE_FILTER", "Date");
define("NETCAT_MODULE_REQUESTS_DATE_FILTER_FROM", "from");
define("NETCAT_MODULE_REQUESTS_DATE_FILTER_TO", "to");
define("NETCAT_MODULE_REQUESTS_REQUEST_FILTER_SUBMIT", "Search");
define("NETCAT_MODULE_REQUESTS_REQUEST_FILTER_RESET", "Clear form");
define("NETCAT_MODULE_REQUESTS_REQUEST_FILTER_RESET_CONFIRM", "Are you sure you want to clear form?");
define("NETCAT_MODULE_REQUESTS_REQUEST_DELETE_SELECTED", "Delete selected");
define("NETCAT_MODULE_REQUESTS_REQUEST_DELETE_SELECTED_CONFIRM", "Delete selected requests?");

define("NETCAT_MODULE_REQUESTS_REQUEST_STATUS", "Request status");
define('NETCAT_MODULE_REQUESTS_CONFIRM_STATUS_CHANGE', 'Confirm status change');
define('NETCAT_MODULE_REQUESTS_CONFIRM_STATUS_CHANGE_TO', 'Change request status to «%s»?');

define("NETCAT_MODULE_REQUESTS_REQUEST_NUMBER", "Request №");
define("NETCAT_MODULE_REQUESTS_REQUEST_EDIT", "Edit request");

define("NETCAT_MODULE_REQUESTS_ITEM_DISCOUNT", "Discount (promo page)");
define("NETCAT_MODULE_REQUESTS_ITEM_DISCOUNT_DESCRIPTION", "Discount value is set in the promo page settings");

define("NETCAT_MODULE_REQUESTS_DEFAULT_NOTIFICATION_EMAIL", "Default notification e-mail addresses (used when notification address is not set in the form settings)");
define("NETCAT_MODULE_REQUESTS_NOTIFICATION_EMAIL_SUBJECT", "New request from %s");
define("NETCAT_MODULE_REQUESTS_REQUEST_ADMIN_LINK", "Requests in the admin panel");
define("NETCAT_MODULE_REQUESTS_SETTINGS_HEADER", "Settings");
define("NETCAT_MODULE_REQUESTS_SAVE", "Save");
define("NETCAT_MODULE_REQUESTS_SETTINGS_SAVED", "Settings saved");
