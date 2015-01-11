<?php

  if(isset($_POST['world_submit'])) {
    unset($_POST['world_submit']);

    $world_mod = World::instance( getValue('id') );

    $world_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $world_mod->check_valid();

    if($tab_error === true) {
      $world_mod->save();

      Page::set_message( 'Record successfuly saved' );
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM