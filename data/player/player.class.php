<?php
/**
 * Classe Player
 *
 */

require_once( DATA."model/player_model.class.php" );

class Player extends Player_Model {

  // CUSTOM

  public static function db_get_by_member_id( $member_id ) {
    $sql = "
SELECT *
FROM `".self::get_table_name()."`
WHERE `member_id` = ".mysql_ureal_escape_string( $member_id );

    return self::sql_to_list( $sql );
  }
  
  public function get_resource_sum_list( ) {
    $sql = '
SELECT `resource`.`id`, IFNULL( SUM( `delta` ), 0 ) as `sum`
FROM `resource`
LEFT JOIN `player_resource_history` ON
  `resource_id` = `resource`.`id`
  AND `player_id` = '.mysql_ureal_escape_string($this->get_id()).'
GROUP BY `resource`.`id`';
    $res = mysql_uquery( $sql );
    
    return mysql_fetch_to_array( $res );
  }
  
  public function get_resource_sum( $resource_id ) {
    $sql = '
SELECT IFNULL( SUM( `delta` ), 0 )
FROM `player_resource_history`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).'
AND `resource_id` = '.mysql_ureal_escape_string($resource_id);

    return array_shift( mysql_fetch_row( mysql_uquery( $sql ) ) );
  }
  
  public function get_resource_history() {
    $sql = '
SELECT `player_id`, `resource_id`, `datetime`, `delta`, `reason`
FROM `player_resource_history`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).'
ORDER BY `datetime` DESC';
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }
  
  public function get_last_spied_value( $value_guid ) {
    $return = null;

    $sql = '
SELECT `masked_value`, `datetime`
FROM `player_spygame_value`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).'
AND `value_guid` = '.mysql_ureal_escape_string( $value_guid ).'
AND `masked_value` IS NOT NULL
ORDER BY `datetime` DESC
LIMIT 0,1';

    $res = mysql_uquery($sql);

    return mysql_fetch_assoc( $res );
  }
  
  public function get_last_spy_date( $value_guid ) {
    $return = null;

    $sql = '
SELECT `datetime`
FROM `player_spygame_value`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).'
AND `value_guid` = '.mysql_ureal_escape_string( $value_guid ).'
ORDER BY `datetime` DESC
LIMIT 0,1';

    $res = mysql_uquery($sql); 
    
    if( $row = mysql_fetch_row( $res ) ) {
      $return = $row[0];
    }

    return $return;
  }
  
  public function get_spied_value( $value_guid, $target_player, $real_value, $timeout = null ) {
    if( !is_null( $timeout ) ) {
      // Check last spy date
      $last_date = $this->get_last_spy_date( $value_guid );
    
      // If too old, refresh
      if( guess_time( $last_date, GUESS_TIME_TIMESTAMP ) < time() - $timeout ) {
        $spy1 = $this->get_resource_sum( 3 );
        $spy2 = $target_player->get_resource_sum( 3 );
        $value = spygame( $spy1, $spy2, $real_value );
      
        $this->set_player_spygame_value(
          $value_guid,
          guess_time( time(), GUESS_TIME_MYSQL ),
          $real_value,
          $value
        );
      }
    }
    
    // Take the last available value
    return $this->get_last_spied_value( $value_guid );
    
    return $value;
  }

  // /CUSTOM

}