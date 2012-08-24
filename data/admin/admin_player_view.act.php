<?php
  $player = Player::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_game_player':
        if( $player->id ) {
          $flag_set_game_player = $player->set_game_player(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn_ready')) == ''?null:$value
          );
          if( ! $flag_set_game_player ) {
            Page::add_message( '$player->set_game_player : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_game_player':
        if( $player->id ) {
          $flag_del_game_player = $player->del_game_player(
            ($value = getValue('game_id')) == ''?null:$value
          );
        }
        break;
      case 'set_player_diplomacy':
        if( $player->id ) {
          $flag_set_player_diplomacy = $player->set_player_diplomacy(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('to_player_id')) == ''?null:$value,
            ($value = getValue('status')) == ''?null:$value
          );
          if( ! $flag_set_player_diplomacy ) {
            Page::add_message( '$player->set_player_diplomacy : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_player_diplomacy':
        if( $player->id ) {
          $flag_del_player_diplomacy = $player->del_player_diplomacy(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('to_player_id')) == ''?null:$value
          );
        }
        break;
      case 'set_player_history':
        if( $player->id ) {
          $flag_set_player_history = $player->set_player_history(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('datetime')) == ''?null:$value,
            getValue('reason'),
            ($value = getValue('territory_id')) == ''?null:$value
          );
          if( ! $flag_set_player_history ) {
            Page::add_message( '$player->set_player_history : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_player_history':
        if( $player->id ) {
          $flag_del_player_history = $player->del_player_history(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('territory_id')) == ''?null:$value
          );
        }
        break;
      case 'set_player_resource_history':
        if( $player->id ) {
          $flag_set_player_resource_history = $player->set_player_resource_history(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('resource_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('datetime')) == ''?null:$value,
            ($value = getValue('delta')) == ''?null:$value,
            getValue('reason'),
            ($value = getValue('player_order_id')) == ''?null:$value
          );
          if( ! $flag_set_player_resource_history ) {
            Page::add_message( '$player->set_player_resource_history : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_player_resource_history':
        if( $player->id ) {
          $flag_del_player_resource_history = $player->del_player_resource_history(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('resource_id')) == ''?null:$value,
            ($value = getValue('player_order_id')) == ''?null:$value
          );
        }
        break;
      case 'set_player_spygame_value':
        if( $player->id ) {
          $flag_set_player_spygame_value = $player->set_player_spygame_value(
            ($value = getValue('game_id')) == ''?null:$value,
            getValue('value_guid'),
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('datetime')) == ''?null:$value,
            ($value = getValue('real_value')) == ''?null:$value,
            ($value = getValue('masked_value')) == ''?null:$value
          );
          if( ! $flag_set_player_spygame_value ) {
            Page::add_message( '$player->set_player_spygame_value : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_player_spygame_value':
        if( $player->id ) {
          $flag_del_player_spygame_value = $player->del_player_spygame_value(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('value_guid')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value
          );
        }
        break;
      case 'set_territory_owner':
        if( $player->id ) {
          $flag_set_territory_owner = $player->set_territory_owner(
            ($value = getValue('territory_id')) == ''?null:$value,
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('contested')) == ''?null:$value,
            ($value = getValue('capital')) == ''?null:$value
          );
          if( ! $flag_set_territory_owner ) {
            Page::add_message( '$player->set_territory_owner : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_owner':
        if( $player->id ) {
          $flag_del_territory_owner = $player->del_territory_owner(
            ($value = getValue('territory_id')) == ''?null:$value,
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value
          );
        }
        break;
      case 'set_territory_player_troops':
        if( $player->id ) {
          $flag_set_territory_player_troops = $player->set_territory_player_troops(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('territory_id')) == ''?null:$value,
            ($value = getValue('quantity')) == ''?null:$value
          );
          if( ! $flag_set_territory_player_troops ) {
            Page::add_message( '$player->set_territory_player_troops : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_player_troops':
        if( $player->id ) {
          $flag_del_territory_player_troops = $player->del_territory_player_troops(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('territory_id')) == ''?null:$value
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
