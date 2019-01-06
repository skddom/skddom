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
      }
    })
    return false;
  })
  $(document).ready(function () {
    if ($('.skdmans-container-fixed').length) {
      c = $('.skdmans-container-fixed:last').find('.skdmans-item-container').last();
      $('.skdmans-container-fixed:last').height(c.position().top + c.height());
    }
  })
})(jQuery)