<?php
  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['conversation_message_id']) && is_array($_POST['conversation_message_id'])) {
        foreach($_POST['conversation_message_id'] as $conversation_message_id) {

          $conversation_message = Conversation_Message::instance( $conversation_message_id );
          switch($_POST['action']) {
            case 'delete' :
              $conversation_message->db_delete();
              break;
          }
        }
      }
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM