<?php
  $member = Member::instance( Member::get_current_user_id() );
  $player_list = Player::db_get_by_member_id( $member->get_id() );
  $current_player = array_shift( $player_list );
  
  if( !is_null( $player_id = getValue('id' ) ) && is_numeric( $player_id ) ) {
    if( $current_player->get_id() == $player_id ) {
      Page::page_redirect( 'dashboard' );
    }else {
      $player = Player::instance( $player_id );
    }
  }else {
    Page::page_redirect( 'player_list' );
  }
?>