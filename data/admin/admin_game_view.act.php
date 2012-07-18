<?php
  $game = Game::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_game_player':
        if( $game->id ) {
          $flag_set_game_player = $game->set_game_player(
            ($value = getValue('player_id')) == ''?null:$value,
            ($value = getValue('turn_ready')) == ''?null:$value
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
      default:
        break;
    }
  }
  
  // CUSTOM

  $list = Player_Order::get_ready_orders( $game->id );
    
  $player_order_log = Player_Order::db_get_order_log( $game->id );

  if( $action = getValue('action') ) {
    switch( $action ) {
      case "reset" : {
        $game->reset();
        
        Page::set_message('reset game OK');
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
