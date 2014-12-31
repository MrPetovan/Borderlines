<?php
  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['resource_id']) && is_array($_POST['resource_id'])) {
        foreach($_POST['resource_id'] as $resource_id) {

          $resource = Resource::instance( $resource_id );
          switch($_POST['action']) {
            case 'delete' :
              $resource->db_delete();
              break;
          }
        }
      }
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM