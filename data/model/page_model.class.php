<?php/**
 * Classe Page_Model *
 */

class Page_Model extends DBObject {
  // Champs BD
  protected $_code = null;
  protected $_act = null;
  protected $_dsp = null;
  protected $_login_required = null;
  protected $_admin_required = null;
  protected $_tpl = null;
  protected $_rewrite_pattern = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "page"; }

  public function get_login_required() { return $this->is_login_required(); }
  public function is_login_required() { return ($this->_login_required == 1); }
  public function get_admin_required() { return $this->is_admin_required(); }
  public function is_admin_required() { return ($this->_admin_required == 1); }

  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_login_required($login_required) {
    if($login_required) $data = 1; else $data = 0; $this->_login_required = $data;
  }
  public function set_admin_required($admin_required) {
    if($admin_required) $data = 1; else $data = 0; $this->_admin_required = $data;
  }

  /* FONCTIONS SQL */

  public static function db_exists ($id) { return self::db_exists_class($id, get_class());}
  public static function db_get_by_id($id) { return self::db_get_by_id_class($id, get_class());}

  public static function db_get_all($page = null, $limit = NB_PER_PAGE) {
    $sql = 'SELECT `id` FROM `'.self::get_table_name().'` ORDER BY `id`';

    if(!is_null($page) && is_numeric($page)) {
      $start = ($page - 1) * $limit;
      $sql .= ' LIMIT '.$start.','.$limit;
    }

    return self::sql_to_list($sql, get_class());
  }

  public static function db_count_all() {
    $sql = "SELECT COUNT(`id`) FROM `".self::get_table_name().'`';
    $res = mysql_uquery($sql);
    return array_pop(mysql_fetch_row($res));
  }

  public static function db_get_page_by_code($code) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `code` LIKE ".mysql_ureal_escape_string($code)."
LIMIT 0,1";

    return self::sql_to_object($sql, get_class());
  }
  public static function db_get_select_list() {
    $return = array();

    $object_list = Page_Model::db_get_all();
    foreach( $object_list as $object ) $return[ $object->get_id() ] = $object->get_id();

    return $return;
  }

  /* FONCTIONS HTML */

  public static function manage_errors($tab_error, &$html_msg) { return self::manage_errors_class($tab_error, $html_msg, get_class());}

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
        <p class="field">'.HTMLHelper::genererInputText('code', $this->get_code(), array(), "Code *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('act', $this->get_act(), array(), "Act *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('dsp', $this->get_dsp(), array(), "Dsp *").'</p>
        <p class="field">'.HTMLHelper::genererInputCheckBox('login_required', '1', $this->get_login_required(), array('label_position' => 'right'), "Login Required" ).'</p>
        <p class="field">'.HTMLHelper::genererInputCheckBox('admin_required', '1', $this->get_admin_required(), array('label_position' => 'right'), "Admin Required" ).'</p>
        <p class="field">'.HTMLHelper::genererInputText('tpl', $this->get_tpl(), array(), "Tpl *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('rewrite_pattern', $this->get_rewrite_pattern(), array(), "Rewrite Pattern *").'</p>
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
      case 2 : $return = "Le champ <strong>Act</strong> est obligatoire."; break;
      case 3 : $return = "Le champ <strong>Dsp</strong> est obligatoire."; break;
      case 4 : $return = "Le champ <strong>Login Required</strong> est obligatoire."; break;
      case 5 : $return = "Le champ <strong>Admin Required</strong> est obligatoire."; break;
      case 6 : $return = "Le champ <strong>Tpl</strong> est obligatoire."; break;
      case 7 : $return = "Le champ <strong>Rewrite Pattern</strong> est obligatoire."; break;
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
    $return[] = Member::check_compulsory($this->get_act(), 2);
    $return[] = Member::check_compulsory($this->get_dsp(), 3);
    $return[] = Member::check_compulsory($this->get_login_required(), 4);
    $return[] = Member::check_compulsory($this->get_admin_required(), 5);
    $return[] = Member::check_compulsory($this->get_tpl(), 6);
    $return[] = Member::check_compulsory($this->get_rewrite_pattern(), 7);

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