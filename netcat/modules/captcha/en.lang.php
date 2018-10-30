<?php

define('NETCAT_MODULE_CAPTCHA_DESCRIPTION', 'Protection with image.');

if (nc_core::get_object()->get_settings('Provider', 'captcha') !== 'nc_captcha_provider_image') {
    define('NETCAT_MODULE_CAPTCHA_WRONG_CODE', 'Please confirm that you are not a robot');
    define('NETCAT_MODULE_CAPTCHA_WRONG_CODE_SMALL', 'Confirm you’re not a robot');
}
else {
    define('NETCAT_MODULE_CAPTCHA_WRONG_CODE', 'Wrong code entered');
    define('NETCAT_MODULE_CAPTCHA_WRONG_CODE_SMALL', 'Wrong code entered');
}

define('NETCAT_MODERATION_CAPTCHA', 'Enter symbols shown on the picture');
define('NETCAT_MODERATION_CAPTCHA_SMALL', 'Symbols<br/>on the picture');
define("NETCAT_MODULE_CAPTCHA_AUDIO_LISTEN", "Listen");
define('NETCAT_MODULE_CAPTCHA_REFRESH', "Refresh");

define('NETCAT_MODULE_CAPTCHA_SETTINGS_SAVE', 'Save');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_SAVED', 'Settings were saved');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_USE_DEFAULT', 'use <a href="%s" target="_top">common settings</a>');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_PROVIDER', 'CAPTCHA type');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_PROVIDER_IMAGE', 'Image and audio');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_PROVIDER_RECAPTCHA', 'reCAPTCHA');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_CHARACTERS', 'Characters used on the image');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_LENGTH', 'Number of characters on the image');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_EXPIRES', 'Image expiration time in seconds');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_WIDTH', 'Image width');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_HEIGHT', 'Image height');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_LINES', 'Number of lines on the image');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_AUDIO_ENABLED', 'enable audioCAPTCHA');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_AUDIO_VOICE', 'AudioCAPTCHA voice');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_NO_GD', 'Cannot generate images because GD library is disabled in PHP.');

define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_SITE_KEY', 'Site key');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_SECRET_KEY', 'Secret key');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_ADD_KEYS', 'Specify site and secret keys. You can get keys at <a href="https://www.google.com/recaptcha/admin" target="_blank">https://www.google.com/recaptcha/admin</a>.');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_INVALID_SECRET', 'Please <a href="https://www.google.com/recaptcha/admin" target="_blank" title="reCAPTCHA control panel">check</a> the secret key value.');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_NO_CURL', 'Cannot use reCAPTCHA because cURL library is disabled in PHP.');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_NO_CONNECTION', 'Unable to connect to Google server to verify settings.');

define('NETCAT_MODULE_CAPTCHA_SETTINGS_LEGACY_MODE', 'Compatibility mode for templates designed for Netcat CAPTCHA');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_REMOVED_LEGACY_TEXT', 'Text to hide in forms (one phrase per line)');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_REMOVED_LEGACY_BLOCKS', 'Blocks to hide in forms (one CSS selector per line)');
