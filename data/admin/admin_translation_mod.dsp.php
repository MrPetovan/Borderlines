<?php

  if(!isset($translation_mod)) {
    $translation_mod = Translation::instance( getValue('id') );
  }

  if(is_null($translation_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = 'Translation : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$translation_mod->get_id();
    $PAGE_TITRE = 'Translation : Mettre à jour les informations pour "'.$translation_mod->get_id().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(Translation::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'objet Translation ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">
    <div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.$form_url.'" method="post">
        '.$translation_mod->html_get_form();

  // CUSTOM

  //Custom content

  // /CUSTOM

        echo '
        <p>'.HTMLHelper::submit('translation_submit', 'Sauvegarder les changements').'</p>
      </form>';

      if( $translation_mod->id ) {
        echo '
      <p><a href="'.get_page_url('admin_translation_view', true, array('id' => $translation_mod->get_id())).'">Revenir à la page de l\'objet "'.$translation_mod->get_id().'"</a></p>';
      }
      echo '
      <p><a href="'.get_page_url('admin_translation').'">Revenir à la liste des objets Translation</a></p>
    </div>
  </div>';