<?php

  if(isset($_POST['order_type_submit'])) {
    unset($_POST['order_type_submit']);

    $order_type_mod = Order_Type::instance( getValue('id') );

    $order_type_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $order_type_mod->check_valid();

    if($tab_error === true) {
      $order_type_mod->save();

      Page::set_message( 'Record successfuly saved' );
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM