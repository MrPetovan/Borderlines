<?php
  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['shout_id']) && is_array($_POST['shout_id'])) {
        foreach($_POST['shout_id'] as $shout_id) {

          $shout = Shout::instance( $shout_id );
          switch($_POST['action']) {
            case 'delete' :
              $shout->db_delete();
              break;
          }
        }
      }
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM