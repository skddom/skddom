<?php if (!class_exists('nc_core')) {
    die;
} ?>
<!-- Вкладка «Справочники» → «Оказываемые услуги» -->
<h2>Статусы счетов</h2>
<h2>Оказываемые услуги</h2>
<!-- /del -->
<table class="nc-table nc--bordered nc--striped" width="100%">
    <tbody>
    <tr>
        <th>Наименование</th>
        <th width="150">Стоимость</th>
        <th width="1%"></th>
    </tr>
    <tr>
        <td><a href="#">Разработка веб-сайтов</a></td>
        <td>30 000 р.</td>
        <td><a onclick="return confirm('Подтвердить удаление')" href="#"><i class="nc-icon nc--remove"></i></a></td>
    </tr>
    <tr>
        <td><a href="#">Обслуживание веб-сайтов</a></td>
        <td>15 000 р.</td>
        <td><a onclick="return confirm('Подтвердить удаление')" href="#"><i class="nc-icon nc--remove"></i></a></td>
    </tr>
    </tbody>
</table>

<!-- del -->
<!-- Вкладка «Справочники» → «Оказываемые услуги» -->
<br><br>
<h2>Добавление и редактирование</h2>
<!-- /del -->
<form action="" method="POST" class="nc-form nc--vertical">
    <div class="nc-form-row">
        <label>Название услуги<br><input type="text" name="" class="nc--xlarge" value="Разработка веб-сайтов"></label>
    </div>
    <div class="nc-form-row">
        <label>Стоимость<br><input type="text" name="" class="nc--large" value="15 000"></label>
    </div>
</form>