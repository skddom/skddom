<?php
return array(
	'mail'  => array(
		'to_email' => array('vp@skd-dom.ru'), //vp@skd-dom.ru
		'subject'    => 'Заказать строительство',
	),
	'configform' => array(
		/*--Заголовок--*/
		array(
			'type'  => 'freearea',
			'value' => '<div class="form-head">Заказать строительство</div><script>jQuery(\'#dsorder-form .field-3 input\').mask("+7(999)999-99-99");</script>',
			),
		/*--Ваше имя--*/
				array(
			'type'      => 'input',
			'label'     => 'Название проекта',
			'error'     => 'Поле "Название проекта" заполнено некорректно!',
			'formail'   => 1,
			'name_mail' => 'Имя',
			'attributs' => array(
							'id'          => 'nameproject',
							'name'        => 'nameproject',
							'type'        => 'text',
							'placeholder' => '',
							'value'       => '',
							'required'    => '',
							'autofocus'   => '',
						   ),
			),


		array(
			'type'      => 'input',
			'label'     => 'Ваше имя (*)',
			'error'     => 'Поле "Ваше имя" заполнено некорректно!',
			'formail'   => 1,
			'name_mail' => 'Имя',
			'attributs' => array(
							'id'          => 'youname',
							'name'        => 'name',
							'type'        => 'text',
					//		'placeholder' => 'Ваше имя',
							'value'       => '',
							'required'    => '',
							'autofocus'   => '',
						   ),
			),
		        /* Однострочный текст */
        array(
        'type' => 'input',
        'label' => 'Ваш телефон (*)',
        'error'=>'Поле "Ваш телефон" заполнено некорректно',
        'formail' => 1,
        'name_mail'=>'Телефон',
        'attributs' => array(
             'id'=>'field-id238580',
             'name'=>'field-name238580',
             'type'=>'text',
           //  'placeholder'=>'+ 7 (___) ___-__-__',
             'value'=>'',
              'required'=>'required',
             'pattern'=>'^\+?[\d,\-,(,),\s]+$',
             ),
        ),
		/*--Ваш e-mail--*/
		array(
			'type'      => 'input',
			'label'     => 'Ваш e-mail',
			'formail'   => 1,
			'name_mail' => 'E-mail',
			'attributs' => array(
							'id'          => 'youemail',
							'name'        => 'email',
							'type'        => 'text',
					//		'placeholder' => 'Ваш e-mail',
							'pattern'     => '^([a-z,._,.\-,0-9])+@([a-z,._,.\-,0-9])+(\.([a-z])+)+$',
						   ),
			),
		/*--Ваше сообщение--*/
		array(
			'type'      => 'textarea',
			'label'     => 'Комментарий',
			'error'     => 'Поле "Комментарий" заполнено некорректно!',
			'formail'   => 1,
			'name_mail' => 'Комментарий',
			'attributs' => array(
							'name'        => 'message',
							'type'        => 'text',
							'rows'        => '8',
							'cols'        => '46',
							'value'       => '',

							'placeholder' => '',
							'value'       => '',
						   ),
			),



		/*--Кнопка--*/
		array(
			'type'      => 'input',
			'class'     => 'buttonform',
			'attributs' => array(
							'type'  => 'submit',
							'value' => 'Отправить',
						   ),
			),
		/*--Блок ошибок--*/
		array(
			'type'  => 'freearea',
			'value' => '<div class="error_form"></div>',
			),
		),
	);