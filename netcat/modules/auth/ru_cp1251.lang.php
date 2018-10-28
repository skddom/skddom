<?php

global $ADMIN_PATH;

define("NETCAT_MODULE_AUTH_DESCRIPTION", "Модуль для работы с пользователями. Возможность регистрации внешней группы пользователей, изменение собственной анкеты, пароля, восстановление пароля. Данный модуль может интегрироваться с другими модулями системы.");
define("NETCAT_MODULE_AUTH_REG_OK", "Регистрация подтверждена.");
define("NETCAT_MODULE_AUTH_REG_ERROR", "Ошибка! Регистрация не подтверждена.");
define("NETCAT_MODULE_AUTH_REG_INVALIDLINK", "Неправильная ссылка.");
define("NETCAT_MODULE_AUTH_ERR_NEEDAUTH", "Необходимо авторизоваться.");
define("NETCAT_MODULE_AUTH_CHANGEPASS_NOTEQUAL", "Пароли не совпадают. Попробуйте еще раз.");
define("NETCAT_MODULE_AUTH_ERR_NOFIELDSET", "Поле для Email не определено.");
define("NETCAT_MODULE_AUTH_ERR_NOUSERFOUND", "Пользователь не найден");
define("NETCAT_MODULE_AUTH_MSG_FILLFIELD", "Заполните одно из полей");
define("NETCAT_MODULE_AUTH_MSG_BADEMAIL", "Некорректный E-mail");
define("NETCAT_MODULE_AUTH_MSG_NEWPASSSENDED", "Подтверждающее письмо было выслано на ваш email. Для продолжения процедуры восстановления пароля откройте ссылку, находящуюся в письме.");
define("NETCAT_MODULE_AUTH_MSG_INVALID_LOGIN_FORMAT", "Неверный формат поля &laquo;Логин&raquo;, используйте буквы, цифры, знак подчёркивания, дефис или пробел.");
define("NETCAT_MODULE_AUTH_MSG_INVALID_EMAIL_FORMAT", "Неверный формат поля &laquo;Email&raquo;, используйте буквы, цифры, знак подчёркивания, дефис и точку.");
define("NETCAT_MODULE_AUTH_NEWPASS_SUCCESS", "Пароль успешно изменен.");
define("NETCAT_MODULE_AUTH_NEWPASS_ERROR", "Ошибка при изменения пароля.");

define("NETCAT_MODULE_AUTH_FORM_AND_MAIL_TEMPLATES", "Шаблоны форм и писем");
define("NETCAT_MODULE_AUTH_EXTERNAL_AUTH", "Авторизация через внешние сервисы");

define("NETCAT_MODULE_AUTH_LOGIN", "Логин");
define("NETCAT_MODULE_AUTH_ENTER", "Вход");
define("NETCAT_MODULE_AUTH_REGISTER", "Зарегистрироваться");
define("NETCAT_MODULE_AUTH_INCORRECT_LOGIN_OR_RASSWORD", "Неверно введен логин или пароль");
define("NETCAT_MODULE_AUTH_AUTHORIZATION_UPPER", "АВТОРИЗАЦИЯ");
define("NETCAT_MODULE_AUTH_AUTHORIZATION", "Авторизация");
define("NETCAT_MODULE_AUTH_FORGOT", "Забыли?");
define("NETCAT_MODULE_AUTH_PASSWORD", "Пароль");
define("NETCAT_MODULE_AUTH_FIRST_NAME", "Имя");
define("NETCAT_MODULE_AUTH_LAST_NAME", "Фамилия");
define("NETCAT_MODULE_AUTH_NICKNAME", "Ник");
define("NETCAT_MODULE_AUTH_PHOTO", "Фотография");
define("NETCAT_MODULE_AUTH_SAVE", "Сохранить логин и пароль");
define("NETCAT_MODULE_AUTH_REMEMBER_ME", "Запомнить меня");
define("NETCAT_MODULE_AUTH_NOT_NEW_MESSAGE", "Новых сообщений нет");
define("NETCAT_MODULE_AUTH_NEW_MESSAGE", "Новые сообщения");
define("NETCAT_MODULE_AUTH_HELLO", "Здравствуйте");
define("NETCAT_MODULE_AUTH_LOGOUT", "Завершить сеанс");
define("NETCAT_MODULE_AUTH_BY_TOKEN", "Войти по токену");

define("NETCAT_MODULE_AUTH_LOGIN_WAIT", "Пожалуйста, подождите");
define("NETCAT_MODULE_AUTH_LOGIN_FREE", "Логин свободен");
define("NETCAT_MODULE_AUTH_LOGIN_BUSY", "Логин занят");
define("NETCAT_MODULE_AUTH_LOGIN_INCORRECT", "Логин содержит запрещенные символы");

define("NETCAT_MODULE_AUTH_PASS_LOW", "Низкая");
define("NETCAT_MODULE_AUTH_PASS_MIDDLE", "Средняя");
define("NETCAT_MODULE_AUTH_PASS_HIGH", "Высокая");
define("NETCAT_MODULE_AUTH_PASS_VHIGH", "Очень высокая");
define("NETCAT_MODULE_AUTH_PASS_EMPTY", "Пароль не может быть пустым");
define("NETCAT_MODULE_AUTH_PASS_SHORT", "Пароль слишком короткий");

define("NETCAT_MODULE_AUTH_PASS_COINCIDE", "Пароли совпадают");
define("NETCAT_MODULE_AUTH_PASS_N_COINCIDE", "Пароли не совпадают");

define("NETCAT_MODULE_AUTH_PASS_RELIABILITY", "Надёжность:");

define("NETCAT_MODULE_AUTH_CP_NEWPASS", "Новый пароль");
define("NETCAT_MODULE_AUTH_CP_CONFIRM", "Повторите ввод пароля");
define("NETCAT_MODULE_AUTH_CP_DOBUTT", "Сменить пароль");

define("NETCAT_MODULE_AUTH_PRF_LOGIN", "Введите логин");
define("NETCAT_MODULE_AUTH_PRF_EMAIL", "Или Email");
define("NETCAT_MODULE_AUTH_PRF_EMAIL_2", "Email");
define("NETCAT_MODULE_AUTH_PRF_DOBUTT", "Восстановить пароль");

define("NETCAT_MODULE_AUTH_BUT_AUTORIZE", "Авторизоваться");
define("NETCAT_MODULE_AUTH_BUT_BACK", "Вернуться");
define("NETCAT_MODULE_AUTH_MSG_AUTHISOK", "Авторизация прошла успешно.");
define("NETCAT_MODULE_AUTH_MSG_AUTHUPISOK", "Сеанс завершен.");

define('NETCAT_MODULE_AUTH_MSG_SESSION_CLOSED', 'Сеанс завершен. <a href=\'%s\'>Вернуться</a>');
define('NETCAT_MODULE_AUTH_MSG_AUTH_SUCCESS', 'Авторизация прошла успешно. <a href=\'%s\'>Вернуться</a>');

define("NETCAT_MODULE_AUTH_ADMIN_SAVE_OK", "Настройки успешно изменены");

define("NETCAT_MODULE_AUTH_ADMIN_INFO", "Вы можете настроить отображение списка пользователей, а также структуры таблицы \"Пользователи\" в разделе \"<a href=" . $ADMIN_PATH . "field/system.php>Системные таблицы</a>\".<br/>");

// название вкладкок
define("NETCAT_MODULE_AUTH_ADMIN_TAB_INFO", "Информация");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_REGANDAUTH", "Регистрация и авторизация");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_TEMPLATES", "Шаблоны вывода");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_MAIL", "Шаблоны писем");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_SETTINGS", "Настройки");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_CLASSIC", "По логину и паролю");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_EXAUTH", "Через внешние сервисы");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_GENERAL", "Общие");
define("NETCAT_MODULE_AUTH_ADMIN_TAB_SYSTEM", "Системные");

// информация
define("NETCAT_MODULE_AUTH_ADMIN_INFO_USER_COUNT", "Общее количество пользователей");
define("NETCAT_MODULE_AUTH_ADMIN_INFO_USER_COUNT_UNCHECKED", "Выключенных пользователей");
define("NETCAT_MODULE_AUTH_ADMIN_INFO_USER_COUNT_UNCONFIRMED", "Количество пользователей, еще не подтвердившие регистрацию");
define("NETCAT_MODULE_AUTH_ADMIN_INFO_NONE", "нет");

// по логину и паролю
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_DENY_REG", "Запретить самостоятельную регистрацию");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_DENY_RECOVERY", "Запретить самостоятельно восстанавливать пароли");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_ALLOW_CYRILLIC", "Разрешить кириллицу в логинах");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_ALLOW_SPECIALCHARS", "Разрешить спецсимволы в логинах");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_ALLOW_CHANGE_LOGIN", "Разрешить менять логин после регистрации");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_BING_TO_CATALOGUE", "Привязывать пользователя к сайту");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_WITH_SUBDOMAIN", "Авторизировать на поддоменах");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_AUTH_CAPTCHA", "Настройки CAPTCHA при авторизации");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_PASS_MIN", "Минимальная длина пароля %input символов");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_REGISTRATION_FORM", "Форма регистрации");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CHECK_LOGIN", "Автоматически проверять доступность логина");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CHECK_PASS", "Автоматически проверять уровень безопасности пароля");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CHECK_PASS2", "Автоматически проверять совпадение паролей");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CHECK_AGREED", "Требовать соглашения с пользовательским соглашением");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_FIELDS_IN_REG_FORM", "Поля в форме регистрации");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_FIELDS_IN_REG_FORM_ALL", "все");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_FIELDS_IN_REG_FORM_CUSTOM", "выборочно");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_ACTIVATION", "Активация");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CONFIRM", "Требовать подтверждение через почту");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CONFIRM_AFTER_MAIL", "Отправлять дополнительное письмо после успешного подтверждения регистрации");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_PREMODARATION", "Премодерация администратором");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_NOTIFY_ADMIN", "Отправлять письмо администратору при регистрации пользователя");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_AUTHAUTORIZE", "Авторизация пользователя сразу после подтверждения");
define("NETCAT_MODULE_AUTH_ADMIN_CLASSIC_CONFIRM_TIME", "Удалять пользователя, если он не подтвердил регистрацию в течение %input часов");

// через внешние сервисы
define("NETCAT_MODULE_AUTH_ADMIN_EX_CURL_REQUIRED", "Для авторизации через внешние сервисы необходима библиотека <a href='http://www.php.net/manual/ru/book.curl.php'>cURL</a>");
define("NETCAT_MODULE_AUTH_ADMIN_EX_JSON_REQUIRED", "Для авторизации через внешние сервисы необходима библиотека <a href='http://ru2.php.net/manual/en/book.json.php'>JSON</a>");
define("NETCAT_MODULE_AUTH_ADMIN_EX_VK", "ВКонтакте");
define("NETCAT_MODULE_AUTH_ADMIN_EX_FB", "Facebook");
define("NETCAT_MODULE_AUTH_ADMIN_EX_TWITTER", "Twitter");
define("NETCAT_MODULE_AUTH_ADMIN_EX_OPENID", "OpenID");
define("NETCAT_MODULE_AUTH_ADMIN_EX_OAUTH", "OAuth");
define("NETCAT_MODULE_AUTH_ADMIN_EX_VK_ENABLED", "Включить авторизацию через vkontakte.ru");
define("NETCAT_MODULE_AUTH_ADMIN_EX_FB_ENABLED", "Включить авторизацию через facebook.com");
define("NETCAT_MODULE_AUTH_ADMIN_EX_OPENID_ENABLED", "Включить авторизацию через OpenID");
define("NETCAT_MODULE_AUTH_ADMIN_EX_OAUTH_ENABLED", "Включить авторизацию через OAuth");
define("NETCAT_MODULE_AUTH_ADMIN_EX_TWITTER_ENABLED", "Включить авторизацию через twitter.com");
define("NETCAT_MODULE_AUTH_ADMIN_EX_DATA_VK", "Данные из ВКонтакте");
define("NETCAT_MODULE_AUTH_ADMIN_EX_DATA_FB", "Данные из Facebook");
define("NETCAT_MODULE_AUTH_ADMIN_EX_DATA_TWITTER", "Данные из Twitter");
define("NETCAT_MODULE_AUTH_ADMIN_EX_DATA_OPENID", "Данные из OpenID");
define("NETCAT_MODULE_AUTH_ADMIN_EX_DATA_OAUTH", "Данные из OAuth");

// общие настройки
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_AUTH_SITE", "Способы авторизации на сайте");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_AUTH_ADMIN", "Способы авторизации в систему администрирования");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_METHOD_LOGIN", "По логину и паролю");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_METHOD_TOKEN", "По usb-токену");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_METHOD_HASH", "По хэшу");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_METHOD_EX", "Через внешние сервисы");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PM", "Личные сообщения");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PM_ALLOW", "Разрешить отправлять личные сообщения");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PM_NOTIFY", "Оповещать пользователя по email о новом сообщение");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_FRIEND_BANNED", "Друзья и враги");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_FRIEND_ALLOW", "Разрешить добавлять пользователей в друзья");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_BANNED_ALLOW", "Разрешить добавлять пользователей во враги");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PA", "Личный счет");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PA_ALLOW", "Ввести личный счет");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PA_CURRENCY", "Единица измерения %input");
define("NETCAT_MODULE_AUTH_ADMIN_GENERAL_PA_START", "Начислять вновь зарегистрированным %input единиц");

// письма
define("NETCAT_MODULE_AUTH_ADMIN_MAIL_SUBJECT", "Заголовок письма");
define("NETCAT_MODULE_AUTH_ADMIN_MAIL_BODY", "Тело письма");
define("NETCAT_MODULE_AUTH_ADMIN_MAIL_HTML", "HTML-письмо");

// системные настройки
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_COMPONENTS_SUBS", "Компоненты, разделы и поля");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_COMPONENT_FRIENDS", "Компонент \"Список друзей\"");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_COMPONENT_PM", "Компонент для личных сообщениях");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_COMPONENT_PA", "Компонент для личного счета");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_FIELD_PA", "Поле c балансом для личного счета");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_SUB_MATERIALS", "Раздел с материалами пользователя");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_SUB_MODIFY", "Разделы для изменения анкеты, через запятую");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_CC_USER_LIST", "Инфоблок в разделе со списком пользователей");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_PSEUDO", "Псевдопользователи");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_PSEUDO_ALLOW", "Доступны псевдопользователи");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_PSEUDO_CHECK_IP", "Проверять IP");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_PSEUDO_GROUP", "Группа псевдопользователей");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_PSEUDO_FIELD", "Поле, по которому идет идентификация");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_HASH", "Хэш-авторизация");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_HASH_DELETE", "Удалять хэш после авторизации");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_HASH_EXPIRE", "Время жизни хэша в часах %input");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_HASH_DISABLED_SUBS", "Номера разделов, где запрещена хэш-авторизация, через запятую %input");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_OTHER", "Прочее");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_OTHER_ONLINE", "Время, в течение которого пользователь считается online в секундах %input");
define("NETCAT_MODULE_AUTH_ADMIN_SYSTEM_OTHER_IP", "Уровень проверки IP-адреса (0-4) %input");

define("NETCAT_MODULE_AUTH_SELFREG_DISABLED", "Самостоятельная регистрация запрещена");



define("NETCAT_MODULE_AUTH_FORM_TEMPLATES", "Шаблоны форм");
define("NETCAT_MODULE_AUTH_FORM_AUTH", "Форма авторизации");
define("NETCAT_MODULE_AUTH_RESTORE_DEF", "Восстановить значения по умолчанию");
define("NETCAT_MODULE_AUTH_FORM_DISABLED", "Не показывать форму при неудачной попытке авторизации");
define("NETCAT_MODULE_AUTH_FORM_CHG_PASS", "Форма изменения пароля");
define("NETCAT_MODULE_AUTH_FORM_CHG_PASS_AFTER", "Текст, выводимый после смены пароля");
define("NETCAT_MODULE_AUTH_FORM_REC_PASS_AFTER", "Текст, выводимый после отправки письма");
define("NETCAT_MODULE_AUTH_FORM_CHG_PASS_WARNBLOCK", "Блок вывода ошибки");
define("NETCAT_MODULE_AUTH_FORM_CHG_PASS_DENY", "Текст, выводимый при запрете восстановления пароля");
define("NETCAT_MODULE_AUTH_FORM_REC_PASS", "Форма восстановления пароля");
define("NETCAT_MODULE_AUTH_FORM_CONFIRM_AFTER", "Текст, выводимый при успешном подтверждении регистрации");
define("NETCAT_MODULE_AUTH_FORM_CONFIRM_AFTER_WARNBLOCK", "Блок вывода ошибки при подтверждении регистрации");
define("NETCAT_MODULE_AUTH_MAIL_TEMPLATES", "Шаблоны писем");
define("NETCAT_MODULE_AUTH_REG_CONFIRM", "Подтверждение регистрации");
define("NETCAT_MODULE_AUTH_REG_CONFIRM_AFTER", "Уведомление после подтверждения регистрации");
define("NETCAT_MODULE_AUTH_AS_HTML", "HTML текст");
define("NETCAT_MODULE_AUTH_RECOVERY", "Восстановление пароля");
define("NETCAT_MODULE_AUTH_ADMIN_MAIL_NOTIFY", "Оповещение администратора о регистрации пользователя");

define("NETCAT_MODULE_AUTH_ADD_FRIEND", "Добавить в друзья");
define("NETCAT_MODULE_AUTH_REMOVE_FRIEND", "Удалить из друзей");

// OpenID
define("NETCAT_MODULE_AUTH_OPEN_ID_ERROR", "Неизвестный OpenID");
define("NETCAT_MODULE_AUTH_OPEN_ID_INVALID", "Неправильный OpenID");
define("NETCAT_MODULE_AUTH_OPEN_ID_ALREADY_EXIST_IN_BASE", "Такой OpenID уже зарегистрирован в базе");
define("NETCAT_MODULE_AUTH_OPEN_ID_COULD_NOT_REDIRECT_TO_SERVER", "Could not redirect to server: %s");
define("NETCAT_MODULE_AUTH_OPEN_ID_CHECK_CANCELED", "Проверка отменена");
define("NETCAT_MODULE_AUTH_OPEN_ID_AUTH_FAILED", "OpenID авторизация не удалась: %s");
define("NETCAT_MODULE_AUTH_OPEN_ID_AUTH_COMPLETE_NAME", "Вы успешно авторизовались как <a href='%s'>%s</a> ");
define("NETCAT_MODULE_AUTH_OPEN_ID_AUTH_COMPLETE_LOGIN", "Ваш логин на сайте: %s");


define("NETCAT_MODULE_AUTH_SETUP_PROFILE", "Личный кабинет");
define("NETCAT_MODULE_AUTH_SETUP_REGISTRATION", "Регистрация");
define("NETCAT_MODULE_AUTH_SETUP_PASSWORD_RECOVERY", "Восстановление пароля");
define("NETCAT_MODULE_AUTH_SETUP_MODIFY", "Изменение анкеты");
define("NETCAT_MODULE_AUTH_SETUP_PASSWORD", "Изменение пароля");
define("NETCAT_MODULE_AUTH_SETUP_PM", "Личные сообщения");


define("NETCAT_MODULE_AUTH_APPLICATION_ID", "ID приложения");
define("NETCAT_MODULE_AUTH_APPLICATION_ID_VK", "ID приложения");
define("NETCAT_MODULE_AUTH_APPLICATION_ID_FB", "ID приложения (Application ID)");
define("NETCAT_MODULE_AUTH_APPLICATION_ID_TWITTER", "ID приложения (Consumer key)");
define("NETCAT_MODULE_AUTH_SECRET_KEY", "Защищенный ключ");
define("NETCAT_MODULE_AUTH_PUBLIC_KEY", "Публичный ключ");
define("NETCAT_MODULE_AUTH_SECRET_KEY_VK", "Защищенный ключ");
define("NETCAT_MODULE_AUTH_SECRET_KEY_FB", "Защищенный ключ (Application Secret)");
define("NETCAT_MODULE_AUTH_SECRET_KEY_TWITTER", "Защищенный ключ (Consumer secret)");

