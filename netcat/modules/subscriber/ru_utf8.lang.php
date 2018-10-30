<?php

/* $Id$ */

define("NETCAT_MODULE_SUBSCRIBES", "Подписки и рассылки");
define("NETCAT_MODULE_SUBSCRIBE_DESCRIPTION", "Возможность для зарегистрированных пользователей осуществлять подписку на обновления разделов.");

define("NETCAT_MODULE_SUBSCRIBE_ADM_GETUSERS", "Выборка (поиск)");
define("NETCAT_MODULE_SUBSCRIBE_ADM_ALLUSERS", "все");
define("NETCAT_MODULE_SUBSCRIBE_ADM_TURNEDON", "вкл.");
define("NETCAT_MODULE_SUBSCRIBE_ADM_TURNEDOFF", "выкл.");
define("NETCAT_MODULE_SUBSCRIBE_ADM_USERID", "По ID пользователя");
define("NETCAT_MODULE_SUBSCRIBE_ADM_CLASSID", "По ID шаблона в разделе");
define("NETCAT_MODULE_SUBSCRIBE_BUT_GETIT", "Выбрать");
define("NETCAT_MODULE_SUBSCRIBE_ADM_CLASSINSECTION", "Шаблон в разделе");
define("NETCAT_MODULE_SUBSCRIBE_ADM_USER", "Пользователь");
define("NETCAT_MODULE_SUBSCRIBE_ADM_STATUS", "Статус");
define("NETCAT_MODULE_SUBSCRIBE_ADM_DELETE", "Удалить");
define("NETCAT_MODULE_SUBSCRIBE_ADM_CANCEL", "Отменить");
define("NETCAT_MODULE_SUBSCRIBE_ADM_SAVE", "Удалить выбранное");
define("NETCAT_MODULE_SUBSCRIBE_MSG_NOSUBSCRIBER", "Не найдено ни одного подписчика соответствующего вашему запросу.");

define("NETCAT_MODULE_SUBSCRIBE_ADM_TURNON", "включить");
define("NETCAT_MODULE_SUBSCRIBE_ADM_TURNOFF", "выключить");
define("NETCAT_MODULE_SUBSCRIBE_ADM_TURNOFFCLR", "cccccc");

define("NETCAT_MODULE_SUBSCRIBE_ADM_ACCESSRIGHTS", "права доступа");
define("NETCAT_MODULE_SUBSCRIBE_ADM_PASSCHANGE", "Смена пароля");
define("NETCAT_MODULE_SUBSCRIBE_ADM_SECSITE", "Выбор каталога");
define("NETCAT_MODULE_SUBSCRIBE_ADM_SECSEL", "Выбор раздела");
define("NETCAT_MODULE_SUBSCRIBE_ADM_ADDING", "Добавление");
define("NETCAT_MODULE_SUBSCRIBE_ADM_SECTION", "раздел");
define("NETCAT_MODULE_SUBSCRIBE_SUCCESS", "Подписка осуществлена успешно.");

define("NETCAT_MODULE_SUBSCRIBER_ALREADY_SUBSCRIBE", "Пользователь на данную рассылку уже подписан.");
define("NETCAT_MODULE_SUBSCRIBER_CONFIRM_SENT_AGAIN", "Письмо с подтверждением подписки Вам выслано повторно.");
define("NETCAT_MODULE_SUBSCRIBER_MAILER_DOES_NOT_EXIST", "Рассылка не существует.");
define("NETCAT_MODULE_SUBSCRIBER_NOT_SUB_FOR_CC", "Нет раздела для компонента раздела");
define("NETCAT_MODULE_SUBSCRIBER_WRONG_EMAIL", "Некорректный E-mail");


//main
define("NETCAT_MODULE_SUBSCRIBER_MAILER_NAME", "Название рассылки");
define("NETCAT_MODULE_SUBJECT", "Тема письма");
define("NETCAT_MODULE_HTML_MAIL", "HTML-письмо");
define("NETCAT_MODULE_SUBSCRIBER_USER", "Пользователь");
define("NETCAT_MODULE_SUBSCRIBE_STATUS", "Статус");
define("NETCAT_MODULE_SUBSCRIBE_MAILER", "Рассылка");

// settings
define("NETCAT_MODULE_SUBSCRIBER_MAILER_SETTINGS", "Настройки рассылок");
define("NETCAT_MODULE_SUBSCRIBER_MERGE_MAIL", "Разрешить объединение писем из различных разделов");
define("NETCAT_MODULE_SUBSCRIBER_MAX_MAIL", "Максимальное количество писем, отправляемых за один запуск скрипта");
define("NETCAT_MODULE_SUBSCRIBER_FROM_NAME", "Имя отправителя");
define("NETCAT_MODULE_SUBSCRIBER_FROM_EMAIL", "Email отправителя");
define("NETCAT_MODULE_SUBSCRIBER_REPLY_TO", "Обратный адрес (reply to)");
define("NETCAT_MODULE_SUBSCRIBER_TEST_EMAIL", "Тестовый адрес для рассылок");
define("NETCAT_MODULE_SUBSCRIBER_CHARSET", "Кодировка письма");
define("NETCAT_MODULE_SUBSCRIBER_EMAIL_FIELD", "Поле из таблицы \"Пользователи\" с Email");
define("NETCAT_MODULE_SUBSCRIBER_NONE_EMAIL_FIELD", "Нет поля с email.<br/> <a href='".$nc_core->ADMIN_PATH."field/index.php?phase=2&isSys=1&SystemTableID=3'>Создайте</a> поле в системной таблице Пользователи типа \"Строка\" с форматом \"email\". ");
define("NETCAT_MODULE_SUBSCRIBER_SUBSCRIPTION_CONFIRM", "Подтверждение подписки");
define("NETCAT_MODULE_SUBSCRIBER_ONLY_UNREGISTERED", "только незарегистрированным");
define("NETCAT_MODULE_SUBSCRIBER_FOR_ONE_SUBSCRIPTION", "при первой подписке");
define("NETCAT_MODULE_SUBSCRIBER_ALWAYS", "всегда");
define("NETCAT_MODULE_SUBSCRIBER_UNCONFIRMED_MAX_TIME", "Удалять неподтвержденную подписку через (в часах)");
define("NETCAT_MODULE_MAIL_BODY", "Тело письма");
define("NETCAT_MODULE_SUBSCRIBER_OTHER", "Прочее");
define("NETCAT_MODULE_SUBSCRIBER_SUBSCRIBE_FORM", "Форма подписки для незарегистрированного пользователя");
define("NETCAT_MODULE_SUBSCRIBER_TEXT_CONFIRM", "Текст, выводимый при подтверждении подписки");
define("NETCAT_MODULE_SUBSCRIBER_TEXT_UNSCRIBE", "Текст, выводимый при отписке");
define("NETCAT_MODULE_SUBSCRIBER_TEXT_ERROR", "Текст, выводимый при ошибке");

//ui
define("NETCAT_MODULE_SUBSCRIBER_SAVE_BUTTON", "Сохранить");
define("NETCAT_MODULE_SUBSCRIBER_CLEAR_BUTTON", "Очистить");
define("NETCAT_MODULE_SUBSCRIBER_MAILER_ADD", "Добавить рассылку");
define("NETCAT_MODULE_SUBSCRIBER_DELETESELECTED", "Удалить выбранные");
define("NETCAT_MODULE_SUBSCRIBER_SELECTEDOFF", "Выключить выбранные");
define("NETCAT_MODULE_SUBSCRIBER_SELECTEDON", "Включить выбранные");
define("NETCAT_MODULE_SUBSCRIBE_ADD_SUBSCRIBE", "Добавить подписку");
define("NETCAT_MODULE_SUBSCRIBE_ACTION_SUBSCRIBE", "Подписать");
define("NETCAT_MODULE_SUBSCRIBE_SEND", "Послать");
define("NETCAT_MODULE_SUBSCRIBE_TESTSEND", "Послать тестово");
define("NETCAT_MODULE_SUBSCRIBE_MAILERS", "Рассылки");
define("NETCAT_MODULE_SUBSCRIBE_STATS", "Статистика");
define("NETCAT_MODULE_SUBSCRIBE_ONCE", "Разовая рассылка");
define("NETCAT_MODULE_SUBSCRIBE_SETTINGS", "Настройки модуля");

