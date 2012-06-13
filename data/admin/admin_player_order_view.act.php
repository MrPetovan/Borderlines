<?php

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_player_resource_history':
        $player_order = Player_Order::instance( getValue('player_order_id') );
        if( $player_order->id ) {
          $flag_set_player_resource_history = $player_order->set_player_resource_history(
            getValue('player_id'),
            getValue('resource_id'),
            getValue('datetime'),
            getValue('delta'),
            getValue('reason')
          );
        }
        break;
      case 'del_player_resource_history':
        $player_order = Player_Order::instance( getValue('player_order_id') );
        if( $player_order->id ) {
          $flag_del_player_resource_history = $matchup->del_player_resource_history(
            getValue('player_id'),
            getValue('resource_id')
          );
        }
        break;
      default:
        break;
    }
  }
