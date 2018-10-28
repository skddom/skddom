<?php

class nc_security_filter_xss extends nc_security_filter {

    static protected $filter_type_string = 'xss';
    static protected $patterns = array(
        'dangerous_tag' => '/<(?:script|style)/i', // other tags without attributes are worthless for xss (?)
        'attribute' => '=',
        'javascript' => "/[\n;(\"`']/s", // инъекция внутрь <script>?
    );

    /**
     * @param string $checked_string
     * @param array $suspicious_input
     * @param mixed $context
     * @return string
     */
    protected function check_string_against_input($checked_string, $suspicious_input, $context) {
        if (isset($suspicious_input['dangerous_tag'])) {
            $checked_string = $this->check_dangerous_tags($checked_string, $suspicious_input['dangerous_tag']);
        }

        if (isset($suspicious_input['attribute'])) {
            $checked_string = $this->check_attributes($checked_string, $suspicious_input['attribute']);
        }

        if (isset($suspicious_input['javascript'])) {
            $checked_string = $this->check_javascript($checked_string, $suspicious_input['javascript']);
        }

        return $checked_string;
    }

    /**
     * @param string $checked_string
     * @param array $suspicious_input
     * @return string
     */
    protected function check_dangerous_tags($checked_string, $suspicious_input) {
        foreach ($suspicious_input as $source => $input) {
            $position = -1;
            while (false !== ($position = $this->get_possibly_escaped_string_position($checked_string, $input, $position + 1))) {
                $this->trigger_error('dangerous_tag', $checked_string, $source, $input);
                // санирование строки можно будет добавить здесь:
                // $checked_string = ...
            }
        }

        return $checked_string;
    }

    /**
     * @param string $checked_string
     * @param array $suspicious_input
     * @return string
     */
    protected function check_attributes($checked_string, $suspicious_input) {
        foreach ($suspicious_input as $source => $input) {
            $position = -1;
            while (false !== ($position = $this->get_possibly_escaped_string_position($checked_string, $input, $position + 1))) {
                $quote = $this->get_attribute_quote_type_at_position($checked_string, $position);

                $error =
                    // $input вставлен вне тэга и в $input есть "<":
                    ($quote === null && strpos($input, '<') !== false) ||
                    // $input вставлен внутри тэга, вне кавычки:
                    $quote === false ||
                    // $input вставлен внутри тэга внутри кавычек, но такая же кавычка есть в $input:
                    ($quote !== null && strpos($input, $quote) !== false);

                if ($error) {
                    $this->trigger_error('attribute', $checked_string, $source, $input);
                    // санирование строки можно будет добавить здесь:
                    // $checked_string = ...
                }
            }
        }

        return $checked_string;
    }

    /**
     * @param $html
     * @param $position
     * @return false|null|string
     *      null: не в тэге
     *      false: в тэге, нет кавычки
     *      ' или "
     */
    protected function get_attribute_quote_type_at_position($html, $position) {
        $length = strlen($html);
        $inside_tag = false;
        $quote_type = false;

        for ($i = 0; $i < $position; $i++) {
            $char = $html[$i];
            if ($inside_tag) {
                if ($quote_type === false) { // inside tag, outside quote
                    if ($char === '>') {
                        $inside_tag = false;
                    } else if ($char === '"' || $char === "'") {
                        $quote_type = $char;
                    }
                } elseif ($char === $quote_type) { // inside tag, inside quote – closing quote
                    $quote_type = false;
                }
            } else if ($char === '<' && $i < $length - 1 && preg_match('/[A-Za-z]/', $html[$i + 1])) {
                $inside_tag = true;
            }
        }

        return $inside_tag ? $quote_type : null;
    }

    /**
     * @param $checked_string
     * @param $suspicious_input
     */
    protected function check_javascript($checked_string, $suspicious_input) {
        foreach ($suspicious_input as $source => $input) {
            $position = -1;
            while (false !== ($position = $this->get_possibly_escaped_string_position($checked_string, $input, $position + 1))) {
                $quote_type = $this->get_script_quote_type_at_position($checked_string, $position);

                if ($quote_type === null) {
                    continue;
                }

                $error = $quote_type === false ||
                    $this->string_has_unescaped_quote($input, $quote_type) ||
                    $quote_type === '`' && strpos($input, '${') !== false;

                if ($error) {
                    $this->trigger_error('javascript', $checked_string, $source, $input);
                    // санирование строки можно будет добавить здесь:
                    // $checked_string = ...
                }
            }
        }
        return $checked_string;
    }

    /**
     * @param string $html
     * @param int $position
     * @return mixed
     *      null: не в <script>
     *      false: в <script>, не в кавычках
     *      ', ", `: тип открытой кавычки
     */
    protected function get_script_quote_type_at_position($html, $position) {
        $backwards_position = (strlen($html) - $position) * -1;

        $previous_script_begin_position = strripos($html, '<script', $backwards_position);
        if ($previous_script_begin_position === false) {
            return null;
        }

        $previous_script_end_position = (int)strripos($html, '</script', $backwards_position);
        if ($previous_script_end_position > $previous_script_begin_position) {
            return null;
        }

        $quote_type = false;
        $escaped = false;

        for ($i = $previous_script_begin_position; $i < $position; $i++) {
            $char = $html[$i];
            if ($char === '\\') {
                $escaped = !$escaped;
            } else if (!$escaped && ($char === '"' || $char === "'" || $char === '`')) {
                if ($quote_type === $char) { // closing quote
                    $quote_type = false;
                } else { // opening quote
                    $quote_type = $char;
                }
            } else if ($escaped) {
                $escaped = false;
            }
        }

        return $quote_type;
    }

    /**
     * @param string $input
     * @param $check_type
     * @return string
     */
    protected function escape_input($input, $check_type) {
        if ($check_type === 'dangerous_tag') {
            $input = htmlspecialchars($input, ENT_QUOTES);
        } else if ($check_type === 'attribute') {
            $input = str_replace('=', '&#61;', $input);
        } else if ($check_type === 'javascript') {
            // К сожалению, из-за эмуляции Неткетом «волшебных кавычек»
            // можно получить двойные слеши. Пока решения этой проблемы нет.
            $input = addcslashes($input, '()$`"\'');
        }
        return $input;
    }

    /**
     * В зависимости от того, откуда взято значение (напрямую из superglobals
     * или из глобальных переменных) значение может быть as is или экранировано
     * addslashes().
     *
     * @param $haystack
     * @param $needle
     * @param $offset
     * @return bool|int
     */
    protected function get_possibly_escaped_string_position($haystack, $needle, $offset) {
        $position = strpos($haystack, $needle, $offset);
        if ($position !== false) {
            return $position;
        }

        return strpos($haystack, addslashes($needle), $offset);
    }

}
