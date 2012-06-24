<?php
  $member = Member::instance( Member::get_current_user_id() );

  // TODO : Create player page
  $player_list = Player::db_get_by_member_id( $member->id );
  $current_player = array_shift( $player_list );

  $game_mod = Game::instance();

  if(isset($_POST['game_submit']) && is_admin() ) {
    unset($_POST['game_submit']);

    $game_mod->load_from_html_form($_POST, $_FILES);
    $game_mod->created = time();
    $game_mod->current_turn = 0;
    $game_mod->created_by = $current_player->id;
    $tab_error = $game_mod->check_valid();

    if($tab_error === true) {
      $game_mod->save();

      Page::add_message( 'Game successfuly created' );
    }else {
      $html_msg = '';
      Game::manage_errors( $tab_error, $html_msg );
      Page::add_message( $html_msg, Page::PAGE_MESSAGE_ERROR );
    }
  }
  
  if( $action = getValue('action' ) ) {
    switch( $action ) {
      case 'join' : {
        if( $game_id = getValue('game_id') ) {
          $game_to_join = Game::instance( $game_id );
          if( $game_to_join->id ) {
            if( $game_to_join->add_player( $current_player ) ) {
              Page::add_message('You successfully joined the game !');
            }
          }else {
            Page::add_message('Unknown game', Page::PAGE_MESSAGE_ERROR);
          }
        }else {
          Page::add_message('Game id parameter missing', Page::PAGE_MESSAGE_ERROR);
        }
        Page::redirect( PAGE_CODE );
        break;
      }
    }
  }

  $game_list = Game::db_get_all();
?>