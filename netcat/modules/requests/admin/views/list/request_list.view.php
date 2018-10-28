<?php if (!class_exists('nc_core')) { die; } ?>

<?= $ui->controls->site_select($catalogue_id) ?>

<div class="nc_admin_mode_content"><?=$request_list ?></div>