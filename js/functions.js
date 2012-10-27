function getURLParameter(name) {
    return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null;
}


$(document).ready(function() {
  $map = $('map');
  $dialog = $('<div class="dialog">\n\
    <h4>\n\
      <a><img src="/img/img_html/arrow_move.png" alt="" title="Move"/></a>\n\
      <span>Dialog</span>\n\
      <a class="close">[X]</a>\n\
    </h4>\n\
    <div class="content">\n\
      <iframe style="border: 0"></iframe>\n\
    </div>\n\
  </div>').mousedown(function (e) {
    var inst = $(this).data('draggable');
    inst._mouseStart(e);
    inst._trigger('start', e);
    inst._clear();
  });
  $dialog.find('.close').click(function(){
    $(this).parent().parent().hide();
  })

  $('map area').each(function(){
    $localdialog = $dialog
      .clone(true)
      .attr('id', 'dialog-' + $(this).attr('territory'))
      .draggable({
        handle: 'h4',
        stack: '.dialog',
        addClasses: false,
        appendTo: 'body',
        containment: 'body'
      });
    $localdialog
      .find('.content')
      .resizable({
        helper: "ui-resizable-helper"
      });
    $localdialog
      .find('h4 span').text($(this).attr('title'));
    $map.after($localdialog);
  })

  if( game_id = getURLParameter('game_id') ) {
    game_url = '&game_id=' + game_id;
  }else {
    game_url = '';
  }
  if( turn = getURLParameter('turn') ) {
    turn_url = '&turn=' + turn;
  }else {
    turn_url = '';
  }
  $map.on('click','area',function(e) {
      $('#dialog-' + $(this).attr('territory'))
        .css({'left': e.pageX, 'top': e.pageY - $map.position().top})
        .show()
        .find('iframe')
        .attr('src', 'index.php?page=show_territory_ajax' + game_url + turn_url + '&id=' + $(this).attr('territory'));

    return false;
  });

  $('tbody.archive:not(.current) tr:not(.title)').hide();
  $('.accordion').on('click', 'tr.title', function() {
    $(this).siblings().toggle();
  });
});