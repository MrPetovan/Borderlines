<?php
  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['world_id']) && is_array($_POST['world_id'])) {
        foreach($_POST['world_id'] as $world_id) {

          $world = World::instance( $world_id );
          switch($_POST['action']) {
            case 'delete' :
              $world->db_delete();
              break;
          }
        }
      }
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM