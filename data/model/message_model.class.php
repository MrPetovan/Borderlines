<?php
/**
 * Classe Message
 *
 */

class Message_Model extends DBObject {
  // Champs BD
  protected $_conversation_id = null;
  protected $_player_id = null;
  protected $_text = null;
  protected $_created = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "message"; }

  public function get_created()    { return guess_time($this->_created);}

  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_conversation_id($conversation_id) {
    if( is_numeric($conversation_id) && (int)$conversation_id == $conversation_id) $data = intval($conversation_id); else $data = null; $this->_conversation_id = $data;
  }
  public function set_player_id($player_id) {
    if( is_numeric($player_id) && (int)$player_id == $player_id) $data = intval($player_id); else $data = null; $this->_player_id = $data;
  }
  public function set_created($date) { $this->_created = guess_time($date, GUESS_DATE_MYSQL);}

  /* FONCTIONS SQL */


  public static function db_get_by_conversation_id($conversation_id) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `conversation_id` = ".mysql_ureal_escape_string($conversation_id);

    return self::sql_to_list($sql);
  }
  public static function db_get_by_player_id($player_id) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `player_id` = ".mysql_ureal_escape_string($player_id);

    return self::sql_to_list($sql);
  }

  public static function db_get_select_list( $with_null = false ) {
    $return = array();

    if( $with_null ) {
        $return[ null ] = 'N/A';
    }

    $object_list = Message_Model::db_get_all();
    foreach( $object_list as $object ) $return[ $object->get_id() ] = $object->get_id();

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
      $conversation_list = Conversation::db_get_all();
      foreach( $conversation_list as $conversation)
        $option_list[ $conversation->id ] = $conversation->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('conversation_id', $option_list, $this->get_conversation_id(), array(), "Conversation Id *").'<a href="'.get_page_url('admin_conversation_mod').'">Créer un objet Conversation</a></p>';
      $option_list = array(null => 'Pas de choix');
      $player_list = Player::db_get_all();
      foreach( $player_list as $player)
        $option_list[ $player->id ] = $player->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('player_id', $option_list, $this->get_player_id(), array(), "Player Id").'<a href="'.get_page_url('admin_player_mod').'">Créer un objet Player</a></p>
        <p class="field">'.HTMLHelper::genererInputText('text', $this->get_text(), array(), "Text *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('created', $this->get_created(), array(), "Created *").'</p>

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
      case 1 : $return = "Le champ <strong>Conversation Id</strong> est obligatoire."; break;
      case 2 : $return = "Le champ <strong>Text</strong> est obligatoire."; break;
      case 3 : $return = "Le champ <strong>Created</strong> est obligatoire."; break;
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

    $return[] = Member::check_compulsory($this->get_conversation_id(), 1, true);
    $return[] = Member::check_compulsory($this->get_text(), 2);
    $return[] = Member::check_compulsory($this->get_created(), 3);

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }

  public function get_message_recipient_list($player_id = null) {
    $where = '';
    if( ! is_null( $player_id )) $where .= '
AND `player_id` = '.mysql_ureal_escape_string($player_id);

    $sql = '
SELECT `message_id`, `player_id`, `read`
FROM `message_recipient`
WHERE `message_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_message_recipient( $player_id, $read = null ) {
    $sql = "REPLACE INTO `message_recipient` ( `message_id`, `player_id`, `read` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $player_id, guess_time( $read, GUESS_TIME_MYSQL ) ).")";

    return mysql_uquery($sql);
  }

  public function del_message_recipient( $player_id = null ) {
    $where = '';
    if( ! is_null( $player_id )) $where .= '
AND `player_id` = '.mysql_ureal_escape_string($player_id);
    $sql = 'DELETE FROM `message_recipient`
    WHERE `message_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }







  // CUSTOM

  //Custom content

  // /CUSTOM

}