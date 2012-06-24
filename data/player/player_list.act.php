<?php
  $member = Member::instance( Member::get_current_user_id() );
  $player_list = Player::db_get_by_member_id( $member->get_id() );
  $current_player = array_shift( $player_list );
  
  $current_game = null;
  if( $game_id = getValue('game_id') ) {
    $current_game = Game::instance( $game_id );
  }else {
    $current_game = $current_player->get_last_game();
  }

  if( !$current_game ) {
    Page::redirect( 'dashboard' );
  }
?>