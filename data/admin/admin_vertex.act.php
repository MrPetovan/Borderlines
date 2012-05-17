<?php
  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['vertex_id']) && is_array($_POST['vertex_id'])) {
        foreach($_POST['vertex_id'] as $vertex_id) {

          $vertex = Vertex::instance( $vertex_id );
          switch($_POST['action']) {
            case 'delete' :
              $vertex->db_delete();
              break;
          }
        }
      }
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM