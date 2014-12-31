<?php
  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['player_id']) && is_array($_POST['player_id'])) {
        foreach($_POST['player_id'] as $player_id) {

          $player = Player::instance( $player_id );
          switch($_POST['action']) {
            case 'delete' :
              $player->db_delete();
              break;
          }
        }
      }
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM