<?php

  if(isset($_POST['member_submit'])) {
    if(isset($_REQUEST['id']) && $_REQUEST['id'] != '') {
      $id = $_REQUEST['id'];
    }else {
      $id = null;
    }
    $member_mod = new Member($id);

    if(isset($_POST['password_admin'])) {
      $member_mod->set_password($_POST['password_admin'], false);
      unset( $_POST['password_admin'] );
    }

    $member_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $member_mod->check_valid();

    if($tab_error === true) {
      $member_mod->db_save();

      if( is_null( $id ) ) {
        page_redirect('admin_member');
      }else {
        page_redirect('admin_member_view', array('id' => $member_mod->get_id()));
      }
    }
  }


?>