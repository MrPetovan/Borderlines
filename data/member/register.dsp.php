<?php
  $PAGE_TITRE = "Enregistrement";

  $show_formulaire = true;
  $show_formulaire_member = true;
  $html_msg = '';

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
        $html_msg .= '</div>';
      }
    }
  }
?>
<h2><?php echo __('Register')?></h2>
<?php echo $html_msg ?>
<form action="<?php echo get_page_url('register')?>" method="post" class="formulaire register form-horizontal">
<?php echo $member_register->html_get_abonnement_form()?>
<div class="form-group">
  <div class="col-sm-9 col-sm-offset-3">
    <?php echo HTMLHelper::genererInputSubmit('submit_register', 'Je m\'inscris', array('class' => 'btn-primary') );?>
  </div>
</form>