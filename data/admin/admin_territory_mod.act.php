<?php

  if(isset($_POST['territory_submit'])) {
    unset($_POST['territory_submit']);

    $territory_mod = Territory::instance( getValue('id') );

    $territory_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $territory_mod->check_valid();

    if($tab_error === true) {
      $territory_mod->db_save();

      /*echo '<a href="'.Page::get_page_url(PAGE_CODE, false, array('id' => $territory_mod->get_id())).'">Lien '.Page::get_page_url(PAGE_CODE, false, array('id' => $territory_mod->get_id())).'</a>';
      die();*/
      //page_redirect(PAGE_CODE, array('id' => $territory_mod->get_id()));
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM