<?php

if(!defined("DS_FORM_LOAD") || DS_FORM_LOAD!==true) die();

class DSFormSend extends DSMain {

    public $error = array();
    public $im = 0;
    public $mail;
	
	function __construct() {
		$this->request();
		$this->getConfig();
	}

	public function index() {

		if (isset($this->post['formid']) && !empty($this->post['formid'])) {
			$this->formID = $this->post['formid'];
		} else {
			throw new Exception("form ID", 1);
		}

		if (!$this->formConfig->getConfig($this->formID)) throw new Exception("file config form", 1);



			foreach ($this->formConfig->formFields as $field) {
				if(isset($field['attributs']['required'])) {
					if(!isset($field['attributs']['pattern'])) $field['attributs']['pattern'] = '';
					if(!isset($field['error'])) $field['error'] = '';

			 		if(!isset($this->post[$field['attributs']['name']])) {
						$this->error[$name] = 1;
					} else {
						$this->validate($field['attributs']['pattern'], $this->post[$field['attributs']['name']],$field['attributs']['name'],$field['error']);
					}
				}
			}

			if (isset($this->error) && sizeof($this->error)) {
				$this->error['error'] = 1;
				$this->error['formid'] = $this->formID;
				$this->responseJson($this->error);
				exit();
			}

			$this->mail = new PHPMailer;

			$message = array();

			foreach ($this->formConfig->formFields as $field) {



				if(isset($field['formail']) && !empty($field['formail'])) {
					$field['attributs']['name'] = preg_replace('|\[[^\]]*\]|siU', '', $field['attributs']['name']);

					$field_name = $field['attributs']['name'];

					if (!strcmp($field['type'],'input') && !strcmp($field['attributs']['type'],'file') && isset($this->files[$field_name])) {
						$this->inputModel($field['attributs']['type'],$field['attributs']['name'],$message);
						continue;
					}

					if (!isset($this->post[$field_name])) {
				 		continue;
					} else if (is_array($this->post[$field_name]) && !isset($this->post[$field_name][0])) {
						array_shift($this->post[$field_name]);
						continue;
					}


					if(isset($field['name_mail']) && !empty($field['name_mail'])) {
						$message[$this->im]['name'] = $field['name_mail'];

					} elseif(isset($field['label']) && !empty($field['label'])) {
						$field['label'] = str_replace('(*)', '', $field['label']);
						$message[$this->im]['name'] = trim($field['label']);
					} else $message[$this->im]['name'] = '';

		




					switch ($field['type']) {
						case 'input':
							 $message = $this->inputModel($field['attributs']['type'],$field['attributs']['name'],$message);
						break;
						case 'textarea':
							 $message = $this->textareaModel($field['attributs']['name'],$message);
						break;
						case 'freearea':
							$message[$this->im]['message'] = $field['value'];
						break;
						case 'select':
							$message = $this->selectModel($field['attributs']['name'], $message);
						break;
					}
					$this->im++;
				}
			}

		$data['mailMessage'] = $message;
		$messageMail = $this->renderTemplate('mail', $data);

		$this->mail->CharSet = $this->formConfig->charset;
		$this->mail->From = $this->formConfig->mailFromEmail;
		$this->mail->FromName = $this->formConfig->mailFromName;

		foreach ($this->formConfig->mailToEmail as $email) {
			$this->mail->addAddress($email);
		}

		foreach ($this->formConfig->mailCcEmail as $email) {
			$this->mail->addCC($email);
		}

		$this->mail->isHTML(true);
		$this->mail->Subject = $this->formConfig->mailSubject;
		$this->mail->Body = $messageMail;
		$this->mail->XMailer = $_SERVER['HTTP_HOST'];

		if($this->formConfig->smtpOn) {
			$this->mail->IsSMTP();
			$this->mail->Host       = $this->formConfig->smtpHost;
			$this->mail->SMTPSecure = $this->formConfig->smtpSecure;
			$this->mail->Port       = $this->formConfig->smtpPort;
			if($this->formConfig->smtpAuth) {
				$this->mail->SMTPAuth = true;
				$this->mail->Username = $this->formConfig->smtpUsername;
				$this->mail->Password = $this->formConfig->smtpPassword;
			} else {
				$this->mail->SMTPAuth = false;
			}
			if(is_bool($this->formConfig->smtpFromEmail)) {
				$this->mail->From = $this->formConfig->smtpUsername;
			} else {
				$this->mail->From = $this->formConfig->smtpFromEmail;
			}
		}





		if(@$this->mail->send()){
		//if(true){
			$this->responseJson(
					array(
						'error'      => 0,
						'formid'     => $this->formID,
						'error_text' => $this->renderTemplate('goodmail', $data),
					)
			);
		} else {
			$this->responseJson(
					array(
						'error'      => 2,
						'formid'     => $this->formID,
						'error_text' => $this->renderTemplate('badmail', $data),
					)
			);
		}

		if($this->formConfig->mailReverseEmail && isset($this->post['email']) && !empty($this->post['email'])) {
			$this->mail->clearAddresses();
			$this->mail->clearCCs();
			$this->mail->Body = $this->renderTemplate('reversemail', $data);
			$this->mail->addAddress($_POST['email']);
			@$this->mail->send();
		}
	}

 	public function validate($pattern, $value, $name, $errorField) {
 		if (extension_loaded('mbstring')) {
 			$valueStrlen = mb_strlen($value, $this->formConfig->charset);
 		} else {
 			$valueStrlen = strlen($value);
 		}
		if ((empty($value) 
			|| ($this->formConfig->validateStrlen > 0 && $valueStrlen < $this->formConfig->validateStrlen))
			|| (!empty($pattern) && !preg_match('/'.$pattern.'/', $value))) {

			$this->error[$name] = 1;
		}

		if(!empty($errorField) && !empty($this->error[$name])) {
			$this->error[$name] = $errorField;
		} elseif(!empty($this->error[$name])) {
			$this->error[$name] = $this->formConfig->validateError;
		}
		return;
	}

	public function inputModel($attrtype, $attrname, $message) {
		
		switch ($attrtype) {
			case 'file':
				$postfiles = $this->files[$attrname];

				if(is_array($postfiles['error'])) {	
					foreach ($postfiles['error'] as $eindex => $evalue) {
						if($postfiles['error'][$eindex] == 0) {
							$this->mail->addAttachment($postfiles['tmp_name'][$eindex],$postfiles['name'][$eindex]);
						}
					}
				} else {
					if($postfiles['error'] == 0) {					
						$this->mail->addAttachment($postfiles['tmp_name'],$postfiles['name']);
					}
				}
			break;	
			default:
				$message = $this->textareaModel($attrname, $message);
			break;
		}
		return 	$message;
	}

	public function selectModel($attrname, $message) {
		
		if(!is_array($this->post[$attrname])){
			$message[$this->im]['message'] = $this->post[$attrname];
		} else {
			foreach ($this->post[$attrname] as $postval) {
				$message[$this->im]['message'] = $postval;
				$this->im++;
			}
		}
		return 	$message;		
	}

	public function textareaModel($attrname, $message)	{
		if(!is_array($this->post[$attrname])){
			$message[$this->im]['message'] = $this->post[$attrname];
		} else {
			if (count($this->post[$attrname])!=0){
			    $message[$this->im]['message'] = $this->post[$attrname][0];			
				array_shift($this->post[$attrname]);
			}

		}
		return 	$message;
	}
}


?>