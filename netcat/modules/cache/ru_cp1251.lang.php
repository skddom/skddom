<?php

/* $Id$ */

// main
define("NETCAT_MODULE_CACHE_DESCRIPTION", "Модуль предназначен для кэширования результатов отображения данных на сайте.");
// catalogue form
define("CONTROL_CONTENT_CATALOGUE_FUNCS_CACHE", "Кэширование");
define("CONTROL_CONTENT_CATALOGUE_FUNCS_CACHE_ALLOW", "Разрешить");
define("CONTROL_CONTENT_CATALOGUE_FUNCS_CACHE_DENY", "Запретить");
define("CONTROL_CONTENT_CATALOGUE_FUNCS_CACHE_LIFETIME", "Актуальность (минуты)");
define("CONTROL_CONTENT_CATALOGUE_FUNCS_CACHE_STATUS", "Состояние кэша");
define("CONTROL_CONTENT_CATALOGUE_FUNCS_CACHE_CLEAR", "Очистить кэш");
// subdivision form
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_CACHE", "Кэширование");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_CACHE_ALLOW", "Разрешить");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_CACHE_DENY", "Запретить");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_CACHE_LIFETIME", "Актуальность (минуты)");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_CACHE_STATUS", "Состояние кэша");
define("CONTROL_CONTENT_SUBDIVISION_FUNCS_CACHE_CLEAR", "Очистить кэш");
// subclass form
define("CONTROL_CONTENT_SUBCLASS_FUNCS_CACHE", "Кэширование");
define("CONTROL_CONTENT_SUBCLASS_FUNCS_CACHE_ALLOW", "Разрешить");
define("CONTROL_CONTENT_SUBCLASS_FUNCS_CACHE_DENY", "Запретить");
define("CONTROL_CONTENT_SUBCLASS_FUNCS_CACHE_LIFETIME", "Актуальность (минуты)");
define("CONTROL_CONTENT_SUBCLASS_FUNCS_CACHE_STATUS", "Состояние кэша");
define("CONTROL_CONTENT_SUBCLASS_FUNCS_CACHE_CLEAR", "Очистить кэш");
// admin interface
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS", "Настройки кэша");
define("NETCAT_MODULE_CACHE_ADMIN_CACHE", "Кэш");
define("NETCAT_MODULE_CACHE_ADMIN_INFO", "Информация");
define("NETCAT_MODULE_CACHE_ADMIN_AUDIT", "Данные аудита");
define("NETCAT_MODULE_CACHE_ADMIN_MAINSETTINGS_TITLE", "Основные настройки");
define("NETCAT_MODULE_CACHE_ADMIN_MAINSETTINGS_SAVE_BUTTON", "Сохранить");
define("NETCAT_MODULE_CACHE_ADMIN_SAVE_OK", "Настройки кэша сохранены");
define("NETCAT_MODULE_CACHE_ADMIN_TYPE_LIST", "список объектов");
define("NETCAT_MODULE_CACHE_ADMIN_TYPE_FULL", "подробное отображение");
define("NETCAT_MODULE_CACHE_ADMIN_TYPE_BROWSE", "функции навигации");
define("NETCAT_MODULE_CACHE_ADMIN_TYPE_FUNCTION", "результаты выполнения функций");
// modules type
define("NETCAT_MODULE_CACHE_ADMIN_TYPE_CALENDAR", "отображение календаря");
// admin interface / cache settings
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_CATALOGUE", "Сайт для настройки");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_CACHE_TYPE", "Тип кэша");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_CACHE_ON", "Включен");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_CACHE_OFF", "Выключен");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_AUDIT", "Настройки аудита");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_AUDIT_ON", "включить режим аудита");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_AUDIT_BEGIN", "начало аудита");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_AUDIT_END", "окончание аудита");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_AUDIT_TIME", "длительность аудита (часы)");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_AUDIT_SAVE_TIME", "время с момента сохранения");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_QUOTA_TITLE", "Настройки квот");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_QUOTA_OVERDRAFT", "Превышение квоты");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_QUOTA_OVERDRAFT_NOCACHE", "Не кэшировать");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_QUOTA_OVERDRAFT_DROP", "Удалять неэффективный кэш");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_QUOTA_MAXSIZE_HEADER_CACHE", "Кэш");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_QUOTA_MAXSIZE_HEADER_SIZE", "Максимальный размер кэша (MB)");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_QUOTA_MAXSIZE_HEADER_CLEAR", "Очистить");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_INFO_DELETED", "%SIZE кэша \"%TYPE\" удалено");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_MEMCACHED", "Memcached");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_MEMCACHED_ON", "Использовать memcached");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_MEMCACHED_HOST", "адрес сервера");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_MEMCACHED_PORT", "порт");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_MEMCACHED_ERROR", "Не удалось подключиться к серверу memcached");
define("NETCAT_MODULE_CACHE_ADMIN_SETTINGS_MEMCACHED_DOESNT_EXIST", "Невозможно использовать memcached, так как не установлено расширение memcache.");
// admin interface / information
define("NETCAT_MODULE_CACHE_ADMIN_MAININFO_TITLE", "Общая информация (только для файлового кэша)");
define("NETCAT_MODULE_CACHE_ADMIN_MAININFO_HEADER_CACHE", "Кэш");
define("NETCAT_MODULE_CACHE_ADMIN_MAININFO_HEADER_FILES", "Файлы");
define("NETCAT_MODULE_CACHE_ADMIN_MAININFO_HEADER_DIRS", "Директории");
define("NETCAT_MODULE_CACHE_ADMIN_MAININFO_HEADER_SIZE", "Размер");
define("NETCAT_MODULE_CACHE_ADMIN_MAININFO_TOTAL", "итоговый");
define("NETCAT_MODULE_CACHE_ADMIN_MAININFO_CLEAR_TABLE", "Таблица очистки");
define("NETCAT_MODULE_CACHE_ADMIN_MAININFO_CACHE_COUNT", "Количество записей");
define("NETCAT_MODULE_CACHE_ADMIN_MAININFO_CACHE_AVERAGE_EFFICIENCY", "Средняя эффективность");
define("NETCAT_MODULE_CACHE_ADMIN_MAININFO_UPDATE_BUTTON", "Обновить информацию");
define("NETCAT_MODULE_CACHE_ADMIN_MAININFO_DROP_CLEAR_BUTTON", "Удалить данные очистки");
define("NETCAT_MODULE_CACHE_ADMIN_MAININFO_TYPE", "Тип кэша");
define("NETCAT_MODULE_CACHE_ADMIN_MAININFO_COUNT", "Записей в базе");
define("NETCAT_MODULE_CACHE_ADMIN_MAININFO_DROP_CLEAR_OK", "Данные таблицы очистки удалены");
// admin interface / audit data
define("NETCAT_MODULE_CACHE_ADMIN_AUDIT_DATA", "Данные аудита");
define("NETCAT_MODULE_CACHE_ADMIN_AUDIT_COUNT", "Подсчитано значений");
define("NETCAT_MODULE_CACHE_ADMIN_AUDIT_CATALOGUE", "Сайт");
define("NETCAT_MODULE_CACHE_ADMIN_AUDIT_SUBDIVISION", "Раздел");
define("NETCAT_MODULE_CACHE_ADMIN_AUDIT_SUBCLASS", "Компонент в разделе");
define("NETCAT_MODULE_CACHE_ADMIN_AUDIT_EFFICIENCY", "Эффективность");
define("NETCAT_MODULE_CACHE_ADMIN_AUDIT_NODATA", "нет данных");
define("NETCAT_MODULE_CACHE_ADMIN_AUDIT_SAVE_CLEAR_BUTTON", "Сохранить в таблице очистки");
define("NETCAT_MODULE_CACHE_ADMIN_AUDIT_SAVE_CLEAR_OK", "Данные аудита сохранены в таблице очистки");
define("NETCAT_MODULE_CACHE_ADMIN_AUDIT_DROP_BUTTON", "Удалить данные аудита");
define("NETCAT_MODULE_CACHE_ADMIN_AUDIT_DROP_OK", "Данные аудита удалены");
// classes constants
define("NETCAT_MODULE_CACHE_CLASS_UNRECOGNIZED_OBJECT_CALLING", "Неподдерживаемый вызов объекта кэша");
define("NETCAT_MODULE_CACHE_CLASS_UNCORRECT_DATA_FORMAT", "Неверный формат данных");
define("NETCAT_MODULE_CACHE_CLASS_CANNOT_CREATE_FILE", "Не получается создать или записать файл %FILE");
?>