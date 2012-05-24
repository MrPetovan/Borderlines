<?php

  if(isset($_POST['order_type_submit'])) {
    unset($_POST['order_type_submit']);

    $order_type_mod = Order_Type::instance( getValue('id') );

    $order_type_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $order_type_mod->check_valid();

    if($tab_error === true) {
      $order_type_mod->db_save();

      /*echo '<a href="'.Page::get_page_url(PAGE_CODE, false, array('id' => $order_type_mod->get_id())).'">Lien '.Page::get_page_url(PAGE_CODE, false, array('id' => $order_type_mod->get_id())).'</a>';
      die();*/
      //page_redirect(PAGE_CODE, array('id' => $order_type_mod->get_id()));
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM