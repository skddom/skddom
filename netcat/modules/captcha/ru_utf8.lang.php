<?php

define('NETCAT_MODULE_CAPTCHA_DESCRIPTION', 'Защита форм картинкой');

if (nc_core::get_object()->get_settings('Provider', 'captcha') !== 'nc_captcha_provider_image') {
    define('NETCAT_MODULE_CAPTCHA_WRONG_CODE', 'Пожалуйста, подтвердите, что вы не робот');
    define('NETCAT_MODULE_CAPTCHA_WRONG_CODE_SMALL', 'Подтвердите, что вы не робот');
}
else {
    define('NETCAT_MODULE_CAPTCHA_WRONG_CODE', 'Неправильно введены символы, изображенные на картинке');
    define('NETCAT_MODULE_CAPTCHA_WRONG_CODE_SMALL', 'Неправильно введены символы');
}

define('NETCAT_MODERATION_CAPTCHA', 'Введите символы, изображенные на картинке');
define('NETCAT_MODERATION_CAPTCHA_SMALL', 'Символы<br/>на картинке');
define("NETCAT_MODULE_CAPTCHA_AUDIO_LISTEN", "Прослушать");
define('NETCAT_MODULE_CAPTCHA_REFRESH', "Обновить");

define('NETCAT_MODULE_CAPTCHA_SETTINGS_SAVE', 'Сохранить');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_SAVED', 'Настройки сохранены');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_USE_DEFAULT', 'использовать <a href="%s" target="_top">общие настройки для всех сайтов</a>');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_PROVIDER', 'Тип CAPTCHA');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_PROVIDER_IMAGE', 'Картинка и аудио');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_PROVIDER_RECAPTCHA', 'reCAPTCHA');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_CHARACTERS', 'Символы, используемые на картинке');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_LENGTH', 'Количество символов на картинке');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_EXPIRES', 'Срок действия кода на картинке в секундах');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_WIDTH', 'Ширина картинки');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_HEIGHT', 'Высота картинки');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_LINES', 'Число линий на картинке');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_AUDIO_ENABLED', 'включить аудиокаптчу');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_AUDIO_VOICE', 'Голос для аудиокаптчи');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_IMAGE_NO_GD', 'Библиотека GD не включена в PHP, генерация картинок для защиты форм невозможна.');

define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_SITE_KEY', 'Ключ');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_SECRET_KEY', 'Секретный ключ');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_ADD_KEYS', 'Укажите публичный и секретный ключи. Получить ключи можно на странице <a href="https://www.google.com/recaptcha/admin" target="_blank">https://www.google.com/recaptcha/admin</a>.');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_INVALID_SECRET', '<a href="https://www.google.com/recaptcha/admin" target="_blank" title="Панель управления reCAPTCHA">Проверьте</a>, правильно ли указан секретный ключ.');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_NO_CURL', 'Библиотека cURL не включена в PHP, использование reCAPTCHA невозможно.');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_RECAPTCHA_NO_CONNECTION', 'Не удалось подключиться к серверу Google для проверки параметров.');

define('NETCAT_MODULE_CAPTCHA_SETTINGS_LEGACY_MODE', 'Режим совместимости с шаблонами, предназначенными для использования со встроенной каптчей Netcat');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_REMOVED_LEGACY_TEXT', 'Текст, убираемый в форме (по одной фразе в строке)');
define('NETCAT_MODULE_CAPTCHA_SETTINGS_REMOVED_LEGACY_BLOCKS', 'Блоки, убираемые в форме (по одному CSS-селектору блока в строке)');
