<?php
/**
 * Classe Criterion
 *
 */

class Criterion_Model extends DBObject {
  // Champs BD
  protected $_name = null;
  protected $_category_id = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "criterion"; }


  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_category_id($category_id) {
    if( is_numeric($category_id) && (int)$category_id == $category_id) $data = intval($category_id); else $data = null; $this->_category_id = $data;
  }

  /* FONCTIONS SQL */


  public static function db_get_by_category_id($category_id) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `category_id` = ".mysql_ureal_escape_string($category_id);

    return self::sql_to_list($sql);
  }

  public static function db_get_select_list( $with_null = false ) {
    $return = array();

    if( $with_null ) {
        $return[ null ] = 'N/A';
    }

    $object_list = Criterion_Model::db_get_all();
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
        <p class="field">'.HTMLHelper::genererInputText('name', $this->get_name(), array(), "Name *").'</p>';
      $option_list = array();
      $category_list = Category::db_get_all();
      foreach( $category_list as $category)
        $option_list[ $category->id ] = $category->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('category_id', $option_list, $this->get_category_id(), array(), "Category Id *").'<a href="'.get_page_url('admin_category_mod').'">Créer un objet Category</a></p>

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
      case 2 : $return = "Le champ <strong>Category Id</strong> est obligatoire."; break;
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
    $return[] = Member::check_compulsory($this->get_category_id(), 2, true);

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }

  public function get_territory_criterion_list($territory_id = null) {
    $where = '';
    if( ! is_null( $territory_id )) $where .= '
AND `territory_id` = '.mysql_ureal_escape_string($territory_id);

    $sql = '
SELECT `territory_id`, `criterion_id`, `percentage`
FROM `territory_criterion`
WHERE `criterion_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_territory_criterion( $territory_id, $percentage ) {
    $sql = "REPLACE INTO `territory_criterion` ( `territory_id`, `criterion_id`, `percentage` ) VALUES (".mysql_ureal_escape_string( $territory_id, $this->get_id(), $percentage ).")";

    return mysql_uquery($sql);
  }

  public function del_territory_criterion( $territory_id = null ) {
    $where = '';
    if( ! is_null( $territory_id )) $where .= '
AND `territory_id` = '.mysql_ureal_escape_string($territory_id);
    $sql = 'DELETE FROM `territory_criterion`
    WHERE `criterion_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }







  // CUSTOM

  //Custom content

  // /CUSTOM

}