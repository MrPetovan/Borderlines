<?php
/**
 * Classe Conversation
 *
 */

class Conversation_Model extends DBObject {
  // Champs BD
  protected $_player_id = null;
  protected $_game_id = null;
  protected $_subject = null;
  protected $_created = null;
  protected $_archived = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "conversation"; }

  public function get_created()    { return guess_time($this->_created);}
  public function get_archived()    { return guess_time($this->_archived);}

  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_player_id($player_id) {
    if( is_numeric($player_id) && (int)$player_id == $player_id) $data = intval($player_id); else $data = null; $this->_player_id = $data;
  }
  public function set_game_id($game_id) {
    if( is_numeric($game_id) && (int)$game_id == $game_id) $data = intval($game_id); else $data = null; $this->_game_id = $data;
  }
  public function set_created($date) { $this->_created = guess_time($date, GUESS_DATE_MYSQL);}
  public function set_archived($date) { $this->_archived = guess_time($date, GUESS_DATE_MYSQL);}

  /* FONCTIONS SQL */


  public static function db_get_by_player_id($player_id) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `player_id` = ".mysql_ureal_escape_string($player_id);

    return self::sql_to_list($sql);
  }
  public static function db_get_by_game_id($game_id) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `game_id` = ".mysql_ureal_escape_string($game_id);

    return self::sql_to_list($sql);
  }

  public static function db_get_select_list( $with_null = false ) {
    $return = array();
    
    if( $with_null ) {
        $return[ null ] = 'N/A';
    }

    $object_list = Conversation_Model::db_get_all();
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
      $player_list = Player::db_get_all();
      foreach( $player_list as $player)
        $option_list[ $player->id ] = $player->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('player_id', $option_list, $this->get_player_id(), array(), "Player Id *").'<a href="'.get_page_url('admin_player_mod').'">Créer un objet Player</a></p>';
      $option_list = array(null => 'Pas de choix');
      $game_list = Game::db_get_all();
      foreach( $game_list as $game)
        $option_list[ $game->id ] = $game->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('game_id', $option_list, $this->get_game_id(), array(), "Game Id").'<a href="'.get_page_url('admin_game_mod').'">Créer un objet Game</a></p>
        <p class="field">'.HTMLHelper::genererInputText('subject', $this->get_subject(), array(), "Subject *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('created', $this->get_created(), array(), "Created *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('archived', $this->get_archived(), array(), "Archived").'</p>

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
      case 1 : $return = "Le champ <strong>Player Id</strong> est obligatoire."; break;
      case 2 : $return = "Le champ <strong>Subject</strong> est obligatoire."; break;
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

    $return[] = Member::check_compulsory($this->get_player_id(), 1, true);
    $return[] = Member::check_compulsory($this->get_subject(), 2);
    $return[] = Member::check_compulsory($this->get_created(), 3);

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }

  public function get_conversation_player_list($player_id = null) {
    $where = '';
    if( ! is_null( $player_id )) $where .= '
AND `player_id` = '.mysql_ureal_escape_string($player_id);

    $sql = '
SELECT `conversation_id`, `player_id`
FROM `conversation_player`
WHERE `conversation_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_conversation_player( $player_id ) {
    $sql = "REPLACE INTO `conversation_player` ( `conversation_id`, `player_id` ) VALUES (".mysql_ureal_escape_string( $this->get_id(), $player_id ).")";

    return mysql_uquery($sql);
  }

  public function del_conversation_player( $player_id = null ) {
    $where = '';
    if( ! is_null( $player_id )) $where .= '
AND `player_id` = '.mysql_ureal_escape_string($player_id);
    $sql = 'DELETE FROM `conversation_player`
    WHERE `conversation_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }







  // CUSTOM

  //Custom content

  // /CUSTOM

}