<?php
  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['category_id']) && is_array($_POST['category_id'])) {
        foreach($_POST['category_id'] as $category_id) {

          $category = Category::instance( $category_id );
          switch($_POST['action']) {
            case 'delete' :
              $category->db_delete();
              break;
          }
        }
      }
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM