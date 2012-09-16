<?php
  $member = Member::instance( Member::get_current_user_id() );

  // TODO : Create player page
  $player_list = Player::db_get_by_member_id( $member->id );
  $current_player = array_shift( $player_list );

  if( $current_game = $current_player->current_game ) {
    if( $current_game->world_id != getValue('id') ) {
      Page::redirect(PAGE_CODE, array('id' => $current_game->word_id));
    }
  }

  $world = World::instance( getValue('id') );

  if( !$world->id ) {
    Page::add_message('Unknown world', Page::PAGE_MESSAGE_ERROR);
    Page::redirect('world_list');
  }