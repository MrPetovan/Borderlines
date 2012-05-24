<?php
/**
 * Classe Player_Order
 *
 */

require_once( DATA."model/player_order_model.class.php" );

class Player_Order extends Player_Order_Model {

  // CUSTOM

  public static function get_current_player_order_list() {
    $sql = "
SELECT id
FROM player_order
WHERE datetime_execution IS NULL
AND datetime_scheduled <= NOW()";

    return self::sql_to_list( $sql );
  }

  // /CUSTOM

}