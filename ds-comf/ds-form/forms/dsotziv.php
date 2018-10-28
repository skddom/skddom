<?php
return array(
	'mail'  => array(
		'to_email' => array('vp@skd-dom.ru'), //
		'subject'    => 'Отзыв',
	),
	'configform' => array(
		/*--Заголовок--*/
		array(
			'type'  => 'freearea',
			'value' => '<div class="form-head">Добавить отзыв</div>',
			),
		/*--Ваше имя--*/
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
							'placeholder' => 'Ваше имя',
							'value'       => '',
							'required'    => '',
							'autofocus'   => '',
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
							'placeholder' => 'Ваш e-mail',
							'pattern'     => '^([a-z,._,.\-,0-9])+@([a-z,._,.\-,0-9])+(\.([a-z])+)+$',
						   ),
			),
		/*--Ваше сообщение--*/
		array(
			'type'      => 'textarea',
			'label'     => 'Ваш отзыв (*)',
			'error'     => 'Поле "Отзыв" заполнено некорректно!',
			'formail'   => 1,
			'name_mail' => 'Отзыв',
			'attributs' => array(
							'name'        => 'message',
							'type'        => 'text',
							'rows'        => '8',
							'cols'        => '46',
							'value'       => '',
							'required'    => '',
							'placeholder' => 'Ваш отзыв',
							'value'       => '',
						   ),
			),

		array(
    'type' => 'input',
    'label'     => 'Приложить файл',
    'id'=>'file2',
    'formail' => 1,
    'attributs' => array(
                    'name'=>'myfiles[]',
                    'type'=>'file',
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