function getURLParameter(name) {
    return decodeURIComponent((new RegExp('[?|&]' + name + '=' + '([^&;]+?)(&|#|;|$)').exec(location.search)||[,""])[1].replace(/\+/g, '%20'))||null;
}

$(document).ready(function() {
  $map = $('.map');
  $dialog = $('<div class="dialog">\n\
    <h4>\n\
      <a><img src="' + URL_ROOT + '/img/img_html/arrow_move.png" alt="" title="Move"/></a>\n\
      <span>Dialog</span>\n\
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>\n\
    </h4>\n\
    <div class="content">\n\
      <iframe style="border: 0"></iframe>\n\
    </div>\n\
  </div>').mousedown(function (e) {
    var inst = $(this).data('ui-draggable');
    inst._mouseStart(e);
    inst._trigger('start', e);
    inst._clear();
  });
  $dialog.find('.close').click(function(){
    $(this).parent().parent().remove();
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

  function showDialog(e) {
    $localdialog = $dialog
      .clone(true)
      .attr('id', 'dialog-' + $(this).attr('data-territory-id'))
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
        helper: 'ui-resizable-helper'
      });
    $localdialog
      .find('h4 > span').text($(this).attr('title'));
    $map.after($localdialog);

    $localdialog
      .css({'left': e.pageX - $map.offset().left, 'top': e.pageY - $map.offset().top})
      .show()
      .find('iframe')
      .attr('src', '?page=show_territory_ajax' + game_url + turn_url + '&id=' + $(this).attr('data-territory-id'));

    return false;
  }
  //$map.on('click','area', showDialog);
  $('#world_map').on('click','div.territory_summary h3', showDialog);

  $('.troops_list').sortable({
    connectWith: '.troops_list',
    placeholder: 'ui-state-highlight',
    items: '> li.moveable',
    cursor: 'move',
    revert: 0,
    receive: function(event, ui) {
      if(!$(this).hasClass('receive_from_' + ui.item.attr('data-from-territory-id'))) {
        ui.sender.sortable('cancel');
      }else {
        if( $(this).attr('id') == 'territory_' + ui.item.attr('data-from-territory-id') ) {
          ui.item.removeClass('moved');
        }else{
          ui.item.addClass('moved');
          console.log( 'receive ' + $(this).attr('id'), ui, event);

          if( ui.item.attr('data-planned-order-id') ) {
            $.get(URL_ROOT, {
              page: 'api',
              token: API_TOKEN_HASH,
              m: 'cancel_order',
              planned_order_id : ui.item.attr('data-planned-order-id'),
            });
          }

          $.get(URL_ROOT, {
            page: 'api',
            token: API_TOKEN_HASH,
            m: 'move_troops',
            from_territory_id: ui.item.attr('data-from-territory-id'),
            to_territory_id: $(event.target).attr('data-territory-id'),
            count: ui.item.attr('data-quantity')
          }, function(data) {
            ui.item.attr('data-planned-order-id', data.planned_order_id);
          },
          'json');
        }
      }
    },
    start: function(event,ui) {
      $(ui.item).show();
      $('.troops_list').parent().addClass('refuse_move');
      $('.receive_from_' + ui.item.attr('data-from-territory-id') ).parent().removeClass('refuse_move');
    },
    stop: function(event,ui) {
      $('.troops_list').parent().removeClass('refuse_move');
      //$('.troops_list').sortable('option', 'helper', 'original');
    },
  }).disableSelection();

  function update_split_troops_slider($self) {
    $('.troops.pull-left .value').val( $self.attr('data-quantity') - $('#slider-troops').slider('option', 'value') );
    $('.troops.pull-right .value').val( $('#slider-troops').slider('option', 'value') );
  }

  $('.troops_list li .split').on('click', function(event) {
    var $self = $(this).parent();

    $('#troops-modal').modal();
    $('.troops.pull-right .value').focus();
    $('#slider-troops').slider({
      min: 1,
      max: $self.attr('data-quantity') - 1,
      value: Math.round($self.attr('data-quantity') / 2),
      slide: function(event, ui) {
        update_split_troops_slider($self);
      }
    });
    update_split_troops_slider($self);
    $('.troops.pull-right .value').on('keyup', function(event) {
      console.log(event);
      var newval = $(event.target).val()
      if( newval > $('#slider-troops').slider('option', 'max' ) ) {
        newval = $('#slider-troops').slider('option', 'max' );
      }
      if( newval != $('#slider-troops').slider('option', 'value') ) {
        $('#slider-troops').slider('option', 'value', newval);

        update_split_troops_slider($self);
      }

      if( event.keyCode === 13 ) {
        $(this).off('keyup');
        $('#troops-modal button.btn-primary').click();
      }
    });
    $('#troops-modal button.btn-primary').on('click', function(event) {
      console.log(event);
      var $new_troops = $self.clone(true);
      $self.attr('data-quantity', $self.attr('data-quantity') - $('#slider-troops').slider('option', 'value'));
      $new_troops.attr('data-quantity', $('#slider-troops').slider('option', 'value'));

      $self.find('.value').text($self.attr('data-quantity'));
      $new_troops.find('.value').text($new_troops.attr('data-quantity'));

      $new_troops.appendTo($self.parent());

      $('#troops-modal').modal('hide');

      $(this).off('click');
    });
  })

  $('.troops_list li .cancel').on('click', function() {
    var $self = $(this).parent();
    var territory_id = $self.attr('data-from-territory-id');
    $self.removeClass('moved');

    $('[data-territory-id=' + territory_id + '] .troops_list').append($self);
    $.get(URL_ROOT, {
      page: 'api',
      token: API_TOKEN_HASH,
      m: 'cancel_order',
      planned_order_id : $self.attr('data-planned-order-id'),
    }, function(data) {
      $self.attr('data-planned-order-id', '');
    },
    'json');
  })

  $('.territory_summary').on('mouseenter', function() {
    $('.territory_summary').css('z-index', 0);
    $(this).css('z-index', 1);
  })

  $('tbody.archive:not(.current) tr:not(.title)').hide();
  $('.accordion').on('click', 'tr.title', function() {
    $(this).siblings().toggle();
  });
});