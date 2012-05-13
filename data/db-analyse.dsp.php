<?php

  $res = mysql_uquery('SHOW TABLES');

  $table_name_list = array();
  $table_columns_list = array();
  $primary_keys = array();
  $php_classes = array();
  $name_field_list = array();
  $reserved_class_list = array('page', 'membre');
  $foreign_keys_list = array();

  $file_list = array(
    'db-analyse-file.php' => DIR_ROOT.'data/CLASS/CLASS.class.php',
    'admin_class.dsp.php' => DIR_ROOT.'data/admin/admin_CLASS.dsp.php',
    'admin_class.act.php' => DIR_ROOT.'data/admin/admin_CLASS.act.php',
    'admin_class_view.dsp.php' => DIR_ROOT.'data/admin/admin_CLASS_view.dsp.php',
    'admin_class_mod.dsp.php' => DIR_ROOT.'data/admin/admin_CLASS_mod.dsp.php',
    'admin_class_mod.act.php' => DIR_ROOT.'data/admin/admin_CLASS_mod.act.php'
  );

  while($row = mysql_fetch_row($res)) $table_name_list[] = $row[0];

  $table_name_list = array_diff($table_name_list, $reserved_class_list);

  foreach($table_name_list as $table_name ) {
    $res = mysql_uquery('SHOW FULL COLUMNS FROM '.$table_name);

    $name_field_list[$table_name] = false;

    $foreign_keys_list[$table_name] = array();

    while($row = mysql_fetch_assoc($res)) {
      if( $row['Key'] == 'PRI' ) $primary_keys[$table_name][] = $row['Field'];

      if( $row['Field'] == 'id' ) {
        $name_field_list[$table_name] = 'id';
        $php_classes[] = $table_name;
      }elseif( $row['Field'] == 'name' ) {
        $name_field_list[$table_name] = 'name';
      }

      $table_columns_list[$table_name][$row['Field']] = $row;

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
    $sql_fk = "SELECT `COLUMN_NAME`, `REFERENCED_TABLE_NAME`
FROM `information_schema`.`KEY_COLUMN_USAGE`
WHERE `CONSTRAINT_SCHEMA` = ".mysql_ureal_escape_string(DB_BASE)."
AND `TABLE_NAME` = ".mysql_ureal_escape_string($table_name)."
AND `REFERENCED_COLUMN_NAME` = 'id'";

    $res_fk = mysql_uquery($sql_fk);

    while($row_fk = mysql_fetch_row($res_fk)) {
      $foreign_keys_list[$table_name][$row_fk[0]] = $row_fk[1];
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
  ('admin_CLASS', 'data/admin/admin_CLASS.act.php', 'data/admin/admin_CLASS.dsp.php', 1, 1, '', ''),
  ('admin_CLASS_view', 'data/admin/admin_CLASS_view.act.php', 'data/admin/admin_CLASS_view.dsp.php', 1, 1, '', '{page}/{id}.html'),
  ('admin_CLASS_mod', 'data/admin/admin_CLASS_mod.act.php', 'data/admin/admin_CLASS_mod.dsp.php', 1, 1, '', '')
  ";

  foreach( $php_classes_ord as $class ) {

    $class_db_identifier = $class;
    $class_php_identifier = to_camel_case($class, true);
    $class_name = to_readable( $class );
    $name_field = $name_field_list[$class];
    $table_columns = $table_columns_list[$class];
    $foreign_keys = $foreign_keys_list[$class];

    foreach( $file_list as $file_in => $file_out ) {
      $file_out = str_replace('CLASS', $class_db_identifier, $file_out);

      ob_start();
      echo '<?php';
      include(dirname(__FILE__).'/'.$file_in);
      echo '?>';
      $content = ob_get_clean();

      _mkdir(dirname($file_out));
      file_put_contents($file_out, str_replace('<_?php', '<?php', $content));
      echo "<p>Création de ".$file_out."</p>";
    }

    $sql_class = str_replace('CLASS', $class_db_identifier, $sql);

    mysql_uquery($sql_class);

    //echo '<pre>'.htmlentities_utf8($content).'</pre>';
  }
?>