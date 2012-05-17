<?php
/**
 * Classe Handler
 *
 */

class Handler_Model extends DBObject {
  // Champs BD
  protected $_handler_type_id = null;
  protected $_class = null;
  protected $_name = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "handler"; }


  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_handler_type_id($handler_type_id) {
    if( is_numeric($handler_type_id) && (int)$handler_type_id == $handler_type_id) $data = intval($handler_type_id); else $data = null; $this->_handler_type_id = $data;
  }

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

  public static function db_get_select_list() {
    $return = array();

    $object_list = Handler_Model::db_get_all();
    foreach( $object_list as $object ) $return[ $object->get_id() ] = $object->get_name();

    return $return;
  }

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
      <legend>Text fields</legend>
        '.HTMLHelper::genererInputHidden('id', $this->get_id()).'';
      $option_list = array();
      $handler_type_list = Handler_Type::db_get_all();
      foreach( $handler_type_list as $handler_type)
        $option_list[ $handler_type->id ] = $handler_type->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('handler_type_id', $option_list, $this->get_handler_type_id(), array(), "Handler Type Id *").'<a href="'.get_page_url('admin_handler_type_mod').'">Créer un objet Handler Type</a></p>
        <p class="field">'.HTMLHelper::genererInputText('class', $this->get_class(), array(), "Class *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('name', $this->get_name(), array(), "Name *").'</p>
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
    switch($num_error) { 
      case 1 : $return = "Le champ <strong>Handler Type Id</strong> est obligatoire."; break;
      case 2 : $return = "Le champ <strong>Class</strong> est obligatoire."; break;
      case 3 : $return = "Le champ <strong>Name</strong> est obligatoire."; break;
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

    $return[] = Member::check_compulsory($this->get_handler_type_id(), 1);
    $return[] = Member::check_compulsory($this->get_class(), 2);
    $return[] = Member::check_compulsory($this->get_name(), 3);

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }

  public function get_handler_param_list($code = null) {
    $where = '';
    if( ! is_null( $code )) $where .= '
AND `code` = '.mysql_ureal_escape_string($code);

    $sql = '
SELECT `handler_id`, `code`, `type`
FROM `handler_param`
WHERE `handler_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_handler_param( $code, $type ) {
    $sql = "REPLACE INTO `handler_param` ( `handler_id`, `code`, `type` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $code, $type ).")";

    return mysql_uquery($sql);
  }

  public function del_handler_param( $code = null ) {
    $where = '';
    if( ! is_null( $code )) $where .= '
AND `code` = '.mysql_ureal_escape_string($code);
    $sql = 'DELETE FROM `handler_param`
    WHERE `handler_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_matchup_hero_action_list($matchup_id = null, $hero_id = null, $tactic_id = null, $tick_start = null) {
    $where = '';
    if( ! is_null( $matchup_id )) $where .= '
AND `matchup_id` = '.mysql_ureal_escape_string($matchup_id);
    if( ! is_null( $hero_id )) $where .= '
AND `hero_id` = '.mysql_ureal_escape_string($hero_id);
    if( ! is_null( $tactic_id )) $where .= '
AND `tactic_id` = '.mysql_ureal_escape_string($tactic_id);
    if( ! is_null( $tick_start )) $where .= '
AND `tick_start` = '.mysql_ureal_escape_string($tick_start);

    $sql = '
SELECT `matchup_id`, `hero_id`, `tactic_id`, `handler_id`, `tick_start`, `tick_stop`, `target_id`, `status`
FROM `matchup_hero_action`
WHERE `handler_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_matchup_hero_action( $matchup_id, $hero_id, $tactic_id, $tick_start, $tick_stop, $target_id, $status ) {
    $sql = "REPLACE INTO `matchup_hero_action` ( `matchup_id`, `hero_id`, `tactic_id`, `handler_id`, `tick_start`, `tick_stop`, `target_id`, `status` ) VALUES (".mysql_ureal_escape_string( $matchup_id, $hero_id, $tactic_id, $this->get_id(), $tick_start, $tick_stop, $target_id, $status ).")";

    return mysql_uquery($sql);
  }

  public function del_matchup_hero_action( $matchup_id = null, $hero_id = null, $tactic_id = null, $tick_start = null ) {
    $where = '';
    if( ! is_null( $matchup_id )) $where .= '
AND `matchup_id` = '.mysql_ureal_escape_string($matchup_id);
    if( ! is_null( $hero_id )) $where .= '
AND `hero_id` = '.mysql_ureal_escape_string($hero_id);
    if( ! is_null( $tactic_id )) $where .= '
AND `tactic_id` = '.mysql_ureal_escape_string($tactic_id);
    if( ! is_null( $tick_start )) $where .= '
AND `tick_start` = '.mysql_ureal_escape_string($tick_start);
    $sql = 'DELETE FROM `matchup_hero_action`
    WHERE `handler_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_tactic_handler_list($tactic_id = null) {
    $where = '';
    if( ! is_null( $tactic_id )) $where .= '
AND `tactic_id` = '.mysql_ureal_escape_string($tactic_id);

    $sql = '
SELECT `tactic_id`, `handler_id`, `params`
FROM `tactic_handler`
WHERE `handler_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_tactic_handler( $tactic_id, $params ) {
    $sql = "REPLACE INTO `tactic_handler` ( `tactic_id`, `handler_id`, `params` ) VALUES (".mysql_ureal_escape_string( $tactic_id, $this->get_id(), $params ).")";

    return mysql_uquery($sql);
  }

  public function del_tactic_handler( $tactic_id = null ) {
    $where = '';
    if( ! is_null( $tactic_id )) $where .= '
AND `tactic_id` = '.mysql_ureal_escape_string($tactic_id);
    $sql = 'DELETE FROM `tactic_handler`
    WHERE `handler_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }







  // CUSTOM

  //Custom content

  // /CUSTOM

}