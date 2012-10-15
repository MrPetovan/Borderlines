<?php
  include_once('data/static/html_functions.php');

  if(!isset($resource_mod)) {
    $resource_mod = Resource::instance( getValue('id') );
  }

  if(is_null($resource_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = 'Resource : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$resource_mod->get_id();
    $PAGE_TITRE = 'Resource : Mettre à jour les informations pour "'.$resource_mod->get_name().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(Resource::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'objet Resource ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">
    <div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.$form_url.'" method="post">
        '.$resource_mod->html_get_form();

  // CUSTOM

  //Custom content

  // /CUSTOM

        echo '
        <p>'.HTMLHelper::submit('resource_submit', 'Sauvegarder les changements').'</p>
      </form>';

      if( $resource_mod->id ) {
        echo '
      <p><a href="'.get_page_url('admin_resource_view', true, array('id' => $resource_mod->get_id())).'">Revenir à la page de l\'objet "'.$resource_mod->get_name().'"</a></p>';
      }
      echo '
      <p><a href="'.get_page_url('admin_resource').'">Revenir à la liste des objets Resource</a></p>
    </div>
  </div>';