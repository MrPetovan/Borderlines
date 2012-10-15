<?php
/**
 * Classe Resource
 *
 */

class Resource_Model extends DBObject {
  // Champs BD
  protected $_name = null;
  protected $_public = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "resource"; }

  public function get_public() { return $this->is_public(); }
  public function is_public() { return ($this->_public == 1); }

  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_public($public) {
    if($public) $data = 1; else $data = 0; $this->_public = $data;
  }

  /* FONCTIONS SQL */



  public static function db_get_select_list( $with_null = false ) {
    $return = array();

    if( $with_null ) {
        $return[ null ] = 'N/A';
    }

    $object_list = Resource_Model::db_get_all();
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
        <p class="field">'.HTMLHelper::genererInputText('name', $this->get_name(), array(), "Name *").'</p>
        <p class="field">'.HTMLHelper::genererInputCheckBox('public', '1', $this->get_public(), array('label_position' => 'right'), "Public" ).'</p>

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
      case 2 : $return = "Le champ <strong>Public</strong> est obligatoire."; break;
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
    $return[] = Member::check_compulsory($this->get_public(), 2, true);

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }

  public function get_player_resource_history_list($game_id = null, $player_id = null, $player_order_id = null) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $player_id )) $where .= '
AND `player_id` = '.mysql_ureal_escape_string($player_id);
    if( ! is_null( $player_order_id )) $where .= '
AND `player_order_id` = '.mysql_ureal_escape_string($player_order_id);

    $sql = '
SELECT `game_id`, `player_id`, `resource_id`, `turn`, `datetime`, `delta`, `reason`, `player_order_id`
FROM `player_resource_history`
WHERE `resource_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_player_resource_history( $game_id, $player_id, $turn, $datetime, $delta, $reason, $player_order_id = null ) {
    $sql = "REPLACE INTO `player_resource_history` ( `game_id`, `player_id`, `resource_id`, `turn`, `datetime`, `delta`, `reason`, `player_order_id` ) VALUES (".mysql_ureal_escape_string( $game_id, $player_id, $this->get_id(), $turn, guess_time( $datetime, GUESS_TIME_MYSQL ), $delta, $reason, $player_order_id ).")";

    return mysql_uquery($sql);
  }

  public function del_player_resource_history( $game_id = null, $player_id = null, $player_order_id = null ) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $player_id )) $where .= '
AND `player_id` = '.mysql_ureal_escape_string($player_id);
    if( ! is_null( $player_order_id )) $where .= '
AND `player_order_id` = '.mysql_ureal_escape_string($player_order_id);
    $sql = 'DELETE FROM `player_resource_history`
    WHERE `resource_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }







  // CUSTOM

  //Custom content

  // /CUSTOM

}