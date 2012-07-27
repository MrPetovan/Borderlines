<?php
  $resource = Resource::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_player_resource_history':
        if( $resource->id ) {
          $flag_set_player_resource_history = $resource->set_player_resource_history(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('player_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('datetime')) == ''?null:$value,
            ($value = getValue('delta')) == ''?null:$value,
            getValue('reason'),
            ($value = getValue('player_order_id')) == ''?null:$value
          );
          if( ! $flag_set_player_resource_history ) {
            Page::add_message( '$resource->set_player_resource_history : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_player_resource_history':
        if( $resource->id ) {
          $flag_del_player_resource_history = $resource->del_player_resource_history(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('player_id')) == ''?null:$value,
            ($value = getValue('player_order_id')) == ''?null:$value
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
