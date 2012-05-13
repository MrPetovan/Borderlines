<?php
  if(isset($_POST['submit_envoyer_ami']) || isset($_POST['submit_envoyer_ami_x'])) {
    $save_ok = true;
    $tab_error = array();

    if($member_envoyer_ami = Member::get_current_user()) {
    }else {
      $member_envoyer_ami = new Member();
      $member_envoyer_ami->set_email($_POST['email']);
    }
    if(isset($_POST['email_ami'])) {
      foreach ($_POST['email_ami'] as $email_ami) {
        $member = new Member();
        $member->set_email($email_ami);
        $member_envoyer_ami_list[] = $member;
        $tab_error_tmp = $member->check_valid(MEMBER_NEW_USER_CHECK);
        $save_ok = $save_ok && $tab_error_tmp === true;
        $tab_error_list[] = $tab_error_tmp;
      }



    }
  }
?>