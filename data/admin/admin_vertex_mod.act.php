<?php

  if(isset($_POST['vertex_submit'])) {
    unset($_POST['vertex_submit']);

    $vertex_mod = Vertex::instance( getValue('id') );

    $vertex_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $vertex_mod->check_valid();

    if($tab_error === true) {
      $vertex_mod->db_save();

      /*echo '<a href="'.Page::get_page_url(PAGE_CODE, false, array('id' => $vertex_mod->get_id())).'">Lien '.Page::get_page_url(PAGE_CODE, false, array('id' => $vertex_mod->get_id())).'</a>';
      die();*/
      //page_redirect(PAGE_CODE, array('id' => $vertex_mod->get_id()));
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM