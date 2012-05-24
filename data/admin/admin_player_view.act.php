<?php

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_player_resource_history':
        $player = Player::instance( getValue('player_id') );
        if( $player->id ) {
          $flag_set_player_resource_history = $player->set_player_resource_history(
            getValue('resource_id'),
            getValue('datetime'),
            getValue('delta'),
            getValue('reason')
          );
        }
        break;
      case 'del_player_resource_history':
        $player = Player::instance( getValue('player_id') );
        if( $player->id ) {
          $flag_del_player_resource_history = $matchup->del_player_resource_history(
            getValue('resource_id'),
            getValue('datetime')
          );
        }
        break;
      case 'set_player_spygame_value':
        $player = Player::instance( getValue('player_id') );
        if( $player->id ) {
          $flag_set_player_spygame_value = $player->set_player_spygame_value(
            getValue('value_guid'),
            getValue('datetime'),
            getValue('real_value'),
            getValue('masked_value')
          );
        }
        break;
      case 'del_player_spygame_value':
        $player = Player::instance( getValue('player_id') );
        if( $player->id ) {
          $flag_del_player_spygame_value = $matchup->del_player_spygame_value(
            getValue('value_guid'),
            getValue('datetime')
          );
        }
        break;
      default:
        break;
    }
  }
