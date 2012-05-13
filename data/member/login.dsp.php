<?php
  $PAGE_TITRE = 'Connectez-vous';

  if( $PAGE_CODE == 'logout' ) {
    Member::del_current_user_id();
    site_redirect();
  }

  $flag_show_form = true;
  $html_msg = '';

  if(isset($membre)) {
    if(isset($_GET['code'])) {
      $flag_show_form = false;
      if($membre !== false) {
        $html_msg = '<div class="msg">Votre compte a bien été activé.</div>';
      }else {
        $html_msg = '<div class="error">Code d\'activation invalide. Veuillez vérifier le lien dans l\'email d\'activation que vous avez reçu.</div>';
      }
    }
  }else {
    $membre = new Member();
  }

  if(isset($tab_error_register)) {
    if(is_array($tab_error_register)) {
      if(count(  $tab_error_register) == 0) {
        $html_msg = '<div class="msg">Un email d\'activation vous a été envoyé. Il contient un lien dont vous aurez besoin pour activer votre compte.</div>';

        $flag_show_form = false;
      }else {
        foreach ($tab_error_register as $error) {
          $tab_msg[] = Member::get_message_erreur($error);
        }
        $tab_msg = array_unique($tab_msg);
        $html_msg = '<div class="error">';
        foreach ($tab_msg as $msg_error) {
          $html_msg .= '
  <p>'.wash_utf8($msg_error).'</p>';
        }
        $html_msg .= '</div>';
      }
    }
  }

  if(isset($_GET['url_redirect'])) {
    $_SESSION['sess']['url_redirect'] = $_GET['url_redirect'];
  }
  if(!isset($error_code_login)) {
    $error_code_login = null;
  }
?>

  <div class="texte_header">
    <p class="bandeau">Connectez-vous</p>
    <img src="<?php echo IMG?>img_html/13login_header.jpg"/>
    <div class="edito">
      <h2>Accédez à votre compte</h2>
      <p>Accéder à votre compte ? Rien de plus simple ! Entrez votre email et votre mot de passe, ou, si vous n'êtes pas encore membre <?php echo SITE_NAME ?>, créez votre compte.</p>
    </div>
  </div>
  <div class="texte_contenu">
    <div class="texte_texte">
      <h3>Accédez à votre compte</h3>
      <h4>Déjà membre ? Identifiez-vous !</h4>
<?php
  $login_email = '';
  if(is_null($error_code_login)) {
    echo '<p class="texte_intro">Entrez votre email et votre mot de passe, ou, si vous n\'êtes pas encore membre, créez votre compte.</p>';
  }else {
    if(isset($_POST['email'])) { $login_email = $_POST['email']; }
    echo '<p class="texte_intro error">Les identifiants que vous avez entrés sont incorrects. Veuillez vérifier votre email et votre mot de passe.</p>';
  }
?>
      <form id="login_form" class="formulaire" action="<?php echo get_page_url('login')?>" method="post">
        <p class="field"><?php echo HTMLHelper::genererInputText('email', $login_email, array(), "Email")?></p>
        <p class="field"><?php echo HTMLHelper::genererInputPassword('pass', null, array(),"Mot de passe");?></p>
         <p>
          <label>&nbsp;</label>
          <a href="<?php echo get_page_url('rappel-identifiants')?>">Mot de passe perdu ?</a>
        </p>
        <!--<p>
          <label>&nbsp;</label>
          <?php echo HTMLHelper::genererInputCheckBox('remember_me', '1', null, array('label_position' => 'right'), "Se rappeler de moi");?>
        </p>-->
        <p class="align">
          <label>&nbsp;</label>
          <?php echo HTMLHelper::genererInputSubmit('submit_login', 'Se connecter');?>
        </p>
        <p>&nbsp;</p>
      </form>
   </div>
  </div>