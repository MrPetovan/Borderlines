<?php
  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['api_token_id']) && is_array($_POST['api_token_id'])) {
        foreach($_POST['api_token_id'] as $api_token_id) {

          $api_token = Api_Token::instance( $api_token_id );
          switch($_POST['action']) {
            case 'delete' :
              $api_token->db_delete();
              break;
          }
        }
      }
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM