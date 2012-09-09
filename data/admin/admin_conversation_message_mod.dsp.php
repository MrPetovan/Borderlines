<?php
  include_once('data/static/html_functions.php');

  if(!isset($conversation_message_mod)) {
    $conversation_message_mod = Conversation_Message::instance( getValue('id') );
  }

  if(is_null($conversation_message_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = 'Conversation Message : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$conversation_message_mod->get_id();
    $PAGE_TITRE = 'Conversation Message : Mettre à jour les informations pour "'.$conversation_message_mod->get_id().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(Conversation_Message::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'objet Conversation Message ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">';
    admin_menu(PAGE_CODE);

    echo '<div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.$form_url.'" method="post">
        '.$conversation_message_mod->html_get_form();

  // CUSTOM

  //Custom content

  // /CUSTOM

        echo '
        <p>'.HTMLHelper::submit('conversation_message_submit', 'Sauvegarder les changements').'</p>
      </form>';

      if( $conversation_message_mod->id ) {
        echo '
      <p><a href="'.get_page_url('admin_conversation_message_view', true, array('id' => $conversation_message_mod->get_id())).'">Revenir à la page de l\'objet "'.$conversation_message_mod->get_id().'"</a></p>';
      }
      echo '
      <p><a href="'.get_page_url('admin_conversation_message').'">Revenir à la liste des objets Conversation Message</a></p>
    </div>
  </div>';