//stats
define("NETCAT_MODULE_SUBSCRIBER_STATS_IS_EMPTY", "Статистика пуста");
define("NETCAT_MODULE_SUBSCRIBER_SUBSCRIBERS_COUNT", "Подписчиков");
define("NETCAT_MODULE_SUBSCRIBER_MAIL_SEND_COUNT", "Выслано писем");
define("NETCAT_MODULE_SUBSCRIBER_LAST_SEND", "Последняя рассылка");
define("NETCAT_MODULE_SUBSCRIBER_ACTION", "Действие");
define("NETCAT_MODULE_SUBSCRIBER_DATE_TIME", "Дата и время");
define("NETCAT_MODULE_SUBSCRIBER_UNRESISTRED_USER", "Незарегистрированный пользователь");
define("NETCAT_MODULE_SUBSCRIBER_MAIL_SEND", "отправка письма");
define("NETCAT_MODULE_SUBSCRIBER_SUBSCRIBE", "подписка");
define("NETCAT_MODULE_SUBSCRIBER_CONFIRM", "подтверждение");
define("NETCAT_MODULE_SUBSCRIBER_UNSUBSCRIBE", "отписка");
define("NETCAT_MODULE_SUBSCRIBER_FULL_STATS_MAILER", "Подробная статистика рассылки");

