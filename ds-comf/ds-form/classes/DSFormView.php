<?php

if (!defined("DS_FORM_LOAD") || DS_FORM_LOAD!==true) die();

class DSFormView extends DSMain {
	
    public $dsConfig = array();

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
		
		if (isset($this->post['dsconfig']) && sizeof($this->post['dsconfig'])) {
			$this->dsConfig = $this->jsonDecode($this->post['dsconfig']);
		}

		if ($this->formConfig->validateHtml5) {
			$novalidate = '';
		} else {
			$novalidate = 'novalidate';
		}

		$strForm = "";
		$formTpl  = "\n";
		$formTpl .= '<form id="' . $this->formID . '-form" method="POST" enctype="multipart/form-data" '.$novalidate.'>';

		foreach ($this->formConfig->formFields as $index => $field) {
			
			if (isset($field['class'])) {
				$fieldclass = ' ' . $field['class'];
			} else {
				$fieldclass = '';
			}
			
			if (isset($field['id'])) {
				$fieldid = 'id = "' . $field['id'] . '" ';
			} else {
				$fieldid = '';
			}

			if ($field['type'] == 'freearea') {
				$strForm .= "\n" . $field['value'];
			} else {
				$strForm .= "\n".'<div ' . $fieldid . 'class="field-'.$index.$fieldclass . '">' . "\n";

				if (!isset($field['label'])) {
					$field['label'] = '';
				} else {
					$field['label'] = str_replace('(*)', '<span class="required">*</span>', $field['label']);
				}

				if (!isset($field['attributs'])) $field['attributs'] = array();

				switch ($field['type']) {
							case 'input':
								$strForm .= $this->input($field['label'], $field['attributs']);
							break;
							case 'textarea':
								$strForm .= $this->textarea($field['label'], $field['attributs']);
							break;
							case 'select':
								$strForm .= $this->select($field['label'], $field['attributs'], $field['options']);
							break;
						}

				$strForm .= '</div>';
			}
		}
		$formTpl .= $strForm . "\n".'</form>';

		$this->responseJson(
				array(
					'error'      => 0,
					'error_text' => $formTpl,
				)
		);
	}

    public function changeValue($attributs, $fieldType, $options = array()) {
    	$changeResult = array();
    	$dsConfig = $this->dsConfig;
    	
    	if (isset($attributs['name']) && !empty($attributs['name']) 
    	    && isset($this->dsConfig[$attributs['name']])
    	    && !empty($this->dsConfig[$attributs['name']])) {

		    $attributs['name'] = preg_replace('|\[[^\]]*\]|siU', '', $attributs['name']);

    		switch ($fieldType) {
    			case 'input':
    				$changeResult = $attributs;
    				$changeResult['value'] = $dsConfig[$attributs['name']];
    			break;
    			case 'textarea':
    				$changeResult = $attributs;
    				$changeResult['value'] = $dsConfig[$attributs['name']];
    			break;
    			case 'select':
    				$changeResult = $options;
    				$changeResult = $dsConfig[$attributs['name']];
    			break;
    		}
    		return $changeResult;
    	} else {
    		if ($fieldType == 'select') {
    			return $options;
    		} else {
    			return $attributs;
    		}
    	}
    }

 	public function input($label, $attributs) {
		$input = '';
		$attributs = $this->changeValue($attributs,'input');

		if (isset($label) && !empty($label)){
			if (isset($attributs['id']) && !empty($attributs['id'])) {
				$forid = ' for = "' . $attributs['id'] . '"';
			} else $forid = '';
			$input = '<label' . $forid . '>' . $label . '</label>' . "\n";
		}

		$input .= '<input';

		foreach ($attributs as $attr => $avalue) {
			switch ($attr) {
				case 'required':
					$input .= ' required';
					break;
				case 'autofocus':
					$input .= ' autofocus';
					break;
				case 'checked':
					$input .= ' checked';
					break;
				
				default:
					$input .= ' ' . $attr . '="' . $avalue .'" ';
					break;
			}
		}
		$input .= '>' . "\n";

		return $input;
	}

    public function textarea($label, $attributs) {
    	$textarea ="";
		$attributs = $this->changeValue($attributs, 'textarea');

		if (isset($label) && !empty($label)) {
			if (isset($attributs['id']) && !empty($attributs['id'])) {
				$forid = ' for = "' . $attributs['id'] . '"';
			} else {
				$forid = '';
			}
			$textarea='<label' . $forid . '>' . $label . '</label>'."\n";
		}

		$textarea .= '<textarea';
		foreach ($attributs as $attr => $avalue) {
			switch ($attr) {
				case 'required':
					$textarea .= ' required';
					break;
				case 'autofocus':
					$textarea .= ' autofocus';
					break;
				
				default:
					if ($attr != 'value'){
						$textarea .= ' ' . $attr . '="' . $avalue . '" ';
					}
					break;
			}
		}
		if (isset($attributs['value']) && !empty($attributs['value'])) {
			$textarea .= '>' . $attributs['value'] . '</textarea>' . "\n";
		} else $textarea .= '></textarea>' . "\n";
		return $textarea;
	}

    public function select($label, $attributs, $options) {
		$select = '';
		$options = $this->changeValue($attributs,'select',$options);

		if (isset($label) && !empty($label)){
			if (isset($attributs['id']) && !empty($attributs['id'])) {
				$forid = ' for = "' . $attributs['id'] . '"';
			} else $forid = '';
			$select='<label' . $forid . '>' . $label . '</label>' . "\n";
		}

		$select .= '<select';

		foreach ($attributs as $attr => $avalue) {
			switch ($attr) {
				case 'autofocus':
					$select .= ' autofocus';
					break;
				case 'required':
					$select .= ' required';
					break;
				case 'multiple':
					$select .= ' multiple';
					break;
				case 'disabled':
					$select .= ' disabled';
					break;
				default:
					$select .= ' ' . $attr . '="' . $avalue . '" ';
					break;
			}
		}

		$select .= '>' . "\n";

		foreach ($options as $soption) {
			$text = $soption['text'];
			unset($soption['text']);
			$select .= '<option';
			foreach ($soption as $attr => $avalue) {
				switch ($attr) {
					case 'disabled':
						$select .= ' disabled';
						break;
					case 'selected':
						$select .= ' selected';
						break;
					default:
						$select .= ' ' . $attr . '="' . $avalue . '" ';
						break;
				}
			}
			$select .= '>'.$text.'</option>'."\n";
		}
		$select .= '</select>' . "\n";
		return $select;
	}
}
?>
