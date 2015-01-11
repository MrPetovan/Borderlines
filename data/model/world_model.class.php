<?php
/**
 * Classe World
 *
 */

class World_Model extends DBObject {
  // Champs BD
  protected $_name = null;
  protected $_size_x = null;
  protected $_size_y = null;
  protected $_generation_method = null;
  protected $_generation_parameters = null;
  protected $_created = null;
  protected $_created_by = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "world"; }

  public function get_created()    { return guess_time($this->_created);}

  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_size_x($size_x) {
    if( is_numeric($size_x) && (int)$size_x == $size_x) $data = intval($size_x); else $data = null; $this->_size_x = $data;
  }
  public function set_size_y($size_y) {
    if( is_numeric($size_y) && (int)$size_y == $size_y) $data = intval($size_y); else $data = null; $this->_size_y = $data;
  }
  public function set_created($date) { $this->_created = guess_time($date, GUESS_DATE_MYSQL);}
  public function set_created_by($created_by) {
    if( is_numeric($created_by) && (int)$created_by == $created_by) $data = intval($created_by); else $data = null; $this->_created_by = $data;
  }

  /* FONCTIONS SQL */


  public static function db_get_by_created_by($created_by) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `created_by` = ".mysql_ureal_escape_string($created_by);

    return self::sql_to_list($sql);
  }

  public static function db_get_select_list( $with_null = false ) {
    $return = array();

    if( $with_null ) {
        $return[ null ] = 'N/A';
    }

    $object_list = World_Model::db_get_all();
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
        <p class="field">'.(is_array($this->get_size_x())?
          HTMLHelper::genererTextArea( "size_x", parameters_to_string( $this->get_size_x() ), array(), "Size X *" ):
          HTMLHelper::genererInputText( "size_x", $this->get_size_x(), array(), "Size X *")).'
        </p>
        <p class="field">'.(is_array($this->get_size_y())?
          HTMLHelper::genererTextArea( "size_y", parameters_to_string( $this->get_size_y() ), array(), "Size Y *" ):
          HTMLHelper::genererInputText( "size_y", $this->get_size_y(), array(), "Size Y *")).'
        </p>
        <p class="field">'.(is_array($this->get_generation_method())?
          HTMLHelper::genererTextArea( "generation_method", parameters_to_string( $this->get_generation_method() ), array(), "Generation Method" ):
          HTMLHelper::genererInputText( "generation_method", $this->get_generation_method(), array(), "Generation Method")).'
        </p>
        <p class="field">'.(is_array($this->get_generation_parameters())?
          HTMLHelper::genererTextArea( "generation_parameters", parameters_to_string( $this->get_generation_parameters() ), array(), "Generation Parameters" ):
          HTMLHelper::genererInputText( "generation_parameters", $this->get_generation_parameters(), array(), "Generation Parameters")).'
        </p>
        <p class="field">'.(is_array($this->get_created())?
          HTMLHelper::genererTextArea( "created", parameters_to_string( $this->get_created() ), array(), "Created *" ):
          HTMLHelper::genererInputText( "created", $this->get_created(), array(), "Created *")).'
        </p>';
      $option_list = array(null => 'Pas de choix');
      $player_list = Player::db_get_all();
      foreach( $player_list as $player)
        $option_list[ $player->id ] = $player->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('created_by', $option_list, $this->get_created_by(), array(), "Created By").'<a href="'.get_page_url('admin_player_mod').'">Créer un objet Player</a></p>

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
      case 2 : $return = "Le champ <strong>Size X</strong> est obligatoire."; break;
      case 3 : $return = "Le champ <strong>Size Y</strong> est obligatoire."; break;
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

    $return[] = Member::check_compulsory($this->get_name(), 1);
    $return[] = Member::check_compulsory($this->get_size_x(), 2, true);
    $return[] = Member::check_compulsory($this->get_size_y(), 3, true);
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