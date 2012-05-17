<?php
/**
 * Classe Matchup
 *
 */

class Matchup_Model extends DBObject {
  // Champs BD
  protected $_season_id = null;
  protected $_turn = null;
  protected $_local_team_id = null;
  protected $_away_team_id = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "matchup"; }


  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_season_id($season_id) {
    if( is_numeric($season_id) && (int)$season_id == $season_id) $data = intval($season_id); else $data = null; $this->_season_id = $data;
  }
  public function set_turn($turn) {
    if( is_numeric($turn) && (int)$turn == $turn) $data = intval($turn); else $data = null; $this->_turn = $data;
  }
  public function set_local_team_id($local_team_id) {
    if( is_numeric($local_team_id) && (int)$local_team_id == $local_team_id) $data = intval($local_team_id); else $data = null; $this->_local_team_id = $data;
  }
  public function set_away_team_id($away_team_id) {
    if( is_numeric($away_team_id) && (int)$away_team_id == $away_team_id) $data = intval($away_team_id); else $data = null; $this->_away_team_id = $data;
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

    $object_list = Matchup_Model::db_get_all();
    foreach( $object_list as $object ) $return[ $object->get_id() ] = $object->get_id();

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
      $season_list = Season::db_get_all();
      foreach( $season_list as $season)
        $option_list[ $season->id ] = $season->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('season_id', $option_list, $this->get_season_id(), array(), "Saison *").'<a href="'.get_page_url('admin_season_mod').'">Créer un objet Season</a></p>
        <p class="field">'.HTMLHelper::genererInputText('turn', $this->get_turn(), array(), "Tour *").'</p>';
      $option_list = array();
      $guild_list = Guild::db_get_all();
      foreach( $guild_list as $guild)
        $option_list[ $guild->id ] = $guild->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('local_team_id', $option_list, $this->get_local_team_id(), array(), "Equipe locale *").'<a href="'.get_page_url('admin_guild_mod').'">Créer un objet Guild</a></p>';
      $option_list = array();
      $guild_list = Guild::db_get_all();
      foreach( $guild_list as $guild)
        $option_list[ $guild->id ] = $guild->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('away_team_id', $option_list, $this->get_away_team_id(), array(), "Equipe invitée *").'<a href="'.get_page_url('admin_guild_mod').'">Créer un objet Guild</a></p>
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
      case 1 : $return = "Le champ <strong>Saison</strong> est obligatoire."; break;
      case 2 : $return = "Le champ <strong>Tour</strong> est obligatoire."; break;
      case 3 : $return = "Le champ <strong>Equipe locale</strong> est obligatoire."; break;
      case 4 : $return = "Le champ <strong>Equipe invitée</strong> est obligatoire."; break;
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

    $return[] = Member::check_compulsory($this->get_season_id(), 1);
    $return[] = Member::check_compulsory($this->get_turn(), 2);
    $return[] = Member::check_compulsory($this->get_local_team_id(), 3);
    $return[] = Member::check_compulsory($this->get_away_team_id(), 4);

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }

  public function get_matchup_hero_action_list($hero_id = null, $tactic_id = null, $handler_id = null, $tick_start = null) {
    $where = '';
    if( ! is_null( $hero_id )) $where .= '
AND `hero_id` = '.mysql_ureal_escape_string($hero_id);
    if( ! is_null( $tactic_id )) $where .= '
AND `tactic_id` = '.mysql_ureal_escape_string($tactic_id);
    if( ! is_null( $handler_id )) $where .= '
AND `handler_id` = '.mysql_ureal_escape_string($handler_id);
    if( ! is_null( $tick_start )) $where .= '
AND `tick_start` = '.mysql_ureal_escape_string($tick_start);

    $sql = '
SELECT `matchup_id`, `hero_id`, `tactic_id`, `handler_id`, `tick_start`, `tick_stop`, `target_id`, `status`
FROM `matchup_hero_action`
WHERE `matchup_id` = '.mysql_ureal_escape_string($this->get_id()).$where.'
ORDER BY `tick_start`';
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_matchup_hero_action( $hero_id, $tactic_id, $handler_id, $tick_start, $tick_stop, $target_id, $status ) {
    $sql = "REPLACE INTO `matchup_hero_action` ( `matchup_id`, `hero_id`, `tactic_id`, `handler_id`, `tick_start`, `tick_stop`, `target_id`, `status` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $hero_id, $tactic_id, $handler_id, $tick_start, $tick_stop, $target_id, $status ).")";

    return mysql_uquery($sql);
  }

  public function del_matchup_hero_action( $hero_id = null, $tactic_id = null, $handler_id = null, $tick_start = null ) {
    $where = '';
    if( ! is_null( $hero_id )) $where .= '
AND `hero_id` = '.mysql_ureal_escape_string($hero_id);
    if( ! is_null( $tactic_id )) $where .= '
AND `tactic_id` = '.mysql_ureal_escape_string($tactic_id);
    if( ! is_null( $handler_id )) $where .= '
AND `handler_id` = '.mysql_ureal_escape_string($handler_id);
    if( ! is_null( $tick_start )) $where .= '
AND `tick_start` = '.mysql_ureal_escape_string($tick_start);
    $sql = 'DELETE FROM `matchup_hero_action`
    WHERE `matchup_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_matchup_hero_status_list($hero_id = null, $tick = null) {
    $where = '';
    if( ! is_null( $hero_id )) $where .= '
AND `hero_id` = '.mysql_ureal_escape_string($hero_id);
    if( ! is_null( $tick )) $where .= '
AND `tick` = '.mysql_ureal_escape_string($tick);

    $sql = '
SELECT `hero_id`, `matchup_id`, `tick`, `health`, `stamina`, `morale`, `coord_x`, `coord_y`, `ammunition`
FROM `matchup_hero_status`
WHERE `matchup_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_matchup_hero_status( $hero_id, $tick, $health, $stamina, $morale, $coord_x, $coord_y, $ammunition ) {
    $sql = "REPLACE INTO `matchup_hero_status` ( `hero_id`, `matchup_id`, `tick`, `health`, `stamina`, `morale`, `coord_x`, `coord_y`, `ammunition` ) VALUES (".mysql_ureal_escape_string( $hero_id, $this->get_id(), $tick, $health, $stamina, $morale, $coord_x, $coord_y, $ammunition ).")";

    return mysql_uquery($sql);
  }

  public function del_matchup_hero_status( $hero_id = null, $tick = null ) {
    $where = '';
    if( ! is_null( $hero_id )) $where .= '
AND `hero_id` = '.mysql_ureal_escape_string($hero_id);
    if( ! is_null( $tick )) $where .= '
AND `tick` = '.mysql_ureal_escape_string($tick);
    $sql = 'DELETE FROM `matchup_hero_status`
    WHERE `matchup_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_matchup_object_status_list($object_id = null, $tick = null) {
    $where = '';
    if( ! is_null( $object_id )) $where .= '
AND `object_id` = '.mysql_ureal_escape_string($object_id);
    if( ! is_null( $tick )) $where .= '
AND `tick` = '.mysql_ureal_escape_string($tick);

    $sql = '
SELECT `matchup_id`, `object_id`, `tick`, `durability`
FROM `matchup_object_status`
WHERE `matchup_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_matchup_object_status( $object_id, $tick, $durability ) {
    $sql = "REPLACE INTO `matchup_object_status` ( `matchup_id`, `object_id`, `tick`, `durability` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $object_id, $tick, $durability ).")";

    return mysql_uquery($sql);
  }

  public function del_matchup_object_status( $object_id = null, $tick = null ) {
    $where = '';
    if( ! is_null( $object_id )) $where .= '
AND `object_id` = '.mysql_ureal_escape_string($object_id);
    if( ! is_null( $tick )) $where .= '
AND `tick` = '.mysql_ureal_escape_string($tick);
    $sql = 'DELETE FROM `matchup_object_status`
    WHERE `matchup_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_matchup_team_list($hero_id = null) {
    $where = '';
    if( ! is_null( $hero_id )) $where .= '
AND `hero_id` = '.mysql_ureal_escape_string($hero_id);

    $sql = '
SELECT `matchup_id`, `hero_id`
FROM `matchup_team`
WHERE `matchup_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_matchup_team( $hero_id ) {
    $sql = "REPLACE INTO `matchup_team` ( `matchup_id`, `hero_id` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $hero_id ).")";

    return mysql_uquery($sql);
  }

  public function del_matchup_team( $hero_id = null ) {
    $where = '';
    if( ! is_null( $hero_id )) $where .= '
AND `hero_id` = '.mysql_ureal_escape_string($hero_id);
    $sql = 'DELETE FROM `matchup_team`
    WHERE `matchup_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }







  // CUSTOM

  //Custom content

  // /CUSTOM

}
