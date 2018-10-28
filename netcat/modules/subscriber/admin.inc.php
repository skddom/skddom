<?php
/* $Id: admin.inc.php 7681 2012-07-16 15:09:22Z ewind $ */

function SearchSubscriberForm() {
    global $ROOT_FOLDER, $INCLUDE_FOLDER, $admin_mode, $DOMAIN_NAME;
    global $systemTableID, $systemMessageID, $systemTableName, $UI_CONFIG;
?>

    <form method='get' action='admin.php' id='SearchSubscriberForm'>
        <fieldset>
            <legend><?=NETCAT_MODULE_SUBSCRIBE_ADM_GETUSERS ?></legend>
            <table border='0' cellpadding='6' cellspacing='0' width='100%'>
                <tr><td>
                <nobr><font color='gray'><?=NETCAT_MODULE_SUBSCRIBE_ADM_USERID
?>: <input type='text' name='UserID' size='5' maxlength='10' value=''></nobr>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <nobr><?=NETCAT_MODULE_SUBSCRIBE_ADM_CLASSID
?>: <input type='text' name='SubClassID' size='5' maxlength='10' value=''></nobr>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <nobr><?=NETCAT_MODULE_SUBSCRIBE_ADM_STATUS
?>: <input checked id='chk1' type='radio' name='Checked' value=''> <label for='chk1'><?=NETCAT_MODULE_SUBSCRIBE_ADM_ALLUSERS
?></label>
                            <input id='chk2' type='radio' name='Checked' value='1'> <label for='chk2'><?=NETCAT_MODULE_SUBSCRIBE_ADM_TURNEDON
?></label>
                        <input id='chk3' type='radio' name='Checked' value='2'> <label for='chk3'><?=NETCAT_MODULE_SUBSCRIBE_ADM_TURNEDOFF ?></label></nobr>
                    </td></tr>
                    <tr><td><div align='right'>
                                <?php
                                $UI_CONFIG->actionButtons[] = array("id" => "submit",
                                        "caption" => NETCAT_MODULE_SUBSCRIBE_BUT_GETIT,
                                        "action" => "mainView.submitIframeForm('SearchSubscriberForm')");
                                ?>
                                <input type='submit' class='hidden'>
                            </div></td></tr>
                    </table></fieldset>
                    <input type='hidden' name='phase' value='2'>
                    </form>

                    <?php
                            }

                            function ListSubscriberPages($totRows, $queryStr) {
                                global $curPos;

                                $range = 15;
                                $maxRows = 20;

                                $curPos = (int) $curPos;
                                if ($curPos < 0) $curPos = 0;

                                if (!$maxRows || !$totRows) return;

                                $page_count = ceil($totRows / $maxRows);
                                $half_range = ceil($range / 2);
                                $cur_page = ceil($curPos / $maxRows) + 1;

                                if ($page_count < 2) return;

                                $maybe_from = $cur_page - $half_range;
                                $maybe_to = $cur_page + $half_range;

                                if ($maybe_from < 0) {
                                    $maybe_to = $maybe_to - $maybe_from;
                                    $maybe_from = 0;

                                    if ($maybe_to > $page_count)
                                            $maybe_to = $page_count;
                                }

                                if ($maybe_to > $page_count) {
                                    $maybe_from = $page_count - $range;
                                    $maybe_to = $page_count;

                                    if ($maybe_from < 0) $maybe_from = 0;
                                }

                                echo "";

                                for ($i = $maybe_from; $i < $maybe_to; $i++) {
                                    $page_number = $i + 1;
                                    $page_from = $i * $maxRows;
                                    $page_to = $page_from + $maxRows;
                                    $url = "?phase=2".$queryStr."&curPos=".$page_from;

                                    if ($curPos == $page_from)
                                            echo "<b>$page_number</b>"; else
                                            echo "<a href=$url>$page_number</a>";

                                    if ($i != ($maybe_to - 1)) echo " | ";
                                }
                                echo '</font>';
                            }

                            function SearchSubscriberResult() {
                                global $ROOT_FOLDER, $INCLUDE_FOLDER;
                                global $db, $SubClassID, $Checked;
                                global $admin_mode, $curPos;
                                global $AUTHORIZE_BY, $DOMAIN_NAME, $SUB_FOLDER, $ADMIN_PATH, $ADMIN_TEMPLATE;

                                $curPos += 0;

                                $select = "SELECT COUNT(*) FROM Subscriber WHERE 1";
                                if ($UserID) $select .= " AND User_ID=".$UserID;
                                if ($SubClassID)
                                        $select .= " AND Sub_Class_ID=".$SubClassID;
                                if ($Checked != "")
                                        $select .= " AND Status='".$Checked."'";

                                $totRows = $db->get_var($select);
                                ListSubscriberPages($totRows, ($curPos ? "?curPos=".$curPos : ""));


                                $select = "SELECT
					a.Subscriber_ID,
					a.User_ID,
					b.".$AUTHORIZE_BY.",
					a.Sub_Class_ID,
					c.Sub_Class_Name,
					d.Subdivision_Name,
					a.Status,
					d.Subdivision_ID,
					d.Catalogue_ID
				FROM
					Subscriber as a,
					User as b,
					Sub_Class as c,
					Subdivision as d
				WHERE
					a.User_ID=b.User_ID
				AND
					a.Sub_Class_ID=c.Sub_Class_ID
				AND
					c.Subdivision_ID=d.Subdivision_ID";
                                if ($UserID)
                                        $select .= " AND a.User_ID=".$UserID;
                                if ($SubClassID)
                                        $select .= " AND a.Sub_Class_ID=".$SubClassID;
                                if ($Checked != "")
                                        $select .= " AND a.Status=".$Checked;
                                $select .= " LIMIT $curPos,20";


                                if ($Result = $db->get_results($select, ARRAY_N)) {
                    ?>

                                    <form method='post' action='admin.php' id='deleteSubscriptionsForm'>
                                        <table border='0' cellpadding='0' cellspacing='0' width='100%'><tr><td >

                                                    <table class='admin_table' width='100%'>
                                                        <tr>
                                                            <td ><b>ID</td>
                                                                        <td  width='40%'><b><?=NETCAT_MODULE_SUBSCRIBE_ADM_CLASSINSECTION ?></td>
                                                                    <td ><b><?=NETCAT_MODULE_SUBSCRIBE_ADM_USER ?></td>
                                                                                <td  align='center'><b><?=NETCAT_MODULE_SUBSCRIBE_ADM_STATUS ?></td>
                                                                                            <td  align='center'><div class='icons icon_delete' title='<?=NETCAT_MODULE_SUBSCRIBE_ADM_DELETE ?>'></div></td>
                                                                                            </tr>
                                                                                            <?
                                                                                            foreach ($Result as $Array) {
                                                                                                print '<tr>';
                                                                                                print "<td><b>".(!$Array[6] ? "<font color=cccccc>" : "").$Array[0]."</td>";
                                                                                                print "<td><a href=\"" . nc_get_scheme() . "://{$DOMAIN_NAME}{$ADMIN_PATH}subdivision/SubClass.php?phase=3&SubdivisionID={$Array[7]}&CatalogueID={$Array[8]}&SubClassID={$Array[3]}\">" . (!$Array[6] ? "<font color='cccccc'>" : '') . $Array[3] . ' (' . $Array[4] . ')</a><br></b>' . NETCAT_MODULE_SUBSCRIBE_ADM_SECTION . " : <a href=\"" . nc_get_scheme() . "://{$DOMAIN_NAME}{$ADMIN_PATH}subdivision/index.php?phase=5&SubdivisionID={$Array[7]}\">" . (!$Array[6] ? "<font color='cccccc'>" : '') . $Array[7] . ' (' . $Array[5] . ')</a></td>';

                                                                                                print "<td><a href=\"" . nc_get_scheme() . "://{$DOMAIN_NAME}{$ADMIN_PATH}user/index.php?phase=4&UserID={$Array[1]}\">" . (!$Array[6] ? "<font color='cccccc'>" : '') . $Array[1];
                                                                                                if ($AUTHORIZE_BY != 'User_ID')
                                                                                                        print " (".$Array[2].")";
                                                                                                print '</td>';

                                                                                                print "<td align=center><a href=admin.php?phase=3&SubscriberID=".$Array[0].">".(!$Array[6] ? "<font color=".NETCAT_MODULE_SUBSCRIBE_ADM_TURNOFFCLR.">".NETCAT_MODULE_SUBSCRIBE_ADM_TURNON : NETCAT_MODULE_SUBSCRIBE_ADM_TURNOFF)."</a></td>";
                                                                                                print "<td align='center'><input type='checkbox' name=\"Delete".$Array[0]."\" value=".$Array[0]."></td>";
                                                                                                print '</tr>';
                                                                                            }

                                                                                            global $UI_CONFIG;
                                                                                            $UI_CONFIG->actionButtons[] = array("id" => "submit",
                                                                                                    "caption" => NETCAT_MODULE_SUBSCRIBE_ADM_SAVE,
                                                                                                    "action" => "mainView.submitIframeForm('deleteSubscriptionsForm')"
                                                                                            );
                                                                                            ?>
                                                                                            <input type='submit' class='hidden'>
                                                                                            </table>
                                                                                            </td></tr></table><br>
                                                                                            <input type='hidden' name='phase' value='4'>
                                                                                            </form>
                                                                                            <?
                                                                                        } else {
                                                                                            echo NETCAT_MODULE_SUBSCRIBE_MSG_NOSUBSCRIBER;
                                                                                        }
                                                                                    }

                                                                                    function ToggleSubscriber($SubscriberID) {
                                                                                        global $db;

                                                                                        $res = $db->query("UPDATE Subscriber SET Status=(1-Status) WHERE Subscriber_ID=".intval($SubscriberID));

                                                                                        return ($res);
                                                                                    }

                                                                                    function DeleteSubscribers() {
                                                                                        global $db;

                                                                                        while (list($key, $val) = each($_POST)) {
                                                                                            if ($key == 'Submit')
                                                                                                    continue;
                                                                                            if ($key == 'phase')
                                                                                                    continue;

                                                                                            $delete = "delete from Subscriber where Subscriber_ID=".intval($val);

                                                                                            $res = $db->query($delete);
                                                                                        }
                                                                                    }
                                                                                            ?>