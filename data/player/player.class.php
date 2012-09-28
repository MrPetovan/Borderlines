<?php
/**
 * Class Player
 *
 */

require_once( DATA."model/player_model.class.php" );

class Player extends Player_Model {

  // CUSTOM
  protected $current_game = null;

  public static function db_get_by_member_id( $member_id ) {
    $sql = "
SELECT *
FROM `".self::get_table_name()."`
WHERE `member_id` = ".mysql_ureal_escape_string( $member_id );

    return self::sql_to_list( $sql );
  }

  public static function get_current( Member $member ) {
    $return = null;
    $player_list = Player::db_get_by_member_id( $member->get_id() );
    if( count( $player_list ) ) {
      $return = array_shift( $player_list );
    }

    return $return;
  }

  public function get_resource_sum_list( $game_id = null ) {
    $return = null;

    if( !is_null( $game_id ) || !is_null( $game_id = $this->get_current_game_id() ) ) {
      $sql = '
SELECT `resource`.`id`, IFNULL( SUM( `delta` ), 0 ) as `sum`
FROM `resource`
LEFT JOIN `player_resource_history` ON
  `resource_id` = `resource`.`id`
  AND `player_id` = '.mysql_ureal_escape_string($this->get_id()).'
  AND `game_id` = '.mysql_ureal_escape_string($game_id).'
GROUP BY `resource`.`id`';
      $res = mysql_uquery( $sql );

      $return = mysql_fetch_to_array( $res );
    }else {
      error_log('[Borderlines] '.__CLASS__.'->'.__FUNCTION__.' : $game_id not defined');
      throw new Exception('[Borderlines] '.__CLASS__.'->'.__FUNCTION__.' : $game_id not defined');
    }
    return $return;
  }

