<?php

require '../../no_header.inc.php';

$nc_core = nc_core::get_object();
$site_id = $nc_core->input->fetch_get_post('site_id') ?:
           $nc_core->catalogue->get_current('Catalogue_ID');

$condition_data_folder = nc_module_path('netshop') . 'admin/condition/data';

?>
<div class="nc-modal-dialog nc-netshop-condition-dialog">
    <style scoped>
        .nc-netshop-condition-dialog .nc-modal-dialog-body {
            overflow: hidden !important;
        }

        .nc-netshop-condition-dialog-tree-column, .nc-netshop-condition-dialog-list-column {
            overflow: auto;
            float: left;
        }
        
        .nc-netshop-condition-dialog-tree-column {
            overflow-x: hidden;
            width: 29%;
            margin-right: 1%;
            padding: 30px 15px 30px 0;
        }
        
        .nc-netshop-condition-dialog-list-column {
            width: 70%;
            padding: 30px 30px 30px 15px;
        }

        .nc-netshop-condition-dialog-list-column table {
            margin-bottom: 30px;
        }
        
        .nc-netshop-condition-dialog-tree {
            margin: -5px 0 0 0;
            padding: 0 0 0 16px;
        }
        
        .nc-netshop-condition-dialog-tree a, .nc-netshop-condition-dialog-tree a:hover {
            color: #333333 !important;
        }
        
        .nc-netshop-condition-dialog-list-column .item {
            cursor: pointer;
        }
    </style>
    
    <div class="nc-modal-dialog-header"><h2><?= NETCAT_MODULE_NETSHOP_CONDITION_USER_SELECTION ?></h2></div>
    <div class="nc-modal-dialog-body nc-padding-0">
        <!-- "menu_left_block" class is required for the dynamicTree :( -->
        <div class="nc--fill nc-netshop-condition-dialog-tree-column menu_left_block">
            <ul class="nc-netshop-condition-dialog-tree" id="nc_netshop_condition_editor_user_selection_dialog_tree"></ul>
        </div>
        <div class="nc--fill nc-netshop-condition-dialog-list-column">
        </div>
    </div>
    <div class="nc-modal-dialog-footer">
        <button data-action="close"><?= NETCAT_MODULE_NETSHOP_CONDITION_DIALOG_CANCEL_BUTTON ?></button>
    </div>


    <script>
        (function() {
            var dialog = nc.ui.modal_dialog.get_current_dialog();

            function selectItem() {
                // This is a handler for the cell in the object list table.
                // The cell has "data-user-id" attribute,
                // and its contents is the name (caption) of the object.
                // Target parameter fields are set by the following dialog.data entries:
                // "targetInput", "targetCaption"
                var el = $nc(this);

                dialog.get_option("condition_dialog_target_input").val(el.data('userId'));
                dialog.get_option("condition_dialog_target_caption").html(el.html());
                dialog.close();
            }


            // --- tree initialization ---
            function initTree() {
                var treeId = 'nc_netshop_condition_editor_user_selection_dialog_tree',
                    treeUrl = '<?= "$condition_data_folder/json/user_group_tree.php?site_id=$site_id&node=" ?>',
                    tree = new window.top.dynamicTree(treeId, treeId, treeUrl),
                    listColumn = dialog.find(".nc-netshop-condition-dialog-list-column").html('');

                tree.actions = {
                    selectNode: function(id) {
                        $nc.get('<?= "$condition_data_folder/html/user_list.php" ?>',
                              { group_id: id },
                              function(response) {
                                  listColumn.html(response)
                                      .find(".item").click(selectItem);
                              });
                        tree.selectNode('usergroup-' + id);
                        return false;
                    }
                };

                /** @todo FIX THIS (see dynamicTree constructor // buttons when scrollTop > 0) */
                window.top.$nc('body').css('overflowY', 'hidden').scrollTop(0).scrollLeft(0);
            }

            // load the "dynamic tree" script and initialize it
            if (!window.top.dynamicTree) {
                nc.load_script('<?= $nc_core->ADMIN_PATH ?>js/tree_frame.js').success(initTree);
            }
            else {
                initTree();
            }

        })();
    </script>

</div>
