<?php
  $PAGE_TITRE = 'Oubli de mot de passe';
?>
  <div class="texte_header">
    <p class="bandeau">Connectez-vous</p>
    <img src="<?php echo IMG?>img_html/13login_header.jpg"/>
    <div class="edito">
      <h2>Accédez à votre compte</h2>
      <p>Accéder à votre compte ? Rien de plus simple ! Entrez votre email et votre mot de passe, ou, si vous n'êtes pas encore membre, créez votre compte.</p>
    </div>
  </div>
  <div class="texte_contenu">
    <div class="texte_texte">
      <h3>Accédez à votre compte</h3>
      <h4>Rappel de vos identifiants</h4>
<?php
  if(isset($error_msg) && $error_msg != '') echo '
    <p class="error">'.$error_msg.'</p>';
  if(isset($succes_msg) && $succes_msg != '') {
    echo '<p class="message">Un email avec votre nouveau mot de passe a été envoyé, vérifiez que '.ADMIN_EMAIL.' est dans votre liste d\'expéditeurs autorisés.</p>';
  }else {
    echo '
    <p>Saisissez l\'email que vous avez utilisé lors de votre inscription à Geo pour recevoir un nouveau mot de passe.</p>
    <br/>
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
      <p class="center">
        '.HTMLHelper::genererInputText('email', null, array(), "Email").'
        '.HTMLHelper::genererInputSubmit('password_forgotten', 'Envoyer').'
      </p>
    </form>';
  }
?>
    </div>
  </div>