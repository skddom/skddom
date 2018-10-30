<?php

class nc_netshop_mailer_template_table extends nc_netshop_table
{
    protected $table       = 'Netshop_MailTemplate';
    protected $primary_key = 'Template_ID';
    protected $fields      = array(
        'Template_ID' => array(
            'field' => 'hidden',
        ),
        'Parent_Template_ID' => array(
            'field' => 'hidden',
        ),
        'Catalogue_ID' => array(
            'field' => 'hidden',
        ),
        'Name' => array(
            'title' => NETCAT_MODULE_NETSHOP_MAILER_MASTER_TEMPLATE_NAME,
            'field' => 'string',
            'required' => true,
        ),
        'Type' => array(
            'field' => 'hidden',
        ),
        'Subject' => array(
            'title' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_SUBJECT,
            'field' => 'string',
            'required' => false,
        ),
        'Body' => array(
            'title' => NETCAT_MODULE_NETSHOP_MAILER_TEMPLATE_BODY,
            'field' => 'string',
            'required' => true,
        ),
        'Enabled' => array(
            'field' => 'hidden',
        ),

    );

}