<?php

  if($member = Member::get_logged_user()) {
    //$member = Member::get_current_user();

    page_redirect('dashboard');
  }

  if( !isset( $member_register )) {
    $member_register = Member::instance();
  }

  if(isset($_POST['submit_register']) || isset($_POST['submit_register_x'])) {
    //Visiteur (!id)
    // + On passe le niveau à 1
    // - On vérifie les informations
    // - On créé

    //Prospect (id, niveau == 0)
    // + On passe le niveau à 1
    // - On vérifie les informations
    // - On met à jour

    //Membre (id, niveau == 1)
    // - On ne fait rien

    $save_member = false;
    $tab_error = true;
    if(!($member_register = Member::get_logged_user())) {
      $save_member = true;
      if($member_register = Member::get_current_user()) {
        //Prospect, pas de vérification e-mail
        $check_flags = MEMBER_COMPLETE_INFO_CHECK;
      }else {
        $check_flags = MEMBER_NEW_USER_CHECK | MEMBER_COMPLETE_INFO_CHECK;
        $member_register = new Member();
      }

      $member_register->niveau = 0;
      $member_register->load_from_html_form($_POST, $_FILES);

      $tab_error = $member_register->check_valid($check_flags);
    }

    if($tab_error === true) {

      if($save_member) {
        $clear_password = $member_register->get_password();
        $member_register->set_password($clear_password, false);
        $member_register->db_save();
        php_mail($member_register->get_email(), SITE_NAME.' | Confirmation d\'inscription', $member_register->get_email_confirmation( $clear_password ), true);
        Member::set_current_user_id( $member_register->get_id() );
        Page::redirect('dashboard');
      }

      $_POST = array();
      unset($_SESSION['tab_error'], $tab_error);
    }
  }
?>