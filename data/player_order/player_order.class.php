<?php
/**
 * Class Player_Order
 *
 */

require_once( DATA."model/player_order_model.class.php" );

class Player_Order extends Player_Order_Model {

  // CUSTOM

  public function get_parameters()        { return unserialize($this->_parameters);}
  public function set_parameters($params) { $this->_parameters = serialize($params);}

  public static function db_truncate_by_game( $game_id ) {
    $sql = "DELETE FROM `".self::get_table_name()."`
WHERE `game_id` = ".mysql_ureal_escape_string($game_id);

    return mysql_uquery( $sql );
  }

  public static function db_get_planned_by_player_id( $player_id, $game_id ) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `player_id` = ".mysql_ureal_escape_string($player_id)."
AND `game_id` = ".mysql_ureal_escape_string($game_id)."
AND `turn_executed` IS NULL";
    return self::sql_to_list($sql);
  }

  public static function db_get_executed( $game_id, $turn, $order_type_id ) {
    $sql = '
SELECT `id` FROM `'.self::get_table_name().'`
WHERE `game_id` = '.mysql_ureal_escape_string($game_id).'
AND `turn_executed` = '.mysql_ureal_escape_string($turn).'
AND `order_type_id` = '.mysql_ureal_escape_string($order_type_id);
    return self::sql_to_list($sql);
  }

  public function plan( Order_Type $order_type, Player $player, $params = array(), $turn = null, $player_order_id = null ) {
    if( is_null( $turn ) ) {
      $turn = $player->current_game->current_turn;
    }
    $this->order_type_id = $order_type->id;
    $this->player_id = $player->id;
    $this->game_id = $player->current_game->id;
    $this->datetime_order = time();
    $this->turn_ordered = $player->current_game->current_turn;
    $this->turn_scheduled = $turn;
    $this->parameters = $params;
    $this->parent_player_order_id = $player_order_id;

    $return = $this->save();

    return $return;
  }

  public function pre_execute() {}
  public function execute() {}

  public function cancel( ) {
    $return = false;

    if( is_null( $this->turn_executed ) ) {
      $return = $this->db_delete();
    }

    return $return;
  }

  public static function get_html_form_by_class( $class, $order_params = array(), $page_params = array(), $page_code = PAGE_CODE ) {

    require_once(DATA.'order_type/'.$class.'.class.php');

    $options = array_merge(
      array(
        'page_code' => $page_code,
        'page_params' => $page_params
      ),
      $order_params
    );

    return $class::get_html_form( $options );
  }

  public static function get_html_form( $params ) {}

  public static function factory($order_type_id, $id = null) {
    $order_type = Order_Type::instance( $order_type_id );
    $class = $order_type->class_name;
    return self::factory_by_class($class, $id);
  }

  public static function factory_by_class($class, $id = null) {
    require_once(DATA.'order_type/'.$class.'.class.php');
    return $class::instance($id);
  }


  /**
     * Fonction retournant une liste d'objets en fonction d'une requête SQL
     *
     * La requête doit contenir un champ "id".
     *
     * @param $sql string Requête SQL à exécuter
     * @param $class string Classe des objets à créer
     * @return array Tableau des objets
     * @static
     */
    protected static function sql_to_list($sql) {
      $res = mysql_uquery($sql);

      if($res) {
        $return = array();
        while($data = mysql_fetch_assoc($res)) {
          if( isset( $data['order_type_id'] ) ) {
            $return[$data['id']] = self::factory( $data['order_type_id'], $data['id'] );
          }else {
            $return[$data['id']] = self::instance( $data['id'] );
          }
        }
        mysql_free_result($res);
      }else {
        $return = false;
      }

      return $return;
    }

  // /CUSTOM

}