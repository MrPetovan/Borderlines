<?php
/**
 * Classe Hero
 *
 */

class Hero_Model extends DBObject {
  // Champs BD
  protected $_race_id = null;
  protected $_name = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "hero"; }


  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_race_id($race_id) {
    if( is_numeric($race_id) && (int)$race_id == $race_id) $data = intval($race_id); else $data = null; $this->_race_id = $data;
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

    $object_list = Hero_Model::db_get_all();
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
      $race_list = Race::db_get_all();
      foreach( $race_list as $race)
        $option_list[ $race->id ] = $race->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('race_id', $option_list, $this->get_race_id(), array(), "Race Id *").'<a href="'.get_page_url('admin_race_mod').'">Créer un objet Race</a></p>
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
      case 1 : $return = "Le champ <strong>Race Id</strong> est obligatoire."; break;
      case 2 : $return = "Le champ <strong>Name</strong> est obligatoire."; break;
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

    $return[] = Member::check_compulsory($this->get_race_id(), 1);
    $return[] = Member::check_compulsory($this->get_name(), 2);

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }

  public function get_hero_state_list($season_id = null, $turn = null) {
    $where = '';
    if( ! is_null( $season_id )) $where .= '
AND `season_id` = '.mysql_ureal_escape_string($season_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);

    $sql = '
SELECT `season_id`, `turn`, `hero_id`, `guild_id`, `health`, `stamina`, `morale`, `experience`
FROM `hero_state`
WHERE `hero_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_hero_state( $season_id, $turn, $guild_id, $health, $stamina, $morale, $experience ) {
    $sql = "REPLACE INTO `hero_state` ( `season_id`, `turn`, `hero_id`, `guild_id`, `health`, `stamina`, `morale`, `experience` ) VALUES (".mysql_ureal_escape_string( $season_id, $turn, $this->get_id(), $guild_id, $health, $stamina, $morale, $experience ).")";

    return mysql_uquery($sql);
  }

  public function del_hero_state( $season_id = null, $turn = null ) {
    $where = '';
    if( ! is_null( $season_id )) $where .= '
AND `season_id` = '.mysql_ureal_escape_string($season_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    $sql = 'DELETE FROM `hero_state`
    WHERE `hero_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_hero_tactic_list($tactic_id = null) {
    $where = '';
    if( ! is_null( $tactic_id )) $where .= '
AND `tactic_id` = '.mysql_ureal_escape_string($tactic_id);

    $sql = '
SELECT `hero_id`, `tactic_id`, `order`
FROM `hero_tactic`
WHERE `hero_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_hero_tactic( $tactic_id, $order ) {
    $sql = "REPLACE INTO `hero_tactic` ( `hero_id`, `tactic_id`, `order` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $tactic_id, $order ).")";

    return mysql_uquery($sql);
  }

  public function del_hero_tactic( $tactic_id = null ) {
    $where = '';
    if( ! is_null( $tactic_id )) $where .= '
AND `tactic_id` = '.mysql_ureal_escape_string($tactic_id);
    $sql = 'DELETE FROM `hero_tactic`
    WHERE `hero_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_matchup_hero_action_list($matchup_id = null, $tactic_id = null, $handler_id = null, $tick_start = null) {
    $where = '';
    if( ! is_null( $matchup_id )) $where .= '
AND `matchup_id` = '.mysql_ureal_escape_string($matchup_id);
    if( ! is_null( $tactic_id )) $where .= '
AND `tactic_id` = '.mysql_ureal_escape_string($tactic_id);
    if( ! is_null( $handler_id )) $where .= '
AND `handler_id` = '.mysql_ureal_escape_string($handler_id);
    if( ! is_null( $tick_start )) $where .= '
AND `tick_start` = '.mysql_ureal_escape_string($tick_start);

    $sql = '
SELECT `matchup_id`, `hero_id`, `tactic_id`, `handler_id`, `tick_start`, `tick_stop`, `target_id`, `status`
FROM `matchup_hero_action`
WHERE `hero_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_matchup_hero_action( $matchup_id, $tactic_id, $handler_id, $tick_start, $tick_stop, $target_id, $status ) {
    $sql = "REPLACE INTO `matchup_hero_action` ( `matchup_id`, `hero_id`, `tactic_id`, `handler_id`, `tick_start`, `tick_stop`, `target_id`, `status` ) VALUES (".mysql_ureal_escape_string( $matchup_id, $this->get_id(), $tactic_id, $handler_id, $tick_start, $tick_stop, $target_id, $status ).")";

    return mysql_uquery($sql);
  }

  public function del_matchup_hero_action( $matchup_id = null, $tactic_id = null, $handler_id = null, $tick_start = null ) {
    $where = '';
    if( ! is_null( $matchup_id )) $where .= '
AND `matchup_id` = '.mysql_ureal_escape_string($matchup_id);
    if( ! is_null( $tactic_id )) $where .= '
AND `tactic_id` = '.mysql_ureal_escape_string($tactic_id);
    if( ! is_null( $handler_id )) $where .= '
AND `handler_id` = '.mysql_ureal_escape_string($handler_id);
    if( ! is_null( $tick_start )) $where .= '
AND `tick_start` = '.mysql_ureal_escape_string($tick_start);
    $sql = 'DELETE FROM `matchup_hero_action`
    WHERE `hero_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_matchup_hero_status_list($matchup_id = null, $tick = null) {
    $where = '';
    if( ! is_null( $matchup_id )) $where .= '
AND `matchup_id` = '.mysql_ureal_escape_string($matchup_id);
    if( ! is_null( $tick )) $where .= '
AND `tick` = '.mysql_ureal_escape_string($tick);

    $sql = '
SELECT `hero_id`, `matchup_id`, `tick`, `health`, `stamina`, `morale`, `coord_x`, `coord_y`, `ammunition`
FROM `matchup_hero_status`
WHERE `hero_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_matchup_hero_status( $matchup_id, $tick, $health, $stamina, $morale, $coord_x, $coord_y, $ammunition ) {
    $sql = "REPLACE INTO `matchup_hero_status` ( `hero_id`, `matchup_id`, `tick`, `health`, `stamina`, `morale`, `coord_x`, `coord_y`, `ammunition` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $matchup_id, $tick, $health, $stamina, $morale, $coord_x, $coord_y, $ammunition ).")";

    return mysql_uquery($sql);
  }

  public function del_matchup_hero_status( $matchup_id = null, $tick = null ) {
    $where = '';
    if( ! is_null( $matchup_id )) $where .= '
AND `matchup_id` = '.mysql_ureal_escape_string($matchup_id);
    if( ! is_null( $tick )) $where .= '
AND `tick` = '.mysql_ureal_escape_string($tick);
    $sql = 'DELETE FROM `matchup_hero_status`
    WHERE `hero_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_matchup_team_list($matchup_id = null) {
    $where = '';
    if( ! is_null( $matchup_id )) $where .= '
AND `matchup_id` = '.mysql_ureal_escape_string($matchup_id);

    $sql = '
SELECT `matchup_id`, `hero_id`
FROM `matchup_team`
WHERE `hero_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_matchup_team( $matchup_id ) {
    $sql = "REPLACE INTO `matchup_team` ( `matchup_id`, `hero_id` ) VALUES (".mysql_ureal_escape_string( $matchup_id, $this->get_id() ).")";

    return mysql_uquery($sql);
  }

  public function del_matchup_team( $matchup_id = null ) {
    $where = '';
    if( ! is_null( $matchup_id )) $where .= '
AND `matchup_id` = '.mysql_ureal_escape_string($matchup_id);
    $sql = 'DELETE FROM `matchup_team`
    WHERE `hero_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }







  // CUSTOM

  //Custom content

  // /CUSTOM

}