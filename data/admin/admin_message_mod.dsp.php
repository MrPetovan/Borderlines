<?php

  if(!isset($message_mod)) {
    $message_mod = Message::instance( getValue('id') );
  }

  if(is_null($message_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = 'Message : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$message_mod->get_id();
    $PAGE_TITRE = 'Message : Mettre à jour les informations pour "'.$message_mod->get_id().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(Message::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'objet Message ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">
    <div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.$form_url.'" method="post">
        '.$message_mod->html_get_form();

  // CUSTOM

  //Custom content

  // /CUSTOM

        echo '
        <p>'.HTMLHelper::submit('message_submit', 'Sauvegarder les changements').'</p>
      </form>';

      if( $message_mod->id ) {
        echo '
      <p><a href="'.get_page_url('admin_message_view', true, array('id' => $message_mod->get_id())).'">Revenir à la page de l\'objet "'.$message_mod->get_id().'"</a></p>';
      }
      echo '
      <p><a href="'.get_page_url('admin_message').'">Revenir à la liste des objets Message</a></p>
    </div>
  </div>';