<?php
  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['conversation_id']) && is_array($_POST['conversation_id'])) {
        foreach($_POST['conversation_id'] as $conversation_id) {

          $conversation = Conversation::instance( $conversation_id );
          switch($_POST['action']) {
            case 'delete' :
              $conversation->db_delete();
              break;
          }
        }
      }
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM