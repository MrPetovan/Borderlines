<?php
/**
 * Class Shout
 *
 */

require_once( DATA."model/shout_model.class.php" );

class Shout extends Shout_Model {

  // CUSTOM

  public static function db_get_by_game_id($game_id = null) {
    if( $game_id === null ) {
      $where = 'WHERE `game_id` IS NULL';
    }else {
      $where = 'WHERE `game_id` = '.mysql_ureal_escape_string($game_id);
    }
    $sql = '
SELECT `id` FROM `'.self::get_table_name().'`
'.$where;

    return self::sql_to_list($sql);
  }

  // /CUSTOM

}