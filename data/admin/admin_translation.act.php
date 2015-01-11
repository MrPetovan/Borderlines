<?php
  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['translation_id']) && is_array($_POST['translation_id'])) {
        foreach($_POST['translation_id'] as $translation_id) {

          $translation = Translation::instance( $translation_id );
          switch($_POST['action']) {
            case 'delete' :
              $translation->db_delete();
              break;
          }
        }
      }
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM