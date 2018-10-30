<?php
/*
    Шаблон лежит в базе: MySQL » u335095.mysql.masterhost.ru » u335095_skd_test » Template
*/

if(strrpos($_SERVER['REQUEST_URI'], 'object'))
{
    if(strrpos($_SERVER['REQUEST_URI'], 'page')){

        $s = strrpos($_SERVER['REQUEST_URI'], '/?object')+9;
        $e = strlen($_SERVER['REQUEST_URI']);

        $url = '/galereya/stroitelstvo/?object='.substr($_SERVER['REQUEST_URI'], $s, $e);

    } else {
        $url = $_SERVER['REQUEST_URI'];
    }

    switch($url)
    {
        case '/galereya/stroitelstvo/?object=97'  : $ttl = "Дом из клееного бруса: разработка индивидуального проекта, кровельные работы, сборка стен"; break;
        case '/galereya/stroitelstvo/?object=103' : $ttl = "Дом из бруса с цокольным этажом по индивидуальному проекту"; break;
        case '/galereya/stroitelstvo/?object=101' : $ttl = "Индивидуальный дом из клееного бруса: внутренние стены, общий вид, кровля "; break;
        case '/galereya/stroitelstvo/?object=107' : $ttl = "Дом из клееного бруса СКД 175: общий вид"; break;
        case '/galereya/stroitelstvo/?object=131' : $ttl = "Дом из клееного бруса СКД 263: тепловой контур, сборка стен"; break;
        case '/galereya/stroitelstvo/?object=106' : $ttl = "Дом из клееного бруса: разработка индивидуального проекта, возведение стен"; break;
        case '/galereya/stroitelstvo/?object=111' : $ttl = "Дом из клееного бруса СКД 280, баня из бруса СКД 80: тепловой контур, навес с отдеокой и имитацией бруса, навес под авто"; break;
        case '/galereya/stroitelstvo/?object=114' : $ttl = "Дом из клееного бруса: тепловой контур, покраска, монтаж окон, подшив кровли"; break;
        case '/galereya/stroitelstvo/?object=129' : $ttl = "Дом из клееного бруса СКД 215: покраска, тепловой контур, возведение коробки, возведение стен, кровельные работы"; break;
        case '/galereya/stroitelstvo/?object=118' : $ttl = "Дом из клееного бруса по индивидуальному проекту: общий вид, возведение навеса"; break;
        case '/galereya/stroitelstvo/?object=139' : $ttl = "Дом из клееного бруса по индивидуальному проекту: покраска стен, сборка стен, разгрузка клееного бруса, сборка стен 2 этажа"; break;
        case '/galereya/stroitelstvo/?object=126' : $ttl = "Дом из клееного бруса СКД 233: покраска, тепловой контур, установка окон, монтаж кровли"; break;
        case '/galereya/stroitelstvo/?object=153' : $ttl = "Дом из клееного бруса по индивидуальному проекту: устройство дома, подборка цветового решения, сборка стен 2 этажа, установка окон, покраска стен"; break;
        case '/galereya/stroitelstvo/?object=113' : $ttl = "Дом из клееного бруса СКД 200: строительство теплового контура, общий вид"; break;
        case '/galereya/stroitelstvo/?object=140' : $ttl = "Дом из клееного бруса: общий вид, сборка стен из бруса"; break;
        case '/galereya/stroitelstvo/?object=105' : $ttl = "Дом из клееного бруса: общий вид, монтаж кровли, сборка стен, монтаж стропильной системы, утепление чашек"; break;
        case '/galereya/stroitelstvo/?object=112' : $ttl = "Дом из бруса СКД 80: общий вид, тепловой контур"; break;
        case '/galereya/stroitelstvo/?object=120' : $ttl = "Дом из клееного бруса СКД 200 и баня из клееного бруса: покрасочные работы, установка окон, тепловой контур"; break;
        case '/galereya/stroitelstvo/?object=124' : $ttl = "Дом из клееного бруса по индивидуальному проекту: цветовое решение, сборка дома"; break;
        case '/galereya/stroitelstvo/?object=154' : $ttl = "Дом из клееного бруса в Симферополе: отделочные работы, лестица в доме, устройство металлического каркаса, стропильная система"; break;
        case '/galereya/stroitelstvo/?object=123' : $ttl = "Дом из клееного бруса СКД 80: наружные светильники, отделочные работы в бане, декоративная форма с подкосами, сборка стен"; break;
        case '/galereya/stroitelstvo/?object=148' : $ttl = "Дом из клееного бруса: подбой кровли, сборка стен"; break;
        case '/galereya/stroitelstvo/?object=157' : $ttl = "Дом из клееного бруса СКД 233: монтаж кровли, установка окон, сборка стен"; break;
        case '/galereya/stroitelstvo/?object=152' : $ttl = "Баня из бруса по индивидуальному проекту"; break;
        case '/galereya/stroitelstvo/?object=116' : $ttl = "Дом из клееного бруса СКД 175: тепловой контур, внешний вид"; break;
        case '/galereya/stroitelstvo/?object=135' : $ttl = "Дом из клееного бруса: тепловой контур, покраска, ограждение балкона"; break;
        case '/galereya/stroitelstvo/?object=142' : $ttl = "Дом из клееного бруса СКД 215: устройство стропильной системы, возведение стен 2-го этажа, сборка стен"; break;
        case '/galereya/stroitelstvo/?object=127' : $ttl = "Дом из клееного бруса СКД 200: отделочные работы, лестница в доме, монтаж котельного оборудования, тепловой контур, устройство подшива кровли"; break;
        case '/galereya/stroitelstvo/?object=151' : $ttl = "Дом из клееного бруса 263: сборка стен из клееного бруса, скрытая электрика в брусовой стене, сборка первого венца, покраска, утройство вентиляционных коробов на крыше"; break;
        case '/galereya/stroitelstvo/?object=137' : $ttl = "Дом из клееного бруса СКД 390: отделочные работы, работы по электрике, сдача теплового контура"; break;
        case '/galereya/stroitelstvo/?object=150' : $ttl = "Дом из клееного бруса СКД 263: тепловой контур, стены и кровля"; break;
        case '/galereya/stroitelstvo/?object=144' : $ttl = "Дом из клееного бруса по индивидуальному проекту: установка окон, прединженерный этап, эркер на доме"; break;
        case '/galereya/stroitelstvo/?object=149' : $ttl = "Дом из клееного бруса СКД 390: установка окон, ограждение балкона и входной террасы, тепловой контур, утройство кровли"; break;
        case '/galereya/stroitelstvo/?object=121' : $ttl = "Баня по индивидуальному проекту: покраска, овнешний вид"; break;
        case '/galereya/stroitelstvo/?object=115' : $ttl = "Дом из клееного бруса СКД 200: покраска, строительство теплового контура"; break;
        case '/galereya/stroitelstvo/?object=147' : $ttl = "Дом из клееного бруса: ландшафтные работы, ограждение балкона и террасы, перегородки под гипсокартон, барбекю зона"; break;
        case '/galereya/stroitelstvo/?object=134' : $ttl = "Баня проект СКД 80: возведение стен, кровельные работы"; break;
        case '/galereya/stroitelstvo/?object=130' : $ttl = "Дом, баня и гараж по индивидуальному проекту СКД 200: тепловой контур, кровельные работы, установка окон"; break;
        case '/galereya/stroitelstvo/?object=128' : $ttl = "Дом из клееного бруса СКД 280: покраска стен, отделка дома, сооружение навеса, устройство стропильной системы, сборка стен"; break;
        case '/galereya/stroitelstvo/?object=119' : $ttl = "Дом из клееного бруса СКД 263: прединженерный этап, возведение каркасных перегородок, сборка стен"; break;
        case '/galereya/stroitelstvo/?object=145' : $ttl = "Дом из клееного бруса: индивидуальное проектирование, качественные материалы"; break;
        case '/galereya/stroitelstvo/?object=143' : $ttl = "Дом из клееного бруса СКД 280: устройство настила и балкона, терраса из лиственицы, устройство водостока, первые жители"; break;
        case '/galereya/stroitelstvo/?object=141' : $ttl = "Дом из клееного бруса СКД 280: возведение стен 1-го этажа"; break;
        case '/galereya/stroitelstvo/?object=159' : $ttl = "Баня из клееного бруса: возведение стен, материалы для строительства"; break;
        case '/galereya/stroitelstvo/?object=133' : $ttl = "Дом из клееного бруса СКД 280: устройство кровли, сборка стен из бруса, разгрузка материалов, установка перегородок"; break;
        case '/galereya/stroitelstvo/?object=138' : $ttl = "Дом из клееного бруса СКД 280: создание теплового контура, устройство кровли, стропильная система в сборе"; break;
        case '/galereya/stroitelstvo/?object=155' : $ttl = "Дом из клееного бруса: разгрузка и сборка стен 1-го этажа"; break;
        case '/galereya/stroitelstvo/?object=160' : $ttl = "Строительство дома из клееного бруса: установка стен, монтаж кровли"; break;
        case '/galereya/stroitelstvo/?object=122' : $ttl = "Дом из клееного бруса СКД 200: установка окон, внешний вид, установка забора"; break;
        case '/galereya/stroitelstvo/?object=132' : $ttl = "Дом из клееного бруса СКД 415: тепловой контур, пристроенный гараж, сборка стен"; break;
        case '/galereya/stroitelstvo/?object=156' : $ttl = "Дом из клееного бруса СКД 215: устройство ограждений балкона, кровельные работы, монтаж стропильной системы"; break;
        case '/galereya/stroitelstvo/?object=136' : $ttl = "Дом из клееного бруса: подборка цвета, сборка стропил, возведение стен, внешний вид"; break;
        case '/galereya/stroitelstvo/?object=92'  : $ttl = "Дом из клееного бруса СКД 390: покраска клееного бруса, установка оконных систем, сборка стен, сборка бруса, материалы на стройплощадке"; break;
        case '/galereya/stroitelstvo/?object=80'  : $ttl = "Дом из клееного бруса по индивидуальному проекту: монтаж мягкой кровли, сборка перегородок, покраска"; break;
        case '/galereya/stroitelstvo/?object=10'  : $ttl = "Дом из клееного бруса: фронтальный вид, оконная система"; break;
        case '/galereya/stroitelstvo/?object=66'  : $ttl = "Дом из клееного бруса СКД: проект и строительство дома, основной этап, покраска"; break;
        case '/galereya/stroitelstvo/?object=62'  : $ttl = "Дом из клееного бруса: готовый дом, покраска, внешний вид, установлены современные окна"; break;
        case '/galereya/stroitelstvo/?object=65'  : $ttl = "Дом из клееного бруса: дом перед покраской, индивидуальный проект, установка окон, постройка бани, сборка кровли"; break;
        case '/galereya/stroitelstvo/?object=86'  : $ttl = "Дом из клееного бруса: монтаж стропильной системы, внешний вид без кровли"; break;
        case '/galereya/stroitelstvo/?object=54'  : $ttl = "Дом из клееного бруса: отделочные работы, утепление полов, сборка стен из бруса, пропитка бруса"; break;
        case '/galereya/stroitelstvo/?object=64'  : $ttl = "Дом из клееного бруса: проектировани, внешний вид, ландшафтный дизайн"; break;
        case '/galereya/stroitelstvo/?object=95'  : $ttl = "Дом из клееного бруса: возведен тепловой контур дома, установка водосточной системы, кровельные работы, подшива кровли, возведение стен"; break;
        case '/galereya/stroitelstvo/?object=98'  : $ttl = "Дом из клееного бруса: инженерные работы, утройство котельного оборудования, работы по черновой электрике, покраска"; break;
        case '/galereya/stroitelstvo/?object=57'  : $ttl = "Дом из клееного бруса: устройство террассы с навесом и ограждением, фасад дома"; break;
        case '/galereya/stroitelstvo/?object=58'  : $ttl = "Дом из клееного бруса: укладка пола на застекленнной террасе, покраска дома, установка котельного оборудования, установка камина"; break;
        case '/galereya/stroitelstvo/?object=87'  : $ttl = "Дом из клееного бруса: внешний вид без окон, утсройство пароизоляции кровельного пирога, возведение стропильной ситемы"; break;
        case '/galereya/stroitelstvo/?object=100' : $ttl = "Дом из клееного бруса СКД 280 и баня из СКД 80: строительство бани, тепловой контур дома, сборка стен"; break;
        case '/galereya/stroitelstvo/?object=73'  : $ttl = "Дом из клееного бруса СКД 280: установка напольного конвектора, монтаж евровагонки на потолок, установка откосов "; break;
        case '/galereya/stroitelstvo/?object=72'  : $ttl = "Дом из клееного бруса СКД 280: построенный дом, покраска, настил фальцовой кровли"; break;
        case '/galereya/stroitelstvo/?object=46'  : $ttl = "Дом из клееного бруса СКД 280: стены окрашеные в выбранный цвет, фасад дома, эргономичный навес"; break;
        case '/galereya/stroitelstvo/?object=60'  : $ttl = "Дом из клееного бруса СКД 390: сборка стен, монтаж стропил, укрепение фундамента"; break;
        case '/galereya/stroitelstvo/?object=44'  : $ttl = "Дом из клееного бруса СКД 390: возведение навеса, ландшафтный дизайн, первоначальная сборка"; break;
        case '/galereya/stroitelstvo/?object=28'  : $ttl = "Дом из клееного бруса СКД 175: наруная отдеока, оригинальная кровля"; break;
        case '/galereya/stroitelstvo/?object=89'  : $ttl = "Дом из клееного бруса СКД 175: крыша из металлочерепицы, компактный навес"; break;
        case '/galereya/stroitelstvo/?object=41'  : $ttl = "Дом из клееного бруса СКД 215: дом с крышей из металлочерепицы и пластиковыми окнами"; break;
        case '/galereya/stroitelstvo/?object=24'  : $ttl = "Дом из клееного бруса СКД 215: фронтальный вид, фасад дома"; break;
        case '/galereya/stroitelstvo/?object=90'  : $ttl = "Дом из клееного бруса СКД 215: тепловой контур с навесами, сборка стен из бруса"; break;
        case '/galereya/stroitelstvo/?object=42'  : $ttl = "Дом из клееного бруса СКД 215: монтаж электрики, укладка пола"; break;
        case '/galereya/stroitelstvo/?object=21'  : $ttl = "Дом из клееного бруса СКД 263: общий вид строительства"; break;
        case '/galereya/stroitelstvo/?object=1'   : $ttl = "Дом из клееного бруса СКД 263: индивидуальный проект, навес для машин, летняя веранда"; break;
        case '/galereya/stroitelstvo/?object=5'   : $ttl = "Дом из клееного бруса СКД 263: внутренная отделка стен, отделка кухни, установка лестницы"; break;
        case '/galereya/stroitelstvo/?object=78'  : $ttl = "Дом из клееного бруса СКД 280: возведение теплового контура для дома, внешние отделочные работы"; break;
        case '/galereya/stroitelstvo/?object=36'  : $ttl = "Дом из клееного бруса СКД 280: оригинальный проект, сборка дома"; break;
        case '/galereya/stroitelstvo/?object=69'  : $ttl = "Дом из клееного бруса СКД 280: возведение стен, сборка лестницы, установка кровли, оригинальный проект"; break;
        case '/galereya/stroitelstvo/?object=84'  : $ttl = "Дом из клееного бруса СКД 280: возведение стропильной системы, подготовка фундамента и грунта"; break;
        case '/galereya/stroitelstvo/?object=53'  : $ttl = "Дом из клееного бруса СКД 280: отделочные и покрасочные работы"; break;
        case '/galereya/stroitelstvo/?object=81'  : $ttl = "Дом из клееного бруса СКД 360: покрашенный дом, инженерные работы, установка теплого пола, навес под автомобиль"; break;
        case '/galereya/stroitelstvo/?object=61'  : $ttl = "Дом из клееного бруса СКД 360: стены из бруса, пластиковые окна, фасад дома"; break;
        case '/galereya/stroitelstvo/?object=6'   : $ttl = "Дом из клееного бруса СКД 360: работы по устройству террасы, внутренние отделочные работы"; break;
        case '/galereya/stroitelstvo/?object=29'  : $ttl = "Дом из клееного бруса СКД 360: внешний вид с кровлей, строительная площадка, монтаж навеса"; break;

        case '/galereya/stroitelstvo/?object=26'  : $ttl = "Дом из клееного бруса СКД 390: вариант строительства, проект компании, готовый дом, клееный брус"; break;
        case '/galereya/stroitelstvo/?object=85'  : $ttl = "Дом из клееного бруса СКД 390: дом под ключ, баня, котельная, кухня, ванная, хозблок"; break;
        case '/galereya/stroitelstvo/?object=75'  : $ttl = "Дом из клееного бруса СКД 200: возведение стен, погрузка материалов, строительство фасада"; break;
        case '/galereya/stroitelstvo/?object=45'  : $ttl = "Дом из клееного бруса СКД 290: фасад дома, остекление, навес"; break;
        case '/galereya/stroitelstvo/?object=74'  : $ttl = "Дом из клееного бруса СКД 215: навес под авто, покраска дома, подготовка к сдаче, колодец, ограда"; break;
        case '/galereya/stroitelstvo/?object=59'  : $ttl = "Дом из клееного бруса СКД 263: индивидуальный проект, дом + баня, декоративные элементы в строительстве"; break;
        case '/galereya/stroitelstvo/?object=11'  : $ttl = "Индивидуальный проект, дом + баня из клееного бруса"; break;
        case '/galereya/stroitelstvo/?object=110'  : $ttl = "Хозблок-гараж из клееного бруса, строительная площадка, монтаж навеса"; break;
        case '/galereya/stroitelstvo/?object=91'  : $ttl = "Дом из клееного бруса СКД 200: тепловой контур, остекление, фасад, навес под автомобиль"; break;
        case '/galereya/stroitelstvo/?object=32'  : $ttl = "Дом из клееного бруса СКД 215 + баня: установка отопительного оборудования, сборка стен"; break;
        case '/galereya/stroitelstvo/?object=96'  : $ttl = "Индивидуальный дом с цокольным этажом, покраска, устройство кровли, укладка кровельного пирога"; break;
        case '/galereya/stroitelstvo/?object=109'  : $ttl = "Гостевой Дом: внутренние отделочные работы"; break;
        case '/galereya/stroitelstvo/?object=52'  : $ttl = "Дом из клееного бруса СКД 263: работы на крыше, внутренние работы, укладка утеплителя"; break;
        case '/galereya/stroitelstvo/?object=94'  : $ttl = "Дом из клееного бруса СКД 280 + Баня: монтаж кровли, возведение стен, фундамент, внешний вид бани, "; break;
        case '/galereya/stroitelstvo/?object=79'  : $ttl = "Дом из клееного бруса СКД 390: покраска дома из бруса, сборка стен, укладка кровли, летние навесы"; break;
        case '/galereya/stroitelstvo/?object=9999'  : $ttl = "Дом из клееного бруса СКД 415: фронтальный вид, кровельные работы, погрузка материалов"; break;
        case '/galereya/stroitelstvo/?object=56'  : $ttl = "Дом из клееного бруса СКД 175: укладка теплого пола, установка котельной, вид сбоку, второй этаж"; break;
        case '/galereya/stroitelstvo/?object=99'  : $ttl = "Индивидуальная баня: баня из бруса с бассейном, индивидуальный проект, сборка стен, установка бассейна"; break;
        case '/galereya/stroitelstvo/?object=1000'  : $ttl = "Баня СКД 80: ландшафтные работы, выполнение террасы, отделка парной и монтаж печи"; break;

	case '/galereya/stroitelstvo/?object=162'  : $ttl = "СКД 200 Тепловой контур"; break;
	case '/galereya/stroitelstvo/?object=161'  : $ttl = "Устройство террасы"; break;
	case '/galereya/stroitelstvo/?object=158'  : $ttl = "СКД 80: Устройство металочерепицы, кровельного пирога, стропильной системы кровли, сборка стен бани"; break;
    }

    if(strrpos($_SERVER['REQUEST_URI'], 'page')){

        $s = strrpos($_SERVER['REQUEST_URI'], 'page-')+5;

        $page = substr($_SERVER['REQUEST_URI'], $s, 1);


        $ttl = $ttl . ' - фотоотчет стр №'.$page. ' - СКД дом';
    }
    else {
        $ttl .= " - фотоотчет - СКД дом";
    }
}