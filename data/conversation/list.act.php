<?php
  $member = Member::instance( Member::get_current_user_id() );
  $player_list = Player::db_get_by_member_id( $member->get_id() );

  $game_id = null;

  if( count( $player_list ) ) {
    $current_player = array_shift( $player_list );
    // Game retrival
    if( $current_game = $current_player->current_game ) {
      $game_id = $current_player->current_game->id;
    }
  }else {
    // No player created
    Page::redirect( 'create_player' );
  }

  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['conversation_id']) && is_array($_POST['conversation_id'])) {
        foreach($_POST['conversation_id'] as $conversation_id) {

          $conversation = Conversation::instance( $conversation_id );
          if( $conversation ) {
            $conversation_player_list = $conversation->get_conversation_player_list($current_player->id);
            if( count($conversation_player_list) ) {
              switch($_POST['action']) {
                case 'archive' : {
                  $conversation->set_conversation_player($current_player->id, time(), $conversation_player_list[0]['left'] );
                  Page::set_message('Conversation "'.$conversation->subject.'" successfully archived');
                  break;
                }
                case 'unarchive' : {
                  $conversation->set_conversation_player($current_player->id, null, $conversation_player_list[0]['left'] );
                  Page::set_message('Conversation "'.$conversation->subject.'" successfully unarchived');
                  break;
                }
                case 'leave' : {
                  $conversation->set_conversation_player($current_player->id, $conversation_player_list[0]['archived'], time() );
                  Page::set_message('Conversation "'.$conversation->subject.'" successfully left');
                  break;
                }
              }
            }else {
              Page::add_message('You don\'t belong to this conversation', Page::PAGE_MESSAGE_WARNING);
            }
          }else {
            Page::add_message('Unknown conversation', Page::PAGE_MESSAGE_WARNING);
          }
        }
      }
    }
  }

  $conversation_list = Conversation_Player::db_get_by_game($current_player->id, getValue('general')?null:$game_id, getValue('archive', false));