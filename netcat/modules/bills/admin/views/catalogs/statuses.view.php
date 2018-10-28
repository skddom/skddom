<?php if (!class_exists('nc_core')) {
    die;
} ?>
<!-- Вкладка «Статусы счетов» -->
<h2>Статусы счетов</h2>
<!-- /del -->
<table class="nc-table nc--bordered nc--striped" width="100%">
    <tbody>
    <tr>
        <th>Наименование</th>
        <th width="1%"></th>
    </tr>
    <tr>
        <td><a href="#">черновик</a></td>
        <td><a onclick="return confirm('Подтвердить удаление')" href="#"><i class="nc-icon nc--remove"></i></a></td>
    </tr>
    <tr>
        <td><a href="#">не оплачен</a></td>
        <td><a onclick="return confirm('Подтвердить удаление')" href="#"><i class="nc-icon nc--remove"></i></a></td>
    </tr>
    <tr>
        <td><a href="#">оплачен</a></td>
        <td><a onclick="return confirm('Подтвердить удаление')" href="#"><i class="nc-icon nc--remove"></i></a></td>
    </tr>
    <tr>
        <td><a href="#">отменён</a></td>
        <td><a onclick="return confirm('Подтвердить удаление')" href="#"><i class="nc-icon nc--remove"></i></a></td>
    </tr>
    </tbody>
</table>