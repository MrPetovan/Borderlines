<?php
/**
 * Classe Player
 *
 */

class Player_Model extends DBObject {
  // Champs BD
  protected $_member_id = null;
  protected $_name = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "player"; }


  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_member_id($member_id) {
    if( is_numeric($member_id) && (int)$member_id == $member_id) $data = intval($member_id); else $data = null; $this->_member_id = $data;
  }

  /* FONCTIONS SQL */


  public static function db_get_select_list() {
    $return = array();

    $object_list = Player_Model::db_get_all();
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
        <p class="field">'.HTMLHelper::genererInputText('member_id', $this->get_member_id(), array(), "Member Id *").'</p>
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
      case 1 : $return = "Le champ <strong>Member Id</strong> est obligatoire."; break;
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

    $return[] = Member::check_compulsory($this->get_member_id(), 1);
    $return[] = Member::check_compulsory($this->get_name(), 2);

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }

  public function get_player_resource_history_list($resource_id = null, $datetime = null) {
    $where = '';
    if( ! is_null( $resource_id )) $where .= '
AND `resource_id` = '.mysql_ureal_escape_string($resource_id);
    if( ! is_null( $datetime )) $where .= '
AND `datetime` = '.mysql_ureal_escape_string($datetime);

    $sql = '
SELECT `player_id`, `resource_id`, `datetime`, `delta`, `reason`
FROM `player_resource_history`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_player_resource_history( $resource_id, $datetime, $delta, $reason ) {
    $sql = "REPLACE INTO `player_resource_history` ( `player_id`, `resource_id`, `datetime`, `delta`, `reason` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $resource_id, $datetime, $delta, $reason ).")";

    return mysql_uquery($sql);
  }

  public function del_player_resource_history( $resource_id = null, $datetime = null ) {
    $where = '';
    if( ! is_null( $resource_id )) $where .= '
AND `resource_id` = '.mysql_ureal_escape_string($resource_id);
    if( ! is_null( $datetime )) $where .= '
AND `datetime` = '.mysql_ureal_escape_string($datetime);
    $sql = 'DELETE FROM `player_resource_history`
    WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_player_spygame_value_list($value_guid = null, $datetime = null) {
    $where = '';
    if( ! is_null( $value_guid )) $where .= '
AND `value_guid` = '.mysql_ureal_escape_string($value_guid);
    if( ! is_null( $datetime )) $where .= '
AND `datetime` = '.mysql_ureal_escape_string($datetime);

    $sql = '
SELECT `player_id`, `value_guid`, `datetime`, `real_value`, `masked_value`
FROM `player_spygame_value`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_player_spygame_value( $value_guid, $datetime, $real_value, $masked_value ) {
    $sql = "REPLACE INTO `player_spygame_value` ( `player_id`, `value_guid`, `datetime`, `real_value`, `masked_value` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $value_guid, $datetime, $real_value, $masked_value ).")";

    return mysql_uquery($sql);
  }

  public function del_player_spygame_value( $value_guid = null, $datetime = null ) {
    $where = '';
    if( ! is_null( $value_guid )) $where .= '
AND `value_guid` = '.mysql_ureal_escape_string($value_guid);
    if( ! is_null( $datetime )) $where .= '
AND `datetime` = '.mysql_ureal_escape_string($datetime);
    $sql = 'DELETE FROM `player_spygame_value`
    WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }







  // CUSTOM

  //Custom content

  // /CUSTOM

}