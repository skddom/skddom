<?php

if (!class_exists('nc_core')) { die; }
$netshop = nc_netshop::get_instance($catalogue_id);

echo $ui->controls->site_select($catalogue_id);

// Поля компонента "Заказ"
$order_class = $netshop->get_setting('OrderComponentID');
$order_fields = $db->get_col("SELECT CONCAT('\'', `Field_Name`, '\'') FROM `Field` WHERE `Class_ID` = '" . $order_class . "' ORDER BY `Field_ID`");

// Список статусов заказа
$order_statuses = array("0: 'новый заказ'");
foreach ($db->get_results("SELECT * FROM `Classificator_ShopOrderStatus`") as $row) {
    $order_statuses[] = $row->ShopOrderStatus_ID . ": '" . $row->ShopOrderStatus_Name . "'";
}

// Выводим форму
$form = $ui->form("?controller=$controller_name&action=statuses_save&catalogue_id=$catalogue_id")->vertical();
$form->add()->h2(NETCAT_MODULE_NETSHOP_ORDER_STATUSES);
$form->add_row(NETCAT_MODULE_NETSHOP_ORDER_STATUS_CHANGE_SEQUENCES);
$div = $form->add()->div()->class_name('work');
echo $form;

// Уже сохраненные настройки
$conditions = json_decode($netshop->get_setting('OrderStatusConditions'), true);

?>

<script>
    
    /*
     * Функция для динамического создания узла HTML
     */
    function HTMLNode(nodeName, data, innerText){
        if (nodeName === undefined) {nodeName = 'div';}
        if (data === undefined) {data = {};}
        if (innerText === undefined) {innerText = '';}
        this.data = data;
        this.nodeName = nodeName;
        this.self = document.createElement(nodeName);
        for (var i in this.data) {
            this.self.setAttribute(i, data[i]);
        }
        if (innerText) {
            this.self.innerHTML = innerText;
        }   
        return this.self;
    }
    
    var work = document.querySelector('.work');
    var conditionID = 0;
    var orderFields = [<?= implode(',', $order_fields) ?>];
    var orderStatuses = {<?= implode(',', $order_statuses) ?>};
    
    work.appendChild(HTMLNode('a', {href:'#', onclick:'orderStatusesAddCondition(); return false;'}, 'Новое условие'));
    
    function orderStatusesAddCondition(key, value) {
        conditionID++;
        var conditionRow = HTMLNode('div', {style: 'position:relative; background-color:#fafafa; border:1px solid #ccc; padding:25px; margin:10px 0; width:800px;'});
        var fieldSelect = HTMLNode('select', {name:'condition[' + conditionID + '][key]'});
        for (var i in orderFields) {
            fieldSelect.appendChild(HTMLNode('option', {}, orderFields[i]));
        }
        if (key) {
            fieldSelect.value = key;
        }
        conditionRow.appendChild(HTMLNode('span', {}, 'Значение поля '));
        conditionRow.appendChild(fieldSelect);
        conditionRow.appendChild(HTMLNode('span', {}, ' равно '));
        conditionRow.appendChild(HTMLNode('input', {name:'condition[' + conditionID + '][value]', value: value ? value : ''}));
        conditionRow.appendChild(HTMLNode('hr'));
        for (var i in orderStatuses) {
            conditionRow.appendChild(HTMLNode('h3', {style: 'text-transform:capitalize'}, orderStatuses[i]));
            var statusRow = HTMLNode('div', {style: 'padding-left:25px;'});
            var statusSelect = HTMLNode('select', {onchange:'statusChanged(this)', style:'display:block;', name:'condition[' + conditionID + '][statuses][' + i + '][]'});
            statusSelect.appendChild(HTMLNode('option', {value:'none'}, '- выберите статус -'));
            for (var j in orderStatuses) {
                statusSelect.appendChild(HTMLNode('option', {value:j}, orderStatuses[j]));
            }
            statusRow.appendChild(statusSelect);
            conditionRow.appendChild(statusRow);
        }
        conditionRow.appendChild(HTMLNode('a', {href:'#', onclick:'this.parentNode.parentNode.removeChild(this.parentNode); return false;', style:'position:absolute; right:25px; top:25px;'}, 'Удалить условие'));
        work.appendChild(conditionRow);
    }
    
    function statusChanged(element) {
        var neighbours = element.parentNode.querySelectorAll('select');
        for (var i in neighbours) {
            var item = neighbours[i];
            if (typeof item == 'object') {
                if (item != element && item.value == element.value) {
                    for (var j in neighbours) {
                        if (typeof neighbours[j] == 'object') {
                            if (neighbours[j].value == 'none') {
                                element.parentNode.removeChild(element);
                                return;
                            }
                        }
                    }
                    element.value = 'none';
                    return;
                }
            }
        };
        
        var addSelect = true;
        for (var i in neighbours) {
            var item = neighbours[i];
            if (typeof item == 'object') {
                if (item.value == 'none') {
                    addSelect = false;
                    break;
                }
            }
        }
        
        if (addSelect) {
            var statusSelect = element.cloneNode(true);
            statusSelect.value = 'none';
            element.parentNode.appendChild(statusSelect);
        }
    }
    
<?php
    
    if ($conditions) {
        foreach ($conditions as $condition) {
            echo "orderStatusesAddCondition('" . $condition['key'] . "', '" . $condition['value'] . "');";
            foreach ($condition['statuses'] as $status => $values) {
                foreach ($values as $key => $value) {
                    if ($value != 'none') {
                        echo "var element = document.querySelector('select[name=\"condition[' + conditionID + '][statuses][$status][]\"]:nth-of-type(" . ++$key . ")');";
                        echo "element.value = '$value';";
                        echo "statusChanged(element);";
                    }
                }
            }
        }
    }
    
?>

</script>