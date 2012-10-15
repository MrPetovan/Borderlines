<?php
  define('CUSTOM_START', "  // CUSTOM");
  define('CUSTOM_STOP', "  // /CUSTOM");

  echo '<p><a href="'.get_page_url(PAGE_CODE).'">Génération avec Custom Code</a></p>
  <p><a href="'.get_page_url(PAGE_CODE, true, array('reset' => '')).'">Génération SANS Custom Code</a></p>';

  $res = mysql_uquery('SHOW TABLES');

  $table_name_list = array();
  $table_columns_list = array();
  $primary_keys = array();
  $php_classes = array();
  $name_field_list = array();
  $reserved_class_list = array('member', 'page');
  $foreign_keys_list = array();
  $sub_tables_list = array();

  $file_list = array(
    '_class.class.php' => DIR_ROOT.'data/CLASS/CLASS.class.php',
    '_class_model.class.php' => DIR_ROOT.'data/model/CLASS_model.class.php',
    '_admin_class.dsp.php' => DIR_ROOT.'data/admin/admin_CLASS.dsp.php',
    '_admin_class.act.php' => DIR_ROOT.'data/admin/admin_CLASS.act.php',
    '_admin_class_view.dsp.php' => DIR_ROOT.'data/admin/admin_CLASS_view.dsp.php',
    '_admin_class_view.act.php' => DIR_ROOT.'data/admin/admin_CLASS_view.act.php',
    '_admin_class_mod.dsp.php' => DIR_ROOT.'data/admin/admin_CLASS_mod.dsp.php',
    '_admin_class_mod.act.php' => DIR_ROOT.'data/admin/admin_CLASS_mod.act.php'
  );

  while($row = mysql_fetch_row($res)) $table_name_list[] = $row[0];

  // Pour chaque table de la BD
  foreach($table_name_list as $table_name ) {
    // Description
    $create_table_string = array_pop( mysql_fetch_row( mysql_uquery('SHOW CREATE TABLE `'.$table_name.'`') ) );

    $table_description[ $table_name ] = to_readable( $table_name );

    if( strpos($create_table_string, 'COMMENT') !== false ) {
      $create_table_string = trim( substr($create_table_string, strrpos($create_table_string, ')') + 1) );

      $options = explode(' ', $create_table_string);

      foreach( $options as $option_string ) {
        if( strpos($option_string, '=') !== false ) {
          list($option, $value) = explode('=', $option_string);
          if( $option == 'COMMENT') {
            $table_description[ $table_name ] = str_replace('\'', '', $value);
          }
        }
      }
    }


    $res = mysql_uquery('SHOW FULL COLUMNS FROM `'.$table_name.'`');

    $name_field_list[$table_name] = false;

    $foreign_keys_list[$table_name] = array();

    while($row = mysql_fetch_assoc($res)) {
      if( $row['Comment'] == '') $row['Comment'] = to_readable($row['Field']);
      if( $row['Key'] == 'PRI' ) $primary_keys[ $table_name ][] = $row['Field'];
      if( $row['Key'] == 'MUL' ) $primary_keys[ $table_name ][] = $row['Field'];

      // Détermination des classes PHP à ajouter
      if( $row['Field'] == 'id' ) {
        $name_field_list[$table_name] = 'id';
        $php_classes[] = $table_name;
      }elseif( $row['Field'] == 'name' ) {
        $name_field_list[$table_name] = 'name';
      }

      $table_columns_list[$table_name][$row['Field']] = $row;

      // Détermination du type de la colonne
      if( ($pos = strpos($row['Type'], '(')) !== false ) {
        $simple_type = substr($row['Type'], 0, $pos);
      }else {
        $simple_type = $row['Type'];
      }

      $table_columns_list[$table_name][$row['Field']]['SimpleType'] = $simple_type;

      // Gestion des clés étrangères
      if( substr($row['Field'], -3) == "_id") {
        $foreign_keys_list[$table_name][$row['Field']] = substr($row['Field'], 0, -3);
      }
    }



    // Gestion des clés étrangères non triviales
    $sql_fk = "
SELECT k_c_u_column.`COLUMN_NAME`, k_c_u_column.`REFERENCED_TABLE_NAME`
FROM (
  SELECT `REFERENCED_TABLE_NAME`, `CONSTRAINT_SCHEMA`, `TABLE_NAME`, `REFERENCED_COLUMN_NAME`, MIN(`CONSTRAINT_NAME`) AS `MIN_CONSTRAINT_NAME`
  FROM `information_schema`.`KEY_COLUMN_USAGE`
  WHERE `CONSTRAINT_SCHEMA` = ".mysql_ureal_escape_string(DB_BASE)."
  AND `TABLE_NAME` = ".mysql_ureal_escape_string($table_name)."
  AND `REFERENCED_COLUMN_NAME` = 'id'
  GROUP BY `REFERENCED_TABLE_NAME`
) k_c_u_unique
JOIN `information_schema`.`KEY_COLUMN_USAGE` k_c_u_column
  ON k_c_u_column.`CONSTRAINT_SCHEMA` = k_c_u_unique.`CONSTRAINT_SCHEMA`
  AND k_c_u_column.`TABLE_NAME` = k_c_u_unique.`TABLE_NAME`
  AND k_c_u_column.`REFERENCED_TABLE_NAME` = k_c_u_unique.`REFERENCED_TABLE_NAME`
  AND k_c_u_column.`REFERENCED_COLUMN_NAME` = k_c_u_unique.`REFERENCED_COLUMN_NAME`
  AND k_c_u_column.`CONSTRAINT_NAME` = k_c_u_unique.`MIN_CONSTRAINT_NAME`
ORDER BY `CONSTRAINT_NAME`";
    $res_fk = mysql_uquery($sql_fk);

    while($row_fk = mysql_fetch_row($res_fk)) {
      $foreign_keys_list[$table_name][$row_fk[0]] = $row_fk[1];

      if( !in_array($table_name, $php_classes) && in_array( $row_fk[0], $primary_keys[ $table_name ] ) ) {
        $sub_tables_list[ $row_fk[1] ][ ] = array('table' => $table_name, 'field' => $row_fk[0] );
      }
    }

    $foreign_keys_list[$table_name] = array_intersect($foreign_keys_list[$table_name], $table_name_list);
  }

  // Tri de l'ordre de création des classes en fonction des clés éttrangères
  $php_classes_ord = array();
  $count = 100;
  while( count($php_classes) != 0 && $count-- > 0) {
    // On prend le premier élément
    $table_name = array_shift($php_classes);

    $foreign_keys_temp = array_diff($foreign_keys_list[$table_name], array($table_name));

    if( !count($foreign_keys_temp) ) {
      // Pas de contraintes, on met directement dans le tableau trié
      array_push($php_classes_ord, $table_name);
    }else {
      if( count( array_intersect($foreign_keys_temp, $php_classes_ord) ) == count( $foreign_keys_temp ) ) {
        // Toutes les contraintes existent, on met dans le tableau trié
        array_push($php_classes_ord, $table_name);
      }else {
        // Sinon, on le met à la fin du tableau initial pour traitement futur
        array_push($php_classes, $table_name);
      }
    }
  }

  $sql = "REPLACE INTO `page` (`code`, `act`, `dsp`, `login_required`, `admin_required`, `tpl`, `rewrite_pattern`) VALUES
  ('admin_CLASS', 'data/admin/admin_CLASS.act.php', 'data/admin/admin_CLASS.dsp.php', 1, 1, 'pagelayout_admin.tpl.php', ''),
  ('admin_CLASS_view', 'data/admin/admin_CLASS_view.act.php', 'data/admin/admin_CLASS_view.dsp.php', 1, 1, 'pagelayout_admin.tpl.php', '{page}/{id}.html'),
  ('admin_CLASS_mod', 'data/admin/admin_CLASS_mod.act.php', 'data/admin/admin_CLASS_mod.dsp.php', 1, 1, 'pagelayout_admin.tpl.php', '')
  ";

  $processed_classes = array_diff( $php_classes_ord, $reserved_class_list );


  $created_file_list = array();
  $overwritten_file_list = array();
  $unchanged_file_list = array();

  foreach( $processed_classes as $class ) {

    $class_db_identifier = $class;
    $class_php_identifier = to_camel_case($class, true);
    $class_name = $table_description[ $class ];
    $name_field = $name_field_list[$class];
    $table_columns = $table_columns_list[$class];
    $foreign_keys = $foreign_keys_list[$class];
    $sub_tables = isset($sub_tables_list[ $class ])?$sub_tables_list[ $class ]:array();

    foreach( $file_list as $file_in => $file_out ) {
      $file_out = str_replace('CLASS', $class_db_identifier, $file_out);

      $custom_content = "\n\n  //Custom content";

      //Gestion du code custom
      if( is_file($file_out) && ($old_content = file_get_contents($file_out)) !== false) {
        if( ($start = strpos($old_content, CUSTOM_START)) !== false && ($stop = strpos($old_content, CUSTOM_STOP)) !== false ) {
          $custom_content = substr($old_content, $start + strlen(CUSTOM_START), $stop - $start - strlen(CUSTOM_START));
        }
      }

      ob_start();
      echo '<?php
';
      include(dirname(__FILE__).'/'.$file_in);
      $content = ob_get_clean();

      if( ($start = strpos($content, CUSTOM_START)) !== false && ($stop = strpos($content, CUSTOM_STOP)) !== false ) {
        $content = substr($content, 0, $start + strlen(CUSTOM_START)).rtrim($custom_content)."\n\n".substr($content, $stop);
      }

      $content = str_replace('<_?php', '<?php', $content);

      _mkdir(dirname($file_out));

      if( file_exists($file_out) ) {
        $file_out_size = strlen( file_get_contents( $file_out ) );
        $content_size = strlen( $content );
        if( $file_out_size != $content_size ) {
          $overwritten_file_list[] = $file_out;

          file_put_contents($file_out, $content);
        }else {
          $unchanged_file_list[] = $file_out;
        }
      }else {
        $created_file_list[] = $file_out;

        file_put_contents($file_out, $content);
      }
    }

    $sql_class = str_replace('CLASS', $class_db_identifier, $sql);

    mysql_uquery($sql_class);
  }
  if( count( $created_file_list ) ) {
    echo '
    <p>File created :</p>
    <ul>';
    foreach( $created_file_list as $file ) {
      echo '
      <li>'.$file.'</li>';
    }
    echo '
    </ul>';
  }
  if( count( $overwritten_file_list ) ) {
    echo '
    <p>File overwritten :</p>
    <ul>';
    foreach( $overwritten_file_list as $file ) {
      echo '
      <li>'.$file.'</li>';
    }
    echo '
    </ul>';
  }
  echo '<p>'.count( $unchanged_file_list ).' files unchanged</p>';

  // Sauvegarde de la structure de la base
  $command = MYSQLDUMP_PATH.'mysqldump --no-data --host="'.DB_HOST.'" --user="'.DB_USER.'" --password="'.DB_PASS.'" "'.DB_BASE.'" 2>&1 > "'.DATA.'database_structure_'.date('Ymd').'.sql"';
  $return_var = null;
  $output = array();
  exec( $command, $output, $return_var );

  if( $return_var === 0 ) {
    echo '
    <p>Database structure saved in '.DATA.'database_structure_'.date('Ymd').'.sql</p>';
  }else {
    echo '<p>Error while saving database structure</p>
    <p>'.implode('<br/>', $output).'</p>';
  }


?>