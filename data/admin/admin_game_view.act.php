<?php
  $game = Game::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_game_player':
        if( $game->id ) {
          $flag_set_game_player = $game->set_game_player(
            getValue('player_id'),
            getValue('turn_ready')
          );
        }
        break;
      case 'del_game_player':
        if( $game->id ) {
          $flag_del_game_player = $game->del_game_player(
            getValue('player_id')
          );
        }
        break;
      case 'set_player_resource_history':
        if( $game->id ) {
          $flag_set_player_resource_history = $game->set_player_resource_history(
            getValue('player_id'),
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
        if( $game->id ) {
          $flag_del_player_resource_history = $game->del_player_resource_history(
            getValue('player_id'),
            getValue('resource_id'),
            getValue('player_order_id')
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
