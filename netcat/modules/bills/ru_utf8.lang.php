<?php

global $SUB_FOLDER, $HTTP_ROOT_PATH;

define("NETCAT_MODULE_BILLS", "Счета и акты");
define("NETCAT_MODULE_BILLS_TITLE", "Счета и акты");
define("NETCAT_MODULE_BILLS_DESCRIPTION", "Модуль для работы со&nbsp;счетами и&nbsp;актами. <br>
    Имеет возможность создания, редактирования, отправки на&nbsp;эл. почту и&nbsp;распечатки счетов и&nbsp;соответствующим им&nbsp;актов.<br>
    Данный модуль может интегрироваться с&nbsp;модулем «Приём платежей».");

define("NETCAT_MODULE_BILLS_INFORMATION", "Информация");
define("NETCAT_MODULE_BILLS_MANAGER", "Счета и акты");
define("NETCAT_MODULE_BILLS_BILLS", "Счета");
define("NETCAT_MODULE_BILLS_ACTS", "Акты");
define("NETCAT_MODULE_BILLS_SETTINGS", "Настройки");
define("NETCAT_MODULE_BILLS_CATALOGS", "Справочники");
define("NETCAT_MODULE_BILLS_STATUSES", "Статусы счетов");
define("NETCAT_MODULE_BILLS_SERVICES", "Оказываемые услуги");
define("NETCAT_MODULE_BILLS_CUSTOMERS", "Клиенты");

# Счета
define("NETCAT_MODULE_BILLS_BILL_ADD", "Добавление счёта");
define("NETCAT_MODULE_BILLS_BILL_EDIT", "Редактирование счета");

define("NETCAT_MODULE_BILLS_BILL_NUMBER", "Счёт №");
define("NETCAT_MODULE_BILLS_BILL_FROM", "от");

define("NETCAT_MODULE_BILLS_BILL_CLIENT_NAME", "ФИО клиента");
define("NETCAT_MODULE_BILLS_BILL_CLIENT_ADDRESS", "Адрес клиента");

define("NETCAT_MODULE_BILLS_BILL_ADD_POSITION", "Добавить позицию");
define("NETCAT_MODULE_BILLS_BILL_IS_PAID", "Счёт оплачен");
define("NETCAT_MODULE_BILLS_BILL_IS_PAID_SHORT", "Оплачен");

define("NETCAT_MODULE_BILLS_BILL_JURIDICAL", "на юр. лицо");
define("NETCAT_MODULE_BILLS_BILL_PHIYSICAL", "на физ. лицо");

define("NETCAT_MODULE_BILLS_BILL_LINK_TO_PDF", "Ссылка на PDF счета");
define("NETCAT_MODULE_BILLS_BILL_LINK_TO_PDF_WITH_SIGN", "Ссылка на PDF счета с подписью");

define("NETCAT_MODULE_BILLS_BILL_EMPTY", "Не создано ни одного счёта");

define("NETCAT_MODULE_BILLS_BILL_SEARCH", "Поиск по клиентам");
define("NETCAT_MODULE_BILLS_BILL_SEARCH_EMPTY", "Счет не найден");

define("NETCAT_MODULE_BILLS_BILL_CONFIRM_DELETE", "Действительно удалить позицию?");

# Акты
define("NETCAT_MODULE_BILLS_ACT_ADD", "Добавление акта");
define("NETCAT_MODULE_BILLS_ACT_EDIT", "Редактирование акта");
define("NETCAT_MODULE_BILLS_ACT_NUMBER", "Акт №");
define("NETCAT_MODULE_BILLS_ACT_BY_BILL", "По счёту");
define("NETCAT_MODULE_BILLS_ACT_EMPTY", "Не создано ни одного акта");
define("NETCAT_MODULE_BILLS_ACT_SEARCH", "Поиск по клиентам");

define("NETCAT_MODULE_BILLS_ACT_NOT_FOUND", "Акт не найден");

define("NETCAT_MODULE_BILLS_ACT_LINK_TO_PDF", "Ссылка на PDF акта");
define("NETCAT_MODULE_BILLS_ACT_LINK_TO_PDF_WITH_SIGN", "Ссылка на PDF акта с подписью");

# Клиенты
define("NETCAT_MODULE_BILLS_CLIENT_ADD", "Новый клиент");
define("NETCAT_MODULE_BILLS_CLIENT_EDIT", "Редактирование клиента");
define("NETCAT_MODULE_BILLS_CLIENT_OPF", "ОПФ");
define("NETCAT_MODULE_BILLS_CLIENT_NAME", "Название");
define("NETCAT_MODULE_BILLS_CLIENT_LEGAL_ADDRESS", "Юридический адрес");
define("NETCAT_MODULE_BILLS_CLIENT_PHONE", "Телефон");
define("NETCAT_MODULE_BILLS_CLIENT_INN", "ИНН");
define("NETCAT_MODULE_BILLS_CLIENT_KPP", "КПП");
define("NETCAT_MODULE_BILLS_CLIENT_PAYMENT_DETAILS", "Платёжные реквизиты");
define("NETCAT_MODULE_BILLS_CLIENT_BANK_NAME", "Название банка");
define("NETCAT_MODULE_BILLS_CLIENT_BANK_CURRENT_ACCOUNT", "Расчетный счет");
define("NETCAT_MODULE_BILLS_CLIENT_BANK_CORRESPONDENT_ACCOUNT", "Корреспондентский счет");
define("NETCAT_MODULE_BILLS_CLIENT_BANK_INN", "ИНН");
define("NETCAT_MODULE_BILLS_CLIENT_BANK_BIK", "БИК");

define("NETCAT_MODULE_BILLS_CLIENT_NOT_FOUND", "Клиент не найден");

define("NETCAT_MODULE_BILLS_CLIENT_CHANGES_OK", "Изменения успешно сохранены");
define("NETCAT_MODULE_BILLS_CLIENT_EMPTY", "Не создано ни одного клиента");

# Информация
define("NETCAT_MODULE_BILLS_INFORMATION_STAT_ALL", "Всего");
define("NETCAT_MODULE_BILLS_INFORMATION_STAT_PAID", "Оплаченные");
define("NETCAT_MODULE_BILLS_INFORMATION_STAT_UNPAID", "Неоплаченные");

# Настройки
define("NETCAT_MODULE_BILLS_SETTINGS_VAT", "Ставка НДС");
define("NETCAT_MODULE_BILLS_SETTINGS_SECRET_KEY", "Секретный ключ");
define("NETCAT_MODULE_BILLS_SETTINGS_COMPANY_DETAILS", "Реквизиты компании");
define("NETCAT_MODULE_BILLS_SETTINGS_OPF", "ОПФ");
define("NETCAT_MODULE_BILLS_SETTINGS_NAME", "Название");
define("NETCAT_MODULE_BILLS_SETTINGS_LEGAL_ADDRESS", "Юридический адрес");
define("NETCAT_MODULE_BILLS_SETTINGS_PHONE", "Телефон");
define("NETCAT_MODULE_BILLS_SETTINGS_INN", "ИНН");
define("NETCAT_MODULE_BILLS_SETTINGS_KPP", "КПП");
define("NETCAT_MODULE_BILLS_SETTINGS_PAYMENT_DETAILS", "Платёжные реквизиты");
define("NETCAT_MODULE_BILLS_SETTINGS_BANK_NAME", "Название банка");
define("NETCAT_MODULE_BILLS_SETTINGS_BANK_CURRENT_ACCOUNT", "Расчетный счет");
define("NETCAT_MODULE_BILLS_SETTINGS_BANK_CORRESPONDENT_ACCOUNT", "Корреспондентский счет");
define("NETCAT_MODULE_BILLS_SETTINGS_BANK_INN", "ИНН");
define("NETCAT_MODULE_BILLS_SETTINGS_BANK_BIK", "БИК");

define("NETCAT_MODULE_BILLS_SETTINGS_GRAPHIC_ELEMENTS", "Графические элементы");
define("NETCAT_MODULE_BILLS_SETTINGS_LOGO", "Логотип");
define("NETCAT_MODULE_BILLS_SETTINGS_DIRECTOR_SIGN", "Подпись директора");
define("NETCAT_MODULE_BILLS_SETTINGS_BUCH_SIGN", "Подпись бухгалтера");
define("NETCAT_MODULE_BILLS_SETTINGS_PRINT", "Печать");

define("NETCAT_MODULE_BILLS_SETTINGS_SAVED", "Настройки успешно сохранены");

# Общие
define("NETCAT_MODULE_BILLS_CLIENT", "Клиент");
define("NETCAT_MODULE_BILLS_NAME", "Наименование");
define("NETCAT_MODULE_BILLS_UNIT", "Ед. изм");
define("NETCAT_MODULE_BILLS_COUNT", "Кол-во");
define("NETCAT_MODULE_BILLS_PRICE", "Цена");
define("NETCAT_MODULE_BILLS_SUM", "Сумма");
define("NETCAT_MODULE_BILLS_DATE", "Дата");
define("NETCAT_MODULE_BILLS_SELECT", "Выберите");
define("NETCAT_MODULE_BILLS_SAVE", "Сохранить");
define("NETCAT_MODULE_BILLS_ADD", "Добавить");
define("NETCAT_MODULE_BILLS_ADD_JURIDICAL", "Добавить счет на юр. лицо");
define("NETCAT_MODULE_BILLS_ADD_PHYSICAL", "Добавить счет на физ. лицо");
define("NETCAT_MODULE_BILLS_BACK", "Назад");
define("NETCAT_MODULE_BILLS_DELETE", "Удалить");
define("NETCAT_MODULE_BILLS_SHOW", "Посмотреть");

define("NETCAT_MODULE_BILLS_PDF", "PDF");
define("NETCAT_MODULE_BILLS_PDF_WITH_SIGN", "PDF с подписью");
define("NETCAT_MODULE_BILLS_PDF_WITH_SIGN_AND_PRINT", "PDF с подписью и печатью");
define("NETCAT_MODULE_BILLS_BATCH_LOADING", "Документы пакетом");

define("NETCAT_MODULE_BILLS_CONFIRM_DELETE", "Подтвердить удаление");

define("NETCAT_MODULE_BILLS_YES", "Да");
define("NETCAT_MODULE_BILLS_NO", "Нет");

define("NETCAT_MODULE_BILLS_VALIDATE_NO_ACT_NUMBER", "Не указан номер акта");
define("NETCAT_MODULE_BILLS_VALIDATE_NO_BILL_NUMBER", "Не указан номер счёта");
define("NETCAT_MODULE_BILLS_VALIDATE_NO_DATE", "Не указана дата");
define("NETCAT_MODULE_BILLS_VALIDATE_NO_ACCOUNT", "Не выбран счет");
define("NETCAT_MODULE_BILLS_VALIDATE_NO_CLIENT", "Не выбран клиент");
define("NETCAT_MODULE_BILLS_VALIDATE_NO_POSITION", "Не добавлена ни одна позиция");