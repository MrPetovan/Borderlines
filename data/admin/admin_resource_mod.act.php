<?php

  if(isset($_POST['resource_submit'])) {
    unset($_POST['resource_submit']);

    $resource_mod = Resource::instance( getValue('id') );

    $resource_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $resource_mod->check_valid();

    if($tab_error === true) {
      $resource_mod->save();

      Page::set_message( 'Record successfuly saved' );
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM