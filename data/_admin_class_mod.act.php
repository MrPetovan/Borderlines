
  if(isset($_POST['<?php echo $class_db_identifier ?>_submit'])) {
    unset($_POST['<?php echo $class_db_identifier ?>_submit']);

    $<?php echo $class_db_identifier ?>_mod = <?php echo $class_php_identifier ?>::instance( getValue('id') );

    $<?php echo $class_db_identifier ?>_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $<?php echo $class_db_identifier ?>_mod->check_valid();

    if($tab_error === true) {
      $<?php echo $class_db_identifier ?>_mod->save();

      Page::set_message( 'Record successfuly saved' );
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM