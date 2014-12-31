<?php
/**
 * Classe Shout
 *
 */

class Shout_Model extends DBObject {
  // Champs BD
  protected $_date_sent = null;
  protected $_shouter_id = null;
  protected $_text = null;
  protected $_game_id = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "shout"; }

  public function get_date_sent()    { return guess_time($this->_date_sent);}

  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_date_sent($date) { $this->_date_sent = guess_time($date, GUESS_DATE_MYSQL);}
  public function set_shouter_id($shouter_id) {
    if( is_numeric($shouter_id) && (int)$shouter_id == $shouter_id) $data = intval($shouter_id); else $data = null; $this->_shouter_id = $data;
  }
  public function set_game_id($game_id) {
    if( is_numeric($game_id) && (int)$game_id == $game_id) $data = intval($game_id); else $data = null; $this->_game_id = $data;
  }

  /* FONCTIONS SQL */


  public static function db_get_by_shouter_id($shouter_id) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `shouter_id` = ".mysql_ureal_escape_string($shouter_id);

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

    $object_list = Shout_Model::db_get_all();
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
        '.HTMLHelper::genererInputHidden('id', $this->get_id()).'
        <p class="field">'.(is_array($this->get_date_sent())?
          HTMLHelper::genererTextArea( "date_sent", parameters_to_string( $this->get_date_sent() ), array(), "Date Sent *" ):
          HTMLHelper::genererInputText( "date_sent", $this->get_date_sent(), array(), "Date Sent *")).'
        </p>';
      $option_list = array();
      $player_list = Player::db_get_all();
      foreach( $player_list as $player)
        $option_list[ $player->id ] = $player->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('shouter_id', $option_list, $this->get_shouter_id(), array(), "Shouter Id *").'<a href="'.get_page_url('admin_player_mod').'">Créer un objet Player</a></p>
        <p class="field">'.(is_array($this->get_text())?
          HTMLHelper::genererTextArea( "text", parameters_to_string( $this->get_text() ), array(), "Text *" ):
          HTMLHelper::genererInputText( "text", $this->get_text(), array(), "Text *")).'
        </p>';
      $option_list = array(null => 'Pas de choix');
      $game_list = Game::db_get_all();
      foreach( $game_list as $game)
        $option_list[ $game->id ] = $game->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('game_id', $option_list, $this->get_game_id(), array(), "Game Id").'<a href="'.get_page_url('admin_game_mod').'">Créer un objet Game</a></p>

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
      case 1 : $return = "Le champ <strong>Date Sent</strong> est obligatoire."; break;
      case 2 : $return = "Le champ <strong>Shouter Id</strong> est obligatoire."; break;
      case 3 : $return = "Le champ <strong>Text</strong> est obligatoire."; break;
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

    $return[] = Member::check_compulsory($this->get_date_sent(), 1);
    $return[] = Member::check_compulsory($this->get_shouter_id(), 2, true);
    $return[] = Member::check_compulsory($this->get_text(), 3);

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