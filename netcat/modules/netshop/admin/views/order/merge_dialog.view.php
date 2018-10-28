<?php if (!class_exists('nc_core')) { die; } ?>

<?
/** @var nc_netshop_order[] $orders */
/** @var nc_ui $ui */
/** @var array $order_statuses */
/** @var string $form_action */
?>

<div class="nc-modal-dialog" data-width="600" data-height="300">
    <div class="nc-modal-dialog-body" style="font-size: 110%">
        <form action="<?= $form_action ?>" method="post">
        <p>
            <strong><?= NETCAT_MODULE_NETSHOP_ORDER_MERGE_DESCRIPTION ?></strong>
        </p>

        <p>
            <?= NETCAT_MODULE_NETSHOP_ORDER_MERGE_BASE ?>
            <? foreach ($orders as $i => $order): ?>
                <input type="hidden" name="order_ids[]" value="<?= $order->get_id() ?>">
                <label style="display: block">
                    <input type="radio" name="base_order_id" value="<?= $order->get_id() ?>" <?= (!$i ? ' checked' : '') ?>>
                    <?= NETCAT_MODULE_NETSHOP_ORDERS_NUMBER ?>
                    <?= $order->get_id() ?>
                    (<?= date(NETCAT_MODULE_NETSHOP_DATETIME_FORMAT, strtotime($order->get('Created'))) ?>)
                </label>
            <? endforeach; ?>
        </p>

        <p>
            <?= NETCAT_MODULE_NETSHOP_ORDER_MERGE_SET_STATUS ?><br>
            <select name="merged_orders_status">
                <option value=""><?= NETCAT_MODULE_NETSHOP_ORDER_MERGE_NO_STATUS_CHANGE ?></option>
                <option value="0"><?= NETCAT_MODULE_NETSHOP_ORDER_NEW ?></option>
                <? foreach ($order_statuses as $status_id => $status_description): ?>
                    <option value="<?= $status_id ?>"><?= $status_description ?></option>
                <? endforeach; ?>
            </select>
        </p>

        </form>
    </div>
    <div class="nc-modal-dialog-footer">
        <button data-action="submit"><?= NETCAT_MODULE_NETSHOP_ORDER_MERGE_SUBMIT ?></button>
        <button data-action="close"><?= NETCAT_MODULE_NETSHOP_ORDER_MERGE_CANCEL ?></button>
    </div>
    <script>
        nc.ui.modal_dialog.get_current_dialog().set_options({
            on_submit_response: function(response) {
                // ответ должен придти в виде json, но '&' могут быть заменены на '&amp;', если ответ загружен в iframe
                response = $nc.parseJSON(response);

                // закрываем этот диалог
                this.destroy();

                // открываем новый диалог
                if (response && response.order_edit_dialog_url) {
                    nc.load_dialog(response.order_edit_dialog_url.replace(/&amp;/g, '&'));
                }
            }
        });
    </script>
</div>
