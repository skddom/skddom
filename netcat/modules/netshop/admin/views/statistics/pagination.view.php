<?php if (!class_exists('nc_core')) { die; } ?>

<? $total_pages = ceil($total/$items) ?>
<? if ($total_pages > 1): ?>
	<div class="nc-pagination">
		<? for ($p = 1; $p < $total_pages+1; $p++): ?>
			<a<?=$p == $page ? " class='nc--active'" : ''?> href="<?="{$page_url}&page={$p}";?>"><?=$p;?></a>
		<? endfor ?>
	</div>
<? endif ?>