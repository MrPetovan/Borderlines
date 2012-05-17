<?php
/**
 * Classe Tactic
 *
 */

class Tactic_Model extends DBObject {
  // Champs BD
  protected $_name = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "tactic"; }


  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
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

    $object_list = Tactic_Model::db_get_all();
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
        '.HTMLHelper::genererInputHidden('id', $this->get_id()).'
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
      case 1 : $return = "Le champ <strong>Name</strong> est obligatoire."; break;
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

    $return[] = Member::check_compulsory($this->get_name(), 1);

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }

  public function get_hero_tactic_list($hero_id = null) {
    $where = '';
    if( ! is_null( $hero_id )) $where .= '
AND `hero_id` = '.mysql_ureal_escape_string($hero_id);

    $sql = '
SELECT `hero_id`, `tactic_id`, `order`
FROM `hero_tactic`
WHERE `tactic_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_hero_tactic( $hero_id, $order ) {
    $sql = "REPLACE INTO `hero_tactic` ( `hero_id`, `tactic_id`, `order` ) VALUES (".mysql_ureal_escape_string( $hero_id, $this->get_id(), $order ).")";

    return mysql_uquery($sql);
  }

  public function del_hero_tactic( $hero_id = null ) {
    $where = '';
    if( ! is_null( $hero_id )) $where .= '
AND `hero_id` = '.mysql_ureal_escape_string($hero_id);
    $sql = 'DELETE FROM `hero_tactic`
    WHERE `tactic_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_matchup_hero_action_list($matchup_id = null, $hero_id = null, $handler_id = null, $tick_start = null) {
    $where = '';
    if( ! is_null( $matchup_id )) $where .= '
AND `matchup_id` = '.mysql_ureal_escape_string($matchup_id);
    if( ! is_null( $hero_id )) $where .= '
AND `hero_id` = '.mysql_ureal_escape_string($hero_id);
    if( ! is_null( $handler_id )) $where .= '
AND `handler_id` = '.mysql_ureal_escape_string($handler_id);
    if( ! is_null( $tick_start )) $where .= '
AND `tick_start` = '.mysql_ureal_escape_string($tick_start);

    $sql = '
SELECT `matchup_id`, `hero_id`, `tactic_id`, `handler_id`, `tick_start`, `tick_stop`, `target_id`, `status`
FROM `matchup_hero_action`
WHERE `tactic_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_matchup_hero_action( $matchup_id, $hero_id, $handler_id, $tick_start, $tick_stop, $target_id, $status ) {
    $sql = "REPLACE INTO `matchup_hero_action` ( `matchup_id`, `hero_id`, `tactic_id`, `handler_id`, `tick_start`, `tick_stop`, `target_id`, `status` ) VALUES (".mysql_ureal_escape_string( $matchup_id, $hero_id, $this->get_id(), $handler_id, $tick_start, $tick_stop, $target_id, $status ).")";

    return mysql_uquery($sql);
  }

  public function del_matchup_hero_action( $matchup_id = null, $hero_id = null, $handler_id = null, $tick_start = null ) {
    $where = '';
    if( ! is_null( $matchup_id )) $where .= '
AND `matchup_id` = '.mysql_ureal_escape_string($matchup_id);
    if( ! is_null( $hero_id )) $where .= '
AND `hero_id` = '.mysql_ureal_escape_string($hero_id);
    if( ! is_null( $handler_id )) $where .= '
AND `handler_id` = '.mysql_ureal_escape_string($handler_id);
    if( ! is_null( $tick_start )) $where .= '
AND `tick_start` = '.mysql_ureal_escape_string($tick_start);
    $sql = 'DELETE FROM `matchup_hero_action`
    WHERE `tactic_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_tactic_handler_list($handler_id = null) {
    $where = '';
    if( ! is_null( $handler_id )) $where .= '
AND `handler_id` = '.mysql_ureal_escape_string($handler_id);

    $sql = '
SELECT `tactic_id`, `handler_id`, `params`
FROM `tactic_handler`
WHERE `tactic_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_tactic_handler( $handler_id, $params ) {
    $sql = "REPLACE INTO `tactic_handler` ( `tactic_id`, `handler_id`, `params` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $handler_id, $params ).")";

    return mysql_uquery($sql);
  }

  public function del_tactic_handler( $handler_id = null ) {
    $where = '';
    if( ! is_null( $handler_id )) $where .= '
AND `handler_id` = '.mysql_ureal_escape_string($handler_id);
    $sql = 'DELETE FROM `tactic_handler`
    WHERE `tactic_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }







  // CUSTOM

  //Custom content

  // /CUSTOM

}