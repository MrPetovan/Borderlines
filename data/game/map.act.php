<?php
  $member = Member::instance( Member::get_current_user_id() );
  $current_player = Player::get_current( $member );

  $turn = getValue('turn');
  $redirect_page = null;

  if( $current_player ) {
    // Game retrival
    $current_game = $current_player->last_game;
    if( $current_game ) {
      // In game OR game ended
      $game_player = array_pop( $current_game->get_game_player_list( $current_player->id) );

      if( !$game_player['turn_leave'] && !$current_game->has_ended() ) {
        if( $action = getValue('action') ) {
          switch( $action ) {
            case 'ready' : {
              $current_player->set_game_player( $current_game->id, $current_game->current_turn + 1 );
              break;
            }
            case 'notready' : {
              $current_player->set_game_player( $current_game->id, $current_game->current_turn );
              break;
            }
          }
          $redirect_page = PAGE_CODE;
        }
      }else {
        Page::set_message(__('You quit during your last game, please join another one.'), Page::PAGE_MESSAGE_WARNING);
        // Left the game
        $redirect_page = 'game_list';
      }
    }else {
      // No game ever played
      $redirect_page = 'game_list';
    }
  }

  if( $redirect_page ) {
    Page::redirect($redirect_page);
  }

  $api_token_hash = Api_Token::get_current_token($current_player->id)->hash;

  $world_id = $current_game->world_id;
  if( $world_id == 1 ) {
    Page::add_message(__('Unable to show this world : This is a test world without territories'), Page::PAGE_MESSAGE_WARNING);
    Page::redirect('world_list');
  }

  $world = World::instance( $world_id );

  if( !$world->id ) {
    Page::add_message(__('Unknown world'), Page::PAGE_MESSAGE_ERROR);
    Page::redirect('world_list');
  }

  if( $turn === null ) {
    $turn = $current_game->current_turn;
  }

  $params = array('game_id' => $current_game->id, 'turn' => $turn);