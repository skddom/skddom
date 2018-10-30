<?php

/**
 * This file is meant to be included.
 * Set following variables prior to this file inclusion:
 *   - $mail_to
 *   - $mail_subject
 *   - $mail_body
 *   - $catalogue_id   [used to determine sender settings]
 */

if (!class_exists('nc_core')) { die; }

if (!$mail_subject) {
    $mail_subject = "<span class='no-subject'>" . NETCAT_MODULE_NETSHOP_MAILER_MESSAGE_PREVIEW_NO_SUBJECT . "</span>";
}

// стандартные стили не подключаются чтобы не нарушить вывод тела письма
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=<?= nc_core()->NC_CHARSET ?>'>
    <title><?=NETCAT_MODULE_NETSHOP_MAILER_MESSAGE_PREVIEW ?></title>
    <style>
        body {
            background: #EEE;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }
        table.nc-netshop-mailer-preview {
            width: 100%;
            border-spacing: 5px;
            padding: 20px;
        }
        table.nc-netshop-mailer-preview td {
            padding: 0;
        }
        table.nc-netshop-mailer-preview td.label {
            text-align: right;
            padding: 0 10px;
            white-space: nowrap;
            width: 1%;
        }
        table.nc-netshop-mailer-preview td.value {
            border: 1px solid #DADADA;
            background-color: white;
            padding: 5px 10px;
        }
        table.nc-netshop-mailer-preview td.value.mail-subject > .no-subject {
            color: red;
        }
        table.nc-netshop-mailer-preview td.body {
            height: 100%;
            border: 1px solid #DADADA;
            background-color: white;
            vertical-align: top;
        }
        table.nc-netshop-mailer-preview td.body > .body-wrapper {
            height: 100px;
            padding: 10px;
            overflow-y: scroll;
        }
        tr.buttons #send_button, tr.buttons #close_button {
            display: inline-block;
            color: white;
            font: 12px "Helvetica Neue", Helvetica, Arial, sans-serif;
            padding: 8px 18px;
            background-color: #898989;
            cursor: pointer;
        }

        tr.buttons #close_button {
            float: right;
            background-color: #1A87C2;
        }
    </style>
    <?=nc_jquery(); ?>
    <script>
        (function($) {
            $(function() {
                var table = $('table.nc-netshop-mailer-preview'),
                    bodyCell = $('table.nc-netshop-mailer-preview td.body'),
                    bodyWrapper = $('table.nc-netshop-mailer-preview .body .body-wrapper');

                function onResize() {
                    bodyWrapper.height(100);
                    table.height($(window).height() - ((/Firefox/i).test(window.navigator.userAgent) ? 40 : 0));
                    bodyWrapper.height(bodyCell.height() - 20);
                }

                $(window).resize(onResize);
                onResize();

                $('#send_button').click(function() {
                    var email = window.prompt('<?=addcslashes(NETCAT_MODULE_NETSHOP_MAILER_MESSAGE_PREVIEW_SEND_PROMPT, "'\r\n") ?>', '');
                    if (email) {
                        $.ajax({
                            url: '<?=nc_module_path('netshop') ?>admin/mailer/send_preview.php',
                            method: 'POST',
                            data: {
                                catalogue_id: $('input[name=catalogue_id]').val(),
                                mail_to: email,
                                mail_subject: $('.nc-netshop-mailer-preview .header > .mail-subject').html(),
                                mail_body: $('.nc-netshop-mailer-preview td.body > div.body-wrapper').html()
                            },
                            success: function(result) {
                                alert(result);
                            }
                        });
                    }
                });
                $('#close_button').click(function() { window.close(); });
            })
        })(window.$ || window.$nc || window.jQuery);
    </script>
</head>
<body>

    <table class='nc-netshop-mailer-preview'>
        <tr class='header'>
            <td class='label'><?=NETCAT_MODULE_NETSHOP_MAILER_MESSAGE_PREVIEW_TO ?></td>
            <td class='value mail-to'><?=$mail_to ?></td>
        </tr>
        <tr class='header'>
            <td class='label'><?=NETCAT_MODULE_NETSHOP_MAILER_MESSAGE_PREVIEW_SUBJECT ?></td>
            <td class='value mail-subject'><?=$mail_subject ?></td>
        </tr>
        <tr>
            <td colspan='2' class='body'><div class='body-wrapper'><?=$mail_body ?></div></td>
        </tr>
        <tr class='buttons'>
            <td colspan='2'>
                <a id='send_button'>Отправить копию...</a>
                <a id='close_button'>Закрыть</a>
            </td>
        </tr>
    </table>
    <input type='hidden' name='catalogue_id' value='<?=$catalogue_id ?>' />

</body>
</html>