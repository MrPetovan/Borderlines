<?php
  $member = Member::instance( Member::get_current_user_id() );
  $current_player = Player::get_current( $member );

  $turn = getValue('turn');
  $redirect_page = null;

  if( $current_player ) {
    // Game retrival
    $current_game = $current_player->last_game;
    if( $current_game ) {
      $game_player = array_pop( $current_game->get_game_player_list( $current_player->id) );

      if( !$game_player['turn_leave'] && !$current_game->has_ended() ) {
        $action = getValue('action');
        $redirect_page = PAGE_CODE;
        switch( $action ) {
          default: {
            $redirect_page = null;
          }
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
  }else {
    // No pplayer created
    $redirect_page = 'create_player';
  }

  if( $redirect_page ) {
    Page::redirect($redirect_page);
  }

  if( $turn === null ) {
    $turn = $current_game->current_turn;
  }

  $params = array('game_id' => $current_game->id, 'turn' => $turn);