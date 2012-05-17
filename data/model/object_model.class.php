<?php
/**
 * Classe Object
 *
 */

class Object_Model extends DBObject {
  // Champs BD
  protected $_object_template_id = null;
  protected $_name = null;
  protected $_quality = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "object"; }


  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_object_template_id($object_template_id) {
    if( is_numeric($object_template_id) && (int)$object_template_id == $object_template_id) $data = intval($object_template_id); else $data = null; $this->_object_template_id = $data;
  }
  public function set_quality($quality) {
    if( is_numeric($quality) && (int)$quality == $quality) $data = intval($quality); else $data = null; $this->_quality = $data;
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

  public static function db_get_select_list() {
    $return = array();

    $object_list = Object_Model::db_get_all();
    foreach( $object_list as $object ) $return[ $object->get_id() ] = $object->get_name();

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
        '.HTMLHelper::genererInputHidden('id', $this->get_id()).'';
      $option_list = array();
      $object_template_list = Object_Template::db_get_all();
      foreach( $object_template_list as $object_template)
        $option_list[ $object_template->id ] = $object_template->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('object_template_id', $option_list, $this->get_object_template_id(), array(), "Object Template Id *").'<a href="'.get_page_url('admin_object_template_mod').'">Créer un objet Object Template</a></p>
        <p class="field">'.HTMLHelper::genererInputText('name', $this->get_name(), array(), "Name").'</p>
        <p class="field">'.HTMLHelper::genererInputText('quality', $this->get_quality(), array(), "Quality").'</p>
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
      case 1 : $return = "Le champ <strong>Object Template Id</strong> est obligatoire."; break;
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

    $return[] = Member::check_compulsory($this->get_object_template_id(), 1);

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }

  public function get_matchup_object_status_list($matchup_id = null, $tick = null) {
    $where = '';
    if( ! is_null( $matchup_id )) $where .= '
AND `matchup_id` = '.mysql_ureal_escape_string($matchup_id);
    if( ! is_null( $tick )) $where .= '
AND `tick` = '.mysql_ureal_escape_string($tick);

    $sql = '
SELECT `matchup_id`, `object_id`, `tick`, `durability`
FROM `matchup_object_status`
WHERE `object_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_matchup_object_status( $matchup_id, $tick, $durability ) {
    $sql = "REPLACE INTO `matchup_object_status` ( `matchup_id`, `object_id`, `tick`, `durability` ) VALUES (".mysql_ureal_escape_string( $matchup_id, $this->get_id(), $tick, $durability ).")";

    return mysql_uquery($sql);
  }

  public function del_matchup_object_status( $matchup_id = null, $tick = null ) {
    $where = '';
    if( ! is_null( $matchup_id )) $where .= '
AND `matchup_id` = '.mysql_ureal_escape_string($matchup_id);
    if( ! is_null( $tick )) $where .= '
AND `tick` = '.mysql_ureal_escape_string($tick);
    $sql = 'DELETE FROM `matchup_object_status`
    WHERE `object_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }



  public function get_object_state_list($guild_id = null, $season_id = null, $turn = null) {
    $where = '';
    if( ! is_null( $guild_id )) $where .= '
AND `guild_id` = '.mysql_ureal_escape_string($guild_id);
    if( ! is_null( $season_id )) $where .= '
AND `season_id` = '.mysql_ureal_escape_string($season_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);

    $sql = '
SELECT `guild_id`, `object_id`, `season_id`, `turn`, `durability`
FROM `object_state`
WHERE `object_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_object_state( $guild_id, $season_id, $turn, $durability ) {
    $sql = "REPLACE INTO `object_state` ( `guild_id`, `object_id`, `season_id`, `turn`, `durability` ) VALUES (".mysql_ureal_escape_string( $guild_id, $this->get_id(), $season_id, $turn, $durability ).")";

    return mysql_uquery($sql);
  }

  public function del_object_state( $guild_id = null, $season_id = null, $turn = null ) {
    $where = '';
    if( ! is_null( $guild_id )) $where .= '
AND `guild_id` = '.mysql_ureal_escape_string($guild_id);
    if( ! is_null( $season_id )) $where .= '
AND `season_id` = '.mysql_ureal_escape_string($season_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    $sql = 'DELETE FROM `object_state`
    WHERE `object_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }







  // CUSTOM

  //Custom content

  // /CUSTOM

}