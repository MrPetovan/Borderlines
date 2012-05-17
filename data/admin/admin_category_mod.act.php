<?php

  if(isset($_POST['category_submit'])) {
    unset($_POST['category_submit']);

    $category_mod = Category::instance( getValue('id') );

    $category_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $category_mod->check_valid();

    if($tab_error === true) {
      $category_mod->db_save();

      /*echo '<a href="'.Page::get_page_url(PAGE_CODE, false, array('id' => $category_mod->get_id())).'">Lien '.Page::get_page_url(PAGE_CODE, false, array('id' => $category_mod->get_id())).'</a>';
      die();*/
      //page_redirect(PAGE_CODE, array('id' => $category_mod->get_id()));
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM