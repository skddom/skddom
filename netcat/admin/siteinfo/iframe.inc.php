<script src="audit.js"></script>
<script>
    /*
      group_name => array(  array(name,), ...)
     */
    //[NETCAT_MODULE_AUDITOR_GROUP_LINKS: 'yandex_links'],
    var data_to_fetch =
        {
        'NETCAT_MODULE_AUDITOR_GROUP_CY':        [['yandex_cy']/*,['google_pr']*/],
        'NETCAT_MODULE_AUDITOR_GROUP_INDEXED':   [['nigmaru_pages'],['rambler_pages'],['yandex_pages'],['google_pages'],['msn_pages'],['yahoo_pages']],
        'NETCAT_MODULE_AUDITOR_GROUP_LINKS':     [['google_links'],/*['msn_links'],*/['yahoo_links']],
        'NETCAT_MODULE_AUDITOR_GROUP_CATALOGUE': [['dmoz_catalogue'],['rambler_catalogue'],['mailru_catalogue'],['yandex_catalogue']],
        'NETCAT_MODULE_AUDITOR_GROUP_STAT':      [['hotlog_stat'],['liveinternet_stat'],['mailru_stat'],['rambler_stat'],['spylog_stat']]
    },

    group_labels =
        {
        'NETCAT_MODULE_AUDITOR_GROUP_CY':        '<?=NETCAT_MODULE_AUDITOR_GROUP_CY
?>',
        'NETCAT_MODULE_AUDITOR_GROUP_INDEXED':   '<?=NETCAT_MODULE_AUDITOR_GROUP_INDEXED
?>',
        'NETCAT_MODULE_AUDITOR_GROUP_LINKS':     '<?=NETCAT_MODULE_AUDITOR_GROUP_LINKS
?>',
        'NETCAT_MODULE_AUDITOR_GROUP_CATALOGUE': '<?=NETCAT_MODULE_AUDITOR_GROUP_CATALOGUE
?>',
        'NETCAT_MODULE_AUDITOR_GROUP_STAT':      '<?=NETCAT_MODULE_AUDITOR_GROUP_STAT
?>'
    }

    NETCAT_MODULE_AUDITOR_WRONG_URL = '<?=NETCAT_MODULE_AUDITOR_WRONG_URL
?>';

</script>

<div id=audit_results width=100%></div>

<iframe src="<?=$path
?>/index.php" id="audit_iframe" style='width:1px; height: 1px; border:none' frameborder="0"></iframe>
