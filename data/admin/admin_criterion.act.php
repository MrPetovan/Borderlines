<?php
  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['criterion_id']) && is_array($_POST['criterion_id'])) {
        foreach($_POST['criterion_id'] as $criterion_id) {

          $criterion = Criterion::instance( $criterion_id );
          switch($_POST['action']) {
            case 'delete' :
              $criterion->db_delete();
              break;
          }
        }
      }
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM