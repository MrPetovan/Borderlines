<?php
  $resource = Resource::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_player_resource_history':
        if( $resource->id ) {
          $flag_set_player_resource_history = $resource->set_player_resource_history(
            getValue('game_id'),
            getValue('player_id'),
            getValue('turn'),
            getValue('datetime'),
            getValue('delta'),
            getValue('reason'),
            getValue('player_order_id')
          );
        }
        break;
      case 'del_player_resource_history':
        if( $resource->id ) {
          $flag_del_player_resource_history = $resource->del_player_resource_history(
            getValue('game_id'),
            getValue('player_id'),
            getValue('player_order_id')
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
