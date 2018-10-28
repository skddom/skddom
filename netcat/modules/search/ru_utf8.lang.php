<?php

/* $Id: ru_utf8.lang.php 8184 2012-10-10 12:20:57Z ewind $ */

define("NETCAT_MODULE_SEARCH_TITLE", "Поиск по сайту");
define("NETCAT_MODULE_SEARCH_DESCRIPTION", "Индексирование и поиск по сайту.");

define("NETCAT_MODULE_SEARCH_EVENT", "Событие модуля поиска");

define("NETCAT_MODULE_SEARCH_ADMIN_INVALID_REQUEST", "Невозможно отобразить страницу: ошибка в запросе (параметр '%s')");

define("NETCAT_MODULE_SEARCH_ADMIN_INFO", "Информация");
define("NETCAT_MODULE_SEARCH_ADMIN_INDEXING", "Индексирование");
define("NETCAT_MODULE_SEARCH_ADMIN_LISTS", "Списки");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTINGS", "Настройки");

define("NETCAT_MODULE_SEARCH_ADMIN_QUERIES", "Запросы");
define("NETCAT_MODULE_SEARCH_ADMIN_SYNONYMS", "Синонимы");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKENLINKS", "Битые ссылки");

define("NETCAT_MODULE_SEARCH_ADMIN_GENERAL_SETTINGS", "Общие");
define("NETCAT_MODULE_SEARCH_ADMIN_VIEW_SETTINGS", "Отображение");
define("NETCAT_MODULE_SEARCH_ADMIN_RULES_SETTINGS", "Правила");
define("NETCAT_MODULE_SEARCH_ADMIN_SYSTEM_SETTINGS", "Системные");

define("NETCAT_MODULE_SEARCH_ADMIN_STAT_CHECK_CRONTAB",
        "В очереди индексирования имеются невыполненные задания. Проверьте, запускается ли в cron скрипт переиндексирования.");

define("NETCAT_MODULE_SEARCH_WIDGET_BROKEN_LINKS", "битых<br>ссылок");
define("NETCAT_MODULE_SEARCH_WIDGET_CHECK_CRONTAB", "в очереди невыполненные задания");
define("NETCAT_MODULE_SEARCH_WIDGET_NO_RULES", "нет правил индексирования");
define("NETCAT_MODULE_SEARCH_WIDGET_CONFIGURATION_ERRORS", "имеются ошибки конфигурации");

define("NETCAT_MODULE_SEARCH_ADMIN_STAT_HEADER", "Статистика");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_NUM_DOCUMENTS", "Проиндексировано cтраниц");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_NUM_TERMS", "Слов в индексе");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_NUM_SITEMAP_URLS", "Страниц в sitemap.xml");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_NUM_QUERIES_TODAY", "Обращений к поиску сегодня");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_NUM_QUERIES_YESTERDAY", "Обращений к поиску вчера");
define("NETCAT_MODULE_SEARCH_ADMIN_SHOW_QUERY_LOG", "показать все");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_LAST_QUERIES", "Последние запросы к поисковой системе");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_MOST_POPULAR", "Самые популярные запросы к поисковой системе");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_MOST_POPULAR_NO_RESULTS", "Самые популярные запросы к поисковой системе, по которым ничего не найдено");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_INDEXING", "Индексирование");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_INDEX", "Индексировать");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_INDEXING_NOW", "идёт индексирование");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_INDEX_IN_BACKGROUND", "в фоне");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_INDEX_IN_BROWSER", "в окне");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_INDEX_AREA", "Индексировать область");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_INDEX_AREA_IN_BACKGROUND", "Индексировать в фоне");
define("NETCAT_MODULE_SEARCH_ADMIN_STAT_INDEX_AREA_IN_BROWSER", "Индексировать в окне");

define("NETCAT_MODULE_SEARCH_ADMIN_STATUS_DELETED", "Записи удалены.");

define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_FILTER", "Фильтр запросов");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_FRAGMENT", "Фрагмент");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_TIME_PERIOD", "Временной промежуток");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_TIME_PERIOD_FROM", "с");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_TIME_PERIOD_TO", "по");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_TIME_PERIOD_CLEAR", "очистить");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_RESULTS", "Результаты");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_RESULTS_ALL", "не важно");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_RESULTS_NONE", "нет");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_RESULTS_MATCHED", "есть");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_SUBMIT_FILTER", "Показать");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_PER_PAGE", "по %s");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_SORT_BY_RESULT_COUNT", "по количеству запросов");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_SORT_BY_TIME", "по хронологии");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_SORT_BY_QUERY", "по алфавиту");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_STRING", "Запрос");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_COUNT", "Запросов");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_LAST_QUERY", "Последний запрос");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_LAST_QUERY_TIME", "Время");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_LAST_QUERY_RESULT_COUNT", "Найдено");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_LAST_QUERY_USER", "IP, пользователь");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_NO_ENTRIES", "Запросов, удовлетворяющих указанным параметрам, не найдено");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_PREV_PAGE", "Предыдущая страница");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_NEXT_PAGE", "Следующая страница");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_BACK_TO_LIST", "Назад к списку");

define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_ALL_QUERIES", "Все запросы &laquo;<span class='q'>%s</span>&raquo;");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_OPEN_RESULTS_HINT", "для просмотра результатов запроса нажмите на число в колонке &laquo;Найдено&raquo;");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_OPEN_RESULTS_LINK_HINT", "перейти к результатам поиска");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_OPEN_LOG_LINK_HINT", "показать все обращения по этому запросу");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_AREA", "Область поиска");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_AREA_INCLUDED", "Включить в результаты");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_AREA_EXCLUDED", "Исключить из результатов");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_TIME", "Время");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_RESULTS_COUNT", "Найдено");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_IP", "IP");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_USER", "Пользователь");
define("NETCAT_MODULE_SEARCH_ADMIN_QUERY_LOG_DISABLED", "Сохранение истории запросов отключено в <a href='%s' target='_top'>настройках модуля</a>.");

define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION", "Расширение");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSIONS", "Расширения");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_INTERFACE", "Интерфейс расширения");
define("NETCAT_MODULE_SEARCH_ADMIN_SEARCH_PROVIDER", "Служба поиска");
define("NETCAT_MODULE_SEARCH_ADMIN_SEARCH_PROVIDER_ANY", "Любая");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_ACTION", "Действие");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_ACTION_ANY", "Поиск и индексирование");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_ACTION_SEARCHING", "Поиск");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_ACTION_INDEXING", "Индексирование");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_CLASS", "Класс расширения");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_CONTENT_TYPE", "MIME-тип");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_CONTENT_TYPE_ANY", "Любой");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_PRIORITY", "Приоритет");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_ENABLED", "Расширение включено");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSIONS_EMPTY_LIST", "Расширений нет.");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSIONS_ADD", "Добавить расширение");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSIONS_CONFIRM_DELETE_WARNING",
        "<h3>ВНИМАНИЕ!</h3><p><b>Удаление записи о расширении может привести к полной потере работоспособности ".
        "модуля поиска или неправильным результатам при поиске.</b></p>Пожалуйста, подтвердите действие.");

define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSIONS_CONFIRM_DELETE_OK", "Я нахожусь в здравом уме и твердой памяти");
define("NETCAT_MODULE_SEARCH_ADMIN_EXTENSION_NOT_FOUND", "Расширение (ID=%s) не найдено");

define("NETCAT_MODULE_SEARCH_ADMIN_SYNONYMS_FIELD_CAPTION", "Синонимы (по одному в строке, заглавными буквами)");
define("NETCAT_MODULE_SEARCH_ADMIN_SYNONYMS_DO_NOT_APPLY_FILTERS", "Не обрабатывать введённые слова (не рекомендуется)");
define("NETCAT_MODULE_SEARCH_ADMIN_SYNONYMS_DO_NOT_APPLY_FILTERS_HELP",
        "<p>При сохранении списка синонимов к нему будут применены фильтры для текущего языка, ".
        "например фильтр стоп-слов и морфологический анализатор (или стеммер).</p>".
        "<p>Таким образом, слова будут приведены к базовым формам, а игнорируемые при поиске слова ".
        "будут удалены из списка, например список слов <code>ЗДАНИЯ; ДОМА</code> ".
        "при включенном морфологическом анализаторе будет сохранён как <code>ЗДАНИЕ; ДОМ; ДОМА</code> ".
        "(слову <code>ДОМА</code> соответствуют две базовые формы — наречие и существительное).</p>".
        "<p>Обратите внимание, что при настройках по умолчанию синонимы подставляются в запросы ".
        "после фильтрации стоп-слов и приведения к базовой форме, поэтому включение данной опции ".
        "<strong>не&nbsp;рекомендуется</strong>.");

define("NETCAT_MODULE_SEARCH_ADMIN_SYNONYM_LIST_MUST_HAVE_AT_LEAST_TWO_WORDS",
        "Список синонимов должен содержать по крайней мере два слова. Если вы указали несколько слов, ".
        "возможно, какие-то из них внесены в список стоп-слов.");

define("NETCAT_MODULE_SEARCH_ADMIN_SYNONYM_SAVE_RESULT",
        "Список слов после преобразований был сохранён как &laquo;%s&raquo;. Если результат Вас ".
        "не устраивает, <a href='%s' target='_top'>отредактируйте список слов</a>, перед сохранением отметив ".
        "опцию &laquo;Не обрабатывать введённые слова&raquo;.");


define("NETCAT_MODULE_SEARCH_ADMIN_RECORD_NOT_FOUND", "Неправильный идентификатор (ID=%s)");
define("NETCAT_MODULE_SEARCH_ADMIN_LANGUAGE_ANY", "Любой");
define("NETCAT_MODULE_SEARCH_ADMIN_LANGUAGE_ANY_LANGUAGE", "Любой язык");

define("NETCAT_MODULE_SEARCH_ADMIN_STOPWORDS", "Стоп-слова");
define("NETCAT_MODULE_SEARCH_ADMIN_STOPWORD", "Стоп-слово");
define("NETCAT_MODULE_SEARCH_ADMIN_STOPWORD_FIELD_CAPTION", "Стоп-слово (ЗАГЛАВНЫМИ буквами)");
define("NETCAT_MODULE_SEARCH_ADMIN_STOPWORD_HAS_NO_BASEFORM", "Введённое вами слово <code>%s</code> отфильтровывается ".
        "до применения фильтра стоп-слов (вероятно, является слишком коротким). Хотите сохранить это слово?");
define("NETCAT_MODULE_SEARCH_ADMIN_STOPWORD_HAS_ONE_BASEFORM", "Введённое вами слово <code>%s</code> имеет базовую форму <code>%s</code>.");
define("NETCAT_MODULE_SEARCH_ADMIN_STOPWORD_HAS_SEVERAL_BASEFORMS", "Введённое вами слово <code>%s</code> имеет несколько базовых форм.");
define("NETCAT_MODULE_SEARCH_ADMIN_STOPWORD_BASEFORM_QUESTION", "Какую форму слова следует сохранить в качестве стоп-слова?");
define("NETCAT_MODULE_SEARCH_ADMIN_STOPWORD_AS_ENTERED", "(введённый вариант &mdash; не рекомендуется)");

define("NETCAT_MODULE_SEARCH_ADMIN_RULE", "Правило");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_NAME", "Название");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_SITE", "Сайт");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA", "Область");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_SCHEDULE", "Расписание");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_MUST_HAVE_INTERVAL_TYPE", "Не указан интервал выполнения расписания");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_TO_INDEX", "Индексировать");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_WHOLE_SITE", "весь сайт");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_AREAS", "области сайта");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_FREQUENCY", "Частота переиндексирования");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_DAILY", "ежедневно в %s");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_EVERY_N_MINUTES", "каждые %s минут, начиная с %s");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_EVERY_N_HOURS", "каждые %s часов, начиная с %s");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_EVERY_N_DAYS", "каждые %s дней в %s");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_EVERY_X_DAY", "каждое %s число месяца в %s");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_ON_REQUEST", "только по запросу");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_START_URL", "Начальный адрес");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_NONEXISTENT_SITE", "Несуществующий сайт (ID=%s)");

define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_DESCRIPTION_ALLSITES", "Все сайты");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_DESCRIPTION_SITE", "Сайт &laquo;<a href='%s' target='_top'>%s</a>&raquo;");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_DESCRIPTION_SUB_ONLY", "Основную страницу раздела &laquo;<a href='%s' target='_top'>%s</a>&raquo;");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_DESCRIPTION_SUB_CHILDREN", "Все страницы раздела &laquo;<a href='%s' target='_top'>%s</a>&raquo;");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_DESCRIPTION_SUB_DESCENDANTS", "Все страницы и подразделы раздела &laquo;<a href='%s' target='_top'>%s</a>&raquo;");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_DESCRIPTION_PAGE", "Страницу &laquo;<a href='%s' target='_blank'>%s</a>&raquo;");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_DESCRIPTION_INCLUDED", "Индексировать");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_AREA_DESCRIPTION_EXCLUDED", "Не индексировать");

define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_MINUTE", "минуту минут минуты");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_HOUR", "час часов часа");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_INTERVAL_DAY", "день дней дня");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_EVERY_SEVERAL", "каждые");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_EVERY_SINGLE_MASCULINE", "каждый");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_EVERY_SINGLE_FEMININE", "каждую");

define("NETCAT_MODULE_SEARCH_ADMIN_NO_RULES", "Отсутствуют правила индексирования. Для работы модуля необходимо <a href='%s' target='_top'>создать</a> хотя бы одно правило.");
define("NETCAT_MODULE_SEARCH_ADMIN_ADD_RULE", "Добавить правило");

define("NETCAT_MODULE_SEARCH_ADMIN_UNNAMED_RULE", "Без названия");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_SHOW_DETAILS", "подробнее");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_LAST_RUN", "Последнее индексирование");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_NEVER_RUN", "Правило ни разу не выполнялось");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_LAST_RUN_DURATION", "продолжительность: ");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_LAST_RUN_NOT_FINISHED", "не окончено");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_RUN_IN_BROWSER", "Индексировать в реальном времени");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_RUN_IN_BACKGROUND", "Индексировать в фоновом режиме");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_EDIT_LINK", "Редактировать");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_QUEUE_LOADING", "Загрузка...");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_QUEUED", "Правило будет выполнено в фоновом режиме");
define("NETCAT_MODULE_SEARCH_ADMIN_RULE_QUEUE_ERROR", "Возникла ошибка при постановке правила в очередь переиндексирования");

define("NETCAT_MODULE_SEARCH_ADMIN_RULE_STATISTICS", "Проиндексировано документов: %d, удалено документов: %d, проверено ссылок: %d");

define("NETCAT_MODULE_SEARCH_ADMIN_INDEXING_TITLE", "NetCat / Индексирование сайта");
define("NETCAT_MODULE_SEARCH_ADMIN_INDEXING_IN_PROGRESS_ERROR",
        "В настоящее время производится переиндексирование области &laquo;%s&raquo;.<br />".
        "Запуск другого процесса индексирования невозможен; попробуйте позднее.");
define("NETCAT_MODULE_SEARCH_ADMIN_INDEXING_IN_PROGRESS", "Идёт переиндексирование. Не закрывайте это окно до завершения процесса.");
define("NETCAT_MODULE_SEARCH_ADMIN_INDEXING_DONE", "Индексирование завершено.");
define("NETCAT_MODULE_SEARCH_ADMIN_INDEXING_DONE_STATS",
        "Затраченное время: %s<br />".
        "Проиндексировано документов: %d<br />".
        "Проверено ссылок: %d<br />".
        "Ссылок на несуществующие документы: %d<br />".
        "Удалено документов: %d<br />");

define("NETCAT_MODULE_SEARCH_ADMIN_RESULTS_MANY", "Показаны результаты %d&mdash;%d из %d");
define("NETCAT_MODULE_SEARCH_ADMIN_RESULTS_ONE", "Показан результат %d из %d");

define("NETCAT_MODULE_SEARCH_ADMIN_MINUTES", "%d мин");
define("NETCAT_MODULE_SEARCH_ADMIN_HOURS_MINUTES", "%d ч %d мин");

define("NETCAT_MODULE_SEARCH_ADMIN_BULLET", "&mdash;");

define("NETCAT_MODULE_SEARCH_ADMIN_BACK", "Назад");
define("NETCAT_MODULE_SEARCH_ADMIN_ADD", "Добавить");
define("NETCAT_MODULE_SEARCH_ADMIN_SAVE", "Сохранить");
define("NETCAT_MODULE_SEARCH_ADMIN_EDIT", "Изменить");
define("NETCAT_MODULE_SEARCH_ADMIN_COPY", "Скопировать");
define("NETCAT_MODULE_SEARCH_ADMIN_DELETE", "Удалить");
define("NETCAT_MODULE_SEARCH_ADMIN_DELETE_SELECTED", "Удалить выбранное");
define("NETCAT_MODULE_SEARCH_ADMIN_ID", "ID");
define("NETCAT_MODULE_SEARCH_ADMIN_ACTIONS", "Действия");

define("NETCAT_MODULE_SEARCH_ADMIN_FILTER", "Найти");
define("NETCAT_MODULE_SEARCH_ADMIN_FILTER_RESET", "Сбросить фильтр");

define("NETCAT_MODULE_SEARCH_ADMIN_LANGUAGE", "Язык");
define("NETCAT_MODULE_SEARCH_ADMIN_EMPTY_LIST", "Список пуст");

define("NETCAT_MODULE_SEARCH_ADMIN_SAVE_OK", "Данные сохранены");
define("NETCAT_MODULE_SEARCH_ADMIN_SAVE_ERROR", "Ошибка при сохранении данных");

define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_SEARCH_DISABLED",
        "Поиск по сайту отключен. Для индексирования сайта необходимо <a href='%s' target='%s'>разрешить индексирование и поиск по сайту</a>.");

define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_INDEXING", "Настройки индексирования");

define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_ENABLE_SEARCH", "разрешить индексирование и поиск по сайту");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_DISABLE_SECTION_INDEXING", "Запретить индексирование страниц по регулярному выражению");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_MINIMUM_WORD_LENGTH", "Минимальная длина слова для индексирования %s символов");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_USE_STOPWORDS", "использовать списки <a href='%s' target='_top'>стоп-слов</a>");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_USE_ROBOTS_TXT", "учитывать инструкции robots.txt");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_MAX_DOCUMENT_LENGTH", "Максимальный размер индексируемых страниц %s байт");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CRAWLER_DELAY", "Пауза между запросами поискового робота %s секунд");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_IGNORE_NUMBERS", "не учитывать цифры при индексировании и поиске");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CHECK_LINKS", "проверять ссылки на сайте");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CHECK_EXTERNAL_LINKS", "проверять внешние ссылки");

define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_CORRECTION", "Исправление запросов, не давших результатов");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_CORRECTION_ENABLED", "Исправлять запросы, не давшие результатов");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CORRECTION_PHRASES", "искать фразы как обычный набор слов");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CORRECTION_BREAK_WORDS_UP", "искать пропущенные пробелы");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CORRECTION_LAYOUT", "исправлять раскладку клавиатуры");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CORRECTION_FUZZY",
        "использовать нечёткий поиск для слов, отсутствующих в словарях ".
        "(должен быть включён <a href='#fuzzy' class='internal on_page'>нечёткий поиск</a>)");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CORRECTION_MAXIMUM_QUERY_LENGTH", "Максимальная длина запроса для исправления %s слов");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_CORRECTION_SIMILARITY_FACTOR", "Коэффициент похожести, используемый при исправлении запросов: %s");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG", "История запросов");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_ENABLED", "Включить историю запросов");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE", "Удалять запросы");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE_NEVER", "никогда");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE_BEFORE", "раньше");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE_BEFORE_HOURS", "часов");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE_BEFORE_DAYS", "дней");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE_BEFORE_MONTHS", "месяцев");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE_NOW", "Очистить список поисковых запросов");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE_NOW_EVERYTHING", "полностью");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGE_NOW_SUBMIT", "Очистить");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_LOG_PURGED", "История запросов очищена.");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_FEATURES", "Пользовательские поисковые запросы");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_DEFAULT_OPERATOR", "Оператор по умолчанию");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_DEFAULT_OPERATOR_AND", "логическое И");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_DEFAULT_OPERATOR_OR", "логическое ИЛИ");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_TERM_BOOST", "разрешить указывать вес слов");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_PROXIMITY_SEARCH", "разрешить поиск с указанием расстояния между словами");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_WILDCARD_SEARCH", "разрешить поиск по шаблону");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_RANGE_SEARCH", "разрешить поиск по интервалу значений");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_FUZZY_SEARCH", "разрешить нечёткий поиск");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_FIELD_SEARCH", "разрешить поиск по полю");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_QUERY_FIELD_INVALID_NAME", "Введено некорректное имя поля.\n\n".
        "Имя поля может состоять только из латинских букв, цифр и символа подчёркивания, ".
        "и не должно совпадать с зарезервированными именами (title, content, last_updated, language).");

define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_RESULTS", "Отображение результатов");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_SHOW_MATCHED_FRAGMENT", "показывать фрагмент текста в списке найденных страниц");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_HIGHLIGHT_MATCHED", "выделять слова из запроса полужирным начертанием");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ALLOW_FIELD_SORT", "разрешать сортировку по полям (по дате/времени)");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_OPEN_LINKS_IN_NEW_WINDOW", "открывать ссылки в новом окне");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_RESULTS_TITLE_WORD_COUNT", "Максимальное количество слов в заголовке результатов");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_RESULTS_FRAGMENT_WORD_COUNT", "Максимальное количество слов в фрагменте текста");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_MAX_PREVIEW_TEXT_LENGTH", "Максимальная длина текста, сохраняемого для показа совпавших фрагментов");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_KBYTES", "КБайт");

define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ADVANCED_SEARCH", "Расширенный поиск");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ENABLE_ADVANCED_SEARCH_FORM", "разрешить расширенный поиск");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ADVANCED_SEARCH_FORM_OPTIONS", "Включить в форму расширенного поиска поля");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ADVANCED_SEARCH_EXCLUDE", "исключить страницы, где встречаются слова");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ADVANCED_SEARCH_FIELD", "расположение слов");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ADVANCED_SEARCH_DATETIME", "дата обновления");

define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_QUERY_SUGGEST", "Автозаполнение строки запроса");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_QUERY_SUGGEST_TITLES", "заголовки страниц");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_QUERY_SUGGEST_QUERIES", "запросы");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_QUERY_SUGGEST_DISABLED", "не применять");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_QUERY_SUGGEST_MINIMUM_LENGTH", "Минимальная длина запроса для срабатывания автозаполнения");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_QUERY_SUGGEST_NUMBER_OF_HITS", "Количество результатов в выпадающем списке");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_QUERY_SUGGEST_TITLES_SEARCH_IN_INDEX", "искать все формы введённых слов");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_QUERY_SUGGEST_TITLES_SEARCH_AS_PHRASE", "строгий порядок слов (искать как фразу)");

define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_FORM_TEMPLATES", "Шаблоны отображения для формы поиска по умолчанию");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ADVANCED_FORM_TEMPLATE", "Шаблон отображения расширенной формы поиска");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_FORM_TEMPLATES_MOBILE", "Шаблоны отображения для формы поиска по умолчанию (мобильный)");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ADVANCED_FORM_TEMPLATE_MOBILE", "Шаблон отображения расширенной формы поиска (мобильный)");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_FORM_TEMPLATES_RESPONSIVE", "Шаблоны отображения для формы поиска по умолчанию (адаптивный)");
define("NETCAT_MODULE_SEARCH_ADMIN_INTERFACE_ADVANCED_FORM_TEMPLATE_RESPONSIVE", "Шаблон отображения расширенной формы поиска (адаптивный)");

define("NETCAT_MODULE_SEARCH_ADMIN_SETTINGS_SAVE", "Сохранить настройки");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTINGS_SAVED", "Настройки сохранены.");

define("NETCAT_MODULE_SEARCH_ADMIN_SETTINGS_INCORRECT_PROVIDER_CLASS", "SearchProvider: класс %s должен реализовывать интерфейс nc_search_provider!");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTINGS_PROVIDER_CLASS_NOT_FOUND", "SearchProvider: класс %s не найден");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTINGS_PROVIDER_CLASS_INITIALIZATION_ERROR", "SearchProvider: возникла ошибка при инициализации класса %s (%s)");

define("NETCAT_MODULE_SEARCH_ADMIN_FIELDS", "HTML-документы");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_DOCUMENT_AREAS", "Область индексирования");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_DOCUMENT_CONTENT", "Область, индексируемая на HTML-страницах (используется первое совпавшее правило)");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_DOCUMENT_NOINDEX", "Области, содержимое которых не индексируется");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_TAG_WEIGHT", "Веса тэгов");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_TAGS", "Тэги");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_TAGS_HAVE_WEIGHT", "имеют вес");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_TAGS_DELETE", "удалить");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_TITLE_TAG_HAS_WEIGHT", "Тэг <code>TITLE</code> имеет вес");
define("NETCAT_MODULE_SEARCH_ADMIN_SETTING_FIELDS_SAVED",
        "Изменения вступят в силу при следующем переиндексировании. ".
        "<a href='%s' target='_top'>Перейти в раздел &laquo;Индексирование&raquo;</a>.");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION", "Извлечение данных");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_NAME", "Имя поля");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_QUERY", "Область HTML-документа");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_WEIGHT", "Вес");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_TYPE", "Тип данных");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_TYPE_STRING", "Строка");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_TYPE_INTEGER", "Целое число");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_ADD_FIELD", "Добавить поле");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_IS_INDEXED", "поле участвует в поиске");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_IS_SORTABLE", "разрешить сортировку по полю");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_IS_NORMALIZED", "нормализовывать текст");
define("NETCAT_MODULE_SEARCH_ADMIN_DATA_EXTRACTION_FIELD_IS_RETRIEVABLE", "значение доступно в результатах поиска");

define("NETCAT_MODULE_SEARCH_ADMIN_NO_BROKEN_LINKS", "При индексировании не было обнаружено некорректных ссылок.");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKEN_LINK_GROUP_BY", "Группировка по");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKEN_LINK_GROUP_BY_URL", "ссылкам");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKEN_LINK_GROUP_BY_REFERRER", "по ссылающимся страницам");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKEN_LINK_PREV_PAGE", "Предыдущая страница");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKEN_LINK_NEXT_PAGE", "Следующая страница");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKEN_LINK_EDIT", "редактировать");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKEN_LINKS_MENU_ITEM", "Неработающие ссылки");
define("NETCAT_MODULE_SEARCH_ADMIN_BROKEN_LINKS_REFERRER_LIMIT", "Показано только %s ссылающихся страниц.");

define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG", "События");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_FILTER", "Журнал событий");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_SETTINGS", "Настройки журнала событий");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_SHOW_SETTINGS", "Показать настройки журнала событий");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_LEVEL", "Сохранять в журнале события следующих типов");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_DELETE_PERIOD", "Хранить записи в течение %s дней");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_DELETE_ALL", "Очистить журнал");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_DELETED", "Журнал событий очищен.");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_EMPTY", "Журнал событий пуст.");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_TIME", "Время");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_TYPE", "Тип события");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_MESSAGE", "Сообщение");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_PREV_PAGE", "Ранее");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_LOG_NEXT_PAGE", "Позднее");

define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_ERROR", "Ошибка в работе модуля");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_PHP_EXCEPTION", "Исключение");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_PHP_ERROR", "Ошибка PHP");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_PHP_WARNING", "Предупреждение PHP");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_INDEXING_BEGIN_END", "Начало, окончание индексирования");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_INDEXING_NO_SUB", "Невозможность определения раздела документа");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_CRAWLER_REQUEST", "HTTP-запрос");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_PARSER_DOCUMENT_BRIEF", "Краткие сведения о полученном документе");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_PARSER_DOCUMENT_VERBOSE", "Полные сведения о полученном документе");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_PARSER_DOCUMENT_LINKS", "Добавление ссылки в очередь");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_SCHEDULER_START", "Запуск планировщика");
define("NETCAT_MODULE_SEARCH_ADMIN_EVENT_INDEXING_CONTENT_ERROR", "Ошибка при индексировании документа");

// Общие
define("NETCAT_MODULE_SEARCH_DATETIME_FORMAT", "%d.%m.%Y %H:%M");

// Поиск на сайте

define("NETCAT_MODULE_SEARCH_SUBMIT_BUTTON_TEXT", "Найти");
define("NETCAT_MODULE_SEARCH_ADVANCED_LINK_TEXT", "Расширенный поиск");

define("NETCAT_MODULE_SEARCH_FIND_CAPTION", "Я ищу");
define("NETCAT_MODULE_SEARCH_EXCLUDE_CAPTION", "Исключая");
define("NETCAT_MODULE_SEARCH_FIELD_CAPTION", "Слова");
define("NETCAT_MODULE_SEARCH_FIELD_ANY", "в любой части страницы");
define("NETCAT_MODULE_SEARCH_FIELD_TITLE", "только в заголовке");
define("NETCAT_MODULE_SEARCH_TIME_CAPTION", "Дата обновления");
define("NETCAT_MODULE_SEARCH_TIME_ANY", "все");
define("NETCAT_MODULE_SEARCH_TIME_LAST", "последние");
define("NETCAT_MODULE_SEARCH_TIME_LAST_HOURS", "часов");
define("NETCAT_MODULE_SEARCH_TIME_LAST_DAYS", "дней");
define("NETCAT_MODULE_SEARCH_TIME_LAST_WEEKS", "недель");
define("NETCAT_MODULE_SEARCH_TIME_LAST_MONTHS", "месяцев");

define("NETCAT_MODULE_SEARCH_NO_RESULTS", "По вашему запросу ничего не найдено.");
define("NETCAT_MODULE_SEARCH_RESULTS_RANGE", "Результаты %d&mdash;%d из %d");
define("NETCAT_MODULE_SEARCH_RESULTS_ONE", "Результат %d из %d");
define("NETCAT_MODULE_SEARCH_RESULTS_PREV", "предыдущая");
define("NETCAT_MODULE_SEARCH_RESULTS_NEXT", "следующая");
define("NETCAT_MODULE_SEARCH_SORT_BY", "Сортировать по: ");
define("NETCAT_MODULE_SEARCH_SORT_BY_LAST_MODIFIED", "дате изменения");
define("NETCAT_MODULE_SEARCH_SORT_BY_RELEVANCE", "релевантности");
define("NETCAT_MODULE_SEARCH_QUERY_ERROR", "Ошибка в запросе.");
define("NETCAT_MODULE_SEARCH_NO_TITLE", "Без заголовка");

// Подсказки при исправлении запросов
define("NETCAT_MODULE_SEARCH_CORRECTION_GENERIC", "По запросу &laquo;<span class='nc_query'>%s</span>&raquo; ничего не найдено. ".
        "<span class='nc_correction_suggesion'>Показаны результаты по запросу &laquo;<span class='nc_query'>%s</span>&raquo;.</span>");

define("NETCAT_MODULE_SEARCH_CORRECTION_QUOTES", "По запросу &laquo;<span class='nc_query'>%s</span>&raquo; ничего не найдено. ".
        "<span class='nc_correction_suggesion'>Показаны результаты по запросу без кавычек: &laquo;<span class='nc_query'>%s</span>&raquo;.</span>");

define("NETCAT_MODULE_SEARCH_PAGES", "Страницы");
define("NETCAT_MODULES_SEARCH_FROM", "из");

// Ошибки конфигурации PHP
define("NETCAT_MODULE_SEARCH_NO_DOM_ERROR", "Для работы модуля необходима поддержка <a href='http://php.net/DOM'>Document Object Model</a>.");
define("NETCAT_MODULE_SEARCH_NO_MB_EXTENSION_ERROR", "Для работы модуля необходимо расширение <a href='http://php.net/mbstring'>mbstring</a>.");
define("NETCAT_MODULE_SEARCH_MB_OVERLOAD_ENABLED_ERROR", "Для работы модуля необходимо отключить перезагрузку строковых функций ".
        "расширением <i>mbstring</i>. Измените в настройках PHP значение опции <i>mbstring.func_overload</i> на <i>0</i>.");
define("NETCAT_MODULE_SEARCH_PCRE_UTF_ERROR", "Для работы модуля необходима поддержка работы регулярных выражений в кодировке UTF-8. ".
        "При компиляции PHP следует использовать встроенную библиотеку PCRE или библиотеку PCRE, скомпилированную с опцией <i>--enable-unicode-properties</i>.");
define("NETCAT_MODULE_SEARCH_INDEX_DIRECTORY_NOT_WRITABLE_ERROR", "Папка <i>%s</i> недоступна для записи. ".
        "Убедитесь, что указанный путь существует и на папку установлены права, позволяющие запись в неё.");
define("NETCAT_MODULE_SEARCH_CANNOT_OPEN_INDEX_ERROR", "Невозможно открыть поисковый индекс. ".
        "Пожалуйста, сотрите все файлы в папке <i>/netcat_files/Search/Lucene/</i>, после чего запустите полное переиндексирование всех сайтов. ");

// Ошибки конфигурации сайтов
define("NETCAT_MODULE_SEARCH_SITE_WITHOUT_LANGUAGE_ERROR", "В настройках сайта %s не указан язык сайта. Корректное индексирование и поиск по данному сайту невозможны.");
define("NETCAT_MODULE_SEARCH_SITES_WITHOUT_LANGUAGE_ERROR", "В настройках сайтов %s не указан язык сайта. Корректное индексирование и поиск по данным сайтам невозможны.");
