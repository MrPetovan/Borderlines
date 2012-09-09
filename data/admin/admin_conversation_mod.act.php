<?php

  if(isset($_POST['conversation_submit'])) {
    unset($_POST['conversation_submit']);

    $conversation_mod = Conversation::instance( getValue('id') );

    $conversation_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $conversation_mod->check_valid();

    if($tab_error === true) {
      $conversation_mod->save();

      Page::set_message( 'Record successfuly saved' );
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM