<div class='nc-progress'>
    <div id="progress" class="nc-progress-bar"></div>
</div>

<div class="nc--clearfix">
    <? foreach ($steps as $key => $name): ?>
        <span class="nc-btn nc--lighten nc--left" style='margin:0 1px 1px 0; cursor:default; opacity:1' id="step_<?=$key?>">
            <i class="nc-icon" id="step_icon_<?=$key?>"></i>
            <?=$name ?>
        </span>
    <? endforeach ?>

    <? if ($mode == 'export'): ?>
        <a id="download_btn" class="nc-btn nc--lighten nc--left"><i class="nc-icon nc--download nc--dark"></i> <?=TOOLS_DOWNLOAD ?></a>
    <? endif ?>
</div>

<div id="error" class='nc-alert nc--red nc--hide'></div>

<script>
var export_id    = '<?=$export_id ?>';
var steps_no     = 0;
var total_steps  = <?=count($steps) ?>;

function update_progress() {
    steps_no++;
    var percent   = Math.ceil((steps_no/total_steps)*100);
    var $progress = nc('#progress');
    if (percent == 100) {
        $progress.parent().removeClass('nc--loading');
    }
    else {
        $progress.parent().addClass('nc--loading');
    }
    $progress.css({width:percent+'%'});
}

function process_step(step, cross_data) {
    nc('#step_icon_' + step).addClass('nc--loading');
    cross_data = cross_data || {};

    var request = nc.$.ajax({
        url:      '<?=$ADMIN_PATH ?>backup.php?mode=<?=$mode ?>&type=<?=$type ?>&id=<?=$id ?>&step=' + step + (export_id ? '&export_id=' + export_id : ''),
        cache:    false,
        type:     'POST',
        data:     cross_data,
        dataType: 'JSON'
    });

    request.success(function(data){
        update_progress();
        nc('#step_' + step).toggleClass('nc--lighten nc--green');
        nc('#step_icon_' + step).toggleClass('nc--loading nc--status-success nc--white');

        if (nc.key_exists('export_id', data)) {
            export_id = data.export_id;
        }

        if (nc.key_exists('next_step', data)) {
            return process_step(data.next_step, (nc.key_exists('cross_data', data) ? data.cross_data : {}));
        }

        if (nc.key_exists('file', data)) {
            nc('#result').fadeIn();
            nc('#download_btn').attr('href',data.file).toggleClass('nc--lighten nc--blue').find('i').toggleClass('nc--dark nc--white');
        }
    });

    request.fail(function(xhr, message){
        nc('#progress').parent().toggleClass('nc--loading nc--striped nc--red');
        nc('#step_' + step).toggleClass('nc--loading nc--lighten nc--red');
        nc('#step_icon_' + step).toggleClass('nc--loading nc--status-error nc--white');
        nc('#error').html(xhr.responseText).fadeIn();
    });
}

process_step('<?=$step ?>', <?=json_encode((array)$cross_data) ?>);

</script>