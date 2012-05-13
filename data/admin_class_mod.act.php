  if(isset($_POST['<?php echo $class_db_identifier ?>_submit'])) {
    unset($_POST['<?php echo $class_db_identifier ?>_submit']);

    $<?php echo $class_db_identifier ?>_mod = DBObject::instance('<?php echo $class_php_identifier ?>', getValue('id'));

    $<?php echo $class_db_identifier ?>_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $<?php echo $class_db_identifier ?>_mod->check_valid();

    if($tab_error === true) {
      $<?php echo $class_db_identifier ?>_mod->db_save();

      /*echo '<a href="'.Page::get_page_url(PAGE_CODE, false, array('id' => $<?php echo $class_db_identifier ?>_mod->get_id())).'">Lien '.Page::get_page_url(PAGE_CODE, false, array('id' => $<?php echo $class_db_identifier ?>_mod->get_id())).'</a>';
      die();*/
      //page_redirect(PAGE_CODE, array('id' => $<?php echo $class_db_identifier ?>_mod->get_id()));
    }
  }