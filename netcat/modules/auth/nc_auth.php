<?php

/**
 * class nc_auth
 * @package nc_auth
 * @category nc_auth
 */
class nc_auth {

    public $hash, $tpl;
    protected $core, $db, $auth_view;
    protected $settings;

    protected function __construct() {

        $this->hash = nc_auth_hash::get_object();
        $this->core = nc_Core::get_object();
        $this->db = $this->core->db;
        $this->tpl = new nc_auth_template();

        $interface = $this->core->get_interface();
        $this->auth_view = new nc_tpl_module_view();
        $this->auth_view->load('auth', $interface);

        $this->core->event->bind($this, array(nc_Event::AFTER_USER_CREATED => 'add_user_listen'));
    }

    /**
     * Instance self object method
     *
     * @return self object
     */
    public static function get_object() {
        // call as static
        static $storage;
        // check inited object
        if (!isset($storage)) {
            // init object
            $storage = new self();
        }
        // return object
        return is_object($storage) ? $storage : false;
    }

    public function token_enabled() {
        $nc_core = nc_Core::get_object();
        if (!$nc_core->php_ext('gmp')) return false;
        return $nc_core->get_settings('authtype_admin', 'auth') & NC_AUTHTYPE_TOKEN;
    }

    public function add_form() {
        $nc_core = nc_Core::get_object();
        $st = new nc_Component(0, 3);
        $fields = $st = $st->get_fields();
        $res = <<<NETCAT_ADD_FORM
".opt(\$auth_settings = \$nc_core->get_settings('', 'auth'), "")."
".( \$auth_settings['deny_reg'] ? NETCAT_MODULE_AUTH_SELFREG_DISABLED : "
".( \$warnText ? "<div class='warnText'>\$warnText</div>" : NULL )."
<form name='adminForm' id='adminForm' class='nc-form' enctype='multipart/form-data' method='post' action='".\$nc_core->SUB_FOLDER.\$nc_core->HTTP_ROOT_PATH."add.php'>
<div id='nc_moderate_form'>
  <div class='nc_clear'></div>
  <input name='admin_mode' type='hidden' value='\$admin_mode' />
  <input name='catalogue' type='hidden' value='\$catalogue' />
  <input name='cc' type='hidden' value='\$cc' />
  <input name='sub' type='hidden' value='\$sub' />
  <input name='posting' type='hidden' value='1' />
  <input name='curPos' type='hidden' value='\$curPos' />
  <div class='nc_clear'></div>
</div>

".nc_string_field("$nc_core->AUTHORIZE_BY", "id='f_Login' maxlength='255' size='50'", \$classID, 1)."
<span id='nc_auth_wait' class='nc_auth_login_check'>". NETCAT_MODULE_AUTH_LOGIN_WAIT ."</span>
<span id='nc_auth_login_ok' class='nc_auth_login_check'>". NETCAT_MODULE_AUTH_LOGIN_FREE ."</span>
<span id='nc_auth_login_fail' class='nc_auth_login_check'>". NETCAT_MODULE_AUTH_LOGIN_BUSY ."</span>
<span id='nc_auth_login_incorrect' class='nc_auth_login_check'>". NETCAT_MODULE_AUTH_LOGIN_INCORRECT ."</span>
<br/><br/>
NETCAT_ADD_FORM;


        $allow_fields = explode(',', $nc_core->get_settings('field_custom', 'auth'));
        $field_all = $nc_core->get_settings('field_all', 'auth');
        if (!empty($fields))
                foreach ($fields as $field) {
                if ($field['edit_type'] > 1) continue;
                if ($field['name'] == $this->core->AUTHORIZE_BY) continue;
                if (!$field_all && !in_array($field['name'], $allow_fields))
                        continue;
                switch ($field['type']) {
                    case 1:
                        $res .= "\".nc_string_field(\"".$field['name']."\", \"maxlength='255' size='50'\", \$classID, 1).\"<br />\r\n";
                        break;
                    case 2:
                        $res .= "\".nc_int_field(\"".$field['name']."\", \"maxlength='12' size='12'\", \$classID, 1).\"<br />\r\n";
                        break;
                    case 3:
                        $res .= "\".nc_text_field(\"".$field['name']."\", \"\", \$classID, 1).\"<br />\r\n";
                        break;
                    case 4:
                        $res .= "\".nc_list_field(\"".$field['name']."\", \"\", \$classID, 1).\"<br />\r\n";
                        break;
                    case 5:
                        $res .= "\".nc_bool_field(\"".$field['name']."\", \"\", \$classID, 1).\"<br />\r\n";
                        break;
                    case 6:
                        $res .= "\".nc_file_field(\"".$field['name']."\", \"size='50'\", \$classID, 1).\"<br />\r\n";
                        break;
                    case 7:
                        $res .= "\".nc_float_field(\"".$field['name']."\", \"maxlength='12' size='12'\", \$classID, 1).\"<br />\r\n";
                        break;
                    case 8:
                        $res .= "\".nc_date_field(\"".$field['name']."\", \"\", \$classID, 1).\"<br />\r\n";
                        break;
                    case 9:
                        $res .= "\".nc_related_field(\"".$field['name']."\").\"<br />\r\n";
                        break;
                    case 10:
                        $res .= "\".nc_multilist_field(\"".$field['name']."\", \"\", \"\", \$classID, 1).\"<br />\r\n";
                        break;
                }
                $res.= "<br />\r\n";
            }

        $res .= NETCAT_MODERATION_PASSWORD.":<br/><input id='Password1' name='Password1' type='password' size='25' maxlength='32' value='' />
      <span id='nc_auth_pass1_security' class='nc_auth_pass1_check'>".NETCAT_MODULE_AUTH_PASS_RELIABILITY." </span>
      <span id='nc_auth_pass1_s1' class='nc_auth_pass1_check'>".NETCAT_MODULE_AUTH_PASS_LOW."</span>
      <span id='nc_auth_pass1_s2' class='nc_auth_pass1_check'>".NETCAT_MODULE_AUTH_PASS_MIDDLE."</span>
      <span id='nc_auth_pass1_s3' class='nc_auth_pass1_check'>".NETCAT_MODULE_AUTH_PASS_HIGH."</span>
      <span id='nc_auth_pass1_s4' class='nc_auth_pass1_check'>".NETCAT_MODULE_AUTH_PASS_VHIGH."</span>
      <span id='nc_auth_pass1_empty' class='nc_auth_pass1_check'>".NETCAT_MODULE_AUTH_PASS_EMPTY."</span>
      <span id='nc_auth_pass_min' class='nc_auth_pass1_check'>".NETCAT_MODULE_AUTH_PASS_SHORT."</span>
      <br/><br/>";
        $res .= NETCAT_MODERATION_PASSWORDAGAIN.":<br/><input id='Password2' name='Password2' type='password' size='25' maxlength='32' value='' />
     <span id='nc_auth_pass2_ok' class='nc_auth_pass2_check'>".NETCAT_MODULE_AUTH_PASS_COINCIDE."</span>
     <span id='nc_auth_pass2_fail' class='nc_auth_pass2_check'>".NETCAT_MODULE_AUTH_PASS_N_COINCIDE."</span>
     <br/><br/>";

        if ($this->core->modules->get_by_keyword('captcha')) {
            $res .= "\".(!\$AUTH_USER_ID && \$current_cc['UseCaptcha'] && \$MODULE_VARS['captcha'] ? nc_captcha_formfield().\"<br/><br/>\".NETCAT_MODERATION_CAPTCHA.\" (*):<br/><input type='text' name='nc_captcha_code' size='10'><br/><br/>\" : \"\").\"\r\n";
        }

        $res .= <<<NETCAT_ADD_FORM
".( \$nc_core->get_settings('agreed', 'auth') ? "<input type='checkbox' name='nc_agreed' id='nc_agreed' value='1' /><label for='nc_agreed'>".str_replace('%USER_AGR', \$nc_core->SUB_FOLDER . nc_auth_regform_url(0, 0)."agreed/", NETCAT_MODULE_AUTH_USER_AGREEMENT)."</label><br/><br/>" : "")."
<input type='submit' title='".NETCAT_MODULE_AUTH_REGISTER."' value='".NETCAT_MODULE_AUTH_REGISTER."' />
<script type='text/javascript'>
   var SUB_FOLDER = '\$nc_core->SUB_FOLDER';
   var nc_auth_obj = new nc_auth(".json_encode(array('check_login'=>\$auth_settings['check_login'],
                                                     'pass_min' => intval(\$auth_settings['pass_min']),
                                                     'check_pass'=>\$auth_settings['check_pass'],
                                                     'check_pass2'=>\$auth_settings['check_pass2'])).");
</script>
" )."
</form>
NETCAT_ADD_FORM;
        return $res;
    }

    public function add_form_fs() {
        $nc_core = nc_Core::get_object();
        $st = new nc_Component(0, 3);
        $fields = $st = $st->get_fields();
        $res = <<<NETCAT_ADD_FORM
<? \$auth_settings = \$nc_core->get_settings('', 'auth')?>
<?= \$auth_settings['deny_reg'] ? NETCAT_MODULE_AUTH_SELFREG_DISABLED : (\$warnText ? "<div class='warnText'>\$warnText</div>" : NULL ) ?>
<form name='adminForm' id='adminForm' class='nc-form' enctype='multipart/form-data' method='post' action='<?= \$nc_core->SUB_FOLDER ?><?= \$nc_core->HTTP_ROOT_PATH ?>add.php'>
<div id='nc_moderate_form'>
  <div class='nc_clear'></div>
  <input name='admin_mode' type='hidden' value='<?= \$admin_mode ?>' />
  <input name='catalogue' type='hidden' value='<?= \$catalogue ?>' />
  <input name='cc' type='hidden' value='<?= \$cc ?>' />
  <input name='sub' type='hidden' value='<?= \$sub ?>' />
  <input name='posting' type='hidden' value='1' />
  <input name='curPos' type='hidden' value='<?= \$curPos ?>' />
  <div class='nc_clear'></div>
</div>

<?= nc_string_field(\$nc_core->AUTHORIZE_BY, "id='f_Login' maxlength='255' size='50'", \$classID, 1) ?>
<span id='nc_auth_wait' class='nc_auth_login_check'><?= NETCAT_MODULE_AUTH_LOGIN_WAIT ?></span>
<span id='nc_auth_login_ok' class='nc_auth_login_check'><?= NETCAT_MODULE_AUTH_LOGIN_FREE ?></span>
<span id='nc_auth_login_fail' class='nc_auth_login_check'><?= NETCAT_MODULE_AUTH_LOGIN_BUSY ?></span>
<span id='nc_auth_login_incorrect' class='nc_auth_login_check'><?= NETCAT_MODULE_AUTH_LOGIN_INCORRECT ?></span>
<br/><br/>
NETCAT_ADD_FORM;


        $allow_fields = explode(',', $nc_core->get_settings('field_custom', 'auth'));
        $field_all = $nc_core->get_settings('field_all', 'auth');
        if (!empty($fields))
                foreach ($fields as $field) {
                if ($field['edit_type'] > 1) continue;
                if ($field['name'] == $this->core->AUTHORIZE_BY) continue;
                if (!$field_all && !in_array($field['name'], $allow_fields))
                        continue;
                switch ($field['type']) {
                    case 1:
                        $res .= "<?= nc_string_field(\"".$field['name']."\", \"maxlength='255' size='50'\", \$classID, 1) ?><br />\r\n";
                        break;
                    case 2:
                        $res .= "<?= nc_int_field(\"".$field['name']."\", \"maxlength='12' size='12'\", \$classID, 1) ?><br />\r\n";
                        break;
                    case 3:
                        $res .= "<?= nc_text_field(\"".$field['name']."\", \"\", \$classID, 1) ?><br />\r\n";
                        break;
                    case 4:
                        $res .= "<?= nc_list_field(\"".$field['name']."\", \"\", \$classID, 1) ?><br />\r\n";
                        break;
                    case 5:
                        $res .= "<?= nc_bool_field(\"".$field['name']."\", \"\", \$classID, 1) ?><br />\r\n";
                        break;
                    case 6:
                        $res .= "<?= nc_file_field(\"".$field['name']."\", \"size='50'\", \$classID, 1) ?><br />\r\n";
                        break;
                    case 7:
                        $res .= "<?= nc_float_field(\"".$field['name']."\", \"maxlength='12' size='12'\", \$classID, 1) ?><br />\r\n";
                        break;
                    case 8:
                        $res .= "<?= nc_date_field(\"".$field['name']."\", \"\", \$classID, 1) ?><br />\r\n";
                        break;
                    case 9:
                        $res .= "<?= nc_related_field(\"".$field['name']."\") ?><br />\r\n";
                        break;
                    case 10:
                        $res .= "<?= nc_multilist_field(\"".$field['name']."\", \"\", \"\", \$classID, 1) ?><br />\r\n";
                        break;
                }
                $res.= "<br />\r\n";
            }

        $res .= NETCAT_MODERATION_PASSWORD.":<br/><input id='Password1' name='Password1' type='password' size='25' maxlength='32' value='' />
      <span id='nc_auth_pass1_security' class='nc_auth_pass1_check'>".NETCAT_MODULE_AUTH_PASS_RELIABILITY." </span>
      <span id='nc_auth_pass1_s1' class='nc_auth_pass1_check'>".NETCAT_MODULE_AUTH_PASS_LOW."</span>
      <span id='nc_auth_pass1_s2' class='nc_auth_pass1_check'>".NETCAT_MODULE_AUTH_PASS_MIDDLE."</span>
      <span id='nc_auth_pass1_s3' class='nc_auth_pass1_check'>".NETCAT_MODULE_AUTH_PASS_HIGH."</span>
      <span id='nc_auth_pass1_s4' class='nc_auth_pass1_check'>".NETCAT_MODULE_AUTH_PASS_VHIGH."</span>
      <span id='nc_auth_pass1_empty' class='nc_auth_pass1_check'>".NETCAT_MODULE_AUTH_PASS_EMPTY."</span>
      <span id='nc_auth_pass_min' class='nc_auth_pass1_check'>".NETCAT_MODULE_AUTH_PASS_SHORT."</span>
      <br/><br/>";
        $res .= NETCAT_MODERATION_PASSWORDAGAIN.":<br/><input id='Password2' name='Password2' type='password' size='25' maxlength='32' value='' />
     <span id='nc_auth_pass2_ok' class='nc_auth_pass2_check'>".NETCAT_MODULE_AUTH_PASS_COINCIDE."</span>
     <span id='nc_auth_pass2_fail' class='nc_auth_pass2_check'>".NETCAT_MODULE_AUTH_PASS_N_COINCIDE."</span>
     <br/><br/>";

        if ($this->core->modules->get_by_keyword('captcha')) {
            $res .= "<?= (!\$AUTH_USER_ID && \$current_cc['UseCaptcha'] && \$MODULE_VARS['captcha'] ? nc_captcha_formfield().\"<br/><br/>\".NETCAT_MODERATION_CAPTCHA.\" (*):<br/><input type='text' name='nc_captcha_code' size='10'><br/><br/>\" : \"\") ?>\r\n";
        }

        $res .= <<<NETCAT_ADD_FORM
<?= ( \$nc_core->get_settings('agreed', 'auth') ? "<input type='checkbox' name='nc_agreed' id='nc_agreed' value='1' /><label for='nc_agreed'>".str_replace('%USER_AGR', \$nc_core->SUB_FOLDER . nc_auth_regform_url(0, 0)."agreed/", NETCAT_MODULE_AUTH_USER_AGREEMENT)."</label><br/><br/>" : "") ?>
<input type='submit' title='<?= NETCAT_MODULE_AUTH_REGISTER ?>' value='<?= NETCAT_MODULE_AUTH_REGISTER ?>' />
<script type='text/javascript'>
   var SUB_FOLDER = '<?= \$nc_core->SUB_FOLDER ?>';
   var nc_auth_obj = new nc_auth(<?= json_encode(array('check_login'=>\$auth_settings['check_login'],
                                                     'pass_min' => intval(\$auth_settings['pass_min']),
                                                     'check_pass'=>\$auth_settings['check_pass'],
                                                     'check_pass2'=>\$auth_settings['check_pass2'])) ?>);
</script>
</form>
NETCAT_ADD_FORM;
        return $res;
    }

    public function pa_add($sum, $user_id = 0, $desc = '', $dont_log = 0) {
        return $this->pa_operation('+', $sum, $user_id, $desc, $dont_log);
    }

    public function pa_deduct($sum, $user_id = 0, $desc = '', $dont_log = 0) {
        return $this->pa_operation('-', $sum, $user_id, $desc, $dont_log);
    }

    public function pa_operation($type, $sum, $user_id, $desc, $dont_log = 0) {
        $user_id = intval($user_id);
        if (!$user_id) {
            global $AUTH_USER_ID;
            $user_id = $AUTH_USER_ID;
        }
        $sum = doubleval($sum);

        if (!$sum || !$user_id || ( $type != '+' && $type != '-')) {
            return false;
        }

        $class_id = intval($this->core->get_settings('pa_class_id', 'auth'));
        $field = $this->db->escape($this->core->get_settings('pa_field', 'auth'));
        if (!$dont_log) {
            $this->db->query("INSERT INTO `Message".$class_id."`
                      SET `ToUserID` = '".$user_id."',
                          `Type` = '".$type."',
                          `Sum` = '".$sum."',
                          `Created` = NOW(),
                          `Description` = '".$this->db->escape($desc)."' ");
        }


        $cur_sum = $this->db->get_var("SELECT `".$field."` FROM `User` WHERE `User_ID` = '".$user_id."' ");
        $cur_sum += ( $type == '+' ? 1 : -1) * $sum;
        $this->db->query("UPDATE `User` SET `".$field."` = '".$cur_sum."' WHERE `User_ID` = '".$user_id."' ");

        return $cur_sum;
    }

    /**
     *
     * @param mixed массив с параметрами формы. Ключи массива
     * form_type - тип формы: table, v/vertical, h/horizontal
     * hide_recovery_pass - если 1, то скрыть ссылку на восстановление пароля
     * hide_register_link  - если 1, то скрыть ссылку на регистрацию
     * login_save - если 1, то не показывается checkbox "запомнить меня"
     * submit_name - текст на кнопке
     * @param <type> $template
     * @return string
     */
    public function auth_form($params = array(), $template = array()) {

        global $SUB_FOLDER, $REQUESTED_FROM, $REQUEST_METHOD, $REQUEST_URI, $AUTH_USER_ID, $AUTHORIZE_BY, $current_user;
        /** @var nc_core $nc_core */
        global $nc_core;
        global $HTTP_ROOT_PATH, $AUTH_USER, $SUB_FOLDER;
        global $db, $catalogue, $sub, $cc, $AUTH_USER_ID, $AUTHORIZATION_TYPE, $ADMIN_AUTHTYPE, $REQUESTED_BY;
	    global $nc_auth, $nc_auth_vk, $nc_auth_fb, $nc_auth_twitter, $nc_auth_openid, $nc_auth_oauth;

        if ($AUTH_USER_ID) {
            return $this->auth_form_for_authorized($params, $template);
        }

        $ex = array('vk', 'fb', 'twitter', 'openid', 'oauth');

        $params['need_captcha'] = $need_captcha = $nc_core->user->captcha_is_required();
        $params['invalid_captcha'] = $nc_core->user->captcha_is_invalid();

        // параметры по умолчанию
        if (!$params['submit_name'])
                $params['submit_name'] = NETCAT_MODULE_AUTH_ENTER;
        if (!$params['form_type'] || in_array($params['form_type'], array('v', 'h', 't')))
                $params['form_type'] = 'v';
        if (!$params['captcha_wrong'])
                $params['captcha_wrong'] = NETCAT_MODULE_CAPTCHA_WRONG_CODE;
        if (!$params['login_wrong'])
                $params['login_wrong'] = NETCAT_MODULE_AUTH_INCORRECT_LOGIN_OR_RASSWORD;
        if (!$params['auth_text'])
                $params['auth_text'] = NETCAT_MODULE_AUTH_AUTHORIZATION_UPPER;
        if (!$params['login_text'])
                $params['login_text'] = NETCAT_MODULE_AUTH_LOGIN;
        if (!$params['reg_text'])
                $params['reg_text'] = NETCAT_MODULE_AUTH_SETUP_REGISTRATION;
        if (!$params['pass_text'])
                $params['pass_text'] = NETCAT_MODULE_AUTH_PASSWORD;
        if (!$params['recovery_text'])
                $params['recovery_text'] = NETCAT_MODULE_AUTH_FORGOT;

        // запрещена самостоятельная регистарция
        if ($this->core->get_settings('deny_reg', 'auth'))
                $params['hide_register_link'] = 1;

        // доступность авторизации через внешние сервисы
        foreach ($ex as $v) {
            if (!isset($params[$v.'_enabled']) || $params[$v.'_enabled']) {
                $params[$v.'_enabled'] = nc_auth_provider::get_object($v)->enabled();
            }
        }

        // "запомнить меня"
        if (!$template['login_save']) {
            $template['login_save'] = '';
            if ($this->core->ADMIN_AUTHTYPE == 'manual' && $this->core->AUTHORIZATION_TYPE == 'cookie') {
                $template['login_save'] = $this->tpl->get_login_save();
                if ($params['login_save'] === 'auto')
                        $template['login_save'] = $this->tpl->get_login_save_hidden();
                if ($params['login_save'] === 'checked')
                        $template['login_save'] = $this->tpl->get_login_save(1);
                if ($params['login_save'] === 'none')
                        $template['login_save'] = '';
            }
        }

        // формы авторизации по умолчанию
        if (!$template['vk_form']) $template['vk_form'] = $this->tpl->get_vk();
        if (!$template['fb_form']) $template['fb_form'] = $this->tpl->get_fb();
        if (!$template['twitter_form'])
                $template['twitter_form'] = $this->tpl->get_twitter();
        if (!$template['openid_form'])
                $template['openid_form'] = $this->tpl->get_openid();
	    if (!$template['oauth_form'])
		    $template['oauth_form'] = $this->tpl->get_oauth();
        if (0 && !$template['token_form'])
                $template['token_form'] = $this->tpl->get_token();

        // генерация формы авторизации "по умолчанию"
        if (!isset($template['auth_form'])) {
            if ($this->core->template->get_current('File_Mode')) {
                $field = $this->auth_view->get_field_path('user_login_form');
						ob_start();
						include($field);
						$template['auth_form'] = ob_get_clean();
            } else {
                $method = 'get_auth_form_'.$params['form_type'];
                $template['auth_form'] = $this->tpl->$method($params);
            }
        }

        // замена макропеременных
        $macro = array('form_id' => "nc_auth_form".($params['ajax'] ? "_ajax" : ""),
                'action' => $this->core->SUB_FOLDER.$this->core->HTTP_ROOT_PATH.'modules/auth/',
                'requested_from' => htmlspecialchars($REQUESTED_FROM ? $REQUESTED_FROM : $REQUEST_URI, ENT_QUOTES),
                'register_link' => nc_auth_regform_url(0, 0),
                'recovery_link' => $this->core->SUB_FOLDER.$this->core->HTTP_ROOT_PATH.'modules/auth/password_recovery.php',
                'login_save' => $template['login_save'],
                'token_form' => $template['token_form']);
        foreach ($ex as $v) {
            $macro[$v.'_form'] = $params[$v.'_enabled'] ? $template[$v.'_form'] : '';
        }

        if ($need_captcha) $macro['captcha'] = nc_captcha_formfield();

        foreach ($macro as $k => $v) {
            $template['auth_form'] = str_replace('%'.$k, $v, $template['auth_form']);
        }

        return $template['auth_form'];
    }

    /**
     * Выводит ссылки "Регистрация" и "Авторизация"
     */
    public function auth_links($params = array(), $template = array()) {

        global $AUTH_USER_ID;
        if ($AUTH_USER_ID) {
            return $this->auth_form($params, $template);
        }

        $params['ajax'] = true;

        if (!$template['auth_link_form']) {
            $template['auth_link_form'] = "<div class='auth_block'><div class='nc_auth_links'><a href='".nc_auth_regform_url(0, 0)."'>".NETCAT_MODULE_AUTH_SETUP_REGISTRATION."</a>  ";
            $template['auth_link_form'] .= "<a id='nc_auth_link' href='#'>".NETCAT_MODULE_AUTH_AUTHORIZATION."</a></div></div>";
            $template['auth_link_form'] .= "<div id='nc_auth_layer' style='display:none;'>".$this->auth_form($params)."<span class='simplemodal-close'></span></div>";
            $template['auth_link_form'] .= " <script type='text/javascript' >
        var nc_auth_ajax_obj = new nc_auth_ajax(".json_encode(array('params' => urlencode(serialize($params)), 'template' => urlencode(serialize($template)), 'postlink' => $this->core->SUB_FOLDER.$this->core->HTTP_ROOT_PATH.'modules/auth/ajax.php')).");
      </script>";
        }

        return $template['auth_link_form'];
    }

    public function auth_form_for_authorized($params = array(), $template = array()) {
        global $current_user, $AUTHORIZE_BY, $AUTH_USER_ID, $SUB_FOLDER, $REQUEST_URI, $REQUEST_METHOD;
        $msg_url = nc_auth_messages_url();
        $new_msg = nc_auth_messages_new();

        if (!isset($template['messages']))
                $template['messages'] = $this->tpl->get_messages();
        if (!isset($template['messages_new']))
                $template['messages_new'] = $this->tpl->get_messages_new();
        if (!isset($template['authorized']))
                $template['authorized'] = $this->tpl->get_authorized();

        $template['messages_new'] = str_replace(array('%msg_url', '%msg_new'),
                        array($msg_url, $new_msg), $template['messages_new']);
        $messages = $new_msg ? $template['messages_new'] : $template['messages'];
        $login = $current_user['ForumName'] ? $current_user['ForumName'] :  $current_user[$AUTHORIZE_BY];
        //$login = str_replace('$', '&#36;', $login);
        $login = str_replace('$', '&#36;', htmlspecialchars($login));

        $result = str_replace(array('%login', '%profile_link', '%exit_link', '%messages'),
                        array($login,
                                nc_auth_profile_url($AUTH_USER_ID),
                                $this->core->SUB_FOLDER.$this->core->HTTP_ROOT_PATH."modules/auth/?logoff=1&amp;REQUESTED_FROM=".$REQUEST_URI."&amp;REQUESTED_BY=".$REQUEST_METHOD,
                                $messages),
                        $template['authorized']);

        return $result;
    }

    /**
     * Проверка необходимости вывода каптчи в форме аутентификации.
     * @return bool
     */
    public function need_captcha() {
        return nc_core::get_object()->user->captcha_is_required();
    }

    public function add_user_listen($user_id) {
        $user_id = intval($user_id);

        // личный счет
        if ($this->core->get_settings('pa_allow', 'auth') &&
                ($start = $this->core->get_settings('pa_start', 'auth'))) {
            $this->pa_add($start, $user_id, 'Стартовый капитал');
        }
    }

    private function make_mail($type, $user_id, $params) {
        $userinfo = $this->db->get_row("SELECT * FROM `User` WHERE `User_ID` = '".intval($user_id)."' ", ARRAY_A);
        $subject = $this->core->get_settings($type . '_subject', 'auth');
        $body = $this->core->get_settings($type . '_body', 'auth');
        $is_hrml = $this->core->get_settings($type . '_is_html', 'auth');

        $confirm_link = $params['confirm_link'] ? $params['confirm_link'] :
            nc_get_scheme().'://'.$_SERVER['HTTP_HOST'].$this->core->SUB_FOLDER.$this->core->HTTP_ROOT_PATH."modules/auth/confirm.php?id=".$userinfo['User_ID']."&code=".$userinfo['RegistrationCode'];

        $password = $params['password'] ? $params['password'] : $this->core->input->fetch_get_post('Password1');

        $macro = array('SITE_NAME' => $this->core->catalogue->get_current('Catalogue_Name'),
                'SITE_URL' => $_SERVER['HTTP_HOST'],
                'USER_LOGIN' => $userinfo[$this->core->AUTHORIZE_BY],
                'USER_NAME' => $userinfo['ForumName'],
                'USER_EMAIL' => $userinfo['Email'],
                'USER_ID' => $userinfo['User_ID'],
                'PASSWORD' => $password,
                'CONFIRM_LINK' => $confirm_link);

        foreach ($macro as $k => $v) {
            $subject = str_replace('%'.$k, $v, $subject);
            $body = str_replace('%'.$k, $v, $body);
        }

        return array('subject' => $subject, 'body' => $body, 'html' => $is_hrml, 'user_email' => $userinfo['Email']);
    }

    public function get_confirm_mail($user_id, $password = null) {
        $params = array('password' => $password);
        return $this->make_mail('mail_confirm', $user_id, $params);
    }

    public function get_confirm_after_mail($user_id, $password = null) {
        $params = array('password' => $password);
        return $this->make_mail('mail_confirm_after', $user_id, $params);
    }

    public function get_recovery_mail($user_id, $confirm_code) {
        global $sub;

        $confirm_link = nc_get_scheme().'://'.$_SERVER['HTTP_HOST'].$this->core->SUB_FOLDER.$this->core->HTTP_ROOT_PATH."modules/auth/password_recovery.php?sub=".$sub."&uid=".$user_id."&ucc=".$confirm_code;
        $params = array('confirm_link' => $confirm_link);

        return $this->make_mail('mail_recovery', $user_id, $params);
    }

    public function get_notify_admin_mail($user_id) {
        $params = array();

        return $this->make_mail('mail_notify_admin', $user_id, $params);
    }

    public function change_password_form() {
        global $nc_core, $db, $catalogue, $sub, $cc;
        global $HTTP_ROOT_PATH, $SUB_FOLDER;

        if (isset($_REQUEST['uid'])) $uid = (int) $_REQUEST['uid'];
        if (isset($_REQUEST['ucc']))
                $ucc = htmlspecialchars($_REQUEST['ucc'], ENT_QUOTES);

        if ($this->core->template->get_current('File_Mode')) {
            $field = $this->auth_view->get_field_path('change_password_form');

            ob_start();
            include($field);
            $result = ob_get_clean();
        } else {
            eval(nc_check_eval('$result = "'.$nc_core->get_settings('change_password_form', 'auth').'";'));
        }

        return $result;
    }

    public function recovery_password_form() {
        global $nc_core, $db, $catalogue, $sub, $cc;
        global $Login, $Email;
        global $HTTP_ROOT_PATH, $SUB_FOLDER;

        if ($this->core->template->get_current('File_Mode')) {
            $field = $this->auth_view->get_field_path('recovery_password_form');

            ob_start();
            include($field);
            $result = ob_get_clean();
        } else {
            eval(nc_check_eval('$result = "'.$nc_core->get_settings('recovery_password_form', 'auth').'";'));
        }

        return $result;
    }

    public function login_form($IsReturn = 0) {
        global $nc_core;
        global $HTTP_ROOT_PATH, $AUTH_USER, $REQUEST_URI, $REQUESTED_FROM, $REQUEST_METHOD, $SUB_FOLDER;
        global $db, $catalogue, $sub, $cc, $AUTH_USER_ID, $AUTHORIZATION_TYPE, $ADMIN_AUTHTYPE, $REQUESTED_BY;
        global $nc_auth, $nc_auth_fb, $nc_auth_vk, $nc_auth_openid, $nc_auth_twitter, $nc_auth_oauth;

        if ($AUTH_USER_ID) return;

        if (!$REQUESTED_FROM) $REQUESTED_FROM = $REQUEST_URI;
        if (!isset($REQUESTED_BY) || isset($REQUESTED_BY) && (strtoupper((string)$REQUESTED_BY) != 'GET' && strtoupper((string)$REQUESTED_BY) != 'POST'))
                $REQUESTED_BY = $REQUEST_METHOD;
        if ($nc_core->get_settings('user_login_form_disable', 'auth')) return;

        if ($this->core->template->get_current('File_Mode')) {
            $field = $this->auth_view->get_field_path('user_login_form');

            ob_start();
            include($field);
            $result = ob_get_clean();
        } else {
            $template = $nc_core->get_settings('user_login_form', 'auth');

            eval(nc_check_eval('$result = "'.$template.'";'));

            if ($IsReturn) {
                return $result;
            }
        }

        echo $result;
    }

    public function get_sql_check_ip() {
        $ip_check_level = intval($this->core->get_settings('ip_check_level', 'auth'));


        if ($ip_check_level == 0) {
            $SqlCheckIp = '';
        } elseif ($ip_check_level == 4) {
            $SqlCheckIp = ' AND s.UserIP = '.sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
        } else {
            $IpVal = explode('.', $_SERVER['REMOTE_ADDR'], $ip_check_level + 1);
            array_pop($IpVal);
            $IpVal = implode('.', $IpVal);
            $UserIPBegin = $IpVal.str_repeat('.0', 4 - $ip_check_level);
            $UserIPEnd = $IpVal.str_repeat('.255', 4 - $ip_check_level);
            $SqlCheckIp = ' AND s.UserIP >= '.sprintf("%u", ip2long($UserIPBegin)).' AND s.UserIP <= '.sprintf("%u", ip2long($UserIPEnd));
        }

        return $SqlCheckIp;
    }

}

