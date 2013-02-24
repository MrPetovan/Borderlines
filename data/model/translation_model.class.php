<?php
/**
 * Classe Translation
 *
 */

class Translation_Model extends DBObject {
  // Champs BD
  protected $_code = null;
  protected $_locale = null;
  protected $_translation = null;
  protected $_context = null;
  protected $_translator_id = null;
  protected $_created = null;
  protected $_updated = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "translation"; }

  public function get_created()    { return guess_time($this->_created);}
  public function get_updated()    { return guess_time($this->_updated);}

  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_translator_id($translator_id) {
    if( is_numeric($translator_id) && (int)$translator_id == $translator_id) $data = intval($translator_id); else $data = null; $this->_translator_id = $data;
  }
  public function set_created($date) { $this->_created = guess_time($date, GUESS_DATE_MYSQL);}
  public function set_updated($date) { $this->_updated = guess_time($date, GUESS_DATE_MYSQL);}

  /* FONCTIONS SQL */


  public static function db_get_by_code($code) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `code` = ".mysql_ureal_escape_string($code);

    return self::sql_to_list($sql);
  }
  public static function db_get_by_translator_id($translator_id) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `translator_id` = ".mysql_ureal_escape_string($translator_id);

    return self::sql_to_list($sql);
  }

  public static function db_get_select_list( $with_null = false ) {
    $return = array();

    if( $with_null ) {
        $return[ null ] = 'N/A';
    }

    $object_list = Translation_Model::db_get_all();
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
        <p class="field">'.(is_array($this->get_code())?
          HTMLHelper::genererTextArea( "code", parameters_to_string( $this->get_code() ), array(), "Code *" ):
          HTMLHelper::genererInputText( "code", $this->get_code(), array(), "Code *")).'
        </p>
        <p class="field">'.(is_array($this->get_locale())?
          HTMLHelper::genererTextArea( "locale", parameters_to_string( $this->get_locale() ), array(), "Locale *" ):
          HTMLHelper::genererInputText( "locale", $this->get_locale(), array(), "Locale *")).'
        </p>
        <p class="field">'.(is_array($this->get_translation())?
          HTMLHelper::genererTextArea( "translation", parameters_to_string( $this->get_translation() ), array(), "Translation" ):
          HTMLHelper::genererInputText( "translation", $this->get_translation(), array(), "Translation")).'
        </p>
        <p class="field">'.(is_array($this->get_context())?
          HTMLHelper::genererTextArea( "context", parameters_to_string( $this->get_context() ), array(), "Context" ):
          HTMLHelper::genererInputText( "context", $this->get_context(), array(), "Context")).'
        </p>
        <p class="field">'.(is_array($this->get_translator_id())?
          HTMLHelper::genererTextArea( "translator_id", parameters_to_string( $this->get_translator_id() ), array(), "Translator Id" ):
          HTMLHelper::genererInputText( "translator_id", $this->get_translator_id(), array(), "Translator Id")).'
        </p>
        <p class="field">'.(is_array($this->get_created())?
          HTMLHelper::genererTextArea( "created", parameters_to_string( $this->get_created() ), array(), "Created *" ):
          HTMLHelper::genererInputText( "created", $this->get_created(), array(), "Created *")).'
        </p>
        <p class="field">'.(is_array($this->get_updated())?
          HTMLHelper::genererTextArea( "updated", parameters_to_string( $this->get_updated() ), array(), "Updated *" ):
          HTMLHelper::genererInputText( "updated", $this->get_updated(), array(), "Updated *")).'
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
      case 1 : $return = "Le champ <strong>Code</strong> est obligatoire."; break;
      case 2 : $return = "Le champ <strong>Locale</strong> est obligatoire."; break;
      case 3 : $return = "Le champ <strong>Created</strong> est obligatoire."; break;
      case 4 : $return = "Le champ <strong>Updated</strong> est obligatoire."; break;
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

    $return[] = Member::check_compulsory($this->get_code(), 1);
    $return[] = Member::check_compulsory($this->get_locale(), 2);
    $return[] = Member::check_compulsory($this->get_created(), 3);
    $return[] = Member::check_compulsory($this->get_updated(), 4);

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