<?php
/**
 * Classe Territory
 *
 */

class Territory_Model extends DBObject {
  // Champs BD
  protected $_name = null;
  protected $_capital_name = null;
  protected $_world_id = null;
  protected $_vertices = null;
  protected $_passable = null;
  protected $_capturable = null;
  protected $_background = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "territory"; }

  public function get_passable() { return $this->is_passable(); }
  public function is_passable() { return ($this->_passable == 1); }
  public function get_capturable() { return $this->is_capturable(); }
  public function is_capturable() { return ($this->_capturable == 1); }

  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_world_id($world_id) {
    if( is_numeric($world_id) && (int)$world_id == $world_id) $data = intval($world_id); else $data = null; $this->_world_id = $data;
  }
  public function set_passable($passable) {
    if($passable) $data = 1; else $data = 0; $this->_passable = $data;
  }
  public function set_capturable($capturable) {
    if($capturable) $data = 1; else $data = 0; $this->_capturable = $data;
  }

  /* FONCTIONS SQL */


  public static function db_get_by_world_id($world_id) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `world_id` = ".mysql_ureal_escape_string($world_id);

    return self::sql_to_list($sql);
  }

  public static function db_get_select_list( $with_null = false ) {
    $return = array();

    if( $with_null ) {
        $return[ null ] = 'N/A';
    }

    $object_list = Territory_Model::db_get_all();
    foreach( $object_list as $object ) $return[ $object->get_id() ] = $object->get_name();

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
      <legend>Text fields</legend>
        '.HTMLHelper::genererInputHidden('id', $this->get_id()).'
        <p class="field">'.(is_array($this->get_name())?
          HTMLHelper::genererTextArea( "name", parameters_to_string( $this->get_name() ), array(), "Name *" ):
          HTMLHelper::genererInputText( "name", $this->get_name(), array(), "Name *")).'
        </p>
        <p class="field">'.(is_array($this->get_capital_name())?
          HTMLHelper::genererTextArea( "capital_name", parameters_to_string( $this->get_capital_name() ), array(), "Capital Name *" ):
          HTMLHelper::genererInputText( "capital_name", $this->get_capital_name(), array(), "Capital Name *")).'
        </p>';
      $option_list = array();
      $world_list = World::db_get_all();
      foreach( $world_list as $world)
        $option_list[ $world->id ] = $world->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('world_id', $option_list, $this->get_world_id(), array(), "World Id *").'<a href="'.get_page_url('admin_world_mod').'">Créer un objet World</a></p>
        <p class="field">'.(is_array($this->get_vertices())?
          HTMLHelper::genererTextArea( "vertices", parameters_to_string( $this->get_vertices() ), array(), "Vertices" ):
          HTMLHelper::genererInputText( "vertices", $this->get_vertices(), array(), "Vertices")).'
        </p>
        <p class="field">'.HTMLHelper::genererInputCheckBox('passable', '1', $this->get_passable(), array('label_position' => 'right'), "Passable" ).'</p>
        <p class="field">'.HTMLHelper::genererInputCheckBox('capturable', '1', $this->get_capturable(), array('label_position' => 'right'), "Capturable" ).'</p>
        <p class="field">'.(is_array($this->get_background())?
          HTMLHelper::genererTextArea( "background", parameters_to_string( $this->get_background() ), array(), "Background" ):
          HTMLHelper::genererInputText( "background", $this->get_background(), array(), "Background")).'
        </p>

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
      case 2 : $return = "Le champ <strong>Capital Name</strong> est obligatoire."; break;
      case 3 : $return = "Le champ <strong>World Id</strong> est obligatoire."; break;
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
    $return[] = Member::check_compulsory($this->get_capital_name(), 2);
    $return[] = Member::check_compulsory($this->get_world_id(), 3, true);

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }

  public function get_player_history_list($game_id = null, $player_id = null) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $player_id )) $where .= '
AND `player_id` = '.mysql_ureal_escape_string($player_id);

    $sql = '
