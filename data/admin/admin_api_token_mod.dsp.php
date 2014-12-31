<?php

  if(!isset($api_token_mod)) {
    $api_token_mod = Api_Token::instance( getValue('id') );
  }

  if(is_null($api_token_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = 'Api Token : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$api_token_mod->get_id();
    $PAGE_TITRE = 'Api Token : Mettre à jour les informations pour "'.$api_token_mod->get_id().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(Api_Token::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'objet Api Token ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">
    <div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.$form_url.'" method="post">
        '.$api_token_mod->html_get_form();

  // CUSTOM

  //Custom content

  // /CUSTOM

        echo '
        <p>'.HTMLHelper::submit('api_token_submit', 'Sauvegarder les changements').'</p>
      </form>';

      if( $api_token_mod->id ) {
        echo '
      <p><a href="'.get_page_url('admin_api_token_view', true, array('id' => $api_token_mod->get_id())).'">Revenir à la page de l\'objet "'.$api_token_mod->get_id().'"</a></p>';
      }
      echo '
      <p><a href="'.get_page_url('admin_api_token').'">Revenir à la liste des objets Api Token</a></p>
    </div>
  </div>';