<?php

  if(isset($_POST['criterion_submit'])) {
    unset($_POST['criterion_submit']);

    $criterion_mod = Criterion::instance( getValue('id') );

    $criterion_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $criterion_mod->check_valid();

    if($tab_error === true) {
      $criterion_mod->save();

      Page::set_message( 'Record successfuly saved' );
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM