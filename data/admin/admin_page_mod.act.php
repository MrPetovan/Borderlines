<?php
  if(isset($_POST['page_submit'])) {
    if(isset($_POST['id']) && $_POST['id'] != '') {
      $id = $_POST['id'];
    }else {
      $id = null;
    }

    $page_mod = DBObject::instance('Page', $id);

    $tab_error = $page_mod->load_from_html_form($_POST, $_FILES);

    $_SESSION['tab_error'] = $tab_error;

    if(count($tab_error) == 0) {
      $page_mod->db_save();

      $_POST = array();
      page_redirect(PAGE_CODE, array('id' => $page_mod->get_id()));
    }
  }


?>