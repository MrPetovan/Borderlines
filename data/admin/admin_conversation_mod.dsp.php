<?php

  if(!isset($conversation_mod)) {
    $conversation_mod = Conversation::instance( getValue('id') );
  }

  if(is_null($conversation_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = 'Conversation : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$conversation_mod->get_id();
    $PAGE_TITRE = 'Conversation : Mettre à jour les informations pour "'.$conversation_mod->get_id().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(Conversation::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'objet Conversation ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">
    <div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.$form_url.'" method="post">
        '.$conversation_mod->html_get_form();

  // CUSTOM

  //Custom content

  // /CUSTOM

        echo '
        <p>'.HTMLHelper::submit('conversation_submit', 'Sauvegarder les changements').'</p>
      </form>';

      if( $conversation_mod->id ) {
        echo '
      <p><a href="'.get_page_url('admin_conversation_view', true, array('id' => $conversation_mod->get_id())).'">Revenir à la page de l\'objet "'.$conversation_mod->get_id().'"</a></p>';
      }
      echo '
      <p><a href="'.get_page_url('admin_conversation').'">Revenir à la liste des objets Conversation</a></p>
    </div>
  </div>';