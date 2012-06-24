<?php
  $member = Member::instance( Member::get_current_user_id() );

  // TODO : Create player page
  $player_list = Player::db_get_by_member_id( $member->id );
  $current_player = array_shift( $player_list );

  $game = Game::instance( getValue('id') );
  
  if( !$game->id ) {
    Page::add_message('Unknown game', Page::PAGE_MESSAGE_ERROR);
    Page::redirect('game_list');
  }

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
      case 'join' : {
        if( $game->add_player( $current_player ) ) {
          Page::add_message('You successfully joined the game !');
        }
        break;
      }
    }
    Page::redirect( PAGE_CODE, array('id' => $game->id ) );
  }