  public function get_resource_sum( $resource_id, $turn = null, $game_id = null ) {
    $return = null;

    if( !is_null( $game_id ) || !is_null( $game_id = $this->get_current_game_id() ) ) {
      $where = '';
      if( !is_null( $turn ) ) {
        $where = '
AND `turn` <= '.mysql_ureal_escape_string( $turn );
      }
      $sql = '
SELECT IFNULL( SUM( `delta` ), 0 )
FROM `player_resource_history`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).'
AND `game_id` = '.mysql_ureal_escape_string($game_id).'
AND `resource_id` = '.mysql_ureal_escape_string($resource_id).$where;
      $res = mysql_uquery( $sql );
      $row = mysql_fetch_row( $res );
      $return = array_shift( $row );
    }else {
      error_log('[Borderlines] '.__CLASS__.'->'.__FUNCTION__.' : $game_id not defined');
      throw new Exception('[Borderlines] '.__CLASS__.'->'.__FUNCTION__.' : $game_id not defined');
    }
    return $return;
  }

  public function get_resource_history( $game_id = null ) {
    $return = null;

    if( !is_null( $game_id ) || !is_null( $game_id = $this->get_current_game_id() ) ) {
      $sql = '
SELECT `player_id`, `resource_id`, `turn`, `datetime`, `delta`, `reason`, `player_order_id`
FROM `player_resource_history`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).'
AND `game_id` = '.mysql_ureal_escape_string($game_id).'
ORDER BY `turn` DESC, `datetime` DESC';
      $res = mysql_uquery($sql);

      $return = mysql_fetch_to_array($res);
    }else {
      error_log('[Borderlines] '.__CLASS__.'->'.__FUNCTION__.' : $game_id not defined');
      throw new Exception('[Borderlines] '.__CLASS__.'->'.__FUNCTION__.' : $game_id not defined');
    }
    return $return;
  }

  public function get_last_spied_value( $value_guid, $game_id = null ) {
    $return = null;

    if( !is_null( $game_id ) || !is_null( $game_id = $this->get_current_game_id() ) ) {
      $sql = '
SELECT `masked_value`, `turn`
FROM `player_spygame_value`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).'
AND `game_id` = '.mysql_ureal_escape_string($game_id).'
AND `value_guid` = '.mysql_ureal_escape_string( $value_guid ).'
AND `masked_value` IS NOT NULL
ORDER BY `turn` DESC
LIMIT 0,1';
      $res = mysql_uquery($sql);

      $return = mysql_fetch_assoc( $res );
    }else {
      error_log('[Borderlines] '.__CLASS__.'->'.__FUNCTION__.' : $game_id not defined');
      throw new Exception('[Borderlines] '.__CLASS__.'->'.__FUNCTION__.' : $game_id not defined');
    }

    return $return;
  }

  public function get_last_spy_date( $value_guid, $game_id = null ) {
    $return = null;

    if( !is_null( $game_id ) || !is_null( $game_id = $this->get_current_game_id() ) ) {
      $sql = '
SELECT `datetime`
FROM `player_spygame_value`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).'
AND `game_id` = '.mysql_ureal_escape_string($game_id).'
AND `value_guid` = '.mysql_ureal_escape_string( $value_guid ).'
ORDER BY `datetime` DESC
LIMIT 0,1';

      $res = mysql_uquery($sql);

      if( $row = mysql_fetch_row( $res ) ) {
        $return = $row[0];
      }
    }else {
      error_log('[Borderlines] '.__CLASS__.'->'.__FUNCTION__.' : $game_id not defined');
      throw new Exception('[Borderlines] '.__CLASS__.'->'.__FUNCTION__.' : $game_id not defined');
    }

    return $return;
  }

  public function get_last_spy_turn( $value_guid, $game_id = null ) {
    $return = null;

    if( !is_null( $game_id ) || !is_null( $game_id = $this->get_current_game_id() ) ) {
      $sql = '
SELECT `turn`
FROM `player_spygame_value`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).'
AND `game_id` = '.mysql_ureal_escape_string($game_id).'
AND `value_guid` = '.mysql_ureal_escape_string( $value_guid ).'
ORDER BY `turn` DESC
LIMIT 0,1';

      $res = mysql_uquery($sql);

      if( $row = mysql_fetch_row( $res ) ) {
        $return = $row[0];
      }
    }else {
      error_log('[Borderlines] '.__CLASS__.'->'.__FUNCTION__.' : $game_id not defined');
      throw new Exception('[Borderlines] '.__CLASS__.'->'.__FUNCTION__.' : $game_id not defined');
    }

    return $return;
  }

  public function get_spied_value( $value_guid, $target_player, $real_value ) {
    $return = null;

    if( !is_null( $game = $this->get_current_game() ) ) {
      // Check last spy date
      $last_turn = $this->get_last_spy_turn( $value_guid );

      // If too old, refresh
      if( $game->current_turn > $last_turn ) {
        $spy1 = $this->get_resource_sum( 3 );
        $spy2 = $target_player->get_resource_sum( 3 );
        $value = spygame( $spy1, $spy2, $real_value );

        $success = $this->set_player_spygame_value(
          $game->id,
          $value_guid,
          $game->current_turn,
          time(),
          $real_value,
          $value
        );
      }
      // Take the last available value
      $return = $this->get_last_spied_value( $value_guid );
    }else {
      error_log('[Borderlines] '.__CLASS__.'->'.__FUNCTION__.' : $game_id not defined');
      throw new Exception('[Borderlines] '.__CLASS__.'->'.__FUNCTION__.' : $game_id not defined');
    }

    return $return;
  }

  public function get_last_game() {
    $sql = '
SELECT `id`
FROM `'.Game::get_table_name().'`
JOIN `game_player` ON `game_id` = `id`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).'
ORDER BY UNIX_TIMESTAMP( GREATEST( IFNULL(  `updated` , 0 ) , IFNULL(  `started` , 0 ) , IFNULL(  `created` , 0 ) ) ) DESC
LIMIT 0,1';

    return Game::sql_to_object( $sql );
  }

  public function get_current_game() {
    if( is_null( $this->current_game ) ) {
      $sql = '
SELECT `id`
FROM `'.Game::get_table_name().'`
JOIN `game_player` ON `game_id` = `id`
WHERE `player_id` = '.mysql_ureal_escape_string($this->get_id()).'
AND `turn_leave` IS NULL
AND `ended` IS NULL
LIMIT 0,1';

      $this->current_game = Game::sql_to_object( $sql );
    }

    return $this->current_game;
  }

  public function get_current_game_id() {
    $return = null;

    $current_game = $this->get_current_game();

    if( $current_game ) {
      $return = $current_game->id;
    }

    return $return;
  }

  public static function db_get_leaderboard_list( $game_id ) {
    $return = null;

$sql = "
SELECT `".self::get_table_name()."`.`id`
FROM `".self::get_table_name()."`
JOIN `player_resource_history` ON `".self::get_table_name()."`.`id` = `player_resource_history`.`player_id`
JOIN `game_player` ON `game_player`.`player_id` = `".self::get_table_name()."`.`id` AND `game_player`.`game_id` = `player_resource_history`.`game_id`
WHERE `player_resource_history`.`game_id` = ".mysql_ureal_escape_string( $game_id )."
AND `player_resource_history`.`resource_id` = 4
GROUP BY `".self::get_table_name()."`.`id`
ORDER BY SUM( `delta` ) DESC";
    $return = self::sql_to_list( $sql );

    return $return;
  }

  public static function db_get_by_game( $game_id, $active = null ) {
    $where = '';
    if( $active === true ) {
      $where = '
AND `turn_leave` IS NULL';
    }elseif( $active === false ) {
      $where = '
AND `turn_leave` IS NOT NULL';
    }elseif( is_numeric( $active ) ) {
      $where = '
AND `turn_leave` = '.  mysql_ureal_escape_string($active);
    }

    $sql = '
SELECT `player_id` as `id`
FROM `game_player`
WHERE `game_id` = '.mysql_ureal_escape_string($game_id).$where;

    $return = self::sql_to_list( $sql );

    return $return;
  }

  public function get_last_player_diplomacy_list($game_id) {
    $sql = '
SELECT `game_id`, `turn`, `from_player_id`, `pd`.`to_player_id`, `status`
FROM `player_diplomacy` pd
JOIN (
  SELECT `to_player_id`, MAX( `turn` ) AS `max_turn`
  FROM `player_diplomacy`
  WHERE `from_player_id` = '.mysql_ureal_escape_string($this->get_id()).'
  AND `game_id` = '.mysql_ureal_escape_string($game_id).'
  AND `from_player_id` != `to_player_id`
  GROUP BY `to_player_id`
) AS `pd_max`
WHERE `from_player_id` = '.mysql_ureal_escape_string($this->get_id()).'
AND `game_id` = '.mysql_ureal_escape_string($game_id).'
AND `pd`.`to_player_id` = `pd_max`.`to_player_id`
AND `turn` = `pd_max`.`max_turn`';
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function get_territory_summary($game_id, $turn) {
    $sql = '
SELECT t_p_t.`territory_id`, `quantity`, `owner_id` , `contested`, `capital`
FROM `territory_player_troops` t_p_t
LEFT JOIN `territory_owner` t_o ON
  t_o.`game_id` = t_p_t.`game_id`
  AND t_o.`turn` = t_p_t.`turn`
  AND t_o.`territory_id` = t_p_t.`territory_id`
WHERE t_p_t.`game_id` = '.mysql_ureal_escape_string($game_id).'
AND t_p_t.`turn` = '.mysql_ureal_escape_string($turn).'
AND t_p_t.`player_id` = '.mysql_ureal_escape_string($this->get_id()).'
UNION
SELECT t_o.`territory_id`, 0, `owner_id`, `contested`, `capital`
FROM `territory_owner` t_o
WHERE NOT EXISTS (
  SELECT "x"
  FROM `territory_player_troops` t_p_t
  WHERE t_p_t.`territory_id` = t_o.`territory_id`
  AND t_p_t.`game_id` = t_o.`game_id`
  AND t_p_t.`turn` = t_o.`turn`
  AND t_p_t.`player_id` = t_o.`owner_id`
)
AND t_o.`game_id` = '.mysql_ureal_escape_string($game_id).'
AND t_o.`turn` = '.mysql_ureal_escape_string($turn).'
AND t_o.`owner_id` = '.mysql_ureal_escape_string($this->get_id()).'
ORDER BY `territory_id`';
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  /**
   * Game new turn mail
   *
   * @see php_mail
   * @param string $game Current game
   * @return string
   */
  public function get_email_game_new_turn( $game ) {
    $return = '
      <td width="698" style="vertical-align:top; padding-left:80px; padding-right:80px; font-size: 14px; color:#444444;">
        <p>Hi '.wash_utf8($this->name).',</p>
        <p>Game "'.wash_utf8($game->name).'"\'s turn '.$game->current_turn.' has been computed and is ready for you.</p>
        <p><a href="'.Page::get_url('dashboard').'">Play now</a></p>
      </td>';

    return $return;
  }

  /**
   * Game ended mail
   *
   * @see php_mail
   * @param string $game Current game
   * @return string
   */
  public function get_email_game_end( $game ) {
    $return = '
      <td width="698" style="vertical-align:top; padding-left:80px; padding-right:80px; font-size: 14px; color:#444444;">
        <p>Hi '.wash_utf8($this->name).',</p>
        <p>Game "'.wash_utf8($game->name).'" has ended !</p>
        <p><a href="'.Page::get_url('dashboard').'">Check the leaderboard</a></p>
      </td>';

    return $return;
  }

  public function get_email_new_conversation($conversation) {
    /* @var $conversation Conversation */
    $creator = Player::instance( $conversation->player_id );
    $return = '
      <td width="698" style="vertical-align:top; padding-left:80px; padding-right:80px; font-size: 14px; color:#444444;">
        <p>Hi '.wash_utf8($this->name).',</p>
        <p>'.wash_utf8($creator->name).' invited you to a conversation. To see the messages, follow this link :</p>
        <p>'.Page::get_url('conversation_view', array('id' => $conversation->id)).'</p>
        <p><span style="font-weight:bold;">Pour accéder à votre compte,</span> <a style="color:#F22A83;" href="'.get_page_url('mon-compte').'">cliquez sur ce lien</a>.</p>
      </td>';

    return $return;
  }

  // /CUSTOM

}