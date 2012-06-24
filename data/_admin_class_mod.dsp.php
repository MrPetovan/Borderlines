  include_once('data/static/html_functions.php');

  if(!isset($<?php echo $class_db_identifier ?>_mod)) {
    $<?php echo $class_db_identifier ?>_mod = <?php echo $class_php_identifier ?>::instance( getValue('id') );
  }

  if(is_null($<?php echo $class_db_identifier ?>_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = '<?php echo $class_name?> : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$<?php echo $class_db_identifier ?>_mod->get_id();
    $PAGE_TITRE = '<?php echo $class_name?> : Mettre à jour les informations pour "'.$<?php echo $class_db_identifier ?>_mod->get_<?php echo $name_field?>().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(<?php echo $class_php_identifier ?>::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'objet <?php echo $class_name ?> ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">';
    admin_menu(PAGE_CODE);

    echo '<div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.$form_url.'" method="post">
        '.$<?php echo $class_db_identifier ?>_mod->html_get_form();

  // CUSTOM

  //Custom content

  // /CUSTOM

        echo '
        <p>'.HTMLHelper::submit('<?php echo $class_db_identifier ?>_submit', 'Sauvegarder les changements').'</p>
      </form>';

      if( $<?php echo $class_db_identifier ?>_mod->id ) {
        echo '
      <p><a href="'.get_page_url('admin_<?php echo $class_db_identifier?>_view', true, array('id' => $<?php echo $class_db_identifier ?>_mod->get_id())).'">Revenir à la page de l\'objet "'.$<?php echo $class_db_identifier ?>_mod->get_<?php echo $name_field?>().'"</a></p>';
      }
      echo '
      <p><a href="'.get_page_url('admin_<?php echo $class_db_identifier?>').'">Revenir à la liste des objets <?php echo $class_name ?></a></p>
    </div>
  </div>';