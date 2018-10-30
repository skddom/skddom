<?php

global $SUB_FOLDER, $HTTP_ROOT_PATH;


define("NETCAT_MODULE_NETSHOP_TITLE", "Интернет-магазин");
define("NETCAT_MODULE_NETSHOP_DESCRIPTION", "Интернет-магазин");

define("NETCAT_MODULE_NETSHOP_ERROR_NO_SETTINGS", "Отсутствует объект настроек в компоненте Интернет-магазин");

define("NETCAT_MODULE_NETSHOP_UPGRADE", "Функция доступна в старших редакциях системы. <a href='https://netcat.ru/products/upgrade/' target='_blank'>Обновить лицензию</a>");

// Настройки магазина
define("NETCAT_MODULE_NETSHOP_SHOP_URL", 'URL магазина');
define("NETCAT_MODULE_NETSHOP_SHOP_NAME", 'Название магазина');
define("NETCAT_MODULE_NETSHOP_COMPANY_NAME", 'Полное название организации');
define("NETCAT_MODULE_NETSHOP_ADDRESS", 'Юридический адрес');
define("NETCAT_MODULE_NETSHOP_CITY", 'Город расположения магазина');
define("NETCAT_MODULE_NETSHOP_PHONE", 'Телефон');
define("NETCAT_MODULE_NETSHOP_DEFAULT_CURRENCY_ID", 'Валюта магазина');
define("NETCAT_MODULE_NETSHOP_MAIL_FROM", 'Email, с которого высылаются письма');
define("NETCAT_MODULE_NETSHOP_MANAGER_EMAIL", 'Email для оповещения');
define("NETCAT_MODULE_NETSHOP_EXTERNAL_CURRENCY", 'Основная валюта (ЦБ)');
define("NETCAT_MODULE_NETSHOP_CURRENCY_CONVERSION_PERCENT", 'Добавочный процент при пересчете валют');
define("NETCAT_MODULE_NETSHOP_INN", 'ИНН');
define("NETCAT_MODULE_NETSHOP_BANK_NAME", 'Название банка');
define("NETCAT_MODULE_NETSHOP_BANK_ACCOUNT", 'Расчетный счет');
define("NETCAT_MODULE_NETSHOP_CORRESPONDENT_ACCOUNT", 'Корреспондентский счет');
define("NETCAT_MODULE_NETSHOP_KPP", 'КПП');
define("NETCAT_MODULE_NETSHOP_BIK", 'БИК');
define("NETCAT_MODULE_NETSHOP_VAT", 'Ставка НДС, %');
define("NETCAT_MODULE_NETSHOP_WEBMONEY_PURSE", 'Webmoney: кошелек продавца');
define("NETCAT_MODULE_NETSHOP_WEBMONEY_SECRET_KEY", 'Webmoney: secret key');
define("NETCAT_MODULE_NETSHOP_PAY_CASH_SETTINGS", 'Настройки "Яндекс-Деньги"');
define("NETCAT_MODULE_NETSHOP_ASSIST_SHOP_ID", 'Идентификатор в ASSIST');
define("NETCAT_MODULE_NETSHOP_PAYMENT_SUCCESS_PAGE", 'URL страницы сайта при удачном платеже (с http://)');
define("NETCAT_MODULE_NETSHOP_ASSIST_SECRET_WORD", 'Секретное слово для Assist');
define("NETCAT_MODULE_NETSHOP_PAYMENT_FAILED_PAGE", 'URL страницы сайта при неуспешном платеже (с http://)');
define("NETCAT_MODULE_NETSHOP_ROBOKASSA_LOGIN", 'Логин в системе Robokassa');
define("NETCAT_MODULE_NETSHOP_ROBOKASSA_PASS1", 'Пароль #1 в системе Robokassa');
define("NETCAT_MODULE_NETSHOP_ROBOKASSA_PASS2", 'Пароль #2 в системе Robokassa');
define("NETCAT_MODULE_NETSHOP_INC_CURR_LABEL", 'Валюта для системы Robokassa');
define("NETCAT_MODULE_NETSHOP_PAYPAL_BIZ_MAIL", 'Paypal Log-in Email');
define("NETCAT_MODULE_NETSHOP_QIWI_FROM", 'Номер магазина в системе QIWI');
define("NETCAT_MODULE_NETSHOP_QIWI_PWD", 'Пароль в системе QIWI');
define("NETCAT_MODULE_NETSHOP_MAIL_SHOP_ID", 'Номер магазина в системе Деньги@mail.ru');
define("NETCAT_MODULE_NETSHOP_MAIL_SECRET_KEY", 'Ключ магазина в системе Деньги@mail.ru');
define("NETCAT_MODULE_NETSHOP_MAIL_HASH", 'Криптографический хэш от ключа в системе Деньги@mail.ru');


define("NETCAT_MODULE_NETSHOP_SHOP", "Магазин");
define("NETCAT_MODULE_NETSHOP_ITEM", "Товар");
define("NETCAT_MODULE_NETSHOP_DISCOUNT", "Скидка");
define("NETCAT_MODULE_NETSHOP_DISCOUNTS", "Скидки");
define("NETCAT_MODULE_NETSHOP_CART_DISCOUNT", "Скидка на заказ");
define("NETCAT_MODULE_NETSHOP_SURCHARGE", "Наценка");
define("NETCAT_MODULE_NETSHOP_COST", "Стоимость");
define("NETCAT_MODULE_NETSHOP_ITEM_COST", "СТОИМОСТЬ ТОВАРОВ");
define("NETCAT_MODULE_NETSHOP_QTY", "Количество");
define("NETCAT_MODULE_NETSHOP_ITEM_FULL_NAME", "Полное наименование (производитель, название варианта)");
define("NETCAT_MODULE_NETSHOP_ITEM_PRICE", "Цена");
define("NETCAT_MODULE_NETSHOP_SUM", "Итого");
define("NETCAT_MODULE_NETSHOP_ITEM_DELETE", "Удалить");
define("NETCAT_MODULE_NETSHOP_SETTINGS", "Настройки");

define("NETCAT_MODULE_NETSHOP_APPLIED_DISCOUNTS", "На этот товар действует скидка:");

define("NETCAT_MODULE_NETSHOP_PRICE_WITH_DISCOUNT", "Цена товара со&nbsp;скидкой");
define("NETCAT_MODULE_NETSHOP_PRICE_WITHOUT_DISCOUNT", "Цена товара без&nbsp;скидки");
define("NETCAT_MODULE_NETSHOP_PRICE_WITH_DISCOUNT_SHORT", "Цена со&nbsp;скидкой");
define("NETCAT_MODULE_NETSHOP_PRICE_WITHOUT_DISCOUNT_SHORT", "Цена без&nbsp;скидки");


define("NETCAT_MODULE_NETSHOP_CURRENCIES", "Валюты");

define("NETCAT_MODULE_NETSHOP_DELIVERY", "Доставка");
define("NETCAT_MODULE_NETSHOP_PAYMENT", "Оплата");

define("NETCAT_MODULE_NETSHOP_REFRESH", "Пересчитать корзину");
define("NETCAT_MODULE_NETSHOP_PRICE_TYPE", "Тип цен");
define("NETCAT_MODULE_NETSHOP_ITEM_FORMS", "товар, товара, товаров");

define("NETCAT_MODULE_NETSHOP_FILL_REQUIRED", "Пожалуйста, заполните все поля, отмеченные звездочкой (*)");


define("NETCAT_MODULE_NETSHOP_NEXT", "Далее");
define("NETCAT_MODULE_NETSHOP_BACK", "Назад");
define("NETCAT_MODULE_NETSHOP_MORE", "подробнее");
define("NETCAT_MODULE_NETSHOP_INSTALL", "Установить");


// Статистика
define("NETCAT_MODULE_NETSHOP_STATISTICS", "Статистика");
define("NETCAT_MODULE_NETSHOP_DATA_NOT_AVAILABLE", "Данные отсутствуют");

define("NETCAT_MODULE_NETSHOP_SALES",     "Продажи");
define("NETCAT_MODULE_NETSHOP_ORDERS",    "Заказы");
define("NETCAT_MODULE_NETSHOP_GOODS",     "Товары");
define("NETCAT_MODULE_NETSHOP_CUSTOMERS", "Покупатели");

define("NECTAT_MODULE_NETSHOP_SALES_AMOUNT",                  "Сумма продаж");
define("NECTAT_MODULE_NETSHOP_TOTAL_ORDERS",                  "Количество заказов");
define("NECTAT_MODULE_NETSHOP_COMPLETED_ORDERS",              "Выполнено заказов");
define("NECTAT_MODULE_NETSHOP_PURCHASED_GOODS",               "Продано товаров");
define("NECTAT_MODULE_NETSHOP_TOP_PURCHASED_GOODS",           "Лидеры продаж");
define("NECTAT_MODULE_NETSHOP_SUCCESSFUL_ORDERS_PERCENTAGE",  "Процент успешных заказов");
define("NECTAT_MODULE_NETSHOP_AVG_ORDER_AMOUNT",              "Средняя стоимость заказа");
define("NECTAT_MODULE_NETSHOP_AVG_SALES_ORDER_AMOUNT_BY_DAY", "Средние ежедневные продажи");
define("NETCAT_MODULE_NETSHOP_SELECTED_PERIOD",               "Выбранный период");
define("NETCAT_MODULE_NETSHOP_LAST_PERIOD",                   "Прошлый период");

define("NETCAT_MODULE_NETSHOP_OVER_PERIOD", "За период…");
define("NETCAT_MODULE_NETSHOP_7_DAYS",      "За 7 дней");
define("NETCAT_MODULE_NETSHOP_30_DAYS",     "За 30 дней");
define("NETCAT_MODULE_NETSHOP_X_DAYS",      "За %s дней");

define("NETCAT_MODULE_NETSHOP_BY_DAYS",   "дням");
define("NETCAT_MODULE_NETSHOP_BY_WEEKS",  "неделям");
define("NETCAT_MODULE_NETSHOP_BY_MONTHS", "месяцам");
define("NETCAT_MODULE_NETSHOP_GROUP_BY",  "Группировка по");

define("NETCAT_MODULE_NETSHOP_GOODS_BY_QTY",          "По количеству продаж");
define("NETCAT_MODULE_NETSHOP_GOODS_BY_SALES_AMOUNT", "По сумме продаж");

define("NETCAT_MODULE_NETSHOP_TODAY", "Сегодня");
define("NETCAT_MODULE_NETSHOP_YESTERDAY", "Вчера");
define("NETCAT_MODULE_NETSHOP_AVG_FOR_7_DAYS", "В среднем за 7 дней");

define("NETCAT_MODULE_NETSHOP_WEEK", "Неделя");
define("NETCAT_MODULE_NETSHOP_LAST_WEEK", "Неделя назад");
define("NETCAT_MODULE_NETSHOP_MONTH", "Месяц");
define("NETCAT_MODULE_NETSHOP_LAST_MONTH", "Месяц назад");


define("NETCAT_MODULE_NETSHOP_1C_INTEGRATION", "1С и МойСклад");
define("NETCAT_MODULE_NETSHOP_1C_INTEGRATION_IMPORT", "Импорт источника");
define("NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR", "Перехватчик файлов импорта");
define("NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR_FILES_LIST", "Список файлов");
define("NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR_FILE", "Файл");
define("NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR_CREATED_AT", "Время создания");
define("NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR_IMPORT", "Импортировать");
define("NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR_CONFIRM_DELETE_FILE", "Действительно удалить данный файл?");
define("NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR_DELETE_ALL_FILES", "Удалить все файлы");
define("NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR_CONFIRM_DELETE_ALL_FILES", "Действительно удалить все файлы?");
define("NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR_INTERCEPT_URL", "URL для настройки источника");


//Forms
define("NETCAT_MODULE_NETSHOP_SAVE",          "Сохранить");
define("NETCAT_MODULE_NETSHOP_ADMIN_SAVE_OK", "Настройки успешно сохранены");
define("NETCAT_MODULE_NETSHOP_FORMS",         "Бланки");
define("NETCAT_MODULE_NETSHOP_FORMS_TYPE",    "Тип бланка");

define("NETCAT_MODULE_NETSHOP_CASHMEMO",                 "Товарный чек");
define("NETCAT_MODULE_NETSHOP_CASHMEMO_COMPANY",         "Название компании");
define("NETCAT_MODULE_NETSHOP_CASHMEMO_ADDRESS",         "Адрес");
define("NETCAT_MODULE_NETSHOP_CASHMEMO_SELLER",          "Продавец");
define("NETCAT_MODULE_NETSHOP_CASHMEMO_SELLER_POSITION", "Должность");

define("NETCAT_MODULE_NETSHOP_EMS_INTERNATIONAL_FROM_FULLNAME",      "ФИО отправителя");
define("NETCAT_MODULE_NETSHOP_EMS_INTERNATIONAL_FROM_ADDRESS_LINE1", "Адрес отправителя строка 1");
define("NETCAT_MODULE_NETSHOP_EMS_INTERNATIONAL_FROM_ADDRESS_LINE2", "Адрес отправителя строка 2");
define("NETCAT_MODULE_NETSHOP_EMS_INTERNATIONAL_FROM_PHONE",         "Телефон отправителя");
define("NETCAT_MODULE_NETSHOP_EMS_INTERNATIONAL_TO_FULLNAME",        "ФИО получателя");
define("NETCAT_MODULE_NETSHOP_EMS_INTERNATIONAL_TO_ADDRESS_LINE1",   "Адрес получателя строка 1");
define("NETCAT_MODULE_NETSHOP_EMS_INTERNATIONAL_TO_ADDRESS_LINE2",   "Адрес получателя строка 2");
define("NETCAT_MODULE_NETSHOP_EMS_INTERNATIONAL_TO_PHONE",           "Телефон получателя");
define("NETCAT_MODULE_NETSHOP_EMS_INTERNATIONAL_DESCRIPTION",        "Описание вложения");
define("NETCAT_MODULE_NETSHOP_EMS_INTERNATIONAL_VALUE",              "Стоимость");
define("NETCAT_MODULE_NETSHOP_EMS_INTERNATIONAL_WEIGHT",             "Вес");

define("NETCAT_MODULE_NETSHOP_EMS_RUSSIA_FROM_LEGAL_ENTITY", "Отправитель: юридическое лицо");
define("NETCAT_MODULE_NETSHOP_EMS_RUSSIA_TO_LEGAL_ENTITY",   "Получатель: юридическое лицо");
define("NETCAT_MODULE_NETSHOP_EMS_RUSSIA_FROM_STREET",       "Улица");
define("NETCAT_MODULE_NETSHOP_EMS_RUSSIA_FROM_HOUSE",        "Дом");
define("NETCAT_MODULE_NETSHOP_EMS_RUSSIA_FROM_BLOCK",        "Корпус");
define("NETCAT_MODULE_NETSHOP_EMS_RUSSIA_FROM_FLOOR",        "Этаж");
define("NETCAT_MODULE_NETSHOP_EMS_RUSSIA_FROM_APARTMENT",    "Квартира");
define("NETCAT_MODULE_NETSHOP_EMS_RUSSIA_FROM_INTERCOM",     "Домофон");
define("NETCAT_MODULE_NETSHOP_EMS_RUSSIA_FROM_CITY",         "Город");
define("NETCAT_MODULE_NETSHOP_EMS_RUSSIA_FROM_REGION",       "Регион");
define("NETCAT_MODULE_NETSHOP_EMS_RUSSIA_FROM_ZIPCODE",      "Индекс");
define("NETCAT_MODULE_NETSHOP_EMS_RUSSIA_CASH_ON_DELIVERY",  "Наложенный платеж");

define("NETCAT_MODULE_NETSHOP_POST_INN",     "ИНН");
define("NETCAT_MODULE_NETSHOP_POST_KOR",     "Кор/счет");
define("NETCAT_MODULE_NETSHOP_POST_BANK",    "Наименование банка");
define("NETCAT_MODULE_NETSHOP_POST_ACCOUNT", "Рас/счет");
define("NETCAT_MODULE_NETSHOP_POST_BIK",     "БИК");

define("NETCAT_MODULE_NETSHOP_TORG12",                      "ТОРГ-12");
define("NETCAT_MODULE_NETSHOP_TORG12_NUMBER_TEMPLATE",      "Шаблон номера документа");
define("NETCAT_MODULE_NETSHOP_TORG12_UNIT",                 "Структурное подразделение");
define("NETCAT_MODULE_NETSHOP_TORG12_CONSIGNEE",            "Грузополучатель");
define("NETCAT_MODULE_NETSHOP_TORG12_SUPPLIER",             "Поставщик");
define("NETCAT_MODULE_NETSHOP_TORG12_PAYER",                "Плательщик");
define("NETCAT_MODULE_NETSHOP_TORG12_CONTRACT",             "Основание");
define("NETCAT_MODULE_NETSHOP_TORG12_OKDP",                 "ОКДП");
define("NETCAT_MODULE_NETSHOP_TORG12_TRANS_NUMBER",         "Транс. накл. номер");
define("NETCAT_MODULE_NETSHOP_TORG12_TRANS_DATE",           "Транс. накл. дата");
define("NETCAT_MODULE_NETSHOP_TORG12_OPERATION_TYPE",       "Вид операции");
define("NETCAT_MODULE_NETSHOP_TORG12_NDS",                  "НДС (%)");
define("NETCAT_MODULE_NETSHOP_TORG12_RESOLVED_BY_POSITION", "Разрешил (должность)");
define("NETCAT_MODULE_NETSHOP_TORG12_RESOLVED_BY_SURNAME",  "Разрешил (расшифровка подписи)");
define("NETCAT_MODULE_NETSHOP_TORG12_ACCOUNTANT_SURNAME",   "Бухгалтер (расшифровка подписи)");
define("NETCAT_MODULE_NETSHOP_TORG12_RELEASED_BY_POSITION", "Отпустил (должность)");
define("NETCAT_MODULE_NETSHOP_TORG12_RELEASED_BY_SURNAME",  "Отпустил (расшифровка подписи)");

define("NETCAT_MODULE_NETSHOP_DELIVERY_SERVICE_DONT_USE", "Не использовать");
define("NETCAT_MODULE_NETSHOP_DELIVERY_SERVICE_FIELD_MAPPING", "Соответствие полей для автоматического расчёта «%s»");
define("NETCAT_MODULE_NETSHOP_DELIVERY_SERVICE_FIELD_MAPPING_SHOP", "Настройки магазина");
define("NETCAT_MODULE_NETSHOP_DELIVERY_SERVICE_FIELD_MAPPING_ORDER", "Параметры заказа");
define("NETCAT_MODULE_NETSHOP_DELIVERY_SERVICE_SETTINGS", "Дополнительные настройки для автоматического расчёта «%s»");
define("NETCAT_MODULE_NETSHOP_DELIVERY_EMS", "EMS");
define("NETCAT_MODULE_NETSHOP_DELIVERY_RUSSIANPOST", "Почта России");
define("NETCAT_MODULE_NETSHOP_EMS_RUSSIA", "EMS внутренние отправления");
define("NETCAT_MODULE_NETSHOP_EMS_INTERNATIONAL", "EMS международные отправления");
define("NETCAT_MODULE_NETSHOP_RUSSIANPOST_PACKAGE", "Почта России ф. 116");
define("NETCAT_MODULE_NETSHOP_RUSSIANPOST_CASH_ON_DELIVERY", "Почта России ф. 113эн");
define("NETCAT_MODULE_NETSHOP_DELIVERY_YANDEX", "Яндекс.Доставка");
define("NETCAT_MODULE_NETSHOP_DELIVERY_YANDEX_KEYS", "Ключи для методов (верхнее поле с ключами в личном кабинете Яндекса)");
define("NETCAT_MODULE_NETSHOP_DELIVERY_YANDEX_IDS", "Идентификаторы (нижнее поле с ключами в личном кабинете Яндекса)");
define("NETCAT_MODULE_NETSHOP_DELIVERY_YANDEX_PAYMENT_CHARGE", "Сбор за перечисление денежных средств");
define("NETCAT_MODULE_NETSHOP_DELIVERY_YANDEX_PAYMENT_CHARGE_INCLUDED", "включён в стоимость заказа");
define("NETCAT_MODULE_NETSHOP_DELIVERY_YANDEX_PAYMENT_CHARGE_EXTRA", "прибавить к наценке за способ оплаты");
define("NETCAT_MODULE_NETSHOP_DELIVERY_TYPE", "Тип доставки");
define("NETCAT_MODULE_NETSHOP_DELIVERY_TYPE_POST", "Получение в почтовом отделении");
define("NETCAT_MODULE_NETSHOP_DELIVERY_TYPE_PICKUP", "Получение в пункте выдачи");
define("NETCAT_MODULE_NETSHOP_DELIVERY_TYPE_COURIER", "Доставка курьером по адресу");
define("NETCAT_MODULE_NETSHOP_DELIVERY_WITH_CHECK", "Возможна проверка заказа при получении. ");
define("NETCAT_MODULE_NETSHOP_DELIVERY_COURIER_TIME", "Время доставки: ");
define("NETCAT_MODULE_NETSHOP_DELIVERY_POINT_SELECT_BUTTON", "Выбрать этот пункт выдачи");
define("NETCAT_MODULE_NETSHOP_DELIVERY_DAYS_OF_WEEK_SHORT", "/пн/вт/ср/чт/пт/сб/вс");
define("NETCAT_MODULE_NETSHOP_DELIVERY_TIME_ALL_DAY", "круглосуточно");
define("NETCAT_MODULE_NETSHOP_DELIVERY_ON_MAP", "на карте");

define("NETCAT_MODULE_NETSHOP_DELIVERY_POINTS", "Пункты выдачи");
define("NETCAT_MODULE_NETSHOP_DELIVERY_POINT", "Пункт выдачи");
define("NETCAT_MODULE_NETSHOP_DELIVERY_POINT_LOCATION_NAME", "Населённый пункт");
define("NETCAT_MODULE_NETSHOP_DELIVERY_POINT_ADDRESS", "Адрес");
define("NETCAT_MODULE_NETSHOP_DELIVERY_POINT_SCHEDULE", "Расписание работы");
define("NETCAT_MODULE_NETSHOP_DELIVERY_POINT_GROUP", "Группа пунктов выдачи");
define("NETCAT_MODULE_NETSHOP_DELIVERY_POINT_GROUP_SHORT", "Группа");
define("NETCAT_MODULE_NETSHOP_DELIVERY_POINT_GROUP_ANY", "любая");
define("NETCAT_MODULE_NETSHOP_DELIVERY_POINT_CONFIRM_DELETE", "Удалить пункт выдачи «%s»?");
define("NETCAT_MODULE_NETSHOP_DELIVERY_POINT_DRAG", "перетащите маркер, чтобы изменить его положение на карте");

define("NETCAT_MODULE_NETSHOP_DELIVERY_PAYMENT_CASH", "оплата при получении наличными");
define("NETCAT_MODULE_NETSHOP_DELIVERY_PAYMENT_CARD", "оплата при получении банковскими картами");

define("NETCAT_MODULE_NETSHOP_DELIVERY_SCHEDULE_TIME_FROM", "с");
define("NETCAT_MODULE_NETSHOP_DELIVERY_SCHEDULE_TIME_TO", "до");
define("NETCAT_MODULE_NETSHOP_DELIVERY_SCHEDULE_TIME_PLACEHOLDER", "чч:мм");
define("NETCAT_MODULE_NETSHOP_DELIVERY_SCHEDULE_INTERVAL_REMOVE", "Удалить интервал?");

define("NETCAT_MODULE_NETSHOP_PHONE_EXTENSION", "доб.");

//Filter
define("NETCAT_MODULE_NETSHOP_FILTER_SHOW", "Применить фильтр");
define("NETCAT_MODULE_NETSHOP_FILTER_RESET", "Сбросить фильтр");
define("NETCAT_MODULE_NETSHOP_FILTER_FROM", "от");
define("NETCAT_MODULE_NETSHOP_FILTER_TO", "до");
define("NETCAT_MODULE_NETSHOP_FILTER_BOOLEAN_TRUE", "есть");
define("NETCAT_MODULE_NETSHOP_FILTER_BOOLEAN_FALSE", "нет");

define("NETCAT_MODULE_NETSHOP_EXPORT_COMMERCEML", "Экспорт в 1C");

define("NETCAT_MODULE_NETSHOP_IMPORT_COMMERCEML", "Импорт данных в формате CommerceML");
define("NETCAT_MODULE_NETSHOP_IMPORT_COMMERCEML_NOT_WELL_FORMED", "Ошибка при загрузке XML-файла");
define("NETCAT_MODULE_NETSHOP_IMPORT_COMMERCEML_SCHEME_VER", "Версия схемы");
define("NETCAT_MODULE_NETSHOP_IMPORT_COMMERCEML_SCHEME_VER_0", "автоопределение");
define("NETCAT_MODULE_NETSHOP_IMPORT_COMMERCEML_SCHEME_VER_1", "1С версии 7.7");
define("NETCAT_MODULE_NETSHOP_IMPORT_COMMERCEML_SCHEME_VER_2", "1С версии 8.1");
define("NETCAT_MODULE_NETSHOP_IMPORT_SUBMIT", "  Импорт  ");
define("NETCAT_MODULE_NETSHOP_IMPORT_SOURCE_NAME", "Источник");
define("NETCAT_MODULE_NETSHOP_IMPORT_SOURCE_NEW", "Новый источник (введите название)");
define("NETCAT_MODULE_NETSHOP_IMPORT_SOURCE_WRONG", "Неверный источник данных");
define("NETCAT_MODULE_NETSHOP_IMPORT_FILE", "Файл");
define("NETCAT_MODULE_NETSHOP_IMPORT_ACTION_NONEXISTANT", "Что делать с позициями, которых нет в источнике");
define("NETCAT_MODULE_NETSHOP_IMPORT_ACTION_NONEXISTANT_DISABLE", "отключить");
define("NETCAT_MODULE_NETSHOP_IMPORT_ACTION_NONEXISTANT_DELETE", "удалить");
define("NETCAT_MODULE_NETSHOP_IMPORT_ACTION_NONEXISTANT_IGNORE", "оставить как есть");
define("NETCAT_MODULE_NETSHOP_IMPORT_AUTO_ADD_SECTIONS", "При автоимпорте создавать группы с компонентом:");
define("NETCAT_MODULE_NETSHOP_IMPORT_AUTO_ADD_SECTIONS_DONT_ADD", "Не добавлять");
define("NETCAT_MODULE_NETSHOP_IMPORT_AUTO_ADD_GOODS", "добавлять товары без проверки");
define("NETCAT_MODULE_NETSHOP_IMPORT_AUTO_MOVE_SECTIONS", "Синхронизировать изменения дерева групп");
define("NETCAT_MODULE_NETSHOP_IMPORT_DELETE_TMP_FILES", "Удалять временные файлы после импорта");
define("NETCAT_MODULE_NETSHOP_IMPORT_AUTO_RENAME_SUBDIVISIONS", "Переименовывать разделы, если изменены группы в источнике");
define("NETCAT_MODULE_NETSHOP_IMPORT_AUTO_CHANGE_SUBDIVISION_LINKS", "Менять ссылки на переименованные разделы");
define("NETCAT_MODULE_NETSHOP_IMPORT_DISABLE_OUT_OF_STOCK_ITEMS", "Отключать товары, у которых количество на складе не указано или равно нулю");

define("NETCAT_MODULE_NETSHOP_IMPORT_MAP_SECTION", "Укажите соответствие разделов источника разделам интернет-магазина");
define("NETCAT_MODULE_NETSHOP_IMPORT_MAP_PRICE", "Укажите соответствие типов цен полям шаблонов");
define("NETCAT_MODULE_NETSHOP_IMPORT_MAP_STORES", "Укажите соответствие остатков на складах полям компонента");
define("NETCAT_MODULE_NETSHOP_IMPORT_MAP_CHARACTERISTICS", "Укажите соответствие характеристик вариантов товара полям компонента");
define("NETCAT_MODULE_NETSHOP_IMPORT_REMAIN_IN_STORE", "Остаток на складе");
define("NETCAT_MODULE_NETSHOP_IMPORT_CREATE_SECTION", "Создать новый раздел");
define("NETCAT_MODULE_NETSHOP_IMPORT_CREATE_SECTION_PARENT", "Родительский раздел");
define("NETCAT_MODULE_NETSHOP_IMPORT_TEMPLATE", "Компонент");

define("NETCAT_MODULE_NETSHOP_IMPORT_SOURCE_TITLE", "Источник импортируемых данных");
define("NETCAT_MODULE_NETSHOP_IMPORT_FILE_UPLOAD_TITLE", "Загрузка файла с данными");
define("NETCAT_MODULE_NETSHOP_IMPORT_FILE_FTP_PATH", "Имя файла в директории ".$SUB_FOLDER.$HTTP_ROOT_PATH."tmp/");
define("NETCAT_MODULE_NETSHOP_IMPORT_ROOT_SUBDIVISION", "Для корректной загрузки ОБЯЗАТЕЛЬНО укажите ID корневого раздела магазина:");

define("NETCAT_MODULE_NETSHOP_IMPORT_XML_FILE", "Обработка импортируемого файла");
define("NETCAT_MODULE_NETSHOP_IMPORT_CATALOGUE_STRUCTURE", "Импорт структуры каталога");
define("NETCAT_MODULE_NETSHOP_IMPORT_OFFERS", "Импорт пакета предложений");
define("NETCAT_MODULE_NETSHOP_IMPORT_ORDERS", "Импорт заказов");
define("NETCAT_MODULE_NETSHOP_IMPORT_ORDERS_ID_MAP", "Для корректного импорта заказов вам необходимо настроить соответствие поля \"Ид\" товара");
define("NETCAT_MODULE_NETSHOP_IMPORT_COMMODITIES_IN_CATALOGUE", "Импорт объектов в каталог");
define("NETCAT_MODULE_NETSHOP_IMPORT_FIELDS_AND_TAGS_COMPLIANCE", "Соответствие XML-тегов полям в компоненте:");

define("NETCAT_MODULE_NETSHOP_IMPORT_IGNORE_SECTION", "Не вносить в каталог");

define("NETCAT_MODULE_NETSHOP_IMPORT_DONE", "Обработка источника завершена");

define("NETCAT_MODULE_NETSHOP_IMPORT_CACHE_CLEARED_PARTIAL", "Временные файлы удалены не полностью!");

define("NETCAT_MODULE_NETSHOP_PHP4_DOMXML_REQUIRED", "Импорт данных в формате XML невозможен, поскольку на сервере отсутствует библиотека DOMXML. Пожалуйста, обратитесь к Вашему хостинг-провайдеру для установки данной библиотеки.");

define("NETCAT_MODULE_NETSHOP_IMPORT_1C_LINK", "Для автоматической выгрузки данного источника данных на сайт из 1С:
<ol>
 <li>В 1С откройте меню <b>Сервис — Обмен данными в формате CommerceML — Выгрузка пакета коммерческих предложений</b></li>
 <li>Отметьте пункт <b>Отправить на сайт</b> и нажмите на многоточие (<b>...</b>)
 <li>В диалоговом окне нажмите <b>Новая строка</b>, введите наименование сайта.
     <br>В поле <b>Адрес</b> укажите:
     <br><b style='background:#DFDFDF'>%s</b>
     <br>Поля <b>Имя пользователя</b> и <b>Пароль доступа</b> оставьте пустыми.
</ol>
<b>Обратите внимание:</b> вновь созданные в 1С разделы не будут добавлены на
сайт, пока Вы снова не загрузите файл вручную через данный интерфейс.
Подробнее см. в документации к модулю.");

define("NETCAT_MODULE_NETSHOP_IMPORT_1C8_LINK", "Для автоматической выгрузки этого источника данных на сайт из 1С:
<ol>
 <li>В 1С8 откройте меню <b>Сервис</b> — <b>Обмен данными с WEB-сайтом</b> — <b>Настройка обмена данными с WEB-сайтом</b>;</li>
 <li>Отметьте пункт <b>Создать новую настройку обмена с WEB-сайтом</b> и нажмите <b>Далее</b>;</li>
 <li>В диалоговом окне укажите желаемые настройки обмена данными:
     <br>В поле <b>Адрес сайта</b> укажите:
     <br><b style='background:#DFDFDF'>%s</b>
     <br>Поля <b>Пользователь</b> и <b>Пароль</b> заполните в соответствии с настройками модуля Интернет-магазин (<b>SECRET_NAME</b> и <b>SECRET_KEY</b>).</li>
</ol>
<b>Обратите внимание:</b> вновь созданные в 1С8 разделы не будут добавлены на
сайт, пока Вы снова не загрузите файл вручную через данный интерфейс.
Подробнее см. в документации к модулю.");

define("NETCAT_MODULE_NETSHOP_DISCOUNT_EDIT", "Редактирование скидки");
define("NETCAT_MODULE_NETSHOP_DISCOUNT_MANUAL", "Размер скидки был указан оператором вручную");
define("NETCAT_MODULE_NETSHOP_APPLIES_TO_GOODS", "к отдельным товарам");
define("NETCAT_MODULE_NETSHOP_APPLIES_TO_CART", "ко всей корзине");

define("NETCAT_MODULE_NETSHOP_DISCOUNT_SELECT_FIELD", "выберите поле...");

define("NETCAT_MODULE_NETSHOP_CUSTOMER", "Заказчик");
define("NETCAT_MODULE_NETSHOP_ORDER_EDIT_TITLE", "Заказ №%s от %%d.%%m.%%Y");
define("NETCAT_MODULE_NETSHOP_SHOW_ORDER_STATUS", "Показать только заказы со статусом");
define("NETCAT_MODULE_NETSHOP_ORDER_NEW", "новый");
define("NETCAT_MODULE_NETSHOP_ORDER_ANY", "любой");
define("NETCAT_MODULE_NETSHOP_ORDER_FILTER", "Фильтр заказов");
define("NETCAT_MODULE_NETSHOP_ORDER_SEARCH", "Номер, клиент, телефон или e-mail");
define("NETCAT_MODULE_NETSHOP_ORDER_NO_INFOBLOCK", "На выбранном сайте нет ни одного раздела с инфоблоком «Заказ»");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_FILTER", "Способ доставки");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_ALL", "любой");
define("NETCAT_MODULE_NETSHOP_DATE_FILTER", "Дата");
define("NETCAT_MODULE_NETSHOP_DATE_FILTER_FROM", "с");
define("NETCAT_MODULE_NETSHOP_DATE_FILTER_TO", "по");
define("NETCAT_MODULE_NETSHOP_PRICE_FILTER", "Сумма");
define("NETCAT_MODULE_NETSHOP_PRICE_FILTER_FROM", "от");
define("NETCAT_MODULE_NETSHOP_PRICE_FILTER_TO", "до");
define("NETCAT_MODULE_NETSHOP_ORDER_FILTER_SUBMIT", "Искать");
define("NETCAT_MODULE_NETSHOP_ORDER_FILTER_RESET", "Очистить форму");
define("NETCAT_MODULE_NETSHOP_ORDER_FILTER_RESET_CONFIRM", "Вы уверены, что хотите очистить форму?");
define("NETCAT_MODULE_NETSHOP_ORDER_BACK_TO_LIST", "К списку заказов");
define("NETCAT_MODULE_NETSHOP_ORDER_DUPLICATE", "Продублировать заказ");
define("NETCAT_MODULE_NETSHOP_ORDER_CREATE", "Добавить заказ");
define("NETCAT_MODULE_NETSHOP_ORDER_EDIT", "Редактировать заказ");
define("NETCAT_MODULE_NETSHOP_ORDER_REMOVE_ITEM", "Удалить товар из заказа");
define("NETCAT_MODULE_NETSHOP_ORDER_REMOVE_ITEM_CONFIRM", "Удалить «%s» из заказа?");
define("NETCAT_MODULE_NETSHOP_ORDER_ADD_ITEM", "Добавить товар в заказ");
define("NETCAT_MODULE_NETSHOP_ORDER_DELETE_SELECTED", "Удалить отмеченные");
define("NETCAT_MODULE_NETSHOP_ORDER_DELETE_SELECTED_CONFIRM", "Удалить отмеченные заказы?");
define("NETCAT_MODULE_NETSHOP_ORDER_MERGE_SELECTED", "Объединить отмеченные");
define("NETCAT_MODULE_NETSHOP_ORDER_MERGE", "Объединение заказов");
define("NETCAT_MODULE_NETSHOP_ORDER_MERGE_CANCEL", "Отмена");
define("NETCAT_MODULE_NETSHOP_ORDER_MERGE_SUBMIT", "Продолжить");
define("NETCAT_MODULE_NETSHOP_ORDER_MERGE_DESCRIPTION", "Будет создан новый заказ, содержащий все товары из выбранных заказов.");
define("NETCAT_MODULE_NETSHOP_ORDER_MERGE_BASE", "Заказ, из которого будут взяты контактные данные, способ оплаты и доставки:");
define("NETCAT_MODULE_NETSHOP_ORDER_MERGE_SET_STATUS", "Установить статус для объединяемых заказов:");
define("NETCAT_MODULE_NETSHOP_ORDER_MERGE_NO_STATUS_CHANGE", "не менять");

define("NETCAT_MODULE_NETSHOP_EQUALS", "равно");
define("NETCAT_MODULE_NETSHOP_MULTIPLY", "умножить");
define("NETCAT_MODULE_NETSHOP_ADD", "прибавить");
define("NETCAT_MODULE_NETSHOP_SUBSTRACT", "вычесть");

define("NETCAT_MODULE_NETSHOP_OR", "или");


define("NETCAT_MODULE_NETSHOP_ITEM_MINIMAL_PRICE_REACHED", "При расчете скидки достигнут предел минимальной цены товара (%s)");
define("NETCAT_MODULE_NETSHOP_CART_MINIMAL_PRICE_REACHED", "При расчете скидки достигнут предел минимальной стоимости товаров в корзине (%s)");


define("NETCAT_MODULE_NETSHOP_SHOP_SETTINGS", "Настройки интернет-магазина");
define("NETCAT_MODULE_NETSHOP_DEPARTMENT_SETTINGS", "Настройки раздела интернет-магазина");
define("NETCAT_MODULE_NETSHOP_CURRENCY_SETTINGS", "Курсы валют");

// Эти настройки по умолчанию (применяются, если не указаны соотв. настройки валют)
define("NETCAT_MODULE_NETSHOP_CURRENCY_FORMAT", "%s #"); // # - знак валюты
define("NETCAT_MODULE_NETSHOP_CURRENCY_DECIMALS", 2); // количество знаков после запятой
define("NETCAT_MODULE_NETSHOP_CURRENCY_DEC_POINT", ","); // разделитель целой и дробной части числа
define("NETCAT_MODULE_NETSHOP_CURRENCY_THOUSAND_SEP", ""); // разделитель групп разрядов (оставьте пустым!)
// скрипт получения курсов валют:
define("NETCAT_MODULE_NETSHOP_CURRENCY_VAR_NOT_SET", "Не указано значение переменной %s");
define("NETCAT_MODULE_NETSHOP_CURRENCY_NOTHING_TO_FETCH", "Все курсы валют определены вручную");
define("NETCAT_MODULE_NETSHOP_CURRENCY_FETCH_NOTFOUND", "Не удалось получить источник курсов валют");
define("NETCAT_MODULE_NETSHOP_CURRENCY_FETCH_PARSING_ERROR", "Курсы валют не получены (ошибка при обработке источника)");
define("NETCAT_MODULE_NETSHOP_CURRENCY_FETCH_OK", "Получены курсы валют: %s");

define("NETCAT_MODULE_NETSHOP_ERROR_CART_EMPTY", "Невозможно оформить заказ, поскольку корзина пуста");

define("NETCAT_MODULE_NETSHOP_EMAIL_TO_MANAGER_HEADER", "Заказ с сайта %s");


define("NETCAT_MODULE_NETSHOP_PAYMENT_NO_SETTINGS", "Не указаны настройки платежной системы %s");
define("NETCAT_MODULE_NETSHOP_PAYMENT_NO_CURRENCY", "Не указана валюта магазина");
// №, название магазина
define("NETCAT_MODULE_NETSHOP_PAYMENT_DESCRIPTION", "Оплата заказа №%s (%s)");
define("NETCAT_MODULE_NETSHOP_PAYMENT_SUBMIT", "Оплатить");

// название платежной системы, сумма, дата, номер транзакции, id покупателя
define("NETCAT_MODULE_NETSHOP_PAYMENT_LOG", "Оплачено через %s: %s %s (ID транзакции: %s, ID покупателя: %s)");
define("NETCAT_MODULE_NETSHOP_PAYED_ON", "Оплачено %d.%m.%Y");
define("NETCAT_MODULE_NETSHOP_PAYMENT_DOCUMENT", "платежный документ № ");


define("NETCAT_MODULE_NETSHOP_CART_EMPTY", "Ваша корзина пуста");
define("NETCAT_MODULE_NETSHOP_CART_CONTENTS", "<a href='%s'>в Вашей корзине</a>: %s на сумму <b>%s</b>");
define("NETCAT_MODULE_NETSHOP_CART_CHECKOUT", "Оформить заказ");

define("NETCAT_MODULE_NETSHOP_NO_RIGTHS", "У Вас нет прав для доступа к данной информации");

define("NETCAT_MODULE_NETSHOP_SOURCES", "Источники");
define("NETCAT_MODULE_NETSHOP_SOURCES_NOT_EXISTS_SOURCE", "Выбран несуществующий источник");
define("NETCAT_MODULE_NETSHOP_SOURCES_SOURCES_NOT_SELECTED", "Вы не выбрали ни одного источника");
define("NETCAT_MODULE_NETSHOP_SOURCES_SOURCES_DELETED", "Источники успешно удалены");
define("NETCAT_MODULE_NETSHOP_SOURCES_SOURCES_DELETE_ERROR", "Произошла ошибка при удалении источников");
define("NETCAT_MODULE_NETSHOP_SOURCES_SOURCE_SAVED", "Настройки сохранены");
define("NETCAT_MODULE_NETSHOP_SOURCES_SOURCE_NOT_SAVED", "Произошла ошибка при сохранении настроек");
define("NETCAT_MODULE_NETSHOP_SOURCES_MAPPING_SAVED", "Соответствия сохранены");
define("NETCAT_MODULE_NETSHOP_SOURCES_MAPPING_NOT_SAVED", "Произошла ошибка при сохранении соответствий");
define("NETCAT_MODULE_NETSHOP_SOURCES_NOT_EXISTS_STORE", "Выбран несуществующий склад");
define("NETCAT_MODULE_NETSHOP_SOURCES_SOURCE_NAME", "Название источника");
define("NETCAT_MODULE_NETSHOP_SOURCES_CATALOGUE_ID", "ID сайта");
define("NETCAT_MODULE_NETSHOP_SOURCES_GOODS_IMPORTED", "Импортировано товаров");
define("NETCAT_MODULE_NETSHOP_SOURCES_STORES_IMPORTED", "Импортировано складов");
define("NETCAT_MODULE_NETSHOP_SOURCES_LAST_SYNC", "Последняя синхронизация");
define("NETCAT_MODULE_NETSHOP_SOURCES_EDIT_MAPPING", "Редактировать<br>соотвествия");
define("NETCAT_MODULE_NETSHOP_SOURCES_EDIT", "Редактировать");
define("NETCAT_MODULE_NETSHOP_SOURCES_FIELD_NOT_SELECTED", "Не выбрано");
define("NETCAT_MODULE_NETSHOP_SOURCES_DELETE_SOURCE", "Удалить источник");
define("NETCAT_MODULE_NETSHOP_SOURCES_NO_SOURCES", "Нет источников");
define("NETCAT_MODULE_NETSHOP_SOURCES_NO_SOURCES_MESSAGE", 'На этой странице отображаются созданные источники обмена данными с 1С, МойСклад и другими системами, поддерживающими обмен данными в формате CommerceML.<br>
Для создания нового источника перейдите в раздел: ');
define("NETCAT_MODULE_NETSHOP_SOURCES_DELETE_SELECTED", "Удалить выбранные");
define("NETCAT_MODULE_NETSHOP_SOURCES_SAVE", "Сохранить");
define("NETCAT_MODULE_NETSHOP_SOURCES_REALLY_WANT_TO_DELETE_SOURCES", "Вы действительно желаете удалить следующие источники?");
define("NETCAT_MODULE_NETSHOP_SOURCES_BACK", "Назад");
define("NETCAT_MODULE_NETSHOP_SOURCES_CANCEL", "Отмена");
define("NETCAT_MODULE_NETSHOP_SOURCES_DELETE_CONFIRM", "Подтвердить удаление");
define("NETCAT_MODULE_NETSHOP_SOURCES_SOURCE", "Источник");
define("NETCAT_MODULE_NETSHOP_SOURCES_SETTINGS", "Настройки");
define("NETCAT_MODULE_NETSHOP_SOURCES_MANUAL_SYNC", "Ссылки для ручной синхронизации");
define("NETCAT_MODULE_NETSHOP_SOURCES_OWNER", "Владелец");
define("NETCAT_MODULE_NETSHOP_SOURCES_ID", "Идентификатор");
define("NETCAT_MODULE_NETSHOP_SOURCES_NAME", "Наименование");
define("NETCAT_MODULE_NETSHOP_SOURCES_OFFICIAL_NAME", "Официальное наименование");
define("NETCAT_MODULE_NETSHOP_SOURCES_ADDRESS", "Адрес");
define("NETCAT_MODULE_NETSHOP_SOURCES_INN", "ИНН");
define("NETCAT_MODULE_NETSHOP_SOURCES_KPP", "КПП");
define("NETCAT_MODULE_NETSHOP_SOURCES_INFORMATION_NOT_AVAILABLE", "Информация недоступна");
define("NETCAT_MODULE_NETSHOP_SOURCES_INFORMATION", "Информация");
define("NETCAT_MODULE_NETSHOP_SOURCES_IMPORTED_STORES", "Импортированные склады");
define("NETCAT_MODULE_NETSHOP_SOURCES_STORE_NAME", "Название склада");
define("NETCAT_MODULE_NETSHOP_SOURCES_1C_ID", "Идентификатор CommerceML");
define("NETCAT_MODULE_NETSHOP_SOURCES_REMAIN_GOODS", "Остаток товаров");
define("NETCAT_MODULE_NETSHOP_SOURCES_VIEW_GOODS", "Посмотреть товары");
define("NETCAT_MODULE_NETSHOP_SOURCES_STORES_NOT_IMPORTED", "Не импортировано ни одного склада");
define("NETCAT_MODULE_NETSHOP_SOURCES_GO_BACK", "Вернуться назад");
define("NETCAT_MODULE_NETSHOP_SOURCES_STORE_REMAIN", "Остатки по складу");
define("NETCAT_MODULE_NETSHOP_SOURCES_ITEM", "Товар");
define("NETCAT_MODULE_NETSHOP_SOURCES_REMAIN", "Остаток");
define("NETCAT_MODULE_NETSHOP_SOURCES_EXPORT_CATALOGUE", "Экспортировать каталог в файл CommerceML2");
define("NETCAT_MODULE_NETSHOP_SOURCES_EXPORT_OFFERS", "Экспортировать предложения в файл CommerceML2");
define("NETCAT_MODULE_NETSHOP_SOURCES_EXPORT_ORDERS", "Экспортировать заказы в файл CommerceML2");

define("NETCAT_MODULE_NETSHOP_SETUP", "Установка модуля на сайт");
define("NETCAT_MODULE_NETSHOP_SETUP_ON_SITE", "На какой сайт Вы хотите установить интернет-магазин?");
define("NETCAT_MODULE_NETSHOP_SETUP_EVERYWHERE", "Интернет-магазин установлен на всех сайтах в системе.");
define("NETCAT_MODULE_NETSHOP_SETUP_SHOP_SETTINGS_REDIRECT", "После нажатия кнопки &laquo;OK&raquo; вы будете переадресованы на страницу редактирования настроек интернет-магазина. Пожалуйста, заполните все необходимые поля и нажмите кнопку &laquo;Добавить&raquo;, иначе модуль не будет работать на указанном сайте.");

define("NETCAT_MODULE_NETSHOP_PREV_ORDERS_SUM", "Сумма пред. покупок");
define("NETCAT_MODULE_NETSHOP_NOT_REGISTERED_USER", "Незарегистрированный пользователь");

define("NETCAT_MODULE_NETSHOP_NETSHOP", "Интернет-магазин");
define("NETCAT_MODULE_NETSHOP_GOODS_CATALOGUE", "Каталог товаров");
define("NETCAT_MODULE_NETSHOP_CART", "Корзина");
define("NETCAT_MODULE_NETSHOP_MAKE_ORDER", 'Оформление заказа');
define("NETCAT_MODULE_NETSHOP_EURO", "евро, евро, евро, M");
define("NETCAT_MODULE_NETSHOP_EUROCENT", "евроцент, евроцента, евроцентов, M");
define("NETCAT_MODULE_NETSHOP_USD", "доллар, доллара, долларов, M");
define("NETCAT_MODULE_NETSHOP_CENT", "цент, цента, центов, M");
define("NETCAT_MODULE_NETSHOP_RUR", "рубль, рубля, рублей, M");
define("NETCAT_MODULE_NETSHOP_COPECK", "копейка, копейки, копеек, F");
define("NETCAT_MODULE_NETSHOP_CB_RATES", 'Курсы валют ЦБ');
define("NETCAT_MODULE_NETSHOP_PRICE_GROUPS", 'Цены для разных групп пользователей');
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHODS", 'Способы доставки');
define("NETCAT_MODULE_NETSHOP_BY_COURIER", "Курьером");
define("NETCAT_MODULE_NETSHOP_PAYMENT_METHODS", 'Способы оплаты');
define("NETCAT_MODULE_NETSHOP_CREDIT_CARD", "Пластиковая карта");
define("NETCAT_MODULE_NETSHOP_CREDIT_CARD_DESCRIPTION", "VISA, MasterCard, EuroCard, JCB, DCL (через систему ASSIST)");
define("NETCAT_MODULE_NETSHOP_YANDEX_MONEY", "Яндекс.Деньги");
define("NETCAT_MODULE_NETSHOP_RBK_MONEY", "RBK Money");
define("NETCAT_MODULE_NETSHOP_WEBMONEY", "Webmoney");
define("NETCAT_MODULE_NETSHOP_CASHLESS", "Безналичный расчет");
define("NETCAT_MODULE_NETSHOP_SBERBANK", "Через Сбербанк");
define("NETCAT_MODULE_NETSHOP_CASH", "Наличными");
define("NETCAT_MODULE_NETSHOP_EMAIL_TEMPLATES", 'Шаблоны писем');
define("NETCAT_MODULE_NETSHOP_ORDER_EMAIL_HEADER", "Ваш заказ в %SHOP_SHOPNAME%");
define("NETCAT_MODULE_NETSHOP_STORES", "Склады");
define("NETCAT_MODULE_NETSHOP_MAIN_STORE", "Основной склад");

define("NETCAT_MODULE_NETSHOP_UNITS", "Единицы измерения");
define("NETCAT_MODULE_NETSHOP_PCS", "шт");
define("NETCAT_MODULE_NETSHOP_ORDER_STATUS", "Статус заказа");
define("NETCAT_MODULE_NETSHOP_ACCEPTED", "принят");
define("NETCAT_MODULE_NETSHOP_REJECTED", "отклонен");
define("NETCAT_MODULE_NETSHOP_PAYED", "оплачен");
define("NETCAT_MODULE_NETSHOP_DONE", "завершен");

define("NETCAT_MODULE_NETSHOP_FULL_NAME", "ФИО");


define("NETCAT_MODULE_NETSHOP_ORDER_EMAIL_BODY", "Уважаемый %CUSTOMER_CONTACTNAME%!

Ваш заказ принят к обработке.

%CART_CONTENTS%
%CART_DISCOUNTS%
%CART_DELIVERY%%CART_PAYMENT%

ИТОГО: %CART_COUNT% на сумму %CART_SUM%

Для того, чтобы уточнить Ваш заказ, с Вами в самое ближайшее время свяжутся
наши менеджеры.


С уважением,                     Тел.: %SHOP_PHONE%
%SHOP_SHOPNAME%");


define("NETCAT_MODULE_NETSHOP_NO_PREV_ORDERS_STATUS_ID", "В настройках модуля &quot;Интернет-магазин&quot; не установлен параметр PREV_ORDERS_SUM_STATUS. Подробнее см. в документации по модулю.");

define("NETCAT_MODULE_NETSHOP_MONTHS_GENITIVE", '/января/февраля/марта/апреля/мая/июня/июля/августа/сентября/октября/ноября/декабря');

define("NETCAT_MODULE_NETSHOP_1C_ID", "Ид");
define("NETCAT_MODULE_NETSHOP_1C_CLASSIFICATOR_ID", "ИдКлассификатора");
define("NETCAT_MODULE_NETSHOP_1C_NAME", "Наименование");
define("NETCAT_MODULE_NETSHOP_1C_PRICE", "Цена");
define("NETCAT_MODULE_NETSHOP_1C_PRICES", "Цены");
define("NETCAT_MODULE_NETSHOP_1C_PRICE_TYPE", "ТипЦены");
define("NETCAT_MODULE_NETSHOP_1C_PRICES_TYPE", "ТипыЦен");
define("NETCAT_MODULE_NETSHOP_1C_PRICE_TAG_UNIT", "Единица");
define("NETCAT_MODULE_NETSHOP_1C_PRICE_TAG_COEFFICIENT", "Коэффициент");
define("NETCAT_MODULE_NETSHOP_1C_STORES", "Склады");
define("NETCAT_MODULE_NETSHOP_1C_STORE", "Склад");
define("NETCAT_MODULE_NETSHOP_1C_STORE_ID", "СкладИД");
define("NETCAT_MODULE_NETSHOP_1C_STORE_QTY", "КоличествоОстаток");
define("NETCAT_MODULE_NETSHOP_1C_STORE_ID_2", "ИдСклада");
define("NETCAT_MODULE_NETSHOP_1C_STORE_QTY_2", "КоличествоНаСкладе");
define("NETCAT_MODULE_NETSHOP_1C_STORE_REMAIN", "ОстаткиПоСкладам");
define("NETCAT_MODULE_NETSHOP_1C_REMAIN", "Остаток");
define("NETCAT_MODULE_NETSHOP_1C_PRICE_TYPE_ID", "ИдТипаЦены");
define("NETCAT_MODULE_NETSHOP_1C_PRICE_UNIT", "ЦенаЗаЕдиницу");
define("NETCAT_MODULE_NETSHOP_1C_CURRENCY", "Валюта");
define("NETCAT_MODULE_NETSHOP_1C_CURRENCY_DEFAULT", "руб");
define("NETCAT_MODULE_NETSHOP_1C_CURRENCY_DEFAULT_2", "р");
define("NETCAT_MODULE_NETSHOP_1C_GROUP", "Группа");
define("NETCAT_MODULE_NETSHOP_1C_GROUPS", "Группы");
define("NETCAT_MODULE_NETSHOP_1C_PRODUCT_CHARS", "ХарактеристикиТовара");
define("NETCAT_MODULE_NETSHOP_1C_PRODUCT_CHAR", "ХарактеристикаТовара");
define("NETCAT_MODULE_NETSHOP_1C_VALUE", "Значение");
define("NETCAT_MODULE_NETSHOP_1C_REC_VALUES", "ЗначенияРеквизитов");
define("NETCAT_MODULE_NETSHOP_1C_REC_VALUE", "ЗначениеРеквизита");
define("NETCAT_MODULE_NETSHOP_1C_PROPERTIES_VALUES", "ЗначенияСвойств");
define("NETCAT_MODULE_NETSHOP_1C_PROPERTIES_VALUE", "ЗначенияСвойства");
define("NETCAT_MODULE_NETSHOP_1C_TAX", "СтавкаНалога");
define("NETCAT_MODULE_NETSHOP_1C_TAXES", "СтавкиНалогов");
define("NETCAT_MODULE_NETSHOP_1C_RATE", "Ставка");
define("NETCAT_MODULE_NETSHOP_1C_BASE_UNIT", "БазоваяЕдиница");
define("NETCAT_MODULE_NETSHOP_1C_IMG", "Картинка");
define("NETCAT_MODULE_NETSHOP_1C_QTY", "Количество");
define("NETCAT_MODULE_NETSHOP_1C_OFFICIAL_NAME", "ОфициальноеНаименование");
define("NETCAT_MODULE_NETSHOP_1C_ADDRESS", "ЮридическийАдрес");
define("NETCAT_MODULE_NETSHOP_1C_ADDRESS_VIEW", "Представление");
define("NETCAT_MODULE_NETSHOP_1C_INN", "ИНН");
define("NETCAT_MODULE_NETSHOP_1C_KPP", "КПП");

define("NETCAT_MODULE_NETSHOP_RESPONSE_STAT_MESSAGE", "Статус заказа в системе");
define("NETCAT_MODULE_NETSHOP_RESPONSE_COMMENT", "пользовательский комментарий");
define("NETCAT_MODULE_NETSHOP_ORDERS_NUMBER", "Заказ №");
define("NETCAT_MODULE_NETSHOP_TRANSACTION_NUMBER", "номер транзакции в системе");
define("NETCAT_MODULE_NETSHOP_TELEPHONE_NUMBER", "Введите номер Вашего кошелька в системе QIWI");
define("NETCAT_MODULE_NETSHOP_NO_PAYMENT_SYSTEM", "Платежная система не найдена");

define("NETCAT_MODULE_NETSHOP_ERROR_ASSIST", "Введите идентификатор в ASSIST");
define("NETCAT_MODULE_NETSHOP_ERROR_PAYPAL_MAIL", "Заполните поле Paypal Log-in Email и выберите валюту магазина");
define("NETCAT_MODULE_NETSHOP_ERROR_PAYPAL_RATES", "Необходимо получить котировки валют");
define("NETCAT_MODULE_NETSHOP_ERROR_QIWI", "Укажите номер магазина и пароль в системе QIWI");
define("NETCAT_MODULE_NETSHOP_ERROR_MAIL", "Укажите номер магазина, ключ магазина и криптографический хэш от ключа в системе Деньги@mail.ru");
define("NETCAT_MODULE_NETSHOP_ERROR_ROBOKASSA", "Укажите логин, пароль #1 и пароль #2 в системе Robokassa");
define("NETCAT_MODULE_NETSHOP_ERROR_WEBMONEY", "Укажите кошелек продавца и secret key в системе Webmoney");
define("NETCAT_MODULE_NETSHOP_ERROR_YANDEX", "Заполните настройки Яндекс-Деньги");
define("NETCAT_MODULE_NETSHOP_ERROR_PAYMASTER", "Укажите идентификатор магазина и секретное слово в системе Paymaster");

define("NETCAT_MODULE_NETSHOP_SBERBANK_PRINT_BILL", "Распечатать квитанцию");
define("NETCAT_MODULE_NETSHOP_SBERBANK_NOTICE", "ИЗВЕЩЕНИЕ");
define("NETCAT_MODULE_NETSHOP_SBERBANK_CASHIER", "Кассир");
define("NETCAT_MODULE_NETSHOP_SBERBANK_PAYMENT_RECEIVER", "Получатель платежа");
define("NETCAT_MODULE_NETSHOP_SBERBANK_INN", "ИНН");
define("NETCAT_MODULE_NETSHOP_SBERBANK_RS", "Р/c");
define("NETCAT_MODULE_NETSHOP_SBERBANK_KS", "Корр.сч.");
define("NETCAT_MODULE_NETSHOP_SBERBANK_KPP", "КПП");
define("NETCAT_MODULE_NETSHOP_SBERBANK_BIK", "БИК");
define("NETCAT_MODULE_NETSHOP_SBERBANK_NAME_ADDR", "фамилия, и. о., адрес");
define("NETCAT_MODULE_NETSHOP_SBERBANK_PAYMENT_TYPE", "Вид платежа");
define("NETCAT_MODULE_NETSHOP_SBERBANK_DATE", "Дата");
define("NETCAT_MODULE_NETSHOP_SBERBANK_AMOUNT", "Сумма");
define("NETCAT_MODULE_NETSHOP_SBERBANK_PAYER", "Плательщик");
define("NETCAT_MODULE_NETSHOP_SBERBANK_RECEIPT", "КВИТАНЦИЯ");

define("NETCAT_MODULE_NETSHOP_BANK_PRINT_BILL", "Распечатать счет");
define("NETCAT_MODULE_NETSHOP_BANK_ADDRESS", "Адрес");
define("NETCAT_MODULE_NETSHOP_BANK_PHONE", "тел.");
define("NETCAT_MODULE_NETSHOP_BANK_EXAMPLE", "Образец заполнения платежного поручения");
define("NETCAT_MODULE_NETSHOP_BANK_INN", "ИНН");
define("NETCAT_MODULE_NETSHOP_BANK_KPP", "КПП");
define("NETCAT_MODULE_NETSHOP_BANK_BILL", "Сч.");
define("NETCAT_MODULE_NETSHOP_BANK_RECEIVER", "Получатель");
define("NETCAT_MODULE_NETSHOP_BANK_RECEIVER_BANK", "Банк получателя");
define("NETCAT_MODULE_NETSHOP_BANK_BIK", "БИК");
define("NETCAT_MODULE_NETSHOP_BANK_BILL_FULL", "СЧЕТ");
define("NETCAT_MODULE_NETSHOP_BANK_BILL_SUFFIX", "/И");
define("NETCAT_MODULE_NETSHOP_BANK_FROM", "от");
define("NETCAT_MODULE_NETSHOP_BANK_YEAR", "г.");
define("NETCAT_MODULE_NETSHOP_BANK_CUSTOMER", "Заказчик");
define("NETCAT_MODULE_NETSHOP_BANK_PAYER", "Плательщик");
define("NETCAT_MODULE_NETSHOP_BANK_GOODS_TITLE", "Наименование<br>товара");
define("NETCAT_MODULE_NETSHOP_BANK_UNIT", "Единица<br>измерения");
define("NETCAT_MODULE_NETSHOP_BANK_AMOUNT", "Количество");
define("NETCAT_MODULE_NETSHOP_BANK_PRICE", "Цена");
define("NETCAT_MODULE_NETSHOP_BANK_SUM", "Сумма");
define("NETCAT_MODULE_NETSHOP_BANK_SHIPPING", "Доставка");
define("NETCAT_MODULE_NETSHOP_BANK_TOTAL", "Итого");
define("NETCAT_MODULE_NETSHOP_BANK_VAT_INCLUDED", "В том числе НДС");
define("NETCAT_MODULE_NETSHOP_BANK_VAT_NOT_INCLUDED", "НДС не предусмотрен");
define("NETCAT_MODULE_NETSHOP_BANK_TOTAL_SUM", "Всего к оплате");
define("NETCAT_MODULE_NETSHOP_BANK_TOTAL_TITLES", "Всего наименований");
define("NETCAT_MODULE_NETSHOP_BANK_WITH_SUM", "на сумму");
define("NETCAT_MODULE_NETSHOP_BANK_TIP", "Оплата в рублях по курсу ЦБ РФ на день выставления счета");

define("NETCAT_MODULE_NETSHOP_CHECKOUT_ORDER_DATA_SECTION", "Контактные данные и адрес доставки");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_CUSTOMER_DATA_SECTION", "Данные заказчика");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_CUSTOMER_ADDRESS_SECTION", "Адрес доставки");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_ITEMS_SECTION", "Состав заказа");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_DELIVERY_METHOD_SECTION", "Способ доставки");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_PAYMENT_METHOD_SECTION", "Способ оплаты");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_DELIVERY_AND_PAYMENT_METHOD_SECTION", "Способы доставки и оплаты");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_NO_AVAILABLE_DELIVERY_METHODS",
"Не удалось подобрать способ доставки вашего заказа по указанному адресу. Для уточнения способа доставки с вами свяжется сотрудник нашего магазина.");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_DELIVERY_ESTIMATE_ERROR", "Не удалось вычислить стоимость и сроки доставки");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_DELIVERY_ESTIMATE_ERROR_NO_RESPONSE", "не получен ответ от сервера");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_DELIVERY_ESTIMATE_PRICE", "Стоимость: ");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_DELIVERY_ESTIMATE_PRICE_UNKNOWN", "неизвестно");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_DELIVERY_ESTIMATE_DATE", "Ожидаемая дата доставки: ");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_DELIVERY_ESTIMATE_DATES", "Ожидаемые даты доставки: ");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_DELIVERY_ESTIMATE_LOADING", " (данные загружаются) ");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_NO_AVAILABLE_PAYMENT_METHODS",
"Не удалось подобрать способ оплаты для вашего заказа. Для уточнения способа оплаты с вами свяжется сотрудник нашего магазина.");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_NO_AVAILABLE_PAYMENT_METHODS_ADMIN", "Нет подходящего способа оплаты");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_PAYMENT_EXTRA_CHARGE", "Дополнительный сбор: ");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_CONFIRMATION_SECTION", "Подтверждение заказа");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_PLEASE_REVIEW_ORDER", "Пожалуйста, проверьте правильность указанных данных.");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_TOTALS_SECTION", "Итого");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_CART_TOTALS", "Стоимость товаров: ");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_ITEM_TOTALS", "Стоимость всех товаров");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_ORDER_TOTALS", "Общая сумма к оплате: ");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_BUTTON", "Оформить заказ");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_PREV_PAGE_BUTTON", "Назад");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_NEXT_PAGE_BUTTON", "Далее");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_CHANGE_BUTTON", "Изменить");

define("NETCAT_MODULE_NETSHOP_CHECKOUT_DELIVERY_INCORRECT_METHOD", "Ошибка: выбран недоступный для вашего заказа способ доставки");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_SELECTED_DELIVERY_METHOD", "Способ доставки заказа: ");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_SELECTED_DELIVERY_POINT", "Пункт выдачи заказа: ");

define("NETCAT_MODULE_NETSHOP_CHECKOUT_PAYMENT_INCORRECT_METHOD", "Ошибка: выбран недоступный для вашего заказа способ оплаты");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_SELECTED_PAYMENT_METHOD", "Способ оплаты заказа: ");

define("NETCAT_MODULE_NETSHOP_CHECKOUT_BOOLEAN_FIELD_YES", "да");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_BOOLEAN_FIELD_NO", "нет");

define("NETCAT_MODULE_NETSHOP_CHECKOUT_INCORRECT_DELIVERY_METHOD", "Указан недопустимый способ доставки.");
define("NETCAT_MODULE_NETSHOP_CHECKOUT_INCORRECT_PAYMENT_METHOD", "Указан недопустимый способ оплаты.");

define("NETCAT_MODULE_NETSHOP_ITEM_CANNOT_BE_ORDERED", "Товар «%s» в настоящее время недоступен для заказа.");
define("NETCAT_MODULE_NETSHOP_ITEM_QTY_CHANGED", "Количество товара «%s» в вашей корзине было изменено, так как количество товара на складе менее выбранного вами.");

define("NETCAT_MODULE_NETSHOP_NO_PAYMENT_MODULE", "Интеграция с платёжными системами отключена, поскольку в вашей редакции системы отсутствует модуль «Приём платежей»");
define("NETCAT_MODULE_NETSHOP_PAYMENT_METHOD_PAYMENT_SYSTEM", "Платёжная система");
define("NETCAT_MODULE_NETSHOP_PAYMENT_METHOD_NO_PAYMENT_SYSTEM_OPTION", "-- нет --");
define("NETCAT_MODULE_NETSHOP_PAYMENT_METHOD_EXTRA_CHARGE_ABSOLUTE", "Дополнительный сбор (абсолютная величина)");
define("NETCAT_MODULE_NETSHOP_PAYMENT_METHOD_EXTRA_CHARGE_RELATIVE", "Дополнительный сбор (процент от общей стоимости заказа)");
define("NETCAT_MODULE_NETSHOP_PAYMENT_METHOD_DELIVERY", "возможность и стоимость оплаты при получении определяет служба доставки");

define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD", "Способ доставки");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE", "Способ автоматического расчёта стоимости доставки");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_NO_SERVICE_OPTION", "-- нет --");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_EXTRA_CHARGE_ABSOLUTE", "Дополнительный сбор (абсолютная величина)");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_EXTRA_CHARGE_RELATIVE", "Дополнительный сбор (процент от общей стоимости заказа)");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_COST", "Стоимость");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_CALCULATED", "Автоматический расчёт");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_MIN_DAYS", "Минимальное число дней для доставки (если срок доставки рассчитывается автоматически, прибавляется к нему)");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_MAX_DAYS", "Максимальное число дней для доставки (если срок доставки рассчитывается автоматически, прибавляется к нему)");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SHIPMENT_DAYS", "Дни, по которым производится отправка");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SAME_DAY_SHIPMENT_TIME", "Время, до которого возможна отправка в тот же день");

define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_FROM_CITY", "Город отправки");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_TO_REGION", "Регион (область) доставки");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_TO_DISTRICT", "Район региона доставки");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_TO_CITY", "Город доставки");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_TO_ADDRESS", "Адрес доставки");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_TO_ZIP_CODE", "Индекс получателя");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_INCORRECT_ID", "указан некорректный способ доставки");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_INCORRECT_METHOD_ID", "указан некорректный способ доставки");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_INCORRECT_WEIGHT", "вес посылки вне допустимых значений");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_INCORRECT_RECIPIENT_ADDRESS", "не удалось найти адрес доставки");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_INCORRECT_SENDER_ADDRESS", "не удалось найти адрес отправки");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_INCORRECT_REMOTE_SERVER_RESPONSE", "ошибка в ответе удалённого сервера");
define("NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_NO_REMOTE_SERVER_RESPONSE", "не получен ответ от удалённого сервера");

define("NETCAT_MODULE_NETSHOP_DELIVERY_FREE_OF_CHARGE", "бесплатно");
define("NETCAT_MODULE_NETSHOP_DELIVERY_DISCOUNT_STRING", "(скидка: %s)");


define("NETCAT_MODULE_NETSHOP_GENITIVE_DAY_FORMAT", "j"); // English: "jS"  (format as specified for the date() function)
define("NETCAT_MODULE_NETSHOP_DATE_RANGE_FORMAT", "%s %s — %s %s"); // day 1, month1, day 2, month 2. English: '%2$s, %1$2 to %3$s %2$s'
define("NETCAT_MODULE_NETSHOP_DATE_RANGE_FORMAT_ONE_MONTH", "%s — %s %s"); // day 1, day 2, month. English: '%3$s, %1$s – %3$s, %2$s'
define("NETCAT_MODULE_NETSHOP_DAY_AND_MONTH_FORMAT", "%s %s"); // day, month. English: '%2$s, %1$s'
define("NETCAT_MODULE_NETSHOP_SHORT_DAY_OF_WEEK_RANGE", "%s–%s"); // dow-dow
define("NETCAT_MODULE_NETSHOP_DATE_TODAY", "сегодня");
define("NETCAT_MODULE_NETSHOP_DATE_TOMORROW", "завтра");

// (Common)

define('NETCAT_MODULE_NETSHOP_DATETIME_FORMAT', 'd.m.Y H:i');
define('NETCAT_MODULE_NETSHOP_DATE_FORMAT', 'd.m.Y');

define("NETCAT_MODULE_NETSHOP_ITEM_VARIANTS", "Варианты товара");
define("NETCAT_MODULE_NETSHOP_ADD_ITEM_VARIANT", "Добавить один");
define("NETCAT_MODULE_NETSHOP_ADD_ITEM_VARIANTS", "Добавить несколько");
define("NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_ENABLE_ALL", "Включить все");
define("NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_DISABLE_ALL", "Выключить все");
define("NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_EDIT_ALL", "Редактировать все");
define("NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_DELETE_ALL", "Удалить все варианты");
define("NETCAT_MODULE_NETSHOP_ITEM_PARENT", "Основной вариант товара");
define("NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_PRIORITY", "Перетащите для изменения порядка, в котором выводятся варианты");
define("NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_PRICE_RANGE", "от&nbsp;<span class='tpl-value'>%s</span> до&nbsp;<span class='tpl-value'>%s</span>");
define("NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_ADD_MULTIPLE_HEADER", "Добавление вариантов товара «%s»");
define("NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_ADD_MULTIPLE_DESCRIPTION",
    "Выберите поля, по которым отличаются варианты товара, и укажите значения для выбранных полей через точку с запятой.<br>" .
    "Если выбрано несколько полей, будут созданы товары со всеми возможными сочетаниями указанных характеристик.<br>" .
    "Если у поля указано только одно значение, оно будет установлено у всех создаваемых вариантов товара.<br>" .
    "Остальные значения полей будут наследоваться от основного варианта товара.<br>" .
    "Названия вариантов будут сгенерированы автоматически на основе выбранных значений полей (если не были указаны значения для поля «Название варианта»). Порядок полей влияет на порядок перечисления значений в названии варианта.<br>"
);
define("NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_SELECT_PROPERTY", "Выберите свойство товара");
define("NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_FILL_ARTICLE", "Заполнить поле «%s»");
define("NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_FILL_ARTICLE_COMMENT", "Значение поля «%s» у создаваемых вариантов товаров будет сформировано из значения поля «%1\$s» основного товара и порядкового номера варианта, разделённых дефисом");
define("NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_CREATE", "Создать");
define("NETCAT_MODULE_NETSHOP_ITEM_VARIANTS_COUNT", "Количество создаваемых вариантов товара:");

define("NETCAT_MODULE_NETSHOP_LIST_ACTIONS_HEADER", "Действия");
define("NETCAT_MODULE_NETSHOP_ACTION_EDIT", "Редактировать");
define("NETCAT_MODULE_NETSHOP_ACTION_DELETE", "Удалить");
define("NETCAT_MODULE_NETSHOP_LIST_PREVIOUS_PAGE", "Предыдущая страница");
define("NETCAT_MODULE_NETSHOP_LIST_NEXT_PAGE", "Следующая страница");

define("NETCAT_MODULE_NETSHOP_NAME_FIELD", "Название");
define("NETCAT_MODULE_NETSHOP_DESCRIPTION_FIELD", "Описание");
define("NETCAT_MODULE_NETSHOP_CONDITION_FIELD", "Условия");
define("NETCAT_MODULE_NETSHOP_NAME_AND_CONDITIONS_HEADER", "Название, условия");
define("NETCAT_MODULE_NETSHOP_UTM_FIELD", "UTM метки");

define("NETCAT_MODULE_NETSHOP_BUTTON_ADD", "Добавить");
define("NETCAT_MODULE_NETSHOP_BUTTON_BACK", "Назад");
define("NETCAT_MODULE_NETSHOP_BUTTON_SAVE", "Сохранить");
define('NETCAT_MODULE_NETSHOP_BUTTON_APPLY_FILTER', 'Применить');
define('NETCAT_MODULE_NETSHOP_BUTTON_CLOSE_DIALOG', 'Закрыть');
define("NETCAT_MODULE_NETSHOP_BUTTON_DELETE_SELECTED", "Удалить выбранное");
define("NETCAT_MODULE_NETSHOP_BUTTON_DELETE", "Удалить");

define("NETCAT_MODULE_NETSHOP_UNABLE_TO_SAVE_RECORD", "Ошибка при сохранении записи. <a href='javascript:history:back()'>Вернуться к редактированию</a>");

// Settings
define("NETCAT_MODULE_NETSHOP_SHOP_SETTINGS_TAB", "Организация");
define("NETCAT_MODULE_NETSHOP_EXTRA_SETTINGS_TAB", "Настройки");
define("NETCAT_MODULE_NETSHOP_PRICE_RULES_TAB", 'Цены');

define("NETCAT_MODULE_NETSHOP_SETTINGS_NO_CURRENCIES_ON_SITE", "На выбранном сайте не указана ни одна валюта.");
define("NETCAT_MODULE_NETSHOP_SETTINGS_NO_OFFICIAL_RATES_ON_SITE", "На выбранном сайте нет информации об официальных курсах валют.");
define("NETCAT_MODULE_NETSHOP_SETTINGS_NO_PRICE_RULES_ON_SITE", "На выбранном сайте не заданы правила выбора цен.");
define("NETCAT_MODULE_NETSHOP_SETTINGS_NO_PAYMENT_METHODS_ON_SITE", "На выбранном сайте не указаны способы оплаты.");
define("NETCAT_MODULE_NETSHOP_SETTINGS_NO_DELIVERY_METHODS_ON_SITE", "На выбранном сайте не указаны способы доставки.");
define("NETCAT_MODULE_NETSHOP_SETTINGS_NO_DELIVERY_POINTS_ON_SITE", "На выбранном сайте пункты выдачи заказов пока не добавлены.");

define("NETCAT_MODULE_NETSHOP_CURRENCY", "Валюта");
define("NETCAT_MODULE_NETSHOP_CURRENCY_SETTINGS_TAB", "Настройки");
define("NETCAT_MODULE_NETSHOP_CURRENCY_RATE", "Курс по отношению к основной валюте ЦБ (руб.)");
define("NETCAT_MODULE_NETSHOP_CURRENCY_SHORT_NAME", 'Сокращённое наименование валюты');
define("NETCAT_MODULE_NETSHOP_CURRENCY_FULL_NAME", 'Полное название валюты (ед.ч. им., ед.ч. род., мн.ч. род.)');
define("NETCAT_MODULE_NETSHOP_CURRENCY_DECIMAL_PART_NAME", 'Наименование дробной части валюты');
define("NETCAT_MODULE_NETSHOP_CURRENCY_FORMAT_RULE", 'Формат вывода');
define("NETCAT_MODULE_NETSHOP_CURRENCY_DECIMAL_POINTS", 'Количество знаков после запятой');
define("NETCAT_MODULE_NETSHOP_CURRENCY_DECIMAL_SEPARATOR", 'Разделитель дробной и целой части');
define("NETCAT_MODULE_NETSHOP_CURRENCY_THOUSANDS_SEPARATOR", 'Разделитель групп разрядов');
define("NETCAT_MODULE_NETSHOP_DAYS_TO_KEEP_CURRENCY_RATES", 'Сколько дней хранить официальные курсы валют');
define("NETCAT_MODULE_NETSHOP_RULE", 'Правило');
define("NETCAT_MODULE_NETSHOP_PRICE_RULE_NAME", 'Название правила');
define("NETCAT_MODULE_NETSHOP_PRICE_RULE_PRICE_COLUMN", 'Колонка цен');
define("NETCAT_MODULE_NETSHOP_PRICE_RULE_CONFIRM_DELETE", "Удалить правило «%s»?");
define("NETCAT_MODULE_NETSHOP_ORDERS_COMPONENT", "Компонент заказов");
define("NETCAT_MODULE_NETSHOP_DEFAULT_FULL_NAME_TEMPLATE", "Шаблон полного названия товара (FullName) по умолчанию");
define("NETCAT_MODULE_NETSHOP_ORDERS_SUM_STATUS", "Статусы заказов для расчёта суммы покупок");
define("NETCAT_MODULE_NETSHOP_1C_EXPORT_ORDERS_STATUS", "Статусы заказов для экспорта в 1С, МойСклад");
define("NETCAT_MODULE_NETSHOP_PAID_ORDER_STATUS", "Статус, в который переходит заказ в случае успешной оплаты");

// _MAILER_

define("NETCAT_MODULE_NETSHOP_MAILER_ROOT", "Письма");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATES", "Макеты писем");
define("NETCAT_MODULE_NETSHOP_MAILER_CUSTOMER_MAIL", "Письма клиентам");
define("NETCAT_MODULE_NETSHOP_MAILER_MANAGER_MAIL", "Письма менеджерам");
define("NETCAT_MODULE_NETSHOP_MAILER_MASTER_TEMPLATES", "Макеты"); // Макеты дизайна писем
define("NETCAT_MODULE_NETSHOP_MAILER_NO_MASTER_TEMPLATES", "Макеты писем для интернет-магазина на выбранном сайте отсутствуют.");
define("NETCAT_MODULE_NETSHOP_MAILER_CUSTOMER_ORDER", "Новый заказ"); // Шаблон письма клиенту при оформлении заказа
define("NETCAT_MODULE_NETSHOP_MAILER_CUSTOMER_ORDER_REGISTER", "Заказ и регистрация");
define("NETCAT_MODULE_NETSHOP_MAILER_ORDER_CHANGE_ITEMS", "Изменение состава");
define("NETCAT_MODULE_NETSHOP_MAILER_ORDER_STATUS", "Статус &laquo;%s&raquo;");
define("NETCAT_MODULE_NETSHOP_MAILER_ORDER_STATUS_SHORT", "&laquo;%s&raquo;");

define("NETCAT_MODULE_NETSHOP_MAILER_MASTER_TEMPLATE_HEADER_NAME", "Название макета");
define("NETCAT_MODULE_NETSHOP_MAILER_MASTER_TEMPLATE_NAME", "Название макета");
define("NETCAT_MODULE_NETSHOP_MAILER_MASTER_TEMPLATE_IS_USED", "Невозможно удалить данный макет, поскольку он используется в шаблонах писем");
define("NETCAT_MODULE_NETSHOP_MAILER_MASTER_TEMPLATE_CONFIRM_DELETE", "Удалить макет писем «%s»?");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_SUBJECT", "Заголовок письма");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_BODY", "Тело письма");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_PARENT_TEMPLATE", "Макет письма");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_NO_PARENT_TEMPLATE", "Без макета");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_IS_ENABLED", "высылать письмо при переходе заказа в данный статус");

define("NETCAT_MODULE_NETSHOP_MAILER_MESSAGE_PREVIEW", "Предварительный просмотр письма");
define("NETCAT_MODULE_NETSHOP_MAILER_MESSAGE_PREVIEW_TO", "Кому:");
define("NETCAT_MODULE_NETSHOP_MAILER_MESSAGE_PREVIEW_SUBJECT", "Тема:");
define("NETCAT_MODULE_NETSHOP_MAILER_MESSAGE_PREVIEW_NO_SUBJECT", "БЕЗ ТЕМЫ");
define("NETCAT_MODULE_NETSHOP_MAILER_MESSAGE_PREVIEW_SEND_PROMPT", "Укажите адрес, на который следует выслать копию данного письма.\n(Можно указать несколько адресов через запятую.)");
define("NETCAT_MODULE_NETSHOP_MAILER_MESSAGE_PREVIEW_SENT", "Письмо отправлено");

define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_INSERT_CHILD_TEMPLATE", "Дочерний шаблон");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_INSERT_VARIABLES", "Вставить свойство...");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_SITE_VARIABLES", "Сайт");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_SHOP_VARIABLES", "Настройки магазина");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_USER_VARIABLES", "Пользователь");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_VARIABLES", "Заказ");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_CART_VARIABLES", "Товары в корзине");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_COUPON_VARIABLES", "Купон");

define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_URL", "Адрес страницы товара");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_PRICE_AS_DEFINED", "Базовая цена (как указана в поле Price товара)");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ITEM_NON_FORMATTED_VALUE", " (без форматирования)");

define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DATE", "Дата заказа");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_ITEM_PRICE", "Стоимость товаров без скидки на состав заказа");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_CART_PRICE", "Стоимость товаров");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_CART_PRICE_WITHOUT_DISCOUNT", "Стоимость товаров без скидок");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_CART_ITEMS_DISCOUNT", "Сумма скидки на товары");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_ORDER_DISCOUNT", "Сумма скидок на заказ (состав, доставка)");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_CART_DISCOUNT", "Сумма скидки на состав заказа");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_DELIVERY_DISCOUNT", "Сумма скидки на доставку");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_PRICE", "Сумма к оплате");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_TOTAL_DISCOUNT", "Общая сумма скидки");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_METHOD_NAME", "Название способа доставки");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_METHOD_VARIANT_NAME", "Полное служебное название способа доставки (с названием из настроек)");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_ADDRESS", "Адрес доставки или пункта выдачи");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_POINT_NAME", "Название пункта выдачи");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_POINT_DESCRIPTION", "Информация о пункте выдачи");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_POINT_ADDRESS", "Адрес пункта выдачи");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_POINT_PHONES", "Телефоны пункта выдачи");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_POINT_SCHEDULE", "Время работы пункта выдачи");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_DATES", "Ожидаемые даты доставки");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_PRICE", "Стоимость доставки без скидки");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_DELIVERY_PRICE_WITH_DISCOUNT", "Стоимость доставки к оплате");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_PAYMENT_METHOD_NAME", "Название способа оплаты");
define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_PAYMENT_CHARGE", "Дополнительный сбор за способ оплаты");

define("NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_ORDER_ID", "Идентификатор заказа");

define("NETCAT_MODULE_NETSHOP_MAILER_RULES", "Дополнительные адреса менеджеров");
define("NETCAT_MODULE_NETSHOP_MAILER_RULE_ADDRESS", "Адрес электронной почты");
define("NETCAT_MODULE_NETSHOP_MAILER_NO_RULES_ON_SITE", "На выбранном сайте не указано ни одно правило подбора адресов менеджеров.");
define("NETCAT_MODULE_NETSHOP_MAILER_RULES_CONFIRM_DELETE", "Удалить правило «%s»?");

// _CONDITION_

// Фрагменты для составления текстового описания условий
define('NETCAT_MODULE_NETSHOP_OP_EQ', '%s');
define('NETCAT_MODULE_NETSHOP_OP_EQ_IS', '— %s');
define('NETCAT_MODULE_NETSHOP_OP_NE', 'не %s');
define('NETCAT_MODULE_NETSHOP_OP_GT', 'более %s');
define('NETCAT_MODULE_NETSHOP_OP_GE', 'не менее %s');
define('NETCAT_MODULE_NETSHOP_OP_LT', 'менее %s');
define('NETCAT_MODULE_NETSHOP_OP_LE', 'не более %s');
define('NETCAT_MODULE_NETSHOP_OP_GT_DATE', 'позднее %s');
define('NETCAT_MODULE_NETSHOP_OP_GE_DATE', 'не ранее %s');
define('NETCAT_MODULE_NETSHOP_OP_LT_DATE', 'ранее %s');
define('NETCAT_MODULE_NETSHOP_OP_LE_DATE', 'позднее %s');
define('NETCAT_MODULE_NETSHOP_OP_CONTAINS', 'содержит «%s»');
define('NETCAT_MODULE_NETSHOP_OP_NOTCONTAINS', 'не содержит «%s»');
define('NETCAT_MODULE_NETSHOP_OP_BEGINS', 'начинается с «%s»');

define('NETCAT_MODULE_NETSHOP_COND_QUOTED_VALUE', '«%s»');
define('NETCAT_MODULE_NETSHOP_COND_OR', ', или '); // spaces are important
define('NETCAT_MODULE_NETSHOP_COND_AND', '; ');
define('NETCAT_MODULE_NETSHOP_COND_OR_SAME', ', ');
define('NETCAT_MODULE_NETSHOP_COND_AND_SAME', ' и ');
define('NETCAT_MODULE_NETSHOP_COND_DUMMY', '(тип условий, недоступный в текущей редакции модуля)');
define('NETCAT_MODULE_NETSHOP_COND_CART_COUNT', 'количество наименований в заказе —');
define('NETCAT_MODULE_NETSHOP_COND_CART_ITEM', 'заказ содержит');
define('NETCAT_MODULE_NETSHOP_COND_CART_ITEMCOMPONENT', 'заказ содержит');
define('NETCAT_MODULE_NETSHOP_COND_CART_ITEMCOMPONENT_FROM', 'компонента');
define('NETCAT_MODULE_NETSHOP_COND_CART_ITEMPARENTSUB', 'заказ содержит');
define('NETCAT_MODULE_NETSHOP_COND_CART_ITEMPARENTSUB_FROM', 'из раздела');
define('NETCAT_MODULE_NETSHOP_COND_CART_ITEMPARENTSUB_FROM_DESCENDANTS', 'и его подразделов');
define('NETCAT_MODULE_NETSHOP_COND_CART_ITEMSUB', 'заказ содержит');
define('NETCAT_MODULE_NETSHOP_COND_CART_ITEMSUB_FROM', 'из раздела');
define('NETCAT_MODULE_NETSHOP_COND_CART_ITEMPROPERTY', 'заказ содержит');
define('NETCAT_MODULE_NETSHOP_COND_CART_ITEMPROPERTY_WITH', ', у которых');
define('NETCAT_MODULE_NETSHOP_COND_CART_PROPERTYMAX', 'максимальное значение по полю «%s» в заказе —');
define('NETCAT_MODULE_NETSHOP_COND_CART_PROPERTYMIN', 'минимальное значение по полю «%s» в заказе —');
define('NETCAT_MODULE_NETSHOP_COND_CART_PROPERTYSUM', 'сумма по полю «%s» (с учётом количества) в заказе —');
define('NETCAT_MODULE_NETSHOP_COND_CART_TOTALPRICE', 'стоимость товаров');
define('NETCAT_MODULE_NETSHOP_COND_CART_SUM', 'стоимость товаров (без скидок на товары)');
define('NETCAT_MODULE_NETSHOP_COND_ITEM', 'на товар');
define('NETCAT_MODULE_NETSHOP_COND_ITEM_COMPONENT', 'на товары');
define('NETCAT_MODULE_NETSHOP_COND_ITEM_PARENTSUB', 'на товары раздела');
define('NETCAT_MODULE_NETSHOP_COND_ITEM_PARENTSUB_NE', 'на товары не из раздела');
define('NETCAT_MODULE_NETSHOP_COND_ITEM_PARENTSUB_DESCENDANTS', 'и его подразделов');
define('NETCAT_MODULE_NETSHOP_COND_ITEM_PROPERTY', 'на товары, у которых');
define('NETCAT_MODULE_NETSHOP_COND_ORDER_DELIVERYMETHOD', 'способ доставки —');
define('NETCAT_MODULE_NETSHOP_COND_ORDER_PAYMENTMETHOD', 'способ оплаты —');
define('NETCAT_MODULE_NETSHOP_COND_ORDER_PROPERTY', 'заказы, у которых');
define('NETCAT_MODULE_NETSHOP_COND_ORDER_STATUS', 'статус заказа —');
define('NETCAT_MODULE_NETSHOP_COND_ORDERS_COMPONENT', 'клиент ранее покупал товары');
define('NETCAT_MODULE_NETSHOP_COND_ORDERS_COUNT', 'количество выполненных заказов —');
define('NETCAT_MODULE_NETSHOP_COND_ORDERS_ITEM', 'клиент заказывал товар');
define('NETCAT_MODULE_NETSHOP_COND_ORDERS_ITEM_UNITS', 'шт. товаров');
define('NETCAT_MODULE_NETSHOP_COND_ORDERS_SUM', 'сумма заказов');
define('NETCAT_MODULE_NETSHOP_COND_ORDERS_SUMDATES', 'сумма заказов');
define('NETCAT_MODULE_NETSHOP_COND_ORDERS_SUMPERIOD', 'сумма заказов');
define('NETCAT_MODULE_NETSHOP_COND_ORDERS_SUMPERIOD_DAY', 'день дня дней');
define('NETCAT_MODULE_NETSHOP_COND_ORDERS_SUMPERIOD_WEEK', 'неделя недели недель');
define('NETCAT_MODULE_NETSHOP_COND_ORDERS_SUMPERIOD_MONTH', 'месяц месяца месяцев');
define('NETCAT_MODULE_NETSHOP_COND_ORDERS_SUMPERIOD_YEAR', 'год года лет');
define('NETCAT_MODULE_NETSHOP_COND_ORDERS_SUMPERIOD_FOR', 'за');
define('NETCAT_MODULE_NETSHOP_COND_USER', 'пользователь —');
define('NETCAT_MODULE_NETSHOP_COND_USER_CREATED', 'дата регистрации пользователя —');
define('NETCAT_MODULE_NETSHOP_COND_USER_GROUP', 'группа пользователя —');
define('NETCAT_MODULE_NETSHOP_COND_USER_PROPERTY', 'для пользователей, у которых');
define('NETCAT_MODULE_NETSHOP_COND_DATE_FROM', 'с');
define('NETCAT_MODULE_NETSHOP_COND_DATE_TO', 'по');
define('NETCAT_MODULE_NETSHOP_COND_TIME_INTERVAL', '%s — %s');
define('NETCAT_MODULE_NETSHOP_COND_BOOLEAN_TRUE', '«истина»');
define('NETCAT_MODULE_NETSHOP_COND_BOOLEAN_FALSE', '«ложь»');
define('NETCAT_MODULE_NETSHOP_COND_DAYOFWEEK_ON_LIST', 'в понедельник/во вторник/в среду/в четверг/в пятницу/в субботу/в воскресенье');
define('NETCAT_MODULE_NETSHOP_COND_DAYOFWEEK_EXCEPT_LIST', 'кроме понедельника/кроме вторника/кроме среды/кроме четверга/кроме пятницы/кроме субботы/кроме воскресенья');
define('NETCAT_MODULE_NETSHOP_COND', 'Условия: ');

define('NETCAT_MODULE_NETSHOP_COND_NONEXISTENT_COMPONENT', '[НЕСУЩЕСТВУЮЩИЙ КОМПОНЕНТ]');
define('NETCAT_MODULE_NETSHOP_COND_NONEXISTENT_FIELD', '[ОШИБКА В УСЛОВИИ: ПОЛЕ НЕ СУЩЕСТВУЕТ]');
define('NETCAT_MODULE_NETSHOP_COND_NONEXISTENT_VALUE', '[НЕСУЩЕСТВУЮЩЕЕ ЗНАЧЕНИЕ]');
define('NETCAT_MODULE_NETSHOP_COND_NONEXISTENT_SUB', '[НЕСУЩЕСТВУЮЩИЙ РАЗДЕЛ]');
define('NETCAT_MODULE_NETSHOP_COND_NONEXISTENT_ITEM', '[НЕСУЩЕСТВУЮЩИЙ ТОВАР]');
define('NETCAT_MODULE_NETSHOP_COND_NONEXISTENT_USER_GROUP', '[НЕСУЩЕСТВУЮЩАЯ ГРУППА ПОЛЬЗОВАТЕЛЕЙ]');
define('NETCAT_MODULE_NETSHOP_COND_NONEXISTENT_USER', '[НЕСУЩЕСТВУЮЩИЙ ПОЛЬЗОВАТЕЛЬ]');
define('NETCAT_MODULE_NETSHOP_COND_NONEXISTENT_DELIVERY_METHOD', '[НЕСУЩЕСТВУЮЩИЙ СПОСОБ ДОСТАВКИ]');
define('NETCAT_MODULE_NETSHOP_COND_NONEXISTENT_PAYMENT_METHOD', '[НЕСУЩЕСТВУЮЩИЙ СПОСОБ ОПЛАТЫ]');
define('NETCAT_MODULE_NETSHOP_COND_NONEXISTENT_STATUS', '[НЕСУЩЕСТВУЮЩИЙ СТАТУС ЗАКАЗА]');

// Строки, используемые в «простом» редакторе условий
define('NETCAT_MODULE_NETSHOP_SIMPLE_CONDITION_NOTICE', 'Подбор по условиям не доступен в установленной редакции модуля.');
define('NETCAT_MODULE_NETSHOP_SIMPLE_CONDITION_CART_TOTALPRICE_FROM', 'Сумма заказа от');
define('NETCAT_MODULE_NETSHOP_SIMPLE_CONDITION_CART_TOTALPRICE_TO', 'до');

// Строки, используемые в редакторе условий
define('NETCAT_MODULE_NETSHOP_CONDITION_NO_ADVANCED', 'Подбор по условиям не доступен в текущей редакции модуля');
define('NETCAT_MODULE_NETSHOP_CONDITION_AND', 'и');
define('NETCAT_MODULE_NETSHOP_CONDITION_OR', 'или');
define('NETCAT_MODULE_NETSHOP_CONDITION_AND_DESCRIPTION', 'Все условия верны:');
define('NETCAT_MODULE_NETSHOP_CONDITION_OR_DESCRIPTION', 'Любое из условий верно:');
define('NETCAT_MODULE_NETSHOP_CONDITION_REMOVE_GROUP', 'Удалить группу условий');
define('NETCAT_MODULE_NETSHOP_CONDITION_REMOVE_CONDITION', 'Удалить условие');
define('NETCAT_MODULE_NETSHOP_CONDITION_REMOVE_ALL_CONFIRMATION', 'Удалить все условия?');
define('NETCAT_MODULE_NETSHOP_CONDITION_REMOVE_GROUP_CONFIRMATION', 'Удалить группу условий?');
define('NETCAT_MODULE_NETSHOP_CONDITION_REMOVE_CONDITION_CONFIRMATION', 'Удалить условие «%s»?');
define('NETCAT_MODULE_NETSHOP_CONDITION_ADD', 'Добавить...');
define('NETCAT_MODULE_NETSHOP_CONDITION_ADD_GROUP', 'Добавить группу условий');

define('NETCAT_MODULE_NETSHOP_CONDITION_EQUALS', 'равно');
define('NETCAT_MODULE_NETSHOP_CONDITION_NOT_EQUALS', 'не равно');
define('NETCAT_MODULE_NETSHOP_CONDITION_LESS_THAN', 'менее');
define('NETCAT_MODULE_NETSHOP_CONDITION_LESS_OR_EQUALS', 'не более');
define('NETCAT_MODULE_NETSHOP_CONDITION_GREATER_THAN', 'более');
define('NETCAT_MODULE_NETSHOP_CONDITION_GREATER_OR_EQUALS', 'не менее');
define('NETCAT_MODULE_NETSHOP_CONDITION_CONTAINS', 'содержит');
define('NETCAT_MODULE_NETSHOP_CONDITION_NOT_CONTAINS', 'не содержит');
define('NETCAT_MODULE_NETSHOP_CONDITION_BEGINS_WITH', 'начинается с');
define('NETCAT_MODULE_NETSHOP_CONDITION_TRUE', 'да');
define('NETCAT_MODULE_NETSHOP_CONDITION_FALSE', 'нет');

define('NETCAT_MODULE_NETSHOP_CONDITION_SELECT_CONDITION_TYPE', 'выберите тип условия');
define('NETCAT_MODULE_NETSHOP_CONDITION_SEARCH_NO_RESULTS', 'Не найдено: ');

define('NETCAT_MODULE_NETSHOP_CONDITION_GROUP_GOODS', 'Параметры товара'); // 'Свойства товара'

define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_COMPONENT', 'Компонент');
define('NETCAT_MODULE_NETSHOP_CONDITION_SELECT_COMPONENT', 'выберите компонент');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_ITEM', 'Товар');
define('NETCAT_MODULE_NETSHOP_CONDITION_SELECT_ITEM', 'выберите товар');
define('NETCAT_MODULE_NETSHOP_CONDITION_NONEXISTENT_ITEM', '(Несуществующий товар)');
define('NETCAT_MODULE_NETSHOP_CONDITION_ITEM_WITHOUT_NAME', 'Товар без названия');
define('NETCAT_MODULE_NETSHOP_CONDITION_ITEM_SELECTION', 'Выбор товара');
define('NETCAT_MODULE_NETSHOP_CONDITION_DIALOG_CANCEL_BUTTON', 'Отмена');
define('NETCAT_MODULE_NETSHOP_CONDITION_DIALOG_SELECT_BUTTON', 'Выбрать');
define('NETCAT_MODULE_NETSHOP_CONDITION_SUBDIVISION_HAS_LIST_NO_COMPONENTS_OR_OBJECTS', 'В выбранном разделе отсутствуют компоненты или объекты товаров.');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_SUBDIVISION', 'Раздел');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_SUBDIVISION_DESCENDANTS', 'Раздел и его подразделы');
define('NETCAT_MODULE_NETSHOP_CONDITION_SELECT_SUBDIVISION', 'выберите раздел');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_ITEM_FIELD', 'Свойство товара');
define('NETCAT_MODULE_NETSHOP_CONDITION_COMMON_FIELDS', 'Все компоненты');
define('NETCAT_MODULE_NETSHOP_CONDITION_FIELD_BELONGS_TO_ALL_COMPONENTS', 'все компоненты');
define('NETCAT_MODULE_NETSHOP_CONDITION_SELECT_ITEM_FIELD', 'выберите свойство товара');
define('NETCAT_MODULE_NETSHOP_CONDITION_SELECT_VALUE', '...'); // sic

define('NETCAT_MODULE_NETSHOP_CONDITION_GROUP_USER', 'Параметры пользователя'); // 'Свойства пользователя'

define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_USER', 'Пользователь');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_USER_GROUP', 'Группа пользователя');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_USER_CREATED', 'Дата регистрации пользователя');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_USER_FIELD', 'Свойство пользователя');
define('NETCAT_MODULE_NETSHOP_CONDITION_SELECT_USER', 'выберите пользователя');
define('NETCAT_MODULE_NETSHOP_CONDITION_NONEXISTENT_USER', 'Несуществующий пользователь');
define('NETCAT_MODULE_NETSHOP_CONDITION_USER_SELECTION', 'Выбор пользователя');
define('NETCAT_MODULE_NETSHOP_CONDITION_USER_LIST_NO_RESULTS', 'В выбранной группе нет пользователей');
define('NETCAT_MODULE_NETSHOP_CONDITION_SELECT_USER_GROUP', 'выберите группу пользователей');
define('NETCAT_MODULE_NETSHOP_CONDITION_SELECT_USER_PROPERTY', 'выберите поле');

define('NETCAT_MODULE_NETSHOP_CONDITION_GROUP_CART', 'Корзина (состав заказа)');

define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_CART_SUM_WITH_ITEM_DISCOUNTS', 'Общая стоимость товаров');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_CART_SUM', 'Общая стоимость товаров без учёта скидок на товары');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_CART_POSITION_COUNT', 'Количество позиций');
define('NETCAT_MODULE_NETSHOP_CONDITION_CART_POSITION_COUNT', 'Количество позиций в корзине');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_CART_ITEM_COMPONENT', 'Количество товаров из компонента');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_CART_ITEM_SUBDIVISION', 'Количество товаров из раздела');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_CART_ITEM_SUBDIVISION_DESCENDANTS', 'Количество товаров из раздела и подразделов');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_CART_ITEM_COUNT', 'Количество определённого товара');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_CART_ITEM_FIELD', 'Количество товаров с определённым свойством');

define('NETCAT_MODULE_NETSHOP_CONDITION_CART_CONTAINS', 'Корзина содержит');
define('NETCAT_MODULE_NETSHOP_CONDITION_CART_PIECES_OF_COMPONENT', 'шт. товаров из компонента');
define('NETCAT_MODULE_NETSHOP_CONDITION_CART_PIECES_OF_ITEMS_FROM_SUBDIVISION', 'шт. товаров из раздела');
define('NETCAT_MODULE_NETSHOP_CONDITION_CART_PIECES_OF_ITEMS_FROM_SUBDIVISION_DESCENDANTS', 'шт. товаров из раздела и подразделов');
define('NETCAT_MODULE_NETSHOP_CONDITION_CART_PIECES_OF', 'шт. товара');
define('NETCAT_MODULE_NETSHOP_CONDITION_CART_ITEM_FIELD', 'шт. товара, у которых свойство');

define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_CART_FIELD_SUM', 'Сумма по полю товаров (с учётом количества)');
define('NETCAT_MODULE_NETSHOP_CONDITION_CART_FIELD_SUM', 'Сумма по всем товарам в корзине по полю (с учётом количества)');

define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_CART_FIELD_MIN', 'Минимум по полю товаров');
define('NETCAT_MODULE_NETSHOP_CONDITION_CART_FIELD_MIN', 'Минимальное значение среди всех товаров в корзине по полю');

define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_CART_FIELD_MAX', 'Максимум по полю товаров');
define('NETCAT_MODULE_NETSHOP_CONDITION_CART_FIELD_MAX', 'Максимальное значение среди всех товаров в корзине по полю');

define('NETCAT_MODULE_NETSHOP_CONDITION_GROUP_ORDER', 'Параметры заказа');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_ORDER_FIELD', 'Свойство заказа');
define('NETCAT_MODULE_NETSHOP_CONDITION_SELECT_ORDER_PROPERTY', 'выберите поле');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_ORDER_DELIVERY_METHOD', 'Способ доставки');
define('NETCAT_MODULE_NETSHOP_CONDITION_SELECT_DELIVERY_METHOD', 'выберите способ доставки');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_ORDER_PAYMENT_METHOD', 'Способ оплаты');
define('NETCAT_MODULE_NETSHOP_CONDITION_SELECT_PAYMENT_METHOD', 'выберите способ оплаты');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_ORDER_STATUS', 'Статус заказа');
define('NETCAT_MODULE_NETSHOP_CONDITION_SELECT_ORDER_STATUS', 'выберите статус');

define('NETCAT_MODULE_NETSHOP_CONDITION_GROUP_ORDERS', 'Выполненные заказы');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_ORDER_SUM_ALL_TIME', 'Сумма заказов за всё время');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_ORDER_SUM_PERIOD', 'Сумма заказов за период');
define('NETCAT_MODULE_NETSHOP_CONDITION_ORDER_SUM_FOR_LAST', 'Сумма заказов за последние');
define('NETCAT_MODULE_NETSHOP_CONDITION_LAST_X_DAYS', 'дней');
define('NETCAT_MODULE_NETSHOP_CONDITION_LAST_X_WEEKS', 'недель');
define('NETCAT_MODULE_NETSHOP_CONDITION_LAST_X_MONTHS', 'месяцев');
define('NETCAT_MODULE_NETSHOP_CONDITION_LAST_X_YEARS', 'лет');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_ORDER_SUM_DATES', 'Сумма заказов за даты');
define('NETCAT_MODULE_NETSHOP_CONDITION_ORDER_SUM_DATE_FROM', 'Сумма заказов с');
define('NETCAT_MODULE_NETSHOP_CONDITION_ORDER_SUM_DATE_TO', 'по');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_ORDER_COUNT', 'Количество заказов');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_ORDERS_CONTAIN_ITEM', 'Заказы содержат товар');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_ORDERS_CONTAIN_COMPONENT', 'Заказы содержат товар из компонента');

define('NETCAT_MODULE_NETSHOP_CONDITION_GROUP_DATETIME', 'Дата и время');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_DATE_INTERVAL', 'Дата');
define('NETCAT_MODULE_NETSHOP_CONDITION_DATE_FROM', 'Дата:    с');
define('NETCAT_MODULE_NETSHOP_CONDITION_DATE_TO', 'по');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_DAY_OF_WEEK', 'День недели');
define('NETCAT_MODULE_NETSHOP_CONDITION_DAY_OF_WEEK', 'День недели:');
define('NETCAT_MODULE_NETSHOP_CONDITION_MONDAY', 'понедельник');
define('NETCAT_MODULE_NETSHOP_CONDITION_TUESDAY', 'вторник');
define('NETCAT_MODULE_NETSHOP_CONDITION_WEDNESDAY', 'среда');
define('NETCAT_MODULE_NETSHOP_CONDITION_THURSDAY', 'четверг');
define('NETCAT_MODULE_NETSHOP_CONDITION_FRIDAY', 'пятница');
define('NETCAT_MODULE_NETSHOP_CONDITION_SATURDAY', 'суббота');
define('NETCAT_MODULE_NETSHOP_CONDITION_SUNDAY', 'воскресенье');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_TIME_INTERVAL', 'Время');
define('NETCAT_MODULE_NETSHOP_CONDITION_TIME_FROM', 'Время:    с');
define('NETCAT_MODULE_NETSHOP_CONDITION_TIME_TO', 'до');

define('NETCAT_MODULE_NETSHOP_CONDITION_GROUP_VALUEOF', 'Переменные');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_COOKIE_VALUE', 'Значение cookie');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_SESSION_VALUE', 'Значение переменной в сессии');

define('NETCAT_MODULE_NETSHOP_CONDITION_GROUP_EXTENSION', 'Расширения');
define('NETCAT_MODULE_NETSHOP_CONDITION_TYPE_USER_FUNCTION', 'Результат выполнения функции');
define('NETCAT_MODULE_NETSHOP_CONDITION_FUNCTION_CALL', 'Функция');
define('NETCAT_MODULE_NETSHOP_CONDITION_FUNCTION_RETURNS_TRUE', 'возвращает значение «истина»');
define('NETCAT_MODULE_NETSHOP_CONDITION_VALUE_REQUIRED', 'Необходимо указать значение условия или удалить условие «%s»');

// _PROMOTION_

define("NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNTS", "Скидки и купоны");

define("NETCAT_MODULE_NETSHOP_PROMOTION_ITEM_DISCOUNTS", "Скидки на товары");
define("NETCAT_MODULE_NETSHOP_PROMOTION_NO_ITEM_DISCOUNTS", "Скидки на товары на выбранном сайте отсутствуют");

define("NETCAT_MODULE_NETSHOP_PROMOTION_CART_DISCOUNTS", "Скидки на корзину");
define("NETCAT_MODULE_NETSHOP_PROMOTION_NO_CART_DISCOUNTS", "Скидки на корзину на выбранном сайте отсутствуют");

define("NETCAT_MODULE_NETSHOP_PROMOTION_DELIVERY_DISCOUNTS", "Скидки на доставку");
define("NETCAT_MODULE_NETSHOP_PROMOTION_NO_DELIVERY_DISCOUNTS", "Скидки на доставку на выбранном сайте отсутствуют");

define("NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_AMOUNT", "Скидка");
define("NETCAT_MODULE_NETSHOP_PROMOTION_LIST_EDIT_HEADER", "Изменить");
define("NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_COUPONS", "Купоны");
define("NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_IS_CUMULATIVE_SHORT", "суммируется");
define("NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_REQUIRES_ITEM_ACTIVATION_SHORT", "активируется пользователем");
define('NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_CONFIRM_DELETE', 'Удалить скидку «%s»?');

define("NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_VALUE", "Размер скидки");
define("NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_IS_CUMULATIVE", "Суммируется с другими скидками");
define('NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_CONDITIONS', 'Условия применения скидки');
define('NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_REQUIRES_COUPON_CODE', 'Применять по купону');
define('NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_CREATE_COUPONS_AFTER_SAVING', 'Создать купоны после сохранения скидки');
define('NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_NUMBER_OF_COUPONS', 'Купонов: %s (действительных: %s)');
define('NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_GENERATE_COUPONS', 'Добавить купоны');
define('NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_REQUIRES_ITEM_ACTIVATION', 'Использовать как «сиюминутное предложение»');
define('NETCAT_MODULE_NETSHOP_PROMOTION_DISCOUNT_IS_ENABLED', 'Скидка активна');

define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPONS_FOR_DISCOUNT_ITEM_HEADER', 'Купоны для скидки на товары «%s»'); // "DISCOUNT_ITEM": sic (depends on discount class name)
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPONS_FOR_DISCOUNT_CART_HEADER', 'Купоны для скидки на корзину «%s»'); // "DISCOUNT_CART": sic (depends on discount class name)
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPONS_FOR_DISCOUNT_DELIVERY_HEADER', 'Купоны для скидки на доставку «%s»'); // "DISCOUNT_DELIVERY": sic (depends on discount class name)
define('NETCAT_MODULE_NETSHOP_PROMOTION_NO_COUPONS', 'Купоны отсутствуют.');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_CODE', 'Код купона');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_NUMBER_OF_USAGES', 'Количество использований');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_NUMBER_OF_USAGES_OUT_OF', 'из');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_VALID_TILL', 'Срок действия');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_VALID_INDEFINITELY', 'не ограничен');
define('NETCAT_MODULE_NETSHOP_PROMOTION_GENERATE_COUPONS_BUTTON', 'Добавить купоны');
define('NETCAT_MODULE_NETSHOP_PROMOTION_BACK_TO_DISCOUNT_LIST', 'К списку скидок');
define('NETCAT_MODULE_NETSHOP_PROMOTION_BACK_TO_DEAL_LIST', 'К списку предложений');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_CSV_LINK', 'Экспорт в формате CSV');


define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_SEND_CODES_TO_USERS', 'Выслать коды пользователям по электронной почте');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_USERS_SELECTION', 'Выборка пользователей');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_NUMBER_OF_USERS_SELECTED', 'Выбрано пользователей: ');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_SHOW_SELECTED_USERS', 'Открыть список выбранных пользователей');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_NO_USERS', 'Заданным условиям не удовлетворяет ни один пользователь');
define('NETCAT_MODULE_NETSHOP_PROMOTION_NUMBER_OF_COUPONS_TO_GENERATE', 'Количество купонов:');
define('NETCAT_MODULE_NETSHOP_PROMOTION_GENERATED_COUPON_CODE', 'Код купона:');
define('NETCAT_MODULE_NETSHOP_PROMOTION_GENERATED_COUPON_CODE_PREFIX', 'Префикс кодов купонов:');
define('NETCAT_MODULE_NETSHOP_PROMOTION_GENERATED_COUPON_CODE_SYMBOLS', 'Символы, используемые в генерируемой части кода:');
define('NETCAT_MODULE_NETSHOP_PROMOTION_GENERATED_COUPON_CODE_SYMBOLS_DEFAULT_VALUE', 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789');
define('NETCAT_MODULE_NETSHOP_PROMOTION_GENERATED_COUPON_CODE_LENGTH', 'Длина генерируемой части:');
define('NETCAT_MODULE_NETSHOP_PROMOTION_GENERATED_COUPON_CODE_VALID_TILL', 'Срок действия купона:');
define('NETCAT_MODULE_NETSHOP_PROMOTION_GENERATED_COUPON_CODE_VALID_INDEFINITELY', 'не ограничен');
define('NETCAT_MODULE_NETSHOP_PROMOTION_GENERATED_COUPON_CODE_VALID_TILL_DATE', 'до даты');
define('NETCAT_MODULE_NETSHOP_PROMOTION_GENERATED_COUPON_MAX_USAGES', 'Максимальное количество использований каждого купона:');
define('NETCAT_MODULE_NETSHOP_PROMOTION_USER_EMAIL_FIELD', 'Поле пользователя с адресом электронной почты:');
define('NETCAT_MODULE_NETSHOP_PROMOTION_PREVIEW_EMAIL', 'Посмотреть пример письма');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_WITH_THIS_CODE_ALREADY_EXISTS', 'Купон с таким кодом уже существует!');
define('NETCAT_MODULE_NETSHOP_PROMOTION_CREATE_COUPONS', 'Создать');
define('NETCAT_MODULE_NETSHOP_PROMOTION_CANNOT_CREATE_COUPON', 'Невозможно создать купон: указан некорректный или уже существующий код');
define('NETCAT_MODULE_NETSHOP_PROMOTION_CANNOT_GENERATE_COUPONS', 'Невозможно создать требуемое количество купонов с указанными настройками.' .
    '<ul><li>Увеличьте длину генерируемой части кода купона.</li>'.
    '<li>Расширьте набор символов, которые могут использоваться в кодах купонов.</li>' .
    '<li>Выберите другой префикс для кода купонов.</li>' .
    '</ul>');
define('NETCAT_MODULE_NETSHOP_PROMOTION_CANNOT_SEND_COUPONS', 'Невозможно выслать письма пользователям. ' .
    'Проверьте правильность указания настроек, связанных с формированием писем: тему, тело письма, поле пользователя с адресом электронной почты.');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_CODE_MAX_USAGES', 'Максимальное количество использований:');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_IS_ENABLED', 'Купон активен');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_GENERATION_TITLE', 'Создание купонов');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_GENERATION_PLEASE_WAIT', 'Не закрывайте это окно до окончания процесса.');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_GENERATION_STEP_1', 'Генерация кодов');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_GENERATION_STEP_2', 'Создание писем');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_GENERATION_STEP_FINISHED', '— &nbsp;завершено.');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_GENERATION_ERROR_CAPTION', 'При создании купонов возникла ошибка:');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_GENERATION_DIALOG_CLOSE', 'Закрыть');

define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_CODE_IS_INVALID', 'Купон &laquo;%s&raquo; не найден');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_IS_EXPIRED', 'Срок действия купона &laquo;%s&raquo; истёк');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_IS_NOT_VALID_ON_THIS_SITE', 'Купон &laquo;%s&raquo; не действует на текущем сайте');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_IS_USED_UP', 'Купон &laquo;%s&raquo; уже израсходован');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_IS_REMOVED_FROM_SESSION', 'Купон &laquo;%s&raquo; удалён');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_ONLY_ONE_OF_ITS_KIND_IS_ALLOWED', 'Купон &laquo;%s&raquo; не был добавлен, так как уже добавлен другой купон этого типа');
define('NETCAT_MODULE_NETSHOP_PROMOTION_COUPON_CANNOT_BE_APPLIED_TO_ANY_ITEM', 'Купон &laquo;%s&raquo; не действует ни на один товар в корзине');
define('NETCAT_MODULE_NETSHOP_PROMOTION_REGISTERED_COUPON_CODE_IS_APPLIED_TO_CART', 'Купон &laquo;%s&raquo; применён к вашей корзине и товарам на сайте');
define('NETCAT_MODULE_NETSHOP_PROMOTION_REGISTERED_COUPON_CODE_WILL_BE_APPLIED_TO_CART', 'Купон &laquo;%s&raquo; будет применён к вашей корзине и товарам на сайте');

define('NETCAT_MODULE_NETSHOP_1C_SECRET_NAME', 'Имя пользователя для обмена CommerceML (1С, МойСклад)');
define('NETCAT_MODULE_NETSHOP_1C_SECRET_KEY', 'Пароль для обмена CommerceML (1С, МойСклад)');
define('NETCAT_MODULE_NETSHOP_SECRET_KEY', 'Секретный ключ для скрипта получения валют');

define('NETCAT_MODULE_NETSHOP_EXTERNAL_ORDER_SECRET_KEY', 'Внешний заказ: секретный ключ');
define('NETCAT_MODULE_NETSHOP_EXTERNAL_ORDER_IP_LIST', 'Внешний заказ: список разрешенных IP (по одному в каждой строке)');

define('NETCAT_MODULE_NETSHOP_ORDER_STATUSES', 'Статусы заказов');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_CAMPAIGN_NUMBER', 'Номер кампании');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_AUTH_TOKEN', 'Авторизационный токен');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_OAUTH_APP_ID', 'ID OAuth-приложения');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_OAUTH_APP_TOKEN', 'Токен OAuth-приложения');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_STATUS_CANCELLED', 'CANCELLED — заказ отменен');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_STATUS_DELIVERED', 'DELIVERED — заказ получен покупателем');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_STATUS_DELIVERY', 'DELIVERY — заказ передан в доставку');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_STATUS_PICKUP', 'PICKUP — заказ доставлен в пункт самовывоза');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_STATUS_PROCESSING', 'PROCESSING — заказ находится в обработке');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_STATUS_RESERVED', 'RESERVED — заказ в резерве (ожидается подтверждение от пользователя)');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_STATUS_UNPAID', 'UNPAID — заказ оформлен, но еще не оплачен (если выбрана плата при оформлении)');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_SETTINGS_SAVED', 'Настройки успешно сохранены');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_FILL_SETTINGS', 'Для активации функционала заказа на Яндекс.Маркете необходимо заполнить все настройки');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_COMPARE_STATUSES', 'Установите соответствие внешних и внутренних статусов заказов:');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_COMPARE_PAYMENT_METHODS', 'Установите соответствие внешних и внутренних способов оплаты:');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_PAYMENT_PREPAID', 'Предоплата на Яндекс.Маркете');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_PAYMENT_POSTPAID_CASH', 'Постоплата (наличными при получении)');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_PAYMENT_POSTPAID_CARD', 'Постоплата (картой при получении)');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_ONLINE_PAYMENT_CHECKED', 'Предоплата заказа на Яндекс.Маркете включена');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_ORDER_NO_DATA_YET', '-ожидаются данные-');
define('NETCAT_MODULE_NETSHOP_ORDER_STATUS_CHANGE_SEQUENCES', 'Установите правила перехода заказа из одного статуса в другой.');
define('NETCAT_MODULE_NETSHOP_MARKET_YANDEX_EXPORT', 'Выгрузка товаров');
define('NETCAT_MODULE_NETSHOP_MARKET_YANDEX_ORDERS', 'Заказ на Маркете');


define('NETCAT_MODULE_NETSHOP_IGNORE_STOCK_UNITS_VALUE', 'Не учитывать значение поля «Остаток на складе» при добавлении товара в корзину');
define('NETCAT_MODULE_NETSHOP_STOCK_RESERVE_STATUS', 'Статусы заказов, при которых происходит уменьшение значения поля «Остаток на складе»');
define('NETCAT_MODULE_NETSHOP_STOCK_RETURN_STATUS', 'Статусы заказов, при которых происходит возврат товаров на склад (увеличение значения поля «Остаток на складе»)');
define('NETCAT_MODULE_NETSHOP_STOCK_RESERVE_FIELD', 'Товары списаны из остатка на складе');
define('NETCAT_MODULE_NETSHOP_STOCK_RETURN_FIELD', 'Товары возвращены в остаток на склад');

define('NETCAT_MODULE_NETSHOP_DEFAULT_PACKAGE_SIZE', 'Размер упаковки товара по умолчанию');
define('NETCAT_MODULE_NETSHOP_LENGTH_CM', 'см');

define('NETCAT_MODULE_NETSHOP_ITEM_INDEX_FIELDS', 'Поля товаров, используемые для поиска в панели управления');

define("NETCAT_MODULE_NETSHOP_MARKETS", "Торговые площадки");
define("NETCAT_MODULE_NETSHOP_YANDEX_MARKET", "Яндекс.Маркет");
define("NETCAT_MODULE_NETSHOP_YANDEX_MARKET_BUNDLES", "Связки");
define("NETCAT_MODULE_NETSHOP_YANDEX_MARKET_BUNDLE_ADD", "Добавление Связки");
define("NETCAT_MODULE_NETSHOP_YANDEX_MARKET_BUNDLE_EDIT", "Редактирование Связки");
define("NETCAT_MODULE_NETSHOP_YANDEX_MARKET_BUNDLES_NAME", "Название связки");
define("NETCAT_MODULE_NETSHOP_YANDEX_MARKET_BUNDLES_UPDATED", "Изменена");
define('NETCAT_MODULE_NETSHOP_YANDEX_CONFIRM_DELETE', 'Удалить связку «%s»?');

define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_BUNDLE_ID', 'ID');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_BUNDLE_TYPE', 'Тип связки');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_BUNDLE_TYPE_SIMPLE', 'Упрощенное описание');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_BUNDLE_TYPE_FULL', 'Произвольный товар (расширенное)');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_BUNDLES_EDIT_FIELDS', 'Редактировать соответствия');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_BUNDLES_EXPORT_URL', 'URL для экспорта');

define("NETCAT_MODULE_NETSHOP_GOOGLE_MERCHANT", "Google Merchant");
define("NETCAT_MODULE_NETSHOP_MARKET_YANDEX_NO_BUNDLES", "Связки для экспорта в Яндекс.Маркет на выбранном сайте отсутствуют");
define("NETCAT_MODULE_NETSHOP_MARKET_GOOGLE_NO_BUNDLES", "Связки для экспорта в Google Merchant на выбранном сайте отсутствуют");

define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_MULTI_NAME', 'Имя');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_MULTI_UNITS', 'Единицы измерения');
define('NETCAT_MODULE_NETSHOP_YANDEX_MARKET_MULTI_FIELD', 'Поле');

define("NETCAT_MODULE_NETSHOP_GOOGLE_MARKET_BUNDLES", "Связки");
define("NETCAT_MODULE_NETSHOP_GOOGLE_MARKET_BUNDLE_ADD", "Добавление Связки");
define("NETCAT_MODULE_NETSHOP_GOOGLE_MARKET_BUNDLE_EDIT", "Редактирование Связки");
define("NETCAT_MODULE_NETSHOP_GOOGLE_MARKET_BUNDLES_NAME", "Название связки");
define("NETCAT_MODULE_NETSHOP_GOOGLE_MARKET_BUNDLES_UPDATED", "Изменена");
define('NETCAT_MODULE_NETSHOP_GOOGLE_CONFIRM_DELETE', 'Удалить связку «%s»?');
define('NETCAT_MODULE_NETSHOP_GOOGLE_MARKET_BUNDLE_ID', 'ID');
define('NETCAT_MODULE_NETSHOP_GOOGLE_MARKET_BUNDLE_TYPE', 'Тип связки');
define('NETCAT_MODULE_NETSHOP_GOOGLE_MARKET_BUNDLE_TYPE_SIMPLE', 'Упрощенное описание');
define('NETCAT_MODULE_NETSHOP_GOOGLE_MARKET_BUNDLES_EDIT_FIELDS', 'Редактировать соответствия');
define('NETCAT_MODULE_NETSHOP_GOOGLE_MARKET_BUNDLES_EXPORT_URL', 'URL для экспорта');

define("NETCAT_MODULE_NETSHOP_MAILRU", "Товары@Mail.Ru");
define("NETCAT_MODULE_NETSHOP_MARKET_MAIL_NO_BUNDLES", "Связки для экспорта в Товары@Mail.Ru на выбранном сайте отсутствуют");
define("NETCAT_MODULE_NETSHOP_MAIL_BUNDLES", "Связки");
define("NETCAT_MODULE_NETSHOP_MAIL_BUNDLE_ADD", "Добавление Связки");
define("NETCAT_MODULE_NETSHOP_MAIL_BUNDLE_EDIT", "Редактирование Связки");
define("NETCAT_MODULE_NETSHOP_MAIL_BUNDLES_NAME", "Название связки");
define("NETCAT_MODULE_NETSHOP_MAIL_BUNDLES_UPDATED", "Изменена");
define('NETCAT_MODULE_NETSHOP_MAIL_CONFIRM_DELETE', 'Удалить связку «%s»?');
define('NETCAT_MODULE_NETSHOP_MAIL_BUNDLE_ID', 'ID');
define('NETCAT_MODULE_NETSHOP_MAIL_BUNDLE_TYPE', 'Тип связки');
define('NETCAT_MODULE_NETSHOP_MAIL_BUNDLE_TYPE_SIMPLE', 'Упрощенное описание');
define('NETCAT_MODULE_NETSHOP_MAIL_BUNDLES_EDIT_FIELDS', 'Редактировать соответствия');
define('NETCAT_MODULE_NETSHOP_MAIL_BUNDLES_EXPORT_URL', 'URL для экспорта');

define('NETCAT_MODULE_NETSHOP_MAIL_MULTI_NAME', 'Имя');
define('NETCAT_MODULE_NETSHOP_MAIL_MULTI_UNITS', 'Единицы измерения');
define('NETCAT_MODULE_NETSHOP_MAIL_MULTI_FIELD', 'Поле');

define('NETCAT_MODULE_NETSHOP_CONFIRM_STATUS_CHANGE', 'Подтвердите смену статуса');
define('NETCAT_MODULE_NETSHOP_CONFIRM_STATUS_CHANGE_TO', 'Изменить статус заказа на «%s»?');

define('NETCAT_MODULE_NETSHOP_NO_ORDERS', 'У вас нет ни одного заказа');
