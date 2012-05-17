<?php

  if(isset($_POST['world_submit'])) {
    unset($_POST['world_submit']);

    $world_mod = World::instance( getValue('id') );

    $world_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $world_mod->check_valid();

    if($tab_error === true) {
      $world_mod->db_save();

      /*echo '<a href="'.Page::get_page_url(PAGE_CODE, false, array('id' => $world_mod->get_id())).'">Lien '.Page::get_page_url(PAGE_CODE, false, array('id' => $world_mod->get_id())).'</a>';
      die();*/
      //page_redirect(PAGE_CODE, array('id' => $world_mod->get_id()));
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM