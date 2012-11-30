<?php
/**
 * Classe Player
 *
 */

class Player_Model extends DBObject {
  // Champs BD
  protected $_member_id = null;
  protected $_name = null;
  protected $_active = null;
  protected $_api_key = null;
  protected $_created = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "player"; }

  public function get_active() { return $this->is_active(); }
  public function is_active() { return ($this->_active == 1); }
  public function get_created()    { return guess_time($this->_created);}

  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_member_id($member_id) {
    if( is_numeric($member_id) && (int)$member_id == $member_id) $data = intval($member_id); else $data = null; $this->_member_id = $data;
  }
  public function set_active($active) {
    if($active) $data = 1; else $data = 0; $this->_active = $data;
  }
  public function set_created($date) { $this->_created = guess_time($date, GUESS_DATE_MYSQL);}

  /* FONCTIONS SQL */


  public static function db_get_by_member_id($member_id) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `member_id` = ".mysql_ureal_escape_string($member_id);

    return self::sql_to_list($sql);
  }

  public static function db_get_select_list( $with_null = false ) {
    $return = array();

    if( $with_null ) {
        $return[ null ] = 'N/A';
    }

    $object_list = Player_Model::db_get_all();
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
        '.HTMLHelper::genererInputHidden('id', $this->get_id()).'';
      $option_list = array();
      $member_list = Member::db_get_all();
      foreach( $member_list as $member)
        $option_list[ $member->id ] = $member->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('member_id', $option_list, $this->get_member_id(), array(), "Member Id *").'<a href="'.get_page_url('admin_member_mod').'">Créer un objet Member</a></p>
        <p class="field">'.(is_array($this->get_name())?
          HTMLHelper::genererTextArea( "name", parameters_to_string( $this->get_name() ), array(), "Name *" ):
          HTMLHelper::genererInputText( "name", $this->get_name(), array(), "Name *")).'
        </p>
        <p class="field">'.HTMLHelper::genererInputCheckBox('active', '1', $this->get_active(), array('label_position' => 'right'), "Active" ).'</p>
        <p class="field">'.(is_array($this->get_api_key())?
          HTMLHelper::genererTextArea( "api_key", parameters_to_string( $this->get_api_key() ), array(), "Api Key *" ):
          HTMLHelper::genererInputText( "api_key", $this->get_api_key(), array(), "Api Key *")).'
        </p>
        <p class="field">'.(is_array($this->get_created())?
          HTMLHelper::genererTextArea( "created", parameters_to_string( $this->get_created() ), array(), "Created *" ):
          HTMLHelper::genererInputText( "created", $this->get_created(), array(), "Created *")).'
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
      case 1 : $return = "Le champ <strong>Member Id</strong> est obligatoire."; break;
      case 2 : $return = "Le champ <strong>Name</strong> est obligatoire."; break;
      case 3 : $return = "Le champ <strong>Api Key</strong> est obligatoire."; break;
      case 4 : $return = "Le champ <strong>Created</strong> est obligatoire."; break;
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

    $return[] = Member::check_compulsory($this->get_member_id(), 1, true);
    $return[] = Member::check_compulsory($this->get_name(), 2);
    $return[] = Member::check_compulsory($this->get_api_key(), 3);
    $return[] = Member::check_compulsory($this->get_created(), 4);

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }

  public function get_conversation_player_list($conversation_id = null) {
    $where = '';
    if( ! is_null( $conversation_id )) $where .= '
AND `conversation_id` = '.mysql_ureal_escape_string($conversation_id);

    $sql = '
SELECT `conversation_id`, `player_id`, `archived`, `left`
FROM `conversation_player`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_conversation_player( $conversation_id, $archived = null, $left = null ) {
    $sql = "REPLACE INTO `conversation_player` ( `conversation_id`, `player_id`, `archived`, `left` ) VALUES (".mysql_ureal_escape_string( $conversation_id, $this->get_id(), guess_time( $archived, GUESS_TIME_MYSQL ), guess_time( $left, GUESS_TIME_MYSQL ) ).")";

    return mysql_uquery($sql);
  }

  public function del_conversation_player( $conversation_id = null ) {
    $where = '';
    if( ! is_null( $conversation_id )) $where .= '
AND `conversation_id` = '.mysql_ureal_escape_string($conversation_id);
    $sql = 'DELETE FROM `conversation_player`
    WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_game_player_list($game_id = null) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);

    $sql = '
SELECT `game_id`, `player_id`, `turn_ready`, `turn_leave`
FROM `game_player`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_game_player( $game_id, $turn_ready, $turn_leave = null ) {
    $sql = "REPLACE INTO `game_player` ( `game_id`, `player_id`, `turn_ready`, `turn_leave` ) VALUES (".mysql_ureal_escape_string( $game_id, $this->get_id(), $turn_ready, $turn_leave ).")";

    return mysql_uquery($sql);
  }

  public function del_game_player( $game_id = null ) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    $sql = 'DELETE FROM `game_player`
    WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_message_recipient_list($message_id = null) {
    $where = '';
    if( ! is_null( $message_id )) $where .= '
AND `message_id` = '.mysql_ureal_escape_string($message_id);

    $sql = '
SELECT `message_id`, `player_id`, `read`
FROM `message_recipient`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_message_recipient( $message_id, $read = null ) {
    $sql = "REPLACE INTO `message_recipient` ( `message_id`, `player_id`, `read` ) VALUES (".mysql_ureal_escape_string( $message_id, $this->get_id(), guess_time( $read, GUESS_TIME_MYSQL ) ).")";

    return mysql_uquery($sql);
  }

  public function del_message_recipient( $message_id = null ) {
    $where = '';
    if( ! is_null( $message_id )) $where .= '
AND `message_id` = '.mysql_ureal_escape_string($message_id);
    $sql = 'DELETE FROM `message_recipient`
    WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_player_diplomacy_list($game_id = null, $turn = null, $to_player_id = null) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    if( ! is_null( $to_player_id )) $where .= '
AND `to_player_id` = '.mysql_ureal_escape_string($to_player_id);

    $sql = '
SELECT `game_id`, `turn`, `from_player_id`, `to_player_id`, `status`
FROM `player_diplomacy`
WHERE `from_player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_player_diplomacy( $game_id, $turn, $to_player_id, $status ) {
    $sql = "REPLACE INTO `player_diplomacy` ( `game_id`, `turn`, `from_player_id`, `to_player_id`, `status` ) VALUES (".mysql_ureal_escape_string( $game_id, $turn, $this->get_id(), $to_player_id, $status ).")";

    return mysql_uquery($sql);
  }

  public function del_player_diplomacy( $game_id = null, $turn = null, $to_player_id = null ) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    if( ! is_null( $to_player_id )) $where .= '
AND `to_player_id` = '.mysql_ureal_escape_string($to_player_id);
    $sql = 'DELETE FROM `player_diplomacy`
    WHERE `from_player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_player_history_list($game_id = null, $territory_id = null) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $territory_id )) $where .= '
AND `territory_id` = '.mysql_ureal_escape_string($territory_id);

    $sql = '
SELECT `game_id`, `player_id`, `turn`, `datetime`, `reason`, `territory_id`
FROM `player_history`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_player_history( $game_id, $turn, $datetime, $reason, $territory_id = null ) {
    $sql = "REPLACE INTO `player_history` ( `game_id`, `player_id`, `turn`, `datetime`, `reason`, `territory_id` ) VALUES (".mysql_ureal_escape_string( $game_id, $this->get_id(), $turn, guess_time( $datetime, GUESS_TIME_MYSQL ), $reason, $territory_id ).")";

    return mysql_uquery($sql);
  }

  public function del_player_history( $game_id = null, $territory_id = null ) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $territory_id )) $where .= '
AND `territory_id` = '.mysql_ureal_escape_string($territory_id);
    $sql = 'DELETE FROM `player_history`
    WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_player_spygame_value_list($game_id = null, $value_guid = null, $turn = null) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $value_guid )) $where .= '
AND `value_guid` = '.mysql_ureal_escape_string($value_guid);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);

    $sql = '
SELECT `game_id`, `player_id`, `value_guid`, `turn`, `datetime`, `real_value`, `masked_value`
FROM `player_spygame_value`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_player_spygame_value( $game_id, $value_guid, $turn, $datetime, $real_value, $masked_value = null ) {
    $sql = "REPLACE INTO `player_spygame_value` ( `game_id`, `player_id`, `value_guid`, `turn`, `datetime`, `real_value`, `masked_value` ) VALUES (".mysql_ureal_escape_string( $game_id, $this->get_id(), $value_guid, $turn, guess_time( $datetime, GUESS_TIME_MYSQL ), $real_value, $masked_value ).")";

    return mysql_uquery($sql);
  }

  public function del_player_spygame_value( $game_id = null, $value_guid = null, $turn = null ) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $value_guid )) $where .= '
AND `value_guid` = '.mysql_ureal_escape_string($value_guid);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    $sql = 'DELETE FROM `player_spygame_value`
    WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_territory_owner_list($territory_id = null, $game_id = null, $turn = null) {
    $where = '';
    if( ! is_null( $territory_id )) $where .= '
AND `territory_id` = '.mysql_ureal_escape_string($territory_id);
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);

    $sql = '
