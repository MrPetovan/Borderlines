<?php

  if(isset($_POST['criterion_submit'])) {
    unset($_POST['criterion_submit']);

    $criterion_mod = Criterion::instance( getValue('id') );

    $criterion_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $criterion_mod->check_valid();

    if($tab_error === true) {
      $criterion_mod->db_save();

      /*echo '<a href="'.Page::get_page_url(PAGE_CODE, false, array('id' => $criterion_mod->get_id())).'">Lien '.Page::get_page_url(PAGE_CODE, false, array('id' => $criterion_mod->get_id())).'</a>';
      die();*/
      //page_redirect(PAGE_CODE, array('id' => $criterion_mod->get_id()));
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM