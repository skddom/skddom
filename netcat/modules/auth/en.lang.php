<?php

global $ADMIN_PATH;

define("NETCAT_MODULE_AUTH_DESCRIPTION", "   
The module allows to manage such features as:  
<br/>- registration on the site
<br/>- change user profile
<br/>- change user password 
<br/>- password recovery 
<br/>You may integrate this module with others system components (modules, classes, etc.).");
define("NETCAT_MODULE_AUTH_REG_OK", "Registration was confirmed.");
define("NETCAT_MODULE_AUTH_REG_ERROR", "Error! Registration was NOT confirmed.");
define("NETCAT_MODULE_AUTH_REG_INVALIDLINK", "Invalid link.");
define("NETCAT_MODULE_AUTH_ERR_NEEDAUTH", "Need authorize.");
define("NETCAT_MODULE_AUTH_CHANGEPASS_NOTEQUAL", "Passwords are not equal. Try again.");
define("NETCAT_MODULE_AUTH_ERR_NOFIELDSET", "Field not set.");
define("NETCAT_MODULE_AUTH_ERR_NOUSERFOUND", "User not found");
define("NETCAT_MODULE_AUTH_MSG_FILLFIELD", "Fill one of these fields");
define("NETCAT_MODULE_AUTH_MSG_BADEMAIL", "Invalid E-mail");
define("NETCAT_MODULE_AUTH_MSG_NEWPASSSENDED", "The confirmation letter has been sent to your email. You should click on the link to confirm your request.");
define("NETCAT_MODULE_AUTH_MSG_INVALID_LOGIN_FORMAT", "Invalid &laquo;Login&raquo; field format, you should use alphabet, numbers, underline, hyphen and space simbol.");
define("NETCAT_MODULE_AUTH_MSG_INVALID_EMAIL_FORMAT", "Invalid &laquo;Email&raquo; field format, you should use alphabet, numbers, underline, hyphen and dot simbol.");
define("NETCAT_MODULE_AUTH_NEWPASS_SUCCESS", "The password is successfully changed.");
define("NETCAT_MODULE_AUTH_NEWPASS_ERROR", "Error while password change.");

define("NETCAT_MODULE_AUTH_FORM_AND_MAIL_TEMPLATES", "Mail templates");
define("NETCAT_MODULE_AUTH_EXTERNAL_AUTH", "Authorization through the external services");

define("NETCAT_MODULE_AUTH_LOGIN", "Login");
define("NETCAT_MODULE_AUTH_ENTER", "Enter");
define("NETCAT_MODULE_AUTH_REGISTER", "Register");
define("NETCAT_MODULE_AUTH_INCORRECT_LOGIN_OR_RASSWORD", "Username or password is incorrect");
define("NETCAT_MODULE_AUTH_AUTHORIZATION_UPPER", "AUTHORIZATION");
define("NETCAT_MODULE_AUTH_AUTHORIZATION", "Authorization");
define("NETCAT_MODULE_AUTH_FORGOT", "Forgot password");
define("NETCAT_MODULE_AUTH_PASSWORD", "Password");
define("NETCAT_MODULE_AUTH_FIRST_NAME", "Name");
define("NETCAT_MODULE_AUTH_LAST_NAME", "Surname");
define("NETCAT_MODULE_AUTH_NICKNAME", "Nickname");
define("NETCAT_MODULE_AUTH_PHOTO", "Photo");
define("NETCAT_MODULE_AUTH_SAVE", "Save login and password");
define("NETCAT_MODULE_AUTH_REMEMBER_ME", "Remember me");
define("NETCAT_MODULE_AUTH_NOT_NEW_MESSAGE", "No new messages");
define("NETCAT_MODULE_AUTH_NEW_MESSAGE", "New message");
define("NETCAT_MODULE_AUTH_HELLO", "Hello");
define("NETCAT_MODULE_AUTH_LOGOUT", "Logout");
define("NETCAT_MODULE_AUTH_BY_TOKEN", "Authorize by token");

define("NETCAT_MODULE_AUTH_LOGIN_WAIT", "Please, wait");
define("NETCAT_MODULE_AUTH_LOGIN_FREE", "Login is free");
define("NETCAT_MODULE_AUTH_LOGIN_BUSY", "Login busy");
define("NETCAT_MODULE_AUTH_LOGIN_INCORRECT", "Login contains invalid characters");

define("NETCAT_MODULE_AUTH_PASS_LOW", "Low");
define("NETCAT_MODULE_AUTH_PASS_MIDDLE", "Average");
define("NETCAT_MODULE_AUTH_PASS_HIGH", "High");
define("NETCAT_MODULE_AUTH_PASS_VHIGH", "Very high");
define("NETCAT_MODULE_AUTH_PASS_EMPTY", "The password can't be empty");
define("NETCAT_MODULE_AUTH_PASS_SHORT", "The password too short");

define("NETCAT_MODULE_AUTH_PASS_COINCIDE", "Passwords coincide");
define("NETCAT_MODULE_AUTH_PASS_N_COINCIDE", "Passwords not coincide");

define("NETCAT_MODULE_AUTH_PASS_RELIABILITY", "Reliability:");

define("NETCAT_MODULE_AUTH_CP_NEWPASS", "New password");
define("NETCAT_MODULE_AUTH_CP_CONFIRM", "Confirm new password");
define("NETCAT_MODULE_AUTH_CP_DOBUTT", "Change password");

define("NETCAT_MODULE_AUTH_PRF_LOGIN", "Enter login");
define("NETCAT_MODULE_AUTH_PRF_EMAIL", "Or Email");
define("NETCAT_MODULE_AUTH_PRF_EMAIL_2", "Email");
define("NETCAT_MODULE_AUTH_PRF_DOBUTT", "Generate new password");

define("NETCAT_MODULE_AUTH_BUT_AUTORIZE", "Authorize");
define("NETCAT_MODULE_AUTH_BUT_BACK", "Back");
define("NETCAT_MODULE_AUTH_MSG_AUTHISOK", "Authorization successful.");
define("NETCAT_MODULE_AUTH_MSG_AUTHUPISOK", "Session is closed.");

define('NETCAT_MODULE_AUTH_MSG_SESSION_CLOSED', 'Session is closed. <a href=\'%s\'>Back</a>');
define('NETCAT_MODULE_AUTH_MSG_AUTH_SUCCESS', 'Authorization successful. <a href=\'%s\'>Back</a>');

define("NETCAT_MODULE_AUTH_ADMIN_SAVE_OK", "Settings updated successfully");

define("NETCAT_MODULE_AUTH_ADMIN_INFO", "You may edit HTML of user list and add, edit or delete \"Users\" in section \"<a href=" . $ADMIN_PATH . "field/system.php>System tables</a>\".<br><br>User control in section \"<a href=" . $ADMIN_PATH . "user/>Users and rights</a>\".");

// название вкладкок
define("NETCAT_MODULE_AUTH_ADMIN_TAB_INFO", "Information");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_REGANDAUTH", "Registration and authorization");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_TEMPLATES", "Templates");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_MAIL", "Mail templates");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_SETTINGS", "Settings");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_CLASSIC", "Using login and password");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_EXAUTH", "Through external services");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_GENERAL", "General");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_SYSTEM", "System");

// информация
define("NETCAT_MODULE_AUTH_ADMIN_INFO_USER_COUNT", "The total number of users");
define("NETCAT_MODULE_AUTH_ADMIN_INFO_USER_COUNT_UNCHECKED", "The number of unchecked users");
define("NETCAT_MODULE_AUTH_ADMIN_INFO_USER_COUNT_UNCONFIRMED", "The number of users not confirmed their registration yet");
define("NETCAT_MODULE_AUTH_ADMIN_INFO_NONE", "not");

// using username and password
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_DENY_REG", "Forbid self-registration");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_DENY_RECOVERY", "Forbid password self recovery");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_ALLOW_CYRILLIC", "Allow cyrillic usernames");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_ALLOW_SPECIALCHARS", "Allow special characters in usernames");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_ALLOW_CHANGE_LOGIN", "Allow to change username after registration");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_BING_TO_CATALOGUE", "To adhere the user to a site");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_WITH_SUBDOMAIN", "Authorize on subdomains");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_AUTH_CAPTCHA", "Logon CAPTCHA settings");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_PASS_MIN", "Minimal password length %input symbols");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_REGISTRATION_FORM", "Registration form");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CHECK_LOGIN", "Check username availability automatically");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CHECK_PASS", "Check password's safety level automatically");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CHECK_PASS2", "Check for password match automatically");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CHECK_AGREED", "Demand agreement with terms of use");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_FIELDS_IN_REG_FORM", "Fields in registration form");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_FIELDS_IN_REG_FORM_ALL", "All");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_FIELDS_IN_REG_FORM_CUSTOM", "selectively");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_ACTIVATION", "Activation");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CONFIRM", "Demand confirmation via email");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CONFIRM_AFTER_MAIL", "Send additional mail after successful confirmation");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_PREMODARATION", "Pre-moderation by Administrator");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_NOTIFY_ADMIN", "Notify Administrator on user registration");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_AUTHAUTORIZE", "User authorization right after confirmation");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CONFIRM_TIME", "Delete user if registration is not confirmed after %input hours");

// using external services
define("NETCAT_MODULE_AUTH_ADMIN_EX_CURL_REQUIRED", "For authorization through external services curl library required <a href='http://www.php.net/manual/ru/book.curl.php'>cURL</a>");
define("NETCAT_MODULE_AUTH_ADMIN_EX_JSON_REQUIRED", "For authorization through external services curl library is required <a href='http://ru2.php.net/manual/en/book.json.php'>JSON</a>");
define("NETCAT_MODULE_AUTH_ADMIN_EX_VK", "VKontakte");
define("NETCAT_MODULE_AUTH_ADMIN_EX_FB", "Facebook");
define("NETCAT_MODULE_AUTH_ADMIN_EX_TWITTER", "Twitter");
define("NETCAT_MODULE_AUTH_ADMIN_EX_OPENID", "OpenID");
define("NETCAT_MODULE_AUTH_ADMIN_EX_OAUTH", "OAuth");
define("NETCAT_MODULE_AUTH_ADMIN_EX_VK_ENABLED", "Activate authorization through vkontakte.ru");
define("NETCAT_MODULE_AUTH_ADMIN_EX_FB_ENABLED", "Activate authorization through facebook.com");
define("NETCAT_MODULE_AUTH_ADMIN_EX_OPENID_ENABLED", "Activate authorization through OpenID");
define("NETCAT_MODULE_AUTH_ADMIN_EX_OAUTH_ENABLED", "Activate authorization through OAuth");
define("NETCAT_MODULE_AUTH_ADMIN_EX_TWITTER_ENABLED", "Activate authorization through twitter.com");
define("NETCAT_MODULE_AUTH_ADMIN_EX_DATA_VK", "Data from VKontakte");
define("NETCAT_MODULE_AUTH_ADMIN_EX_DATA_FB", "Data from Facebook");
define("NETCAT_MODULE_AUTH_ADMIN_EX_DATA_TWITTER", "Data from Twitter");
define("NETCAT_MODULE_AUTH_ADMIN_EX_DATA_OPENID", "Data from OpenID");
define("NETCAT_MODULE_AUTH_ADMIN_EX_DATA_OAUTH", "Data from OAuth");


// general settings
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_AUTH_SITE", "Methods of the authorization for site");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_AUTH_ADMIN", "Methods of the authorization for administration system");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_METHOD_LOGIN", "By username and password");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_METHOD_TOKEN", "By usb-token");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_METHOD_HASH", "By hash");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_METHOD_EX", "Using external services");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PM", "Personal messages");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PM_ALLOW", "Allow to send personal messages");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PM_NOTIFY", "Notify users on new message via email");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_FRIEND_BANNED", "Friends and banned users");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_FRIEND_ALLOW", "Allow to add users as friends");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_BANNED_ALLOW", "Allow user to ban other users");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PA", "Personal account");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PA_ALLOW", "Allow personal account");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PA_CURRENCY", "Personal account currency %input");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PA_START", "Add %input units to new users&#039; personal accounts");

// emails
define("NETCAT_MODULE_AUTH_ADMIN_MAIL_SUBJECT", "Email subject");
define("NETCAT_MODULE_AUTH_ADMIN_MAIL_BODY", "Email body");
define("NETCAT_MODULE_AUTH_ADMIN_MAIL_HTML", "HTML-mail");

// system settings
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_COMPONENTS_SUBS", "Components, subdivisions and fields");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_COMPONENT_FRIENDS", "Component &#034;Friend&#034; List");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_COMPONENT_PM", "Personal messages component");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_COMPONENT_PA", "Personal account component");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_FIELD_PA", "Field for personal account balance");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_SUB_MATERIALS", "Subdivision for user's ");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_SUB_MODIFY", "Users' modification subdivisions");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_CC_USER_LIST", "Component in users list subdivision");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_PSEUDO", "Pseudo users");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_PSEUDO_ALLOW", "Allow pseudo users");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_PSEUDO_CHECK_IP", "Check IP");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_PSEUDO_GROUP", "Pseudo users group");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_PSEUDO_FIELD", "Identification field");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_HASH", "Hash authorization");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_HASH_DELETE", "Delete hash after authorization");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_HASH_EXPIRE", "Hash expires in %input hours");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_HASH_DISABLED_SUBS", "Numbers of subdivision with forbidden hash-authorization, divide with comma %input");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_OTHER", "Other");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_OTHER_ONLINE", "Time user is considered online, in seconds %input");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_OTHER_IP", "Level IP-address check (0-4) %input");

define("NETCAT_MODULE_AUTH_SELFREG_DISABLED", "Self-registration is forbidden");


define("NETCAT_MODULE_AUTH_FORM_TEMPLATES", "Template forms");
define("NETCAT_MODULE_AUTH_FORM_AUTH", "Authorization form");
define("NETCAT_MODULE_AUTH_RESTORE_DEF", "Restore default settings");
define("NETCAT_MODULE_AUTH_FORM_DISABLED", "Don't show the form if registration fails");
define("NETCAT_MODULE_AUTH_FORM_CHG_PASS", "Password change form");
define("NETCAT_MODULE_AUTH_FORM_CHG_PASS_AFTER", "Text entered after password change");
define("NETCAT_MODULE_AUTH_FORM_REC_PASS_AFTER", "Text entered after sending email");
define("NETCAT_MODULE_AUTH_FORM_CHG_PASS_WARNBLOCK", "Display error block");
define("NETCAT_MODULE_AUTH_FORM_CHG_PASS_DENY", "Text entered when password change denied");
define("NETCAT_MODULE_AUTH_FORM_REC_PASS", "Password recovery form");
define("NETCAT_MODULE_AUTH_FORM_CONFIRM_AFTER", "Text entered after registration confirmed");
define("NETCAT_MODULE_AUTH_FORM_CONFIRM_AFTER_WARNBLOCK", "Display error block when registration not confirmed");
define("NETCAT_MODULE_AUTH_MAIL_TEMPLATES", "Mail templates");
define("NETCAT_MODULE_AUTH_REG_CONFIRM", "Registration confirmation");
define("NETCAT_MODULE_AUTH_REG_CONFIRM_AFTER", "Notification after registration confirmation");
define("NETCAT_MODULE_AUTH_AS_HTML", "HTML-text");
define("NETCAT_MODULE_AUTH_RECOVERY", "Password recovery");
define("NETCAT_MODULE_AUTH_ADMIN_MAIL_NOTIFY", "Notification for administrator on user registration");

define("NETCAT_MODULE_AUTH_ADD_FRIEND", "Add to friends");
define("NETCAT_MODULE_AUTH_REMOVE_FRIEND", "Remove from friends");

// OpenID
define("NETCAT_MODULE_AUTH_OPEN_ID_ERROR", "Undefined OpenID");
define("NETCAT_MODULE_AUTH_OPEN_ID_INVALID", "Invalid OpenID");
define("NETCAT_MODULE_AUTH_OPEN_ID_ALREADY_EXIST_IN_BASE", "This OpenID already exist into the base");
define("NETCAT_MODULE_AUTH_OPEN_ID_COULD_NOT_REDIRECT_TO_SERVER", "Could not redirect to server: %s");
define("NETCAT_MODULE_AUTH_OPEN_ID_CHECK_CANCELED", "Auth breaking");
define("NETCAT_MODULE_AUTH_OPEN_ID_AUTH_FAILED", "OpenID auth failed: %s");
define("NETCAT_MODULE_AUTH_OPEN_ID_AUTH_COMPLETE_NAME", "Auth successful as <a href='%s'>%s</a> ");
define("NETCAT_MODULE_AUTH_OPEN_ID_AUTH_COMPLETE_LOGIN", "Your login: %s");


define("NETCAT_MODULE_AUTH_SETUP_PROFILE", "User profile");
define("NETCAT_MODULE_AUTH_SETUP_REGISTRATION", "Registration");
define("NETCAT_MODULE_AUTH_SETUP_PASSWORD_RECOVERY", "Password recovery");
define("NETCAT_MODULE_AUTH_SETUP_MODIFY", "modification");
define("NETCAT_MODULE_AUTH_SETUP_PASSWORD", "Password change");
define("NETCAT_MODULE_AUTH_SETUP_PM", "Personal messages");


