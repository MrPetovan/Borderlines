<?php
/**
 * Classe Game
 *
 */

class Game_Model extends DBObject {
  // Champs BD
  protected $_name = null;
  protected $_current_turn = null;
  protected $_turn_interval = null;
  protected $_turn_limit = null;
  protected $_created = null;
  protected $_started = null;
  protected $_updated = null;
  protected $_ended = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "game"; }

  public function get_created()    { return guess_time($this->_created);}
  public function get_started()    { return guess_time($this->_started);}
  public function get_updated()    { return guess_time($this->_updated);}
  public function get_ended()    { return guess_time($this->_ended);}

  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_current_turn($current_turn) {
    if( is_numeric($current_turn) && (int)$current_turn == $current_turn) $data = intval($current_turn); else $data = null; $this->_current_turn = $data;
  }
  public function set_turn_interval($turn_interval) {
    if( is_numeric($turn_interval) && (int)$turn_interval == $turn_interval) $data = intval($turn_interval); else $data = null; $this->_turn_interval = $data;
  }
  public function set_turn_limit($turn_limit) {
    if( is_numeric($turn_limit) && (int)$turn_limit == $turn_limit) $data = intval($turn_limit); else $data = null; $this->_turn_limit = $data;
  }
  public function set_created($date) { $this->_created = guess_time($date, GUESS_DATE_MYSQL);}
  public function set_started($date) { $this->_started = guess_time($date, GUESS_DATE_MYSQL);}
  public function set_updated($date) { $this->_updated = guess_time($date, GUESS_DATE_MYSQL);}
  public function set_ended($date) { $this->_ended = guess_time($date, GUESS_DATE_MYSQL);}

  /* FONCTIONS SQL */



  public static function db_get_select_list() {
    $return = array();

    $object_list = Game_Model::db_get_all();
    foreach( $object_list as $object ) $return[ $object->get_id() ] = $object->get_name();

    return $return;
  }

  /* FONCTIONS HTML */

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
        <p class="field">'.HTMLHelper::genererInputText('current_turn', $this->get_current_turn(), array(), "Current Turn *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('turn_interval', $this->get_turn_interval(), array(), "Turn Interval *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('turn_limit', $this->get_turn_limit(), array(), "Turn Limit *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('created', $this->get_created(), array(), "Created *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('started', $this->get_started(), array(), "Started").'</p>
        <p class="field">'.HTMLHelper::genererInputText('updated', $this->get_updated(), array(), "Updated").'</p>
        <p class="field">'.HTMLHelper::genererInputText('ended', $this->get_ended(), array(), "Ended").'</p>
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
      case 2 : $return = "Le champ <strong>Current Turn</strong> est obligatoire."; break;
      case 3 : $return = "Le champ <strong>Turn Interval</strong> est obligatoire."; break;
      case 4 : $return = "Le champ <strong>Turn Limit</strong> est obligatoire."; break;
      case 5 : $return = "Le champ <strong>Created</strong> est obligatoire."; break;
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
    $return[] = Member::check_compulsory($this->get_current_turn(), 2);
    $return[] = Member::check_compulsory($this->get_turn_interval(), 3);
    $return[] = Member::check_compulsory($this->get_turn_limit(), 4);
    $return[] = Member::check_compulsory($this->get_created(), 5);

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }

  public function get_game_player_list($player_id = null) {
    $where = '';
    if( ! is_null( $player_id )) $where .= '
AND `player_id` = '.mysql_ureal_escape_string($player_id);

    $sql = '
SELECT `game_id`, `player_id`
FROM `game_player`
WHERE `game_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_game_player( $player_id ) {
    $sql = "REPLACE INTO `game_player` ( `game_id`, `player_id` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $player_id ).")";

    return mysql_uquery($sql);
  }

  public function del_game_player( $player_id = null ) {
    $where = '';
    if( ! is_null( $player_id )) $where .= '
AND `player_id` = '.mysql_ureal_escape_string($player_id);
    $sql = 'DELETE FROM `game_player`
    WHERE `game_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_player_resource_history_list($player_id = null, $resource_id = null, $player_order_id = null) {
    $where = '';
    if( ! is_null( $player_id )) $where .= '
AND `player_id` = '.mysql_ureal_escape_string($player_id);
    if( ! is_null( $resource_id )) $where .= '
AND `resource_id` = '.mysql_ureal_escape_string($resource_id);
    if( ! is_null( $player_order_id )) $where .= '
AND `player_order_id` = '.mysql_ureal_escape_string($player_order_id);

    $sql = '
SELECT `game_id`, `player_id`, `resource_id`, `turn`, `datetime`, `delta`, `reason`, `player_order_id`
FROM `player_resource_history`
WHERE `game_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_player_resource_history( $player_id, $resource_id, $turn, $datetime, $delta, $reason, $player_order_id ) {
    $sql = "REPLACE INTO `player_resource_history` ( `game_id`, `player_id`, `resource_id`, `turn`, `datetime`, `delta`, `reason`, `player_order_id` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $player_id, $resource_id, $turn, $datetime, $delta, $reason, $player_order_id ).")";

    return mysql_uquery($sql);
  }

  public function del_player_resource_history( $player_id = null, $resource_id = null, $player_order_id = null ) {
    $where = '';
    if( ! is_null( $player_id )) $where .= '
AND `player_id` = '.mysql_ureal_escape_string($player_id);
    if( ! is_null( $resource_id )) $where .= '
AND `resource_id` = '.mysql_ureal_escape_string($resource_id);
    if( ! is_null( $player_order_id )) $where .= '
AND `player_order_id` = '.mysql_ureal_escape_string($player_order_id);
    $sql = 'DELETE FROM `player_resource_history`
    WHERE `game_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }







  // CUSTOM

  //Custom content

  // /CUSTOM

}