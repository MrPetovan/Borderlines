<?php
  $member = Member::instance( Member::get_current_user_id() );

  // TODO : Create player page
  $player_list = Player::db_get_by_member_id( $member->id );
  $current_player = array_shift( $player_list );

  $territory = Territory::instance( getValue('id') );

  if( !$territory->id ) {
    Page::add_message('Unknown Territory', Page::PAGE_MESSAGE_ERROR);
    Page::redirect('world_list');
  }