// mailer
define("NETCAT_MODULE_MAILER_NO_ONE_MAILER", "Не найдено ни одной рассылки");
define("NETCAT_MODULE_SUBSCRIBER_MAILER_FILTER", "Выборка рассылок");
define("NETCAT_MODULE_SUBSCRIBER_MAILER_FILTER_TYPE", "Тип");
define("NETCAT_MODULE_SUBSCRIBER_MAILER_FILTER_STATUS", "Статус");
define("NETCAT_MODULE_SUBSCRIBER_ALL", "все");
define("NETCAT_MODULE_SUBSCRIBER_MAILER_CC", "подписка на раздел");
define("NETCAT_MODULE_SUBSCRIBER_MAILER_PERIODICAL", "регулярная рассылка");
define("NETCAT_MODULE_SUBSCRIBER_MAILER_SERVICE", "сервисная рассылка");
define("NETCAT_MODULE_SUBSCRIBER_MAILER_SERIAL", "cерийная рассылка");
define("NETCAT_MODULE_SUBSCRIBER_ONLY_ACTIVE", "только активные");
define("NETCAT_MODULE_SUBSCRIBER_ONLY_UNACTIVE", "только неактивные");
define("NETCAT_MODULE_SUBSCRIBER_FILTER", "Выбрать");
define("NETCAT_MODULE_SUBSCRIBER_TYPE", "Тип");
define("NETCAT_MODULE_SUBSCRIBER_SETTINGS", "Настройки");
define("NETCAT_MODULE_SUBSCRIBER_MAIN_SETTINGS", "Основные настройки");
define("NETCAT_MODULE_SUBSCRIBER_ACCESS_TO", "Доступно");
define("NETCAT_MODULE_SUBSCRIBER_ACCESS_ALL", "всем");
define("NETCAT_MODULE_SUBSCRIBER_ACCESS_REGISTERED", "зарегистрированным");
define("NETCAT_MODULE_SUBSCRIBER_ACCESS_AUTHORIZED", "уполномоченным");
define("NETCAT_MODULE_SUBSCRIBER_ACTIVE", "Активна");
define("NETCAT_MODULE_SUBSCRIBER_IN_STAT", "Сохранять подробную статистику");
define("NETCAT_MODULE_SUBSCRIBER_SPECIFIC_SETTINGS", "Настройки рассылки");
define("NETCAT_MODULE_SUBSCRIBER_SITE", "Сайт");
define("NETCAT_MODULE_SUBSCRIBER_SUBDIVISION", "Раздел");
define("NETCAT_MODULE_SUBSCRIBER_CC", "Компонент в разделе");
define("NETCAT_MODULE_SUBSCRIBER_ONCLASS", "Подписка на компонент");
define("NETCAT_MODULE_SUBSCRIBER_ADD_OBJECT_TO_MAILLIST", "Добавлять объект в рассылку при");
define("NETCAT_MODULE_SUBSCRIBER_ACTION_ADD_ON", "добавление включенного");
define("NETCAT_MODULE_SUBSCRIBER_ACTION_ADD_OFF", "добавление выключенного");
define("NETCAT_MODULE_SUBSCRIBER_ACTION_EDIT_ON", "изменение включенного");
define("NETCAT_MODULE_SUBSCRIBER_ACTION_EDIT_OFF", "изменение выключенного");
define("NETCAT_MODULE_SUBSCRIBER_ACTION_ON", "включение");
define("NETCAT_MODULE_SUBSCRIBER_ACTION_OFF", "выключение");
define("NETCAT_MODULE_SUBSCRIBER_COND_AND_ACTION", "Условия и действия");
define("NETCAT_MODULE_SUBSCRIBER_SUBSCRIBE_COND", "Условие подписки");
define("NETCAT_MODULE_SUBSCRIBER_SEND_COND", "Условие рассылки");
define("NETCAT_MODULE_SUBSCRIBER_SUBSCRIBE_ACTION", "Действие после подписки");
define("NETCAT_MODULE_SUBSCRIBER_MAIL_TEMPLATE", "Шаблон письма");
define("NETCAT_MODULE_SUBSCRIBER_MAIL_TEMPLATE_HEADER", "Верхняя часть (хэдер)");
define("NETCAT_MODULE_SUBSCRIBER_MAIL_TEMPLATE_FOOTER", "Нижняя часть (футер)");
define("NETCAT_MODULE_SUBSCRIBER_MAIL_TEMPLATE_CONTENT", "Содержательная часть");
define("NETCAT_MODULE_SUBSCRIBER_MAILER_TYPE", "Тип рассылки");
define("NETCAT_MODULE_SUBSCRIBER_MAILER_PERIOD", "Период рассылки");
define("NETCAT_MODULE_SUBSCRIBER_CONFIRM_REMOVE_MAILERS", "Подтвердите удаление рассылок");
define("NETCAT_MODULE_SUBSCRIBER_NO_SUBSCRIBERS", "Нет подписчиков");
define("NETCAT_MODULE_SUBSCRIBER_MAILER_SUBSCRIBER_LIST", "Список подписчиков рассылки");
define("NETCAT_MODULE_SUBSCRIBE_TURNEDON", "включен");
define("NETCAT_MODULE_SUBSCRIBE_TURNEDOFF", "выключен");
define("NETCAT_MODULE_SUBSCRIBE_WAIT_CONFIRM", "требуется подтверждение");
define("NETCAT_MODULE_SUBSCRIBE_UNREGISTERED_USER", "Незарегистрированный пользователь");
define("NETCAT_MODULE_SUBSCRIBE_USER_NOT_SUBSCRIBE", "Пользователь не подписан ни на одну рассылку.");
define("NETCAT_MODULE_SUBSCRIBE_NONE_MAILERS_FOR_USER", "Нет рассылок, на которые пользователь может подписаться.");
define("NETCAT_MODULE_SUBSCRIBE_SELECT_MAILER", "Выберите рассылку");
define("NETCAT_MODULE_SUBSCRIBER_NONE_CC", "Нет компонентов в разделе.");
define("NETCAT_MODULE_SUBSCRIBER_MAILER_SUCCESS_EDIT", "Подписка успешно изменена");
define("NETCAT_MODULE_SUBSCRIBER_MAILER_SUCCESS_ADD", "Подписка успешно добавлена");
define("NETCAT_MODULE_SUBSCRIBER_STATS_DROP", "Статистика успешно удалена");
define("NETCAT_MODULE_SUBSCRIBER_SETTINGS_OK", "Настройки успешно изменены");
define("NETCAT_MODULE_SUBSCRIBER_USER_NOT_FOUND", "Не найдено ни одного пользователя");
define("NETCAT_MODULE_SUBSCRIBER_MAIL_SEND_TO_USER", "Рассылка запущена. Письмо будет отправлено %d пользователям.");

