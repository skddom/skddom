<?php

/**
 * Интеграция с Яндекс.Метрикой и Google Analytics
 */

class nc_stats_analytics {

    const SCRIPT_POSITION_BODY_TOP = 0;
    const SCRIPT_POSITION_BODY_BOTTOM = 1;

    /** @var nc_stats  */
    protected $stats;

    /** @var string  регвыр, по которому мы ищем <script></script> с гуглоаналитикой */
    protected $ga_anchor_regexp = '/[\'"]UA-\d+-\d+[\'"]/';

    /** @var string  регвыр, по которому мы ищем <script></script> с гугл-тег-менеджером */
    protected $gtm_anchor_regexp = '/[\'"]GTM-\w+[\'"]/';

    /** @var string  регвыр, по которому мы ищем <script></script> с яндексометрикой */
    protected $ym_anchor_regexp = '/\.yaCounter\d+/';

    /**
     *
     * @param nc_stats $stats
     */
    public function __construct(nc_stats $stats) {
        $this->stats = $stats;

        if (!$stats->get_setting('Analytics_TitlePageCheckedForCounters')) {
            $this->extract_and_save_counters_from_title_page();
        }
    }

    /**
     * Возвращает TRUE, если в настройках модуля включена интеграция с аналитикой
     * @return mixed
     */
    public function is_enabled() {
        return $this->stats->get_setting('Analytics_Enabled');
    }

    /**
     * Возвращает TRUE, если в настройках модуля указан хотя бы один код счётчика
     * @return bool
     */
    public function is_configured() {
        return $this->stats->get_setting('Analytics_GA_Code') || $this->stats->get_setting('Analytics_YM_Code');
    }

    /**
     * Вставляет необходимые скрипты на страницу
     * @param $buffer
     * @return string
     */
    public function process_page_buffer($buffer) {
        $position = $this->stats->get_setting('Analytics_ScriptPosition');

        // Место вставки
        $insertion_point = false;
        if ($position == self::SCRIPT_POSITION_BODY_TOP) {
            $body_position = stripos($buffer, '<body');
            if ($body_position) {
                $insertion_point = strpos($buffer, '>', $body_position) + 1;
            }
        }
        else if ($position == self::SCRIPT_POSITION_BODY_BOTTOM) {
            $insertion_point = stripos($buffer, '</body');
        }

        // Если определено место вставки (есть <body> или </body>), добавляем нужные скрипты
        if ($insertion_point) {
            $code_to_insert = '';

            // GA: добавляем счётчик, если не видим его на странице
            $add_ga = $this->stats->get_setting('Analytics_GA_Code') &&
                      !$this->extract_script($buffer, $this->ga_anchor_regexp) &&
                      !$this->extract_script($buffer, $this->gtm_anchor_regexp);

            if ($add_ga) {
                $code_to_insert .= $this->stats->get_setting('Analytics_GA_Code');
            }

            // YM: добавляем счётчик, если не видим его на странице
            $add_ym = $this->stats->get_setting('Analytics_YM_Code') &&
                      !$this->extract_script($buffer, $this->ym_anchor_regexp);

            if ($add_ym) {
                $code_to_insert .= $this->stats->get_setting('Analytics_YM_Code');
            }

            $code_to_insert .= '<script>' . file_get_contents(nc_module_folder('stats') . 'js/analytics.min.js') . '</script>';

            $buffer = substr($buffer, 0, $insertion_point) .
                      $code_to_insert .
                      substr($buffer, $insertion_point);
        }

        return $buffer;
    }

    /**
     * Извлекает с главной страницы сайта код счётчиков и сохраняет его в настройках модуля
     * @param bool $replace_existing_counters  замещает указанный в настройках модуля код счётчиков; используйте с крайней осторожностью
     */
    public function extract_and_save_counters_from_title_page($replace_existing_counters = false) {
        $title_page = $this->get_site_title_page();
        if (!$title_page) {
            return;
        }

        if (!$this->stats->get_setting('Analytics_GA_Code') || $replace_existing_counters) {
            $code =
                $this->extract_script($title_page, $this->gtm_anchor_regexp, true) ?:
                $this->extract_script($title_page, $this->ga_anchor_regexp);

            if ($code) {
                $this->stats->set_setting('Analytics_GA_Code', $code);
            }
        }

        if (!$this->stats->get_setting('Analytics_YM_Code') || $replace_existing_counters) {
            $code = $this->extract_script($title_page, $this->ym_anchor_regexp, true);

            if ($code) {
                $this->stats->set_setting('Analytics_YM_Code', $code);
            }
        }

        $this->stats->set_setting('Analytics_TitlePageCheckedForCounters', '1');
    }

    /**
     * @return bool|string
     * @throws Exception
     */
    protected function get_site_title_page() {
        $domain = nc_core::get_object()->catalogue->get_by_id($this->stats->get_site_id(), 'Domain') ?: $_SERVER['HTTP_HOST'];
        if (!$domain) {
            return false;
        }

        return @file_get_contents("http://$domain/") ?: @file_get_contents("https://$domain/");
    }

    /**
     * @param string $html  markup to examine
     * @param string $regexp  regexp for what we are looking for inside <script></script>
     * @param bool $include_no_script  add following <noscript></noscript> fragment if there is any
     * @param int $max_length  maximum length of the <script></script> contents
     * @return bool|string
     */
    protected function extract_script($html, $regexp, $include_no_script = false, $max_length = 2048) {
        if (!$html) {
            return false;
        }

        if (!preg_match($regexp, $html, $regs, PREG_OFFSET_CAPTURE)) {
            return false;
        }

        $offset = $regs[0][1];

        $html_length = strlen($html);
        $script_tag_start = strrpos($html, '<script', $offset - $html_length);
        if (!$script_tag_start) {
            return false;
        }

        $script_tag_end = strpos($html, '</script>', $offset);
        if (!$script_tag_end) {
            return false;
        }

        $script_tag_end += 9; // 9 is the length of '</script>'
        if ($script_tag_end - $script_tag_start > $max_length) {
            return false;
        }

        if ($include_no_script) {
            // Try to find sibling </noscript> tag (immediately before or after the <script>)
            $next = trim(substr($html, $script_tag_end, 30));
            if (strpos($next, '<noscript>') === 0) {
                $noscript_tag_end = strpos($html, '</noscript>', $script_tag_end);
                if ($noscript_tag_end) {
                    $script_tag_end = $noscript_tag_end + 11; // 11 is the length of '</noscript>'
                }
            }
            else {
                $previous = substr($html, $script_tag_start - 30, $script_tag_start);
                $noscript_tag_end = strrpos($previous, '</noscript>');
                if ($noscript_tag_end !== false) {
                    $noscript_tag_start = strrpos($html, '<noscript>', $script_tag_start - $html_length);
                    if ($noscript_tag_start) {
                        $script_tag_start = $noscript_tag_start;
                    }
                }
            }
        }

        return substr($html, $script_tag_start, $script_tag_end - $script_tag_start);
    }

}