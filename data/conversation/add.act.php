<?php
  $member = Member::instance( Member::get_current_user_id() );
  $player_list = Player::db_get_by_member_id( $member->get_id() );

  $game_id = null;

  if( count( $player_list ) ) {
    $current_player = array_shift( $player_list );

    // Game retrival
    if( getValue('general') === null && ($current_game = $current_player->current_game) ) {
      $game_id = $current_player->current_game->id;
    }
  }else {
    // No player created
    Page::redirect( 'create_player' );
  }

  /* @var $conversation_mod Conversation */
  $conversation_mod = Conversation::instance();
  /* @var $message_mod Message */
  $message_mod = Message::instance();

  $recipient_list = getValue('recipient_list', array());

  if(isset($_POST['conversation_submit']) || isset($_POST['add_to']) || isset($_POST['remove_recipient'])) {
    $conversation_mod->load_from_html_form($_POST['conversation'], $_FILES);
    $message_mod->load_from_html_form($_POST['message'], $_FILES);

    if( isset($_POST['add_to']) && getValue('player_id') ) {
      $recipient_list[] = getValue('player_id');
    }
    if( isset($_POST['remove_recipient']) ) {
      unset( $recipient_list[array_search(getValue('remove_recipient'), $recipient_list) ]);
    }
    if(isset($_POST['conversation_submit'])) {
      $conversation_mod->player_id = $current_player->id;
      $conversation_mod->game_id = $game_id;
      $conversation_mod->created = time();

      $tab_error = $conversation_mod->check_valid();

      if($tab_error === true) {
        $conversation_mod->save();

        $message_mod->created = time();
        $message_mod->player_id = $current_player->id;
        $message_mod->conversation_id = $conversation_mod->id;
        $message_mod->save();

        $conversation_mod->set_conversation_player($current_player->id);
        $message_mod->set_message_recipient($current_player->id, time());
        foreach( $recipient_list as $recipient_id ) {
          $conversation_mod->set_conversation_player($recipient_id);
          $message_mod->set_message_recipient($recipient_id);
          /* @var $player Player */
          $player = Player::instance($recipient_id);
          $member = Member::instance( $player->member_id );

          if( php_mail($member->email, SITE_NAME." | New conversation", $player->get_email_new_conversation( $conversation_mod ), true) ) {
            Page::add_message("Message sent to ".$player->name);
          }else {
            Page::add_message("Message failed to ".$player->name, Page::PAGE_MESSAGE_WARNING);
            Page::add_message(var_export( error_get_last(), 1 ), Page::PAGE_MESSAGE_WARNING);
          }
        }

        Page::set_message( 'Record successfuly saved' );
        if( getValue('return_url') ) {
          redirect(getValue('return_url'));
        }else {
          if( getValue('general') !== null ) {
            $params = array('general' => getValue('general'));
          }else {
            $params = array();
          }
          Page::redirect( 'conversation_list', $params );
        }
      }else {
        Conversation::manage_errors($tab_error, $html_msg);
        Page::set_message( $html_msg, Page::PAGE_MESSAGE_WARNING );
      }
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM