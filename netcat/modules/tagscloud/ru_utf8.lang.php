<?php

/* $Id: ru_utf8.lang.php 7302 2012-06-25 21:12:35Z alive $ */

define("NETCAT_MODULE_TAGSCLOUD_DESCRIPTION", "Данный модуль предназначен для вывода облака тегов.");

define("NETCAT_MODULE_TAGSCLOUD_LEGEND", "Добавление функционала тегов в шаблоны");
define("NETCAT_MODULE_TAGSCLOUD_SELECT_CLASS", "Выбрать шаблон");
define("NETCAT_MODULE_TAGSCLOUD_ADD_BUTTON", "Добавить функционал");
define("NETCAT_MODULE_TAGSCLOUD_DROP_BUTTON", "Удалить функционал");
define("NETCAT_MODULE_TAGSCLOUD_TAGS", "Теги");

define("NETCAT_MODULE_TAGSCLOUD_ADD_FIELD", "<li>Поле Tags уже существует в данном шаблоне!");
define("NETCAT_MODULE_TAGSCLOUD_ADD_ADDACTION", "<li>В шаблоне действий (\"После добавления\") присутствует нужный функционал, проверьте шаблон и исправьте его!");
define("NETCAT_MODULE_TAGSCLOUD_ADD_EDITACTION", "<li>В шаблоне действий (\"После изменения\") присутствует нужный функционал, проверьте шаблон и исправьте его!");
define("NETCAT_MODULE_TAGSCLOUD_ADD_DROPACTION", "<li>В шаблоне действий (\"После удаления\") присутствует нужный функционал, проверьте шаблон и исправьте его!");
define("NETCAT_MODULE_TAGSCLOUD_ADD_SETTINGS", "<li>В системных настройках шаблона присутствует нужный функционал, проверьте шаблон и исправьте его!");
define("NETCAT_MODULE_TAGSCLOUD_ADD_SOME", "<b>Выполнены все действия, за исключением:</b>\r\n<ul>");
define("NETCAT_MODULE_TAGSCLOUD_ADD_ALL", "<b>Добавление функционала в шаблон \"%ClassName\" невозможно, причина:</b>\r\n<ul>");
define("NETCAT_MODULE_TAGSCLOUD_ADD_DONE", "Функционал успешно добавлен.");

define("NETCAT_MODULE_TAGSCLOUD_DROP_FIELD", "<li>Поле Tags отсутствует в данном шаблоне!");
define("NETCAT_MODULE_TAGSCLOUD_DROP_ADDACTION", "<li>В шаблоне действий (\"После добавления\") отсутствует нужный функционал или значения отличаются от стандартных, проверьте шаблон и исправьте его, удалив вызов функции nc_tag_add()!");
define("NETCAT_MODULE_TAGSCLOUD_DROP_EDITACTION", "<li>В шаблоне действий (\"После изменения\") отсутствует нужный функционал или значения отличаются от стандартных, проверьте шаблон и исправьте его, удалив вызов функций nc_tag_drop() и nc_tag_add()!");
define("NETCAT_MODULE_TAGSCLOUD_DROP_DROPACTION", "<li>В шаблоне действий (\"После удаления\") отсутствует нужный функционал или значения отличаются от стандартных, проверьте шаблон и исправьте его, удалив вызов функции nc_tag_drop()!");
define("NETCAT_MODULE_TAGSCLOUD_DROP_SETTINGS", "<li>В системных настройках шаблона отсутствует нужный функционал или значения отличаются от стандартных, проверьте шаблон и исправьте его, удалив условие if(\$tags_messages) ...!");
define("NETCAT_MODULE_TAGSCLOUD_DROP_SOME", "<b>Выполнены все действия, за исключением:</b>\r\n<ul>");
define("NETCAT_MODULE_TAGSCLOUD_DROP_ALL", "<b>Удаление функционала из шаблона \"%ClassName\" невозможно, причина:</b>\r\n<ul>");
define("NETCAT_MODULE_TAGSCLOUD_DROP_DONE", "Функционал успешно удалён.");