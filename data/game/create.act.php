<?php
  $member = Member::get_current_user();
  $current_player = Player::get_current( $member );

  if( !$current_player ) {
    Page::redirect('create_player');
  }

  $game_mod = Game::instance();

  $redirect_page = null;
  if( !$current_player->can_create_game() ) {
    Page::add_message( __('You have already created a non-finished game, cancel or finish the first before creating a new one.'), Page::PAGE_MESSAGE_ERROR );
    $redirect_page = 'game_list';
  }

  if(isset($_POST['game_submit']) ) {
    unset($_POST['game_submit']);

    $game_mod->load_from_html_form($_POST, $_FILES);
    $game_mod->created = time();
    $game_mod->current_turn = 0;
    $game_mod->created_by = $current_player->id;
    $tab_error = $game_mod->check_valid();

    if($tab_error === true) {
      $game_mod->save();

      Page::add_message( __('Game successfuly created') );
      $redirect_page = 'game_list';
    }else {
      $html_msg = '';
      Game::manage_errors( $tab_error, $html_msg );
      Page::add_message( $html_msg, Page::PAGE_MESSAGE_ERROR );
    }
  }

  if( $redirect_page ) {
    Page::redirect($redirect_page);
  }
?>