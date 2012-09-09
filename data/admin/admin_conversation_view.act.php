<?php
  $conversation = Conversation::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
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
  
  // CUSTOM

  //Custom content

  // /CUSTOM
