<? if ($result && $result['new_id']): ?>
    <? nc_print_status(TOOLS_STORE_INSTALL_COMPLETE, 'ok') ?>
    <a class='nc-btn nc--blue' href="<?=$ADMIN_PATH ?>catalogue/index.php?action=edit&amp;phase=2&amp;CatalogueID=<?= $result['new_id'] ?>"><?= TOOLS_STORE_GOTO_SITE_SETTINGS ?></a>
<? endif ?>