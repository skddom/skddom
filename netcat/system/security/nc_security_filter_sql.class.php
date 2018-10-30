<?php

class nc_security_filter_sql extends nc_security_filter {

    static protected $filter_type_string = 'sql';
    static protected $patterns = array(
        // выход из значения сравнения с AND, OR; UNION; SUB-SELECT (e.g. with CONCAT)
        'statement' => '/(?:\bAND\b|&&|\bOR\b|\|\||\bUNION\b|\bSELECT\b)./Ssi',
        // комментарии #, --, /*
        'comment' => '/#|--|\/\*/',
    );

    /**
     * @param string $checked_string
     * @param array $suspicious_input
     * @param mixed $context
     * @return string
     */
    protected function check_string_against_input($checked_string, $suspicious_input, $context) {
        foreach ($suspicious_input as $type => $data) {
            foreach ($data as $source => $input) {
                $checked_string = $this->check_if_inside_quotes($type, $checked_string, $source, $input);
            }
        }
        return $checked_string;
    }

    /**
     * @param $type
     * @param $checked_string
     * @param $source
     * @param $input
     * @return bool
     */
    protected function check_if_inside_quotes($type, $checked_string, $source, $input) {
        $position = -1;
        while (false !== ($position = strpos($checked_string, $input, $position + 1))) {
            // тип открытой до $position кавычки:
            $quote_type = $this->get_quote_type_at_position($checked_string, $position);

            // предотвращение срабатывания, когда (единственная) кавычка идёт первой, но экранирование есть
            if ($quote_type && $input[0] === $quote_type && $checked_string[$position - 1] === '\\') {
                $checked_input = '\\' . $input;
            } else {
                $checked_input = $input;
            }

            // ошибка приведения к типу     WHERE a = $_GET[x]
            // отсутствие экранирования     WHERE a = '$_GET[x]'
            if (!$quote_type || $this->string_has_unescaped_quote($checked_input, $quote_type)) {
                $this->trigger_error($type, $checked_string, $source, $input);
                // санирование строки можно будет добавить здесь:
                // $checked_string = ...
            }
        };

        return $checked_string;
    }

    /**
     * Проверяет, находится ли $checked_position внутри открытых кавычек
     *
     * @param string $query_string
     * @param int $checked_position
     * @return null|string
     */
    protected function get_quote_type_at_position($query_string, $checked_position) {
        $checked_position = min($checked_position, strlen($query_string));
        $next_char_is_escaped_with_slash = false;
        $quote_type = null;
        for ($i = 0; $i < $checked_position; $i++) {
            $current_char = $query_string[$i];

            if ($quote_type === null && ($current_char === "'" || $current_char === '"')) {
                // открывающая кавычка
                $quote_type = $current_char;
            } else if ($quote_type !== null && !$next_char_is_escaped_with_slash && $current_char === $quote_type) {
                // закрывающая кавычка (неэкранированная дублированием)
                $next_char = substr($query_string, $i+1, 1);
                if ($next_char === $quote_type) {
                    $i++;
                } else {
                    $quote_type = null;
                }

            }

            $next_char_is_escaped_with_slash = !$next_char_is_escaped_with_slash && $current_char === '\\';
        }

        return $quote_type;
    }

    /**
     * @param string $input
     * @param $check_type
     * @return string
     */
    protected function escape_input($input, $check_type) {
        return nc_db()->prepare($input);
    }

}