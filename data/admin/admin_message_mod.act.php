<?php

  if(isset($_POST['message_submit'])) {
    unset($_POST['message_submit']);

    $message_mod = Message::instance( getValue('id') );

    $message_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $message_mod->check_valid();

    if($tab_error === true) {
      $message_mod->save();

      Page::set_message( 'Record successfuly saved' );
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM