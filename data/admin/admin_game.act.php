<?php
  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['game_id']) && is_array($_POST['game_id'])) {
        foreach($_POST['game_id'] as $game_id) {

          $game = Game::instance( $game_id );
          switch($_POST['action']) {
            case 'delete' :
              $game->db_delete();
              break;
          }
        }
      }
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM