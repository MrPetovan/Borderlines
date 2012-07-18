<?php
  $PAGE_TITRE = 'Login';

  if( $PAGE_CODE == 'logout' ) {
    Member::del_current_user_id();
    Page::redirect('accueil');
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
<h2>Log In</h2>
<p>Don't have an account yet ? <a href="<?php echo Page::get_url('register')?>">Sign Up now</a> !</p>
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
  <a href="<?php echo Page::get_url('rappel-identifiants')?>">Forgotten password ?</a>
</p>
<!--<p>
  <label>&nbsp;</label>
  <?php echo HTMLHelper::genererInputCheckBox('remember_me', '1', null, array('label_position' => 'right'), "Se rappeler de moi");?>
</p>-->
<p class="align">
  <label>&nbsp;</label>
  <?php echo HTMLHelper::genererInputSubmit('submit_login', 'Se connecter');?>
</p>
</form>