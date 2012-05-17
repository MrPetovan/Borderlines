<?php
  include_once('data/static/html_functions.php');

  if(!isset($criterion_mod)) {
    $criterion_mod = Criterion::instance( getValue('id') );
  }

  if(is_null($criterion_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = 'Criterion : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$criterion_mod->get_id();
    $PAGE_TITRE = 'Criterion : Mettre à jour les informations pour "'.$criterion_mod->get_name().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(Criterion::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'objet Criterion ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">';
    admin_menu(PAGE_CODE);

    echo '<div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.$form_url.'" method="post">
        '.$criterion_mod->html_get_form(MEMBER_FORM_ADMIN);

  // CUSTOM

  //Custom content

  // /CUSTOM

        echo '
        <p>'.HTMLHelper::submit('criterion_submit', 'Sauvegarder les changements').'</p>
      </form>';

      if( $criterion_mod->id ) {
        echo '
      <p><a href="'.get_page_url('admin_criterion_view', true, array('id' => $criterion_mod->get_id())).'">Revenir à la page de l\'objet "'.$criterion_mod->get_name().'"</a></p>';
      }
      echo '
      <p><a href="'.get_page_url('admin_criterion').'">Revenir à la liste des objets Criterion</a></p>
    </div>
  </div>';