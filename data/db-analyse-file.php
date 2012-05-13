
/**
 * Classe décrivant un compte utilisateur, qu'il soit prospect ou enregistré
 *
 */

class <?php echo $class_php_identifier?> extends DBObject {
  // Champs BD<?php
foreach( $table_columns as $column_name => $dummy ) {
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
    case 'date':
      echo '
  public function get_'.$column_name.'()    { return guess_date($this->_'.$column_name.');}';
      break;
    case 'tinyint' :
      echo '
  public function get_'.$column_name.'() { return ($this->is_'.$column_name.'(); }
  public function is_'.$column_name.'() { return ($this->_'.$column_name.' == 1); }';
      break;

  }
} ?>


  /* MUTATEURS */<?php
foreach( $table_columns as $column_name => $column_props ) {
  switch ($column_props['SimpleType']) {
    case 'datetime':
    case 'time':
    case 'date':
      echo '
  public function set_'.$column_name.'($date) { $this->_'.$column_name.' = guess_date($date, GUESS_DATE_MYSQL);}';
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
    var_debug("set_'.$column_name.'($'.$column_name.')");
    if( is_numeric($'.$column_name.') && (int)$'.$column_name.' == $'.$column_name.') $data = intval($'.$column_name.'); else $data = null; $this->_'.$column_name.' = $data;
  }';
      break;

  }
} ?>


  /* FONCTIONS SQL */

  public static function db_exists ($id) { return self::db_exists_class($id, get_class());}
  public static function db_get_by_id($id) { return self::db_get_by_id_class($id, get_class());}

  public static function db_get_all($page = null, $limit = NB_PER_PAGE) {
    $sql = 'SELECT `id` FROM `'.self::get_table_name().'` ORDER BY `id`';

    if(!is_null($page) && is_numeric($page)) {
      $start = ($page - 1) * $limit;
      $sql .= ' LIMIT '.$start.','.$limit;
    }

    return self::sql_to_list($sql, get_class());
  }

  public static function db_count_all() {
    $sql = "SELECT COUNT(`id`) FROM `".self::get_table_name().'`';
    $res = mysql_uquery($sql);
    return array_pop(mysql_fetch_row($res));
  }
<?php
foreach( $table_columns as $column_name => $column_props ) {
  if( $column_props['Key'] == 'UNI' )
      echo '
  public static function db_get_membre_by_'.$column_name.'($'.$column_name.') {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `'.$column_name.'` LIKE ".mysql_ureal_escape_string($'.$column_name.')."
LIMIT 0,1";

    return self::sql_to_object($sql, get_class());
  }';
} ?>

  /* FONCTIONS HTML */

  public static function manage_errors($tab_error, &$html_msg) { return self::manage_errors_class($tab_error, $html_msg, get_class());}

  /**
   * Formulaire d'édition partie Administration
   *
   * @param string $form_url URL de la page action
   * @return string
   */
  public function html_get_form($form_url) {
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
      <p class="field">\'.HTMLHelper::genererSelect(\''.$column_name.'\', $option_list, $this->get_'.$column_name.'(), array(), "'.to_readable($column_name).($column_props['Null'] == 'NO'?' *':'').'").\'<a href="\'.get_page_url(\'admin_'.$foreign_table.'_mod\').\'">Créer un objet '.to_readable($foreign_table).'</a></p>';

  }elseif( $column_name != 'id' ) {
    switch ($column_props['SimpleType']) {
      case 'varchar':
      case 'char':
      case 'datetime':
      case 'time':
      case 'date':
      default:
        echo '
        <p class="field">\'.HTMLHelper::genererInputText(\''.$column_name.'\', $this->get_'.$column_name.'(), array(), "'.to_readable($column_name).($column_props['Null'] == 'NO'?' *':'').'").\'</p>';
        break;
      case 'tinyint' :
        echo '
        <p class="field">\'.HTMLHelper::genererInputCheckBox(\''.$column_name.'\', \'1\', $this->get_'.$column_name.'(), array(\'label_position\' => \'right\'), "'.to_readable($column_name).'" ).\'</p>';
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
    switch($num_error) {<?php
$n = 1;
foreach( $table_columns as $column_name => $column_props ) {
  if( $column_name != 'id' && $column_props['Null'] == 'NO' ) {
    echo '
      case '.$n++.' : $return = "Le champ <strong>'.to_readable($column_name).'</strong> est obligatoire."; break;';
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
  if( $column_name != 'id' && $column_props['Null'] == 'NO' ) {
    echo '
    $return[] = Member::check_compulsory($this->get_'.$column_name.'(), '.$n++.');';
  }
} ?>


    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }
}