<?php

  if(isset($_POST['save_profile']) || isset($_POST['save_profile_x'])) {
    if(isset($_POST['type_form'])) {
      $type_form = $_POST['type_form'];
    }

    $flags = 0;
    if(PAGE_CODE == 'mon-compte-infos') {
      $flags = MEMBER_COMPLETE_INFO_CHECK;
    }

    $member = new Member(Member::get_current_user_id());

    $tab_error = $member->load_from_html_form($_POST, $_FILES);

    if(($tab_error = $member->db_save($flags)) === true) {
      if(isset($_POST['password'])) {
        $clear_password = $member->get_password();
        $member->set_password($clear_password, false);
        $member->db_save();
        php_mail($member->get_email(), 'Geo | Modifications de vos identifiants', $member->get_email_modif_identifiants( $clear_password ), true);
      }

      if(isset($_POST['change_inscr'])) {
        if(
           ($_POST['inscr_newsletter_old'] != $member->get_inscr_newsletter() && $member->get_inscr_newsletter() == 0)
          || ($_POST['inscr_partner_old'] != $member->get_inscr_partner() && $member->get_inscr_partner() == 0))
        {
          php_mail($member->get_email(), 'Geo | Désinscription', $member->get_email_desinscription(), true);
        }

        if($_POST['inscr_newsletter_old'] != $member->get_inscr_newsletter() && $member->get_inscr_newsletter() == 0)
        {
          php_mail($member->get_email(), "Geo | Inscription à la newsletter", $member->get_email_newsletter(), true);
        }
      }
      $_POST = array();
    }else {
      $_SESSION['tab_error'] = $tab_error;
    }
  }
?>