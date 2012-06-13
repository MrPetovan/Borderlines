<?php

  if(isset($_POST['vertex_submit'])) {
    unset($_POST['vertex_submit']);

    $vertex_mod = Vertex::instance( getValue('id') );

    $vertex_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $vertex_mod->check_valid();

    if($tab_error === true) {
      $vertex_mod->save();

      Page::set_message( 'Record successfuly saved' );
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM