<?php
/**
 * Classe Player_Order
 *
 */

require_once( DATA."model/player_order_model.class.php" );

class Player_Order extends Player_Order_Model {

  // CUSTOM

  public static function db_truncate_by_game( $game_id ) {
    $sql = "DELETE FROM `".self::get_table_name()."`
WHERE `game_id` = ".mysql_ureal_escape_string($game_id);

    return mysql_uquery( $sql );
  }
  
  public static function get_ready_orders( $game_id ) {
    $sql = "
SELECT id
FROM `".self::get_table_name()."`
WHERE `game_id` = ".mysql_ureal_escape_string( $game_id )."
AND `datetime_execution` IS NULL
AND `datetime_scheduled` <= NOW()";

    return self::sql_to_list( $sql );
  }
  
  public static function db_get_planned_by_player_id( $player_id, $game_id ) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `player_id` = ".mysql_ureal_escape_string($player_id)."
AND `game_id` = ".mysql_ureal_escape_string($game_id)."
AND `datetime_execution` IS NULL";

    return self::sql_to_list($sql);
  }
  
  public static function db_get_order_log( $game_id ) {
    $sql = "
SELECT 
  `id`,
  `order_type_id`,
  `".self::get_table_name()."`.`player_id` AS `order_player_id`,
  `datetime_order`,
  `datetime_scheduled`,
  `datetime_execution`,
  `parameters`,
  `return`,
  `player_resource_history`.`player_id`,
  `resource_id`,
  `datetime`,
  `delta`,
  `reason`
FROM `".self::get_table_name()."`
LEFT JOIN `player_resource_history` ON `player_order_id` = `id`
WHERE `".self::get_table_name()."`.`game_id` = ".mysql_ureal_escape_string($game_id)."
ORDER BY `datetime_execution` DESC, `".self::get_table_name()."`.`player_id`";

    $res = mysql_uquery( $sql );
    
    return mysql_fetch_to_array( $res );
  }
  
  public function plan( Order_Type $order_type, Player $player, $params ) {
    $this->order_type_id = $order_type->id;
    $this->player_id = $player->id;
    $this->game_id = $player->current_game->id;
    $this->turn_ordered = $player->current_game->current_turn;
    $this->turn_scheduled = $player->current_game->current_turn;
    $this->datetime_order = time();
    $this->datetime_scheduled = time();
    $this->parameters = serialize( $params );
    
    $this->save();
  }

  public function execute() {}

  public function cancel( ) {
    $return = false;
  
    if( is_null( $this->datetime_execution ) || is_null( $this->turn_executed ) ) {
      $return = $this->db_delete();
    }
    
    return $return;
  }

  public static function get_html_form( $params ) {}

  // /CUSTOM

}