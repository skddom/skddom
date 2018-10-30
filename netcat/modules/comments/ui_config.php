<?php
/* $Id: ui_config.php 4978 2011-07-11 12:38:26Z andrey $ */
if ( !class_exists("nc_System") ) die("Unable to load file.");
/**
  * Класс для облегчения формирования UI в модулях
  */
class ui_config_module_comments extends ui_config_module {

  public $headerText = NETCAT_MODULE_COMMENTS;
  public $headerImage = 'i_module_comments_big.gif';

  function ui_config_module_comments($active_tab = 'admin', $toolbar_action = 'list', $hash = '') {
    global $db;
    global $MODULE_FOLDER;



    if ($active_tab == 'admin') {
        $this->tabs[] = array(
            'id' => 'admin',
            'caption'=> NETCAT_MODULE_COMMENTS,
            'location' => "module.comments.".$toolbar_action
        );
    }
    else {
        $this->tabs[] = array(
            'id' => 'admin',
            'caption'=> NETCAT_MODULE_COMMENTS,
            'location' => "module.comments.list"
        );
    }
    $this->tabs[] = array(
      'id' => 'settings',
      'caption' => TOOLS_MODULES_MOD_PREFS,
      'location' => "module.comments.settings"
    );

    $this->activeTab = $active_tab;
    $this->treeMode = "modules";
    
    if ($active_tab == 'admin') {
      $this->toolbar[] = array(
        'id' => "list",
        'caption' => NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_LIST_TAB,
        'location' => "module.comments.list",
        'group' => "comments"
      );
      $this->toolbar[] = array(
        'id' => "subscribes",
        'caption' => NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SUBSCRIBES,
        'location' => "module.comments.subscribes",
        'group' => "comments"
      );
      
      $this->toolbar[] = array(
        'id' => "converter",
        'caption' => NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_CONVERTER_TAB,
        'location' => "module.comments.converter",
        'group' => "comments"
      );
      $this->toolbar[] = array(
        'id' => "optimize",
        'caption' => NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_OPTIMIZE_TAB,
        'location' => "module.comments.optimize",
        'group' => "comments"
      );

      
    }

    if ( $active_tab == 'settings' ) {
      if ( !$toolbar_action ) $toolbar_action = 'settings';
      $this->toolbar[] = array(
        'id' => "settings",
        'caption' => NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_TEMPLATE_MAIN,
        'location' => "module.comments.settings",
        'group' => "comments"
      );
      $this->toolbar[] = array(
        'id' => "template",
        'caption' => NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_TEMPLATE_TAB,
        'location' => "module.comments.template",
        'group' => "comments"
      );
      $this->toolbar[] = array(
        'id' => "subscribe",
        'caption' => NETCAT_MODULE_COMMENTS_ADMIN_TEMPLATE_SUBSCRIBE_TAB,
        'location' => "module.comments.subscribe",
        'group' => "comments"
      );
    }

    if ($toolbar_action) $this->locationHash = "module.comments.".($hash ? $hash : $toolbar_action);
      $this->activeToolbarButtons[] = $toolbar_action;
  }
}

?>