SELECT `territory_id`, `game_id`, `turn`, `owner_id`, `contested`, `capital`
FROM `territory_owner`
WHERE `owner_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_territory_owner( $territory_id, $game_id, $turn, $contested, $capital ) {
    $sql = "REPLACE INTO `territory_owner` ( `territory_id`, `game_id`, `turn`, `owner_id`, `contested`, `capital` ) VALUES (".mysql_ureal_escape_string( $territory_id, $game_id, $turn, $this->get_id(), $contested, $capital ).")";

    return mysql_uquery($sql);
  }

  public function del_territory_owner( $territory_id = null, $game_id = null, $turn = null ) {
    $where = '';
    if( ! is_null( $territory_id )) $where .= '
AND `territory_id` = '.mysql_ureal_escape_string($territory_id);
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    $sql = 'DELETE FROM `territory_owner`
    WHERE `owner_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_territory_player_status_list($game_id = null, $turn = null, $territory_id = null) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    if( ! is_null( $territory_id )) $where .= '
AND `territory_id` = '.mysql_ureal_escape_string($territory_id);

    $sql = '
SELECT `game_id`, `turn`, `territory_id`, `player_id`, `supremacy`
FROM `territory_player_status`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_territory_player_status( $game_id, $turn, $territory_id, $supremacy ) {
    $sql = "REPLACE INTO `territory_player_status` ( `game_id`, `turn`, `territory_id`, `player_id`, `supremacy` ) VALUES (".mysql_ureal_escape_string( $game_id, $turn, $territory_id, $this->get_id(), $supremacy ).")";

    return mysql_uquery($sql);
  }

  public function del_territory_player_status( $game_id = null, $turn = null, $territory_id = null ) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    if( ! is_null( $territory_id )) $where .= '
AND `territory_id` = '.mysql_ureal_escape_string($territory_id);
    $sql = 'DELETE FROM `territory_player_status`
    WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_territory_player_troops_history_list($game_id = null, $turn = null, $territory_id = null, $reason_player_id = null) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    if( ! is_null( $territory_id )) $where .= '
AND `territory_id` = '.mysql_ureal_escape_string($territory_id);
    if( ! is_null( $reason_player_id )) $where .= '
AND `reason_player_id` = '.mysql_ureal_escape_string($reason_player_id);

    $sql = '
SELECT `game_id`, `turn`, `territory_id`, `player_id`, `delta`, `reason`, `reason_player_id`
FROM `territory_player_troops_history`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_territory_player_troops_history( $game_id, $turn, $territory_id, $delta, $reason, $reason_player_id = null ) {
    $sql = "REPLACE INTO `territory_player_troops_history` ( `game_id`, `turn`, `territory_id`, `player_id`, `delta`, `reason`, `reason_player_id` ) VALUES (".mysql_ureal_escape_string( $game_id, $turn, $territory_id, $this->get_id(), $delta, $reason, $reason_player_id ).")";

    return mysql_uquery($sql);
  }

  public function del_territory_player_troops_history( $game_id = null, $turn = null, $territory_id = null, $reason_player_id = null ) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND `game_id` = '.mysql_ureal_escape_string($game_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    if( ! is_null( $territory_id )) $where .= '
AND `territory_id` = '.mysql_ureal_escape_string($territory_id);
    if( ! is_null( $reason_player_id )) $where .= '
AND `reason_player_id` = '.mysql_ureal_escape_string($reason_player_id);
    $sql = 'DELETE FROM `territory_player_troops_history`
    WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }







  // CUSTOM

  //Custom content

  // /CUSTOM

}