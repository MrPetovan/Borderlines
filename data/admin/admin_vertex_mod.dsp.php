<?php
  include_once('data/static/html_functions.php');

  if(!isset($vertex_mod)) {
    $vertex_mod = Vertex::instance( getValue('id') );
  }

  if(is_null($vertex_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = 'Vertex : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$vertex_mod->get_id();
    $PAGE_TITRE = 'Vertex : Mettre à jour les informations pour "'.$vertex_mod->get_name().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(Vertex::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'objet Vertex ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">';
    admin_menu(PAGE_CODE);

    echo '<div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.$form_url.'" method="post">
        '.$vertex_mod->html_get_form();

  // CUSTOM

  //Custom content

  // /CUSTOM

        echo '
        <p>'.HTMLHelper::submit('vertex_submit', 'Sauvegarder les changements').'</p>
      </form>';

      if( $vertex_mod->id ) {
        echo '
      <p><a href="'.get_page_url('admin_vertex_view', true, array('id' => $vertex_mod->get_id())).'">Revenir à la page de l\'objet "'.$vertex_mod->get_name().'"</a></p>';
      }
      echo '
      <p><a href="'.get_page_url('admin_vertex').'">Revenir à la liste des objets Vertex</a></p>
    </div>
  </div>';