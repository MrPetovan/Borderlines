<?php
  include_once('data/static/html_functions.php');

  if(!isset($game_mod)) {
    $game_mod = Game::instance( getValue('id') );
  }

  if(is_null($game_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = 'Game : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$game_mod->get_id();
    $PAGE_TITRE = 'Game : Mettre à jour les informations pour "'.$game_mod->get_name().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(Game::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'objet Game ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">';
    admin_menu(PAGE_CODE);

    echo '<div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.$form_url.'" method="post">
        '.$game_mod->html_get_form(MEMBER_FORM_ADMIN);

  // CUSTOM

  //Custom content

  // /CUSTOM

        echo '
        <p>'.HTMLHelper::submit('game_submit', 'Sauvegarder les changements').'</p>
      </form>';

      if( $game_mod->id ) {
        echo '
      <p><a href="'.get_page_url('admin_game_view', true, array('id' => $game_mod->get_id())).'">Revenir à la page de l\'objet "'.$game_mod->get_name().'"</a></p>';
      }
      echo '
      <p><a href="'.get_page_url('admin_game').'">Revenir à la liste des objets Game</a></p>
    </div>
  </div>';