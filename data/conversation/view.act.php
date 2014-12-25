<?php
  $member = Member::instance( Member::get_current_user_id() );
  $player_list = Player::db_get_by_member_id( $member->get_id() );

  $game_id = null;

  if( count( $player_list ) ) {
    $current_player = array_shift( $player_list );

    // Game retrival
    if( getValue('general') !== null && ($current_game = $current_player->current_game) ) {
      $game_id = $current_player->current_game->id;
    }
  }else {
    // No player created
    Page::redirect( 'create_player' );
  }

  /* @var $conversation Conversation */
  $conversation = Conversation::instance( getValue('id') );
  $current_recipients = $conversation->get_current_recipients();
  $conversation_player_list = $conversation->get_conversation_player_list();

  /* @var $message_mod Message */
  $message_mod = Message::instance();


  if(isset($_POST['conversation_submit'])) {
    $message_mod->load_from_html_form($_POST['message'], $_FILES);
    $message_mod->created = time();
    $message_mod->player_id = $current_player->id;
    $message_mod->conversation_id = $conversation->id;

    $tab_error = $message_mod->check_valid();

    if($tab_error === true) {
      $message_mod->save();

      $message_mod->set_message_recipient($current_player->id, time());
      foreach( $current_recipients as $player ) {
        if( $player->id != $current_player->id) {
          $message_mod->set_message_recipient($player->id);
        }
      }
    }else {
      Message::manage_errors($tab_error, $html_msg);
      Page::set_message( $html_msg, Page::PAGE_MESSAGE_WARNING );
    }
  }

  if( 1 == 2 && !is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_conversation_player':
        if( $conversation->id ) {
          $flag_set_conversation_player = $conversation->set_conversation_player(
            ($value = getValue('player_id')) == ''?null:$value,
            ($value = getValue('archived')) == ''?null:$value,
            ($value = getValue('left')) == ''?null:$value
          );
          if( ! $flag_set_conversation_player ) {
            Page::add_message( '$conversation->set_conversation_player : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_conversation_player':
        if( $conversation->id ) {
          $flag_del_conversation_player = $conversation->del_conversation_player(
            ($value = getValue('player_id')) == ''?null:$value
          );
        }
        break;
      default:
        break;
    }
  }