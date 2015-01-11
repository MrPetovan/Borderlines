<?php

  if(!isset($player_mod)) {
    $player_mod = Player::instance( getValue('id') );
  }

  if(is_null($player_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = 'Player : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$player_mod->get_id();
    $PAGE_TITRE = 'Player : Mettre à jour les informations pour "'.$player_mod->get_name().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(Player::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'objet Player ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">
    <div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.$form_url.'" method="post">
        '.$player_mod->html_get_form();

  // CUSTOM

  //Custom content

  // /CUSTOM

        echo '
        <p>'.HTMLHelper::submit('player_submit', 'Sauvegarder les changements').'</p>
      </form>';

      if( $player_mod->id ) {
        echo '
      <p><a href="'.get_page_url('admin_player_view', true, array('id' => $player_mod->get_id())).'">Revenir à la page de l\'objet "'.$player_mod->get_name().'"</a></p>';
      }
      echo '
      <p><a href="'.get_page_url('admin_player').'">Revenir à la liste des objets Player</a></p>
    </div>
  </div>';