<?php
  $member = Member::instance( Member::get_current_user_id() );
  
  // TODO : Create player page
  $player_list = Player::db_get_by_member_id( $member->get_id() );
  $current_player = array_shift( $player_list );
  
  // Game retrival
  // TODO : Select game page
  $current_game = $current_player->current_game;
?>