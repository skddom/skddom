<?php

if(!defined("DS_FORM_LOAD") || DS_FORM_LOAD!==true) die();

	class ConfigForm{

		public $charset;
		public $validatehtml5;
		public $kolsim;
		public $error_empty;

		public $smtp_on;
		public $smtp_host;
		public $smtp_secure;
		public $smtp_port;
		public $smtp_auth;
		public $smtp_username;
		public $smtp_password;
		public $smtp_fromemail;

		public $to_email;
		public $cc_email;
		public $from_email;
		public $from_name;
		public $subject;
		public $headtable;
		public $width_table;
		public $width_name;
		public $width_message;
		public $alignname;
		public $alignmessage;

		public $good_mail;
		public $bad_mail;
		public $back_mail;
		public $repeat_mail;

		public $form_fields;

		public function getConfig($form_id) {

			if (file_exists('forms/'.$form_id.'.php')) {
				include 'forms/' . $form_id.'.php';
			} else {
				echo 'Not found template for #"'.$form_id.'"';
				die();
			}

			$this->charset = CHARSET;
			$this->validatehtml5 = VALIDATE_HTML5;
			$this->kolsim = KOLSIM;
			$this->error_empty = ERROR_EMPTY;

			$this->smtp_on = SMTP_ON;
			$this->smtp_host = SMTP_HOST;
			$this->smtp_secure = SMTP_SECURE;
			$this->smtp_port = SMTP_PORT;
			$this->smtp_auth = SMTP_AUTH;
			$this->smtp_username = SMTP_USERNAME;
			$this->smtp_password = SMTP_PASSWORD;
			$this->smtp_fromemail = SMTP_FROMEMAIL;

			$this->to_email = $to_email;
			$this->cc_email = $cc_email;
			$this->from_email = FROM_EMAIL;
			$this->from_name = FROM_NAME;
			$this->subject = SUBJECT;
			$this->headtable = HEADTABLE;
			$this->width_table = WIDTHTABLE;
			$this->width_name = WIDTHNAME;
			$this->width_message = WIDTHMESSAGE;
			$this->alignname = ALIGNNAME;
			$this->alignmessage = ALIGNMESSAGE;

			$this->good_mail = $good_mail;
			$this->bad_mail = $bad_mail;
			$this->back_mail = BACK_MAIL;
			$this->repeat_mail = $repeat_mail;

			$this->form_fields = $dsForma;

			return true;
		}


	}


?>