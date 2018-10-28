<script>
(function($nc){
    // $nc('.wrap_block').css({'margin-left':17});

    var store_link = "//<?= NC_STORE_DOMAIN ?>?host=<?=$nc_core->HTTP_HOST ?>&is_trial=<?=$nc_core->is_trial ?><?=$tab == 'my' ? '#:my:' : '' ?>";
    $nc('#mainViewContent').hide(); // .after('<i class="nc-icon nc--loading"></i>')
    $nc('#mainViewIframe')
        .on('load', function(){
            $nc('#mainViewContent').fadeIn();
        })
        .attr('src', store_link);

})(nc.root.$);
</script>

<!-- <iframe src='' style='border:none; position:fixed; top:-4px; left:0; width:100%; height:100%; display:none'> -->

