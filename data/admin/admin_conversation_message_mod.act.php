<?php

  if(isset($_POST['conversation_message_submit'])) {
    unset($_POST['conversation_message_submit']);

    $conversation_message_mod = Conversation_Message::instance( getValue('id') );

    $conversation_message_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $conversation_message_mod->check_valid();

    if($tab_error === true) {
      $conversation_message_mod->save();

      Page::set_message( 'Record successfuly saved' );
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM