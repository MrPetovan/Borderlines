<?php
  $member = Member::instance( Member::get_current_user_id() );

  if(isset($_POST['action'])) {
    unset($_POST['action']);

    if( Player::can_create_player( $member ) ) {
      $player = Player::instance();
      $player->member_id = Member::get_current_user_id();
      $player->active = true;
      $player->created = time();

      $player->load_from_html_form($_POST, $_FILES);
      $tab_error = $player->check_valid();

      if($tab_error === true) {
        $player->save();

        Page::set_message( __('Player successfuly created') );

        Page::redirect( 'dashboard' );
      }else {
        $html_msg = '';
        Player::manage_errors($tab_error, $html_msg);
        Page::set_message( $html_msg, Page::PAGE_MESSAGE_ERROR );
      }
    }else {
      Page::set_message( __('You can\'t create more player yet'), Page::PAGE_MESSAGE_ERROR );
      Page::redirect( 'dashboard' );
    }
  }
?>