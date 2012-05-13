  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['<?php echo $class_db_identifier ?>_id']) && is_array($_POST['<?php echo $class_db_identifier ?>_id'])) {
        foreach($_POST['<?php echo $class_db_identifier ?>_id'] as $<?php echo $class_db_identifier ?>_id) {

          $<?php echo $class_db_identifier ?> = DBObject::instance('<?php echo $class_php_identifier ?>', $<?php echo $class_db_identifier ?>_id);
          switch($_POST['action']) {
            case 'delete' :
              $<?php echo $class_db_identifier ?>->db_delete();
              break;
          }
        }
      }
    }
  }