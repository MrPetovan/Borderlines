<?php

  if(isset($_POST['api_token_submit'])) {
    unset($_POST['api_token_submit']);

    $api_token_mod = Api_Token::instance( getValue('id') );

    $api_token_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $api_token_mod->check_valid();

    if($tab_error === true) {
      $api_token_mod->save();

      Page::set_message( 'Record successfuly saved' );
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM