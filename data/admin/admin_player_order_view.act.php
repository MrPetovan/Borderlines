<?php
  $player_order = Player_Order::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_player_resource_history':
        if( $player_order->id ) {
          $flag_set_player_resource_history = $player_order->set_player_resource_history(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('player_id')) == ''?null:$value,
            ($value = getValue('resource_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('datetime')) == ''?null:$value,
            ($value = getValue('delta')) == ''?null:$value,
            getValue('reason')
          );
          if( ! $flag_set_player_resource_history ) {
            Page::add_message( '$player_order->set_player_resource_history : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_player_resource_history':
        if( $player_order->id ) {
          $flag_del_player_resource_history = $player_order->del_player_resource_history(
            getValue('game_id'),
            getValue('player_id'),
            getValue('resource_id')
          );
        }
        break;
      default:
        break;
    }
  }
  
  // CUSTOM

  //Custom content

  // /CUSTOM
