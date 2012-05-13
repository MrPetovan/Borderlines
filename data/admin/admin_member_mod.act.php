<?php

  if(isset($_POST['member_submit'])) {
    if(isset($_POST['id']) && $_POST['id'] != '') {
      $id = $_POST['id'];
    }else {
      $id = null;
    }
    $member_mod = new Member($id);

    $member_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $member_mod->check_valid();

    if($tab_error === true) {
      $member_mod->db_save();

      /*echo '<a href="'.Page::get_page_url(PAGE_CODE, false, array('id' => $member_mod->get_id())).'">Lien '.Page::get_page_url(PAGE_CODE, false, array('id' => $member_mod->get_id())).'</a>';
      die();*/
      //page_redirect(PAGE_CODE, array('id' => $member_mod->get_id()));
    }
  }


?>