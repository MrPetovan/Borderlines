$(document).ready(function() {
  /*$("map > area").tooltip({
    track: true,
    showURL: false,
    showBody: " | ",
    fade: 250
  });*/

  $('div.accordion').accordion({
    header: "h4",
    autoHeight: false
  });

  $("map > area").cluetip({
    local:true,
    cursor: 'help',
    tracking: true,
    /*cluetipClass: 'jtip',*/
    topOffset: 10,
    leftOffset: 40,
    arrows: true,
    showTitle: false
  });



  //Accordion
  $('.texte_cache').hide();
  $('.texte_chapo').append('&nbsp;<a href="#" class="readmore">Lire la suite</a>');
  $('.texte_chapo').children('a').click(function(e) {
    $(this).parent().next('.texte_cache').show();
    $(this).remove();
    return false;
  });
});