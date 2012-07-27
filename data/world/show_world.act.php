<?php
  $member = Member::instance( Member::get_current_user_id() );

  // TODO : Create player page
  $player_list = Player::db_get_by_member_id( $member->id );
  $current_player = array_shift( $player_list );

  $world = World::instance( getValue('id') );

  if( !$world->id ) {
    Page::add_message('Unknown world', Page::PAGE_MESSAGE_ERROR);
    Page::redirect('world_list');
  }