define("NETCAT_MODULE_AUTH_APPLICATION_ID", "Application ID");
define("NETCAT_MODULE_AUTH_APPLICATION_ID_VK", "ID applications");
define("NETCAT_MODULE_AUTH_APPLICATION_ID_FB", "ID applications (Application ID)");
define("NETCAT_MODULE_AUTH_APPLICATION_ID_TWITTER", "ID applications (Consumer key)");
define("NETCAT_MODULE_AUTH_SECRET_KEY", "Secret Key");
define("NETCAT_MODULE_AUTH_PUBLIC_KEY", "Public Key");
define("NETCAT_MODULE_AUTH_SECRET_KEY_VK", "Protected key");
define("NETCAT_MODULE_AUTH_SECRET_KEY_FB", "Protected key (Application Secret)");
define("NETCAT_MODULE_AUTH_SECRET_KEY_TWITTER", "Protected key (Consumer secret)");

define("NETCAT_MODULE_AUTH_GROUPS_WHERE_USER_WILL_BE", "Groups to which user will be assign");
define("NETCAT_MODULE_AUTH_GROUPS_WHERE_USER_WILL_BE_EMPTY", "No chosen group!");
define("NETCAT_MODULE_AUTH_ACTION_BEFORE_FIRST_AUTHORIZATION", "Action before user's first authorization");
define("NETCAT_MODULE_AUTH_ACTION_AFTER_FIRST_AUTHORIZATION", "Action after user's first authorization");
define("NETCAT_MODULE_AUTH_ACTION_FIELDS_MAPPING", "Field mapping");
define("NETCAT_MODULE_AUTH_ACTION_FIELDS_MAPPING_ADD", "Add mapping");
define("NETCAT_MODULE_AUTH_PROVIDER_ADD", "Add provider");
define("NETCAT_MODULE_AUTH_PROVIDER", "Provider");
define("NETCAT_MODULE_AUTH_PROVIDER_ICON", "Provider icon");


define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_CONFIRM_SUBJECT", "Confirmation of registration on %SITE_NAME");
define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_CONFIRM_BODY",
"Hello, %USER_LOGIN! <br />
You have successfully registered for <a href='%SITE_URL'>%SITE_NAME</a>.<br />
Your username: %USER_LOGIN.<br />
Your password: %PASSWORD.<br />
To activate your account, click this link: <a href='%CONFIRM_LINK'>%CONFIRM_LINK</a>.<br />
This email was sent to you because your address was registered for %SITE_URL.<br />
If you didn't register, ignore this email.<br />
With best regards, Administration of <a href='%SITE_URL'>%SITE_NAME</a>.
");

define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_CONFIRM_AFTER_SUBJECT", "Registration on %SITE_NAME successfully complete");
define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_CONFIRM_AFTER_BODY",
        "Hello, %USER_LOGIN

Your account was successfully activated? now you can use all features of this site.

With best regards, Administration of <a href='%SITE_URL'>%SITE_NAME</a>.
");

define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_PASSWORDRECOVERY_SUBJECT", "Password recovery for %SITE_NAME");
define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_PASSWORDRECOVERY_BODY",
"Hello, %USER_LOGIN!<br />
To recover password for %USER_LOGIN on <a href='%SITE_URL'>%SITE_NAME</a> click this link: <a href='%CONFIRM_LINK'>%CONFIRM_LINK</a>.<br />
If you didn&#039;t ask for password recovery, ignore this email.<br />
With best regards, Administration of <a href='%SITE_URL'>%SITE_NAME</a>.
");
define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_ADMIN_NOTIFY_SUBJECT", "New user on %SITE_NAME");
define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_ADMIN_NOTIFY_BODY",
"Hello, Administrator.<br />
New user %USER_LOGIN registered on <a href='%SITE_URL'>%SITE_NAME</a>.<br />
With best regards, Administration of <a href='%SITE_URL'>%SITE_NAME</a>.
");
define("NETCAT_MODULE_AUTH_USER_AGREEMENT", "I accept <a href='%USER_AGR'> terms and conditions </a>");
define("NETCAT_MODULE_AUTH_AUTHENTICATION_FAILED", "An error occurred during authentication.");
define("NETCAT_MODULE_AUTH_RETRY", "Please refresh the page or try again later.");
