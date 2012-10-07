<?php

  if(isset($_POST['translation_submit'])) {
    unset($_POST['translation_submit']);

    $translation_mod = Translation::instance( getValue('id') );

    $translation_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $translation_mod->check_valid();

    if($tab_error === true) {
      $translation_mod->save();

      Page::set_message( 'Record successfuly saved' );
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM