<?php

/**
 * Функция перевода русских букв в латиницу
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
define("MAIN_LANG", "ru");
define("MAIN_NAME", "Russian");
define("MAIN_ENCODING", $nc_core->NC_CHARSET);
define("MAIN_EMAIL_ENCODING", $nc_core->NC_CHARSET);
define("NETCAT_RUALPHABET", "а-яА-ЯёЁ");

define("NETCAT_TREE_SITEMAP", "Карта сайта");
define("NETCAT_TREE_MODULES", "Модули и виджеты");
define("NETCAT_TREE_USERS", "Пользователи");

define("NETCAT_TREE_PLUS_TITLE", "Раскрыть список");
define("NETCAT_TREE_MINUS_TITLE", "Свернуть список");

define("NETCAT_TREE_QUICK_SEARCH", "Быстрый поиск");

// Tabs
define("NETCAT_TAB_REFRESH", "Обновить");

define("STRUCTURE_TAB_SUBCLASS_ADD", "Добавить инфоблок");
define("STRUCTURE_TAB_INFO", "Информация");
define("STRUCTURE_TAB_SETTINGS", "Настройки");
define("STRUCTURE_TAB_USED_SUBCLASSES", "Инфоблоки");
define("STRUCTURE_TAB_EDIT", "Редактирование");
define("STRUCTURE_TAB_PREVIEW", "Просмотр &rarr;");
define("STRUCTURE_TAB_PREVIEW_SITE", "Перейти на сайт &rarr;");


define("CLASS_TAB_INFO", "Настройки");
define("CLASS_TAB_EDIT", "Редактирование компонента");
define("CLASS_TAB_CUSTOM_ACTION", "Шаблоны действий");
define("CLASS_TAB_CUSTOM_ADD", "Добавление");
define("CLASS_TAB_CUSTOM_EDIT", "Изменение");
define("CLASS_TAB_CUSTOM_DELETE", "Удаление");
define("CLASS_TAB_CUSTOM_SEARCH", "Поиск");

# BeginHtml
define("BEGINHTML_TITLE", "Администрирование");
define("BEGINHTML_USER", "Пользователь");
define("BEGINHTML_VERSION", "версия");
define("BEGINHTML_PERM_GUEST", "гостевой доступ");
define("BEGINHTML_PERM_DIRECTOR", "директор");
define("BEGINHTML_PERM_SUPERVISOR", "супервизор");
define("BEGINHTML_PERM_CATALOGUEADMIN", "администратор сайта");
define("BEGINHTML_PERM_SUBDIVISIONADMIN", "администратор раздела");
define("BEGINHTML_PERM_SUBCLASSADMIN", "администратор компонента раздела");
define("BEGINHTML_PERM_CLASSIFICATORADMIN", "администратор списка");
define("BEGINHTML_PERM_MODERATOR", "модератор");

define("BEGINHTML_LOGOUT", "выход из системы");
define("BEGINHTML_LOGOUT_OK", "Сеанс завершен.");
define("BEGINHTML_LOGOUT_RELOGIN", "Войти под другим именем");
define("BEGINHTML_LOGOUT_IE", "Для завершения сеанса закройте все окна браузера.");


define("BEGINHTML_ALARMON", "Непрочитанные системные сообщения");
define("BEGINHTML_ALARMOFF", "Системные сообщения: непрочитанных нет");
define("BEGINHTML_ALARMVIEW", "Просмотр системного сообщения");
define("BEGINHTML_HELPNOTE", "подсказка");

# EndHTML
define("ENDHTML_NETCAT", "НетКэт");

# Common
define("NETCAT_ADMIN_DELETE_SELECTED", "Удалить выбранное");
define("NETCAT_SELECT_SUBCLASS_DESCRIPTION", "В разделе &laquo;%s&raquo;, имеется несколько компонентов типа &laquo;%s&raquo;.<br />
  Выберите компонент раздела, в который нужно перенести объект, нажав на название компонента.");

# INDEX PAGE
define("SECTION_INDEX_SITES_SETTINGS", "Настройки сайтов");
define("SECTION_INDEX_MODULES_MUSTHAVE", "не установленные");
define("SECTION_INDEX_MODULES_DESCRIPTION", "описание");
define("SECTION_INDEX_MODULES_TRANSITION", "Переход на старшие редакции");
define("DASHBOARD_WIDGET", "Панель виджетов");
define("DASHBOARD_ADD_WIDGET", "Добавить виджет");
define("DASHBOARD_DEFAULT_WIDGET", "Виджеты по умолчанию");
define("DASHBOARD_WIDGET_SYS_NETCAT", "О системе");
define("DASHBOARD_WIDGET_MOD_AUTH", "Статистика ЛК");
define("DASHBOARD_UPDATES_EXISTS", "есть обновления");
define("DASHBOARD_UPDATES_DONT_EXISTS", "нет обновлений");
define("DASHBOARD_DONT_ACTIVE", "неактивированных");
define("DASHBOARD_TODAY", "сегодня");
define("DASHBOARD_YESTERDAY", "вчера");
define("DASHBOARD_PER_WEEK", "в неделю");
define("DASHBOARD_WAITING", "ждут");


# MODULES LIST
define("NETCAT_MODULE_DEFAULT", "Интерфейс разработчика");
define("NETCAT_MODULE_AUTH", "Личный кабинет");
define("NETCAT_MODULE_SEARCH", "Поиск по сайту");
define("NETCAT_MODULE_SERCH", "Поиск по сайту (старая версия)");
define("NETCAT_MODULE_POLL", "Голосование (опросник)");
define("NETCAT_MODULE_ESHOP", "Интернет-магазин (старый)");
define("NETCAT_MODULE_STATS", "Статистика посещений");
define("NETCAT_MODULE_SUBSCRIBE", "Подписка и рассылка");
define("NETCAT_MODULE_BANNER", "Управление рекламой");
define("NETCAT_MODULE_FORUM", "Форум");
define("NETCAT_MODULE_FORUM2", "Форум v2");
define("NETCAT_MODULE_NETSHOP", "Интернет-магазин");
define("NETCAT_MODULE_LINKS", "Управление ссылками");
define("NETCAT_MODULE_CAPTCHA", "Защита форм картинкой");
define("NETCAT_MODULE_TAGSCLOUD", "Облако тегов");
define("NETCAT_MODULE_BLOG", "Блог и сообщество");
define("NETCAT_MODULE_CALENDAR", "Календарь");
define("NETCAT_MODULE_COMMENTS", "Комментарии");
define("NETCAT_MODULE_LOGGING", "Логирование");
define("NETCAT_MODULE_FILEMANAGER", "Файл-менеджер");
define("NETCAT_MODULE_CACHE", "Кэширование");
define("NETCAT_MODULE_MINISHOP", "Минимагазин");
define("NETCAT_MODULE_ROUTING", "Маршрутизация");

define("NETCAT_MODULE_NETSHOP_MODULEUNCHECKED", "Модуль \"Интернет-магазин\" не установлен или выключен!");
# /MODULES LIST

define("SECTION_INDEX_USER_STRUCT_CLASSIFICATOR", "Списки");

define("SECTION_INDEX_USER_RIGHTS_TYPE", "Тип прав");
define("SECTION_INDEX_USER_RIGHTS_RIGHTS", "Права");

define("SECTION_INDEX_USER_USER_MAIL", "Рассылка по базе");
define("SECTION_INDEX_USER_SUBSCRIBERS", "Подписки пользователя");

define("SECTION_INDEX_DEV_CLASSES", "Компоненты");
define("SECTION_INDEX_DEV_CLASS_TEMPLATES", "Шаблоны компонента");
define("SECTION_INDEX_DEV_TEMPLATES", "Макеты дизайна");


define("SECTION_INDEX_ADMIN_PATCHES_INFO", "Системная информация");
define("SECTION_INDEX_ADMIN_PATCHES_INFO_VERSION", "Версия системы");
define("SECTION_INDEX_ADMIN_PATCHES_INFO_REDACTION", "Редакция системы");
define("SECTION_INDEX_ADMIN_PATCHES_INFO_LAST_PATCH", "Последнее обновление");
define("SECTION_INDEX_ADMIN_PATCHES_INFO_LAST_PATCH_DATE", "Последняя проверка обновлений");
define("SECTION_INDEX_ADMIN_PATCHES_INFO_CHECK_PATCH", "Проверить наличие обновлений");

define("SECTION_INDEX_REPORTS_STATS", "Общая статистика проекта");
define("SECTION_INDEX_REPORTS_SYSTEM", "Системные сообщения");



# SECTION CONTROL
define("SECTION_CONTROL_CONTENT_CATALOGUE", "Сайты");
define("SECTION_CONTROL_CONTENT_FAVORITES", "Быстрое редактирование");
define("SECTION_CONTROL_CONTENT_CLASSIFICATOR", "Списки");

# SECTION USER
define("SECTION_CONTROL_USER", "Пользователи");
define("SECTION_CONTROL_USER_LIST", "Список пользователей");
define("SECTION_CONTROL_USER_PERMISSIONS", "Пользователи и права");
define("SECTION_CONTROL_USER_GROUP", "Группы пользователей");
define("SECTION_CONTROL_USER_MAIL", "Рассылка по базе");

# SECTION CLASS
define("SECTION_CONTROL_CLASS", "Компоненты");
define("CONTROL_CLASS_USE_CAPTCHA", "Защищать форму добавления картинкой");
define("CONTROL_CLASS_CACHE_FOR_AUTH", "Кэширование по авторизации");
define("CONTROL_CLASS_CACHE_FOR_AUTH_NONE", "Не использовать");
define("CONTROL_CLASS_CACHE_FOR_AUTH_USER", "Учитывать каждого пользователя");
define("CONTROL_CLASS_CACHE_FOR_AUTH_GROUP", "Учитывать основную группу пользователя");
define("CONTROL_CLASS_CACHE_FOR_AUTH_DESCRIPTION", "Если в компоненте нужно выводить данные уникальные для каждого пользователя, эта настройка позволит выбрать требуемые условия.");
define("CLASSIFICATOR_TYPEOFDATA_MULTIFILE", "Множественная загрузка файлов");
define("CLASSIFICATOR_TYPEOFDATA_NOONE", "Недоступно никому");
define("CONTROL_CLASS_ADMIN", "Администрирование");
define("CONTROL_CLASS_CONTROL", "Управление");
define("CONTROL_CLASS_FIELDSLIST", "Список полей");
define("CONTROL_CLASS_CLASS_GOTOFIELDS", "Перейти к списку полей компонента");
define("CONTROL_CLASS_CLASSFORM_ADDITIONAL_INFO", "Дополнительная информация");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SORTNOTE", "Название_поля_1[ DESC][, Название_поля_2[ DESC]][, ...]<br>DESC - сортировка по убыванию");
define("CONTROL_CLASS_CLASS_SHOW_VAR_FUNC_LIST", "Показать список переменных и функций");
define("CONTROL_CLASS_CLASS_SHOW_VAR_LIST", "Показать список переменных");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_AUTODEL", "Удалять объекты через");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_AUTODELEND", "дней после добавления");
define("CONTROL_CLASS_CLASS_FORMS_YES", "Да");
define("CONTROL_CLASS_CLASS_FORMS_NO", "Нет");
define("CONTROL_CLASS_CLASS_FORMS_ADDFORM", "Альтернативная форма добавления объекта");
define("CONTROL_CLASS_CLASS_FORMS_ADDFORM_GEN", "сгенерировать код формы");
define("CONTROL_CLASS_CLASS_FORMS_ADDRULES", "Условия добавления объекта");
define("CONTROL_CLASS_CLASS_FORMS_ADDCOND_GEN", "сгенерировать код условия");
define("CONTROL_CLASS_CLASS_FORMS_ADDLASTACTION", "Действие после добавления объекта");
define("CONTROL_CLASS_CLASS_FORMS_ADDACTION_GEN", "сгенерировать код действия");
define("CONTROL_CLASS_CLASS_FORMS_EDITFORM", "Альтернативная форма изменения объекта");
define("CONTROL_CLASS_CLASS_FORMS_EDITFORM_GEN", "сгенерировать код формы");
define("CONTROL_CLASS_CLASS_FORMS_EDITRULES", "Условия изменения объекта");
define("CONTROL_CLASS_CLASS_FORMS_EDITCOND_GEN", "сгенерировать код условия");
define("CONTROL_CLASS_CLASS_FORMS_EDITLASTACTION", "Действие после изменения объекта");
define("CONTROL_CLASS_CLASS_FORMS_EDITACTION_GEN", "сгенерировать код действия");
define("CONTROL_CLASS_CLASS_FORMS_ONONACTION", "Действие после включения и выключения объекта");
define("CONTROL_CLASS_CLASS_FORMS_CHECKACTION_GEN", "сгенерировать код действия");
define("CONTROL_CLASS_CLASS_FORMS_DELETEFORM", "Альтернативная форма удаления объекта");
define("CONTROL_CLASS_CLASS_FORMS_DELETERULES", "Условие удаления объекта");
define("CONTROL_CLASS_CLASS_FORMS_ONDELACTION", "Действие после удаления объекта");
define("CONTROL_CLASS_CLASS_FORMS_DELETEACTION_GEN", "сгенерировать код действия");
define("CONTROL_CLASS_CLASS_FORMS_QSEARCH", "Форма поиска перед списком объектов");
define("CONTROL_CLASS_CLASS_FORMS_QSEARCH_GEN", "сгенерировать код формы");
define("CONTROL_CLASS_CLASS_FORMS_SEARCH", "Форма расширенного поиска (на отдельной странице)");
define("CONTROL_CLASS_CLASS_FORMS_SEARCH_GEN", "сгенерировать код формы");
define("CONTROL_CLASS_CLASS_FORMS_MAILRULES", "Условия для подписки");
define("CONTROL_CLASS_CLASS_FORMS_MAILTEXT", "Шаблон письма для подписчиков");
define("CONTROL_CLASS_CLASS_FORMS_QSEARCH_GEN_WARN", "Поле \\\"".CONTROL_CLASS_CLASS_FORMS_QSEARCH."\\\" не пустое! Заменить текст в этом поле на новый?");
define("CONTROL_CLASS_CLASS_FORMS_SEARCH_GEN_WARN", "Поле \\\"".CONTROL_CLASS_CLASS_FORMS_SEARCH."\\\" не пустое! Заменить текст в этом поле на новый?");
define("CONTROL_CLASS_CLASS_FORMS_ADDFORM_GEN_WARN", "Поле \\\"".CONTROL_CLASS_CLASS_FORMS_ADDFORM."\\\" не пустое! Заменить текст в этом поле на новый?");
define("CONTROL_CLASS_CLASS_FORMS_EDITFORM_GEN_WARN", "Поле \\\"".CONTROL_CLASS_CLASS_FORMS_EDITFORM."\\\" не пустое! Заменить текст в этом поле на новый?");
define("CONTROL_CLASS_CLASS_FORMS_ADDCOND_GEN_WARN", "Поле \\\"".CONTROL_CLASS_CLASS_FORMS_ADDRULES."\\\" не пустое! Заменить текст в этом поле на новый?");
define("CONTROL_CLASS_CLASS_FORMS_EDITCOND_GEN_WARN", "Поле \\\"".CONTROL_CLASS_CLASS_FORMS_EDITRULES."\\\" не пустое! Заменить текст в этом поле на новый?");
define("CONTROL_CLASS_CLASS_FORMS_ADDACTION_GEN_WARN", "Поле \\\"".CONTROL_CLASS_CLASS_FORMS_ADDLASTACTION."\\\" не пустое! Заменить текст в этом поле на новый?");
define("CONTROL_CLASS_CLASS_FORMS_EDITACTION_GEN_WARN", "Поле \\\"".CONTROL_CLASS_CLASS_FORMS_EDITLASTACTION."\\\" не пустое! Заменить текст в этом поле на новый?");
define("CONTROL_CLASS_CLASS_FORMS_CHECKACTION_GEN_WARN", "Поле \\\"".CONTROL_CLASS_CLASS_FORMS_ONONACTION."\\\" не пустое! Заменить текст в этом поле на новый?");
define("CONTROL_CLASS_CLASS_FORMS_DELETEACTION_GEN_WARN", "Поле \\\"".CONTROL_CLASS_CLASS_FORMS_ONDELACTION."\\\" не пустое! Заменить текст в этом поле на новый?");
define("CONTROL_CLASS_CUSTOM_SETTINGS_ISNOTSET", "Настройки отображения компонента раздела отсутствуют.");
define("CONTROL_CLASS_CUSTOM_SETTINGS_INHERIT_FROM_PARENT", "Настройки отображения шаблона компонента задаются в самом компоненте.");

# SECTION WIDGET
define("WIDGETS", "Виджеты");
define("WIDGETS_LIST_IMPORT", "Импорт");
define("WIDGETS_LIST_ADD", "Добавить");
define("WIDGETS_PARAMS", "Параметры");
define("SECTION_INDEX_DEV_WIDGET", "Виджет-компоненты");
define("CONTROL_WIDGETCLASS_ADD", "Добавить виджет");
define("WIDGET_LIST_NAME", "Название");
define("WIDGET_LIST_CATEGORY", "Категория");
define("WIDGET_LIST_ALL", "Все");
define("WIDGET_LIST_GO", "Перейти");
define("WIDGET_LIST_FIELDS", "Поля");
define("WIDGET_LIST_DELETE", "Удалить");
define("WIDGET_LIST_DELETE_WIDGETCLASS", "Виджет-компонент:");
define("WIDGET_LIST_DELETE_WIDGET", "Виджеты:");
define("WIDGET_LIST_EDIT", "Редактирование");
define("WIDGET_LIST_AT", "Шаблоны действия");
define("WIDGET_LIST_ADDWIDGET", "Добавить виджет-компонент");
define("WIDGET_LIST_DELETE_SELECTED", "Удалить выбранное");
define("WIDGET_LIST_ERROR_DELETE", "Сначала выберите виджет-компоненты для удаления");
define("WIDGET_LIST_INSERT_CODE", "код для вставки");
define("WIDGET_LIST_INSERT_CODE_CLASS", "Код для вставки в макет/компонент");
define("WIDGET_LIST_INSERT_CODE_TEXT", "Код для вставки в текст");
define("WIDGET_LIST_LOAD", "Загрузка...");
define("WIDGET_LIST_PREVIEW", "превью");
define("WIDGET_LIST_EXPORT", "Экспортировать виджет-компонент в файл");
define("WIDGET_ADD_CREATENEW", "Создать новый виджет-компонент &quot;с нуля&quot;");
define("WIDGET_ADD_CONTINUE", "Продолжить");
define("WIDGET_ADD_CREATENEW_BASICOLD", "Создать новый виджет-компонент на основе существующего");
define("WIDGET_ADD_NAME", "Название");
define("WIDGET_ADD_KEYWORD", "Ключевое слово");
define("WIDGET_ADD_UPDATE", "Обновлять виджеты каждые N минут (0 - не обновлять)");
define("WIDGET_ADD_NEWGROUP", "новая группа");
define("WIDGET_ADD_DESCRIPTION", "Описание виджет-компонента");
define("WIDGET_ADD_OBJECTVIEW", "Шаблон отображения");
define("WIDGET_ADD_PAGEBODY", "Отображение объекта");
define("WIDGET_ADD_DOPL", "Дополнительно");
define("WIDGET_ADD_DEVELOP", "В разработке");
define("WIDGET_ADD_SYSTEM", "Системные настройки");
define("WIDGETCLASS_ADD_ADD", "Добавить виджет-компонент");
define("WIDGET_ADD_ADD", "Добавить виджет");
define("WIDGET_ADD_ERROR_NAME", "Введите название виджет-компонента");
define("WIDGET_ADD_ERROR_KEYWORD", "Введите ключевое слово");
define("WIDGET_ADD_ERROR_KEYWORD_EXIST", "Ключевое слово должно быть уникальным");
define("WIDGET_ADD_WK", "Виджет-компонент");
define("WIDGET_ADD_OK", "Виджет успешно добавлен");
define("WIDGET_ADD_DISALLOW", "Запретить встраивание в объект");
define("WIDGET_IS_STATIC", "Статичный виджет");
define("WIDGET_EDIT_SAVE", "Сохранить изменения");
define("WIDGET_EDIT_OK", "Изменения сохранены");
define("WIDGET_INFO_DESCRIPTION", "Описание виджет-компонента");
define("WIDGET_INFO_DESCRIPTION_NONE", "Описание отсутствует");
define("WIDGET_INFO_PREVIEW", "Превью");
define("WIDGET_INFO_INSERT", "Код для вставки в макет/компонент");
define("WIDGET_INFO_INSERT_TEXT", "Код для вставки в текст");
define("WIDGET_INFO_GENERATE", "Пример синтаксиса для динамической вставки в макет/компонент");
define("WIDGET_DELETE_WARNING", "Внимание: виджет-компонент%s и все с ним%s связанное будет удалено.");
define("WIDGET_DELETE_CONFIRMDELETE", "Подтвердить удаление");
define("WIDGET_DELETE", "Внимание: Виджет будет удалён.");
define("WIDGET_ACTION_ADDFORM", "Альтернативная форма добавления объекта");
define("WIDGET_ACTION_EDITFORM", "Альтернативная форма изменения объекта");
define("WIDGET_ACTION_BEFORE_SAVE", "Действие перед сохранением объекта");
define("WIDGET_ACTION_AFTER_SAVE", "Действие после сохранения объекта");
define("WIDGET_IMPORT", "Импортировать");
define("WIDGET_IMPORT_TAB", "Импорт");
define("WIDGET_IMPORT_CHOICE", "Выберите файл");
define("WIDGET_IMPORT_ERROR", "Ошибка добавления файла");
define("WIDGET_IMPORT_OK", "Виджет-компонент успешно импортирован");

define("SECTION_CONTROL_WIDGET", "Виджеты");
define("SECTION_CONTROL_WIDGETCLASS", "Виджет-компоненты");
define("SECTION_CONTROL_WIDGET_LIST", "Список виджетов");
define("CONTROL_WIDGET_ACTIONS_EDIT", "Редактирование");
define("CONTROL_WIDGET_NONE", "В системе нет ни одного виджет-компонента");
define("TOOLS_WIDGET", "Виджеты");
define("CONTROL_WIDGET_ADD_ACTION", "Добавление виджета");
define("CONTROL_WIDGETCLASS_ADD_ACTION", "Добавление виджет-компонента");
define("SECTION_INDEX_DEV_WIDGETS", "Виджеты");
define("CONTROL_WIDGETCLASS_IMPORT", "Импорт виджета");
define("CONTROL_WIDGETCLASS_FILES_PATH", "Файлы виджет-компонента находятся в папке <a href='%s'>%s</a>");

define("WIDGET_TAB_INFO", "Информация");
define("WIDGET_TAB_EDIT", "Редактирование виджет-компонента");
define("WIDGET_TAB_CUSTOM_ACTION", "Шаблоны действий");
define("NETCAT_REMIND_SAVE_TEXT", "Выйти без сохранения?");
define("NETCAT_REMIND_SAVE_SAVE", "Сохранить");
define("SECTION_SECTIONS_INSTRUMENTS_WIDGETS", "Виджеты");

# SECTION TEMPLATE
define("SECTION_CONTROL_TEMPLATE_SHOW", "Макеты дизайна");

# SECTIONS OPTIONS
define("SECTION_SECTIONS_OPTIONS", "Настройки системы");
define("SECTION_SECTIONS_OPTIONS_MODULE_LIST", "Управление модулями");
define("SECTION_SECTIONS_OPTIONS_WYSIWYG", "Настройки WYSIWYG");
define("SECTION_SECTIONS_OPTIONS_SYSTEM", "Системные таблицы");
define("SECTION_SECTIONS_OPTIONS_SECURITY", "Безопасность");

# SECTIONS OPTIONS
define("SECTION_SECTIONS_INSTRUMENTS_SQL", "Командная строка SQL");
define("SECTION_SECTIONS_INSTRUMENTS_TRASH", "Корзина удаленных объектов");
define("SECTION_SECTIONS_INSTRUMENTS_CRON", "Управление задачами");
define("SECTION_SECTIONS_INSTRUMENTS_HTML", "HTML-редактор");
define("SECTION_SECTIONS_INSTRUMENTS_SITEINFO", "SEO-анализ");

# SECTIONS MODDING
define("SECTION_SECTIONS_MODDING_ARHIVES", "Архивы проекта");

# REPORTS
define("SECTION_REPORTS_TOTAL", "Общая статистика проекта");
define("SECTION_REPORTS_SYSMESSAGES", "Системные сообщения");

# SUPPORT

# ABOUT
define("SECTION_ABOUT_TITLE", "О программе");
define("SECTION_ABOUT_HEADER", "О программе");
define("SECTION_ABOUT_BODY", "Система управления сайтами NetCat <font color=%s>%s</font> версия %s. Все права защищены.<br><br>\nВеб-сайт системы NetCat: <a target=_blank href=https://netcat.ru>www.netcat.ru</a><br>\nEmail службы поддержки: <a href=mailto:support@netcat.ru>support@netcat.ru</a>\n<br><br>\nРазработчик: ООО &laquo;НетКэт&raquo;<br>\nEmail: <a href=mailto:info@netcat.ru>info@netcat.ru</a><br>\n+7 (495) 783-6021<br>\n<a target=_blank href=https://netcat.ru>www.netcat.ru</a><br>");
define("SECTION_ABOUT_DEVELOPER", "Разработчик проекта");

// ARRAY-2-FORMS


# INDEX
define("CONTROL_CONTENT_CATALOUGE_SITE", "Сайты");
define("CONTROL_CONTENT_CATALOUGE_ONESITE", "Сайт");
define("CONTROL_CONTENT_CATALOUGE_ADD", "добавление");
define("CONTROL_CONTENT_CATALOUGE_SITEDELCONFIRM", "Подтверждение удаления сайта");
define("CONTROL_CONTENT_CATALOUGE_ADDSECTION", "Добавление раздела");
define("CONTROL_CONTENT_CATALOUGE_ADDSITE", "Добавление сайта");
define("CONTROL_CONTENT_CATALOUGE_SITEOPTIONS", "Настройки сайта");

define("CONTROL_CONTENT_CATALOUGE_ERROR_CASETREE_ONE", "Название сайта не может быть пустым!");
define("CONTROL_CONTENT_CATALOUGE_ERROR_DUPLICATE_DOMAIN", "Сайт с таким доменным именем уже существует в системе. Укажите другое доменное имя.");
define("CONTROL_CONTENT_CATALOUGE_ERROR_CASETREE_THREE", "Доменное имя может содержать только латинские буквы, цифры, подчеркивание, дефис и точку! Цифры должны совмещаться с буквами.");
define("CONTROL_CONTENT_CATALOUGE_ERROR_DOMAIN_NOT_SET", "Доменное имя не указано");
define("CONTROL_CONTENT_CATALOUGE_ERROR_INCORRECT_DOMAIN", "Проверьте домен");
define("CONTROL_CONTENT_CATALOUGE_ERROR_INCORRECT_DOMAIN_FULLTEXT", "Проверьте, правильно ли указан домен. NetCat должен быть установлен в корневую папку этого домена (или синонима)!");

define("CONTROL_CONTENT_CATALOUGE_SUCCESS_ADD", "Сайт успешно добавлен!");
define("CONTROL_CONTENT_CATALOUGE_SUCCESS_EDIT", "Настройки сайта успешно изменены!");
define("CONTROL_CONTENT_CATALOUGE_SUCCESS_DELETE", "Сайт успешно удален!");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MAININFO", "Основная информация");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NAME", "Название");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DOMAIN", "Домен");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_CATALOGUEFORM_LANG", "Язык сайта (ISO 639-1)");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MIRRORS", "Зеркала (по одному на строчке)");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_OFFLINE", "Показывать, когда сайт выключен");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ROBOTS", "Содержимое файла Robots.txt");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ROBOTS_DONT_CHANGE", "Не изменяйте содержимое этого раздела.");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_TEMPLATE", "Макет дизайна");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_TITLEPAGE", "Титульная страница");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_TITLEPAGE_PAGE", "Титульная страница");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NOTFOUND", "Страница не найдена (ошибка 404)");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_NOTFOUND_PAGE", "Страница не найдена");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_PRIORITY", "Приоритет");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ON", "включен");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_HTTPS_ENABLED", "использовать HTTPS");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_LABEL_COLOR", "Цвет метки");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DEFAULT_CLASS", "Компонент по умолчанию");
define("CONTROL_CONTENT_SITE_ADD_EMPTY", "новый пустой сайт");
define("CONTROL_CONTENT_SITE_ADD_WITH_CONTENT", "готовый сайт");
define("CONTROL_CONTENT_SITE_CATEGORY", "Категория");
define("CONTROL_CONTENT_SITE_CATEGORY_ANY", "любая");
define("CONTROL_CONTENT_SITE_ADD_DATA_ERROR", "Не удалось загрузить список доступных готовых сайтов");
define("CONTROL_CONTENT_SITE_ADD_PREVIEW", "демо");
define("CONTROL_CONTENT_SITE_ADD_DOWNLOADING", "Производится скачивание и развёртывание сайта");
define("CONTROL_CONTENT_SITE_ADD_DOWNLOADING_ERROR", "Не удалось загрузить архив с сайтом");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE", "Способ отображения");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE_TRADITIONAL", "Традиционный");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE_SHORTPAGE", "Shortpage");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE_LONGPAGE_VERTICAL", "Longpage");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_DISPLAYTYPE_LONGPAGE_HORIZONTAL", "Longpage горизонтальный");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ACCESS", "Доступ");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_USERS", "пользователи");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_VIEW", "Просмотр");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_COMMENT", "комментирование");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_CHANGE", "Изменение");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SUBSCRIBE", "подписка");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_EXTFIELDS", "Дополнительные поля");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SAVE", "Сохранить");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_WARNING_SITEDELETE_I", "ы");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_WARNING_SITEDELETE_U", "и");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_WARNING_SITEDELETE", "Внимание: сайт%s и все с ним%s связанное будет удалено.");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_CONFIRMDELETE", "Подтвердить удаление");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_SETTINGS", "Настройки мобильности");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SIMPLE", "Обычный сайт");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE", "Мобильный сайт");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_ADAPTIVE", "Адаптивный сайт");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_USE_RESS", "Использовать технологию RESS");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_FOR", "Мобильная версия для сайта");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_FOR_NOTICE", "доступна только для мобильных сайтов");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_REDIRECT", "Использовать принудительную переадресацию");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_NONE", "[нет]");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_DELETE", "удалить");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_CHANGE", "изменить");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_CRITERION", "Определять мобильность по: ");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_USERAGENT", "User-agent");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_SCREEN_RESOLUTION", "Разрешение экрана");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_MOBILE_ALL_CRITERION", "Обе характеристики");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_CREATED", "Дата создания сайта");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_UPDATED", "Дата изменения информации о сайте");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SECTIONSCOUNT", "Количество подразделов");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SITESTATUS", "Статус сайта");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_ON", "включен");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_OFF", "выключен");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_ADD", "добавить");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_USERS", "пользователи");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_READACCESS", "Доступ на чтение");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_ADDACCESS", "Доступ на добавление");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_EDITACCESS", "Доступ на изменение");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SUBSCRIBEACCESS", "Доступ на подписку");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_PUBLISHACCESS", "Публикация объектов");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_VIEW", "Просмотр");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_ADDING", "Добавление");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SEARCHING", "Поиск");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_SUBSCRIBING", "Подписка");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_EDIT", "Редактирование");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_DELETE", "Удалить сайт");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SITE", "Сайт");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SUBSECTIONS", "Подразделы");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_PRIORITY", "Приоритет");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_GOTO", "Перейти");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_DELETE", "Удалить");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_LIST", "список");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_TOOPTIONS", "изменить настройки сайта");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SHOW", "посмотреть сайт");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_EDIT", "изменить информацию");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_NONE", "В проекте нет ни одного сайта");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_ADDSITE", "Добавить сайт");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SAVE", "Сохранить изменения");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_DBERROR", "Ошибка выборки из базы!");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_SECTIONWASCREATED", "Создан раздел %s<br>");

# CONTROL CONTENT SUBDIVISION
define("CONTROL_CONTENT_SUBDIVISION_FAVORITES_TITLE", "Быстрое редактирование");
define("CONTROL_CONTENT_SUBDIVISION_FULL_TITLE", "Карта сайта");

# CONTROL CONTENT SUBDIVISION
define("CONTROL_CONTENT_SUBDIVISION_INDEX_SITES", "Сайты");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_SECTIONS", "Разделы");
define("CONTROL_CONTENT_SUBDIVISION_CLASS", "Инфоблок");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_ADDSECTION", "Добавление раздела");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_OPTIONSECTION", "Настройки раздела");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_DELETECONFIRMATION", "Подтверждение удаления");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_MOVESECTION", "Перенос раздела");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_ERROR_THREE_NAME", "Введите название раздела!");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_ERROR_THREE_KEYWORD", "Данное ключевое слово уже используется. Введите другое ключевое слово.");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_ERROR_THREE_PARENTSUB", "Не выбран родительский раздел!");
define("CONTROL_CONTENT_SUBDIVISION_INDEX_ERROR", "Ошибка добавления раздела");

define("CONTROL_CONTENT_SUBDIVISION_SUCCESS_EDIT", "Настройки раздела сохранены");

define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_CLASSLIST_SECTION", "Список компонентов раздела");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_CLASSLIST_SITE", "Список компонентов на сайте");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ADDCLASS", "Добавление компонента в раздел");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_OPTIONSCLASS", "Настройки компонента раздела");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ADDCLASSSITE", "Добавление компонента на сайт");

define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_NAME", "Название инфоблока не может быть пустым!");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_KEYWORD_INVALID", "Ключевое слово содержит недопустимые символы, либо слишком длинное. Оно может содержать только буквы, цифры и символ подчеркивания, и не может быть длиннее 64 символов.");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_KEYWORD", "Данное ключевое слово уже занято одним из инфоблоков. Введите другое ключевое слово.");

define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_SUCCESS_ADD", "Инфоблок успешно добавлен");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_ADD", "Ошибка добавления инфоблока в раздел");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_SUCCESS_EDIT", "Инфоблок успешно изменен");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_EDIT", "Ошибка редактирования инфоблока");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_ERROR_DELETE", "Ошибка удаления инфоблока");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_LIST_SUCCESS_EDIT", "Список инфоблоков успешно изменен");
define("CONTROL_CONTENT_SUBDIVISION_SUBCLASS_LIST_ERROR_EDIT", "Ошибка редактирования списка инфоблоков");

define("CONTROL_CONTENT_SUBDIVISION_FIRST_SUBCLASS", "В данном разделе нет ни одного инфоблока.<br />Для того, чтобы добавлять информацию в раздел, необходимо добавить в него хотя бы один инфоблок.");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_SECTION", "Раздел");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_SUBSECTIONS", "Подразделы");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_GOTO", "Перейти");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_NOONEFAVORITES", "Нет избранных разделов.");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_TOOPTIONS", "изменить настройки раздела");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_TOOPTIONSSUBCLASS", "изменить настройки компонента в разделе");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_TOVIEW", "посмотреть страницу");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_TOEDIT", "изменить информацию");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_PRIORITY", "Приоритет");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_DELETE", "Удалить");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_NONE", "нет");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LIST", "список");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ADD", "добавить");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_NOSECTIONS", "У данного сайта нет ни одного раздела.");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_NOSUBSECTIONS", "В данном разделе нет подразделов.");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ADDSECTION", "Добавить раздел");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_CONTINUE", "Продолжить");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_SELECT_ROOT_SECTION", "Выберите раздел, в который хотите добавить новый");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_SAVE", "Сохранить изменения");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ADDFAVOTITES", "показывать раздел в &quot;Избранных разделах&quot;");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_USEEDITDESIGNTEMPLATE", "Использовать этот макет при редактировании объектов");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA", "Основная информация");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_NAME", "Название");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_KEYWORD", "Ключевое слово");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_EXTURL", "Внешняя ссылка");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_LANG", "Язык раздела (ISO 639-1)");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DTEMPLATE", "Макет дизайна");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DTEMPLATE_CS", "Настройки макета дизайна");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DTEMPLATE_EDIT", "Редактировать код макета");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DTEMPLATE_N", "Наследовать");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_TURNON", "включен");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_TURNOFF", "выключен");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_A_ADDSUBSECTION", "добавить подраздел");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_A_REMSITE", "удалить сайт");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_MULTI_SUB_CLASS", "Несколько инфоблоков в разделе");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE", "Способ отображения");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE_INHERIT", "Наследовать");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE_TRADITIONAL", "Традиционный");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE_SHORTPAGE", "Shortpage");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE_LONGPAGE_VERTICAL", "Longpage");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_MAINDATA_DISPLAYTYPE_LONGPAGE_HORIZONTAL", "Longpage горизонтальный");

define("CONTROL_TEMPLATE_CUSTOM_SETTINGS_NOT_AVAILABLE", "Данный макет дизайна не имеет дополнительных настроек.");
define("CONTROL_TEMPLATE_CUSTOM_SETTINGS", "Настройки отображения макета дизайна в разделе");
define("CONTROL_TEMPLATE_CUSTOM_SETTINGS_ISNOTSET", "Настройки отображения макета дизайна в разделе отсутствуют");
define("CONTROL_TEMPLATE_CUSTOM_SETTINGS_INHERITED_FROM_SITE", "Значения параметров, которые не заданы в настройках этого раздела,
        будут взяты из <a href='%s' target='_top'>настроек макета сайта</a>.");
define("CONTROL_TEMPLATE_CUSTOM_SETTINGS_INHERITED_FROM_FOLDER", "Значения параметров, которые не заданы в настройках этого раздела,
        будут взяты из <a href='%s' target='_top'>настроек макета раздела «%s»</a>.");

define("CONTROL_CUSTOM_SETTINGS_INHERIT", "использовать значение, заданное в родительском разделе");
define("CONTROL_CUSTOM_SETTINGS_OFF", "нет");
define("CONTROL_CUSTOM_SETTINGS_ON", "да");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_A_EDIT", "изменить информацию");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_A_KILL", "удалить этот раздел");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWMENU_A_VIEW", "посмотреть страницу");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_MSG_OK", "Раздел успешно добавлен.");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_A_ADDCLASSTOSECTION", "Добавить компонент в раздел");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_A_BACKTOSECTIONLIST", "Вернуться к списку разделов");

define("CONTROL_CONTENT_CATALOUGE_FUNCS_ERROR_NOCATALOGUE", "Сайт не существует.");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_ERROR_NOSUBDIVISION", "Раздел не существует.");
define("CONTROL_CONTENT_CATALOUGE_FUNCS_ERROR_NOSUBCLASS", "Компонент в разделе не существует.");

define("CLASSIFICATOR_COMMENTS_DISABLE", "Запретить");
define("CLASSIFICATOR_COMMENTS_ENABLE", "Разрешить");
define("CLASSIFICATOR_COMMENTS_NOREPLIED", "разрешить, если нет ответов");

define("CONTROL_CONTENT_CATALOGUE_FUNCS_COMMENTS", "Комментирование");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_COMMENTS", "Комментирование");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_COMMENTS_ADD", "Добавление комментариев");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_COMMENTS_AUTHOR_EDIT", "Редактирование своих комментариев");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_COMMENTS_AUTHOR_DELETE", "Удаление своих комментариев");

define("CONTROL_CONTENT_CATALOGUE_FUNCS_DEMO_MODE", "Демо-режим");
define("CONTROL_CONTENT_CATALOGUE_FUNCS_DEMO_MODE_CHECKBOX", "Демонстрационный режим работы сайта");

define("CONTROL_CONTENT_SUBCLASS_FUNCS_COMMENTS", "Комментирование");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS", "Доступ");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INHERIT", "Наследовать");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_PUBLISH", "Публикация объектов");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INFO_READ", "Доступ на чтение");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INFO_ADD", "Доступ на добавление");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INFO_EDIT", "Доступ на изменение");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INFO_SUBSCRIBE", "Доступ на подписку");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_INFO_PUBLISH", "Публикация объектов");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_USERS", "пользователи");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_VIEW", "просмотр");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_READ", "Просмотр");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_COMMENT", "комментирование");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_ADD", "добавление");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_WRITE", "Добавление");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_EDIT", "Изменение");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_CHECKED", "Включение");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_DELETE", "Удаление");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_SUBSCRIBE", "подписка");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_ACCESS_ADVANCEDFIELDS", "Дополнительные поля");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_OBJ_HOWSHOW", "Настройки отображения");
define("CONTROL_CONTENT_SUBDIVISION_CUSTOM_SETTINGS_TEMPLATE", "Настройки отображения компонента");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_OBJ_YES", "Да");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_OBJ_NO", "Нет");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_INFO_UPDATED", "Дата изменения информации о разделе");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_INFO_CLASS_COUNT", "Количество компонентов");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_INFO_STATUS", "Статус раздела");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_INFO_SUBSECTIONS_COUNT", "Количество подразделов");


define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_DELETE", "Удалить раздел");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_ROOT", "Корневой раздел");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_DELETE_CONFIRMATION", "Подтвердить удаление");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_WARNING", "Внимание: раздел%s и все с н%s связанное будет удалено.");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_WARNING_ONE_MANY", "ы");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_WARNING_ONE_ONE", "");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_WARNING_TWO_MANY", "ими");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_WARNING_TWO_ONE", "им");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_ERR_NOONESITE", "Указанного сайта не существует.");

define("CONTROL_CONTENT_SUBDIVISION_SYSTEM_FIELDS", "Системные");
define("CONTROL_CONTENT_SUBDIVISION_SYSTEM_FIELDS_NO", "В системной таблице \"Разделы\" нет дополнительных полей");

define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_ALWAYS", "всегда");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_HOURLY", "ежечасно");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_DAILY", "ежедневно");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_WEEKLY", "еженедельно");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_MONTHLY", "ежемесячно");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_YEARLY", "ежегодно");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ_NEVER", "никогда");

define("CONTROL_CONTENT_SUBDIVISION_SEO_META", "Мета-тэги для SEO");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SMO_META", "Мета-тэги для социальных сетей");
define("CONTROL_CONTENT_SUBDIVISION_SEO_INDEXING", "Индексирование");
define("CONTROL_CONTENT_SUBDIVISION_SEO_CURRENT_VALUE", "Текущее значение");
define("CONTROL_CONTENT_SUBDIVISION_SEO_VALUE_NOT_SETTINGS", "Значение %s на странице отличное от того, что Вы вводили. <a target='_blank' href='https://netcat.ru/developers/docs/seo/title-keywords-and-description/'>Подробнее</a>.");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_HEADER", "Заголовок Last-Modified");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_NONE", "Не посылать");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_YESTERDAY", "Предыдущий день");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_HOUR", "Предыдущий час");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_CURRENT", "Текущую дату");
define("CONTROL_CONTENT_SUBDIVISION_SEO_LAST_MODIFIED_ACTUAL", "Актуальную дату");
define("CONTROL_CONTENT_SUBDIVISION_SEO_DISALLOW_INDEXING", "Разрешить индексирование");
define("CONTROL_CONTENT_SUBDIVISION_SEO_DISALLOW_INDEXING_YES", "Да");
define("CONTROL_CONTENT_SUBDIVISION_SEO_DISALLOW_INDEXING_NO", "Нет");
define("CONTROL_CONTENT_SUBDIVISION_SEO_INCLUDE_IN_SITEMAP", "Включить раздел в Sitemap");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_PRIORITY", "Sitemap: приоритет страницы");
define("CONTROL_CONTENT_SUBDIVISION_SEO_SITEMAP_CHANGEFREQ", "Sitemap: частота изменения страницы");

define("CONTROL_CONTENT_SUBDIVISION_FUNCS_LINEADD_DELETE_SUCCESS", "Удаление выполнено успешно.");

define("CONTROL_CONTENT_CLASS", "Компонент");
define("CONTROL_CONTENT_SUBCLASS_CLASSNAME", "Название блока");
define("CONTROL_CONTENT_SUBCLASS_SHOW_ALL", "показать все");
define("CONTROL_CONTENT_SUBCLASS_ONSECTION", "в разделе");
define("CONTROL_CONTENT_SUBCLASS_ONSITE", "на сайте");
define("CONTROL_CONTENT_SUBCLASS_MSG_NONE", "В данном разделе нет инфоблоков.");
define("CONTROL_CONTENT_SUBCLASS_DEFAULTACTION", "Действие по умолчанию");
define("CONTROL_CONTENT_SUBCLASS_CREATIONDATE", "Дата создания компонента %s");
define("CONTROL_CONTENT_SUBCLASS_UPDATEDATE", "Дата изменения информации о компоненте %s");
define("CONTROL_CONTENT_SUBCLASS_TOTALOBJECTS", "Объектов");
define("CONTROL_CONTENT_SUBCLASS_CLASSSTATUS", "Статус компонента");
define("CONTROL_CONTENT_SUBCLASS_CHANGEPREFS", "Изменить настройки компонента %s");
define("CONTROL_CONTENT_SUBCLASS_DELETECLASS", "Удалить компонент %s");
define("CONTROL_CONTENT_SUBCLASS_ISNAKED", "Не использовать макет дизайна");
define("CONTROL_CONTENT_SUBCLASS_SRCMIRROR", "Источник данных");
define("CONTROL_CONTENT_SUBCLASS_SRCMIRROR_NONE", "[нет]");
define("CONTROL_CONTENT_SUBCLASS_SRCMIRROR_EDIT", "изменить");
define("CONTROL_CONTENT_SUBCLASS_SRCMIRROR_DELETE", "удалить");
define("CONTROL_CONTENT_SUBCLASS_TYPE", "Тип инфоблока");
define("CONTROL_CONTENT_SUBCLASS_TYPE_SIMPLE", "обычный");
define("CONTROL_CONTENT_SUBCLASS_TYPE_MIRROR", "зеркальный");
define("CONTROL_CONTENT_SUBCLASS_MIRROR", "Зеркальный инфоблок");
define("CONTROL_CONTENT_SUBCLASS_MULTI_TITLE", "Способ отображения инфоблоков на странице");
define("CONTROL_CONTENT_SUBCLASS_MULTI_ONONEPAGE", "выводить на одной странице");
define("CONTROL_CONTENT_SUBCLASS_MULTI_ONTABS", "выводить во вкладках");
define("CONTROL_CONTENT_SUBCLASS_MULTI_NONE", "выводить только первый инфоблок");
define("CONTROL_CONTENT_SUBCLASS_EDIT_IN_PLACE", "Данные этого инфоблока необходимо редактировать на странице \"<a href='%s'>%s</a>\"");

define("CONTROL_SETTINGSFILE_TITLE_ADD", "Добавление");
define("CONTROL_SETTINGSFILE_TITLE_EDIT", "Редактирование");
define("CONTROL_SETTINGSFILE_BASIC_REGCODE", "Номер лицензии");
define("CONTROL_SETTINGSFILE_BASIC_MAIN", "Основная информация");
define("CONTROL_SETTINGSFILE_BASIC_MAIN_NAME", "Название проекта");

define("CONTROL_SETTINGSFILE_BASIC_EDIT_TEMPLATE", "Макет дизайна, используемый при редактировании объектов");
define("CONTROL_SETTINGSFILE_BASIC_EDIT_TEMPLATE_DEFAULT", "макет редактируемого раздела");

define("CONTROL_SETTINGSFILE_SHOW_EXCURSION", "Показывать экскурсию для текущего пользователя");

define("CONTROL_SETTINGSFILE_BASIC_EMAILS", "Рассылки");
define("CONTROL_SETTINGSFILE_BASIC_EMAILS_FILELD", "Поле (с форматом email) в таблице пользователей");
define("CONTROL_SETTINGSFILE_BASIC_EMAILS_FROMNAME", "Имя отправителя");
define("CONTROL_SETTINGSFILE_BASIC_EMAILS_FROMEMAIL", "Email отправителя");
define("CONTROL_SETTINGSFILE_BASIC_CHANGEDATA", "Изменить настройки системы");


define("CONTROL_SETTINGSFILE_BASIC_USE_SMTP", "Использовать SMTP");
define("CONTROL_SETTINGSFILE_BASIC_USE_SENDMAIL", "Использовать Sendmail");
define("CONTROL_SETTINGSFILE_BASIC_USE_MAIL", "Использовать функцию mail");
define("CONTROL_SETTINGSFILE_BASIC_MAIL_PARAMETERS", "Дополнительные параметры для sendmail (<code>%s</code> для подстановки адреса отправителя)");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_HOST", "SMTP сервер");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_PORT", "Порт");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_AUTH_USE", "Использовать авторизацию");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_USERNAME", "Имя пользователя");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_PASSWORD", "Пароль");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_ENCRYPTION", "Шифрование");
define("CONTROL_SETTINGSFILE_BASIC_SMTP_NOENCRYPTION", "Нет");
define("CONTROL_SETTINGSFILE_BASIC_SENDMAIL_COMMAND", "Sendmail команда (например '/usr/sbin/sendmail -bs')");
define("CONTROL_SETTINGSFILE_BASIC_MAIL_TRANSPORT_HEADER", "Вид транспорта");

define("CONTROL_SETTINGSFILE_AUTOSAVE", "Настройки функции \"Черновик\"");
define("CONTROL_SETTINGSFILE_AUTOSAVE_USE", "Использовать функцию \"Черновик\"");
define("CONTROL_SETTINGSFILE_AUTOSAVE_TYPE_KEYBOARD", "Сохранять по нажатию клавиш");
define("CONTROL_SETTINGSFILE_AUTOSAVE_TYPE_TIMER", "Сохранять периодически");
define("CONTROL_SETTINGSFILE_AUTOSAVE_PERIOD", "Периодичность, сек");
define("CONTROL_SETTINGSFILE_AUTOSAVE_NO_ACTIVE", "Сохранять только при бездействии");

define("CONTROL_SETTINGSFILE_INLINE_IMAGE_CROP", "Настройки редактирования изображений");
define("CONTROL_SETTINGSFILE_INLINE_IMAGE_CROP_USE", "Использовать быстрое редактирование изображений");
define("CONTROL_SETTINGSFILE_INLINE_IMAGE_CROP_DIMENSIONS", "Предустановленные расширения (Ширина x Высота)");

define("CONTROL_SETTINGSFILE_DOCHANGE_ERROR_NAME", "Название проекта не может быть пустым!");

define("NETCAT_AUTH_TYPE_LOGINPASSWORD", "Вход по логину/паролю");
define("NETCAT_AUTH_TYPE_TOKEN", "Вход по e-token");
define("CONTROL_AUTH_HTML_CMS", "Система управления сайтами");
define("CONTROL_AUTH_ON_ONE_SITE", "Авторизовывать на сайте");
define("CONTROL_AUTH_ON_ALL_SITES", "На всех сайтах");
define("CONTROL_AUTH_HTML_LOGIN", "Логин");
define("CONTROL_AUTH_HTML_PASSWORD", "Пароль");
define("CONTROL_AUTH_HTML_PASSWORDCONFIRM", "Пароль еще раз");
define("CONTROL_AUTH_HTML_SAVELOGIN", "Запомнить логин и пароль");
define("CONTROL_AUTH_HTML_LANG", "Язык");
define("CONTROL_AUTH_HTML_AUTH", "Авторизоваться");
define("CONTROL_AUTH_HTML_BACK", "Вернуться");
define("CONTROL_AUTH_FIELDS_NOT_EMPTY", "Поля \"".CONTROL_AUTH_HTML_LOGIN."\" и \"".CONTROL_AUTH_HTML_PASSWORD."\" не могут быть пустыми!");
define("CONTROL_AUTH_LOGIN_NOT_EMPTY", "Поле \"".CONTROL_AUTH_HTML_LOGIN."\" не может быть пустым!");
define("CONTROL_AUTH_LOGIN_OR_PASSWORD_INCORRECT", "Авторизационные данные неверны!");
define("CONTROL_AUTH_PIN_INCORRECT", "Введен неверный PIN код!");
define("CONTROL_AUTH_TOKEN_PLUGIN_DONT_INSTALL", "Плагин не установлен");
define("CONTROL_AUTH_KEYPAIR_INCORRECT", "Ошибка при создании ключевой пары");
define("CONTROL_AUTH_USB_TOKEN_NOT_INSERTED", "USB-токен отсутствует");
define("CONTROL_AUTH_TOKEN_CURRENT_TOKENS", "Текущие привязанные токены пользователя");
define("CONTROL_AUTH_TOKEN_NEW", "Привязать новый токен");
define("CONTROL_AUTH_TOKEN_PLUGIN_ERROR", "В браузере не установлен <a href='http://www.rutoken.ru/hotline/download/' target='_blank'>плагин для работы с токеном</a>");
define("CONTROL_AUTH_TOKEN_MISS", "Токен отсутствует");
define("CONTROL_AUTH_TOKEN_NEW_BUTTON", "Привязать");

define("CONTROL_AUTH_JS_REQUIRED", "Для работы в системе администрирования необходимо включить поддержку javascript");

define("NETCAT_MODULE_AUTH_INSIDE_ADMIN_ACCESS", "доступ в зону администрирования");
define("CONTROL_AUTH_MSG_MUSTAUTH", "Для авторизации необходимо ввести логин и пароль.");


define("CONTROL_FS_NAME_SIMPLE", "Простая");
define("CONTROL_FS_NAME_ORIGINAL", "Стандартная");
define("CONTROL_FS_NAME_PROTECTED", "Защищенная");

define("CONTROL_CLASS_CLASS_TEMPLATE", "Шаблон вывода инфоблока");
define("CONTROL_CLASS_CLASS_TEMPLATE_CHANGE_LATER", "Другие настройки инфоблока вы сможете изменить после добавления раздела.");
define("CONTROL_CLASS_CLASS_TEMPLATE_DEFAULT", "Шаблон по умолчанию");
define("CONTROL_CLASS_CLASS_TEMPLATE_EDIT_MODE", "Шаблон вывода в режиме редактирования");
define("CONTROL_CLASS_CLASS_TEMPLATE_ADMIN_MODE", "Шаблон вывода в режиме администрирования");
define("CONTROL_CLASS_CLASS_TEMPLATE_EDIT_MODE_DONT_USE", "-- не использовать отдельный шаблон --");
define("CONTROL_CLASS_CLASS_TEMPLATE_ADD", "Добавить шаблон");
define("CONTROL_CLASS_CLASS_DONT_USE_TEMPLATE", "-- не использовать шаблон --");
define("CONTROL_CLASS_CLASS_TEMPLATE_ERROR_NAME", "Введите название шаблона компонента");
define("CONTROL_CLASS_CLASS_TEMPLATE_ERROR_NOT_FOUND", "Шаблоны компонента отсутствуют");
define("CONTROL_CLASS_CLASS_TEMPLATE_DELETE_WARNING", "Внимание: вместо шаблонов будет использоваться основной компонент \"%s\".");
define("CONTROL_CLASS_CLASS_TEMPLATE_NOT_FOUND", "Шаблон с идентификатором %s не найден!");
define("CONTROL_CLASS_CLASS_TEMPLATE_ERROR_ADD", "Ошибка добавления шаблона компонента");
define("CONTROL_CLASS_CLASS_TEMPLATE_ERROR_EDIT", "Ошибка редактирования шаблона компонента");
define("CONTROL_CLASS_CLASS_TEMPLATE_SUCCESS_ADD", "Шаблон компонента успешно добавлен");
define("CONTROL_CLASS_CLASS_TEMPLATE_SUCCESS_EDIT", "Шаблон компонента успешно изменен");
define("CONTROL_CLASS_CLASS_TEMPLATE_GROUP", "Шаблоны компонентов");
define("CONTROL_CLASS_CLASS_TEMPLATE_BUTTON_EDIT", "Редактировать");
define("CONTROL_CLASS_CLASS_TEMPLATES", "Шаблоны компонента");
define("CLASS_TEMPLATE_TAB_EDIT", "Редактирование шаблона");
define("CLASS_TEMPLATE_TAB_DELETE", "Удаление шаблона");
define("CLASS_TEMPLATE_TAB_INFO", "Настройки");

define("CONTROL_CLASS", "Компоненты");
define("CONTROL_CLASS_ADD_ACTION", "Добавление компонента");
define("CONTROL_CLASS_DELETECOMMIT", "Подтверждение удаления компонента");
define("CONTROL_CLASS_DOEDIT", "Редактирование компонента");
define("CONTROL_CLASS_CONTINUE", "Продолжить");
define("CONTROL_CLASS_NONE", "Компоненты отсутствуют.");
define("CONTROL_CLASS_ADD", "Добавить компонент");
define("CONTROL_CLASS_ADD_FS", "Добавить компонент 5.0");
define("CONTROL_CLASS_CLASS", "Компонент");
define("CONTROL_CLASS_SYSTEM_TABLE", "Системная таблица");
define("CONTROL_CLASS_ACTIONS", "Шаблоны действий");
define("CONTROL_CLASS_FIELD", "Поле");
define("CONTROL_CLASS_FIELDS", "Поля");
define("CONTROL_CLASS_FIELDS_COUNT", "Полей");
define("CONTROL_CLASS_CUSTOM", "Пользовательские настройки");
define("CONTROL_CLASS_DELETE", "Удалить");
define("CONTROL_CLASS_NEWCLASS", "Новый компонент");
define("CONTROL_CLASS_TO_FS", "Класс в файловую систему");

define("CONTROL_CLASS_FUNCS_SHOWCLASSLIST_ADDCLASS", "Добавить компонент");
define("CONTROL_CLASS_FUNCS_SHOWCLASSLIST_IMPORTCLASS", "Импортировать компонент");

define("CONTROL_CLASS_ACTIONS_VIEW", "просмотр");
define("CONTROL_CLASS_ACTIONS_ADD", "добавление");
define("CONTROL_CLASS_ACTIONS_EDIT", "изменение");
define("CONTROL_CLASS_ACTIONS_CHECKED", "включение");
define("CONTROL_CLASS_ACTIONS_SEARCH", "поиск");
define("CONTROL_CLASS_ACTIONS_MAIL", "подписка");
define("CONTROL_CLASS_ACTIONS_DELETE", "удаление");
define("CONTROL_CLASS_ACTIONS_MODERATE", "модерирование");
define("CONTROL_CLASS_ACTIONS_ADMIN", "администрирование");

define("CONTROL_CLASS_INFO_ADDSLASHES", "Настраивая компонент, не забудьте <a href='#' onclick=\"window.open('".$ADMIN_PATH."template/converter.php', 'converter','width=600,height=410,status=no,resizable=yes'); return false;\">экранировать спецсимволы</a>.");
define("CONTROL_CLASS_ERRORS_DB", "Ошибка выборки из базы!");
define("CONTROL_CLASS_CLASS_NAME", "Название");
define("CONTROL_CLASS_CLASS_KEYWORD", "Ключевое слово");
define("CONTROL_CLASS_CLASS_OBJECT_NAME_SINGULAR", "Название объекта в единственном числе винительного падежа («добавить <em>что</em>?»)");
define("CONTROL_CLASS_CLASS_OBJECT_NAME_PLURAL", "Название объекта во множественном числе винительного падежа («удалить все <em>что</em>?»)");
define("CONTROL_CLASS_CLASS_GROUPS", "Группы компонентов");
define("CONTROL_CLASS_CLASS_NO_GROUP", "Без группы");
define("CONTROL_CLASS_CLASS_OBJECTSLIST", "Шаблон отображения списка объектов");
define("CONTROL_CLASS_CLASS_DESCRIPTION", "Описание инфоблока");
define("CONTROL_CLASS_CLASS_SETTINGS", "Настройки инфоблока");
define("CONTROL_SCLASS_ACTION", "Шаблоны действий");
define("CONTROL_SCLASS_TABLE", "Таблица");
define("CONTROL_SCLASS_TABLE_NAME", "Название таблицы");
define("CONTROL_SCLASS_LISTING_NAME", "Название списка");
define("CONTROL_CLASS_CLASSFORM_INFO_FOR_NEWCLASS", "Информация о компоненте");
define("CONTROL_CLASS_CLASSFORM_MAININFO", "Основная информация");
define("CONTROL_CLASS_CLASSFORM_TEMPLATE_PATH", "Файлы компонента находятся в папке <a href='%s'>%s</a>");
define("CONTROL_CLASS_SITE_STYLES", "Стили для сайта");
define("CONTROL_CLASS_SITE_STYLES_DISABLED_WARNING", "Данный компонент работает в режиме совместимости с NetCat 5.6, добавление CSS-стилей в котором невозможно.");
define("CONTROL_CLASS_SITE_STYLES_ENABLE_BUTTON", "Включить стили шаблона компонента");
define("CONTROL_CLASS_SITE_STYLES_ENABLE_WARNING",
    "После отключения режима совместимости с NetCat 5.6 будет добавляться дополнительная разметка
    (блок-обёртка <code>&lt;div&gt;</code>) при выводе блоков с использованием данного шаблона:
    <ul><li>списков объектов из инфоблоков, 
    <li>основной части страницы полного вывода объекта, 
    <li>форм добавления, изменения и поиска.</ul>
    В зависимости от особенностей используемых на существующих сайтах CSS-стилей может 
    понадобиться соответствующее изменение CSS-правил.");
define("CONTROL_CLASS_SITE_STYLES_DOCS_LINK", "Подробнее о стилях компонентов см. <a href='%s' target='_blank'>в документации</a>.");
define("CONTROL_CLASS_MULTIPLE_MODE_SWITCH", "Оптимизирован для использования в режиме отображения нескольких блоков на странице");
define("CONTROL_CLASS_TEMPLATE_MULTIPLE_MODE_SWITCH", "Шаблон оптимизирован для использования в режиме отображения нескольких блоков на странице");
define("CONTROL_CLASS_LIST_PREVIEW", "Эскиз отображения списка объектов (.png)");
define("CONTROL_CLASS_LIST_PREVIEW_NONE", "Эскиз отсутствует");

define("CONTROL_CLASS_KEYWORD_ONLY_DIGITS", "Ключевое слово не может состоять только из цифр");
define("CONTROL_CLASS_KEYWORD_TOO_LONG", "Длина ключевого слова не может быть более %d символов");
define("CONTROL_CLASS_KEYWORD_INVALID_CHARACTERS", "Ключевое слово может содержать только буквы латинского алфавита, цифры и символы подчёркивания");
define("CONTROL_CLASS_KEYWORD_NON_UNIQUE", "Ключевое слово «%s» уже присвоено компоненту «%s»");
define("CONTROL_CLASS_KEYWORD_TEMPLATE_NON_UNIQUE", "Ключевое слово «%s» уже присвоено шаблону «%s»");
define("CONTROL_CLASS_KEYWORD_RESERVED", "Невозможно использовать «%s» в качестве ключевого слова, так как оно является зарезервированным");

define("CONTROL_CLASS_CLASSFORM_CHECK_ERROR", "<div style='color: red;'>Ошибка кода в поле &laquo;<i>%s</i>&raquo; компонента.</div>");

define("CONTROL_CLASS_CLASS_OBJECTSLIST_PREFIX", "Префикс списка объектов");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_BODY", "Объект в списке");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SUFFIX", "Суффикс списка объектов");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOW", "Показывать по");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ", "объектов на странице");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOW_NUM", "Количество объектов на странице");
define("CONTROL_CLASS_CLASS_MIN_RECORDS", "Минимальное количество объектов в инфоблоке");
define("CONTROL_CLASS_CLASS_MAX_RECORDS", "Максимальное количество объектов в инфоблоке");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SORT", "Сортировать объекты по полю (полям)");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_TITLE", "Заголовок страницы");

define("CONTROL_CLASS_CLASS_OBJECTSLIST_WRONG_NC_CTPL", "В nc_object_list(%s, %s) передан ошибочный nc_ctpl - %s. ");
define("CONTROL_CLASS_CLASS_OBJECTFULL_WRONG_NC_CTPL", "Передан ошибочный nc_ctpl - %s. ");

define("CONTROL_CLASS_CLASS_OBJECTVIEW", "Шаблон отображения одного объекта на отдельной странице");

define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_DOPL", "Дополнительно");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_CACHE", "Кэширование");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_SYSTEM", "Системные настройки");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_BR", "перенос строки - &lt;BR&gt;");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_HTML", "разрешать HTML-теги");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_PAGETITLE", "Заголовок страницы");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_USEASALT", "Использовать как полностью альтернативный заголовок");
define("CONTROL_CLASS_CLASS_OBJECTSLIST_SHOWOBJ_PAGEBODY", "Отображение объекта");
define("CONTROL_CLASS_CLASS_CREATENEW_BASICOLD", "Создать новый компонент на основе существующего");
define("CONTROL_CLASS_CLASS_CREATENEW_CLEARNEW", "Создать новый компонент &quot;с нуля&quot;");
define("CONTROL_CLASS_CLASS_DELETE_WARNING", "Внимание: компонент%s и все с ним%s связанное будет удалено.");
define("CONTROL_CLASS_CLASS_NOT_FOUND", "Компонент с идентификатором %s не найден!");

define("CONTROL_CLASS_CUSTOM_SETTINGS_TEMPLATE", "Настройки отображения компонента раздела");
define("CONTROL_CLASS_CUSTOM_SETTINGS_PARAMETER", "Параметр");
define("CONTROL_CLASS_CUSTOM_SETTINGS_DEFAULT", "По умолчанию");
define("CONTROL_CLASS_CUSTOM_SETTINGS_VALUE", "Значение");
define("CONTROL_CLASS_CUSTOM_SETTINGS_HAS_ERROR", "Одно или несколько значений указаны некорректно. Пожалуйста, исправьте ошибку.");

define("CONTROL_CLASS_IMPORT", "Импорт компонента");
define("CONTROL_CLASS_IMPORTS", "Импорт компонентов");
define("CONTROL_CLASS_IMPORT_UPLOAD", "Закачать");
define("CONTROL_CLASS_IMPORT_ERROR_NOTUPLOADED", "Файл не закачан.");
define("CONTROL_CLASS_IMPORT_ERROR_CANNOTBEINSTALLED", "Компонент не может быть установлен.");
define("CONTROL_CLASS_IMPORT_ERROR_VERSION_ID", "Компонент для версии %s, текущая версия %s.");
define("CONTROL_CLASS_IMPORT_ERROR_NO_VERSION_ID", "Версия системы не указана или неверный формат файла.");
define("CONTROL_CLASS_IMPORT_ERROR_NO_FILES", "Отсутствуют данные для создания файлов шаблонов компонента.");
define("CONTROL_CLASS_IMPORT_ERROR_CLASS_IMPORT", "Ошибка создания компонента, данные компонента не добавлены.");
define("CONTROL_CLASS_IMPORT_ERROR_CLASS_TEMPLATE_IMPORT", "Ошибка создания шаблонов компонента, данные шаблонов не добавлены.");
define("CONTROL_CLASS_IMPORT_ERROR_MESSAGE_TABLE", "Ошибка создания таблицы данных компонента.");
define("CONTROL_CLASS_IMPORT_ERROR_FIELD", "Ошибка создания полей компонента.");

define("CONTROL_CLASS_CONVERT", "Конвертирование компонента");
define("CONTROL_CLASS_CONVERT_BUTTON", "Конвертировать в 5.0");
define("CONTROL_CLASS_CONVERT_BUTTON_UNDO", "Отменить конвертирование");
define("CONTROL_CLASS_CONVERT_DB_ERROR", "Ошибка изменения компонентов в базе");
define("CONTROL_CLASS_CONVERT_OK", "Конвертация успешна");
define("CONTROL_CLASS_CONVERT_OK_GOEDIT", "Перейти к редактированию компонента");
define("CONTROL_CLASS_CONVERT_CLASSLIST_TITLE", "Будут сконвертированы следующие компоненты и их шаблоны");
define("CONTROL_CLASS_CONVERT_CLASSLIST_TITLE_UNDO", "Будет отменена конвертация следующих компонентов и их шаблонов");
define("CONTROL_CLASS_CONVERT_CLASSFOLDERS_TITLE", "Будут созданы папки с файлами шаблонов v5, включая дампы шаблона v4 в файлах class_40_backup.html");
define("CONTROL_CLASS_CONVERT_CLASSFOLDERS_TITLE_UNDO", "Необходимо будет удалить папки с файлами шаблонов 5.0(необязательно)");
define("CONTROL_CLASS_CONVERT_NOTICE", "После конвертации компонента могут возникнуть ошибки синтаксиса в его шаблонах!
                    Рекомендуем закрыть сайт на время изменений.");
define("CONTROL_CLASS_CONVERT_NOTICE_UNDO", "После отмены конвертации компонент вернется к состоянию до конвертации, все изменения в режиме 5.0 потеряются!");
define("CONTROL_CLASS_CONVERT_UNDO_FILE_ERROR","Нет данных для восстановления");

define("CONTROL_CLASS_NEWGROUP", "Новая группа");
define("CONTROL_CLASS_EXPORT", "Экспортировать компонент в файл");
define("CONTROL_CLASS_AUXILIARY_SWITCH", "Служебный компонент");
define("CONTROL_CLASS_AUXILIARY", "(служебный)");

define("CONTROL_CLASS_COMPONENT_TEMPLATE_FOR_RSS_DOESNT_EXIST", "Rss-лента %sне доступна, поскольку отсутствует шаблон компонента для rss");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_FOR_XML_DOESNT_EXIST", "Xml-выгрузка %sне доступна, поскольку отсутствует шаблон компонента для xml");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_FOR_TRASH_DOESNT_EXIST", "Вывод корзины не доступен, поскольку отсутствует шаблон компонента для корзины");

define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE", "Тип");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_CLASSTEMPLATE", "Тип шаблона компонента");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_MULTI_EDIT", "Множественное редактирование");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_RSS", "RSS");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_XML", "XML-выгрузка");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_TRASH", "Для корзины удаленных объектов");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_USEFUL", "Обычный");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_INSIDE_ADMIN", "Режим администрирования");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_ADMIN_MODE", "Режим редактирования");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_TITLE", "Для титульной страницы");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_MOBILE", "Мобильный");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TYPE_RESPONSIVE", "Адаптивный");

define("CONTROL_CLASS_COMPONENT_TEMPLATE_BASE_AUTO", "Автоматически");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_BASE_EMPTY", "Пустой");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_ADD_PARAMETRS", "Параметры добавления шаблона компонента");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATE_BASE", "Создать шаблон компонента на основе");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATE_FOR_TRASH", "Создать шаблон для корзины");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATE_FOR_RSS", "Создать шаблон для rss");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATE_FOR_XML", "Создать шаблон для xml");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TURN_ON_RSS", "Включить rss-ленту");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_TURN_ON_XML", "Включить xml-выгрузку");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_VIEW", "посмотреть");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_EDIT", "настроить");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_ERROR", "Ошибка создания шаблона компонента");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_USEFUL", "Шаблон компонента успешно создан");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_RSS", "Шаблон компонента для RSS успешно создан");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_XML", "Шаблон компонента успешно для XML создан");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_TRASH", "Шаблон компонента для корзины удаленных объектов успешно создан");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_INSIDE_ADMIN", "Шаблон компонента для режима редактирования успешно создан");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_ADMIN_MODE", "Шаблон компонента для режима администрирования успешно создан");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_TITLE", "Шаблон компонента для титульной страницы успешно создан");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_MOBILE", "Шаблон компонента для мобильного сайта успешно создан");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_MULTI_EDIT", "Шаблон компонента для множественного редактирования успешно создан");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_CREATED_FOR_RESPONSIVE", "Шаблон компонента для адаптивного сайта успешно создан");

define("CONTROL_CLASS_COMPONENT_TEMPLATE_RETURN_TO_SUB", "Вернуться</a> к настройке раздела");
define("CONTROL_CLASS_COMPONENT_TEMPLATE_RETURN_TO_TRASH", "Вернуться</a> к корзине");
define("CONTROL_CLASS_SHOW_AUX", "Показать системные компоненты");
define("CONTROL_CLASS_DEFAULT_CHANGE", "Компонент по умолчанию можно изменить в настройках сайта.");

define("CONTROL_CONTENT_CLASS_SUCCESS_ADD", "Компонент успешно добавлен");
define("CONTROL_CONTENT_CLASS_ERROR_ADD", "Ошибка добавления компонента");
define("CONTROL_CONTENT_CLASS_ERROR_NAME", "Введите название компонента");
define("CONTROL_CONTENT_CLASS_GROUP_ERROR_NAME", "Название группы не должно начинаться с цифры");
define("CONTROL_CONTENT_CLASS_SUCCESS_EDIT", "Компонент успешно изменен");
define("CONTROL_CONTENT_CLASS_ERROR_EDIT", "Ошибка редактирования компонента");

#TYPE OF DATA
define("CLASSIFICATOR_TYPEOFDATA_STRING", "Строка");
define("CLASSIFICATOR_TYPEOFDATA_INTEGER", "Целое число");
define("CLASSIFICATOR_TYPEOFDATA_TEXTBOX", "Текстовый блок");
define("CLASSIFICATOR_TYPEOFDATA_LIST", "Список");
define("CLASSIFICATOR_TYPEOFDATA_BOOLEAN", "Логическая переменная (истина или ложь)");
define("CLASSIFICATOR_TYPEOFDATA_FILE", "Файл");
define("CLASSIFICATOR_TYPEOFDATA_FLOAT", "Число с плавающей запятой");
define("CLASSIFICATOR_TYPEOFDATA_DATETIME", "Дата и время");
define("CLASSIFICATOR_TYPEOFDATA_RELATION", "Связь с другим объектом");
define("CLASSIFICATOR_TYPEOFDATA_MULTILIST", "Множественный выбор");
define("CLASSIFICATOR_TYPEOFDATA_OPENID", "OpenID");

define("CLASSIFICATOR_TYPEOFFILESYSTEM", "Тип файловой системы");

define("CLASSIFICATOR_TYPEOFEDIT_ALL", "Доступно всем");
define("CLASSIFICATOR_TYPEOFDATA_ADMINS", "Доступно только администраторам");

define("CLASSIFICATOR_TYPEOFMODERATION_RIGHTAWAY", "После добавления");
define("CLASSIFICATOR_TYPEOFMODERATION_MODERATION", "После проверки администратором");

define("CLASSIFICATOR_USERGROUP_ALL", "Все");
define("CLASSIFICATOR_USERGROUP_REGISTERED", "Зарегистрированные");
define("CLASSIFICATOR_USERGROUP_AUTHORIZED", "Уполномоченные");

define("CONTROL_TEMPLATE_CLASSIFICATOR", "Экранирование спецсимволов");
define("CONTROL_TEMPLATE_CLASSIFICATOR_EKRAN", "Экранировать");
define("CONTROL_TEMPLATE_CLASSIFICATOR_RES", "Результат");

define("CONTROL_FIELD_LIST_NAME", "Название поля");
define("CONTROL_FIELD_LIST_NAMELAT", "Название поля (латинскими буквами)");
define("CONTROL_FIELD_LIST_DESCRIPTION", "Описание");
define("CONTROL_FIELD_LIST_ADD", "Добавить поле");
define("CONTROL_FIELD_LIST_CHANGE", "Сохранить изменения");
define("CONTROL_FIELD_LIST_DELETE", "Удалить поле");
define("CONTROL_FIELD_ADDING", "Добавление поля");
define("CONTROL_FIELD_EDITING", "Редактирование поля");
define("CONTROL_FIELD_DELETING", "Удаление поля");
define("CONTROL_FIELD_FIELDS", "Поля");
define("CONTROL_FIELD_LIST_NONE", "В данном компоненте нет ни одного поля.");
define("CONTROL_FIELD_ONE_FORMAT", "Формат");
define("CONTROL_FIELD_ONE_FORMAT_NONE", "нет");
define("CONTROL_FIELD_ONE_FORMAT_EMAIL", "Email");
define("CONTROL_FIELD_ONE_FORMAT_URL", "URL");
define("CONTROL_FIELD_ONE_FORMAT_PASSWORD", "Пароль");
define("CONTROL_FIELD_ONE_FORMAT_PHONE", "Телефон");
define("CONTROL_FIELD_ONE_FORMAT_TAGS", "Тэги");
define("CONTROL_FIELD_ONE_PROTECT_EMAIL", "защищать при выводе");
define("CONTROL_FIELD_ONE_EXTENSION", "Связанное поле");
define("CONTROL_FIELD_ONE_MUSTBE", "обязательно для заполнения");
define("CONTROL_FIELD_ONE_INDEX", "возможен поиск по данному полю");
define("CONTROL_FIELD_ONE_IN_TABLE_VIEW", "использовать в табличном списке объектов");
define("CONTROL_FIELD_ONE_INHERITANCE", "наследовать значение поля");
define("CONTROL_FIELD_ONE_DEFAULT", "Значение по умолчанию (устанавливается при записи, если поле не было заполнено)");
define("CONTROL_FIELD_ONE_DEFAULT_NOTE", "для всех типов полей кроме &quot;".CLASSIFICATOR_TYPEOFDATA_TEXTBOX."&quot;, &quot;".CLASSIFICATOR_TYPEOFDATA_FILE."&quot;, &quot;".CLASSIFICATOR_TYPEOFDATA_DATETIME."&quot;, &quot;".CLASSIFICATOR_TYPEOFDATA_MULTILIST."&quot;");
define("CONTROL_FIELD_ONE_FTYPE", "Тип поля");
define("CONTROL_FIELD_ONE_ACCESS", "Тип доступа к полю");
define("CONTROL_FIELD_ONE_RESERVED", "Данное название поля зарезервировано!");
define('CONTROL_FIELD_NAME_ERROR', 'Название поля должно содержать только латинские буквы и цифры!');
define('CONTROL_FIELD_DIGIT_ERROR', 'Название поля не может начинаться с цифры.');
define('CONTROL_FIELD_DB_ERROR', 'Ошибка записи в БД.');
define('CONTROL_FIELD_EXITS_ERROR', 'Такое поле уже существует.');
define('CONTROL_FIELD_FORMAT_ERROR', 'Такой формат поля не допустим.');
define("CONTROL_FIELD_MSG_ADDED", "Поле добавлено успешно.");
define("CONTROL_FIELD_MSG_EDITED", "Поле успешно изменено.");
define("CONTROL_FIELD_MSG_DELETED_ONE", "Поле успешно удалено.");
define("CONTROL_FIELD_MSG_DELETED_MANY", "Поле успешно удалено.");
define("CONTROL_FIELD_MSG_CONFIRM_REMOVAL_ONE", "Внимание: поле будет удалено.");
define("CONTROL_FIELD_MSG_CONFIRM_REMOVAL_MANY", "Внимание: поля будут удалены.");
define("CONTROL_FIELD_MSG_FIELDS_CHANGED", "Приоритеты полей изменены.");
define("CONTROL_FIELD_CONFIRM_REMOVAL", "Подтвердить удаление");
define('CONTROL_FIELD__EDITOR_EMBED_TO_FIELD', 'встроить редактор в поле для редактирования');
define('CONTROL_FIELD__TEXTAREA_SIZE', 'Размер текстового блока');
define('CONTROL_FIELD_HEIGHT', 'высота');
define('CONTROL_FIELD_WIDTH', 'ширина');
define('CONTROL_FIELD_ATTACHMENT', 'закачиваемый');
define('CONTROL_FIELD_DOWNLOAD_COUNT', 'считать количество скачиваний');
define('CONTROL_FIELD_PANELS', 'Использовать набор панелей CKEditor');
define('CONTROL_FIELD_PANELS_DEFAULT', 'По умолчанию');
define('CONTROL_FIELD_TYPO', 'типографировать');
define('CONTROL_FIELD_TYPO_BUTTON', 'Типографировать текст');
define('CONTROL_FIELD_BBCODE_ENABLED', 'разрешить bb-коды');
define('CONTROL_FIELD_USE_CALENDAR', 'использовать календарь для выбора даты');
define('CONTROL_FIELD_FILE_UPLOADS_LIMITS', 'Ваша конфигурация PHP имеет следующие ограничения на загрузку файлов:');
define('CONTROL_FIELD_FILE_POSTMAXSIZE', 'максимально допустимый размер данных, отправляемых методом POST');
define('CONTROL_FIELD_FILE_UPLOADMAXFILESIZE', 'максимальный размер закачиваемого файла');
define('CONTROL_FIELD_FILE_MAXFILEUPLOADS', 'разрешенное количество одновременно закачиваемых файлов');
define('CONTROL_FIELD_MULTIFIELD_USE_IMAGE_RESIZE', 'Использовать уменьшение изображений');
define('CONTROL_FIELD_MULTIFIELD_USE_IMAGE_CROP', 'Использовать обрезку изображений');
define('CONTROL_FIELD_MULTIFIELD_CROP_IGNORE', 'Не обрезать, если изображение меньше указанного размера');
define('CONTROL_FIELD_MULTIFIELD_USE_IMAGE_PREVIEW', 'Создавать картинку-предпросмотр(превью)');
define('CONTROL_FIELD_MULTIFIELD_USE_PREVIEW_RESIZE', 'Использовать уменьшение превью');
define('CONTROL_FIELD_MULTIFIELD_PREVIEW_USE_IMAGE_CROP', 'Использовать обрезку превью');
define('CONTROL_FIELD_MULTIFIELD_PREVIEW_CROP_IGNORE', 'Не обрезать, если превью меньше указанного размера');
define('CONTROL_FIELD_MULTIFIELD_IMAGE_WIDTH', 'Ширина');
define('CONTROL_FIELD_MULTIFIELD_IMAGE_HEIGHT', 'Высота');
define('CONTROL_FIELD_MULTIFIELD_CROP_CENTER', 'По центру');
define('CONTROL_FIELD_MULTIFIELD_CROP_COORD', 'По координатам');
define('CONTROL_FIELD_MULTIFIELD_MIN', 'Минимум');
define('CONTROL_FIELD_MULTIFIELD_MAX', 'Максимум');
define('CONTROL_FIELD_MULTIFIELD_MINMAX', 'Ограничить количество файлов доступное для загрузки');
define('CONTROL_FIELD_USE_TRANSLITERATION', 'Транслитерация');
define('CONTROL_FIELD_TRANSLITERATION_FIELD', 'Поле для записи результата транслитерации');
define('CONTROL_FIELD_USE_URL_RULES', 'Использовать правила для URL');
define('CONTROL_FIELD_FILE_WRONG_GD', 'На сервере не включено расширение GD2, уменьшение и обрезка изображений работать не будет');

# SYS
define("TOOLS_SYSTABLE_SITES", "Сайты");
define("TOOLS_SYSTABLE_SECTIONS", "Разделы");
define("TOOLS_SYSTABLE_USERS", "Пользователи");
define("TOOLS_SYSTABLE_TEMPLATE", "Макеты дизайна");


#DATABACKUP
define("TOOLS_DATA_BACKUP",                            "Экспорт/импорт данных");
define("TOOLS_DATA_BACKUP_IMPORT_FILE",                "Файл импорта (*.tgz)");
define("TOOLS_DATA_BACKUP_IMPORT_COMPLETE",            "Импорт данных завершен!");
define("TOOLS_DATA_BACKUP_IMPORT_ERROR",               "Во время импорта данных произошла ошибка!");
define("TOOLS_DATA_BACKUP_IMPORT_DUPLICATE_KEY_ERROR", "Объекты с такими идентификаторами уже существуют.");
define("TOOLS_DATA_BACKUP_EXPORT_COMPLETE",            "Экспорт данных завершен!");
define("TOOLS_DATA_BACKUP_INCOMPATIBLE_VERSION",       "Файл импорта имеет формат, который не поддерживается в текущей версии NetCat. Пожалуйста, обновите вашу копию системы.");
define("TOOLS_DATA_SAVE_IDS",                          "Сохранять идентификаторы импортируемых объектов");
define("TOOLS_DATA_BACKUP_SYSTEM",                     "Системные");
define("TOOLS_DATA_BACKUP_DATATYPE",                   "Тип данных");
define("TOOLS_DATA_BACKUP_INSERT_OBJECTS",             "Добавлено записей в БД");
define("TOOLS_DATA_BACKUP_CREATE_TABLES",              "Создано таблиц в БД");
define("TOOLS_DATA_BACKUP_COPIED_FILES",               "Добавлено файлов/папок");
define("TOOLS_DATA_BACKUP_SKIPPED_FILES",              "Пропущено файлов/папок");
define("TOOLS_DATA_BACKUP_REPLACED_FILES",             "Заменено файлов/папок");
define("TOOLS_DATA_BACKUP_EXPORT_DATE",                "Дата экспорта");
define("TOOLS_DATA_BACKUP_USED_SPACE",                 "использовано");
define("TOOLS_DATA_BACKUP_SPACE_FROM",                 "из");

define("TOOLS_DATA_BACKUP_DELETE_ALL_CONFIRMATION", "Удалить все файлы?");

define("TOOLS_EXPORT",                  "Экспорт");
define("TOOLS_IMPORT",                  "Импорт");
define("TOOLS_DOWNLOAD",                "Загрузить");
define("TOOLS_DATA_BACKUP_GOTO_OBJECT", "Перейти к импортированному объекту");


define("TOOLS_MODULES", "Модули");
define("TOOLS_MODULES_LIST", "Список модулей");
define("TOOLS_MODULES_INSTALLEDMODULE", "Установлен модуль");
define("TOOLS_MODULES_ERR_INSTALL", "Установка модуля невозможна");
define("TOOLS_MODULES_ERR_UNINSTALL", "Удаление модуля невозможно");
define("TOOLS_MODULES_ERR_CANTOPEN", "Невозможно открыть файл");
define("TOOLS_MODULES_ERR_PATCH", "Не установлен необходимый патч с номером");
define("TOOLS_MODULES_ERR_VERSION", "Модуль не для существующей версии");
define("TOOLS_MODULES_ERR_INSTALLED", "Модуль уже установлен");
define("TOOLS_MODULES_ERR_ITEMS", "Ошибка: выполнены не все необходимые условия");
define("TOOLS_MODULES_ERR_DURINGINSTALL", "Ошибка при инсталляции");
define("TOOLS_MODULES_ERR_NOTUPLOADED", "Файл не закачан");
define("TOOLS_MODULES_ERR_EXTRACT", "Ошибка при распаковке архива c модулем.<br />Попробуйте распаковать содержимое архива с модулем в папку $TMP_FOLDER на Вашем сервере и снова запустить процедуру установки модуля.");

define("TOOLS_MODULES_MOD_NAME", "Название модуля");
define("TOOLS_MODULES_MOD_PREFS", "Настройки");
define("TOOLS_MODULES_MOD_GOINSTALL", "Завершить установку");
define("TOOLS_MODULES_MOD_EDIT", "изменить параметры модуля");
define("TOOLS_MODULES_MOD_LOCAL", "Установка модуля с локального диска");
define("TOOLS_MODULES_MOD_INSTALL", "Установка модуля");
define("TOOLS_MODULES_MSG_CHOISESECTION", "Для завершения установки модуля необходимо создать дополнительные разделы. Вам необходимо выбрать родительский раздел, где будут созданы необходимые подразделы.");
define("TOOLS_MODULES_PREFS_SAVED", "Настройки модулей сохранены");
define("TOOLS_MODULES_PREFS_ERROR", "Ошибка во время сохранения настроек модуля");

# PATCH
define("TOOLS_PATCH", "Обновление системы");
define("TOOLS_PATCH_INSTRUCTION_TAB", "Инструкция");
define("TOOLS_PATCH_INSTRUCTION", "Инструкция по установке обновления");
define("TOOLS_PATCH_CHEKING", "Проверка наличия новых обновлений");
define("TOOLS_PATCH_MSG_OK", "Все необходимые обновления установлены.");
define("TOOLS_PATCH_MSG_NOCONNECTION", "Не удалось соединиться с сервером обновлений. О наличии новых обновлений вы можете узнать на <a href='https://partners.netcat.ru/forclients/update/' target='_blank'>нашем сайте</a>.");
define("TOOLS_PATCH_ERR_CANTINSTALL", "Инсталляция патча невозможна.");
define("TOOLS_PATCH_INSTALL_LOCAL", "Установка обновления с локального диска");
define("TOOLS_PATCH_INSTALL_ONLINE", "Установка обновления с официального сайта");
define("TOOLS_PATCH_INFO_NOTINSTALLED", "Не установлено обновление");
define("TOOLS_PATCH_INFO_LASTCHECK", "Последняя проверка была осуществлена");
define("TOOLS_PATCH_INFO_REFRESH", "обновить сведения");
define("TOOLS_PATCH_INFO_DOWNLOAD", "скачать");
define("TOOLS_PATCH_ERR_EXTRACT", "Ошибка при распаковке архива c обновлением.<br />Попробуйте распаковать содержимое архива с обновлением в папку $TMP_FOLDER на Вашем сервере и снова запустить процедуру обновления.");
define("TOOLS_PATCH_ERROR_TMP_FOLDER_NOT_WRITABLE", "Установите права на запись для папки $TMP_FOLDER.<br />(%DIR недоступна для записи)");
define("TOOLS_PATCH_ERROR_FILELIST_NOT_WRITABLE", "Некоторые файлы, требующие обновления, нельзя будет автоматически изменить.");
define("TOOLS_PATCH_ERROR_AUTOINSTALL", "Автоматическая установка обновления невозможна, установите обновление вручную, согласно прилагающейся документации или документации на сайте.");
define("TOOLS_PATCH_ERROR_UPDATE_SERVER_NOT_AVAILABLE", "Не удалось соединиться с сервером обновлений, повторите попытку позже.<br />" .
    "Если доступ в глобальную сеть должен осуществляться через прокси-сервер, " .
    "<a href='{$nc_core->ADMIN_PATH}#system.edit' target='_top'>проверьте его настройки</a>.");
define("TOOLS_PATCH_ERROR_UPDATE_FILE_NOT_AVAILABLE", "Файл обновления не может быть получен, повторите попытку позже. Если ошибка повторится, обратитесь в службу поддержки.");
define("TOOLS_PATCH_DOWNLOAD_LINK_DESCRIPTION", "Ссылка на файл обновления");
define("TOOLS_PATCH_IS_WRITABLE", "Доступ на запись");

# patch after install information
define("TOOLS_PATCH_INFO_FILES_COPIED", "[%COUNT] файлов скопировано.");
define("TOOLS_PATCH_INFO_QUERIES_EXEC", "[%COUNT] MySQL запросов выполненно.");
define("TOOLS_PATCH_INFO_SYMLINKS_EXEC", "[%COUNT] символических ссылок создано.");

define("TOOLS_PATCH_LIST_DATE", "Дата установки");
define("TOOLS_PATCH_ERROR", "Ошибка");
define("TOOLS_PATCH_ERROR_DURINGINSTALL", "Ошибка при инсталляции");
define("TOOLS_PATCH_INSTALLED", "Патч установлен");
define("TOOLS_PATCH_INVALIDVERSION", "Патч не предназначен для используемой версии системы NetCat, текущая версия %EXIST, патч для версии %REQUIRE.");
define("TOOLS_PATCH_ALREDYINSTALLED", "Патч уже установлен");

define("TOOLS_PATCH_NOTAVAIL_DEMO", "Не доступно в демо-версии");
define("NETCAT_DEMO_NOTICE", "Система управления сайтами NetCat %s DEMO");
define("NETCAT_PERSONAL_MODULE_DESCRIPTION", "Возможность подключения дополнительных модулей существует только в полноценной версии.<br />
                                              Оценить функционал недостающего Вам модуля Вы можете путем скачивания той редакции, где он представлен.<br />
                                              <a href='https://netcat.ru/products/editions/compare/' target='_blank'>Таблица</a> сравнения редакций. ");

#UPGRADE
define("TOOLS_UPGRADE_ERR_NO_PRODUCTNUMBER", "В системе отсутствует номер лицензии");
define("TOOLS_UPGRADE_ERR_INVALID_PRODUCTNUMBER", "Номер не прошёл проверку на достоверность. Перепроверьте правильность номера вашей лицензии");
define("TOOLS_UPGRADE_ERR_NO_MATCH_HOST", "Используемый в системе ключ активации не прошёл проверку. Подлинность системы на данном домене не установлена.");
define("TOOLS_UPGRADE_ERR_NO_ORDER", "Для данной лицензии не поступало заказа для перехода системы на старшую редакцию.");
define("TOOLS_UPGRADE_ERR_NOT_PAID", "Заказ на переход системы на старшую редакцию не оплачен на netcat.ru.");

#ACTIVATION
define("TOOLS_ACTIVATION", "Активация системы");
define("TOOLS_ACTIVATION_INSTRUCTION", 'Инструкция активация системы');
define("TOOLS_ACTIVATION_VERB", "Активировать");
define("TOOLS_ACTIVATION_OK", "Активация прошла успешно");
define("TOOLS_ACTIVATION_OWNER", "Владелец лицензии");
define("TOOLS_ACTIVATION_LICENSE", "Номер лицензии");
define("TOOLS_ACTIVATION_CODE", "Ключ активации");
define("TOOLS_ACTIVATION_ALREADY_ACTIVE", "Система уже активирована");
define("TOOLS_ACTIVATION_INPUT_KEY_CODE", "Необходимо ввести номер лицензии и ключ активации");
define("TOOLS_ACTIVATION_INVALID_KEY_CODE", "Лицензия или ключ активации не прошли проверку");
define("TOOLS_ACTIVATION_DAY", "Срок действия демо-версии истекает через %DAY дн.");
define("TOOLS_ACTIVATION_FORM", "Для активации системы Вам нужно ввести номер лицензии и ключ активации, которые Вы получите после <a href='https://netcat.ru/products/editions/' target='_blank'>покупки</a>");
define("TOOLS_ACTIVATION_DESC", "В полноценной версии:
<ul>
<li> открый код;</li>
<li> неограниченный срок действия лицензии;</li>
<li> возможность дополнять редакцию необходимым функционалом путем перехода на другие редакции;</li>
<li> автоматическая установка обновлений;</li>
<li>годовая бесплатная оперативная техническая поддержка.</li>
</ul>");
//define("TOOLS_ACTIVATION_DEMO_DISABLED", "Возможность обновления существует только в полноценной версии.<br />");
define("TOOLS_ACTIVATION_REMIND_UNCOMPLETED", "Введены данные о лицензии. Завершите процесс активации в разделе &laquo;<a href='%s'>Активация системы</a>&raquo;.");

define("TOOLS_ACTIVATION_FORM_ERR_MANDATORY", "Заполните все необходимые поля");
define("TOOLS_ACTIVATION_FORM_ERR_ORG_EMAIL", "Неверный формат email организации");
define("TOOLS_ACTIVATION_FORM_ERR_PERSON_EMAIL", "Неверный формат email контактного лица");
define("TOOLS_ACTIVATION_FORM_ERR_PRIMARY_EMAIL", "Неверный формат email");
define("TOOLS_ACTIVATION_FORM_ERR_ADDIT_EMAIL", "Неверный формат дополнительного email");
define("TOOLS_ACTIVATION_FORM_ERR_INN", "ИНН должен содержать 10 или 12 цифр");

define("TOOLS_ACTIVATION_PLEASE_CHECK", "<p>Внимание! Проверяйте точность указываемых сведений, в дальнейшем изменить владельца будет невозможно, в соответствии с Лицензионным соглашением.</p>
Укажите информацию о владельце лицензии:<br />");
define("TOOLS_ACTIVATION_FLD_OWNER", "Владелец лицензии");
define("TOOLS_ACTIVATION_FLD_PHIS", "Физическое лицо");
define("TOOLS_ACTIVATION_FLD_UR", "Юридическое лицо");
define("TOOLS_ACTIVATION_FLD_NAME", "ФИО");
define("TOOLS_ACTIVATION_FLD_CITY", "Город");
define("TOOLS_ACTIVATION_FLD_PHIS_PHONE", "Контактный телефон");
define("TOOLS_ACTIVATION_FLD_PRIMARY_EMAIL", "Email");
define("TOOLS_ACTIVATION_FLD_ADDIT_EMAIL", "Дополнительный email");
define("TOOLS_ACTIVATION_FLD_ORGANIZATION", "Название организации");
define("TOOLS_ACTIVATION_FLD_INN", "ИНН");
define("TOOLS_ACTIVATION_FLD_ADDRESS", "Юридический адрес");
define("TOOLS_ACTIVATION_FLD_ORG_EMAIL", "Email организации");
define("TOOLS_ACTIVATION_FLD_PHONE", "Телефон компании");
define("TOOLS_ACTIVATION_FLD_CEO", "ФИО генерального директора");
define("TOOLS_ACTIVATION_FLD_PERSON", "Контактное лицо");
define("TOOLS_ACTIVATION_FLD_PERSON_EMAIL", "Email контактного лица");
define("TOOLS_ACTIVATION_FLD_PERSON_PHONE", "Телефон контактного лица");

define("REPORTS", "Общая статистика проекта");
define("REPORTS_SECTIONS", "%d раздел(ов) (выключено: %d)");
define("REPORTS_USERS", "%d пользователей (выключено: %d)");
define("REPORTS_LAST_NAME", "Название раздела");
define("REPORTS_CLASS", "Статистика компонентов");
define("REPORTS_STAT_CLASS_DOGET", "Выбрать");
define("REPORTS_STAT_CLASS_CLEAR", "Очистить");
define("REPORTS_STAT_CLASS_CLEARED", "Объекты удалены");
define("REPORTS_STAT_CLASS_CONFIRM", "Подвердите удаление объектов из компонентов раздела");
define("REPORTS_STAT_CLASS_CONFIRM_OK", "Далее");
define("REPORTS_STAT_CLASS_NOT_CC", "Не выбраны компоненты в разделе");
define("REPORTS_STAT_CLASS_USE", "Используемые");
define("REPORTS_STAT_CLASS_NOTUSE", "Неиспользуемые");

define("REPORTS_SYSMSG_MSG", "Сообщение");
define("REPORTS_SYSMSG_DATE", "Дата");
define("REPORTS_SYSMSG_NONE", "Нет ни одного системного сообщения.");
define("REPORTS_SYSMSG_MARK", "Пометить как прочитанное");
define("REPORTS_SYSMSG_TOTAL", "Всего");
define("REPORTS_SYSMSG_BACK", "Вернуться к списку");

define("SUPPORT", "Обращение в техподдержку");
define("SUPPORT_HELP_MESSAGE", "
Техническая поддержка доступна только зарегистрированным пользователям системы NetCat.<br />
Для того, чтобы обратиться в техподдержку:
<ol>
 <li style='padding-bottom:10px'><a target=_blank href='https://netcat.ru/forclients/my/copies/add_copies.html'>Зарегистрируйте Вашу копию системы</a>.
 <li style='padding-bottom:10px'>После проверки введенных Вами данных Вы можете создавать и отслеживать обращения<br> в техническую поддержку
   на странице &laquo;<a target=_blank href='https://netcat.ru/forclients/support/tickets/'>Поддержка онлайн</a>&raquo;.
 </li>
</ol>
");

define("TOOLS_SQL", "Командная строка SQL");
define("TOOLS_SQL_ERR_NOQUERY", "Введите запрос!");
define("TOOLS_SQL_SEND", "Отправить запрос");
define("TOOLS_SQL_OK", "Запрос выполнен успешно");
define("TOOLS_SQL_TOTROWS", "Строк, соответствующих запросу");
define("TOOLS_SQL_HELP", "Примеры запросов");
define("TOOLS_SQL_HISTORY", "Последние 15 запросов");
define("TOOLS_SQL_HELP_EXPLAIN", "показать список полей из таблицы %s");
define("TOOLS_SQL_HELP_SELECT", "показать количество строк в таблице %s");
define("TOOLS_SQL_HELP_SHOW", "показать список таблиц");
define("TOOLS_SQL_HELP_DOCS", "С подробной документацией по БД MySQL вы можете ознакомиться по адресу:<br>\n<a target=_blank href=http://dev.mysql.com/doc/refman/5.1/en/>http://dev.mysql.com/doc/refman/5.1/en/</a>");
define("TOOLS_SQL_BENCHMARK", "Время выполнения запроса");
define("TOOLS_SQL_ERR_OPEN_FILE", "Не удалось открыть sql-файл: %s");
define("TOOLS_SQL_ERR_FILE_QUERY", "Неудачное выполнение запроса в файле %s MySQL ошибка: %s");

define("NETCAT_TRASH_SIZEINFO", "На данный момент в корзине - %s. <br />Лимит корзины - %s МБ.");
define("NETCAT_TRASH_NOMESSAGES", "Корзина пуста.");
define("NETCAT_TRASH_MESSAGES_SK1", "объект");
define("NETCAT_TRASH_MESSAGES_SK2", "объектов");
define("NETCAT_TRASH_MESSAGES_SK3", "объекта");
define("NETCAT_TRASH_RECOVERED_SK1", "Восстановлен");
define("NETCAT_TRASH_RECOVERED_SK2", "Восстановлено");
define("NETCAT_TRASH_RECOVERED_SK3", "Восстановлено");
define("NETCAT_TRASH_RECOVERY", "Восстановить");
define("NETCAT_TRASH_DELETE_FROM_TRASH", "Удалить из корзины");
define("NETCAT_TRASH_OBJECT_WERE_DELETED_TRASHBIN_FULL", "Объекты не были помещены в корзину, так как она заполнена");
define("NETCAT_TRASH_OBJECT_IN_TRASHBIN_AND_CANCEL", "Объекты перемещены в <a href='%s'>корзину</a>. <a href='%s'>Отменить</a>");
define("NETCAT_TRASH_TRASHBIN_DISABLED", "Корзина удаленных объектов выключена");
define("NETCAT_TRASH_EDIT_SETTINGS", "Изменить настройки");
define("NETCAT_TRASH_OBJECT_NOT_FOUND", "Не найдено объектов, удовлетворяющих выборке");
define("NETCAT_TRASH_TRASHBIN", "Корзина");
define("NETCAT_TRASH_PRERECOVERYSUB_INFO", "Некоторые из восстанавливаемых объектов находились в разделах, которых сейчас уже нет. NetCat может восстановить эти разделы с теми параметрами, которые были на момент удаления объектов. Вы можете помнять эти свойства.");
define("NETCAT_TRASH_PRERECOVERYSUB_CHECKED", "включен");
define("NETCAT_TRASH_PRERECOVERYSUB_NAME", "Название");
define("NETCAT_TRASH_PRERECOVERYSUB_KEYWORD", "Ключевое слово");
define("NETCAT_TRASH_PRERECOVERYSUB_PARENT", "Родительский раздел");
define("NETCAT_TRASH_PRERECOVERYSUB_ROOT", "Корневой раздел сайта");
define("NETCAT_TRASH_PRERECOVERYSUB_NEXT", "Далее");
define("NETCAT_TRASH_FILTER", "Выборка удаленных объектов");
define("NETCAT_TRASH_FILTER_DATE_FROM", "Дата удаления с");
define("NETCAT_TRASH_FILTER_DATE_TO", "Дата удаления по");
define("NETCAT_TRASH_FILTER_DATE_FORMAT", "дд-мм-гггг чч:мм");
define("NETCAT_TRASH_FILTER_SUBDIVISION", "Раздел");
define("NETCAT_TRASH_FILTER_COMPONENT", "Компонент");
define("NETCAT_TRASH_FILTER_ALL", "все");
define("NETCAT_TRASH_FILTER_APPLY", "Выбрать");
define("NETCAT_TRASH_FILE_DOEST_EXIST", "Файл %s не найден");
define("NETCAT_TRASH_FOLDER_FAIL", "Директория %s не существует или не доступна для записи");
define("NETCAT_TRASH_ERROR_RELOAD_PAGE", "Обнаружены ошибки. Необходимо <a href='index.php'>перезагрузить страницу</a>");
define("NETCAT_TRASH_TRASHBIN_IS_FULL", "Корзина переполнена");
define("NETCAT_TRASH_TEMPLATE_DOESNT_EXIST", "У данного компонента нет шаблона для корзины удаленных объектов");
define("NETCAT_TRASH_IDENTIFICATOR", "Идентификатор");
define("NETCAT_TRASH_USER_IDENTIFICATOR", "ID добавившего пользователя");

# USERS
define("CONTROL_USER_GROUPS", "Группы пользователей");
define("CONTROL_USER_FUNCS_ALLUSERS", "все");
define("CONTROL_USER_FUNCS_ONUSERS", "включенные");
define("CONTROL_USER_FUNCS_OFFUSERS", "выключенные");
define("CONTROL_USER_FUNCS_DOGET", "Выбрать");
define("CONTROL_USER_FUNCS_VIEWCONTROL", "Настройка вывода");
define("CONTROL_USER_FUNCS_SORTBY", "Сортировать по полю");
define("CONTROL_USER_FUNCS_USER_NUMBER_ON_THE_PAGE", "пользователей на странице.");
define("CONTROL_USER_FUNCS_SORT_ORDER", "Порядок сортировки");
define("CONTROL_USER_FUNCS_SORT_ORDER_ACS", "По возрастанию");
define("CONTROL_USER_FUNCS_SORT_ORDER_DESC", "По убыванию");
define("CONTROL_USER_FUNCS_PREV_PAGE", "предыдущая страница");
define("CONTROL_USER_FUNC_CONFIRM_DEL", "Подтвердите удаление");
define("CONTROL_USER_FUNC_CONFIRM_DEL_OK", "Подтверждаю");
define("CONTROL_USER_FUNC_CONFIRM_DEL_NOT_USER", "Не выбраны пользователи");
define("CONTROL_USER_FUNC_GROUP_ERROR", "Не выбранна группа");
define("CONTROL_USER", "Пользователь");
define("CONTROL_USER_FUNCS_EDITACCESSRIGHT", "Редактировать права доступа");
define("CONTROL_USER_ACTIONS", "Действия");
define("CONTROL_USER_RIGHTS_ITEM", "Сущность");
define("CONTROL_USER_RIGHTS_TYPE_OF_RIGHT", "Тип прав");
define("CONTROL_USER_RIGHTS", "Права");
define("CONTROL_USER_ERROR_NEWPASS_IS_CURRENT", "Новый пароль совпадает с текущим!");
define("CONTROL_USER_CHANGEPASS", "сменить пароль");
define("CONTROL_USER_EDIT", "редактировать");
define("CONTROL_USER_REG", "Регистрация пользователя");
define("CONTROL_USER_NEWPASSWORD", "Новый пароль");
define("CONTROL_USER_NEWPASSWORDAGAIN", "Новый пароль еще раз");
define("CONTROL_USER_MSG_USERNOTFOUND", "Не найдено ни одного пользователя, соответствующего Вашему запросу.");
define("CONTROL_USER_GROUP", "Группа");
define("CONTROL_USER_GROUP_MEMBERS", "Участники");
define("CONTROL_USER_GROUP_NOMEMBERS", "Участников нет.");
define("CONTROL_USER_GROUP_TOTAL", "всего");
define("CONTROL_USER_GROUPNAME", "Название группы");
define("CONTROL_USER_ERROR_GROUPNAME_IS_EMPTY", "Название группы не может быть пустым!");
define("CONTROL_USER_ADDNEWGROUP", "Добавить группу");
define("CONTROL_USER_CHANGERIGHTS", "Настроить права доступа");
define("CONTROL_USER_NEW_ADDED", "Пользователь добавлен");
define("CONTROL_USER_NEW_NOTADDED", "Пользователь не добавлен");
define("CONTROL_USER_EDITSUCCESS", "Пользователь успешно изменен");
define("CONTROL_USER_REGISTER", "Регистрация нового пользователя");
define("CONTROL_USER_GROUPS_ADD", "Добавление группы");
define("CONTROL_USER_GROUPS_EDIT", "Редактирование группы");
define("CONTROL_USER_ACESSRIGHTS", "права доступа");
define("CONTROL_USER_USERSANDRIGHTS", "Пользователи и права");
define("CONTROL_USER_PASSCHANGE", "Смена пароля");
define("CONTROL_USER_CATALOGUESWITCH", "Выбор каталога");
define("CONTROL_USER_SECTIONSWITCH", "Выбор раздела");
define("CONTROL_USER_TITLE_USERINFOEDIT", "Редактирование информации о пользователе");
define("CONTROL_USER_TITLE_PASSWORDCHANGE", "Смена пароля пользователю");
define("CONTROL_USER_ERROR_EMPTYPASS", "Пароль не может быть пустым!");
define("CONTROL_USER_ERROR_NOTCANGEPASS", "Пароль не изменен. Ошибка!");
define("CONTROL_USER_OK_CHANGEDPASS", "Пароль успешно изменен.");
define("CONTROL_USER_ERROR_RETRY", "Попробуйте снова!");
define("CONTROL_USER_ERROR_PASSDIFF", "Введенные пароли не совпадают!");
define("CONTROL_USER_MAIL", "Рассылка по базе");
define("CONTROL_USER_MAIL_TITLE_COMPOSE", "Отправление письма");
define("CONTROL_USER_MAIL_GROUP", "Группа пользователей");
define("CONTROL_USER_MAIL_ALLGROUPS", "Все группы");
define("CONTROL_USER_MAIL_FROM", "Отправитель");
define("CONTROL_USER_MAIL_BODY", "Текст письма");
define("CONTROL_USER_MAIL_ADDATTACHMENT", "вложить файл");
define("CONTROL_USER_MAIL_SEND", "Отправить сообщение");
define("CONTROL_USER_MAIL_ERROR_EMAILFIELD", "Не определено поле содержащее Email пользователей.");
define("CONTROL_USER_MAIL_OK", "Письмо отправлено всем пользователям");
define("CONTROL_USER_MAIL_ERROR_NOONEEMAIL", "В указанном поле не найдено ни одного электронного адреса.");
define("CONTROL_USER_MAIL_ATTCHAMENT", "Присоединить файл");
define("CONTROL_USER_MAIL_ERROR_ONE", "Рассылка невозможна, так как в <a href=".$ADMIN_PATH."settings.php?phase=1>системных настройках</a> не указано поле для рассылок.");
define("CONTROL_USER_MAIL_ERROR_TWO", "Рассылка невозможна, так как в <a href=".$ADMIN_PATH."settings.php?phase=1>системных настройках</a> не указано имя отправителя писем.");
define("CONTROL_USER_MAIL_ERROR_THREE", "Рассылка невозможна, так как в <a href=".$ADMIN_PATH."settings.php?phase=1>системных настройках</a> не указан Email отправителя писем.");
define("CONTROL_USER_MAIL_ERROR_NOBODY", "Отсутствует текст письма.");
define("CONTROL_USER_MAIL_CHANGE", "изменить");
define("CONTROL_USER_MAIL_CONTENT", "Содержимое письма");
define("CONTROL_USER_MAIL_SUBJECT", "Тема письма");
define("CONTROL_USER_MAIL_RULES", "Условия рассылки");
define("CONTROL_USER_FUNCS_USERSGET", "Выборка пользователей");
define("CONTROL_USER_FUNCS_USERSGET_EXT", "Расширенный поиск");
define("CONTROL_USER_FUNCS_SEARCHEDUSER", "Найдено пользователей");
define("CONTROL_USER_FUNCS_USERCOUNT", "Всего пользователей");
define("CONTROL_USER_FUNCS_ADDUSER", "Добавить пользователя");
define("CONTROL_USER_FUNCS_NORIGHTS", "Данному пользователю не присвоены права.");
define("CONTROL_USER_FUNCS_GROUP_NORIGHTS", "У данной группы нет прав.");
define("CONTROL_USER_RIGHTS_GUESTONE", "Гость");
define("CONTROL_USER_RIGHTS_DIRECTOR", "Директор");
define("CONTROL_USER_RIGHTS_SUPERVISOR", "Супервизор");
define("CONTROL_USER_RIGHTS_SITEADMIN", "Редактор сайта");
define("CONTROL_USER_RIGHTS_CATALOGUEADMINALL", "Редактор всех сайтов");
define("CONTROL_USER_RIGHTS_SUBDIVISIONADMIN", "Редактор раздела");
define("CONTROL_USER_RIGHTS_SUBCLASSADMIN", "Редактор компонента");
define("CONTROL_USER_RIGHTS_SUBCLASSADMINS", "Редактор компонента раздела");
define("CONTROL_USER_RIGHTS_CLASSIFICATORADMIN", "Администратор списка");
define("CONTROL_USER_RIGHTS_CLASSIFICATORADMINALL", "Администратор всех списков");
define("CONTROL_USER_RIGHTS_EDITOR", "Редактор");
define("CONTROL_USER_RIGHTS_SUBSCRIBER", "Подписчик");
define("CONTROL_USER_RIGHTS_MODERATOR", "Управление пользователями");
define("CONTROL_USER_RIGHTS_BAN", "Ограничение в правах");
define("CONTROL_USER_RIGHTS_SITE", "Ограничение в правах сайта");
define("CONTROL_USER_RIGHTS_SITEALL", "Ограничение в правах на всех сайтах");
define("CONTROL_USER_RIGHTS_SUB", "Ограничение в правах раздела");
define("CONTROL_USER_RIGHTS_CC", "Ограничение в правах компонента");
define("CONTROL_USER_RIGHTS_LOAD", "Загрузка");
define("CONTROL_USER_RIGHT_ADDNEWRIGHTS", "Присвоить права");
define("CONTROL_USER_RIGHT_ADDPERM", "Присвоение права пользователю");
define("CONTROL_USER_RIGHT_ADDPERM_GROUP", "Присвоение права группе");
define("CONTROL_USER_FUNCS_FROMCAT", "из каталога");
define("CONTROL_USER_FUNCS_FROMSEC", "из раздела");
define("CONTROL_USER_FUNCS_ADDNEWRIGHTS", "Присвоить новые права");
define("CONTROL_USER_FUNCS_ERR_CANTREMGROUP", "Не удалось удалить группу %s. Ошибка!");
define("CONTROL_USER_SELECTSITE", "Выберите сайт");
define("CONTROL_USER_SELECTSECTION", "Выберите раздел");
define("CONTROL_USER_NOONESECSINSITE", "В данном сайте нет ни одного раздела.");
define("CONTROL_USER_FUNCS_CLASSINSECTION", "Первый инфоблок раздела");
define("CONTROL_USER_RIGHTS_ERR_CANTREMPRIV", "Не удалось удалить привилегию. Ошибка!");
define("CONTROL_USER_RIGHTS_UPDATED_OK", "Права пользователя обновлены.");
define("CONTROL_USER_RIGHTS_ERROR_NOSELECTED", "Не выбрана сущность");
define("CONTROL_USER_RIGHTS_ERROR_DATA", "Ошибка в дате");
define("CONTROL_USER_RIGHTS_ERROR_DB", "Ошибка записи в БД");
define("CONTROL_USER_RIGHTS_ERROR_POSSIBILITY", "Не выбрана возможность");
define("CONTROL_USER_RIGHTS_ERROR_NOTSITE", "Не выбран сайт");
define("CONTROL_USER_RIGHTS_ERROR_NOTSUB", "Не выбран раздел");
define("CONTROL_USER_RIGHTS_ERROR_NOTCCINSUB", "В выбранном разделе нет компонентов");
define("CONTROL_USER_RIGHTS_ERROR_NOTTYPEOFRIGHT", "Не выбран тип прав");
define("CONTROL_USER_RIGHTS_ERROR_START", "Ошибка в дате начала действия права");
define("CONTROL_USER_RIGHTS_ERROR_END", "Ошибка в дате окончания действия права");
define("CONTROL_USER_RIGHTS_ERROR_STARTEND", "Время окончания действия прав не может быть раньше времени начала");
define("CONTROL_USER_RIGHTS_ERROR_GUEST", "Нельзя назнчаить право \"Гость\" самому себе");
define("CONTROL_USER_RIGHTS_ADDED", "Права присвоены");
define("CONTROL_USER_RIGHTS_LIVETIME", "Срок действия");
define("CONTROL_USER_RIGHTS_UNLIMITED", "не ограничен");
define("CONTROL_USER_RIGHTS_NONLIMITED", "без ограничений");
define("CONTROL_USER_RIGHTS_LIMITED", "ограничен");
define("CONTROL_USER_RIGHTS_STARTING_OPERATIONS", "Начало действия");
define("CONTROL_USER_RIGHTS_FINISHING_OPERATIONS", "Конец действия");
define("CONTROL_USER_RIGHTS_NOW", "сейчас");
define("CONTROL_USER_RIGHTS_ACROSS", "через");
define("CONTROL_USER_RIGHTS_ACROSS_MINUTES", "минут");
define("CONTROL_USER_RIGHTS_ACROSS_HOURS", "часов");
define("CONTROL_USER_RIGHTS_ACROSS_DAYS", "дней");
define("CONTROL_USER_RIGHTS_ACROSS_MONTHS", "месяцев");
define("CONTROL_USER_RIGHTS_RIGHT", "Право");
define("CONTROL_USER_RIGHTS_CONTROL_ADD", "добавление");
define("CONTROL_USER_RIGHTS_CONTROL_EDIT", "редактирование");
define("CONTROL_USER_RIGHTS_CONTROL_DELETE", "удаление");
define("CONTROL_USER_RIGHTS_CONTROL_HELP", "Помощь");
define("CONTROL_USER_USERS_MOVED_SUCCESSFULLY", "Пользователи успешно перемещены");
define("CONTROL_USER_SELECT_GROUP_TO_MOVE", "Выберите группы, в которые нужно переместить выбранных пользователей");
define("CONTROL_USER_SELECTSITEALL", "Все сайты");

# TEMPLATE
define("CONTROL_TEMPLATE", "Макеты дизайна");
define("CONTROL_TEMPLATE_ADD", "Добавление макета");
define("CONTROL_TEMPLATE_EDIT", "Редактирование макета");
define("CONTROL_TEMPLATE_DELETE", "Удаление макета");
define("CONTROL_TEMPLATE_OPT_ADD", "добавление настройки");
define("CONTROL_TEMPLATE_OPT_EDIT", "редактирование настройки");
define("CONTROL_TEMPLATE_ERR_NAME", "Укажите название макета.");
define("CONTROL_TEMPLATE_INFO_CONVERT", "Настраивая макет дизайна, не забудьте <a href='#' onclick=\"window.open('".$ADMIN_PATH."template/converter.php', 'converter','width=600,height=410,status=no,resizable=yes');\">экранировать спецсимволы</a>.");
define("CONTROL_TEMPLATE_TEPL_NAME", "Название макета");
define("CONTROL_TEMPLATE_TEPL_MENU", "Шаблоны вывода навигации");
define("CONTROL_TEMPLATE_TEPL_HEADER", "Верхняя часть страницы (Header)");
define("CONTROL_TEMPLATE_TEPL_FOOTER", "Нижняя часть страницы (Footer)");
define("CONTROL_TEMPLATE_TEPL_CREATE", "Добавить макет");
define("CONTROL_TEMPLATE_NOT_FOUND", "Макет дизайна с идентификатором %s не найден!");
define("CONTROL_TEMPLATE_ERR_USED_IN_SITE", "Данный макет дизайна используется в следующих сайтах:");
define("CONTROL_TEMPLATE_ERR_USED_IN_SUB", "Данный макет дизайна используется в следующих разделах:");
define("CONTROL_TEMPLATE_ERR_CANTDEL", "Не удалось удалить макет");
define("CONTROL_TEMPLATE_INFO_DELETE", "Вы собираетесь удалить макет");
define("CONTROL_TEMPLATE_INFO_DELETE_SOME", "Эти макеты будут удалены");
define("CONTROL_TEMPLATE_DELETED", "Макет удален");
define("CONTROL_TEMPLATE_ADDLINK", "добавить макет дизайна");
define("CONTROL_TEMPLATE_REMOVETHIS", "удалить этот макет дизайна");
define("CONTROL_TEMPLATE_PREF_EDIT", "изменить настройки");
define("CONTROL_TEMPLATE_NONE", "В системе нет ни одного макета");
define("CONTROL_TEMPLATE_TEPL_IMPORT", "Импорт макета");
define("CONTROL_TEMPLATE_IMPORT", "Импорт макета");
define("CONTROL_TEMPLATE_IMPORT_UPLOAD", "Загрузить");
define("CONTROL_TEMPLATE_IMPORT_SELECT", "Выберите шаблон для импорта (импортируются также дочерние шаблоны)");
define("CONTROL_TEMPLATE_IMPORT_CONTINUE", "Далее");
define("CONTROL_TEMPLATE_IMPORT_ERROR_NOTUPLOADED", "Ошибка импорта макета");
define("CONTROL_TEMPLATE_IMPORT_ERROR_SQL", "Ошибка при добавлении макета в базу данных");
define("CONTROL_TEMPLATE_IMPORT_ERROR_EXTRACT", "Ошибка при извлечении файлов макета %s в директорию %s");
define("CONTROL_TEMPLATE_IMPORT_ERROR_MOVE", "Ошибка копирования файлов из %s в %s");
define("CONTROL_TEMPLATE_IMPORT_SUCCESS", "Макет успешно импортирован");
define("CONTROL_TEMPLATE_EXPORT", "Экспортировать макет в файл");
define("CONTROL_TEMPLATE_FILES_PATH", "Файлы макета находятся в папке <a href='%s'>%s</a>");
define("CONTROL_TEMPLATE_PARTIALS", "Врезки");
define("CONTROL_TEMPLATE_PARTIALS_NEW", "Новая врезка");
define("CONTROL_TEMPLATE_PARTIALS_ADD", "Добавить врезку");
define("CONTROL_TEMPLATE_PARTIALS_REMOVE", "Удалить врезку");
define("CONTROL_TEMPLATE_PARTIALS_KEYWORD_FIELD", "Ключевое слово врезки (латинскими буквами)");
define("CONTROL_TEMPLATE_PARTIALS_KEYWORD_FIELD_ERROR", "Ключевое слово врезки может содержать только латинские буквы, цифры и знак подчеркивания");
define("CONTROL_TEMPLATE_PARTIALS_KEYWORD_FIELD_REQUIRED_ERROR", "Ключевое слово врезки обязательно для заполнения");
define("CONTROL_TEMPLATE_PARTIALS_DESCRIPTION_FIELD", "Название");
define("CONTROL_TEMPLATE_PARTIALS_ENABLE_ASYNC_LOAD_FIELD", "разрешить асинхронную загрузку");
define("CONTROL_TEMPLATE_PARTIALS_SOURCE_FIELD", "Шаблон врезки");
define("CONTROL_TEMPLATE_PARTIALS_EXISTS_ERROR", "Врезка с таким ключевым словом уже существует");
define("CONTROL_TEMPLATE_PARTIALS_NOT_EXISTS", "В данном макете нет ни одной врезки");
define("CONTROL_TEMPLATE_BASE_TEMPLATE", "Создать макет дизайна на основе существующего");
define("CONTROL_TEMPLATE_BASE_TEMPLATE_CREATE_FROM_SCRATCH", "Создать макет дизайна \"с нуля\"");
define("CONTROL_TEMPLATE_CONTINUE", "Продолжить");

define("CONTROL_TEMPLATE_KEYWORD", "Ключевое слово");
define("CONTROL_TEMPLATE_KEYWORD_ONLY_DIGITS", "Ключевое слово не может состоять только из цифр");
define("CONTROL_TEMPLATE_KEYWORD_TOO_LONG", "Длина ключевого слова не может быть более %d символов");
define("CONTROL_TEMPLATE_KEYWORD_INVALID_CHARACTERS", "Ключевое слово может содержать только буквы латинского алфавита, цифры и символы подчёркивания");
define("CONTROL_TEMPLATE_KEYWORD_NON_UNIQUE", "Ключевое слово «%s» уже присвоено макету дизайна «%d. %s»");
define("CONTROL_TEMPLATE_KEYWORD_RESERVED", "Невозможно использовать «%s» в качестве ключевого слова, так как оно является зарезервированным");
define("CONTROL_TEMPLATE_KEYWORD_PATH_EXISTS", "Невозможно использовать «%s» в качестве ключевого слова, так как уже существует папка с таким названием");

# CLASSIFICATORS
define("CONTENT_CLASSIFICATORS", "Списки");
define("CONTENT_CLASSIFICATORS_NAMEONE", "Список");
define("CONTENT_CLASSIFICATORS_NAMEALL", "Все списки");
define("CONTENT_CLASSIFICATORS_ELEMENTS", "элементы");
define("CONTENT_CLASSIFICATORS_ELEMENT", "Элемент");
define("CONTENT_CLASSIFICATORS_ELEMENT_NAME", "Название элемента");
define("CONTENT_CLASSIFICATORS_ELEMENT_VALUE", "Дополнительное значение");
define("CONTENT_CLASSIFICATORS_ELEMENTS_ADDONE", "Добавить элемент");
define("CONTENT_CLASSIFICATORS_ELEMENTS_ADD", "Добавление элемента");
define("CONTENT_CLASSIFICATORS_ELEMENTS_ADD_SUCCESS", "Элемент добавлен");
define("CONTENT_CLASSIFICATORS_ELEMENTS_EDIT", "Редактирование элемента");
define("CONTENT_CLASSIFICATORS_LIST_ADD", "Добавление списка");
define("CONTENT_CLASSIFICATORS_LIST_EDIT", "Редактирование списка");
define("CONTENT_CLASSIFICATORS_LIST_DELETE", "Удаление списка");
define("CONTENT_CLASSIFICATORS_LIST_DELETE_SELECTED", "Удалить выбранные");
define("CONTENT_CLASSIFICATORS_ERR_NONE", "В данном проекте нет ни одного списка.");
define("CONTENT_CLASSIFICATORS_ERR_ELEMENTNONE", "В данном списке нет ни одного элемента.");
define("CONTENT_CLASSIFICATORS_ERR_SYSDEL", "Невозможно удалить элемент из системного классификатора");
define("CONTENT_CLASSIFICATORS_ERR_EDITI_GUESTRIGHTS", "Изменение записи в классификаторе невозможно с гостевыми правами!");
define("CONTENT_CLASSIFICATORS_ERROR_NAME", "Введите русское название классификатора!");
define("CONTENT_CLASSIFICATORS_ERROR_FILE_NAME", "Выберите CSV-Файл для импортирования!");
define("CONTENT_CLASSIFICATORS_ERROR_KEYWORD", "Введите английское название классификатора (название таблицы)!");
define("CONTENT_CLASSIFICATORS_ERROR_KEYWORDINV", "Английское название (название таблицы) должно содержать только латинские буквы и цифры!");
define("CONTENT_CLASSIFICATORS_ERROR_KEYWORDFL", "Английское название (название таблицы) должно начинаться с латинской буквы!");
define("CONTENT_CLASSIFICATORS_ERROR_KEYWORDAE", "Классификатор с таким английским названием (названием таблицы) уже существует!");
define("CONTENT_CLASSIFICATORS_ERROR_KEYWORDREZ", "Данное имя зарезервировано!");
define("CONTENT_CLASSIFICATORS_ADDLIST", "Добавить список");
define("CONTENT_CLASSIFICATORS_ADD_KEYWORD", "Название таблицы (латинскими буквами)");
define("CONTENT_CLASSIFICATORS_SAVE", "Сохранить изменения");
define("CONTENT_CLASSIFICATORS_NO_NAME", "(без названия)");
define("CLASSIFICATORS_SORT_HEADER", "Тип сортировки");
define("CLASSIFICATORS_SORT_PRIORITY_HEADER", "Приоритет");
define("CLASSIFICATORS_SORT_TYPE_ID", "ID");
define("CLASSIFICATORS_SORT_TYPE_NAME", "Элемент");
define("CLASSIFICATORS_SORT_TYPE_PRIORITY", "Приоритет");
define("CLASSIFICATORS_SORT_DIRECTION", "Направление сортировки");
define("CLASSIFICATORS_SORT_ASCENDING", "Восходящая");
define("CLASSIFICATORS_SORT_DESCENDING", "Нисходящая");
define("CLASSIFICATORS_IMPORT_HEADER", "Импорт списка");
define("CLASSIFICATORS_IMPORT_BUTTON", "Импортировать");
define("CLASSIFICATORS_IMPORT_FILE", "CSV-Файл (*)");
define("CLASSIFICATORS_IMPORT_DESCRIPTION", "Если в импортируемом файле только одна колонка, то она считается полем Элемент, если две - первая колонка это Элемент, а вторая Приоритет.");
define("CLASSIFICATORS_SUCCESS_DELETEONE", "Список успешно удален.");
define("CLASSIFICATORS_SUCCESS_DELETE", "Списки успешно удалены.");
define("CLASSIFICATORS_SUCCESS_ADD", "Список успешно добавлен.");
define("CLASSIFICATORS_SUCCESS_EDIT", "Список успешно изменен.");
define("CLASSIFICATORS_ERROR_DELETEONE_SYS", "Список %s - системный, удаление запрещено.");
define("CLASSIFICATORS_ERROR_ADD", "Ошибка добавления списка.");
define("CLASSIFICATORS_ERROR_EDIT", "Ошибка изменения списка.");

# TOOLS HTML
define("TOOLS_HTML", "HTML-редактор");
define("TOOLS_HTML_INFO", "Редактировать в визуальном редакторе");

define("TOOLS_DUMP", "Архивы проекта");
define("TOOLS_DUMP_CREATE", "Создание архива");
define("TOOLS_DUMP_CREATED", "Архив проекта создан %FILE.");
define("TOOLS_DUMP_CREATION_FAILED", "Ошибка создания архива.");
define("TOOLS_DUMP_DELETED", "Файл %FILE удалён.");
define("TOOLS_DUMP_RESTORE", "Восстановление архива");
define("TOOLS_DUMP_MSG_RESTORED", "Архив восстановлен.");
define("TOOLS_DUMP_INC_TITLE", "Восстановление архива с локального диска");
define("TOOLS_DUMP_INC_DORESTORE", "Восстановить");
define("TOOLS_DUMP_INC_DBDUMP", "дамп базы данных");
define("TOOLS_DUMP_INC_FOLDER", "содержимое папки");
define("TOOLS_DUMP_ERROR_CANTDELETE", "Ошибка! Не могу удалить %FILE.");
define("TOOLS_DUMP_INC_ARCHIVE", "Архив");
define("TOOLS_DUMP_INC_DATE", "Дата");
define("TOOLS_DUMP_INC_SIZE", "Размер");
define("TOOLS_DUMP_INC_DOWNLOAD", "скачать");
define("TOOLS_DUMP_NOONE", "Архивы проекта отсутствуют.");
define("TOOLS_DUMP_DATE", "Дата архива");
define("TOOLS_DUMP_SIZE", "Размер, байт");
define("TOOLS_DUMP_CREATEAP", "Создать архив проекта");
define("TOOLS_DUMP_CONFIRM", "Подтвердите создание архива проекта");
define("TOOLS_DUMP_BACKUPLIST_HEADER", "Имеющиеся архивы проекта");
define("TOOLS_DUMP_CREATE_HEADER", "Создание архива");
define("TOOLS_DUMP_CREATE_OPT_FULL", "Полный архив (включает все файлы, базу данных и скрипт восстановления)");
define("TOOLS_DUMP_CREATE_OPT_DATA", "Архив данных (директории images, netcat_templates, modules, netcat_files и база данных)");
define("TOOLS_DUMP_CREATE_OPT_SQL", "Только база данных");
define("TOOLS_DUMP_CREATE_SUBMIT", "Создать резервную копию");
define("TOOLS_DUMP_REMOVE_SELECTED", "Удалить выбранные архивы");
define("TOOLS_DUMP_CREATE_WAIT", "Производится создание архива. Пожалуйста, подождите.");
define("TOOLS_DUMP_RESTORE_WAIT", "Производится восстановление данных из архива. Пожалуйста, подождите.");
define("TOOLS_DUMP_CONNECTION_LOST", "Потеряна связь с сервером. Если запрошенное действие не было завершено, %s.");
define("TOOLS_DUMP_CONNECTION_LOST_SYSTEM_TAR", "попробуйте разрешить выполнение системной утилиты tar из PHP");
define("TOOLS_DUMP_CONNECTION_LOST_INCREASE_PHP_LIMITS", "проверьте журнал ошибок PHP, и попробуйте увеличить лимит памяти в PHP, таймауты в в конфигурации веб-сервера, а также лимиты использования ресурсов на сервере");
define("TOOLS_DUMP_CONNECTION_LOST_INCREASE_SERVER_LIMITS", "попробуйте увеличить таймауты в в конфигурации веб-сервера и лимиты использования ресурсов на сервере");
define("TOOLS_DUMP_CONNECTION_LOST_GO_BACK", "Вернуться назад");

define("TOOLS_REDIRECT", "Переадресации");
define("TOOLS_REDIRECT_OLDURL", "Старый URL");
define("TOOLS_REDIRECT_NEWURL", "Новый URL");
define("TOOLS_REDIRECT_OLDLINK", "Старая ссылка");
define("TOOLS_REDIRECT_NEWLINK", "Новая ссылка");
define("TOOLS_REDIRECT_HEADER", "Заголовок");
define("TOOLS_REDIRECT_HEADERSEND", "Посылаемый заголовок");
define("TOOLS_REDIRECT_SETTINGS", "Настройки");
define("TOOLS_REDIRECT_CHANGEINFO", "Изменить информацию");
define("TOOLS_REDIRECT_NONE", "В данной группе нет переадресаций.");
define("TOOLS_REDIRECT_ADD", "Добавить переадресацию");
define("TOOLS_REDIRECT_EDIT", "Изменить переадресацию");
define("TOOLS_REDIRECT_ADDONLY", "Добавить");
define("TOOLS_REDIRECT_CANTBEEMPTY", "Поля не могут быть пустыми!");
define("TOOLS_REDIRECT_OLDURL_MUST_BE_UNIQUE", "Уже есть переадресация с такой &quot;старой ссылкой&quot; - <a href='".nc_core('NETCAT_FOLDER')."action.php?ctrl=admin.redirect&action=edit&id=%s'>перейти</a>");
define("TOOLS_REDIRECT_DISABLED", "В конфигурационном файле инструмент \"Переадресация\" выключен.<br/>Чтобы его включть, исправьте в файле vars.inc.php значение параметра \$NC_REDIRECT_DISABLED на 0. ");
define("TOOLS_REDIRECT_GROUP", "Группа");
define("TOOLS_REDIRECT_GROUP_NAME", "Название группы");
define("TOOLS_REDIRECT_GROUP_ADD", "Добавить группу");
define("TOOLS_REDIRECT_GROUP_EDIT", "Изменить группу");
define("TOOLS_REDIRECT_GROUP_DELETE", "Удалить группу");
define("TOOLS_REDIRECT_BACK", "Назад");
define("TOOLS_REDIRECT_SAVE_OK", "Переадресация сохранена");
define("TOOLS_REDIRECT_GROUP_SAVE_OK", "Группа сохранена");
define("TOOLS_REDIRECT_SAVE_ERROR", "Ошибка сохранения");
define("TOOLS_REDIRECT_DELETE", "Удалить");
define("TOOLS_REDIRECT_DELETE_CONFIRM_REDIRECTS", "Будут удалены следующие переадресации:");
define("TOOLS_REDIRECT_DELETE_CONFIRM_GROUP", "Будет удалена группа &quot;%s&quot; включая следующие переадресации:");
define("TOOLS_REDIRECT_DELETE_OK", "Удаление выполнено");
define("TOOLS_REDIRECT_STATUS", "Статус");
define("TOOLS_REDIRECT_SAVE", "Сохранить");
define("TOOLS_REDIRECT_MOVE", "Перенести в группу");
define("TOOLS_REDIRECT_MOVE_CONFIRM_REDIRECTS", "Будут перемещены следующие переадресации:");
define("TOOLS_REDIRECT_MOVE_OK", "Перемещение выполнено");
define("TOOLS_REDIRECT_NOTHING_SELECTED", "Не выбрано ни одной переадресации");
define("TOOLS_REDIRECT_IMPORT", "Импорт");
define("TOOLS_REDIRECT_FIELDS", "Поля переадресаций");
define("TOOLS_REDIRECT_CONTINUE", "Продолжить");
define("TOOLS_REDIRECT_DO_IMPORT", "Импортировать");
define("TOOLS_REDIRECT_MOVE_TITLE", "Перемещение переадресаций");
define("TOOLS_REDIRECT_DELETE_TITLE", "Удаление переадресаций");
define("TOOLS_REDIRECT_IMPORT_TITLE", "Импортирование переадресаций");

define("TOOLS_CRON", "Управление задачами");
define("TOOLS_CRON_MINUTES", "Интервал (м:ч:д)");
define("TOOLS_CRON_HOURS", "Часы");
define("TOOLS_CRON_DAYS", "Дни");
define("TOOLS_CRON_MONTHS", "Месяцы");
define("TOOLS_CRON_LANCHED", "Последний запуск");
define("TOOLS_CRON_NEXT", "Следующая задача");
define("TOOLS_CRON_SCRIPTURL", "Ссылка на скрипт");
define("TOOLS_CRON_ADDLINK", "Добавить задачу");
define("TOOLS_CRON_CHANGE", "Изменить");
define("TOOLS_CRON_NOTASKS", "В данном проекте нет ни одной задачи.");
define("TOOLS_CRON_WRONG_DOMAIN", "Домен, указанный в файле crontab.php (%s), не соответствует текущему (%s), задачи могут не выполняться! Проверьте настройку в соответствии с <a href='https://netcat.ru/developers/docs/system-tools/task-management/' TARGET='_blank'>документацией</a>.");

define("TOOLS_COPYSUB", "Копирование разделов");
define("TOOLS_COPYSUB_COPY", "Копировать");
define("TOOLS_COPYSUB_COPY_SUCCESS", "Копирование успешно выполнено");
define("TOOLS_COPYSUB_SOURCE", "Источник");
define("TOOLS_COPYSUB_DESTINATION", "Приемник");
define("TOOLS_COPYSUB_ACTION", "Действие");
define("TOOLS_COPYSUB_COPY_SITE", "Копировать сайт");
define("TOOLS_COPYSUB_COPY_SUB", "Копировать раздел");
define("TOOLS_COPYSUB_COPY_SUB_LOWER", "копировать раздел");
define("TOOLS_COPYSUB_SITE", "Сайт");
define("TOOLS_COPYSUB_SUB", "Разделы");
define("TOOLS_COPYSUB_KEYWORD_SUB", "Ключевое слово раздела");
define("TOOLS_COPYSUB_NAME_CC", "Название компонента");
define("TOOLS_COPYSUB_KEYWORD_CC", "Ключевое слово компонента");
define("TOOLS_COPYSUB_TEMPLATE_NAME", "Шаблоны имён");
define("TOOLS_COPYSUB_SETTINGS", "Параметры копирования");
define("TOOLS_COPYSUB_COPY_WITH_CHILD", "копировать подразделы");
define("TOOLS_COPYSUB_COPY_WITH_CC", "копировать компоненты в разделе");
define("TOOLS_COPYSUB_COPY_WITH_OBJECT", "копировать объекты");
define("TOOLS_COPYSUB_ERROR_KEYWORD_EXIST", "Раздел с таким ключевым словом уже существует");
define("TOOLS_COPYSUB_ERROR_LEVEL_COUNT", "Нельзя скопировать раздел в сообственный подраздел");
define("TOOLS_COPYSUB_ERROR_PARAM", "Неверные параметры");
define("TOOLS_COPYSUB_ERROR_SITE_NOT_FOUND", "Сайт не найден");

# TOOLS TRASH
define("TOOLS_TRASH", "Корзина удаленных объектов");
define("TOOLS_TRASH_CLEAN", "Очистить корзину");

# MODERATION SECTION
define("NETCAT_MODERATION_NO_OBJECTS_IN_SUBCLASS", "В данном инфоблоке раздела нет данных для вывода.");

define("NETCAT_MODERATION_ERROR_NORIGHTS", "У вас нет доступа для осуществления операции.");
define("NETCAT_MODERATION_ERROR_NORIGHT", "У вас нет прав на эту операцию");
define("NETCAT_MODERATION_ERROR_NORIGHTGUEST", "Гостевое право не позволяет выполнить эту операцию");
define("NETCAT_MODERATION_ERROR_NOOBJADD", "Ошибка добавления объекта.");
define("NETCAT_MODERATION_ERROR_NOOBJCHANGE", "Ошибка изменения объекта.");
define("NETCAT_MODERATION_MSG_OBJADD", "Объект добавлен.");
define("NETCAT_MODERATION_MSG_OBJADDMOD", "Объект будет доступен после проверки администратором.");
define("NETCAT_MODERATION_MSG_OBJCHANGED", "Объект изменен.");
define("NETCAT_MODERATION_MSG_OBJDELETED", "Объект удален.");
define("NETCAT_MODERATION_FILES_UPLOADED", "Закачан");
define("NETCAT_MODERATION_FILES_DELETE", "удалить файл");
define("NETCAT_MODERATION_LISTS_CHOOSE", "-- выбрать --");
define("NETCAT_MODERATION_RADIO_EMPTY", "Не отвечать");
define("NETCAT_MODERATION_PRIORITY", "Приоритет объекта");
define("NETCAT_MODERATION_TURNON", "включить");
define("NETCAT_MODERATION_OBJADDED", "Добавление объекта");
define("NETCAT_MODERATION_OBJUPDATED", "Изменение объекта");
define("NETCAT_MODERATION_MSG_OBJSDELETED", "Объекты удалены");
define("NETCAT_MODERATION_OBJ_ON", "вкл");
define("NETCAT_MODERATION_OBJ_OFF", "выкл");
define("NETCAT_MODERATION_OBJECT", "Объект");
define("NETCAT_MODERATION_MORE", "еще");
define("NETCAT_MODERATION_MOVE_UP", "Переместить вверх");
define("NETCAT_MODERATION_MOVE_DOWN", "Переместить вниз");
define("NETCAT_MODERATION_BLOCK_SETTINGS", "Настройки блока");
define("NETCAT_MODERATION_DELETE_BLOCK", "Удалить блок");
define("NETCAT_MODERATION_ADD_BLOCK", "Добавить блок");
define("NETCAT_MODERATION_ADD_BLOCK_TITLE", "Добавление блока");
define("NETCAT_MODERATION_ADD_OBJECT", "Добавить");
define("NETCAT_MODERATION_ADD_OBJECT_DEFAULT", "элемент");
define("NETCAT_MODERATION_REMOVE_INFOBLOCK_CONFIRMATION_HEADER", "Удалить безвозвратно?");
define("NETCAT_MODERATION_REMOVE_INFOBLOCK_CONFIRMATION_BODY", "Блок и его содержимое будут удалены со страницы. Для подтверждения нажмите «Удалить».");
define("NETCAT_MODERATION_COMPONENT_SEARCH_BY_NAME", "поиск по названию");
define("NETCAT_MODERATION_CLEAR_FIELD", "очистить");
define("NETCAT_MODERATION_COMPONENT_NO_TEMPLATE", "Основной шаблон компонента");
define("NETCAT_MODERATION_COMPONENT_TEMPLATE", "Шаблон");
define("NETCAT_MODERATION_COMPONENT_TEMPLATES", "Шаблоны");
define("NETCAT_MODERATION_COMPONENT_TEMPLATE_PREV", "предыдущий шаблон");
define("NETCAT_MODERATION_COMPONENT_TEMPLATE_NEXT", "следующий шаблон");
define("NETCAT_MODERATION_COPY_BLOCK", "Скопировать");
define("NETCAT_MODERATION_PASTE_BLOCK", "Вставить скопированный блок");
define("NETCAT_MODERATION_REMOVE_IMAGE", "Удалить изображение");
define("NETCAT_MODERATION_REPLACE_IMAGE", "Заменить изображение");

define("NETCAT_MODERATION_WARN_COMMITDELETION", "Подтвердите удаление объекта #%s");
define("NETCAT_MODERATION_WARN_COMMITDELETIONINCLASS", "Подтвердите удаление объектов инфоблока #%s");

define("NETCAT_MODERATION_PASSWORD", "Пароль (*)");
define("NETCAT_MODERATION_PASSWORDAGAIN", "Введите пароль ещё раз");
define("NETCAT_MODERATION_INFO_REQFIELDS", "Звездочкой (*) отмечены поля, обязательные для заполнения.");
define("NETCAT_MODERATION_BUTTON_ADD", "Добавить");
define("NETCAT_MODERATION_BUTTON_CHANGE", "Сохранить изменения");
define("NETCAT_MODERATION_BUTTON_RESET", "Сброс");

define("NETCAT_MODERATION_COMMON_KILLALL", "Удалить объекты");
define("NETCAT_MODERATION_COMMON_KILLONE", "Удалить объект");

define("NETCAT_MODERATION_MULTIFILE_SIZE", "В поле «%NAME» загружены файлы с размером, превышающим допустимый (%SIZE)");
define("NETCAT_MODERATION_MULTIFILE_TYPE", "В поле «%NAME» загружены файлы недопустимого типа");
define("NETCAT_MODERATION_MULTIFILE_MIN_COUNT", "В поле «%NAME» должно быть загружено не менее %FILES.");
define("NETCAT_MODERATION_MULTIFILE_MAX_COUNT", "В поле «%NAME» может быть загружено не более %FILES.");
define("NETCAT_MODERATION_MULTIFILE_COUNT_FILES", "файла,файлов,файлов");
define("NETCAT_MODERATION_MULTIFILE_DEFAULT_CUSTOM_NAME_CAPTION", "описание файла");
define("NETCAT_MODERATION_ADD", "добавить еще");

define("NETCAT_MODERATION_MSG_ONE", "Поле «%NAME» является обязательным для заполнения.");
define("NETCAT_MODERATION_MSG_TWO", "В поле «%NAME» введено значение недопустимого типа.");
define("NETCAT_MODERATION_MSG_SIX", "Необходимо закачать файл «%NAME».");
define("NETCAT_MODERATION_MSG_SEVEN", "Файл «%NAME» превышает допустимый размер.");
define("NETCAT_MODERATION_MSG_EIGHT", "Недопустимый формат файла «%NAME».");
define("NETCAT_MODERATION_MSG_TWENTYONE", "Введено недопустимое ключевое слово.");
define("NETCAT_MODERATION_MSG_RETRYPASS", "Введенные пароли не совпадают");
define("NETCAT_MODERATION_MSG_PASSMIN", "Пароль слишком короткий. Минимальная длина пароля %s символов.");
define("NETCAT_MODERATION_MSG_NEED_AGREED", "Необходимо согласиться с пользовательским соглашением");
define("NETCAT_MODERATION_MSG_LOGINALREADY", "Логин %s занят другим пользователем");
define("NETCAT_MODERATION_MSG_LOGININCORRECT", "Логин содержит запрещенные символы");
define("NETCAT_MODERATION_BACKTOSECTION", "Вернуться в раздел");

define("NETCAT_MODERATION_ISON", "Включен");
define("NETCAT_MODERATION_ISOFF", "Выключен");
define("NETCAT_MODERATION_OBJISON", "Объект включен");
define("NETCAT_MODERATION_OBJISOFF", "Объект выключен");
define("NETCAT_MODERATION_OBJSAREON", "Объекты включены");
define("NETCAT_MODERATION_OBJSAREOFF", "Объекты выключены");
define("NETCAT_MODERATION_CHANGED", "ID изменившего пользователя");
define("NETCAT_MODERATION_CHANGE", "Изменить");
define("NETCAT_MODERATION_DELETE", "Удалить");
define("NETCAT_MODERATION_TURNTOON", "Включить");
define("NETCAT_MODERATION_TURNTOOFF", "Выключить");
define("NETCAT_MODERATION_ID", "Идентификатор");
define("NETCAT_MODERATION_COPY_OBJECT", "Копировать / перенести");

define("NETCAT_MODERATION_REMALL", "Удалить все");
define("NETCAT_MODERATION_DELETESELECTED", "Удалить выбранные");
define("NETCAT_MODERATION_SELECTEDON", "Включить выбранные");
define("NETCAT_MODERATION_SELECTEDOFF", "Выключить выбранные");
define("NETCAT_MODERATION_NOTSELECTEDOBJ", "Не выбрано ни одного объекта");
define("NETCAT_MODERATION_APPLY_CHANGES_TITLE", "Применить изменения?");
define("NETCAT_MODERATION_APPLY_CHANGES_TEXT", "Вы действительно хотите применить изменения?");
define("NETCAT_MODERATION_CLASSID", "Номер компонента раздела");
define("NETCAT_MODERATION_ADDEDON", "ID добавившего пользователя");

define("NETCAT_MODERATION_MOD_NOANSWER", "не важно");
define("NETCAT_MODERATION_MOD_DON", " до ");
define("NETCAT_MODERATION_MOD_FROM", " от ");
define("NETCAT_MODERATION_MODA", "--------- Не важно ---------");

define("NETCAT_MODERATION_FILTER", "Фильтр");
define("NETCAT_MODERATION_TITLE", "Заголовок");
define("NETCAT_MODERATION_DESCRIPTION", "Описание");

define("NETCAT_MODERATION_TRASHED_OBJECTS", "Удаленные объекты");
define("NETCAT_MODERATION_TRASHED_OBJECTS_RESTORE", "Восстановить объект");

define("NETCAT_MODERATION_NO_RELATED", "[нет]");
define("NETCAT_MODERATION_RELATED_INEXISTENT", "[несуществующий объект ID=%s]");
define("NETCAT_MODERATION_CHANGE_RELATED", "изменить");
define("NETCAT_MODERATION_REMOVE_RELATED", "удалить");
define("NETCAT_MODERATION_SELECT_RELATED", "выбрать");
define("NETCAT_MODERATION_COPY_HERE_RELATED", "Копировать сюда");
define("NETCAT_MODERATION_MOVE_HERE_RELATED", "Переместить сюда");
define("NETCAT_MODERATION_CONFIRM_COPY_RELATED", "Подтвердите действие");
define("NETCAT_MODERATION_RELATED_POPUP_TITLE", "Выбор связанного объекта (поле &quot;%s&quot;)");
define("NETCAT_MODERATION_RELATED_NO_CONCRETE_CLASS_IN_SUB", "В данном разделе нет инфоблоков &laquo;%s&raquo;.");
define("NETCAT_MODERATION_RELATED_NO_ANY_CLASS_IN_SUB", "В данном разделе нет ни одного подходящего инфоблока.");
define("NETCAT_MODERATION_RELATED_ERROR_SAVING", "Не удалось сохранить выбранное значение (возможно, форма редактирования основного объекта была закрыта). Попробуйте выбрать связанное значение еще раз.");
define("NETCAT_MODERATION_COPY_SUCCESS", "Копирование объекта успешно завершено");
define("NETCAT_MODERATION_MOVE_SUCCESS", "Перемещение объекта успешно завершено");


define("NETCAT_MODERATION_SEO_TITLE", "Заголовок страницы (Title)");
define("NETCAT_MODERATION_SEO_KEYWORDS", "Ключевые слова для поисковиков");
define("NETCAT_MODERATION_SEO_DESCRIPTION", "Описание страницы для поисковиков");

define("NETCAT_MODERATION_SMO_TITLE", "Заголовок для социальных сетей");
define("NETCAT_MODERATION_SMO_TITLE_HELPER", "Станет заголовком статьи при размещении ссылки на страницу в фейсбуке или вконтакте");
define("NETCAT_MODERATION_SMO_DESCRIPTION", "Описание для социальных сетей");
define("NETCAT_MODERATION_SMO_DESCRIPTION_HELPER", "Станет текстом статьи при размещении ссылки на страницу в фейсбуке или вконтакте");
define("NETCAT_MODERATION_SMO_IMAGE", "Изображение для социальных сетей");

define("NETCAT_MODERATION_STANDART_FIELD_USER_ID", "ID пользователя");
define("NETCAT_MODERATION_STANDART_FIELD_USER", "Пользователь");
define("NETCAT_MODERATION_STANDART_FIELD_PRIORITY", "Приоритет");
define("NETCAT_MODERATION_STANDART_FIELD_KEYWORD", "Ключевое слово");
define("NETCAT_MODERATION_STANDART_FIELD_NC_TITLE", "SEO Meta Title");
define("NETCAT_MODERATION_STANDART_FIELD_NC_KEYWORDS", "SEO Meta Keywords");
define("NETCAT_MODERATION_STANDART_FIELD_NC_DESCRIPTION", "SEO Meta Description");
define("NETCAT_MODERATION_STANDART_FIELD_NC_SMO_TITLE", "SMO Meta Title");
define("NETCAT_MODERATION_STANDART_FIELD_NC_SMO_DESCRIPTION", "SMO Meta Description");
define("NETCAT_MODERATION_STANDART_FIELD_NC_SMO_IMAGE", "SMO Meta Image");
define("NETCAT_MODERATION_STANDART_FIELD_IP", "IP");
define("NETCAT_MODERATION_STANDART_FIELD_USER_AGENT", "Браузер");
define("NETCAT_MODERATION_STANDART_FIELD_CREATED", "Создан");
define("NETCAT_MODERATION_STANDART_FIELD_LAST_UPDATED", "Обновлен");
define("NETCAT_MODERATION_STANDART_FIELD_LAST_USER_ID", "Посл. ID пользователя");
define("NETCAT_MODERATION_STANDART_FIELD_LAST_USER", "Посл. пользователь");
define("NETCAT_MODERATION_STANDART_FIELD_LAST_IP", "Посл. IP");
define("NETCAT_MODERATION_STANDART_FIELD_LAST_USER_AGENT", "Посл. браузер");

define("NETCAT_MODERATION_VERSION", "черновик");
define("NETCAT_MODERATION_VERSION_NOT_FOUND", "черновик отсутствует");
define("NETCAT_SAVE_DRAFT", "Сохранить черновик");

# MODULE
define("NETCAT_MODULES", "Модули");
define("NETCAT_MODULES_TUNING", "Настройка модуля");
define("NETCAT_MODULES_PARAM", "Параметр");
define("NETCAT_MODULES_VALUE", "Значение");
define("NETCAT_MODULES_ADDPARAM", "Добавить параметр");
define("NETCAT_MODULE_INSTALLCOMPLIED", "Установка модуля завершена.");
define("NETCAT_MODULE_ALWAYS_LOAD", "Загружать всегда");
define("NETCAT_MODULE_ONOFF", "Вкл/выкл");
define("NETCAT_MODULE_MODULE_UNCHECKED", "Модуль выключен, его настройка невозможна. Включить модуль можно в <a href='".$ADMIN_PATH."modules/index.php'>списке модулей.</a>");

# MODULE DEFAULT
define("NETCAT_MODULE_DEFAULT_DESCRIPTION", "Данный модуль предназначен для хранения вспомогательных скриптов и функций. Вы можете дописывать собственные функции в ".$SUB_FOLDER.$HTTP_ROOT_PATH."modules/default/function.inc.php и создавать собственные скрипты, интегрированные с системой по аналогии с ".$SUB_FOLDER.$HTTP_ROOT_PATH."modules/default/index.php. Также, вы можете задавать переменные окружения данного модуля в расположенном ниже поле.<br><br>Инструкции по созданию собственных модулей вы сможете найти в &quot;Руководстве разработчика&quot; в разделе &quot;Разработка модулей&quot;.");

#CODE MIRROR
define('NETCAT_SETTINGS_CODEMIRROR', 'Подсветка синтаксиса');
define('NETCAT_SETTINGS_CODEMIRROR_EMBEDED', 'Встроена');
define('NETCAT_SETTINGS_CODEMIRROR_EMBEDED_ON', 'Да');
define('NETCAT_SETTINGS_CODEMIRROR_DEFAULT', 'Подсветка по умолчанию');
define('NETCAT_SETTINGS_CODEMIRROR_DEFAULT_ON', 'Да');
define('NETCAT_SETTINGS_CODEMIRROR_AUTOCOMPLETE', 'Автодополнение');
define('NETCAT_SETTINGS_CODEMIRROR_AUTOCOMPLETE_ON', 'Да');
define('NETCAT_SETTINGS_CODEMIRROR_HELP', 'Окно подсказки');
define('NETCAT_SETTINGS_CODEMIRROR_HELP_ON', 'Да');
define('NETCAT_SETTINGS_CODEMIRROR_ENABLE', 'Включить редактор');
define('NETCAT_SETTINGS_CODEMIRROR_SWITCH', 'Переключить редактор');
define('NETCAT_SETTINGS_CODEMIRROR_WRAP', 'Переносить строки');
define('NETCAT_SETTINGS_CODEMIRROR_FULLSCREEN', 'На весь экран');

define('NETCAT_SETTINGS_DRAG', 'Перетаскивание объектов (разделов, инфоблоков, объектов, компонентов и т. д.)');
define('NETCAT_SETTINGS_DRAG_SILENT', 'переносить без подтверждения');
define('NETCAT_SETTINGS_DRAG_CONFIRM', 'спрашивать подтверждение перед переносом');
define('NETCAT_SETTINGS_DRAG_DISABLED', 'запретить перетаскивание');

# EDITOR
define('NETCAT_SETTINGS_EDITOR', 'Функции редактирования');
define('NETCAT_SETTINGS_EDITOR_TYPE', 'Тип HTML-редактора');
define('NETCAT_SETTINGS_EDITOR_FCKEDITOR', 'FCKeditor');
define('NETCAT_SETTINGS_EDITOR_CKEDITOR', 'CKEditor');
define('NETCAT_SETTINGS_EDITOR_TINYMCE', 'TinyMCE');
define('NETCAT_SETTINGS_EDITOR_CKEDITOR_FILE_SYSTEM', 'Разделять закачиваемые файлы по личным папкам пользователей (CKEditor)');
define('NETCAT_SETTINGS_EDITOR_CKEDITOR_CYRILIC_FOLDER', 'Разрешать символы кириллицы в именах папок файлового менеджера (CKEditor)');
define('NETCAT_SETTINGS_EDITOR_CKEDITOR_CONTENT_FILTER', 'Включить <a href="http://docs.ckeditor.com/#!/guide/dev_advanced_content_filter" target="_blank">фильтрацию контента</a> (CKEditor)');
define('NETCAT_SETTINGS_EDITOR_EMBED_ON', 'Да');
define('NETCAT_SETTINGS_EDITOR_EMBED_TO_FIELD', 'Встроить редактор в поле для редактирования');
define('NETCAT_SETTINGS_EDITOR_SEND', 'Отправить');
define('NETCAT_SETTINGS_EDITOR_STYLES_SAVE', 'Сохранить изменения');
define('NETCAT_SETTINGS_EDITOR_STYLES', 'Набор стилей для FCKeditor');
define('NETCAT_SETTINGS_EDITOR_SKINS', 'Оформление');
define('NETCAT_SETTINGS_EDITOR_ENTER_MODE', 'Режим клавиши Enter');
define('NETCAT_SETTINGS_EDITOR_ENTER_MODE_P', 'Тег P');
define('NETCAT_SETTINGS_EDITOR_ENTER_MODE_BR', 'Тег BR');
define('NETCAT_SETTINGS_EDITOR_ENTER_MODE_DIV', 'Тег DIV');
define('NETCAT_SETTINGS_EDITOR_SAVE', 'Настройки успешно изменены');
define('NETCAT_SETTINGS_EDITOR_KEYCODE', 'Сохранение данных по Ctrl + %s, требуется обновление страницы Ctrl + F5');

define('NETCAT_SEARCH_FIND_IT', 'Искать');
define('NETCAT_SEARCH_ERROR', 'Невозможен поиск по данному компоненту.');

# JS settings
define('NETCAT_SETTINGS_JS', 'Менеджер загрузки скриптов');
define('NETCAT_SETTINGS_JS_FUNC_NC_JS', 'Функция nc_js()');
define('NETCAT_SETTINGS_JS_LOAD_JQUERY_DOLLAR', 'Загружать jQuery объект $');
define('NETCAT_SETTINGS_JS_LOAD_JQUERY_EXTENSIONS_ALWAYS', 'Всегда загружать расширения jQuery');
define('NETCAT_SETTINGS_JS_LOAD_MODULES_SCRIPTS', 'Загружать модульные скрипты');
define('NETCAT_SETTINGS_MINIFY_STATIC_FILES', 'Минифицировать CSS и JS файлы в админ-панели');

define('NETCAT_SETTINGS_TRASHBIN', 'Корзина удаленных объектов');
define('NETCAT_SETTINGS_TRASHBIN_USE', 'Использовать корзину');

#Components
define('NETCAT_SETTINGS_COMPONENTS', 'Компоненты');
define('NETCAT_SETTINGS_REMIND_SAVE', 'Напоминать о сохранении (требуется обновление страницы Ctrl + F5)');
define('NETCAT_SETTINGS_PACKET_OPERATIONS', 'Включить групповые действия над объектами');
define('NETCAT_SETTINGS_TEXTAREA_RESIZE', 'Включить возможность изменить размер текстового поля при редактировании компонента');

define('NETCAT_SETTINGS_QUICKBAR', 'Панель быстрого редактирования');
define('NETCAT_SETTINGS_QUICKBAR_ENABLE', 'Включить уполномоченным в системе');
define('NETCAT_SETTINGS_QUICKBAR_ON', 'Да');

# ALT ADMIN BLOCKS
define('NETCAT_SETTINGS_ALTBLOCKS', 'Альтернативные блоки администрирования');
define('NETCAT_SETTINGS_ALTBLOCKS_ON', 'Да');
define('NETCAT_SETTINGS_ALTBLOCKS_TEXT', 'Использовать альтернативные блоки администрирования');
define('NETCAT_SETTINGS_ALTBLOCKS_PARAMS', 'Дополнительные параметры при удалении (начните с &)');

define('NETCAT_SETTINGS_HTTP_PROXY', 'Использовать HTTP-прокси-сервер для доступа к серверу обновлений');
define('NETCAT_SETTINGS_HTTP_PROXY_HOST', 'IP-адрес прокси-сервера');
define('NETCAT_SETTINGS_HTTP_PROXY_PORT', 'Порт');
define('NETCAT_SETTINGS_HTTP_PROXY_USER', 'Пользователь');
define('NETCAT_SETTINGS_HTTP_PROXY_PASSWORD', 'Пароль');

define('NETCAT_SETTINGS_USETOKEN', 'Использование ключа подтверждения операций');
define('NETCAT_SETTINGS_USETOKEN_ADD', 'при добавлении');
define('NETCAT_SETTINGS_USETOKEN_EDIT', 'при изменении');
define('NETCAT_SETTINGS_USETOKEN_DROP', 'при удалении');
define('NETCAT_SETTINGS_OBJECTS_FULLINK', 'Полное отображение объектов');
define("CONTROL_SETTINGSFILE_CHANGE_EMAILS_FIELD", "Поле (с форматом email) в таблице пользователей");
define("CONTROL_SETTINGSFILE_CHANGE_EMAILS_NONE", "Поле отсутствует");
define('NETCAT_SETTINGS_CODEMIRROR_EMBEDED_OFF', 'Нет');
define('NETCAT_SETTINGS_CODEMIRROR_DEFAULT_OFF', 'Нет');
define('NETCAT_SETTINGS_CODEMIRROR_AUTOCOMPLETE_OFF', 'Нет');
define('NETCAT_SETTINGS_CODEMIRROR_HELP_OFF', 'Нет');
define('NETCAT_SETTINGS_INLINE_EDIT_CONFIRMATION', 'Спрашивать подтверждение сохранения inline-изменений');
define('NETCAT_SETTINGS_INLINE_EDIT_CONFIRMATION_ON', 'Подтверждение сохранения inline-изменений включено');
define('NETCAT_SETTINGS_INLINE_EDIT_CONFIRMATION_OFF', 'Подтверждение сохранения inline-изменений отключено');
define('NETCAT_SETTINGS_EDITOR_EMBEDED', 'Редактор встроен в поле для редактирования');
define('NETCAT_SETTINGS_EDITOR_EMBED_OFF', 'Нет');
define('NETCAT_SETTINGS_EDITOR_STYLES_CANCEL', 'Отмена');
define('NETCAT_SETTINGS_TRASHBIN_MAXSIZE', 'Максимальный размер корзины');
define('NETCAT_SETTINGS_REMIND_SAVE_INFO', 'Напоминать о необходимости сохранения');
define('NETCAT_SETTINGS_PACKET_OPERATIONS_INFO', 'Включить групповые действия над объектами');
define('NETCAT_SETTINGS_TEXTAREA_RESIZE_INFO', 'Включить возможность изменить размер текстового поля при редактировании компонента');
define('NETCAT_SETTINGS_QUICKBAR_OFF', 'Нет');
define('NETCAT_SETTINGS_ALTBLOCKS_OFF', 'Нет');

# Export / Import
define('NETCAT_IMPORT_FIELD', 'XML-файл импорта');

define('NETCAT_SITEINFO_LINK', 'SEO-анализ');

define('NETCAT_FILEUPLOAD_ERROR', 'Ошибка! У Вас нет прав на директорию %s на этом сервере.');


define("NETCAT_HTTP_REQUEST_SAVING", "Сохранение...");
define("NETCAT_HTTP_REQUEST_SAVED", "Изменения сохранены");
define("NETCAT_HTTP_REQUEST_ERROR", "Ошибка при сохранении");
define("NETCAT_HTTP_REQUEST_HINT", "Вы можете сохранить эту форму, нажав Ctrl + %s");

# Index page menu
define("SECTION_INDEX_MENU_TITLE", "Главное меню");
define("SECTION_INDEX_MENU_HOME", "главная");
define("SECTION_INDEX_MENU_SITE", "сайт");
define("SECTION_INDEX_MENU_DEVELOPMENT", "разработка");
define("SECTION_INDEX_MENU_TOOLS", "инструменты");
define("SECTION_INDEX_MENU_SETTINGS", "настройки");
define("SECTION_INDEX_MENU_HELP", "справка");

define("SECTION_INDEX_HELP_SUBMENU_HELP", "Справка NetCat");
define("SECTION_INDEX_HELP_SUBMENU_DOC", "Документация");
define("SECTION_INDEX_HELP_SUBMENU_HELPDESC", "Онлайн-поддержка");
define("SECTION_INDEX_HELP_SUBMENU_FORUM", "Форум");
define("SECTION_INDEX_HELP_SUBMENU_BASE", "База знаний");
define("SECTION_INDEX_HELP_SUBMENU_ABOUT", "О программе");

define("SECTION_INDEX_SITE_LIST", "Список сайтов");

define("SECTION_INDEX_WIZARD_SUBMENU_CLASS", "Мастер создания компонента");
define("SECTION_INDEX_WIZARD_SUBMENU_SITE", "Мастер создания сайта");

define("SECTION_INDEX_FAVORITE_ANOTHER_SUB", "Другой раздел...");
define("SECTION_INDEX_FAVORITE_ADD", "Добавить в это меню");
define("SECTION_INDEX_FAVORITE_LIST", "Редактировать это меню");
define("SECTION_INDEX_FAVORITE_SETTINGS", "Настройки");

define("SECTION_INDEX_WELCOME", "Добро пожаловать");
define("SECTION_INDEX_WELCOME_MESSAGE", "Здравствуйте, %s!<br />Вы находитесь в системе управления проектом &laquo;%s&raquo;.<br />Вам присвоены права: %s.");
define("SECTION_INDEX_TITLE", "Система управления NetCat");

# SITE
## TABS
define("SITE_TAB_SITEMAP", "Карта сайта");
define("SITE_TAB_SETTINGS", "Настройки");
define("SITE_TAB_SEO", "SEO-анализ");
define("SITE_TAB_STATS", "Статистика");
## TOOLBAR
define("SITE_TOOLBAR_INFO", "Общая информация");
define("SITE_TOOLBAR_SUBLIST", "Список разделов");


#SUBDIVISION
## TABS
## TOOLBAR
define("SUBDIVISION_TAB_INFO_TOOLBAR_INFO", "Информация о разделе");
define("SUBDIVISION_TAB_INFO_TOOLBAR_SUBLIST", "Список разделов");
define("SUBDIVISION_TAB_INFO_TOOLBAR_CCLIST", "Список инфоблоков");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST", "Пользователи");
define("SUBDIVISION_TAB_INFO_TOOLBAR_EDIT_EDIT", "Основные");
define("SUBDIVISION_TAB_INFO_TOOLBAR_EDIT_DESIGN", "Оформление");
define("SUBDIVISION_TAB_INFO_TOOLBAR_EDIT_SEO", "SEO/SMO");
define("SUBDIVISION_TAB_INFO_TOOLBAR_EDIT_SYSTEM", "Системные");
define("SUBDIVISION_TAB_INFO_TOOLBAR_EDIT_FIELDS", "Дополнительные настройки");


## BUTTONS
define("SUBDIVISION_TAB_PREVIEW_BUTTON_PREVIEW", "Просмотр в новом окне");

define("SITE_SITEMAP_SEARCH", "Поиск по карте сайта");
define("SITE_SITEMAP_SEARCH_NOT_FOUND", "Не найдено");

## TEXT
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_READ_ACCESS", "Доступ на чтение");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_COMMENT_ACCESS", "Доступ на комментирование");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_WRITE_ACCESS", "Доступ на запись");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_EDIT_ACCESS", "Доступ на редактирование");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_SUBSCRIBE_ACCESS", "Доступ в подписку");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_MODERATORS", "Модераторы");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_ADMINS", "Администраторы");

define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_ALL_USERS", "Все пользователи");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_REGISTERED_USERS", "Зарегистрированные пользователи");
define("SUBDIVISION_TAB_INFO_TOOLBAR_USERLIST_TEXT_PRIVILEGED_USERS", "Привилегированные пользователи");

# CLASS WIZARD

define("WIZARD_CLASS_FORM_SUBDIVISION_PARENTSUB", "Родительский раздел");

define("WIZARD_PARENTSUB_SELECT_POPUP_TITLE", "Выбор родительского раздела");

define("WIZARD_CLASS_FORM_SUBDIVISION_SELECT_PARENTSUB", "выбрать родительский раздел");
define("WIZARD_CLASS_FORM_SUBDIVISION_SELECT_SUBDIVISION", "выбрать раздел");
define("WIZARD_CLASS_FORM_SUBDIVISION_DELETE", "удалить");

define("WIZARD_CLASS_FORM_START_TEMPLATE_TYPE", "Тип шаблона");
define("WIZARD_CLASS_FORM_START_TEMPLATE_TYPE_SINGLE", "Единственный объект на странице");
define("WIZARD_CLASS_FORM_START_TEMPLATE_TYPE_LIST", "Список объектов");
define("WIZARD_CLASS_FORM_START_TEMPLATE_TYPE_SEARCH", "Поиск по списку объектов");
define("WIZARD_CLASS_FORM_START_TEMPLATE_TYPE_FORM", "Веб-форма");

define("WIZARD_CLASS_FORM_SETTINGS_NO_SETTINGS", "Для данного типа шаблона настроек не предусмотренно.");
define("WIZARD_CLASS_FORM_SETTINGS_FIELDS_FOR_OBJECT_LIST", "Поля для списка объектов");
define("WIZARD_CLASS_FORM_SETTINGS_SORT_OBJECT_BY_FIELD", "Сортировать объекты по полю");
define("WIZARD_CLASS_FORM_SETTINGS_SORT_ASC", "По возрастанию");
define("WIZARD_CLASS_FORM_SETTINGS_SORT_DESC", "По убыванию");
define("WIZARD_CLASS_FORM_SETTINGS_PAGE_NAVIGATION", "Навигация по страницам");
define("WIZARD_CLASS_FORM_SETTINGS_PAGE_NAVIGATION_BY_NEXT_PREV", "переход на другие страницы списка &laquo;следующий-предыдущий&raquo;");
define("WIZARD_CLASS_FORM_SETTINGS_PAGE_NAVIGATION_BY_PAGE_NUMBER", "по номерам страниц");
define("WIZARD_CLASS_FORM_SETTINGS_PAGE_NAVIGATION_BY_BOTH", "оба варианта");
define("WIZARD_CLASS_FORM_SETTINGS_LOCATION_OF_NAVIGATION", "Положение вывода навигации");
define("WIZARD_CLASS_FORM_SETTINGS_LOCATION_TOP", "Вверху страницы");
define("WIZARD_CLASS_FORM_SETTINGS_LOCATION_BOTTOM", "Внизу страницы");
define("WIZARD_CLASS_FORM_SETTINGS_LOCATION_BOTH", "оба варианта");
define("WIZARD_CLASS_FORM_SETTINGS_LIST_TYPE", "Список");
define("WIZARD_CLASS_FORM_SETTINGS_TABLE_TYPE", "Таблица");
define("WIZARD_CLASS_FORM_SETTINGS_LIST_DELIMITER_TYPE", "Тип разделителя");
define("WIZARD_CLASS_FORM_SETTINGS_TABLE_TYPE_SETTINGS", "Настройки типа таблицы");
define("WIZARD_CLASS_FORM_SETTINGS_TABLE_BACKGROUND", "Чередовать фон");
define("WIZARD_CLASS_FORM_SETTINGS_TABLE_BORDER", "Граница таблицы");
define("WIZARD_CLASS_FORM_SETTINGS_FULL_PAGE", "Страница с подробной информацией");
define("WIZARD_CLASS_FORM_SETTINGS_FULL_PAGE_LINK_TYPE", "Способ перехода на страницу отображения объекта");
define("WIZARD_CLASS_FORM_SETTINGS_CHECK_FIELDS_TO_FULL_PAGE", "Отметьте поля при нажатии на которые будет производиться переход на страницу отображения объекта");

define("WIZARD_CLASS_FORM_SETTINGS_FIELDS_FOR_OBJECT_SEARCH", "Поля, по которым будет производиться поиск");

define("WIZARD_CLASS_FORM_SETTINGS_FEEDBACK_FIELDS_SETTINGS", "Настройка полей обратной связи");
define("WIZARD_CLASS_FORM_SETTINGS_FEEDBACK_SENDER_NAME", "Имя отправителя");
define("WIZARD_CLASS_FORM_SETTINGS_FEEDBACK_SENDER_MAIL", "Email отправителя");
define("WIZARD_CLASS_FORM_SETTINGS_FEEDBACK_SUBJECT", "Тема письма");

## TABS
define("WIZARD_CLASS_TAB_SELECT_TEMPLATE_TYPE", "Выбор типа шаблона");
define("WIZARD_CLASS_TAB_ADDING_TEMPLATE_FIELDS", "Добавление полей шаблона");
define("WIZARD_CLASS_TAB_TEMPLATE_SETTINGS", "Настройка шаблона");
define("WIZARD_CLASS_TAB_SUBSEQUENT_ACTION", "Дальнейшее действие");
define("WIZARD_CLASS_TAB_CREATION_SUBDIVISION_WITH_NEW_TEMPLATE", "Создание раздела с новым шаблоном");
define("WIZARD_CLASS_TAB_ADDING_TEMPLATE_TO_EXISTENT_SUBDIVISION", "Добавление шаблона к существующему разделу");

## BUTTONS
define("WIZARD_CLASS_BUTTON_NEXT_TO_ADDING_FIELDS", "Перейти к добавлению полей");
define("WIZARD_CLASS_BUTTON_FINISH_ADDING_FIELDS", "Закончить добавление полей");
define("WIZARD_CLASS_BUTTON_SAVE_SETTINGS", "Сохранить настройки");
define("WIZARD_CLASS_BUTTON_ADDING_SUBDIVISION_WITH_NEW_TEMPLATE", "Добавить раздел с новым компонентом");
define("WIZARD_CLASS_BUTTON_CREATE_SUBDIVISION_WITH_NEW_TEMPLATE", "Создать раздел с новым компонентом");
define("WIZARD_CLASS_BUTTON_NEXT_TO_SUBDIVISION_SELECTION", "Перейти к выбору раздела");

## LINKS
define("WIZARD_CLASS_LINKS_VIEW_TEMPLATE_CODE", "Посмотреть программный код шаблона");
define("WIZARD_CLASS_LINKS_CREATE_SUBDIVISION_WITH_NEW_TEMPLATE", "Создать раздел с этим шаблоном");
define("WIZARD_CLASS_LINKS_ADD_TEMPLATE_TO_EXISTENT_SUBDIVISION", "Прикрепить шаблон к существующему разделу");
define("WIZARD_CLASS_LINKS_CREATE_NEW_TEMPLATE", "Создать новый шаблон");

define("WIZARD_CLASS_LINKS_RETURN_TO_FIELDS_ADDING", "Вернуться к добавлению полей");

## COMMON
define("WIZARD_CLASS_STEP", "Шаг");
define("WIZARD_CLASS_STEP_FROM", "Из");

define("WIZARD_CLASS_DEFAULT", "по умолчанию");

define("WIZARD_CLASS_ERROR_NO_FIELDS", "В шаблон необходимо добавить хотя бы одно поле!");

# SITE WIZARD
define("WIZARD_SITE_FORM_WHICH_MODULES", "Какие модули вы хотите задействовать на сайте?");

## TABS
define("WIZARD_SITE_TAB_NEW_SITE_CREATION", "Создание нового сайта");
define("WIZARD_SITE_TAB_NEW_SITE_ADD_SUB", "Добавление разделов сайта");

## BUTTONS
define("WIZARD_SITE_BUTTON_ADD_SUBS", "Добавить подразделы");
define("WIZARD_SITE_BUTTON_FINISH_ADD_SUBS", "Завершить");

## COMMON
define("WIZARD_SITE_STEP", "Шаг");
define("WIZARD_SITE_STEP_FROM", "Из");
define("WIZARD_SITE_STEP_TWO_DESCRIPTION", "Создание служебных разделов. Каждый сайт должен иметь титульную страницу и страницу 404. Можете оставить эти поля без изменений.");

#DEMO MODE
define("DEMO_MODE_ADMIN_INDEX_MESSAGE", "Сайт \"%s\" работает  демо-режиме, вы можете выключить его в <a href='%s'>системных настройках сайта</a>.");
define("DEMO_MODE_FRONT_INDEX_MESSAGE_GUEST", "Это не настоящий сайт, он предназначен только для демонстрации.");
define("DEMO_MODE_FRONT_INDEX_MESSAGE_ADMIN", "Сайт в демо-режиме, снять его можно <a href='%s'>в настройках</a>.");
define("DEMO_MODE_FRONT_INDEX_MESSAGE_CLOSE", "Закрыть");

# FAVORITE
## HEADER TEXT
define("FAVORITE_HEADERTEXT", "Избранные разделы");


# CRONTAB
##TABS
define("CRONTAB_TAB_LIST", "Список задач");
define("CRONTAB_TAB_ADD", "Добавление задачи");
define("CRONTAB_TAB_EDIT", "Редактирование задачи");

##TRASH
define("TRASH_TAB_LIST", "Корзина удаленных объектов");
define("TRASH_TAB_TITLE", "Список удаленных объектов");
define("TRASH_TAB_SETTINGS", "Настройки");

# REDIRECT
##TABS
define("REDIRECT_TAB_LIST", "Список переадресаций");
define("REDIRECT_TAB_ADD", "Добавление переадресации");
define("REDIRECT_TAB_EDIT", "Редактирование переадресации");


# SYSTEM SETTINGS
##TABS
define("SYSTEMSETTINGS_TAB_LIST", "Базовые настройки системы");
define("SYSTEMSETTINGS_TAB_ADD", "Редактирование базовых настроек");
define("SYSTEMSETTINGS_SAVE_OK", "Настройки системы сохранены");
define("SYSTEMSETTINGS_SAVE_ERROR", "Ошибка сохранения настроек системы");

# WYSIWYG SETTINGS
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TAB_SETTINGS", "Настройки");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TAB_PANELS", "Панели");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TITLE_SETTINGS", "Настройки WYSIWYG");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TITLE_PANELS", "Панели редактора WYSIWYG");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TITLE_DELETE_CONFIRMATION", "Подтверждение удаления панелей WYSIWYG редактора");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TITLE_EDIT_FORM", " - редактирование панели WYSIWYG");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_TITLE_ADD_FORM", "Добавление панели WYSIWYG");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANEL_NOT_EXISTS", "Такой панели не существует");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_NO_PANELS", "Нет ни одной панели");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANEL_EDIT_SUCCESSFUL", "Панель успешно изменена");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANEL_ADD_SUCCESSFUL", "Панель успешно добавлена");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANELS_NOT_SELECTED", "Не выбрано ни одной панели");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANELS_DELETED", "Панели успешно удалены");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_PANELS_DELETE_ERROR", "Ошибка при удалении панелей");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_FILL_PANEL_NAME", "Укажите имя панели");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_MESSAGE_SELECT_TOOLBAR", "Выберите хотя бы один тулбар");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_BUTTON_DELETE_SELECTED", "Удалить выбранные");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_BUTTON_CONFIRM_DELETE", "Подтвердить удаление");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_BUTTON_CANCEL", "Отмена");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_BUTTON_EDIT_PANEL", "Изменить панель");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_BUTTON_ADD_PANEL", "Добавить панель");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_PANEL_NAME", "Название панели");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_DELETE", "Удалить");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_ARE_YOU_REALLY_WANT_TO_DELETE_PANELS", "Вы действительно желаете удалить панели?");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_EDITOR_PANEL_FULL", "Панель полного редактирования");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_EDITOR_PANEL_INLINE", "Панель inline редактирования");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_PANEL_NAME", "Название панели");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_PANEL_PREVIEW", "Предпросмотр");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_SETTINGS", "Настройка панели инструментов");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_MODE", "Переключение типа документа");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_DOCUMENT", "Операции с документом");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_TOOLS", "Инструменты");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_DOCTOOLS", "Шаблоны");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_CLIPBOARD", "Буфер обмена");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_UNDO", "Отмена действий");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_FIND", "Поиск и замена");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_SELECTION", "Выделение");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_SPELLCHECKER", "Проверка орфографии");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_FORMS", "Формы");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_BASICSTYLES", "Базовые стили");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_CLEANUP", "Очистка форматирования");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_LIST", "Списки");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_INDENT", "Отступы");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_BLOCKS", "Блоки текста");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_ALIGN", "Выравнивание");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_LINKS", "Ссылки");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_INSERT", "Вставка объектов");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_STYLES", "Стили");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_COLORS", "Цвета");
define("NETCAT_WYSIWYG_CKEDITOR_SETTINGS_FIELD_TOOLBARS_NAME_OTHERS", "Другие инструменты");

define("NETCAT_WYSIWYG_FCKEDITOR_SETTINGS_TITLE_SETTINGS", "Настройки WYSIWYG");

define("NETCAT_WYSIWYG_SETTINGS_PANEL_SETTINGS", "Настройка панелей");
define("NETCAT_WYSIWYG_SETTINGS_THIS_EDITOR_IS_USED_BY_DEFAULT", "Этот редактор используется по умолчанию");
define("NETCAT_WYSIWYG_SETTINGS_USE_BY_DEFAULT", "Использовать этот редактор по умолчанию");
define("NETCAT_WYSIWYG_SETTINGS_BASIC_SETTINGS", "Основные настройки");
define("NETCAT_WYSIWYG_SETTINGS_MESSAGE_EDITOR_ACTIVATED", "Редактор успешно активирован");
define("NETCAT_WYSIWYG_SETTINGS_PANEL_NOT_SELECTED", "Не выбрана");
define("NETCAT_WYSIWYG_SETTINGS_BUTTON_SAVE", "Сохранить");
define("NETCAT_WYSIWYG_SETTINGS_MESSAGE_SETTINGS_SAVED", "Настройки WYSIWYG сохранены");
define("NETCAT_WYSIWYG_SETTINGS_MESSAGE_SETTINGS_SAVE_ERROR", "Произошла ошибка при сохранении WYSIWYG настроек");
define("NETCAT_WYSIWYG_SETTINGS_CONFIG_JS_SETTINGS", "Настройка config.js");
define("NETCAT_WYSIWYG_SETTINGS_CONFIG_JS_FILE", "Файл config.js");

define("NETCAT_WYSIWYG_EDITOR_OUTWORN", "Этот редактор устарел, рекомендуем переключится на другой редактор и удалить директорию %s с сервера");

# Not Elsewhere Specified
define("NOT_ELSEWHERE_SPECIFIED", "Не указывать");
define("NOT_ADD_CLASS", "Не добавлять инфоблок в раздел");


# BBcodes
define("NETCAT_BBCODE_SIZE", "Размер шрифта");
define("NETCAT_BBCODE_COLOR", "Цвет шрифта");
define("NETCAT_BBCODE_SMILE", "Смайлы");
define("NETCAT_BBCODE_B", "Жирный");
define("NETCAT_BBCODE_I", "Курсив");
define("NETCAT_BBCODE_U", "Подчёркнутый");
define("NETCAT_BBCODE_S", "Зачёркнутый");
define("NETCAT_BBCODE_LIST", "Элемент списка");
define("NETCAT_BBCODE_QUOTE", "Цитата");
define("NETCAT_BBCODE_CODE", "Код");
define("NETCAT_BBCODE_IMG", "Изображение");
define("NETCAT_BBCODE_URL", "Ссылка");
define("NETCAT_BBCODE_CUT", "Сократить текст");

define("NETCAT_BBCODE_QUOTE_USER", "писал(а)");
define("NETCAT_BBCODE_CUT_MORE", "подробнее");
define("NETCAT_BBCODE_SIZE_DEF", "размер");
define("NETCAT_BBCODE_ERROR_1", "введён BB-код недопустимого формата:");
define("NETCAT_BBCODE_ERROR_2", "введены BB-коды недопустимого формата:");

# Help messages for BBcode
define("NETCAT_BBCODE_HELP_SIZE", "Размер шрифта: [SIZE=8]маленький текст[/SIZE]");
define("NETCAT_BBCODE_HELP_COLOR", "Цвет шрифта: [COLOR=FF0000]текст[/COLOR]");
define("NETCAT_BBCODE_HELP_SMILE", "Вставить смайлик");
define("NETCAT_BBCODE_HELP_B", "Жирный шрифт: [B]текст[/B]");
define("NETCAT_BBCODE_HELP_I", "Наклонный шрифт: [I]текст[/I]");
define("NETCAT_BBCODE_HELP_U", "Подчёркнутый шрифт: [U]текст[/U]");
define("NETCAT_BBCODE_HELP_S", "Зачёркнутый шрифт: [S]текст[/S]");
define("NETCAT_BBCODE_HELP_LIST", "Элемент списка: [LIST]текст[/LIST]");
define("NETCAT_BBCODE_HELP_QUOTE", "Цитата: [QUOTE]текст[/QUOTE]");
define("NETCAT_BBCODE_HELP_CODE", "Код: [CODE]код[/CODE]");
define("NETCAT_BBCODE_HELP_IMG", "Вставить картинку");
define("NETCAT_BBCODE_HELP_IMG_URL", "Адрес картинки");
define("NETCAT_BBCODE_HELP_URL", "Вставить ссылку");
define("NETCAT_BBCODE_HELP_URL_URL", "Ссылка");
define("NETCAT_BBCODE_HELP_URL_DESC", "Описание");
define("NETCAT_BBCODE_HELP_CUT", "Сократить текст в листинге: [CUT=подробнее]текст[/CUT]");
define("NETCAT_BBCODE_HELP", "Подсказка: выше расположены кнопки быстрого форматирования");

# Smiles
define("NETCAT_SMILE_SMILE", "улыбка");
define("NETCAT_SMILE_BIGSMILE", "большая улыбка");
define("NETCAT_SMILE_GRIN", "усмешка");
define("NETCAT_SMILE_LAUGH", "смех");
define("NETCAT_SMILE_PROUD", "гордый");
#
define("NETCAT_SMILE_YES", "да");
define("NETCAT_SMILE_WINK", "подмигивает");
define("NETCAT_SMILE_COOL", "клево");
define("NETCAT_SMILE_ROLLEYES", "невинный");
define("NETCAT_SMILE_LOOKDOWN", "стыдно");
#
define("NETCAT_SMILE_SAD", "грустный");
define("NETCAT_SMILE_SUSPICIOUS", "подозрительный");
define("NETCAT_SMILE_ANGRY", "сердитый");
define("NETCAT_SMILE_SHAKEFIST", "грозится");
define("NETCAT_SMILE_STERN", "суровый");
#
define("NETCAT_SMILE_KISS", "поцелуй");
define("NETCAT_SMILE_THINK", "думает");
define("NETCAT_SMILE_THUMBSUP", "круто");
define("NETCAT_SMILE_SICK", "тошнит");
define("NETCAT_SMILE_NO", "нет");
#
define("NETCAT_SMILE_CANTLOOK", "не могу смотреть");
define("NETCAT_SMILE_DOH", "ооо");
define("NETCAT_SMILE_KNOCKEDOUT", "в ауте");
define("NETCAT_SMILE_EYEUP", "хммм");
define("NETCAT_SMILE_QUIET", "тихо");
#
define("NETCAT_SMILE_EVIL", "злой");
define("NETCAT_SMILE_UPSET", "огорчен");
define("NETCAT_SMILE_UNDECIDED", "неуверенный");
define("NETCAT_SMILE_CRY", "плачет");
define("NETCAT_SMILE_UNSURE", "не уверен");

# nc_bytes2size
define("NETCAT_SIZE_BYTES", " байт");
define("NETCAT_SIZE_KBYTES", " КБ");
define("NETCAT_SIZE_MBYTES", " МБ");
define("NETCAT_SIZE_GBYTES", " ГБ");

// quickBar
define("NETCAT_QUICKBAR_BUTTON_VIEWMODE", "просмотр");
define("NETCAT_QUICKBAR_BUTTON_EDITMODE", "редактирование");
define("NETCAT_QUICKBAR_BUTTON_EDITMODE_UNAVAILABLE_FOR_LONGPAGE", "Редактирование недоступно в режиме longpage");
define("NETCAT_QUICKBAR_BUTTON_MORE", "еще");
define("NETCAT_QUICKBAR_BUTTON_SUBDIVISION_SETTINGS", "Настройки страницы");
define("NETCAT_QUICKBAR_BUTTON_TEMPLATE_SETTINGS", "Макет дизайна");
define("NETCAT_QUICKBAR_BUTTON_ADMIN", "Администрирование");

#SYNTAX EDITOR
define('NETCAT_SETTINGS_SYNTAXEDITOR', 'Он-лайн подсветка синтаксиса');
define('NETCAT_SETTINGS_SYNTAXEDITOR_ENABLE', 'Включить использование подсветки синтаксиса в системе (требуется перезагрузка админки Ctrl+F5)');

#SYNTAX CHECK

# LICENSE
define('NETCAT_SETTINGS_LICENSE', 'Лицензия');
define('NETCAT_SETTINGS_LICENSE_PRODUCT', 'Код продукта');

# NETCAT_DEBUG
define("NETCAT_DEBUG_ERROR_INSTRING", "Ошибка в строке : ");
define("NETCAT_DEBUG_BUTTON_CAPTION", "Отладка");

# NETCAT_PREVIEW
define("NETCAT_PREVIEW_BUTTON_CAPTIONCLASS", "Предпросмотр компонента");
define("NETCAT_PREVIEW_BUTTON_CAPTIONTEMPLATE", "Предпросмотр макета");

define("NETCAT_PREVIEW_BUTTON_CAPTIONADDFORM", "Предпросмотр формы добавления");
define("NETCAT_PREVIEW_BUTTON_CAPTIONEDITFORM", "Предпросмотр формы редактирования");
define("NETCAT_PREVIEW_BUTTON_CAPTIONSEARCHFORM", "Предпросмотр формы поиска");

define("NETCAT_PREVIEW_ERROR_NODATA", "Ошибка! Не переданы данные для режима предпросмотра или данные устарели");
define("NETCAT_PREVIEW_ERROR_WRONGDATA", "Ошибка в данных для режима предпросмотра");
define("NETCAT_PREVIEW_ERROR_NOSUB", " Нет ни одного раздела с таким компонентом. Добавьте этот компонент в раздел и режим предпросмотра станет доступен.");
define("NETCAT_PREVIEW_ERROR_NOMESSAGE", " Нет ни одного объекта такого компонента. Добавьте хотя бы один объект такого компонента и режим предпросмотра станет доступен.");
define("NETCAT_PREVIEW_INFO_MORESUB", " Есть несколько разделов с таким компонентом. Выберите раздел для предпросмотра.");
define("NETCAT_PREVIEW_INFO_CHOOSESUB", " Выберите раздел для предпросмотра макета.");

# objects
define("NETCAT_FUNCTION_OBJECTS_LIST_SQL_ERROR_SUPERVISOR", "Ошибка SQL запроса в функции nc_objects_list(%s, %s, \"%s\"), %s");
define("NETCAT_FUNCTION_OBJECTS_LIST_SQL_ERROR_USER", "Ошибка в функции вывода объектов.");
define("NETCAT_FUNCTION_OBJECTS_LIST_CLASSIFICATOR_ERROR", "список \"%s\" не найден");
define("NETCAT_FUNCTION_OBJECTS_LIST_SQL_COLUMN_ERROR_UNKNOWN", "поле \"%s\" не найдено");
define("NETCAT_FUNCTION_OBJECTS_LIST_SQL_COLUMN_ERROR_CLAUSE", "поле \"%s\" не найдено в условии");
define("NETCAT_FUNCTION_OBJECTS_LIST_CC_ERROR", "Ошибочный параметр \$cc в функции nc_objects_list(XX, %s, \"...\")");
define("NETCAT_FUNCTION_LISTCLASSVARS_ERROR_SUPERVISOR", "Ошибочный параметр \$cc в функции ListClassVars(%s)");
define("NETCAT_FUNCTION_FULL_SQL_ERROR_USER", "Ошибка в функции полного отображения объекта.");

# events





// widgets events

define("NETCAT_TOKEN_INVALID", "Неверный ключ подтверждения");

// seo
define("NETCAT_MODULE_AUDITOR_TITLE", "SEO-анализ");
define("NETCAT_MODULE_AUDITOR_NO_URL", "Не указан URL сайта");
define("NETCAT_MODULE_AUDITOR_WRONG_URL", "Неправильный URL");
define("NETCAT_MODULE_AUDITOR_WAIT", "Идет получение данных...");
define("NETCAT_MODULE_AUDITOR_DONE", "Загрузка данных завершена.");
define("NETCAT_MODULE_AUDITOR_URL", "URL сайта");
define("NETCAT_MODULE_AUDITOR_GROUP_CY", "Индекс цитирования");
define("NETCAT_MODULE_AUDITOR_GROUP_INDEXED", "Количество проиндексированных страниц");
define("NETCAT_MODULE_AUDITOR_GROUP_LINKS", "Количество ссылок на сайт");
define("NETCAT_MODULE_AUDITOR_GROUP_CATALOGUE", "Наличие в каталогах");
define("NETCAT_MODULE_AUDITOR_GROUP_STAT", "Статистика за сегодня");
define("NETCAT_MODULE_AUDITOR_GO", "Проверить ");
define("NETCAT_MODULE_AUDITOR_STOP", "Остановить");
define("NETCAT_MODULE_AUDITOR_APORT", "Апорт");
define("NETCAT_MODULE_AUDITOR_SPYLOG", "SpyLog (хиты/хосты)");
define("NETCAT_MODULE_AUDITOR_YANDEX", "Яндекс (страниц / сайтов)");

// Подсказки в сплывающах окнах
define("NETCAT_HINT_COMPONENT_FIELD", "Поля компонента");
define("NETCAT_HINT_COMPONENT_SIZE", "Размер");
define("NETCAT_HINT_COMPONENT_TYPE", "Тип");
define("NETCAT_HINT_COMPONENT_ID", "Номер");
define("NETCAT_HINT_COMPONENT_DAY", "Числовое значение дня");
define("NETCAT_HINT_COMPONENT_MONTH", "Числовое значение месяца");
define("NETCAT_HINT_COMPONENT_YEAR", "Числовое значение года");
define("NETCAT_HINT_COMPONENT_HOUR", "Числовое значение часа");
define("NETCAT_HINT_COMPONENT_MINUTE", "Числовое значение минуты");
define("NETCAT_HINT_COMPONENT_SECONDS", "Числовое значение секунды");
define("NETCAT_HINT_OBJECT_PARAMS", "Переменные, содержащие свойства текущего объекта");
define("NETCAT_HINT_CREATED_DESC", "реквизиты  времени добавления объекта в формате &laquo;гггг-мм-дд чч:мм:сс&raquo;");
define("NETCAT_HINT_LASTUPDATED_DESC", "реквизиты времени последнего изменения объекта в формате &laquo;ггггммддччммсс&raquo;");
define("NETCAT_HINT_MESSAGE_ID", "номер (ID) объекта");
define("NETCAT_HINT_USER_ID", "номер (ID) пользователя, добавившего объект");
define("NETCAT_HINT_CHECKED", "включен или выключен объект (0/1)");
define("NETCAT_HINT_IP", "IP-адрес пользователя, добавившего объект");
define("NETCAT_HINT_USER_AGENT", "значение переменной \$HTTP_USER_AGENT для пользователя, добавившего объект");
define("NETCAT_HINT_LAST_USER_ID", "номер (ID) последнего пользователя, изменившего объект");
define("NETCAT_HINT_LAST_USER_IP", "IP-адрес последнего пользователя, изменившего объект");
define("NETCAT_HINT_LAST_USER_AGENT", "значение переменной \$HTTP_USER_AGENT для последнего пользователя, изменившего объект");
define("NETCAT_HINT_ADMIN_BUTTONS", "в режиме администрирования содержит блок статусной информации о записи и ссылки на действия для данной записи &laquo;изменить&raquo;, &laquo;удалить&raquo;, &laquo;включить/выключить&raquo; (только в поле &laquo;Объект в списке&raquo;)");
define("NETCAT_HINT_ADMIN_COMMONS", "в режиме администрирования содержит блок статусной информации о шаблоне и ссылку на добавление объекта в данный шаблон в раздле и удаление всех объектов из этого же шаблона (только в поле &laquo;Объект в списке&raquo;)");
define("NETCAT_HINT_FULL_LINK", "ссылка на макет полного вывода данной записи");
define("NETCAT_HINT_FULL_DATE_LINK", "ссылка на макет полного вывода с указанием даты в виде &laquo;.../2002/02/02/message_2.html&raquo; (устанавливается в случае если в шаблоне имеется поле типа &laquo;Дата и время&raquo; с форматом &laquo;event&raquo;, иначе значение переменной идентично значению \$fullLink)");
define("NETCAT_HINT_EDIT_LINK", "ссылка на редактирование объекта");
define("NETCAT_HINT_DELETE_LINK", "ссылка на удаление объекта");
define("NETCAT_HINT_DROP_LINK", "ссылка на удаление объекта без подтверждения");
define("NETCAT_HINT_CHECKED_LINK", "ссылка на включение/выключение объекта");
define("NETCAT_HINT_PREV_LINK", "ссылка на предыдущую страницу в листинге шаблона (если текущее положение в списке – его начало, то переменная пустая)");
define("NETCAT_HINT_NEXT_LINK", "ссылка на следующую страницу в листинге шаблона (если текущее положение в списке – его конец, то переменная пустая)");
define("NETCAT_HINT_ROW_NUM", "номер записи по порядку в списке на текущей странице");
define("NETCAT_HINT_REC_NUM", "максимальное количество записей, выводимых в списке");
define("NETCAT_HINT_TOT_ROWS", "общее количество записей в списке");
define("NETCAT_HINT_BEG_ROW", "номер записи (по порядку), с которой начинается листинг списка на данной странице");
define("NETCAT_HINT_END_ROW", "номер записи (по порядку), которой заканчивается листинг списка на данной странице");
define("NETCAT_HINT_ADMIN_MODE", "истинна, если пользователь находится в режиме администрирования");
define("NETCAT_HINT_SUB_HOST", "адрес текущего домена вида &laquo;www.example.com&raquo;");
define("NETCAT_HINT_SUB_LINK", "путь к текущему разделу вида &laquo;/about/pr/&raquo;");
define("NETCAT_HINT_CC_LINK", "ссылка для текущего компонента в разделе вида &laquo;news.html&raquo;");
define("NETCAT_HINT_CATALOGUE_ID", "Номер текущего каталога");
define("NETCAT_HINT_SUB_ID", "Номер текущего раздела");
define("NETCAT_HINT_CC_ID", "Номер текущего компонента в разделе");
define("NETCAT_HINT_CURRENT_CATALOGUE", "Содержат значения свойств текущего каталога");
define("NETCAT_HINT_CURRENT_SUB", "Содержат значения свойств текущего раздела");
define("NETCAT_HINT_CURRENT_CC", "Содержат значения свойств текущего компонента в разделе");
define("NETCAT_HINT_CURRENT_USER", "Содержат значения свойств текущего авторизованного пользователя.");
define("NETCAT_HINT_IS_EVEN", "Проверяет параметр на четность");
define("NETCAT_HINT_OPT", "Функция opt() выводит \$string в случае, если \$flag – истина");
define("NETCAT_HINT_OPT_CAES", "Функция opt_case() выводит \$string1 в случае, если \$flag истина, и \$string2, если \$flag ложь");
define("NETCAT_HINT_LIST_QUERY", "Функция предназначена для выполнения SQL-запроса \$sql_query. Для запроса типа SELECT (или для других случаев, придуманных разработчиком) используется \$output_template для вывода результатов выборки. \$output_template является необязательным параметром.<br>Последний параметр должен содержать вызов хэш-массива \$data, индексы которого соответствуют полям таблицы (знак доллара и двойные кавычки необходимо маскировать). \$divider служит для разделения результатов вывода.");
define("NETCAT_HINT_NC_LIST_SELECT", "Функция позволяет генерировать HTML списки из Списков NetCat");
define("NETCAT_HINT_NC_MESSAGE_LINK", "Функция позволяет получить относительный путь к объекту (без домена) по номеру (ID) этого объекта и номеру (ID) компонента, которому он принадлежит");
define("NETCAT_HINT_NC_FILE_PATH", "Функция позволяет получить путь к файлу, указанному в определенном поле, по номеру (ID) этого объекта и номеру (ID) компонента, которому он принадлежит");
define("NETCAT_HINT_BROWSE_MESSAGE", "Функция отображает блок навигации по страницам списка записей в шаблоне");
define("NETCAT_HINT_NC_OBJECTS_LIST", "Выводит содержимое компонента в разделе \$cc раздела \$sub с параметрами \$params в виде параметров, подающихся на скрипты в строке URL");
define("NETCAT_HINT_RTFM", "Все доступные переменные и функции можно посмотреть в руководстве разработчика.");
define("NETCAT_HINT_FUNCTION", "Функции.");
define("NETCAT_HINT_ARRAY", "Хэш-массивы");
define("NETCAT_HINT_VARS_IN_COMPONENT_SCOPE", "Переменные, доступные во всех полях");
define("NETCAT_HINT_VARS_IN_LIST_SCOPE", "Переменные, доступные в списке объектов шаблона");
define("NETCAT_HINT_FIELD_D", "ДД");
define("NETCAT_HINT_FIELD_M", "ММ");
define("NETCAT_HINT_FIELD_Y", "ГГГГ");
define("NETCAT_HINT_FIELD_H", "чч");
define("NETCAT_HINT_FIELD_MIN", "мм");
define("NETCAT_HINT_FIELD_S", "сс");

define("NETCAT_CUSTOM_ERROR_REQUIRED_INT", "необходимо ввести целое число.");
define("NETCAT_CUSTOM_ERROR_REQUIRED_FLOAT", "необходимо ввести число.");
define("NETCAT_CUSTOM_ERROR_MIN_VALUE", "минимально число для ввода: %s.");
define("NETCAT_CUSTOM_ERROR_MAX_VALUE", "максимальное число для ввода: %s.");
define("NETCAT_CUSTOM_PARAMETR_UPDATED", "Изменения успешны сохранены");
define("NETCAT_CUSTOM_PARAMETR_ADDED", "Параметр успешно добавлен");
define("NETCAT_CUSTOM_KEY", "ключ");
define("NETCAT_CUSTOM_VALUE", "значение");
define("NETCAT_CUSTOM_USETTINGS", "Пользовательские настройки");
define("NETCAT_CUSTOM_NONE_SETTINGS", "Нет пользовательских настроек.");
define("NETCAT_CUSTOM_ONCE_MAIN_SETTINGS", "Основные параметры");
define("NETCAT_CUSTOM_ONCE_FIELD_NAME", "Название поля");
define("NETCAT_CUSTOM_ONCE_FIELD_DESC", "Описание");
define("NETCAT_CUSTOM_ONCE_DEFAULT", "Значение по умолчанию (когда значение не указано)");
define("NETCAT_CUSTOM_ONCE_FIELD_INITIAL_VALUE_INFOBLOCK", "Начальное значение при создании инфоблока");
define("NETCAT_CUSTOM_ONCE_FIELD_INITIAL_VALUE_SUBDIVISION", "Начальное значение при создании раздела");
define("NETCAT_CUSTOM_ONCE_EXTEND", "Дополнительные параметры");
define("NETCAT_CUSTOM_ONCE_EXTEND_REGEXP", "Регулярное выражение для проверки");
define("NETCAT_CUSTOM_ONCE_EXTEND_ERROR", "Текст в случае несоответствия регулярному выражению");
define("NETCAT_CUSTOM_ONCE_EXTEND_SIZE_L", "Длина поля для ввода");
define("NETCAT_CUSTOM_ONCE_EXTEND_RESIZE_W", "Ширина для авторесайза");
define("NETCAT_CUSTOM_ONCE_EXTEND_RESIZE_H", "Высота для авторесайза");
define("NETCAT_CUSTOM_ONCE_EXTEND_VIZRED", "Разрешить редактирование в визуальном редакторе");
define("NETCAT_CUSTOM_ONCE_EXTEND_BR", "Перенос строки - &lt;br>");
define("NETCAT_CUSTOM_ONCE_EXTEND_SIZE_H", "Высота поля для ввода");
define("NETCAT_CUSTOM_ONCE_SAVE", "Сохранить");
define("NETCAT_CUSTOM_ONCE_ADD", "Добавить");
define("NETCAT_CUSTOM_ONCE_DROP", "Удалить");
define("NETCAT_CUSTOM_ONCE_DROP_SELECTED", "Удалить выбранные");
define("NETCAT_CUSTOM_ONCE_MANUAL_EDIT", "Редактировать вручную");
define("NETCAT_CUSTOM_ONCE_ERROR_FIELD_NAME", "Название поля должно содеражать только латинские буквы, цифры и знак подчеркивания");
define("NETCAT_CUSTOM_ONCE_ERROR_CAPTION", "Необходимо заполнить поле \"Описание\"");
define("NETCAT_CUSTOM_ONCE_ERROR_FIELD_EXISTS", "Такой параметр уже есть");
define("NETCAT_CUSTOM_ONCE_ERROR_QUERY", "Ошибка в sql-запросе");
define("NETCAT_CUSTOM_ONCE_ERROR_CLASSIFICATOR", "Классификатор %s не найден");
define("NETCAT_CUSTOM_ONCE_ERROR_CLASSIFICATOR_EMPTY", "Классификатор %s пуст");
define("NETCAT_CUSTOM_TYPE", "Тип");
define("NETCAT_CUSTOM_SUBTYPE", "Подтип");
define("NETCAT_CUSTOM_EX_MIN", "Минимальное значение");
define("NETCAT_CUSTOM_EX_MAX", "Максимальное значние");
define("NETCAT_CUSTOM_EX_QUERY", "SQL-запрос");
define("NETCAT_CUSTOM_EX_CLASSIFICATOR", "Список");
define("NETCAT_CUSTOM_EX_ELEMENTS", "Элементы");
define("NETCAT_CUSTOM_TYPENAME_STRING", "Строка");
define("NETCAT_CUSTOM_TYPENAME_TEXTAREA", "Текст");
define("NETCAT_CUSTOM_TYPENAME_CHECKBOX", "Логическая переменная");
define("NETCAT_CUSTOM_TYPENAME_SELECT", "Список");
define("NETCAT_CUSTOM_TYPENAME_SELECT_SQL", "Динамический");
define("NETCAT_CUSTOM_TYPENAME_SELECT_STATIC", "Статический");
define("NETCAT_CUSTOM_TYPENAME_SELECT_CLASSIFICATOR", "Классификатор");
define("NETCAT_CUSTOM_TYPENAME_DIVIDER", "Разделитель");
define("NETCAT_CUSTOM_TYPENAME_INT", "Целое число");
define("NETCAT_CUSTOM_TYPENAME_FLOAT", "Дробное число");
define("NETCAT_CUSTOM_TYPENAME_DATETIME", "Дата и время");
define("NETCAT_CUSTOM_TYPENAME_REL", "Связь с другой сущностью");
define("NETCAT_CUSTOM_TYPENAME_REL_SUB", "Связь с разделом");
define("NETCAT_CUSTOM_TYPENAME_REL_CC", "Связь с компонентом в разделе");
define("NETCAT_CUSTOM_TYPENAME_REL_USER", "Связь с пользователем");
define("NETCAT_CUSTOM_TYPENAME_REL_CLASS", "Связь с компонентом");
define("NETCAT_CUSTOM_TYPENAME_FILE", "Файл");
define("NETCAT_CUSTOM_TYPENAME_FILE_ANY", "Произвольный файл");
define("NETCAT_CUSTOM_TYPENAME_FILE_IMAGE", "Изображение");
define("NETCAT_CUSTOM_TYPENAME_COLOR", "Цвет");
define("NETCAT_CUSTOM_TYPENAME_COLOR_TRANSPARENT", "Без цвета");

#exceptions
define("NETCAT_EXCEPTION_CLASS_DOESNT_EXIST", "Компонент %s не найден");
# errors
define("NETCAT_ERROR_SQL", "Ошибка в SQL-запросе.<br/>Запрос:<br/>%s<br/>Ошибка:<br/>%s");
define("NETCAT_ERROR_DB_CONNECT", "Фатальная ошибка: невозможно получить настройки системы. Проверьте, правильно ли указаны параметра доступа к базе данных.");
define("NETCAT_ERROR_UNABLE_TO_DELETE_FILES", "Не удалось удалить файл или директорию %s");

#openstat

# admin notice
define("NETCAT_ADMIN_NOTICE_MORE", "Подробнее.");
define("NETCAT_ADMIN_NOTICE_PROLONG", "Продлить.");
define("NETCAT_ADMIN_NOTICE_LICENSE_ILLEGAL", "Данная копия NetCat не является лицензионной.");
define("NETCAT_ADMIN_NOTICE_LICENSE_MAYBE_ILLEGAL", "Возможно, у Вас используется нелицензионная копия NetCat.");
define("NETCAT_ADMIN_NOTICE_SECURITY_UPDATE_SYSTEM", "Вышло важное обновление безопасности системы.");
define("NETCAT_ADMIN_NOTICE_SUPPORT_EXPIRED", "Срок технической поддержки для лицензии %s истек.");
define("NETCAT_ADMIN_NOTICE_CRON", "Вы давно не использовали инструмент \"Управление задачами\". <a href='https://netcat.ru/developers/docs/system-tools/task-management/' target='_blank'>Что это?</a>");
define("NETCAT_ADMIN_NOTICE_RIGHTS", "Неверно выставлены права на директорию");
define("NETCAT_ADMIN_NOTICE_SAFE_MODE", "Включён режим php safe_mode. <a href='https://netcat.ru/adminhelp/safe-mode/' target='_blank'>Что это?</a>");
define('NETCAT_ADMIN_DOMDocument_NOT_FOUND', 'PHP расширение DOMDocument не найдено, работа корзины невозможна');
define('NETCAT_ADMIN_TRASH_OBJECT_HAS_BEEN_REMOVED', 'объект удален');
define('NETCAT_ADMIN_TRASH_OBJECTS_REMOVED', 'объектов удалено');
define('NETCAT_ADMIN_TRASH_OBJECT_IS_REMOVED', 'объекта удалено');
define('NETCAT_ADMIN_TRASH_TRASH_HAS_BEEN_SUCCESSFULLY_CLEARNED', 'Корзина была успешно очищена');

define('NETCAT_FILE_NOT_FOUND', 'Файл %s не найден');
define('NETCAT_DIR_NOT_FOUND', 'Директория %s не найдена');

define('NETCAT_TEMPLATE_FILE_NOT_FOUND', 'Файл шаблона не найден');
define('NETCAT_TEMPLATE_DIR_DELETE_ERROR', 'Нельзя удалить всю папку %s');
define('NETCAT_CAN_NOT_WRITE_FILE', "Не могу записать файл");
define('NETCAT_CAN_NOT_CREATE_FOLDER', 'Не могу создать папку для шаблона');


define('NETCAT_ADMIN_AUTH_PERM', 'Ваши права:');
define('NETCAT_ADMIN_AUTH_CHANGE_PASS', 'Изменить пароль');
define('NETCAT_ADMIN_AUTH_LOGOUT', 'Выход');

define("CONTROL_BUTTON_CANCEL", "Отмена");

define("NETCAT_MESSAGE_FORM_MAIN", "Основное");
define("NETCAT_MESSAGE_FORM_ADDITIONAL", "Дополнительно");
define("NETCAT_EVENT_ADDCATALOGUE", "Добавление сайта");
define("NETCAT_EVENT_ADDSUBDIVISION", "Добавление раздела");
define("NETCAT_EVENT_ADDSUBCLASS", "Добавление компонента в раздел");
define("NETCAT_EVENT_ADDCLASS", "Добавление компонента");
define("NETCAT_EVENT_ADDCLASSTEMPLATE", "Добавление шаблона компонента");
define("NETCAT_EVENT_ADDMESSAGE", "Добавление объекта");
define("NETCAT_EVENT_ADDSYSTEMTABLE", "Добавление системной таблицы");
define("NETCAT_EVENT_ADDTEMPLATE", "Добавление макета");
define("NETCAT_EVENT_ADDUSER", "Добавление пользователя");
define("NETCAT_EVENT_ADDCOMMENT", "Добавление комментария");
define("NETCAT_EVENT_UPDATECATALOGUE", "Редактирование сайта");
define("NETCAT_EVENT_UPDATESUBDIVISION", "Редактирование раздела");
define("NETCAT_EVENT_UPDATESUBCLASS", "Редактирование компонента в разделе");
define("NETCAT_EVENT_UPDATECLASS", "Редактирование компонента");
define("NETCAT_EVENT_UPDATECLASSTEMPLATE", "Редактирование шаблона компонента");
define("NETCAT_EVENT_UPDATEMESSAGE", "Редактирование объекта");
define("NETCAT_EVENT_UPDATESYSTEMTABLE", "Редактирование системной таблицы");
define("NETCAT_EVENT_UPDATETEMPLATE", "Редактирование макета");
define("NETCAT_EVENT_UPDATEUSER", "Редактирование пользователя");
define("NETCAT_EVENT_UPDATECOMMENT", "Редактирование комментария");
define("NETCAT_EVENT_DROPCATALOGUE", "Удаление сайта");
define("NETCAT_EVENT_DROPSUBDIVISION", "Удаление раздела");
define("NETCAT_EVENT_DROPSUBCLASS", "Удаление компонента в разделе");
define("NETCAT_EVENT_DROPCLASS", "Удаление компонента");
define("NETCAT_EVENT_DROPCLASSTEMPLATE", "Удаление шаблона компонента");
define("NETCAT_EVENT_DROPMESSAGE", "Удаление сообщения");
define("NETCAT_EVENT_DROPSYSTEMTABLE", "Удаление системной таблицы");
define("NETCAT_EVENT_DROPTEMPLATE", "Удаление макета");
define("NETCAT_EVENT_DROPUSER", "Удаление пользователя");
define("NETCAT_EVENT_DROPCOMMENT", "Удаление комментария");
define("NETCAT_EVENT_CHECKCOMMENT", "Включение комментария");
define("NETCAT_EVENT_UNCHECKCOMMENT", "Выключение комментария");
define("NETCAT_EVENT_CHECKMESSAGE", "Включение объекта");
define("NETCAT_EVENT_UNCHECKMESSAGE", "Выключение объекта");
define("NETCAT_EVENT_CHECKUSER", "Включение пользователя");
define("NETCAT_EVENT_UNCHECKUSER", "Выключение пользователя");
define("NETCAT_EVENT_CHECKSUBDIVISION", "Включение раздела");
define("NETCAT_EVENT_UNCHECKSUBDIVISION", "Выключение раздела");
define("NETCAT_EVENT_CHECKCATALOGUE", "Включение сайта");
define("NETCAT_EVENT_UNCHECKCATALOGUE", "Выключение сайта");
define("NETCAT_EVENT_CHECKSUBCLASS", "Включение компонента в разделе");
define("NETCAT_EVENT_UNCHECKSUBCLASS", "Выключение компонента в разделе");
define("NETCAT_EVENT_CHECKMODULE", "Включение модуля");
define("NETCAT_EVENT_UNCHECKMODULE", "Выключение модуля");
define("NETCAT_EVENT_AUTHORIZEUSER", "Авторизация пользователя");
define("NETCAT_EVENT_ADDWIDGETCLASS", "Добавление виджет-компонента");
define("NETCAT_EVENT_EDITWIDGETCLASS", "Редактирование виджет-компонента");
define("NETCAT_EVENT_DROPWIDGETCLASS", "Удаление виджет-компонента");
define("NETCAT_EVENT_ADDWIDGET", "Добавление виджета");
define("NETCAT_EVENT_EDITWIDGET", "Редактирование виджета");
define("NETCAT_EVENT_DROPWIDGET", "Удаление виджета");

define("NETCAT_EVENT_ADDCATALOGUEPREP", "Подготовка к добавлению сайта");
define("NETCAT_EVENT_ADDSUBDIVISIONPREP", "Подготовка к добавлению раздела");
define("NETCAT_EVENT_ADDSUBCLASSPREP", "Подготовка к добавлению компонента в раздел");
define("NETCAT_EVENT_ADDCLASSPREP", "Подготовка к добавлению компонента");
define("NETCAT_EVENT_ADDCLASSTEMPLATEPREP", "Подготовка к добавлению шаблона компонента");
define("NETCAT_EVENT_ADDMESSAGEPREP", "Подготовка к добавлению объекта");
define("NETCAT_EVENT_ADDSYSTEMTABLEPREP", "Подготовка к добавлению системной таблицы");
define("NETCAT_EVENT_ADDTEMPLATEPREP", "Подготовка к добавлению макета");
define("NETCAT_EVENT_ADDUSERPREP", "Подготовка к добавлению пользователя");
define("NETCAT_EVENT_ADDCOMMENTPREP", "Подготовка к добавлению комментария");
define("NETCAT_EVENT_UPDATECATALOGUEPREP", "Подготовка к редактированию сайта");
define("NETCAT_EVENT_UPDATESUBDIVISIONPREP", "Подготовка к редактированию раздела");
define("NETCAT_EVENT_UPDATESUBCLASSPREP", "Подготовка к редактированию компонента в разделе");
define("NETCAT_EVENT_UPDATECLASSPREP", "Подготовка к редактированию компонента");
define("NETCAT_EVENT_UPDATECLASSTEMPLATEPREP", "Подготовка к редактированию шаблона компонента");
define("NETCAT_EVENT_UPDATEMESSAGEPREP", "Подготовка к редактированию объекта");
define("NETCAT_EVENT_UPDATESYSTEMTABLEPREP", "Подготовка к редактированию системной таблицы");
define("NETCAT_EVENT_UPDATETEMPLATEPREP", "Подготовка к редактированию макета");
define("NETCAT_EVENT_UPDATEUSERPREP", "Подготовка к редактированию пользователя");
define("NETCAT_EVENT_UPDATECOMMENTPREP", "Подготовка к редактированию комментария");
define("NETCAT_EVENT_DROPCATALOGUEPREP", "Подготовка к удалению сайта");
define("NETCAT_EVENT_DROPSUBDIVISIONPREP", "Подготовка к удалению раздела");
define("NETCAT_EVENT_DROPSUBCLASSPREP", "Подготовка к удалению компонента в разделе");
define("NETCAT_EVENT_DROPCLASSPREP", "Подготовка к удалению компонента");
define("NETCAT_EVENT_DROPCLASSTEMPLATEPREP", "Подготовка к удалению шаблона компонента");
define("NETCAT_EVENT_DROPMESSAGEPREP", "Подготовка к удалению сообщения");
define("NETCAT_EVENT_DROPSYSTEMTABLEPREP", "Подготовка к удалению системной таблицы");
define("NETCAT_EVENT_DROPTEMPLATEPREP", "Подготовка к удалению макета");
define("NETCAT_EVENT_DROPUSERPREP", "Подготовка к удалению пользователя");
define("NETCAT_EVENT_DROPCOMMENTPREP", "Подготовка к удалению комментария");
define("NETCAT_EVENT_CHECKCOMMENTPREP", "Подготовка к включению комментария");
define("NETCAT_EVENT_UNCHECKCOMMENTPREP", "Подготовка к выключению комментария");
define("NETCAT_EVENT_CHECKMESSAGEPREP", "Подготовка к включению объекта");
define("NETCAT_EVENT_UNCHECKMESSAGEPREP", "Подготовка к выключению объекта");
define("NETCAT_EVENT_CHECKUSERPREP", "Подготовка к включению пользователя");
define("NETCAT_EVENT_UNCHECKUSERPREP", "Подготовка к выключению пользователя");
define("NETCAT_EVENT_CHECKSUBDIVISIONPREP", "Подготовка к включению раздела");
define("NETCAT_EVENT_UNCHECKSUBDIVISIONPREP", "Подготовка к выключению раздела");
define("NETCAT_EVENT_CHECKCATALOGUEPREP", "Подготовка к включению сайта");
define("NETCAT_EVENT_UNCHECKCATALOGUEPREP", "Подготовка к выключению сайта");
define("NETCAT_EVENT_CHECKSUBCLASSPREP", "Подготовка к включению компонента в разделе");
define("NETCAT_EVENT_UNCHECKSUBCLASSPREP", "Подготовка к выключению компонента в разделе");
define("NETCAT_EVENT_CHECKMODULEPREP", "Подготовка к включению модуля");
define("NETCAT_EVENT_UNCHECKMODULEPREP", "Подготовка к выключению модуля");
define("NETCAT_EVENT_AUTHORIZEUSERPREP", "Подготовка к авторизации пользователя");
define("NETCAT_EVENT_ADDWIDGETCLASSPREP", "Подготовка к добавлению виджет-компонента");
define("NETCAT_EVENT_EDITWIDGETCLASSPREP", "Подготовка к редактированию виджет-компонента");
define("NETCAT_EVENT_DROPWIDGETCLASSPREP", "Подготовка к удалению виджет-компонента");
define("NETCAT_EVENT_ADDWIDGETPREP", "Подготовка к добавлению виджета");
define("NETCAT_EVENT_EDITWIDGETPREP", "Подготовка к редактированию виджета");
define("NETCAT_EVENT_DROPWIDGETPREP", "Подготовка к удалению виджета");

define("TITLE_WEB", "Обычный шаблон");
define("TITLE_MOBILE", "Мобильный шаблон");
define("TITLE_RESPONSIVE", "Адаптивный шаблон");
define("TITLE_OLD", "Обычный шаблон v4");

define("TOOLS_PATCH_INSTALL_ONLINE_NOTIFY", "Перед установкой обновления настоятельно рекомендуется сделать резервную копию системы. Запустить процесс обновления?");
define("TOOLS_PATCH_INFO_NEW", "Опубликовано обновление");
define("TOOLS_PATCH_INFO_NONEW", "Обновлений не обнаружено.");
define("TOOLS_PATCH_BACKTOLIST", "Вернуться к списку установленных обновлений");
define("TOOLS_PATCH_INFO_INSTALL", "Установить обновление");
define("TOOLS_PATCH_INFO_SYSTEM_MESSAGE", "Добавлено новое системное сообщение, <a href='%LINK'>читать сообщение</a>.");
define("TOOLS_PATCH_ERROR_SET_FILEPERM_IN_HTTP_ROOT_PATH", "Установите права на запись для ВСЕХ файлов в папке $HTTP_ROOT_PATH.<br />(%FILE недоступен для записи)");
define("TOOLS_PATCH_ERROR_SET_DIRPERM_IN_HTTP_ROOT_PATH", "Установите права на запись для папки $HTTP_ROOT_PATH и всех поддиректорий.<br />(%DIR недоступна для записи)");
define("TOOLS_PATCH_FOR_CP1251", "Патч для однобайтной версии NetCat, в то время, как у Вас используется utf-версия");
define("TOOLS_PATCH_FOR_UTF", "Патч для utf-версии NetCat, в то время, как у Вас однобайтная версия");
define("TOOLS_PATCH_ERROR_UNCORRECT_PHP_VERSION", "Для работы системы после обновления требуется версия PHP %NEED, текущая версия PHP %CURRENT.");
define("TOOLS_PATCH_INSTALEXT", "Установка патчей производится через внешний интерфейс");

define("SQL_CONSTRUCT_TITLE", "Конструктор запросов");
define("SQL_CONSTRUCT_CHOOSE_OP", "Выберите операцию");
define("SQL_CONSTRUCT_SELECT_TABLE", "Выбрать данные из таблицы");
define("SQL_CONSTRUCT_SELECT_CC", "Выбрать данные из компонента");
define("SQL_CONSTRUCT_ENTER_CODE", "Ввести код активации и номер лицензии");
define("SQL_CONSTRUCT_VIEW_SETTINGS", "Посмотреть настройки системы");
define("SQL_CONSTRUCT_TABLE_NAME", "Название таблицы");
define("SQL_CONSTRUCT_FIELDS", "Поля");
define("SQL_CONSTRUCT_FIELDS_NOTE", "(опционально, через запятую, без пробелов)");
define("SQL_CONSTRUCT_CC_ID", "ID компонента");
define("SQL_CONSTRUCT_REGNUM", "Номер лицензии");
define("SQL_CONSTRUCT_REGCODE", "Активационный код");
define("SQL_CONSTRUCT_CHOOSE_MOD", "Выберите модуль");
define("SQL_CONSTRUCT_GENERATE", "Сгенерировать запрос");

define("NETCAT_MAIL_ATTACHMENT_FORM_ATTACHMENTS", "Вложения:");
define("NETCAT_MAIL_ATTACHMENT_FORM_DELETE", "Удалить");
define("NETCAT_MAIL_ATTACHMENT_FORM_FILENAME", "Название файла:");
define("NETCAT_MAIL_ATTACHMENT_FORM_ADD", "Добавить еще");

define('NETCAT_DATEPICKER_CALENDAR_DATE_FORMAT', 'dd.mm.yyyy');
define('NETCAT_DATEPICKER_CALENDAR_DAYS', 'Воскресенье Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье');
define('NETCAT_DATEPICKER_CALENDAR_DAYS_SHORT', 'Вск Пнд Втр Срд Чтв Птн Суб Вск');
define('NETCAT_DATEPICKER_CALENDAR_DAYS_MIN', 'Вс Пн Вт Ср Чт Пт Сб Вс');
define('NETCAT_DATEPICKER_CALENDAR_MONTHS', 'Январь Февраль Март Апрель Май Июнь Июль Август Сентябрь Октябрь Ноябрь Декабрь');
define('NETCAT_DATEPICKER_CALENDAR_MONTHS_SHORT', 'Янв Фев Мар Апр Май Июн Июл Авг Сен Окт Ноя Дек');
define('NETCAT_DATEPICKER_CALENDAR_TODAY', 'Сегодня');

define('TOOLS_CSV', 'Экспорт/импорт CSV');
define('TOOLS_CSV_EXPORT', 'Экспорт CSV');
define('TOOLS_CSV_IMPORT', 'Импорт CSV');
define('TOOLS_CSV_EXPORT_TYPE', 'Тип экспорта');
define('TOOLS_CSV_EXPORT_TYPE_SUBCLASS', 'Из инфоблока');
define('TOOLS_CSV_EXPORT_TYPE_COMPONENT', 'Из компонента');
define('TOOLS_CSV_SELECT_SITE', 'Выберите сайт');
define('TOOLS_CSV_SELECT_SUBDIVISION', 'Выберите подраздел');
define('TOOLS_CSV_SELECT_SUBCLASS', 'Выберите инфоблок');
define('TOOLS_CSV_SELECT_COMPONENT', 'Выберите компонент');
define('TOOLS_CSV_SUBCLASSES_NOT_FOUND', 'Инфоблоков не найдено в разделе');
define('TOOLS_CSV_NOT_SELECTED', 'Не выбрано');
define('TOOLS_CSV_CREATE_EXPORT', 'Экспорт');
define('TOOLS_CSV_RECORD_ID', 'Идентификатор записи в файле');
define('TOOLS_CSV_PARENT_RECORD_ID', 'Идентификатор записи-родителя');

define('TOOLS_CSV_SELECT_SETTINGS', 'Параметры CSV');

define('TOOLS_CSV_OPT_ENCLOSED', 'Значения полей обрамлены');
define('TOOLS_CSV_OPT_ESCAPED', 'Символ экранирования');
define('TOOLS_CSV_OPT_SEPARATOR', 'Разделитель полей');
define('TOOLS_CSV_OPT_CHARSET', 'Кодировка');
define('TOOLS_CSV_OPT_CHARSET_UTF8', 'Юникод (utf-8)');
define('TOOLS_CSV_OPT_CHARSET_CP1251', 'Microsoft Excel (windows-1251)');
define('TOOLS_CSV_OPT_NULL', 'Заменить NULL на');
define('TOOLS_CSV_OPT_LISTS', '<nobr>Значения полей типа «Список»</nobr>');
define('TOOLS_CSV_OPT_LISTS_NAME', 'название элемента');
define('TOOLS_CSV_OPT_LISTS_VALUE', 'дополнительное значение (поле.Value)');
define('TOOLS_CSV_EXPORT_DONE', 'Экспорт завершен. Вы можете скачать файл по ссылке <a href="%s" target="_blank">%s</a>. Чтобы удалить файл, нажмите <a href="%s" target="_top">здесь</a>.');

define('TOOLS_CSV_MAPPING_HEADER', 'Соответствия полей');
define('TOOLS_CSV_IMPORT_FILE', 'Файл импорта (*.csv)');
define('TOOLS_CSV_IMPORT_RUN', 'Импортировать');
define('TOOLS_CSV_IMPORT_FILE_NOT_FOUND', 'Файл для импорта не найден');

define('TOOLS_CSV_COMPONENT_FIELD', 'Поле компонента');
define('TOOLS_CSV_FILE_FIELD', 'Поле CSV-файла');
define('TOOLS_CSV_FINISHED_HEADER', 'Импорт завершился');
define('TOOLS_CSV_EXPORT_FINISHED_HEADER', 'Экспорт завершился');
define('TOOLS_CSV_IMPORT_SUCCESS', 'Импорт завершился, импортировано записей: ');
define('TOOLS_CSV_DELETE_FINISHED_HEADER', 'Удаление файла');
define('TOOLS_CSV_DELETE_FINISHED', 'Файл удален. <a href="%s" target="_top">Нажмите, чтобы вернуться</a>');
define('TOOLS_CSV_IMPORT_HISTORY', 'История Импорта');
define('TOOLS_CSV_HISTORY_ID', 'ID');
define('TOOLS_CSV_HISTORY_CREATED', 'Создан');
define('TOOLS_CSV_HISTORY_ROLLBACK', 'Откатить');
define('TOOLS_CSV_HISTORY_EMPTY', 'История импорта пуста');
define('TOOLS_CSV_HISTORY_CLASS_NAME', 'Раздел');
define('TOOLS_CSV_HISTORY_ROWS', 'Записей');
define('TOOLS_CSV_HISTORY_ROLLBACKED', 'Отменено');
define('TOOLS_CSV_ROLLBACK_FINISHED_HEADER', 'Отмена импорта завершена');
define('TOOLS_CSV_ROLLBACK_SUCCESS', 'Отмена импорта завершена успешно. Отменено записей: ');


define('WELCOME_SCREEN_TOOLTIP_SUPPORT',      'В случае затруднений, можно обратиться к документации или получить ответ от техподдержки.');
define('WELCOME_SCREEN_TOOLTIP_SIDEBAR',      'Основные настройки сайта осуществляется через панель управления сайтом.');
define('WELCOME_SCREEN_TOOLTIP_SIDEBAR_SUBS', 'Когда вы <a href="#site.add">создадите сайт</a>, здесь будут показаны разделы, из которых он состоит. Черные повторяют структуру сайта, а серые несут служебный характер и напрямую не отображаются на сайте.');
define('WELCOME_SCREEN_TOOLTIP_TRASH_WIDGET', 'Для ускорения работы вы можете настраивать виджеты. Например, в «Корзине» можно восстановить удаленные объекты.');
define('WELCOME_SCREEN_MODAL_TEXT', '<h2>Добро пожаловать в систему управления сайтами NetCat!</h2>
    <p>Для вашего удобства мы собрали самые важные операции на отдельной странице — <b>панели управления сайтом.</b> Попасть на нее можно кликнув на название вашего сайта в «дереве» слева.</p>
    <p>Более глубокие настройки производятся в соответствующих разделах административного интерфейса.</p>
    <p>Большое спасибо за использование нашей системы и <b>удачи в работе.</b></p>');
define('WELCOME_SCREEN_BTN_START', 'Начать работу');

define('NETCAT_FILTER_FIELD_MESSAGE_ID', 'ID записи');
define('NETCAT_FILTER_FIELD_CREATED', 'Время создания');
define('NETCAT_FILTER_FIELD_LAST_UPDATED', 'Время редактирования');

define('NETCAT_FIELD_VALUE_INHERITED_FROM_SUBDIVISION', 'Значение поля наследуется из раздела «%s»');
define('NETCAT_FIELD_VALUE_INHERITED_FROM_CATALOGUE', 'Значение поля наследуется из <a href="%s" target="_top">свойств сайта</a>');
define('NETCAT_FIELD_VALUE_INHERITED_FROM_TEMPLATE', 'Значение поля наследуется из макета «%s»');

define('NETCAT_USER_BREAK_ATTRIBUTE_NAMING_CONVENTION', 'Некоторые из имен атрибутов нарушают <a href="https://www.w3.org/TR/html-markup/syntax.html#syntax-attributes" target="_blank">конвенцию именования</a> и были проигнорированы: %s.');

define('NETCAT_SECURITY_SETTINGS', 'Параметры защиты сайта');
define('NETCAT_SECURITY_SETTINGS_SAVE', 'Применить');
define('NETCAT_SECURITY_SETTINGS_SAVED', 'Параметры сохранены');
define('NETCAT_SECURITY_SETTINGS_USE_DEFAULT', 'использовать <a href="%s" target="_top">общие настройки для всех сайтов</a>');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER', 'Фильтр входящих данных');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE', 'Действие при обнаружении входящего параметра, используемого для&nbsp;инъекции');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE_DISABLED', 'отключено (не проверять входящие данные)');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE_LOG_ONLY', 'не выполнять действий на странице');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE_RELOAD_ESCAPE_INPUT', 'экранировать параметр и перезагрузить страницу');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE_RELOAD_REMOVE_INPUT', 'сбросить параметр и перезагрузить страницу');
define('NETCAT_SECURITY_SETTINGS_INPUT_FILTER_MODE_EXCEPTION', 'остановить выполнение скрипта');

define('NETCAT_SECURITY_FILTER_NO_TOKENIZER', 'Код PHP не будет проверяться, так как отключено расширение <i>tokenizer</i>.');
define('NETCAT_SECURITY_FILTER_EMAIL_ENABLED', 'Высылать письмо при срабатывании фильтра на электронную почту');
define('NETCAT_SECURITY_FILTER_EMAIL_PLACEHOLDER', 'Адрес электронной почты');
define('NETCAT_SECURITY_FILTER_EMAIL_SUBJECT', 'срабатывание фильтра входящих данных');
define('NETCAT_SECURITY_FILTER_EMAIL_PREFIX', 'На странице %s сработал фильтр входящих данных (%s).');
define('NETCAT_SECURITY_FILTER_EMAIL_INPUT_VALUE', 'Значение входящего параметра %s');
define('NETCAT_SECURITY_FILTER_EMAIL_CHECKED_STRING', 'Строка, в которой найдено неэкранированное значение');
define('NETCAT_SECURITY_FILTER_EMAIL_IP', 'IP-адрес, с которого выполнен запрос');
define('NETCAT_SECURITY_FILTER_EMAIL_URL', 'Адрес страницы');
define('NETCAT_SECURITY_FILTER_EMAIL_REFERER', 'Адрес ссылающейся страницы');
define('NETCAT_SECURITY_FILTER_EMAIL_GET', 'GET-параметры');
define('NETCAT_SECURITY_FILTER_EMAIL_POST', 'POST-параметры');
define('NETCAT_SECURITY_FILTER_EMAIL_BACKTRACE', 'Стек вызовов');
define('NETCAT_SECURITY_FILTER_EMAIL_SUFFIX', 'Рекомендуем незамедлительно предпринять меры для исправления данной проблемы, чтобы устранить угрозу взлома сайта через данную уязвимость.');
define('NETCAT_SECURITY_FILTERS_DISABLED', 'Фильтры входящих данных отключены.');

define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA', 'Защита формы входа в систему при помощи CAPTCHA');
define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA_RECOMMEND_DEFAULT', '(рекомендуем использовать одинаковые настройки на всех сайтах)');
define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA_MODE_DISABLED', 'отключена');
define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA_MODE_ALWAYS', 'показывать CAPTCHA всегда');
define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA_MODE_COUNT', 'показывать CAPTCHA после неправильного ввода логина или пароля');
define('NETCAT_SECURITY_SETTINGS_AUTH_CAPTCHA_ATTEMPTS', 'число попыток без CAPTCHA');
