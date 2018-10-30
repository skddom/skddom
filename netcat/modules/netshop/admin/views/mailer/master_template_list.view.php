<?php if (!class_exists('nc_core')) { die; } ?>

<?= $ui->controls->site_select($catalogue_id) ?>

<table class="nc-table nc--wide nc--bordered">
    <tr>
        <th><?= NETCAT_MODULE_NETSHOP_MAILER_MASTER_TEMPLATE_HEADER_NAME ?></th>
        <th class="nc--compact"></th>
        <th class="nc--compact"></th>
    </tr>
    <?php foreach ($templates as $row): ?>
    <tr>
        <?php
            $edit_link = "module.netshop.mailer.template.edit($row[Template_ID])";
        ?>
        <td><?= $ui->helper->hash_link($edit_link, $row['Name']) ?></a></td>
        <td class="nc--compact">
            <?= $ui->helper->hash_link(
                    $edit_link,
                    '<i class="nc-icon nc--edit" title="' . htmlspecialchars(NETCAT_MODULE_NETSHOP_ACTION_EDIT, ENT_QUOTES) .'"></i>'
                )
            ?>
        </td>
        <td>
            <?php

                if ($row['sub_template_count']) {
                    echo '<a href="#" title="' . htmlspecialchars(NETCAT_MODULE_NETSHOP_MAILER_MASTER_TEMPLATE_IS_USED) .
                         '"><i class="nc-icon nc--remove nc--disabled"></i></a>';
                }
                else {
                    echo $ui->controls->delete_button(
                        sprintf(NETCAT_MODULE_NETSHOP_MAILER_MASTER_TEMPLATE_CONFIRM_DELETE, $row['Name']),
                        array('controller' => $controller_name, 'id' => $row['Template_ID'])
                    );
                }
            ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<br>