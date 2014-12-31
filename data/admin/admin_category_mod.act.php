<?php

  if(isset($_POST['category_submit'])) {
    unset($_POST['category_submit']);

    $category_mod = Category::instance( getValue('id') );

    $category_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $category_mod->check_valid();

    if($tab_error === true) {
      $category_mod->save();

      Page::set_message( 'Record successfuly saved' );
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM