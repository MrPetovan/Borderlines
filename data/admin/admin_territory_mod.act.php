<?php

  if(isset($_POST['territory_submit'])) {
    unset($_POST['territory_submit']);

    $territory_mod = Territory::instance( getValue('id') );

    $territory_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $territory_mod->check_valid();

    if($tab_error === true) {
      $territory_mod->save();

      Page::set_message( 'Record successfuly saved' );
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM