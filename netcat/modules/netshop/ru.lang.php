<?php

if (!class_exists("nc_System")) die("Unable to load file.");

if ($nc_core->NC_UNICODE) {
    require_once "ru_utf8.lang.php";
} else {
    require_once "ru_cp1251.lang.php";
}


if (!function_exists("nc_netshop_word_form")) {
    /**
     * В зависимости от количества $n возвращает форму слова
     * ($f1 — один, $f2 — два, $f3 — пять)
     *
     * @param $n
     * @param string $f1
     * @param string $f2
     * @param string $f5
     * @return string
     */
    function nc_netshop_word_form($n, $f1, $f2, $f5) {
        $n = abs(intval($n)) % 100;
        if ($n > 10 && $n < 20) return $f5;
        $n = $n % 10;
        if ($n > 1 && $n < 5) return $f2;
        if ($n == 1) return $f1;
        return $f5;
    }


    /**
     * Сумма прописью
     *
     * @param int|float $num  Сумма
     * @param array|string|bool $currency_name
     *      Массив в формами названия валюты (1 рубль, 2 рубля, 5 рублей);
     *      последний элемент массива — указание на грамматический род валюты (M, F).
     *      Названия могут быть переданы в виде строки, где указанные элементы
     *      разделены запятой.
     *      Если false, название валюты, а также дробная часть не добавляются
     *      (выводится только сумма прописью).
     *      Если true, в качестве названия валюты используется «рубль».
     * @param array|string|bool $decimal_part
     *      Массив в формами названия дробной части валюты (1 копейка, 2 копейки, 5 копеек);
     *      последний элемент массива — указание на род (M, F).
     *      Названия могут быть переданы в виде строки, где указанные элементы
     *      разделены запятой.
     *      Если false, дробная часть суммы не добавляется.
     *      Если true, в качестве названия дробной части используется «копейка».
     * @return string
     */
    function nc_netshop_amount_in_full($num, $currency_name_bool = true, $decimal_part_bool = true) {
        $nul = 'ноль';
        $ones = array(
            'M' => array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
            'F' => array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        );
        $teens = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
        $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
        $hundreds = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');

        // названия валют
        $defaults = array(
            "decimal_part" => array('копейка', 'копейки', 'копеек', 'F'),
            "currency_name" => array('рубль', 'рубля', 'рублей', 'M')
        );

        // если currency_name и decimal_part являются массивом или строкой, взять
        // названия валюты и её дробной части из этих параметров
        foreach (array('currency_name', 'decimal_part') as $param) {
            if (is_string($$param) && strlen($$param)) {
                $$param = preg_split("/\s*,\s*/u", $$param);
            }
            if (!is_array($$param)) { $$param = $defaults[$param]; }
            // пропущенные формы названий?
            if (!isset($$param[1])) { $$param[1] = $$param[0]; }
            if (!isset($$param[2])) { $$param[2] = $$param[1]; }
            // если пол не указан — по умолчанию мужской
            if (!isset($$param[3])) { $$param[3] = 'M'; }
        }

        // все единицы измерения
        $units = array(
            $decimal_part,
            $currency_name,
            array('тысяча', 'тысячи', 'тысяч', 'F'),
            array('миллион', 'миллиона', 'миллионов', 'M'),
            array('миллиард', 'миллиарда', 'миллиардов', 'M'),
        );


        list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $order => $value) { // by 3 symbols
                if (!intval($value)) { continue; }

                $unit_key = sizeof($units) - $order - 1; // unit key
                $gender = $units[$unit_key][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($value, 1));

                // mega-logic
                $out[] = $hundreds[$i1]; # 1xx-9xx
                if ($i2 > 1) {  // 20-99
                    $out[] = $tens[$i2] . ' ' . $ones[$gender][$i3];
                }
                else { // 10-19 | 1-9
                    $out[] = $i2 > 0 ? $teens[$i3] : $ones[$gender][$i3];
                }

                // units without rub & kop
                if ($unit_key > 1) {
                    $out[] = nc_netshop_word_form($value, $units[$unit_key][0], $units[$unit_key][1], $units[$unit_key][2]);
                }
            } //foreach
        }
        else {
            $out[] = $nul;
        }

        if ($currency_name_bool) {
            $out[] = nc_netshop_word_form(intval($rub), $units[1][0], $units[1][1], $units[1][2]); // rub
            if ($decimal_part_bool) {
                $out[] = $kop . ' ' . nc_netshop_word_form($kop, $units[0][0], $units[0][1], $units[0][2]); // kop
            }
        }

        $result = trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));

        $nc_core = nc_Core::get_object();
        if (!$nc_core->NC_UNICODE) {
            $result = $nc_core->utf8->utf2win($result);
        }

        return $result;
    }

}