SELECT `game_id`, `player_id`, `turn`, `datetime`, `reason`, `territory_id`
FROM `player_history`
WHERE `territory_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_player_history( $game_id, $player_id, $turn, $datetime, $reason ) {
    $sql = "REPLACE INTO `player_history` ( `game_id`, `player_id`, `turn`, `datetime`, `reason`, `territory_id` ) VALUES (".mysql_ureal_escape_string( $game_id, $player_id, $turn, guess_time( $datetime, GUESS_TIME_MYSQL ), $reason, $this->get_id() ).")";

    return mysql_uquery($sql);
  }

  public function del_player_history( $game_id = null, $player_id = null ) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $player_id )) $where .= '
AND `player_id` = '.mysql_ureal_escape_string($player_id);
    $sql = 'DELETE FROM `player_history`
    WHERE `territory_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_territory_criterion_list($criterion_id = null) {
    $where = '';
    if( ! is_null( $criterion_id )) $where .= '
AND `criterion_id` = '.mysql_ureal_escape_string($criterion_id);

    $sql = '
SELECT `territory_id`, `criterion_id`, `percentage`
FROM `territory_criterion`
WHERE `territory_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_territory_criterion( $criterion_id, $percentage ) {
    $sql = "REPLACE INTO `territory_criterion` ( `territory_id`, `criterion_id`, `percentage` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $criterion_id, $percentage ).")";

    return mysql_uquery($sql);
  }

  public function del_territory_criterion( $criterion_id = null ) {
    $where = '';
    if( ! is_null( $criterion_id )) $where .= '
AND `criterion_id` = '.mysql_ureal_escape_string($criterion_id);
    $sql = 'DELETE FROM `territory_criterion`
    WHERE `territory_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_territory_neighbour_list($neighbour_id = null) {
    $where = '';
    if( ! is_null( $neighbour_id )) $where .= '
AND `neighbour_id` = '.mysql_ureal_escape_string($neighbour_id);

    $sql = '
SELECT `territory_id`, `neighbour_id`, `guid1`, `guid2`
FROM `territory_neighbour`
WHERE `territory_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_territory_neighbour( $neighbour_id, $guid1, $guid2 ) {
    $sql = "REPLACE INTO `territory_neighbour` ( `territory_id`, `neighbour_id`, `guid1`, `guid2` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $neighbour_id, $guid1, $guid2 ).")";

    return mysql_uquery($sql);
  }

  public function del_territory_neighbour( $neighbour_id = null ) {
    $where = '';
    if( ! is_null( $neighbour_id )) $where .= '
AND `neighbour_id` = '.mysql_ureal_escape_string($neighbour_id);
    $sql = 'DELETE FROM `territory_neighbour`
    WHERE `territory_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_territory_player_status_list($game_id = null, $turn = null, $player_id = null) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    if( ! is_null( $player_id )) $where .= '
AND `player_id` = '.mysql_ureal_escape_string($player_id);

    $sql = '
SELECT `game_id`, `turn`, `territory_id`, `player_id`, `supremacy`
FROM `territory_player_status`
WHERE `territory_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_territory_player_status( $game_id, $turn, $player_id, $supremacy ) {
    $sql = "REPLACE INTO `territory_player_status` ( `game_id`, `turn`, `territory_id`, `player_id`, `supremacy` ) VALUES (".mysql_ureal_escape_string( $game_id, $turn, $this->get_id(), $player_id, $supremacy ).")";

    return mysql_uquery($sql);
  }

  public function del_territory_player_status( $game_id = null, $turn = null, $player_id = null ) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    if( ! is_null( $player_id )) $where .= '
AND `player_id` = '.mysql_ureal_escape_string($player_id);
    $sql = 'DELETE FROM `territory_player_status`
    WHERE `territory_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_territory_player_troops_history_list($game_id = null, $turn = null, $player_id = null, $reason_player_id = null) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    if( ! is_null( $player_id )) $where .= '
AND `player_id` = '.mysql_ureal_escape_string($player_id);
    if( ! is_null( $reason_player_id )) $where .= '
AND `reason_player_id` = '.mysql_ureal_escape_string($reason_player_id);

    $sql = '
SELECT `game_id`, `turn`, `territory_id`, `player_id`, `delta`, `reason`, `reason_player_id`
FROM `territory_player_troops_history`
WHERE `territory_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_territory_player_troops_history( $game_id, $turn, $player_id, $delta, $reason, $reason_player_id = null ) {
    $sql = "REPLACE INTO `territory_player_troops_history` ( `game_id`, `turn`, `territory_id`, `player_id`, `delta`, `reason`, `reason_player_id` ) VALUES (".mysql_ureal_escape_string( $game_id, $turn, $this->get_id(), $player_id, $delta, $reason, $reason_player_id ).")";

    return mysql_uquery($sql);
  }

  public function del_territory_player_troops_history( $game_id = null, $turn = null, $player_id = null, $reason_player_id = null ) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    if( ! is_null( $player_id )) $where .= '
AND `player_id` = '.mysql_ureal_escape_string($player_id);
    if( ! is_null( $reason_player_id )) $where .= '
AND `reason_player_id` = '.mysql_ureal_escape_string($reason_player_id);
    $sql = 'DELETE FROM `territory_player_troops_history`
    WHERE `territory_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_territory_status_list($game_id = null, $turn = null, $owner_id = null) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    if( ! is_null( $owner_id )) $where .= '
AND `owner_id` = '.mysql_ureal_escape_string($owner_id);

    $sql = '
SELECT `territory_id`, `game_id`, `turn`, `owner_id`, `contested`, `capital`, `economy_ratio`
FROM `territory_status`
WHERE `territory_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_territory_status( $game_id, $turn, $owner_id = null, $contested, $capital, $economy_ratio ) {
    $sql = "REPLACE INTO `territory_status` ( `territory_id`, `game_id`, `turn`, `owner_id`, `contested`, `capital`, `economy_ratio` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $game_id, $turn, $owner_id, $contested, $capital, $economy_ratio ).")";

    return mysql_uquery($sql);
  }

  public function del_territory_status( $game_id = null, $turn = null, $owner_id = null ) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    if( ! is_null( $owner_id )) $where .= '
AND `owner_id` = '.mysql_ureal_escape_string($owner_id);
    $sql = 'DELETE FROM `territory_status`
    WHERE `territory_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }







  // CUSTOM

  //Custom content

  // /CUSTOM

}