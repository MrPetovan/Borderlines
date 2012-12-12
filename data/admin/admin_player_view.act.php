<?php
  $player = Player::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_conversation_player':
        if( $player->id ) {
          $flag_set_conversation_player = $player->set_conversation_player(
            ($value = getValue('conversation_id')) == ''?null:$value,
            ($value = getValue('archived')) == ''?null:$value,
            ($value = getValue('left')) == ''?null:$value
          );
          if( ! $flag_set_conversation_player ) {
            Page::add_message( '$player->set_conversation_player : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_conversation_player':
        if( $player->id ) {
          $flag_del_conversation_player = $player->del_conversation_player(
            ($value = getValue('conversation_id')) == ''?null:$value
          );
        }
        break;
      case 'set_game_player':
        if( $player->id ) {
          $flag_set_game_player = $player->set_game_player(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn_ready')) == ''?null:$value,
            ($value = getValue('turn_leave')) == ''?null:$value
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
      case 'set_message_recipient':
        if( $player->id ) {
          $flag_set_message_recipient = $player->set_message_recipient(
            ($value = getValue('message_id')) == ''?null:$value,
            ($value = getValue('read')) == ''?null:$value
          );
          if( ! $flag_set_message_recipient ) {
            Page::add_message( '$player->set_message_recipient : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_message_recipient':
        if( $player->id ) {
          $flag_del_message_recipient = $player->del_message_recipient(
            ($value = getValue('message_id')) == ''?null:$value
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
      case 'set_territory_player_status':
        if( $player->id ) {
          $flag_set_territory_player_status = $player->set_territory_player_status(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('territory_id')) == ''?null:$value,
            ($value = getValue('supremacy')) == ''?null:$value
          );
          if( ! $flag_set_territory_player_status ) {
            Page::add_message( '$player->set_territory_player_status : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_player_status':
        if( $player->id ) {
          $flag_del_territory_player_status = $player->del_territory_player_status(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('territory_id')) == ''?null:$value
          );
        }
        break;
      case 'set_territory_player_troops_history':
        if( $player->id ) {
          $flag_set_territory_player_troops_history = $player->set_territory_player_troops_history(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('territory_id')) == ''?null:$value,
            ($value = getValue('delta')) == ''?null:$value,
            getValue('reason'),
            ($value = getValue('reason_player_id')) == ''?null:$value
          );
          if( ! $flag_set_territory_player_troops_history ) {
            Page::add_message( '$player->set_territory_player_troops_history : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_player_troops_history':
        if( $player->id ) {
          $flag_del_territory_player_troops_history = $player->del_territory_player_troops_history(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('territory_id')) == ''?null:$value,
            ($value = getValue('reason_player_id')) == ''?null:$value
          );
        }
        break;
      case 'set_territory_status':
        if( $player->id ) {
          $flag_set_territory_status = $player->set_territory_status(
            ($value = getValue('territory_id')) == ''?null:$value,
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('contested')) == ''?null:$value,
            ($value = getValue('capital')) == ''?null:$value,
            ($value = getValue('revenue_suppression')) == ''?null:$value
          );
          if( ! $flag_set_territory_status ) {
            Page::add_message( '$player->set_territory_status : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_status':
        if( $player->id ) {
          $flag_del_territory_status = $player->del_territory_status(
            ($value = getValue('territory_id')) == ''?null:$value,
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value
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
