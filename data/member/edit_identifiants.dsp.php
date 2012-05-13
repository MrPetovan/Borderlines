<?php
  $PAGE_TITRE = 'Modifier mes identifiants';

  include_once('data/member/html_functions.php');

  $current_user = new Member(Member::get_current_user_id());
  if(isset($tab_error)) {
    if($tab_error === true) {
      if(isset($type_form) && $type_form == "save_email") {
        $html_msg = '<div class="msg">Votre nouvel email ('.$_POST['email'].') a été correctement enregistré.</div>';
      }else {
        $html_msg = '<div class="msg">Votre mot de passe a été correctement enregistré.</div>';
      }
    }else {

      foreach ($tab_error as $error) {
        $tab_msg[] = Member::get_message_erreur($error);
      }
      var_debug($tab_msg);
      $tab_msg = array_unique($tab_msg);
      $html_msg = '<div class="error">';
      foreach ($tab_msg as $msg_error) {
        $html_msg .= '
  <p>'.wash_utf8($msg_error).'</p>';
      }
      $html_msg .= '</div>';
    }
  }

?>
<div class="texte_header">
  <p class="bandeau">Mon compte</p>
  <img src="<?php echo IMG?>img_html/13login_header.jpg"/>
  <div class="edito">
    <h2>Mon compte</h2>
    <p>Retrouvez ici toutes les informations concernant vos informations.</p>
  </div>
</div>
<div class="texte_contenu">
<?php echo mon_compte_menu(PAGE_CODE) ?>
  <div class="texte_texte">
    <h3>Modifier mes identifiants</h3>
    <h4>Changer mon email</h4>
    <p class="texte_intro">Laissez les champs vides si vous ne voulez pas changer votre email.</p>
<?php
  if(isset($type_form) && $type_form == "save_email" && isset($html_msg)) {
    echo $html_msg;
  }
?>
    <form id="edit_email_form" action="<?php echo get_page_url(get_current_page())?>" method="post">
      <input type="hidden" name="type_form" value="save_email"/>
      <div class="informations formulaire">
        <p class="field"><?php echo HTMLHelper::genererInputText('email', '', array(), "Nouvel email");?></p>
        <p class="field"><?php echo HTMLHelper::genererInputText('email2', '', array(), "Confirmer");?></p><p></p>
      </div>
      <p class="right"><label>&nbsp;</label><?php echo HTMLHelper::genererInputSubmit('save_profile', "Save changes" );?></p>
    </form>
    <h4>Changer mon mot de passe</h4>
    <p class="texte_intro">Laissez les champs vides si vous ne voulez pas changer votre mot de passe.</p>
<?php
  if(isset($type_form) && $type_form == "save_password" && isset($html_msg)) {
    echo $html_msg;
  }
?>
    <form id="edit_password_form" action="<?php echo get_page_url(get_current_page())?>" method="post">
      <input type="hidden" name="type_form" value="save_password"/>
      <div class="informations formulaire">
        <p class="field"><?php echo HTMLHelper::genererInputPassword('password', '', array(), "Nouveau mot de passe");?></p>
        <p class="field"><?php echo HTMLHelper::genererInputPassword('password2', '', array(), "Confirmer");?></p><p></p>
      </div>
      <p class="right"><label>&nbsp;</label><?php echo HTMLHelper::genererInputSubmit('save_profile', "Save changes" );?></p>
    </form>
  </div>
</div>