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
