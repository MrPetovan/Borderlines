<?php
  $player_order = Player_Order::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_player_resource_history':
        if( $player_order->id ) {
          $flag_set_player_resource_history = $player_order->set_player_resource_history(
            getValue('game_id'),
            getValue('player_id'),
            getValue('resource_id'),
            getValue('turn'),
            getValue('datetime'),
            getValue('delta'),
            getValue('reason')
          );
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
