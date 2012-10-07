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

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "translation"; }


  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }

  /* FONCTIONS SQL */


  public static function db_get_by_code($code) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `code` = ".mysql_ureal_escape_string($code);

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
        <p class="field">'.HTMLHelper::genererInputText('code', $this->get_code(), array(), "Code *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('locale', $this->get_locale(), array(), "Locale *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('translation', $this->get_translation(), array(), "Translation").'</p>
        <p class="field">'.HTMLHelper::genererInputText('context', $this->get_context(), array(), "Context").'</p>

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