  include_once('data/static/html_functions.php');

  if(!isset($<?php echo $class_db_identifier ?>_mod)) {
    $<?php echo $class_db_identifier ?>_mod = DBObject::instance('<?php echo $class_php_identifier ?>', getValue('id'));
  }

  var_debug($<?php echo $class_db_identifier ?>_mod);

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
      <form class="formulaire" action="'.get_page_url(PAGE_CODE, true, array('id' => $<?php echo $class_db_identifier ?>_mod->get_id())).'" method="post">
        '.$<?php echo $class_db_identifier ?>_mod->html_get_form(MEMBER_FORM_ADMIN).'
        <p>'.HTMLHelper::submit('<?php echo $class_db_identifier ?>_submit', 'Sauvegarder les changements').'</p>
      </form>
    </div>
  </div>';