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
    if( !isset( $_SESSION['current_player_id'] ) ) {
      $player_list = Player::db_get_by_member_id( $member->id );
      $_SESSION['current_player_id'] = null;
      if( count( $player_list ) ) {
         $player = array_shift( $player_list );
         $_SESSION['current_player_id'] = $player->id;
         $return = $player;
      }else {
          $return = Player::instance();
      }
    }else {
      $return = Player::instance( $_SESSION['current_player_id'] );
    }

    return $return;
  }

  public static function set_current( Player $player ) {
    $_SESSION['current_player_id'] = $player->id;

    return true;
  }

  public function can_create_game() {
    $return = is_admin();
    if( !$return && $this->id ) {
      $sql = '
SELECT COUNT(*)
FROM `game`
WHERE `created_by` = '.$this->id.'
AND `ended` IS NULL';
      $res = mysql_uquery($sql);
      $count = array_pop( mysql_fetch_row($res) );

      $return = $count == 0;
    }
    return $return;
  }

  public function can_create_world() {
    $return = is_admin();
    if( !$return && $this->id ) {
      $sql = '
SELECT COUNT(*)
FROM `world`
WHERE `created_by` = '.$this->id.'
AND `created` > DATE_SUB(NOW(), INTERVAL 1 HOUR)';
      $res = mysql_uquery($sql);
      $count = array_pop( mysql_fetch_row($res) );

      $return = $count == 0;
    }
    return $return;
  }

  public function can_create_player( Member $member ) {
    $return = is_admin();
    if( !$return ) {
      $sql = '
SELECT COUNT(*)
FROM `player`
WHERE `member_id` = '.$member->id;
      $res = mysql_uquery($sql);
      $count = array_pop( mysql_fetch_row($res) );

      $return = $count < 2;
    }
    return $return;
  }

  public function get_game_player_list($game_id = null) {
    $where = '';
    if( ! is_null( $game_id )) $where .= '
AND g_p.`game_id` = '.mysql_ureal_escape_string($game_id);

    $sql = '
SELECT g_p.`game_id`, g_p.`player_id`, g_p.`turn_ready`, g_p.`turn_leave`
FROM `game_player` g_p
JOIN `game` g ON g.`id` = g_p.`game_id`
WHERE g.`version` IN ("world", "economy")
AND g_p.`player_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    $return = mysql_fetch_to_array($res);

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
ORDER BY ISNULL(turn_leave) DESC, UNIX_TIMESTAMP( GREATEST( IFNULL(  `updated` , 0 ) , IFNULL(  `started` , 0 ) , IFNULL(  `created` , 0 ) ) ) DESC
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

  public function get_player_latest_diplomacy_list($game_id) {
    $sql = '
SELECT `game_id`, `turn`, `from_player_id`, `pd`.`to_player_id`, `status`, `shared_vision`
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

  public function get_to_player_latest_diplomacy_list($game_id) {
    $sql = '
SELECT `game_id`, `turn`, `pd`.`from_player_id`, `to_player_id`, `status`, `shared_vision`, IFNULL(`last_shared_vision`, 0) AS `last_shared_vision`
FROM `player_diplomacy` pd
JOIN (
  SELECT `from_player_id`, MAX( `turn` ) AS `max_turn`
  FROM `player_diplomacy`
  WHERE `to_player_id` = '.mysql_ureal_escape_string($this->get_id()).'
  AND `game_id` = '.mysql_ureal_escape_string($game_id).'
  AND `to_player_id` != `from_player_id`
  GROUP BY `from_player_id`
) AS `pd_max`
LEFT JOIN (
  SELECT `from_player_id`, MAX( `turn` ) AS `last_shared_vision`
  FROM `player_diplomacy`
  WHERE `to_player_id` = '.mysql_ureal_escape_string($this->get_id()).'
  AND `game_id` = '.mysql_ureal_escape_string($game_id).'
  AND `to_player_id` != `from_player_id`
  AND `shared_vision` = 1
  GROUP BY `from_player_id`
) AS `sv_max` ON `pd`.`from_player_id` = `sv_max`.`from_player_id`
WHERE `to_player_id` = '.mysql_ureal_escape_string($this->get_id()).'
AND `game_id` = '.mysql_ureal_escape_string($game_id).'
AND `pd`.`from_player_id` = `pd_max`.`from_player_id`
AND `turn` = `pd_max`.`max_turn`';
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function get_player_last_diplomacy_list( Game $game, $turn = null ) {
    if( is_null( $turn ) ) {
      $turn = $game->current_turn;
    }

    $sql = '
SELECT `game_id`, `turn`, `from_player_id`, `pd`.`to_player_id`, `status`
FROM `player_diplomacy` pd
JOIN (
  SELECT `to_player_id`, MAX( `turn` ) AS `max_turn`
  FROM `player_diplomacy`
  WHERE `from_player_id` = '.mysql_ureal_escape_string( $this->id ).'
  AND `game_id` = '.mysql_ureal_escape_string( $game->id ).'
  AND `from_player_id` != `to_player_id`
  AND `turn` <= '.$turn.'
  GROUP BY `to_player_id`
) AS `pd_max`
WHERE `from_player_id` = '.mysql_ureal_escape_string( $this->id ).'
AND `game_id` = '.mysql_ureal_escape_string( $game->id ).'
AND `pd`.`to_player_id` = `pd_max`.`to_player_id`
AND `turn` = `pd_max`.`max_turn`';
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function get_to_player_last_diplomacy_list( Game $game, $turn = null ) {
    if( is_null( $turn ) ) {
      $turn = $game->current_turn;
    }

    $sql = '
SELECT `game_id`, `turn`, `pd`.`from_player_id`, `to_player_id`, `status`, `shared_vision`, IFNULL(`last_shared_vision`, 0) AS `last_shared_vision`
FROM `player_diplomacy` pd
JOIN (
  SELECT `from_player_id`, MAX( `turn` ) AS `max_turn`
  FROM `player_diplomacy`
  WHERE `to_player_id` = '.mysql_ureal_escape_string($this->get_id()).'
  AND `game_id` = '.mysql_ureal_escape_string($game->id).'
  AND `to_player_id` != `from_player_id`
  AND `turn` <= ' . $turn . '
  GROUP BY `from_player_id`
) AS `pd_max`
LEFT JOIN (
  SELECT `from_player_id`, MAX( `turn` ) AS `last_shared_vision`
  FROM `player_diplomacy`
  WHERE `to_player_id` = '.mysql_ureal_escape_string($this->id).'
  AND `game_id` = '.mysql_ureal_escape_string($game->id).'
  AND `to_player_id` != `from_player_id`
  AND `shared_vision` = 1
  AND `turn` <= ' . $turn . '
  GROUP BY `from_player_id`
) AS `sv_max` ON `pd`.`from_player_id` = `sv_max`.`from_player_id`
WHERE `to_player_id` = '.mysql_ureal_escape_string($this->id).'
AND `game_id` = '.mysql_ureal_escape_string($game->id).'
AND `pd`.`from_player_id` = `pd_max`.`from_player_id`
AND `turn` = `pd_max`.`max_turn`';
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function get_last_player_diplomacy_player( Player $to_player, Game $game, $turn = null ) {
    if( is_null( $turn ) ) {
      $turn = $game->current_turn;
    }

    $sql = '
SELECT `status`
FROM `player_diplomacy`
WHERE `from_player_id` = '.mysql_ureal_escape_string( $this->id ).'
AND `game_id` = '.mysql_ureal_escape_string( $game->id ).'
AND `to_player_id` = '.mysql_ureal_escape_string( $to_player->id ).'
AND `turn` <= '.$turn.'
ORDER BY `turn` DESC
LIMIT 1';

    return mysql_fetch_one($sql);
  }

  public function get_player_name_with_diplomacy( Game $game = null, $turn = null, Player $to_player = null ) {
    if( is_null( $turn ) ) {
      $turn = $game->current_turn;
    }

    $return = $this->name;

    if( $to_player !== null && $game !== null && !$game->has_ended() ) {
      $status = $this->get_last_player_diplomacy_player( $to_player, $game, $turn );

      if( $status ) {
        $prepend = icon('diplomacy_'.strtolower($status));

        $return = $prepend . $this->name;
      }
    }

    return $return;
  }

  public function get_territory_economy_summary( Game $game, $turn = null ) {
    if( is_null( $turn ) ) {
      $turn = $game->current_turn;
    }

    $sql = '
SELECT ts.*, IFNULL( SUM( `delta` ), 0 ) AS `economy_ratio`
FROM `territory_status` ts
LEFT JOIN `territory_economy_history` teh ON teh.`territory_id` = ts.`territory_id` AND teh.`game_id` = ts.`game_id` AND teh.`turn` <= ts.`turn`
WHERE ts.`game_id` = '.mysql_ureal_escape_string($game->id).'
AND ts.`turn` = ' . mysql_ureal_escape_string($turn) . '
AND `owner_id` = '.mysql_ureal_escape_string($this->id).'
GROUP BY ts.`territory_id`';
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }
  public function get_territory_summary( Game $game, $turn = null ) {
    if( is_null( $turn ) ) {
      $turn = $game->current_turn;
    }

    $territory_player_troops_list = $game->get_territory_player_troops_list($turn, null, $this->id);
    $territory_status_list = $game->get_territory_status_list(null, $turn, $this->id);
    $troops = array();
    $return = array();
    foreach( $territory_player_troops_list as $territory_player_troops_row ) {
      $territory = Territory::instance($territory_player_troops_row['territory_id']);
      $territory_row = array_pop( $game->get_territory_status_list($territory_player_troops_row['territory_id'], $turn) );
      $territory_row['quantity'] = $territory_player_troops_row['quantity'];
      $territory_row['economy_ratio'] = $territory->get_economy_ratio($game, $turn);
      $troops[ $territory_row['territory_id'] ] = $territory_row['quantity'];
      $return[ $territory_row['territory_id'] ] = $territory_row;

    }

    foreach( $territory_status_list as $territory_status_row ) {
      if( !isset( $return[ $territory_status_row['territory_id'] ] ) ) {
        $territory = Territory::instance($territory_status_row['territory_id']);
        $territory_status_row['economy_ratio'] = $territory->get_economy_ratio($game, $turn);
        $territory_status_row['quantity'] = 0;
        $troops[ $territory_status_row['territory_id'] ] = $territory_status_row['quantity'];
        $return[ $territory_status_row['territory_id'] ] = $territory_status_row;
      }
    }

    array_multisort($troops, SORT_DESC, $return);

    return $return;
  }

  public function get_capital( Game $game, $turn = null ) {
    if( is_null( $turn ) ) {
      $turn = $game->current_turn;
    }

    $capital_id = null;
    $territory_current_owner_list = $this->get_territory_status_list(null, $game->id, $turn);
    foreach( $territory_current_owner_list as $territory_status_row ) {
      if( $territory_status_row['capital'] ) {
        $capital_id = $territory_status_row['territory_id'];
        break;
      }
    }
    return Territory::instance( $capital_id );
  }

  public function get_revenue( Game $game, $turn = null ) {
    if( is_null( $turn ) ) {
      $turn = $game->current_turn;
    }
    $game_parameters = $game->get_parameters();

    $revenue = 0;

    // Territory revenue
    $territory_previous_owner_list = $this->get_territory_status_list(null, $game->id, $turn - 1);
    foreach( $territory_previous_owner_list as $territory_status_row ) {
      $territory = Territory::instance($territory_status_row['territory_id']);

      $territory_revenue =
        $game_parameters['ECONOMY_BASE_REVENUE']
        * ( $territory->get_economy_ratio( $game, $turn - 1 ) )
        * ( 1 - $territory_status_row['revenue_suppression'] );

      $revenue += $territory_revenue;
    }

    $revenue *= $game->get_bureaucracy_ratio( count($territory_previous_owner_list) );

    $orders = Player_Order::db_get_executed( $game->id, $turn, 11 );
    //Giving tribute
    foreach($orders as $order) {
      if( $order->player_id == $this->id ) {
        $revenue -= $order->parameters['count'];
      }
    }

    //Receiving tribute
    foreach($orders as $order) {
      if( $order->parameters['to_player_id'] == $this->id ) {
        $revenue += $order->parameters['count'];
      }
    }

    return $revenue;
  }

  /**
   * Either returns the complete array of all territory gain (or losses) of a player
   * Or the sum of all thoses gains/losses
   *
   * Note : Current game area value is null
   *
   * @param bool $sum
   * @return array|float
   */
  public function get_game_player_area( $sum = false ) {
    $return = null;
    $game_player_list = $this->get_game_player_list();
    $game_player_area = array();
    foreach( $game_player_list as $game_player_row ) {
      $game = Game::instance( $game_player_row['game_id'] );
      $game_player_area[ $game->id ] = null;
      if($game->has_ended()) {
        $first_territory = array_pop( $this->get_territory_status_list(null, $game->id, 0) );
        $territory = Territory::instance( $first_territory['territory_id'] );

        $game_player_area[ $game->id ] = -1 * $territory->area;

        if( !$game_player_row['turn_leave'] ) {
          $territory_status_list = $this->get_territory_status_list(null, $game->id, $game->current_turn);
          foreach( $territory_status_list as $territory_status_row ) {
            if( $territory_status_row['owner_id'] ) {
              $territory = Territory::instance( $territory_status_row['territory_id'] );

              $game_player_area[ $game->id ] += $territory->area;
            }
          }
        }
      }
    }
    if( $sum ) {
      $return = array_sum( $game_player_area );
    }else {
      $return = $game_player_area;
    }
    return $return;
  }


  /**
   * Game new turn mail
   *
   * @see php_mail
   * @param Game $game Current game
   * @return string
   */
  public function get_email_game_new_turn( Game $game ) {
    $return = '
      <td width="698" style="vertical-align:top; padding-left:80px; padding-right:80px; font-size: 14px; color:#444444;">
        <p>'.__('Hi %s,',wash_utf8($this->name)).'</p>
        <p>'.__('Game "%s"\'s turn %s has been computed and is ready for you.', wash_utf8($game->name), $game->current_turn).'</p>
        <p><a href="'.Page::get_url('dashboard').'">'.__('Play now').'</a></p>
      </td>';

    return $return;
  }

  /**
   * Game ended mail
   *
   * @see php_mail
   * @param Game $game Current game
   * @return string
   */
  public function get_email_game_end( Game $game ) {
    $return = '
      <td width="698" style="vertical-align:top; padding-left:80px; padding-right:80px; font-size: 14px; color:#444444;">
        <p>'.__('Hi %s,',wash_utf8($this->name)).'</p>
        <p>'.__('Game "%s" has ended !', wash_utf8($game->name)).'</p>
        <p><a href="'.Page::get_url('game_show', array('id' => $game->id)).'">'.__('Check the leaderboard').'</a></p>
      </td>';

    return $return;
  }

  /**
   * Game canceled mail
   *
   * @see php_mail
   * @param string $game Current game
   * @return string
   */
  public function get_email_game_cancel( $game ) {
    $return = '
      <td width="698" style="vertical-align:top; padding-left:80px; padding-right:80px; font-size: 14px; color:#444444;">
        <p>'.__('Hi %s,',wash_utf8($this->name)).'</p>
        <p>'.__('Game "%s" has been canceled by its creator', wash_utf8($game->name)).'</p>
        <p>'.__('You can already join another game on the game list.').'</p>
        <p><a href="'.Page::get_url('game_list').'">'.__('Go to the game list').'</a></p>
      </td>';

    return $return;
  }

  public function get_email_new_conversation($conversation) {
    /* @var $conversation Conversation */
    $creator = Player::instance( $conversation->player_id );
    $return = '
      <td width="698" style="vertical-align:top; padding-left:80px; padding-right:80px; font-size: 14px; color:#444444;">
        <p>'.__('Hi %s,',wash_utf8($this->name)).'</p>
        <p>'.__('%s invited you to a conversation.', wash_utf8($creator->name)).'</p>
        <p><a href="'.Page::get_url('conversation_view', array('id' => $conversation->id)).'">'.__('See the messages').'</a></p>
      </td>';

    return $return;
  }

  // /CUSTOM

}