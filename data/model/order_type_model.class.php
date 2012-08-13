<?php
/**
 * Classe Order_Type
 *
 */

class Order_Type_Model extends DBObject {
  // Champs BD
  protected $_class_name = null;
  protected $_name = null;
  protected $_active = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "order_type"; }

  public function get_active() { return $this->is_active(); }
  public function is_active() { return ($this->_active == 1); }

  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_active($active) {
    if($active) $data = 1; else $data = 0; $this->_active = $data;
  }

  /* FONCTIONS SQL */


  public static function db_get_by_class_name($class_name) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `class_name` = ".mysql_ureal_escape_string($class_name)."
LIMIT 0,1";

    return self::sql_to_object($sql);
  }
  public static function db_get_by_active($active) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `active` = ".mysql_ureal_escape_string($active);

    return self::sql_to_list($sql);
  }

  public static function db_get_select_list( $with_null = false ) {
    $return = array();
    
    if( $with_null ) {
        $return[ null ] = 'N/A';
    }

    $object_list = Order_Type_Model::db_get_all();
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
        <p class="field">'.HTMLHelper::genererInputText('class_name', $this->get_class_name(), array(), "Class Name *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('name', $this->get_name(), array(), "Name *").'</p>
        <p class="field">'.HTMLHelper::genererInputCheckBox('active', '1', $this->get_active(), array('label_position' => 'right'), "Active" ).'</p>

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
      case 1 : $return = "Le champ <strong>Class Name</strong> est obligatoire."; break;
      case 2 : $return = "Le champ <strong>Name</strong> est obligatoire."; break;
      case 3 : $return = "Le champ <strong>Active</strong> est obligatoire."; break;
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

    $return[] = Member::check_compulsory($this->get_class_name(), 1);
    $return[] = Member::check_compulsory($this->get_name(), 2);
    $return[] = Member::check_compulsory($this->get_active(), 3, true);

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