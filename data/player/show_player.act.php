<?php
  $member = Member::instance( Member::get_current_user_id() );
  $player_list = Player::db_get_by_member_id( $member->get_id() );
  $current_player = array_shift( $player_list );
  
  if( !is_null( $player_id = getValue('id' ) ) && is_numeric( $player_id ) ) {
    if( $current_player->get_id() == $player_id ) {
      Page::page_redirect( 'dashboard' );
    }else {
      $player = Player::instance( $player_id );
      if( !$player ) {
        Page::add_message( "This player doesn't exist", Page::PAGE_MESSAGE_ERROR );
        Page::page_redirect( 'player_list' );
      }
    }
  }else {
    Page::page_redirect( 'player_list' );
  }
  
  if( $game_id = getValue('game_id') ) {
    $player_current_game = Game::instance( $game_id );
    
    if( !$player_current_game ) {
      Page::add_message( "This game doesn't exist", Page::PAGE_MESSAGE_ERROR );
      Page::page_redirect( 'player_list' );
    }
  }else {
    $player_current_game = $player->get_last_game();
  }
  
  $current_game = $current_player->get_current_game();
  
  // Cases :
  // - Show public profile (game-independant)
  // - Spy (must be in the same running game)
  // - Show past game result (game must have ended)
?>