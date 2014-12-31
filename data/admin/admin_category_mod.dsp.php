<?php

  if(!isset($category_mod)) {
    $category_mod = Category::instance( getValue('id') );
  }

  if(is_null($category_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = 'Category : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$category_mod->get_id();
    $PAGE_TITRE = 'Category : Mettre à jour les informations pour "'.$category_mod->get_name().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(Category::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'objet Category ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">
    <div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.$form_url.'" method="post">
        '.$category_mod->html_get_form();

  // CUSTOM

  //Custom content

  // /CUSTOM

        echo '
        <p>'.HTMLHelper::submit('category_submit', 'Sauvegarder les changements').'</p>
      </form>';

      if( $category_mod->id ) {
        echo '
      <p><a href="'.get_page_url('admin_category_view', true, array('id' => $category_mod->get_id())).'">Revenir à la page de l\'objet "'.$category_mod->get_name().'"</a></p>';
      }
      echo '
      <p><a href="'.get_page_url('admin_category').'">Revenir à la liste des objets Category</a></p>
    </div>
  </div>';