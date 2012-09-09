<?php
/**
 * Classe Conversation_Message
 *
 */

class Conversation_Message_Model extends DBObject {
  // Champs BD
  protected $_conversation_id = null;
  protected $_sender_id = null;
  protected $_receiver_id = null;
  protected $_text = null;
  protected $_created = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "conversation_message"; }

  public function get_created()    { return guess_time($this->_created);}

  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_conversation_id($conversation_id) {
    if( is_numeric($conversation_id) && (int)$conversation_id == $conversation_id) $data = intval($conversation_id); else $data = null; $this->_conversation_id = $data;
  }
  public function set_sender_id($sender_id) {
    if( is_numeric($sender_id) && (int)$sender_id == $sender_id) $data = intval($sender_id); else $data = null; $this->_sender_id = $data;
  }
  public function set_receiver_id($receiver_id) {
    if( is_numeric($receiver_id) && (int)$receiver_id == $receiver_id) $data = intval($receiver_id); else $data = null; $this->_receiver_id = $data;
  }
  public function set_created($date) { $this->_created = guess_time($date, GUESS_DATE_MYSQL);}

  /* FONCTIONS SQL */


  public static function db_get_by_conversation_id($conversation_id) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `conversation_id` = ".mysql_ureal_escape_string($conversation_id);

    return self::sql_to_list($sql);
  }
  public static function db_get_by_sender_id($sender_id) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `sender_id` = ".mysql_ureal_escape_string($sender_id);

    return self::sql_to_list($sql);
  }
  public static function db_get_by_receiver_id($receiver_id) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `receiver_id` = ".mysql_ureal_escape_string($receiver_id);

    return self::sql_to_list($sql);
  }

  public static function db_get_select_list( $with_null = false ) {
    $return = array();
    
    if( $with_null ) {
        $return[ null ] = 'N/A';
    }

    $object_list = Conversation_Message_Model::db_get_all();
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
      <p class="field">'.HTMLHelper::genererSelect('conversation_id', $option_list, $this->get_conversation_id(), array(), "Conversation Id *").'<a href="'.get_page_url('admin_conversation_mod').'">Créer un objet Conversation</a></p>
        <p class="field">'.HTMLHelper::genererInputText('sender_id', $this->get_sender_id(), array(), "Sender Id").'</p>';
      $option_list = array();
      $player_list = Player::db_get_all();
      foreach( $player_list as $player)
        $option_list[ $player->id ] = $player->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('receiver_id', $option_list, $this->get_receiver_id(), array(), "Receiver Id *").'<a href="'.get_page_url('admin_player_mod').'">Créer un objet Player</a></p>
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
      case 2 : $return = "Le champ <strong>Receiver Id</strong> est obligatoire."; break;
      case 3 : $return = "Le champ <strong>Text</strong> est obligatoire."; break;
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

    $return[] = Member::check_compulsory($this->get_conversation_id(), 1, true);
    $return[] = Member::check_compulsory($this->get_receiver_id(), 2, true);
    $return[] = Member::check_compulsory($this->get_text(), 3);
    $return[] = Member::check_compulsory($this->get_created(), 4);

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }





  // CUSTOM

  //Custom content

  // /CUSTOM

}