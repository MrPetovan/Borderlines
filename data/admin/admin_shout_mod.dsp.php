<?php

  if(!isset($shout_mod)) {
    $shout_mod = Shout::instance( getValue('id') );
  }

  if(is_null($shout_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = 'Shout : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$shout_mod->get_id();
    $PAGE_TITRE = 'Shout : Mettre à jour les informations pour "'.$shout_mod->get_id().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(Shout::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'objet Shout ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">
    <div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.$form_url.'" method="post">
        '.$shout_mod->html_get_form();

  // CUSTOM

  //Custom content

  // /CUSTOM

        echo '
        <p>'.HTMLHelper::submit('shout_submit', 'Sauvegarder les changements').'</p>
      </form>';

      if( $shout_mod->id ) {
        echo '
      <p><a href="'.get_page_url('admin_shout_view', true, array('id' => $shout_mod->get_id())).'">Revenir à la page de l\'objet "'.$shout_mod->get_id().'"</a></p>';
      }
      echo '
      <p><a href="'.get_page_url('admin_shout').'">Revenir à la liste des objets Shout</a></p>
    </div>
  </div>';