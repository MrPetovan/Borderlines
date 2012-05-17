<?php
/**
 * Classe Vertex
 *
 */

class Vertex_Model extends DBObject {
  // Champs BD
  protected $_name = null;
  protected $_x = null;
  protected $_y = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "vertex"; }


  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }

  /* FONCTIONS SQL */


  public static function db_get_select_list() {
    $return = array();

    $object_list = Vertex_Model::db_get_all();
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
        <p class="field">'.HTMLHelper::genererInputText('name', $this->get_name(), array(), "Name *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('x', $this->get_x(), array(), "X *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('y', $this->get_y(), array(), "Y *").'</p>
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
      case 2 : $return = "Le champ <strong>X</strong> est obligatoire."; break;
      case 3 : $return = "Le champ <strong>Y</strong> est obligatoire."; break;
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
    $return[] = Member::check_compulsory($this->get_x(), 2);
    $return[] = Member::check_compulsory($this->get_y(), 3);

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }

  public function get_territory_vertex_list($territory_id = null) {
    $where = '';
    if( ! is_null( $territory_id )) $where .= '
AND `territory_id` = '.mysql_ureal_escape_string($territory_id);

    $sql = '
SELECT `territory_id`, `vertex_id`
FROM `territory_vertex`
WHERE `vertex_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_territory_vertex( $territory_id ) {
    $sql = "REPLACE INTO `territory_vertex` ( `territory_id`, `vertex_id` ) VALUES (".mysql_ureal_escape_string( $territory_id, $this->get_id() ).")";

    return mysql_uquery($sql);
  }

  public function del_territory_vertex( $territory_id = null ) {
    $where = '';
    if( ! is_null( $territory_id )) $where .= '
AND `territory_id` = '.mysql_ureal_escape_string($territory_id);
    $sql = 'DELETE FROM `territory_vertex`
    WHERE `vertex_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }







  // CUSTOM

  //Custom content

  // /CUSTOM

}