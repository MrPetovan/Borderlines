<?php
  $PAGE_TITRE = 'Forgotten password';
?>
<h2>Forgotten password</h2>
<?php
  if(isset($error_msg) && $error_msg != '') echo '
    <p class="error">'.$error_msg.'</p>';
  if(isset($succes_msg) && $succes_msg != '') {
    echo '<p class="message">Un email avec votre nouveau mot de passe a été envoyé, vérifiez que '.ADMIN_EMAIL.' est dans votre liste d\'expéditeurs autorisés.</p>';
  }else {
    echo '
    <p>Saisissez l\'email que vous avez utilisé lors de votre inscription à '.SITE_NAME.' pour recevoir un nouveau mot de passe.</p>
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
      <p class="center">
        '.HTMLHelper::genererInputText('email', null, array(), "Email").'
        '.HTMLHelper::genererInputSubmit('password_forgotten', 'Envoyer').'
      </p>
    </form>';
  }
?>