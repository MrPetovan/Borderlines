<?php
  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['territory_id']) && is_array($_POST['territory_id'])) {
        foreach($_POST['territory_id'] as $territory_id) {

          $territory = Territory::instance( $territory_id );
          switch($_POST['action']) {
            case 'delete' :
              $territory->db_delete();
              break;
          }
        }
      }
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM