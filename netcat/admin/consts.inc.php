<?php

define('DIRECTOR', 7);                      // директор
define('SUPERVISOR', 6);                    // супервизор
define('GUEST', 8);                         // гость

define('EDITOR', 5);                        // редактор
define('SUBDIVISION_ADMIN', 3);             // редактор раздела
define('CATALOGUE_ADMIN', 4);               // редактор сайта
define('SUB_CLASS_ADMIN', 9);               // редактор компонета в разделе

define('MODERATOR', 12);                    // управляет пользователями

define('DEVELOPER', 14);                    // разработчик
define('CLASSIFICATOR_ADMIN', 15);          // админ списка

define('BAN', 20);                          // огрничение
define('BAN_SITE', 21);                     // огрничение на сайте
define('BAN_SUB', 22);                      // огрничение в разделе
define('BAN_CC', 23);                       // огрничение в сс

define('SUBSCRIBER', 30);                   // подписчик

define('MASK_DELETE', 256);
define('MASK_CHECKED', 128);
define('MASK_COMMENT', 64);
define('MASK_ADMIN', 32);
define('MASK_MODERATE', 16);
define('MASK_SUBSCRIBE', 8);
define('MASK_EDIT', 4);
define('MASK_ADD', 2);
define('MASK_READ', 1);

//возможности по порядку
// в таком порядке будут отобожаться при просмотре прав
define('NC_PERM_READ_ID', 0);
define('NC_PERM_COMMENT_ID', 1);
define('NC_PERM_ADD_ID', 2);
define('NC_PERM_EDIT_ID', 3);
define('NC_PERM_CHECKED_ID', 4);
define('NC_PERM_DELETE_ID', 5);
define('NC_PERM_SUBCRIBE_ID', 6);
define('NC_PERM_MODERATE_ID', 7);
define('NC_PERM_ADMIN_ID', 8);
define('NC_PERM_COUNT_PERM', 9);


# Сущности
define('NC_PERM_ITEM_SITE', 0);
define('NC_PERM_ITEM_SUB', 1);
define('NC_PERM_ITEM_CC', 2);
define('NC_PERM_ITEM_USER', 3);
define('NC_PERM_ITEM_GROUP', 4);
define('NC_PERM_CLASSIFICATOR', 5);
define('NC_PERM_FAVORITE', 6);
define('NC_PERM_SQL', 7);
define('NC_PERM_CLASS', 8);
define('NC_PERM_FIELD', 9);
define('NC_PERM_SYSTABLE', 10);
define('NC_PERM_MODULE', 11);
define('NC_PERM_PATCH', 12);
define('NC_PERM_REPORT', 13);
define('NC_PERM_TEMPLATE', 14);
define('NC_PERM_CRON', 15);
define('NC_PERM_TOOLSHTML', 16);
define('NC_PERM_SEO', 17);
define('NC_PERM_REDIRECT', 18);


# Действия
define('NC_PERM_ACTION_ADD', 1);
define('NC_PERM_ACTION_DEL', 2);
define('NC_PERM_ACTION_EDIT', 3);
define('NC_PERM_ACTION_ADMIN', 4);
define('NC_PERM_ACTION_LIST', 5);
define('NC_PERM_ACTION_VIEW', 6);
define('NC_PERM_ACTION_INFO', 7);
define('NC_PERM_ACTION_MAIL', 8);
define('NC_PERM_ACTION_RIGHT', 9);
define('NC_PERM_ACTION_ADDSUB', 10);
define('NC_PERM_ACTION_DELSUB', 11);
define('NC_PERM_ACTION_SUBCLASSLIST', 12);
define('NC_PERM_ACTION_SUBCLASSDEL', 13);
define('NC_PERM_ACTION_SUBCLASSADD', 14);
define('NC_PERM_ACTION_ADDELEMENT', 15);
define('NC_PERM_ACTION_WIZARDCLASS', 16);

# Типы файловой системы
define('NC_FS_SIMPLE', 1);
define('NC_FS_ORIGINAL', 2);
define('NC_FS_PROTECTED', 3);

# Типы полей компонента
define('NC_FIELDTYPE_STRING', 1);
define('NC_FIELDTYPE_INT', 2);
define('NC_FIELDTYPE_TEXT', 3);
define('NC_FIELDTYPE_SELECT', 4);
define('NC_FIELDTYPE_BOOLEAN', 5);
define('NC_FIELDTYPE_FILE', 6);
define('NC_FIELDTYPE_FLOAT', 7);
define('NC_FIELDTYPE_DATETIME', 8);
define('NC_FIELDTYPE_RELATION', 9);
define('NC_FIELDTYPE_MULTISELECT', 10);
define('NC_FIELDTYPE_MULTIFILE', 11);

//
define('NC_FIELD_PERMISSION_EVERYONE', 1);
define('NC_FIELD_PERMISSION_ADMIN', 2);
define('NC_FIELD_PERMISSION_NOONE', 3);


define('NC_LANG_ACRONYM', 1);

define('NC_CLASS_TYPE_RSS', 'rss');

define('NC_TOKEN_ADD', 1);
define('NC_TOKEN_EDIT', 2);
define('NC_TOKEN_DROP', 4);

define('NC_FCKEDITOR', 2);
define('NC_CKEDITOR', 3);
define('NC_TINYMCE', 4);


define('NC_AUTH_LOGIN_INCORRECT', 1);
define('NC_AUTH_LOGIN_EXISTS', 2);
define('NC_AUTH_LOGIN_OK', 0);

// типы авторизации
define('NC_AUTHTYPE_LOGIN', 1);
define('NC_AUTHTYPE_HASH', 2);
define('NC_AUTHTYPE_EX', 4);
define('NC_AUTHTYPE_TOKEN', 8);
define('NC_AUTHTYPE_AD', 16);

// Last-Modified
define('NC_LASTMODIFIED_NONE', 1);
define('NC_LASTMODIFIED_YESTERDAY', 2);
define('NC_LASTMODIFIED_HOUR', 3);
define('NC_LASTMODIFIED_CURRENT', 4);
define('NC_LASTMODIFIED_ACTUAL', 5);