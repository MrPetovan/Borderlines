<?php
  include_once('data/static/html_functions.php');

  if(!isset($territory_mod)) {
    $territory_mod = Territory::instance( getValue('id') );
  }

  if(is_null($territory_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = 'Territory : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$territory_mod->get_id();
    $PAGE_TITRE = 'Territory : Mettre à jour les informations pour "'.$territory_mod->get_name().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(Territory::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'objet Territory ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">';
    admin_menu(PAGE_CODE);

    echo '<div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.$form_url.'" method="post">
        '.$territory_mod->html_get_form(MEMBER_FORM_ADMIN);

  // CUSTOM

  //Custom content

  // /CUSTOM

        echo '
        <p>'.HTMLHelper::submit('territory_submit', 'Sauvegarder les changements').'</p>
      </form>';

      if( $territory_mod->id ) {
        echo '
      <p><a href="'.get_page_url('admin_territory_view', true, array('id' => $territory_mod->get_id())).'">Revenir à la page de l\'objet "'.$territory_mod->get_name().'"</a></p>';
      }
      echo '
      <p><a href="'.get_page_url('admin_territory').'">Revenir à la liste des objets Territory</a></p>
    </div>
  </div>';