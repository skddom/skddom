<?
/* $Id: redirect.inc.php 7302 2012-06-25 21:12:35Z alive $ */

/**
 * Функция отображает список переадрасаций
 *
 * @return 0
 */
function RedirectList() {
    global $nc_core, $db, $UI_CONFIG, $ADMIN_TEMPLATE;

    $db->last_error = '';
    $Result = $db->get_results("SELECT `Redirect_ID`,`OldURL`,`NewURL`, `Header` FROM `Redirect` ORDER BY `Redirect_ID`", ARRAY_N);

    // на случай, если поля не существует
    if (strstr($db->last_error, 'Header')) {
        $db->query("ALTER TABLE `Redirect` ADD `Header` INT(3)  NULL DEFAULT '301';");
        return RedirectList();
    }

    if ($countClassif = $db->num_rows) {
        ?>
        <form method=post action=redirect.php>

            <table class='nc-table nc--striped' width='100%'>
                <tr>
                    <th >ID</th>
                    <th width=35%><?= TOOLS_REDIRECT_OLDURL ?></th>
                    <th width=35%><?= TOOLS_REDIRECT_NEWURL ?></th>
                    <th class='nc-text-center'><?= TOOLS_REDIRECT_HEADER ?></th>
                    <th class='nc-text-center'><?= TOOLS_REDIRECT_SETTINGS ?></th>
                    <th class='nc-text-center'><div class='icons icon_delete'  title='<?= CONTROL_CONTENT_CATALOUGE_FUNCS_SHOWCATALOGUELIST_DELETE ?>'></div></th>
                </tr>
                <?
                foreach ($Result as $Array) {
                    print "<tr>";
                    print "<td >" . $Array[0] . "</td>\n";
                    print "<td>" . $Array[1] . "</a></td>";
                    print "<td>" . $Array[2] . "</td>";
                    print "<td class='nc-text-center'>" . ($Array[3] ? $Array[3] : 301) . "</td>";
                    print "<td class='nc-text-center'><a href=redirect.php?phase=1&RedirectID=" . $Array[0] . "><div class='icons icon_settings' title='" . TOOLS_REDIRECT_CHANGEINFO . "'></div></a></td>";
                    print "<td class='nc-text-center'>" . nc_admin_checkbox_simple("Delete" . $Array[0], $Array[0]) . "</td>";
                    print "</tr>";
                }

                print "</table><br>";
            } else {
                nc_print_status(TOOLS_REDIRECT_NONE, 'info');
            }

            if ($countClassif) {
                $UI_CONFIG->actionButtons[] = array(
                    "id" => "submit",
                    "caption" => NETCAT_ADMIN_DELETE_SELECTED,
                    "action" => "mainView.submitIframeForm()",
                    "align" => "right",
                    "red_border" => true,
                );

                print "<input type=hidden name=phase value=3>";
                print "<input type='submit' class='hidden'>";
                print $nc_core->token->get_input();
                print "</form>";
            }

            $UI_CONFIG->actionButtons[] = array("id" => "add",
                    "caption" => TOOLS_REDIRECT_ADD,
                    "location" => "redirect.add",
                    "align" => "left");

            return 0;
        }

###############################################################################

        function RedirectForm($RedirectID) {
            global $nc_core, $db, $UI_CONFIG;

            $RedirectID = intval($RedirectID);

            $OldURL = $db->escape($_POST['OldURL']);
            $NewURL = $db->escape($_POST['NewURL']);
            $HeaderCode = intval($_POST['HeaderCode']);

            if ($RedirectID) {
                list ($OldURL, $NewURL, $HeaderCode) = $db->get_row("SELECT `OldURL`,`NewURL`, `Header` FROM `Redirect` WHERE `Redirect_ID`='" . $RedirectID . "'", ARRAY_N);
            }

            if ($HeaderCode != 301 && $HeaderCode != 302)
                $HeaderCode = 301;

            echo "
    <form method='post' action='redirect.php'>
       <font color='gray'>
  " . TOOLS_REDIRECT_OLDLINK . ":<br/>" . nc_admin_input_simple('OldURL', $OldURL, 70, '', "maxlength='255'") . "<br/><br/>
  " . TOOLS_REDIRECT_NEWLINK . ":<br/>" . nc_admin_input_simple('NewURL', $NewURL, 70, '', "maxlength='255'") . "<br/><br/>
  " . TOOLS_REDIRECT_HEADERSEND . ":<br/>" . nc_admin_select_simple('', 'HeaderCode', array(301 => 301, 302 => 302), $HeaderCode) . "
  <hr size='1' color='cccccc'> ";


            if (!$RedirectID) {

                $UI_CONFIG->actionButtons[] = array("id" => "submit",
                        "caption" => TOOLS_REDIRECT_ADDONLY,
                        "action" => "mainView.submitIframeForm()"
                );
            } else {
                echo "
      <input type='hidden' name='RedirectID' value='" . $RedirectID . "' />";

                $UI_CONFIG->actionButtons[] = array("id" => "submit",
                        "caption" => CONTROL_CONTENT_CATALOUGE_FUNCS_CATALOGUEFORM_SAVE,
                        "action" => "mainView.submitIframeForm()"
                );
            }
            echo $nc_core->token->get_input();
            echo "<input type='hidden' name='phase' value='2'>";
            echo "<input type='submit' class='hidden' /> </form>";

            return 0;
        }

###############################################################################

        function RedirectCompleted() {
            global $db;

            $OldURL = $db->escape($_POST['OldURL']);
            $NewURL = $db->escape($_POST['NewURL']);
            $HeaderCode = intval($_POST['HeaderCode']);
            $RedirectID = intval($_POST['RedirectID']);

            if ($HeaderCode != 301 && $HeaderCode != 302)
                $HeaderCode = 301;

            if (!$OldURL || !$NewURL) {
                print TOOLS_REDIRECT_CANTBEEMPTY . "<br />";
                RedirectForm($RedirectID);
                $Result = 0;
            } elseif (!$RedirectID) {
                $Result = $db->query("INSERT INTO  `Redirect` (`OldURL`,`NewURL`, `Header`) VALUES ('" . $OldURL . "','" . $NewURL . "', '" . $HeaderCode . "')");
            } else {
                $Result = $db->query("UPDATE `Redirect` SET `OldURL`='" . $OldURL . "', `NewURL`='" . $NewURL . "', `Header` = '" . $HeaderCode . "' WHERE `Redirect_ID`='" . $RedirectID . "'");
            }

            return ($Result);
        }

###############################################################################

        function DeleteRedirect($RedirectID) {
            global $db;
            $RedirectID = intval($RedirectID);
            $Delete = "delete from Redirect where Redirect_ID='" . $RedirectID . "'";
            $DeleteResult = $db->query($Delete);
        }