// Разовая рассылка
define("NETCAT_MODULE_SUBSCRIBER_ONCE_TYPE_USER", "Типы пользователя");
define("NETCAT_MODULE_SUBSCRIBER_ONCE_REGISTRED", "зарегистрированные");
define("NETCAT_MODULE_SUBSCRIBER_ONCE_ALL", "все имеющиеся в базе");
define("NETCAT_MODULE_SUBSCRIBER_USER_CHECK", "Активность пользователя");
define("NETCAT_MODULE_SUBSCRIBER_USER_CHECK_ALL", "все");
define("NETCAT_MODULE_SUBSCRIBER_USER_CHECKED", "включенные");
define("NETCAT_MODULE_SUBSCRIBER_USER_NONECHECK", "выключенные");
define("NETCAT_MODULE_SUBSCRIBER_USER_ATTACH", "Прикрепленные файлы");
define("NETCAT_MODULE_SUBSCRIBER_FILE_1", "Первый файл");
define("NETCAT_MODULE_SUBSCRIBER_FILE_2", "Второй файл");
define("NETCAT_MODULE_SUBSCRIBER_FILE_3", "Третий файл");
define("NETCAT_MODULE_SUBSCRIBER_SUBSCRIBE_USER", "Подписанные на рассылку");
define("NETCAT_MODULE_SUBSCRIBER_ONCE_INVALID_TEST_EMAIL", "Неверно задан тестовый адрес для рассылок в настройках модуля");
define("NETCAT_MODULE_SUBSCRIBER_ONCE_MAIL_SEND", "Письмо на адрес %s отправлено.");
define("NETCAT_MODULE_SUBSCRIBER_ONCE_CLASSIFIER_NAME", "Использовать системный список e-mail'ов(исключает выборку пользователей)");
define("NETCAT_MODULE_SUBSCRIBER_ONCE_CLASSIFIER_WRONGNAME", "Название таблицы(классификатора) должно начинаться с буквы и содержать только латинские буквы и цифры");
define("NETCAT_MODULE_SUBSCRIBER_ONCE_CLASSIFIER_EMPTY", "Классификатор пуст или не существует");

function day_of_week_name($day) {
    $day_names = array(1=>'Понедельник',
            2 => 'Вторник',
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
            6 => 'Суббота',
            0 => 'Воскресенье');
    return $day_names[$day];
}

define("NETCAT_MODULE_SUBSCRIBER_PERIOD_WEEKLY", "Рассылать еженедельно в дни");
define("NETCAT_MODULE_SUBSCRIBER_PERIOD_WEEKLY_ON", "Вкл");
define("NETCAT_MODULE_SUBSCRIBER_PERIOD_WEEKLY_DAY", "День");
define("NETCAT_MODULE_SUBSCRIBER_PERIOD_WEEKLY_TIME", "Время старта");
define("NETCAT_MODULE_SUBSCRIBER_PERIOD_MONTHLY", "Отправка по формуле");

define("NETCAT_MODULE_SUBSCRIBER_PERIOD_HELP",
        "С помощью &quot;Отправки по формуле&quot; вы можете создавать гибкие расписания рассылок.<br/><br/>
          Правила формулы стостоят из префикса, обозначения дня и времени старта рассылки.<br/>"
        . "Префикс позволяет задавать относительную точку отсчёта (начало месяца, конец месяца), а также указывать конкретный день месяца:<br/>"
        . "&nbsp;&nbsp;&nbsp;<b>first</b> - от начала месяца, <b>last</b> - от конца месяца, <b>15</b> - день месяца<br/>"
        . "Обозначение дня указывает в какой день недели выполнять рассылку<br/>"
        . "&nbsp;&nbsp;&nbsp;<b>day</b> - любой день недели<br/>"
        . "&nbsp;&nbsp;&nbsp;<b>wday</b> - любой будний день<br/>"
        . "&nbsp;&nbsp;&nbsp;<b>mon, tue, wed, thu, fri, sat</b> или <b>sun</b> - конкретный день недели<br/>"
        . "Правила перечисляются через запятую:<br/>"
        . "&nbsp;&nbsp;&nbsp;<b>first thu 10, 15 day 17 , last day 23</b> - разослать в первый четверг в 10 часов, 15 числа в 17 часов и в последний день месяца(28, 30 или 31 в зависимости от месяца) в 23 часа."
        );

define("NETCAT_MODULE_SUBSCRIBER_STATS_NOTHING", "По заданным условиям ничего не найдено");
define("NETCAT_MODULE_SUBSCRIBER_STATS_FROM", "Дата события от");
define("NETCAT_MODULE_SUBSCRIBER_STATS_TO", "до");
define("NETCAT_MODULE_SUBSCRIBER_STATS_FILTER", "Выборка статистики");
define("NETCAT_MODULE_SUBSCRIBER_PERIOD_EACH", "рассылать");
?>