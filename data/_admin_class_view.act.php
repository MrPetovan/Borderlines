  $<?php echo $class_db_identifier ?> = <?php echo $class_php_identifier ?>::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
 <?php
  foreach( $sub_tables as $sub_table_info ) {
    $sub_table = $sub_table_info['table'];
    $sub_table_field = $sub_table_info['field'];
    $sub_table_pk = $primary_keys[ $sub_table ];
    $sub_table_pk_clean = array_diff($sub_table_pk, array($sub_table_field));
    $sub_table_columns = $table_columns_list[ $sub_table ];
    $sub_table_columns_clean = $sub_table_columns;
    unset($sub_table_columns_clean[ $sub_table_field] );
    $sub_table_description = $table_description[ $sub_table ];
?>
      case 'set_<?php echo $sub_table?>':
        if( $<?php echo $class_db_identifier ?>->id ) {
          $flag_set_<?php echo $sub_table?> = $<?php echo $class_db_identifier ?>->set_<?php echo $sub_table?>(<?php
    $param_list = array();
    foreach( $sub_table_columns_clean as $field_name => $field ) {
      $param_list[] = '
            getValue(\''.$field_name.'\')';
    }
    echo implode(',', $param_list);
?>

          );
        }
        break;
      case 'del_<?php echo $sub_table?>':
        if( $<?php echo $class_db_identifier ?>->id ) {
          $flag_del_<?php echo $sub_table?> = $<?php echo $class_db_identifier ?>->del_<?php echo $sub_table?>(<?php
    $param_list = array();
    foreach( $sub_table_pk_clean as $field_name ) {
      $param_list[] = '
            getValue(\''.$field_name.'\')';
    }
    echo implode(',', $param_list);
?>

          );
        }
        break;
<?php
  }
?>
      default:
        break;
    }
  }
  
  // CUSTOM

  //Custom content

  // /CUSTOM
