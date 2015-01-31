<?php
  $PAGE_TITRE = 'Envoyer à un ami';

  var_dump(Member::get_current_user());

  if(!isset($member_envoyer_ami)) {
    //Prospect
    if($member_envoyer_ami = Member::get_current_user()) {
    }else {
      $member_envoyer_ami = new Member();
    }
  }

  if(!isset($member_envoyer_ami_list)) {
    $member_envoyer_ami_list = array_fill(0, 5, new Member());
  }

?>
    <h3>Envoyer à un ami</h3>
    <form class="formulaire" action="<?php echo get_page_url(PAGE_CODE)?>" method="POST">
      <div class="field form-group"><?php echo HTMLHelper::genererInputText('email', $member_envoyer_ami->get_email(), array(), 'Votre email')?></p>
<?php
  foreach ($member_envoyer_ami_list as $key => $item_ami_list) {
?>
      <div class="field form-group"><?php echo HTMLHelper::genererInputText('email_ami[]', $item_ami_list->get_email(), array(), 'Email d\'une amie')?></p>
<?php
  }
?>
      <p class="btn_center"><?php echo HTMLHelper::genererInputSubmit('submit_envoyer_ami', 'Envoyer' )?></p>
    </form>