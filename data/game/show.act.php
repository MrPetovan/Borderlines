<?php
  $member = Member::get_current_user();
  $current_player = Player::get_current($member);

  /* @var $game Game */
  $game = Game::instance( getValue('id') );
  $is_player_active = false;

  if( !$game->id ) {
    Page::add_message( __('Unknown game'), Page::PAGE_MESSAGE_ERROR);
    Page::redirect('game_list');
  }

  $rows = $game->get_game_player_list( $current_player->id );
  $game_player = array_pop( $rows );

  if( !$game_player['turn_leave'] && !$game->has_ended() ) {
    $is_player_active = true;
  }

  if(!is_null(getValue('action')) && $current_player->id) {
    switch( getValue('action') ) {
      case 'join' : {
        if( $game->add_player( $current_player ) ) {
          Page::add_message( __('You successfully joined the game !') );
        }
        break;
      }
    }
    if( is_admin() ) {
      switch( getValue('action') ) {
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
    }
  }

  $game_player_list = $game->get_game_player_list();
  if( count( $game_player_list ) ) {
    foreach( $game_player_list as $game_player_row ) {
      $player_area[ 'player_' . $game_player_row['player_id'] ] = 0;
    }
    $territory_status_list = $game->get_territory_status_list(null, $game->current_turn);

    foreach( $territory_status_list as $territory_status_row ) {
      if( $territory_status_row['owner_id'] ) {
        $territory = Territory::instance( $territory_status_row['territory_id'] );

        if( isset( $player_area[ 'player_' . $territory_status_row['owner_id'] ] )) {
          $player_area[ 'player_' . $territory_status_row['owner_id'] ] += $territory->area;
        }else {
          $player_area[ 'player_' . $territory_status_row['owner_id'] ] = $territory->area;
        }
      }
    }

    array_multisort($player_area, SORT_DESC, $game_player_list);
  }

  $world_id = $game->world_id;
  if( $world_id == 1 ) {
    Page::add_message(__('Unable to show this world : This is a test world without territories'), Page::PAGE_MESSAGE_WARNING);
    Page::redirect('world_list');
  }

  $world = World::instance( $world_id );