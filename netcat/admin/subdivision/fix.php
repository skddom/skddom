<?php

/**
 * Скрипт для изменения значений наследуемых полей разделов
 * (убирает значения у подразделов там, где они совпадают
 * со значениями родительского раздела).
 *
 * История вопроса: в версии 5.5.0.151xx был изменен способ хранения значений
 * наследуемых полей. До изменений они сохранялись в базе данных для дочерних
 * разделов, после — для наследования значение поля должно быть пустым.
 */

$NETCAT_FOLDER = realpath("../../../") . '/';
require $NETCAT_FOLDER . "vars.inc.php";
require $ADMIN_FOLDER . "function.inc.php";

$nc_core = nc_core::get_object();
$db = $nc_core->db;

ob_start();
BeginHtml('Наследуемые значения дополнительных полей разделов');
echo '<div style="max-width: 1000px">';


$inheritable_subdivision_fields = (array)$db->get_results(
    "SELECT `Field_Name`, `NotNull`, `TypeOfData_ID`
       FROM `Field`
      WHERE `System_Table_ID` = 2
        AND `Inheritance` = 1",
    ARRAY_A
);


if (!$nc_core->input->fetch_get_post('fix')) {
    // --- Вывод сообщения -----------------------------------------------------

    $has_inherited_subdivision_values = false;
    // Проверка необходимости изменений
    if ($inheritable_subdivision_fields) {
        foreach ($inheritable_subdivision_fields as $field) {
            $field_name = $field['Field_Name'];
            $has_inherited_subdivision_values = $db->get_var(
                "SELECT COUNT(*)
                   FROM `Subdivision` AS child
                   JOIN `Subdivision` AS parent
                         ON (child.`Parent_Sub_ID` = parent.`Subdivision_ID`)
                  WHERE child.`$field_name` != ''
                    AND child.`$field_name` IS NOT NULL
                    AND child.`$field_name` = parent.`$field_name`"
            );

            if ($has_inherited_subdivision_values) {
                break;
            }
        }
    }

    if ($inheritable_subdivision_fields && $has_inherited_subdivision_values) {
        echo <<<EOS
<div class="nc-alert nc--yellow">
    <div>
    <i class="nc-icon-l nc--status-warning"></i>

    <p style="margin-left: 40px"><strong>
    Для улучшения работы с наследуемыми полями разделов в обновлённой версии системы
    был изменён способ хранения значений наследуемых полей.</strong></p>

    <p>Теперь унаследованные значения полей для дочерних разделов не сохраняются
    в базе данных, а определяются исходя из значений вышестоящих разделов.</p>

    <p>Эти изменения никак не отразятся на работе стандартных функций системы
    (таких, как <code>nc_browse_sub()</code>, <code>s_browse_sub()</code>, <code>nc_nav</code>),
    но <em>в редких случаях</em>, когда получение информации о разделах производится
    минуя стандартные функции (при помощи SQL-запросов), это может повлиять на
    работу отдельных частей вашего сайта.</p>

    <p>При установке обновления значения полей разделов в базе данных не были изменены,
    и правки в системе не повлияют на существующие разделы на вашем сайте. Однако для того,
    чтобы вы могли в полной мере воспользоваться улучшениями в наследовании полей разделов,
    рекомендуем обновить их значения. При нажатии на кнопку «Обновить значения полей разделов»
    значения наследуемых полей, которые у дочерних разделов совпадают со значениями
    родительских разделов, в базе данных будут заменены на пустое значение.</p>

    <p>Если на вашем сайте используется выборка информации о наследуемых полях
    разделов напрямую из базы данных (SQL-запросами), и вы не уверены, как изменения
    могут повлиять на работу вашего сайта, советуем, если это возможно, сначала
    проверить как отразятся данные изменения на копии сайта. Копию проекта
    можно создать при помощи инструмента
    «<a href="{$nc_core->ADMIN_PATH}/#tools.backup" target="_top">Архивы проекта</a>».</p>

    <p><a href="$fix_script_path?fix=inheritance" class="nc-btn nc--small nc--blue">
    Обновить значения полей разделов
    </a></p>

    </div>
</div>

EOS;

    }
    else {
        // Ничего не нужно делать — нет полей или дублирующихся значений
        echo '<div class="nc-alert nc--green">',
             '<i class="nc-icon-l nc--status-success"></i>',
             'Нет разделов, которые требуют обновления.',
             '</div>';
    }
}
else {
    // --- Обновление значений -------------------------------------------------
    $no_errors = true;
    foreach ($inheritable_subdivision_fields as $field) {
        $field_name = $field['Field_Name'];
        $new_value = ($field['NotNull'] ? "''" : "NULL");
        $db->query(
            "UPDATE `Subdivision` AS child
               JOIN `Subdivision` AS parent
                     ON (child.`Parent_Sub_ID` = parent.`Subdivision_ID`)
                SET child.`$field_name` = $new_value
              WHERE child.`$field_name` != ''
                AND child.`$field_name` IS NOT NULL
                AND child.`$field_name` = parent.`$field_name`"
        );

        if ($db->last_error) {
            $no_errors = false;

            echo '<div class="nc-alert nc--red">',
                 '<i class="nc-icon-l nc--status-error"></i>',
                 'Ошибка выполнения SQL-запроса: <pre>', htmlspecialchars($db->last_error), '</pre>',
                 '</div>';
            $db->last_error = null;
        }
    }

    if ($no_errors) {
        echo '<div class="nc-alert nc--green">',
             '<i class="nc-icon-l nc--status-success"></i>',
             'Значения наследуемых полей разделов успешно обновлены.',
             '</div>';
    }

}


echo '</div>';
EndHtml();

$buffer = ob_get_clean();
if (!$nc_core->NC_UNICODE) {
    $buffer = $nc_core->utf8->utf2win($buffer);
}

echo $buffer;