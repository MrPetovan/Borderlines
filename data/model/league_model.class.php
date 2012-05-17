<?php
/**
 * Classe League
 *
 */

class League_Model extends DBObject {
  // Champs BD
  protected $_lower_league_id = null;
  protected $_upper_league_id = null;
  protected $_name = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "league"; }


  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_lower_league_id($lower_league_id) {
    if( is_numeric($lower_league_id) && (int)$lower_league_id == $lower_league_id) $data = intval($lower_league_id); else $data = null; $this->_lower_league_id = $data;
  }
  public function set_upper_league_id($upper_league_id) {
    if( is_numeric($upper_league_id) && (int)$upper_league_id == $upper_league_id) $data = intval($upper_league_id); else $data = null; $this->_upper_league_id = $data;
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

    $object_list = League_Model::db_get_all();
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
      $option_list = array(null => 'Pas de choix');
      $league_list = League::db_get_all();
      foreach( $league_list as $league)
        $option_list[ $league->id ] = $league->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('lower_league_id', $option_list, $this->get_lower_league_id(), array(), "Lower League Id").'<a href="'.get_page_url('admin_league_mod').'">Créer un objet League</a></p>';
      $option_list = array(null => 'Pas de choix');
      $league_list = League::db_get_all();
      foreach( $league_list as $league)
        $option_list[ $league->id ] = $league->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('upper_league_id', $option_list, $this->get_upper_league_id(), array(), "Upper League Id").'<a href="'.get_page_url('admin_league_mod').'">Créer un objet League</a></p>
        <p class="field">'.HTMLHelper::genererInputText('name', $this->get_name(), array(), "Name *").'</p>
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

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }

  public function get_guild_season_league_list($season_id = null, $guild_id = null) {
    $where = '';
    if( ! is_null( $season_id )) $where .= '
AND `season_id` = '.mysql_ureal_escape_string($season_id);
    if( ! is_null( $guild_id )) $where .= '
AND `guild_id` = '.mysql_ureal_escape_string($guild_id);

    $sql = '
SELECT `season_id`, `guild_id`, `league_id`
FROM `guild_season_league`
WHERE `league_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function set_guild_season_league( $season_id, $guild_id ) {
    $sql = "REPLACE INTO `guild_season_league` ( `season_id`, `guild_id`, `league_id` ) VALUES (".mysql_ureal_escape_string( $season_id, $guild_id, $this->get_id() ).")";

    return mysql_uquery($sql);
  }

  public function del_guild_season_league( $season_id = null, $guild_id = null ) {
    $where = '';
    if( ! is_null( $season_id )) $where .= '
AND `season_id` = '.mysql_ureal_escape_string($season_id);
    if( ! is_null( $guild_id )) $where .= '
AND `guild_id` = '.mysql_ureal_escape_string($guild_id);
    $sql = 'DELETE FROM `guild_season_league`
    WHERE `league_id` = '.mysql_ureal_escape_string($this->get_id()).$where;

    return mysql_uquery($sql);
  }







  // CUSTOM

  //Custom content

  // /CUSTOM

}