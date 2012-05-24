<?php

  if(isset($_POST['resource_submit'])) {
    unset($_POST['resource_submit']);

    $resource_mod = Resource::instance( getValue('id') );

    $resource_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $resource_mod->check_valid();

    if($tab_error === true) {
      $resource_mod->db_save();

      /*echo '<a href="'.Page::get_page_url(PAGE_CODE, false, array('id' => $resource_mod->get_id())).'">Lien '.Page::get_page_url(PAGE_CODE, false, array('id' => $resource_mod->get_id())).'</a>';
      die();*/
      //page_redirect(PAGE_CODE, array('id' => $resource_mod->get_id()));
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM