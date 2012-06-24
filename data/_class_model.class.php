/**
 * Classe <?php echo $class_php_identifier?>

 *
 */

class <?php echo $class_php_identifier?>_Model extends DBObject {
  // Champs BD<?php
foreach( $table_columns as $column_name => $dummy ) {
  if( $column_name != 'id')
    echo '
  protected $_'.$column_name.' = null;';
} ?>


  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "<?php echo $class_db_identifier?>"; }
<?php
foreach( $table_columns as $column_name => $column_props ) {
  switch ($column_props['SimpleType']) {
    case 'datetime':
    case 'time':
    case 'timestamp':
    case 'date':
      echo '
  public function get_'.$column_name.'()    { return guess_time($this->_'.$column_name.');}';
      break;
    case 'tinyint' :
      echo '
  public function get_'.$column_name.'() { return $this->is_'.$column_name.'(); }
  public function is_'.$column_name.'() { return ($this->_'.$column_name.' == 1); }';
      break;

  }
} ?>


  /* MUTATEURS */<?php
foreach( $table_columns as $column_name => $column_props ) {
  switch ($column_props['SimpleType']) {
    case 'datetime':
    case 'time':
    case 'timestamp':
    case 'date':
      echo '
  public function set_'.$column_name.'($date) { $this->_'.$column_name.' = guess_time($date, GUESS_DATE_MYSQL);}';
      break;
    case 'tinyint' :
      echo '
  public function set_'.$column_name.'($'.$column_name.') {
    if($'.$column_name.') $data = 1; else $data = 0; $this->_'.$column_name.' = $data;
  }';
      break;
    case 'int' :
      echo '
  public function set_'.$column_name.'($'.$column_name.') {
    if( is_numeric($'.$column_name.') && (int)$'.$column_name.' == $'.$column_name.') $data = intval($'.$column_name.'); else $data = null; $this->_'.$column_name.' = $data;
  }';
      break;

  }
} ?>


  /* FONCTIONS SQL */

<?php
foreach( $table_columns as $column_name => $column_props ) {
  if( $column_props['Key'] == 'UNI' || $column_props['Key'] == 'MUL' )
    // public static function db_get_'.$class_db_identifier.'_by_'.$column_name.'($'.$column_name.') {
    echo '
  public static function db_get_by_'.$column_name.'($'.$column_name.') {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `'.$column_name.'` = ".mysql_ureal_escape_string($'.$column_name.')."
LIMIT 0,1";

    return self::sql_to_object($sql, get_class());
  }';
} ?>


  public static function db_get_select_list() {
    $return = array();

    $object_list = <?php echo $class_php_identifier?>_Model::db_get_all();
    foreach( $object_list as $object ) $return[ $object->get_id() ] = $object->get_<?php echo $name_field?>();

    return $return;
  }

  /* FONCTIONS HTML */

  /**
   * Formulaire d'édition partie Administration
   *
   * @return string
   */
  public function html_get_form() {
    $return = '
    <fieldset>
      <legend>Text fields</legend><?php
foreach( $table_columns as $column_name => $column_props ) {
  if( array_key_exists( $column_name, $foreign_keys) ) {
    $foreign_table = $foreign_keys[$column_name];
    echo '\';
      $option_list = array('.($column_props['Null'] != 'NO'?'null => \'Pas de choix\'':'').');
      $'.$foreign_table.'_list = '.to_camel_case($foreign_table, true).'::db_get_all();
      foreach( $'.$foreign_table.'_list as $'.$foreign_table.')
        $option_list[ $'.$foreign_table.'->id ] = $'.$foreign_table.'->name;

      $return .= \'
      <p class="field">\'.HTMLHelper::genererSelect(\''.$column_name.'\', $option_list, $this->get_'.$column_name.'(), array(), "'.$column_props['Comment'].($column_props['Null'] == 'NO'?' *':'').'").\'<a href="\'.get_page_url(\'admin_'.$foreign_table.'_mod\').\'">Créer un objet '.to_readable($foreign_table).'</a></p>';

  }elseif( $column_name != 'id' ) {
    switch ($column_props['SimpleType']) {
      case 'varchar':
      case 'char':
      case 'datetime':
      case 'time':
      case 'date':
      default:
        echo '
        <p class="field">\'.HTMLHelper::genererInputText(\''.$column_name.'\', $this->get_'.$column_name.'(), array(), "'.$column_props['Comment'].($column_props['Null'] == 'NO' && is_null( $column_props['Default'] )?' *':'').'").\'</p>';
        break;
      case 'tinyint' :
        echo '
        <p class="field">\'.HTMLHelper::genererInputCheckBox(\''.$column_name.'\', \'1\', $this->get_'.$column_name.'(), array(\'label_position\' => \'right\'), "'.$column_props['Comment'].'" ).\'</p>';
        break;
    }
  }else {
    echo '
        \'.HTMLHelper::genererInputHidden(\''.$column_name.'\', $this->get_'.$column_name.'()).\'';
  }
} ?>


    </fieldset>';

    return $return;
  }

/**
 * Retourne la chaîne de caractère d'erreur en fonction du code correspondant
 *
 * @see Member->check_valid
 * @param int $num_error Code d'erreur
 * @return string
 */
  public static function get_message_erreur($num_error) {
    switch($num_error) { <?php
$n = 1;
foreach( $table_columns as $column_name => $column_props ) {
  if( $column_name != 'id' && $column_props['Null'] == 'NO' && is_null( $column_props['Default'] ) ) {
    echo '
      case '.$n++.' : $return = "Le champ <strong>'.$column_props['Comment'].'</strong> est obligatoire."; break;';
  }
} ?>

      default: $return = "Erreur de saisie, veuillez vérifier les champs.";
    }
    return $return;
  }

  /**
   * Effectue les vérifications basiques pour mettre à jour les champs
   * Retourne true si pas d'erreur, une liste de codes d'erreur sinon :
   *
   * @param int $flags Flags augmentant l'étendue des tests
   * @return true | array
   */
  public function check_valid($flags = 0) {
    $return = array();
<?php
$n = 1;
foreach( $table_columns as $column_name => $column_props ) {
  if( $column_name != 'id' && $column_props['Null'] == 'NO' && is_null( $column_props['Default'] ) ) {
    $strict = '';
    switch ($column_props['SimpleType']) {
      case 'int' :
      case 'tinyint' :
        $strict = ', true';
        break;
    }
    echo '
    $return[] = Member::check_compulsory($this->get_'.$column_name.'(), '.$n++.$strict.');';
  }
} ?>


    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }
<?php
  foreach( $sub_tables as $sub_table_info ) {
    $sub_table = $sub_table_info['table'];
    $sub_table_field = $sub_table_info['field'];
    $sub_table_pk = $primary_keys[ $sub_table ];
    $sub_table_pk_clean = array_diff($sub_table_pk, array($sub_table_field));

    $pk_param_list = array();
    $add_param_list = array();
    $pk_sql_cond = '';
    $sql_select = array();
    $sql_insert = array();
    $sql_delete = array();

    foreach($sub_table_pk_clean as $field) {
      $pk_param_list[] = '$'.$field.' = null';
      $pk_sql_cond .= '
    if( ! is_null( $'.$field.' )) $where .= \'
AND `'.$field.'` = \'.mysql_ureal_escape_string($'.$field.');';
    }

    foreach( $table_columns_list[ $sub_table ] as $field_name => $field ) {
      $sql_select[] = "`$field_name`";

      if( $field_name == $sub_table_field ) {
        $sql_insert[] = '$this->get_id()';

      }else {
        $add_param_list[] = '$'.$field_name;
        $sql_insert[] = '$'.$field_name;

      }

      if( in_array( $field_name, $sub_table_pk ) ) {
        if( $field_name == $sub_table_field )
          $sql_delete[] = '`'.$field_name.'` = \'.mysql_ureal_escape_string( $this->id )';
        else
          $sql_delete[] = '`'.$field_name.'` = \'.mysql_ureal_escape_string( $'.$field_name.' )';
      }

    }
?>

  public function get_<?php echo $sub_table ?>_list(<?php echo implode( ', ', $pk_param_list )?>) {
    $where = '';<?php echo $pk_sql_cond; ?>


    $sql = '
SELECT <?php echo implode(', ', $sql_select)?>

FROM `<?php echo $sub_table?>`
WHERE `<?php echo $sub_table_field?>` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_<?php echo $sub_table ?>( <?php echo implode(', ', $add_param_list)?> ) {
    $sql = "REPLACE INTO `<?php echo $sub_table ?>` ( <?php echo implode(', ', $sql_select ) ?> ) VALUES (".mysql_ureal_escape_string( <?php echo implode( ', ', $sql_insert ) ?> ).")";

    return mysql_uquery($sql);
  }

  public function del_<?php echo $sub_table ?>( <?php echo implode( ', ', $pk_param_list )?> ) {
    $where = '';<?php echo $pk_sql_cond; ?>

    $sql = 'DELETE FROM `<?php echo $sub_table ?>`
    WHERE `<?php echo $sub_table_field?>` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }


<?php
  }
?>





  // CUSTOM

  // /CUSTOM

}