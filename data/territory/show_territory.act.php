<?php
  $member = Member::instance( Member::get_current_user_id() );
  $current_player = Player::get_current( $member );

  $current_game = null;
  if( /*is_admin() &&*/ ( $game_id = getValue('game_id') ) ) {
    $current_game = Game::instance( $game_id );
  }else {
    $current_game = $current_player->current_game;
  }

  $turn = getValue('turn');
  if( $current_game && $turn === null ) {
    $turn = $current_game->current_turn;
  }

  $territory = Territory::instance( getValue('id') );

  if( !$territory->id ) {
    Page::add_message('Unknown Territory', Page::PAGE_MESSAGE_ERROR);
    Page::redirect('world_list');
  }