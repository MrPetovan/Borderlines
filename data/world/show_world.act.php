<?php
  $member = Member::instance( Member::get_current_user_id() );

  // TODO : Create player page
  $player_list = Player::db_get_by_member_id( $member->id );
  $current_player = array_shift( $player_list );

  if( is_admin() && ( $game_id = getValue('game_id') ) ) {
    $current_game = Game::instance( $game_id );
  }else {
    $current_game = $current_player->current_game;
  }

  if( ! $world_id = getValue('id') ) {
    if( $current_game ) {
      $world_id = $current_game->world_id;
    }else {
      Page::redirect('game_list');
    }
  }

  $turn = getValue('turn');
  if( $turn === null ) {
    $turn = $current_game->current_turn;
  }

  $world = World::instance( $world_id );

  if( !$world->id ) {
    Page::add_message('Unknown world', Page::PAGE_MESSAGE_ERROR);
    Page::redirect('world_list');
  }