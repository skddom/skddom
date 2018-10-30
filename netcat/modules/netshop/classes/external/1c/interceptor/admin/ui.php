<?php

/**
 * Класс конфигурации UI для мастера перехвата файлов импорта 1С.
 */
class nc_netshop_external_1c_interceptor_admin_ui extends nc_netshop_admin_ui
{
    /**
     * Конструктор.
     */
    public function __construct()
    {
        $this->headerText = NETCAT_MODULE_NETSHOP;
        $this->subheaderText = NETCAT_MODULE_NETSHOP_1C_INTEGRATION_INTERCEPTOR;
        $this->locationHash = 'module.netshop.1c.interceptor';

        $this->treeMode = 'modules';
        $this->treeSelectedNode = 'netshop-1c.interceptor';
    }
}