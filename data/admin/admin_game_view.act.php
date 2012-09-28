<?php
  $game = Game::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_game_player':
        if( $game->id ) {
          $flag_set_game_player = $game->set_game_player(
            ($value = getValue('player_id')) == ''?null:$value,
            ($value = getValue('turn_ready')) == ''?null:$value,
            ($value = getValue('turn_leave')) == ''?null:$value
          );
          if( ! $flag_set_game_player ) {
            Page::add_message( '$game->set_game_player : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_game_player':
        if( $game->id ) {
          $flag_del_game_player = $game->del_game_player(
            ($value = getValue('player_id')) == ''?null:$value
          );
        }
        break;
      case 'set_player_diplomacy':
        if( $game->id ) {
          $flag_set_player_diplomacy = $game->set_player_diplomacy(
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('from_player_id')) == ''?null:$value,
            ($value = getValue('to_player_id')) == ''?null:$value,
            ($value = getValue('status')) == ''?null:$value
          );
          if( ! $flag_set_player_diplomacy ) {
            Page::add_message( '$game->set_player_diplomacy : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_player_diplomacy':
        if( $game->id ) {
          $flag_del_player_diplomacy = $game->del_player_diplomacy(
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('from_player_id')) == ''?null:$value,
            ($value = getValue('to_player_id')) == ''?null:$value
          );
        }
        break;
      case 'set_player_history':
        if( $game->id ) {
          $flag_set_player_history = $game->set_player_history(
            ($value = getValue('player_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('datetime')) == ''?null:$value,
            getValue('reason'),
            ($value = getValue('territory_id')) == ''?null:$value
          );
          if( ! $flag_set_player_history ) {
            Page::add_message( '$game->set_player_history : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_player_history':
        if( $game->id ) {
          $flag_del_player_history = $game->del_player_history(
            ($value = getValue('player_id')) == ''?null:$value,
            ($value = getValue('territory_id')) == ''?null:$value
          );
        }
        break;
      case 'set_player_resource_history':
        if( $game->id ) {
          $flag_set_player_resource_history = $game->set_player_resource_history(
            ($value = getValue('player_id')) == ''?null:$value,
            ($value = getValue('resource_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('datetime')) == ''?null:$value,
            ($value = getValue('delta')) == ''?null:$value,
            getValue('reason'),
            ($value = getValue('player_order_id')) == ''?null:$value
          );
          if( ! $flag_set_player_resource_history ) {
            Page::add_message( '$game->set_player_resource_history : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_player_resource_history':
        if( $game->id ) {
          $flag_del_player_resource_history = $game->del_player_resource_history(
            ($value = getValue('player_id')) == ''?null:$value,
            ($value = getValue('resource_id')) == ''?null:$value,
            ($value = getValue('player_order_id')) == ''?null:$value
          );
        }
        break;
      case 'set_territory_owner':
        if( $game->id ) {
          $flag_set_territory_owner = $game->set_territory_owner(
            ($value = getValue('territory_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('owner_id')) == ''?null:$value,
            ($value = getValue('contested')) == ''?null:$value,
            ($value = getValue('capital')) == ''?null:$value
          );
          if( ! $flag_set_territory_owner ) {
            Page::add_message( '$game->set_territory_owner : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_owner':
        if( $game->id ) {
          $flag_del_territory_owner = $game->del_territory_owner(
            ($value = getValue('territory_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('owner_id')) == ''?null:$value
          );
        }
        break;
      case 'set_territory_player_troops':
        if( $game->id ) {
          $flag_set_territory_player_troops = $game->set_territory_player_troops(
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('territory_id')) == ''?null:$value,
            ($value = getValue('player_id')) == ''?null:$value,
            ($value = getValue('quantity')) == ''?null:$value
          );
          if( ! $flag_set_territory_player_troops ) {
            Page::add_message( '$game->set_territory_player_troops : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_player_troops':
        if( $game->id ) {
          $flag_del_territory_player_troops = $game->del_territory_player_troops(
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('territory_id')) == ''?null:$value,
            ($value = getValue('player_id')) == ''?null:$value
          );
        }
        break;
      default:
        break;
    }
  }
  
  // CUSTOM

  $list = $game->get_ready_orders();

  $player_order_log = Player_Order::db_get_order_log( $game->id );

  if( $action = getValue('action') ) {
    switch( $action ) {
      case "reset" : {
        $game->reset();

        Page::set_message('reset game OK');
        break;
      }
      case "revert" : {
        $turn = getValue('turn');
        $game->revert( $turn );

        Page::set_message('revert game to turn '.$turn.' OK');
        break;
      }
      case "start" : {
        $game->start();

        Page::set_message('start game OK');
        break;
      }
      case "compute" : {
        if( $game->compute() ) {
          Page::set_message('compute OK');
        }else {
          Page::set_message('compute KO', Page::PAGE_MESSAGE_ERROR);
        }
        break;
      }
    }
    //Page::page_redirect( PAGE_CODE, array('id' => $game->id ) );
  }

  // /CUSTOM
