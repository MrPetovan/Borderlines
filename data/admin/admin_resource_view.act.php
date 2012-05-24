<?php

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_player_resource_history':
        $resource = Resource::instance( getValue('resource_id') );
        if( $resource->id ) {
          $flag_set_player_resource_history = $resource->set_player_resource_history(
            getValue('player_id'),
            getValue('datetime'),
            getValue('delta'),
            getValue('reason')
          );
        }
        break;
      case 'del_player_resource_history':
        $resource = Resource::instance( getValue('resource_id') );
        if( $resource->id ) {
          $flag_del_player_resource_history = $matchup->del_player_resource_history(
            getValue('player_id'),
            getValue('datetime')
          );
        }
        break;
      default:
        break;
    }
  }
