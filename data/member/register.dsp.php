<?php
  $PAGE_TITRE = "Enregistrement";

  $show_formulaire = true;
  $show_formulaire_member = true;
  $html_msg = '';

  if($member = Member::get_logged_user()) {
    $member = Member::get_current_user();

    //Admin ou Compte entreprise
    if($member->get_niveau() > 0){
      page_redirect('dashboard');
    }
  }

  if( !isset( $member_register )) {
    $member_register = DBObject::instance('Member');
  }

  if(isset($_SESSION['tab_error'])) {
    $tab_error = $_SESSION['tab_error'];
    unset($_SESSION['tab_error']);
  }
  if(isset($tab_error)) {
    if(is_array($tab_error)) {
      if(count($tab_error) != 0) {
        foreach ($tab_error as $error) {
          $tab_msg[] = Member::get_message_erreur($error);
        }
        $tab_msg = array_unique($tab_msg);
        $js_msg = '';
        $html_msg = '<div class="error">';
        foreach ($tab_msg as $msg_error) {
          $js_msg .= '- '.strip_tags($msg_error).'\n';
          $html_msg .= '<p>'.$msg_error.'</p>';
        }
        $html_msg .= '</div>
        <script type="text/javascript">
          alert("Le formulaire a rencontré les erreurs suivantes :\n'.$js_msg.'")
        </script';
      }
    }
  }

?>
<?php echo $html_msg ?>
<form action="<?php echo get_page_url('register')?>" method="post" class="formulaire register">
<?php echo $member_register->html_get_register_form()?>
<p class="center"><label>&nbsp;</label><?php echo HTMLHelper::genererInputSubmit('submit_register', 'Je m\'inscris' );?></p>
</form>