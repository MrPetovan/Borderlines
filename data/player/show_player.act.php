<?php
  $member = Member::get_current_user();
  $current_player = Player::get_current($member);

  if( !is_null( $player_id = getValue('id' ) ) && is_numeric( $player_id ) ) {
    /* @var $player Player */
    $player = Player::instance( $player_id );
    if( !$player ) {
      Page::add_message( __('Unknown player'), Page::PAGE_MESSAGE_ERROR );
      Page::page_redirect( 'player_list' );
    }
  }else {
    Page::page_redirect( 'player_list' );
  }

  $game_player_list = $player->get_game_player_list();
  $game_player_area = $player->get_game_player_area();
?>