<?php
/**
 * Classe Player_Order
 *
 */

class Player_Order_Model extends DBObject {
  // Champs BD
  protected $_order_type_id = null;
  protected $_player_id = null;
  protected $_datetime_order = null;
  protected $_datetime_scheduled = null;
  protected $_datetime_execution = null;
  protected $_parameters = null;

  public function __construct($id = null) {
    parent::__construct($id);
  }

  /* ACCESSEURS */
  public static function get_table_name() { return "player_order"; }


  /* MUTATEURS */
  public function set_id($id) {
    if( is_numeric($id) && (int)$id == $id) $data = intval($id); else $data = null; $this->_id = $data;
  }
  public function set_order_type_id($order_type_id) {
    if( is_numeric($order_type_id) && (int)$order_type_id == $order_type_id) $data = intval($order_type_id); else $data = null; $this->_order_type_id = $data;
  }
  public function set_player_id($player_id) {
    if( is_numeric($player_id) && (int)$player_id == $player_id) $data = intval($player_id); else $data = null; $this->_player_id = $data;
  }

  /* FONCTIONS SQL */


  public static function db_get_select_list() {
    $return = array();

    $object_list = Player_Order_Model::db_get_all();
    foreach( $object_list as $object ) $return[ $object->get_id() ] = $object->get_id();

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
        '.HTMLHelper::genererInputHidden('id', $this->get_id()).'';
      $option_list = array();
      $order_type_list = Order_Type::db_get_all();
      foreach( $order_type_list as $order_type)
        $option_list[ $order_type->id ] = $order_type->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('order_type_id', $option_list, $this->get_order_type_id(), array(), "Order Type Id *").'<a href="'.get_page_url('admin_order_type_mod').'">Créer un objet Order Type</a></p>';
      $option_list = array();
      $player_list = Player::db_get_all();
      foreach( $player_list as $player)
        $option_list[ $player->id ] = $player->name;

      $return .= '
      <p class="field">'.HTMLHelper::genererSelect('player_id', $option_list, $this->get_player_id(), array(), "Player Id *").'<a href="'.get_page_url('admin_player_mod').'">Créer un objet Player</a></p>
        <p class="field">'.HTMLHelper::genererInputText('datetime_order', $this->get_datetime_order(), array(), "Datetime Order *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('datetime_scheduled', $this->get_datetime_scheduled(), array(), "Datetime Scheduled *").'</p>
        <p class="field">'.HTMLHelper::genererInputText('datetime_execution', $this->get_datetime_execution(), array(), "Datetime Execution").'</p>
        <p class="field">'.HTMLHelper::genererInputText('parameters', $this->get_parameters(), array(), "Parameters *").'</p>
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
      case 1 : $return = "Le champ <strong>Order Type Id</strong> est obligatoire."; break;
      case 2 : $return = "Le champ <strong>Player Id</strong> est obligatoire."; break;
      case 3 : $return = "Le champ <strong>Datetime Order</strong> est obligatoire."; break;
      case 4 : $return = "Le champ <strong>Datetime Scheduled</strong> est obligatoire."; break;
      case 5 : $return = "Le champ <strong>Parameters</strong> est obligatoire."; break;
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

    $return[] = Member::check_compulsory($this->get_order_type_id(), 1);
    $return[] = Member::check_compulsory($this->get_player_id(), 2);
    $return[] = Member::check_compulsory($this->get_datetime_order(), 3);
    $return[] = Member::check_compulsory($this->get_datetime_scheduled(), 4);
    $return[] = Member::check_compulsory($this->get_parameters(), 5);

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