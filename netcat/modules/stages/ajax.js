jQuery(document).on('change', 'select[name=f_StageSection]', function () {
  updateStageReports(this);
});

function updateStageReports(el) {
  var id = jQuery(el).val();
  var rid = jQuery('select[name=f_StageReports]').val();
  jQuery.ajax({
    url: '/netcat/modules/stages/ajax.php?f_StageSection=' + id,
    type: 'json',
    success: function (d) {
      var s = '';
      jQuery.each(d, function (n, v) {
        s += '<option value=' + v.id + '>' + v.name + '</option>';
      })
      jQuery('select[name=f_StageReports]').html(s);
      jQuery('select[name=f_StageReports]').val(rid);
    }
  })
}
