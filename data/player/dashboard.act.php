<?php
  $member = Member::instance( Member::get_current_user_id() );
  
  // TODO : Create player page
  $player_list = Player::db_get_by_member_id( $member->get_id() );
  $current_player = array_shift( $player_list );
  
  // Game retrival
  if( $current_game = $current_player->last_game ) {
    // In game OR game ended
    if( $current_game->has_ended() ) {
    }else {
      if( $action = getValue('action') ) {
        switch( $action ) {
          case 'ready' : {
            $turn_ready = array_shift( $current_player->get_game_player_list( $current_game->id ) );
            if( $turn_ready['turn_ready'] <= $current_game->current_turn ) {
              $current_player->set_game_player( $current_game->id, $current_game->current_turn + 1 );
            }else {
              $current_player->set_game_player( $current_game->id, $current_game->current_turn );
            }
          }
        }
      }
    }
  }else {
    // No game ever played
    Page::redirect( 'game_list' );
  }
?>