define("NETCAT_MODULE_AUTH_GROUPS_WHERE_USER_WILL_BE", "Группы, куда попадёт пользователь");
define("NETCAT_MODULE_AUTH_GROUPS_WHERE_USER_WILL_BE_EMPTY", "Не выбрано ни одной группы!");
define("NETCAT_MODULE_AUTH_ACTION_BEFORE_FIRST_AUTHORIZATION", "Действие до первой авторизации пользователя");
define("NETCAT_MODULE_AUTH_ACTION_AFTER_FIRST_AUTHORIZATION", "Действие после первой авторизации пользователя");
define("NETCAT_MODULE_AUTH_ACTION_FIELDS_MAPPING", "Соответствие полей");
define("NETCAT_MODULE_AUTH_ACTION_FIELDS_MAPPING_ADD", "Добавить соответствие");
define("NETCAT_MODULE_AUTH_PROVIDER_ADD", "Добавить провайдера");
define("NETCAT_MODULE_AUTH_PROVIDER", "Провайдер");
define("NETCAT_MODULE_AUTH_PROVIDER_ICON", "Иконка провайдера");


define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_CONFIRM_SUBJECT", "Подтверждение регистрации на сайте %SITE_NAME");
define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_CONFIRM_BODY",
        "Здравствуйте, %USER_LOGIN

Вы успешно зарегистрировались на сайте <a href='%SITE_URL'>%SITE_NAME</a>
Ваш логин: 	%USER_LOGIN
Ваш пароль: 	%PASSWORD

Чтобы активировать Ваш аккаунт откройте, пожалуйста, данную ссылку:

<a href='%CONFIRM_LINK'>%CONFIRM_LINK</a>

Вы получили это сообщение, потому что Ваш email адрес был зарегистрирован на сайте %SITE_URL
Если Вы не регистрировались на этом сайте, пожалуйста, проигнорируйте это письмо.

С наилучшими пожеланиями, администрация сайта <a href='%SITE_URL'>%SITE_NAME</a>.
");

define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_CONFIRM_AFTER_SUBJECT", "Регистрация на сайте %SITE_NAME успешно подтверждена");
define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_CONFIRM_AFTER_BODY",
        "Здравствуйте, %USER_LOGIN

Ваш аккаунт успешно активирован, теперь вы можете в полной мере использовать сервисы нашего сайта.

С наилучшими пожеланиями, администрация сайта <a href='%SITE_URL'>%SITE_NAME</a>.
");

define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_PASSWORDRECOVERY_SUBJECT", "Восстановление пароля на сайте %SITE_NAME");
define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_PASSWORDRECOVERY_BODY",
        "Здравствуйте, %USER_LOGIN

Для восстановления пароля для пользователя %USER_LOGIN на сайте <a href='%SITE_URL'>%SITE_NAME</a> откройте, пожалуйста, данную ссылку:

<a href='%CONFIRM_LINK'>%CONFIRM_LINK</a>

Если Вы не запрашивали восстановление пароля, пожалуйста, проигнорируйте это письмо.

С наилучшими пожеланиями, администрация сайта <a href='%SITE_URL'>%SITE_NAME</a>.
");
define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_ADMIN_NOTIFY_SUBJECT", "Новый пользователь на сайте %SITE_NAME");
define("NETCAT_MODULE_AUTH_ADMIN_DEF_MAIL_ADMIN_NOTIFY_BODY",
        "Здравствуйте, администратор.

На сайте <a href='%SITE_URL'>%SITE_NAME</a> зарегистрирован новый пользователь с логином %USER_LOGIN

С наилучшими пожеланиями, сайт <a href='%SITE_URL'>%SITE_NAME</a>.
");
define("NETCAT_MODULE_AUTH_USER_AGREEMENT", "Я принимаю условия <a href='%USER_AGR' target='_blank'>пользовательского соглашения</a>");
define("NETCAT_MODULE_AUTH_AUTHENTICATION_FAILED", "Возникла ошибка во время аутентификации.");
define("NETCAT_MODULE_AUTH_RETRY", "Пожалуйста, обновите страницу и попробуйте еще раз, сейчас или позже.");
