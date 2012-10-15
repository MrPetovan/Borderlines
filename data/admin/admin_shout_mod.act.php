<?php

  if(isset($_POST['shout_submit'])) {
    unset($_POST['shout_submit']);

    $shout_mod = Shout::instance( getValue('id') );

    $shout_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $shout_mod->check_valid();

    if($tab_error === true) {
      $shout_mod->save();

      Page::set_message( 'Record successfuly saved' );
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM