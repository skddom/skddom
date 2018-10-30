<?php
if(!defined("DS_FORM_LOAD") || DS_FORM_LOAD!==true) die();

class DSConfig {
	private $error            = array();
	
	public $charset           = 'utf-8';
	public $validateHtml5     = false;
	public $validateStrlen    = 3;
	public $validateError     = 'Поля не заполнены или слишком короткие!';
	
	public $smtpOn            = false;
	public $smtpHost;
	public $smtpSecure        = 'ssl';
	public $smtpPort          = 465;
	public $smtpAuth          = true;
	public $smtpUsername;
	public $smtpPassword;
	public $smtpFromEmail     = true;
	
	public $mailToEmail;
	public $mailCcEmail       = array();
	public $mailFromEmail;
	public $mailFromName      = 'Info';
	public $mailSubject;
	public $mailReverseEmail  = false;

	public $formFields;

	public function getConfig($form_id) {

		if (file_exists('forms/'.$form_id.'.php')) {
			$formConfig = include 'forms/' . $form_id.'.php';
		} else {
			throw new Exception('Not found template for #'.$form_id);
		}

		if(isset($formConfig['charset']) && !empty($formConfig['charset'])) {
			$this->charset = $formConfig['charset'];
		}
		if(isset($formConfig['validate']['html5'])) {
			$this->validateHtml5 = $formConfig['validate']['html5'];
		}
		if(isset($formConfig['validate']['strlen']) && !empty($formConfig['validate']['strlen'])) {
			$this->validateStrlen = $formConfig['validate']['strlen'];
		}
		if(isset($formConfig['validate']['error']) && !empty($formConfig['validate']['error'])) {
			$this->validateError = $formConfig['validate']['error'];
		}
		if(isset($formConfig['smtp']['on']) && !empty($formConfig['smtp']['on'])) {
			$this->smtpOn = $formConfig['smtp']['on'];
			if(isset($formConfig['smtp']['host']) && !empty($formConfig['smtp']['host'])) {
				$this->smtpHost = $formConfig['smtp']['host'];
			} else {
				$this->errorReport('smtpHost');
			}
			if(isset($formConfig['smtp']['secure']) && !empty($formConfig['smtp']['secure'])) {
				$this->smtpSecure = $formConfig['smtp']['secure'];
			}
			if(isset($formConfig['smtp']['port']) && !empty($formConfig['smtp']['port'])) {
				$this->smtpPort = $formConfig['smtp']['port'];
			}
			if(isset($formConfig['smtp']['auth'])) {
				$this->smtpAuth = $formConfig['smtp']['auth'];
			}
			if(isset($formConfig['smtp']['username']) && !empty($formConfig['smtp']['username'])) {
				$this->smtpUsername = $formConfig['smtp']['username'];
			} else {
				$this->errorReport('smtp_username');
			}
			if(isset($formConfig['smtp']['password']) && !empty($formConfig['smtp']['password'])) {
				$this->smtpPassword = $formConfig['smtp']['password'];
			} else {
				$this->errorReport('smtp_username');
			}
			if(isset($formConfig['smtp']['from_email'])) {
				$this->smtpFromEmail = $formConfig['smtp']['from_email'];
			}
		}
		if(isset($formConfig['mail']['to_email']) && is_array($formConfig['mail']['to_email']) && sizeof($formConfig['mail']['to_email'])) {
			$this->mailToEmail = array_filter($formConfig['mail']['to_email'], array(&$this, 'validateAddress'));
		} else {
			$this->errorReport('to_email');
		}
		if(isset($formConfig['mail']['cc_email']) && is_array($formConfig['mail']['cc_email']) && sizeof($formConfig['mail']['cc_email'])) {
			$this->mailCcEmail = array_filter($formConfig['mail']['cc_email'], array(&$this, 'validateAddress'));
		}
		if(isset($formConfig['mail']['from_email']) && !empty($formConfig['mail']['from_email'])) {
			$this->mailFromEmail = $formConfig['mail']['from_email'];
		} else {
			$this->mailFromEmail = 'info@'.str_replace('www.', '', $_SERVER['HTTP_HOST']);
		}
		if(isset($formConfig['mail']['from_name']) && !empty($formConfig['mail']['from_name'])) {
			$this->mailFromName = $formConfig['mail']['from_name'];
		} else {
			$this->mailFromName = 'Info';
		}
		if(isset($formConfig['mail']['subject']) && !empty($formConfig['mail']['subject'])) {
			$this->mailSubject = $formConfig['mail']['subject'];
		}
		if(isset($formConfig['mail']['reverse_email'])) {
			$this->mailReverseEmail = $formConfig['mail']['reverse_email'];
		}
		if(isset($formConfig['configform']) && is_array($formConfig['configform']) && sizeof($formConfig['configform'])) {
			$this->formFields = $formConfig['configform'];
		} else {
			$this->errorReport('configform');
		}
		
		if(sizeof($this->error)) {
			throw new Exception("config form (" . implode(", ", $this->error) . ")");
		} else {
			return true;
		}

		return false;
	}

	private function validateAddress($address) {
		if((boolean) preg_match(
                '/^[a-zA-Z0-9.!#$%&\'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}' .
                '[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/sD',
        $address)) {
			return $address;
		} else {
			$this->errorReport('email - ' . $address);
		}
	}

	private function errorReport($set){
		array_push($this->error, $set);
	}
}