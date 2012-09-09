<?php
  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['message_id']) && is_array($_POST['message_id'])) {
        foreach($_POST['message_id'] as $message_id) {

          $message = Message::instance( $message_id );
          switch($_POST['action']) {
            case 'delete' :
              $message->db_delete();
              break;
          }
        }
      }
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM