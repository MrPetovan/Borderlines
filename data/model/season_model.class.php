<?php
/**
 * Classe Season
 *
 */

class Season_Model extends DBObject {
  // Champs BD
  protected $_name = null;
  protected $_current_turn = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "season"; }


  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_current_turn($current_turn) {
    if( is_numeric($current_turn) && (int)$current_turn == $current_turn) $data = intval($current_turn); else $data = null; $this->_current_turn = $data;
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

    $object_list = Season_Model::db_get_all();
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
        <p class="field">'.HTMLHelper::genererInputText('name', $this->get_name(), array(), "Name").'</p>
        <p class="field">'.HTMLHelper::genererInputText('current_turn', $this->get_current_turn(), array(), "Tour actuel *").'</p>
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
      case 1 : $return = "Le champ <strong>Tour actuel</strong> est obligatoire."; break;
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

    $return[] = Member::check_compulsory($this->get_current_turn(), 1);

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }

  public function get_guild_season_league_list($guild_id = null, $league_id = null) {
    $where = '';
    if( ! is_null( $guild_id )) $where .= '
AND `guild_id` = '.mysql_ureal_escape_string($guild_id);
    if( ! is_null( $league_id )) $where .= '
AND `league_id` = '.mysql_ureal_escape_string($league_id);

    $sql = '
SELECT `season_id`, `guild_id`, `league_id`
FROM `guild_season_league`
WHERE `season_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_guild_season_league( $guild_id, $league_id ) {
    $sql = "REPLACE INTO `guild_season_league` ( `season_id`, `guild_id`, `league_id` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $guild_id, $league_id ).")";

    return mysql_uquery($sql);
  }

  public function del_guild_season_league( $guild_id = null, $league_id = null ) {
    $where = '';
    if( ! is_null( $guild_id )) $where .= '
AND `guild_id` = '.mysql_ureal_escape_string($guild_id);
    if( ! is_null( $league_id )) $where .= '
AND `league_id` = '.mysql_ureal_escape_string($league_id);
    $sql = 'DELETE FROM `guild_season_league`
    WHERE `season_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_hero_state_list($turn = null, $hero_id = null) {
    $where = '';
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    if( ! is_null( $hero_id )) $where .= '
AND `hero_id` = '.mysql_ureal_escape_string($hero_id);

    $sql = '
SELECT `season_id`, `turn`, `hero_id`, `guild_id`, `health`, `stamina`, `morale`, `experience`
FROM `hero_state`
WHERE `season_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_hero_state( $turn, $hero_id, $guild_id, $health, $stamina, $morale, $experience ) {
    $sql = "REPLACE INTO `hero_state` ( `season_id`, `turn`, `hero_id`, `guild_id`, `health`, `stamina`, `morale`, `experience` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $turn, $hero_id, $guild_id, $health, $stamina, $morale, $experience ).")";

    return mysql_uquery($sql);
  }

  public function del_hero_state( $turn = null, $hero_id = null ) {
    $where = '';
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    if( ! is_null( $hero_id )) $where .= '
AND `hero_id` = '.mysql_ureal_escape_string($hero_id);
    $sql = 'DELETE FROM `hero_state`
    WHERE `season_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_object_state_list($guild_id = null, $object_id = null, $turn = null) {
    $where = '';
    if( ! is_null( $guild_id )) $where .= '
AND `guild_id` = '.mysql_ureal_escape_string($guild_id);
    if( ! is_null( $object_id )) $where .= '
AND `object_id` = '.mysql_ureal_escape_string($object_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);

    $sql = '
SELECT `guild_id`, `object_id`, `season_id`, `turn`, `durability`
FROM `object_state`
WHERE `season_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_object_state( $guild_id, $object_id, $turn, $durability ) {
    $sql = "REPLACE INTO `object_state` ( `guild_id`, `object_id`, `season_id`, `turn`, `durability` ) VALUES (".mysql_ureal_escape_string( $guild_id, $object_id, $this->get_id(), $turn, $durability ).")";

    return mysql_uquery($sql);
  }

  public function del_object_state( $guild_id = null, $object_id = null, $turn = null ) {
    $where = '';
    if( ! is_null( $guild_id )) $where .= '
AND `guild_id` = '.mysql_ureal_escape_string($guild_id);
    if( ! is_null( $object_id )) $where .= '
AND `object_id` = '.mysql_ureal_escape_string($object_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    $sql = 'DELETE FROM `object_state`
    WHERE `season_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }







  // CUSTOM

  //Custom content

  // /CUSTOM

}