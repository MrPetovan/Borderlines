<?php
  $member = Member::instance( Member::get_current_user_id() );
  $player_list = Player::db_get_by_member_id( $member->get_id() );
  $current_player = array_shift( $player_list );

  if( $action = getValue('action') ) {
    if( $action == 'shout' && $current_player->id ) {
      if( trim( $text = getValue('text') ) != '' ) {
        if( is_null( $game_id = getValue('game_id') ) || $current_player->get_current_game_id() == $game_id ) {
          $shout = new Shout();
          $shout->game_id = $game_id;
          $shout->shouter_id = $current_player->id;
          $shout->text = $text;
          $shout->date_sent = time();
          $shout->save();
        }else {
          Page::set_message( 'You can\'t shout in another game than your current.', Page::PAGE_MESSAGE_ERROR );
        }
      }else {
        Page::set_message( 'Empty text', Page::PAGE_MESSAGE_ERROR );
      }
    }

    if( is_null( $url_return = getValue( 'url_return' ) ) ) {
      $url_return = Page::get_page_url( 'dashboard', true );
    }

    redirect( $url_return );
  }else {
    Page::page_redirect('dashboard');
  }
?>