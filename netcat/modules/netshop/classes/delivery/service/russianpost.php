<?php

class nc_netshop_delivery_service_russianpost extends nc_netshop_delivery_service {

    /** @var string название службы */
    protected $name = NETCAT_MODULE_NETSHOP_DELIVERY_RUSSIANPOST;

    /** @var string тип доставки */
    protected $delivery_type = nc_netshop_delivery::DELIVERY_TYPE_POST;

    /** @var array поля, которым нужны соответствия */
    protected $mapped_fields = array(
        'from_city' => NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_FROM_CITY,
        'to_zipcode' => NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_TO_ZIP_CODE,
        'to_region' => NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_TO_REGION,
        'to_district' => NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_TO_DISTRICT,
        'to_city' => NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_TO_CITY,
    );

    /** @var int максимальный вес посылки (в граммах) */
    protected $max_weight = 50000;

    // название свойств в ответе для получения данных о стоимости и сроках
    protected $request_posting_kind = 'PARCEL';
    protected $request_way_forward = 'EARTH';
    protected $response_time_range_property = 'deliveryTimeRange';
    protected $response_time_property = 'deliveryTime';
    protected $response_min_time_property = 'deliveryTime';
    protected $response_max_time_property = 'deliveryTime';

    private $countries = array(
        'АВСТРАЛИЯ' => 36,
        'АВСТРИЯ' => 40,
        'АЗЕРБАЙДЖАН' => 31,
        'АЛАНДСКИЕ ОСТРОВА' => 949,
        'АЛБАНИЯ' => 8,
        'АЛЖИР' => 12,
        'АНГИЛЬЯ' => 660,
        'АНГОЛА' => 24,
        'АНДОРРА' => 20,
        'АНТАРКТИКА' => 10,
        'АНТИГУА И БАРБУДА' => 28,
        'АРГЕНТИНА' => 32,
        'АРМЕНИЯ' => 51,
        'АРУБА' => 533,
        'АФГАНИСТАН' => 4,
        'БАГАМСКИЕ ОСТРОВА' => 44,
        'БАНГЛАДЕШ' => 50,
        'БАРБАДОС' => 52,
        'БАХРЕЙН' => 48,
        'БЕЛИЗ' => 84,
        'БЕЛОРУССИЯ' => 112,
        'БЕЛЬГИЯ' => 56,
        'БЕНИН' => 204,
        'БЕРМУДСКИЕ ОСТРОВА' => 60,
        'БОЛГАРИЯ' => 100,
        'БОЛИВИЯ' => 68,
        'БОСНИЯ И ГЕРЦЕГОВИНА' => 70,
        'БОТСВАНА' => 72,
        'БРАЗИЛИЯ' => 76,
        'БРИТАНСКИЕ ВИРГИНСКИЕ ОСТРОВА' => 92,
        'БРИТАНСКИЕ ТЕРРИТОРИИ В ИНДИЙСКОМ ОКЕАНЕ' => 86,
        'БРУНЕЙ' => 96,
        'БУВЕ ОСТРОВА' => 74,
        'БУРКИНА-ФАСО' => 854,
        'БУРУНДИ' => 108,
        'БУТАН' => 64,
        'ВАНУАТУ' => 548,
        'ВАТИКАН' => 336,
        'ВЕЛИКОБРИТАНИЯ' => 826,
        'ВЕНГРИЯ' => 348,
        'ВЕНЕСУЭЛА' => 862,
        'ВИРГИНСКИЕ ОСТРОВА (США)' => 850,
        'ВОСТОЧНОЕ САМОА' => 16,
        'ВОСТОЧНЫЙ ТИМОР' => 626,
        'ВЬЕТНАМ' => 704,
        'ГАБОН' => 266,
        'ГАИТИ' => 332,
        'ГАЙАНА' => 328,
        'ГАМБИЯ' => 270,
        'ГАНА' => 288,
        'ГВАДЕЛУПА' => 312,
        'ГВАТЕМАЛА' => 320,
        'ГВИНЕЯ' => 324,
        'ГВИНЕЯ-БИСАУ' => 624,
        'ГЕРМАНИЯ' => 276,
        'ГИБРАЛТАР' => 292,
        'ГОНДУРАС' => 340,
        'ГОНКОНГ' => 344,
        'ГРЕНАДА' => 308,
        'ГРЕНЛАНДИЯ' => 304,
        'ГРЕЦИЯ' => 300,
        'ГРУЗИЯ' => 268,
        'ГУАМ' => 316,
        'ДАНИЯ' => 208,
        'ДЕМОКРАТИЧЕСКАЯ РЕСПУБЛИКА КОНГО' => 180,
        'ДЖИБУТИ' => 262,
        'ДОМИНИКА' => 212,
        'ДОМИНИКАНСКАЯ РЕСПУБЛИКА' => 214,
        'ЕГИПЕТ' => 818,
        'ЗАМБИЯ' => 894,
        'ЗАПАДНАЯ САХАРА' => 732,
        'ЗАПАДНОЕ САМОА' => 882,
        'ЗИМБАБВЕ' => 716,
        'ИЗРАИЛЬ' => 376,
        'ИНДИЯ' => 356,
        'ИНДОНЕЗИЯ' => 360,
        'ИОРДАНИЯ' => 400,
        'ИРАК' => 368,
        'ИРАН' => 364,
        'ИРЛАНДИЯ' => 372,
        'ИСЛАНДИЯ' => 352,
        'ИСПАНИЯ' => 724,
        'ИТАЛИЯ' => 380,
        'ЙЕМЕН' => 887,
        'КАБО-ВЕРДЕ' => 132,
        'КАЗАХСТАН' => 398,
        'КАЙМАНОВЫ ОСТРОВА' => 136,
        'КАМБОДЖА' => 116,
        'КАМЕРУН' => 120,
        'КАНАДА' => 124,
        'КАТАР' => 634,
        'КЕНИЯ' => 404,
        'КИПР' => 196,
        'КИРГИЗИЯ' => 417,
        'КИРИБАТИ' => 296,
        'КИТАЙ' => 156,
        'КНДР' => 408,
        'КОКОС ОСТРОВА' => 166,
        'КОЛУМБИЯ' => 170,
        'КОМОРСКИЕ ОСТРОВА' => 174,
        'КОРЕЯ' => 410,
        'КОСТА-РИКА' => 188,
        'КОТ-Д"ИВУАР' => 384,
        'КУБА' => 192,
        'КУВЕЙТ' => 414,
        'КУКА ОСТРОВА' => 184,
        'ЛАОС' => 418,
        'ЛАТВИЯ' => 428,
        'ЛЕСОТО' => 426,
        'ЛИБЕРИЯ' => 430,
        'ЛИВАН' => 422,
        'ЛИВИЯ' => 434,
        'ЛИТВА' => 440,
        'ЛИХТЕНШТЕЙН' => 438,
        'ЛЮКСЕМБУРГ' => 442,
        'МАВРИКИЙ' => 480,
        'МАВРИТАНИЯ' => 478,
        'МАДАГАСКАР' => 450,
        'МАЙОТТ ОСТРОВ' => 175,
        'МАКАО' => 446,
        'МАКЕДОНИЯ' => 807,
        'МАЛАВИ' => 454,
        'МАЛАЙЗИЯ' => 458,
        'МАЛИ' => 466,
        'МАЛЬДИВСКИЕ ОСТРОВА' => 462,
        'МАЛЬТА' => 470,
        'МАРОККО' => 504,
        'МАРТИНИКА' => 474,
        'МАРШАЛЛОВЫ ОСТРОВА' => 584,
        'МЕКСИКА' => 484,
        'МИКРОНЕЗИЯ' => 583,
        'МОЗАМБИК' => 508,
        'МОЛДАВИЯ' => 498,
        'МОНАКО' => 492,
        'МОНГОЛИЯ' => 496,
        'МОНТСЕРРАТ' => 500,
        'МЬЯНМА' => 104,
        'НАМИБИЯ' => 516,
        'НАУРУ' => 520,
        'НЕПАЛ' => 524,
        'НИГЕР' => 562,
        'НИГЕРИЯ' => 566,
        'НИДЕРЛАНДСКИЕ АНТИЛЫ' => 530,
        'НИДЕРЛАНДЫ' => 528,
        'НИКАРАГУА' => 558,
        'НИУЭ' => 570,
        'НОВАЯ ЗЕЛАНДИЯ' => 554,
        'НОВАЯ КАЛЕДОНИЯ' => 540,
        'НОРВЕГИЯ' => 578,
        'НОРФОЛК ОСТРОВ' => 574,
        'ОБЪЕДИНЕННЫЕ АРАБСКИЕ ЭМИРАТЫ' => 784,
        'ОМАН' => 512,
        'ПАКИСТАН' => 586,
        'ПАЛАУ' => 585,
        'ПАНАМА' => 591,
        'ПАПУА НОВАЯ ГВИНЕЯ' => 598,
        'ПАРАГВАЙ' => 600,
        'ПЕРУ' => 604,
        'ПИТКЕРН' => 612,
        'ПОДОПЕЧНЫЕ ТЕРРИТОРИИ США В ТИХОМ ОКЕАНЕ' => 581,
        'ПОЛЬША' => 616,
        'ПОРТУГАЛИЯ' => 620,
        'ПУЭРТО-РИКО' => 630,
        'РЕСПУБЛИКА КОНГО' => 178,
        'РЕЮНЬОН' => 638,
        'РОЖДЕСТВА ОСТРОВ' => 162,
        'РУАНДА' => 646,
        'РУМЫНИЯ' => 642,
        'САЛЬВАДОР' => 222,
        'САН-МАРИНО' => 674,
        'САН-ТОМЕ И ПРИНСИПИ' => 678,
        'САУДОВСКАЯ АРАВИЯ' => 682,
        'СВАЗИЛЕНД' => 748,
        'СВЯТОЙ ЕЛЕНЫ ОСТРОВ' => 654,
        'СЕВЕРНЫЕ МАРИАНСКИЕ ОСТРОВА' => 580,
        'СЕЙШЕЛЬСКИЕ ОСТРОВА' => 690,
        'СЕН-ПЬЕР И МИКЕЛОН' => 666,
        'СЕНЕГАЛ' => 686,
        'СЕНТ-ВИНСЕНТ И ГРЕНАДИНЫ' => 670,
        'СЕНТ-КИТТС И НЕВИС' => 659,
        'СЕНТ-ЛЮСИЯ' => 662,
        'СЕРБИЯ' => 688,
        'СЕРБИЯ И ЧЕРНОГОРИЯ' => 950,
        'СИНГАПУР' => 702,
        'СИРИЯ' => 760,
        'СЛОВАКИЯ' => 703,
        'СЛОВЕНИЯ' => 705,
        'СОЛОМОНОВЫ ОСТРОВА' => 90,
        'СОМАЛИ' => 706,
        'СУДАН' => 736,
        'СУРИНАМ' => 740,
        'США' => 840,
        'СЬЕРРА-ЛЕОНЕ' => 694,
        'ТАДЖИКИСТАН' => 762,
        'ТАИЛАНД' => 764,
        'ТАЙВАНЬ' => 158,
        'ТАНЗАНИЯ' => 834,
        'ТЕРКС И КАЙКОС ОСТРОВА' => 796,
        'ТОГО' => 768,
        'ТОКЕЛАУ' => 772,
        'ТОНГА' => 776,
        'ТРИНИДАД И ТОБАГО' => 780,
        'ТУВАЛУ' => 798,
        'ТУНИС' => 788,
        'ТУРКМЕНИСТАН' => 795,
        'ТУРЦИЯ' => 792,
        'УГАНДА' => 800,
        'УЗБЕКИСТАН' => 860,
        'УКРАИНА' => 804,
        'УОЛЛEС И ФУТУНА ОСТРОВА' => 876,
        'УРУГВАЙ' => 858,
        'ФАРЕРСКИЕ ОСТРОВА' => 234,
        'ФИДЖИ' => 242,
        'ФИЛИППИНЫ' => 608,
        'ФИНЛЯНДИЯ' => 246,
        'ФОЛКЛЕНДСКИЕ (МАЛЬВИНСКИЕ) ОСТРОВА' => 238,
        'ФРАНЦИЯ' => 250,
        'ФРАНЦИЯ, МЕТРОПОЛИЯ' => 249,
        'ФРАНЦУЗСКАЯ ГВИАНА' => 254,
        'ФРАНЦУЗСКАЯ ПОЛИНЕЗИЯ' => 258,
        'ХЕРД И МАКДОНАЛЬД ОСТРОВА' => 334,
        'ХОРВАТИЯ' => 191,
        'ЦЕНТРАЛЬНО-АФРИКАНСКАЯ РЕСПУБЛИКА' => 140,
        'ЧАД' => 148,
        'ЧЕРНОГОРИЯ' => 499,
        'ЧЕХИЯ' => 203,
        'ЧИЛИ' => 152,
        'ШВЕЙЦАРИЯ' => 756,
        'ШВЕЦИЯ' => 752,
        'ШПИЦБЕРГЕН И ЯН-МАЙЕН' => 744,
        'ШРИ ЛАНКА' => 144,
        'ЭКВАДОР' => 218,
        'ЭКВАТОРИАЛЬНАЯ ГВИНЕЯ' => 226,
        'ЭРИТРЕЯ' => 232,
        'ЭСТОНИЯ' => 233,
        'ЭФИОПИЯ' => 231,
        'ЮГОСЛАВИЯ' => 891,
        'ЮЖНАЯ ДЖОРДЖИЯ И ЮЖНЫЕ САНДВИЧЕВЫ ОСТРОВА' => 239,
        'ЮЖНАЯ ОСЕТИЯ' => 896,
        'ЮЖНО-АФРИКАНСКАЯ РЕСПУБЛИКА' => 710,
        'ЮЖНЫЕ ФРАНЦУЗСКИЕ ТЕРРИТОРИИ' => 260,
        'ЯМАЙКА' => 388,
        'ЯПОНИЯ' => 392,
    );

    /**
     * Рассчитать стоимость посылки.
     * При успешном выполнении возвращается массив:
     * array(
     *     'price' => стоимость доставки,
     *     'currency' => 'RUB',
     *     'min_days' => минимальный срок доставки
     *     'max_days' => максимальный срок доставки
     * )
     *
     * При ошибке возвращается null, ошибку можно получить из $this->get_last_error()
     *
     * @return array|null
     */
    public function calculate_delivery() {
        $nc_core = nc_Core::get_object();

        $delivery_data = $this->data;

        $weight = ceil($delivery_data['weight']); // вес в граммах

        if ($weight <= 0 || $weight > $this->max_weight) {
            $this->last_error = NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_INCORRECT_WEIGHT;
            $this->last_error_code = self::ERROR_WRONG_WEIGHT;
            return null;
        }

        $valuation = $delivery_data['valuation'] ? ceil($delivery_data['valuation']) : 0;

        // Откуда посылаем
        $from_city = $delivery_data['from_city']
                        ?: nc_get_list_item_name('Region', nc_netshop::get_instance()->get_setting('City'))
                        ?: ($nc_core->NC_UNICODE ? 'Москва' : "\xCC\xEE\xF1\xEA\xE2\xE0");
        $from = $this->get_location_data(null, null, null, $from_city);

        if (!$from['index']) {
            $this->last_error_code = self::ERROR_WRONG_SENDER;
            $this->last_error = NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_INCORRECT_SENDER_ADDRESS;
            return null;
        }

        // Куда посылаем
        $to_country = $nc_core->NC_UNICODE ? $delivery_data['to_country'] : $nc_core->utf8->win2utf($delivery_data['to_country']);
        $to_country = mb_strtoupper($to_country, 'utf-8');

        $to_country_code = null;
        $international =
            $delivery_data['to_country'] &&
            $to_country != 'РОССИЯ' &&
            $to_country != 'РОССИЙСКАЯ ФЕДЕРАЦИЯ';

        if ($international) {
            $to_country_code = nc_array_value($this->countries, $to_country);

            $to = array(
                'region' => $nc_core->NC_UNICODE ? $delivery_data['to_region'] : $nc_core->utf8->win2utf($delivery_data['to_region']),
                'district' => '',
                'city' => $nc_core->NC_UNICODE ? $delivery_data['to_city'] : $nc_core->utf8->win2utf($delivery_data['to_city']),
                'index' => '',
            );
        }
        else {
            $to = $this->get_location_data($delivery_data['to_zipcode'], $delivery_data['to_region'], $delivery_data['to_district'], $delivery_data['to_city']);
        }

        if (($international && !$to_country_code) || (!$international && !($to['index']))) {
            $this->last_error_code = self::ERROR_WRONG_RECIPIENT;
            $this->last_error = NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_INCORRECT_RECIPIENT_ADDRESS;
            return null;
        }

        $calculation_entity = array(
            'postingType' => $international ? 'MPO' : 'VPO',
            'zipCodeFrom' => $from['index'],
            'zipCodeTo' => $to['index'],
            'postalCodesFrom' => array($from['index']),
            'postalCodesTo' => array($to['index']),
            'weight' => $weight,
            'wayForward' => $this->request_way_forward,
            'postingKind' => $this->request_posting_kind,
            'postingCategory' => 'WITH_DECLARED_VALUE', // без объявленной ценности — 'ORDINARY',
            'parcelKind' => 'STANDARD',
            'declaredValue' => $valuation,
        );

        if ($international) {
            $calculation_entity['countryTo'] = $to_country_code;
        }

        $request = array(
            'calculationEntity' => array(
                'origin' =>
                    array(
                        'country' => 'Россия',
                        'region' => $from['region'],
                        'district' => $from['district'],
                        'city' => $from['city'],
                    ),
                'destination' =>
                    array(
                        'country' => $international ? $delivery_data['to_country'] : 'Россия',
                        'region' => $to['region'],
                        'district' => $to['district'],
                        'city' => $to['city'],
                    ),
                'sendingType' => 'PACKAGE'
            ),
            'costCalculationEntity' => $calculation_entity,
            'minimumCostEntity' =>
                array(
                    'standard' => $calculation_entity,
                    'firstClass' => $calculation_entity,
                    'ems' => $calculation_entity,
                ),
            'productPageState' =>
                array(
                    'cashOnDelivery' => false,
                    'ems' => false,
                    'rapid' => false,
                    'international' => $international,
                    'standard' => true,
                    'fromCity' => '',
                    'fromCountry' => '',
                    'fromDistrict' => '',
                    'fromRegion' => '',
                    'toCity' => '',
                    'toCountry' => '',
                    'toDistrict' => '',
                    'toRegion' => '',
                    'weight' => $weight,
                    'showAsKg' => false,
                    'cost' => 0,
                    'insuranceSum' => null,
                    'cashOnDeliverySum' => null,
                    'mainType' => 'standardParcel',
                    'sizeType' => 'items',
                    'emsTerm' => '',
                    'firstClassTerm' => '',
                    'standardTerm' => '',
                    'productType' => 'PARCEL',
                    'printSummary' => true,
                    'sourceHasCourier' => true,
                    'destinationHasCourier' => false,
                    'costDetailsColumns' => null,
                    'costDetailsSummary' => array(''),
                    'costDetailsRows' => array(array('', '0.00')),
                    'costDetailsSummaryCostNds' => '0,00',
                    'costDetailsSummaryCostMark' => null,
                ),
        );

        $url = 'https://www.pochta.ru/portal-portlet/delegate/calculator/v1/api/delivery.time.cost.get';
        $headers = array(
            'Content-Type' => 'application/json',
            'Referer' => 'https://www.pochta.ru/parcels',
        );

        $result = $this->make_http_request($url, nc_array_json($request), $headers);

        if (!$result) {
            $this->last_error_code = self::ERROR_CANNOT_CONNECT_TO_GATE;
            $this->last_error = NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_NO_REMOTE_SERVER_RESPONSE;
            return null;
        }

        $response = json_decode($result, JSON_OBJECT_AS_ARRAY);
        if (!$response || !isset($response['data']['costEntity']['cost']) || !$response['data']['costEntity']['cost']) {
            $this->last_error_code = self::ERROR_GATE_ERROR;
            $this->last_error = NETCAT_MODULE_NETSHOP_DELIVERY_METHOD_SERVICE_INCORRECT_REMOTE_SERVER_RESPONSE;
            return null;
        }

        if (!empty($response['data']['timeEntity'][$this->response_time_range_property])) {
            $time_range = $response['data']['timeEntity'][$this->response_time_range_property];
        }
        else if (!empty($response['data']['timeEntity'][$this->response_time_property])) {
            // для стандартной доставки deliveryTime иногда содержит диапазон, deliveryTimeRange всегда пустой
            $time_range = $response['data']['timeEntity'][$this->response_time_property];
        }
        else {
            $time_range = null;
        }

        if ($time_range && preg_match('/\D+/', $time_range)) {
            list($min_days, $max_days) = preg_split('/\D+/', $time_range);
        }
        else {
            $min_days = $response['data']['timeEntity'][$this->response_min_time_property] ?: $time_range;
            $max_days = $response['data']['timeEntity'][$this->response_max_time_property] ?: $time_range;
        }

        return array(
            'price' => $response['data']['costEntity']['cost'],
            'currency' => 'RUB',
            'min_days' => $min_days,
            'max_days' => $max_days,
        );
    }

    /**
     * Возврат HTML кода сформированного
     * бланка посылки
     *
     * @return string
     */
    public function print_package_form() {
        $forms = nc_netshop::get_instance()->forms->get_objects();

        ob_start();
        $forms->russianpost_package->template($this->data);
        return ob_get_clean();
    }

    /**
     * Возврат HTML кода сформированного
     * бланка наложенного платежа
     *
     * @return string
     */
    public function print_cash_on_delivery_form() {
        $forms = nc_netshop::get_instance()->forms->get_objects();

        ob_start();
        $forms->russianpost_cash_on_delivery->template($this->data);
        return ob_get_clean();
    }

    /**
     * Возврат информации по точкам
     * следования посылки
     *
     * @return array|null
     */
    public function get_tracking_information() {
        return null;
    }

    /**
     * Возвращает данные о населённом пункта РФ
     *
     * @param string $postal_code Почтовый индекс
     * @param string $region_name Название области
     * @param $district_name
     * @param string $locality_name Название населённого пункта
     * @return array [region => '', district => '', city => '', index => ''].
     *     Если населённый пункт не найден, то все значения в массиве — null
     */
    protected function get_location_data($postal_code, $region_name, $district_name, $locality_name) {
        $db = nc_db();

        $postal_code = $db->escape($postal_code);
        $locality_name = $db->escape($locality_name);
        $region_name = $db->escape($region_name);
        $district_name = $db->escape($district_name);

        $select =
            "SELECT `Region_Name` AS `region`,
                    `District_Name` AS `district`,
                    `Locality_Name` AS `city`, 
                    `Russianpost_Code` AS `index`
               FROM `Russianpost_Code`
                    JOIN `Russianpost_Locality` USING (`Russianpost_Locality_ID`)
                    JOIN `Russianpost_Region` USING (`Russianpost_Region_ID`)
                    LEFT JOIN `Russianpost_District` USING (`Russianpost_District_ID`) ";

        // если есть индекс
        if ($postal_code) {
            $result = $db->get_row("$select WHERE `Russianpost_Code` = '{$postal_code}' LIMIT 1", ARRAY_A);
            if ($result) {
                return $result;
            }
        }

        // если есть название населённого пункта
        if ($locality_name) {
            $result = $db->get_row(
                "$select 
                WHERE `Locality_Name` = '$locality_name'
                ORDER BY `Region_Name` = '$region_name' DESC,
                         `District_Name` = '$district_name' DESC
                LIMIT 1",
                ARRAY_A
            );

            if ($result) {
                return $result;
            }
        }

        // если есть название региона
        if ($region_name) {
            $result = $db->get_row(
                "$select WHERE `Region_Name` = '$region_name' ORDER BY `Russianpost_Code` ASC LIMIT 1",
                ARRAY_A
            );

            if ($result) {
                return $result;
            }
        }

        return array(
            'region' => null,
            'district' => null,
            'city' => null,
            'index' => null,
        );
    }
}