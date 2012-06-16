<?php

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_game_player':
        $player = Player::instance( getValue('player_id') );
        if( $player->id ) {
          $flag_set_game_player = $player->set_game_player(
            getValue('game_id')
          );
        }
        break;
      case 'del_game_player':
        $player = Player::instance( getValue('player_id') );
        if( $player->id ) {
          $flag_del_game_player = $player->del_game_player(
            getValue('game_id')
          );
        }
        break;
      case 'set_player_resource_history':
        $player = Player::instance( getValue('player_id') );
        if( $player->id ) {
          $flag_set_player_resource_history = $player->set_player_resource_history(
            getValue('game_id'),
            getValue('resource_id'),
            getValue('turn'),
            getValue('datetime'),
            getValue('delta'),
            getValue('reason'),
            getValue('player_order_id')
          );
        }
        break;
      case 'del_player_resource_history':
        $player = Player::instance( getValue('player_id') );
        if( $player->id ) {
          $flag_del_player_resource_history = $player->del_player_resource_history(
            getValue('game_id'),
            getValue('resource_id'),
            getValue('player_order_id')
          );
        }
        break;
      case 'set_player_spygame_value':
        $player = Player::instance( getValue('player_id') );
        if( $player->id ) {
          $flag_set_player_spygame_value = $player->set_player_spygame_value(
            getValue('game_id'),
            getValue('value_guid'),
            getValue('turn'),
            getValue('datetime'),
            getValue('real_value'),
            getValue('masked_value')
          );
        }
        break;
      case 'del_player_spygame_value':
        $player = Player::instance( getValue('player_id') );
        if( $player->id ) {
          $flag_del_player_spygame_value = $player->del_player_spygame_value(
            getValue('game_id'),
            getValue('value_guid'),
            getValue('turn')
          );
        }
        break;
      default:
        break;
    }
  }
