<?php
  include_once('data/static/html_functions.php');

  if(!isset($world_mod)) {
    $world_mod = World::instance( getValue('id') );
  }

  if(is_null($world_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = 'World : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$world_mod->get_id();
    $PAGE_TITRE = 'World : Mettre à jour les informations pour "'.$world_mod->get_name().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(World::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'objet World ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">';
    admin_menu(PAGE_CODE);

    echo '<div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.$form_url.'" method="post">
        '.$world_mod->html_get_form();

  // CUSTOM

  //Custom content

  // /CUSTOM

        echo '
        <p>'.HTMLHelper::submit('world_submit', 'Sauvegarder les changements').'</p>
      </form>';

      if( $world_mod->id ) {
        echo '
      <p><a href="'.get_page_url('admin_world_view', true, array('id' => $world_mod->get_id())).'">Revenir à la page de l\'objet "'.$world_mod->get_name().'"</a></p>';
      }
      echo '
      <p><a href="'.get_page_url('admin_world').'">Revenir à la liste des objets World</a></p>
    </div>
  </div>';