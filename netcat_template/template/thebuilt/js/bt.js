(function ($) {
  $(document).on('click', '.vote-skdmans', function () {
    if ($(this).hasClass('voted')) {
      return false;
    }
    var self = $(this);
    $.ajax({
      url: '/netcat/modules/stages/ajax.php?vote-skdmans=' + self.data('myid'),
      tyep: 'json',
      success: function (d) {
        self.addClass('voted');
        self.html(d.votes);
        console.log(d);
      }
    })
    return false;
  })
})(jQuery)