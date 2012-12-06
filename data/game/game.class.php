<?php
/**
 * Class Game
 *
 */

require_once( DATA."model/game_model.class.php" );

class Game extends Game_Model {

  // CUSTOM
  protected $_version = 'world';

  public function get_territory_player_troops_list($turn = null, $territory_id = null, $player_id = null) {
    $return = array();
    if( is_null( $turn ) ) {
      for($turn = 0; $turn <= $this->current_turn; $turn ++ ) {
        $return = array_merge( $return, $this->get_territory_player_troops_list($turn, $territory_id, $player_id));
      }
    }else {
      $where = '';
      if( ! is_null( $territory_id )) $where .= '
AND `territory_id` = '.mysql_ureal_escape_string($territory_id);
      if( ! is_null( $player_id )) $where .= '
AND `player_id` = '.mysql_ureal_escape_string($player_id);

      $sql = '
SELECT
   `game_id`,
   '.$turn.' AS `turn`,
   `territory_id`,
   `player_id`,
   IFNULL( SUM( `delta` ), 0 ) AS `quantity`
FROM `territory_player_troops_history`
WHERE `game_id` = '.mysql_ureal_escape_string($this->get_id()).'
AND `turn` <= '.mysql_ureal_escape_string($turn).$where.'
GROUP BY
  `game_id`,
  `territory_id`,
  `player_id`';
      $res = mysql_uquery( $sql );

      foreach( mysql_fetch_to_array($res) as $row ) {
        if( $row['quantity'] != 0 ) {
          $return[] = $row;
        }
      }
    }
    return $return;
  }

  public function get_parameters() {
    $defaults = array(
        'HOME_TROOPS_MAINTENANCE' => 1,
        'AWAY_TROOPS_MAINTENANCE' => 2,
        'RECRUIT_TROOPS_PRICE' => 10,
        'TROOPS_EFFICACITY' => 10,
        'ALLOW_JOIN_MIDGAME' => 1,
    );

    $options = unserialize($this->_parameters);

    foreach( $defaults as $key => $value ) {
      if( !isset($options[$key] ) ) $options[ $key ] = $value;
    }
    return $options;
  }
  public function set_parameters($params) { $this->_parameters = serialize($params);}

  public function can_join( Player $player ) {
    $options = $this->get_parameters();
    $return = !$this->has_ended();

    if( $return ) {
      $return = $player->current_game === false;
    }
    if( $return && $this->max_players ) {
      $return = count( $this->get_game_player_list() ) < $this->max_players;
    }
    if( $return && $this->started ) {
      $return = $options['ALLOW_JOIN_MIDGAME'] && count( $this->get_territory_owner_list(null, $this->current_turn, false) ) >= 5;
    }

    return $return;
  }

  public function get_status_string() {
    $return = "Waiting for players";
    if( $this->has_ended() ) {
      $return = "Ended";
    }elseif( $this->started ) {
      $return = "Running";
    }

    return $return;
  }

  public function has_ended() {
    return ($this->current_turn >= $this->turn_limit);
  }

  public function get_territory_owner_list($territory_id = null, $turn = null, $owner_id = null) {
    $where = '';
    if( ! is_null( $territory_id )) $where .= '
AND `territory_id` = '.mysql_ureal_escape_string($territory_id);
    if( ! is_null( $turn )) $where .= '
AND `turn` = '.mysql_ureal_escape_string($turn);
    if( ! is_null( $owner_id )) {
      if( $owner_id === false ) {
        $where .= '
AND `owner_id` IS NULL';
      }else {
        $where .= '
AND `owner_id` = '.mysql_ureal_escape_string($owner_id);
      }
    }

    $sql = '
SELECT `territory_id`, `game_id`, `turn`, `owner_id`, `contested`, `capital`
FROM `territory_owner`
WHERE `game_id` = '.mysql_ureal_escape_string($this->get_id()).$where;
    $res = mysql_uquery($sql);

    return mysql_fetch_to_array($res);
  }

  public function reset() {
    $this->current_turn = 0;
    $this->started = null;
    $this->ended = null;
    $this->updated = null;

    Player_Order::db_truncate_by_game( $this->id );

    $world = new World();
    $world->name = $this->name;
    $world->save();
    $world->initialize_territories();

    $this->world_id = $world->id;

    $this->save();
  }

  public function start() {
    $this->started = time();
    $this->updated = time();

    $this->save();

    $player_list = Player::db_get_by_game( $this->id );

    $sql = 'DELETE FROM `territory_owner` WHERE `game_id` = '.mysql_ureal_escape_string($this->id);
    mysql_uquery($sql);
    $sql = 'DELETE FROM `territory_player_troops_history` WHERE `game_id` = '.mysql_ureal_escape_string($this->id);
    mysql_uquery($sql);

    $territories = Territory::db_get_by_world_id( $this->world_id );

    foreach( $player_list as $player ) {
      do {
        shuffle( $territories );
        $starting_territory = array_pop( $territories );
      }while( !$starting_territory->is_capturable() );

      $this->bootstrap_player( $player, $starting_territory->id );
    }
  }

  public function revert($turn) {
    $turn = max( 0, (int)$turn );

    $sql = 'UPDATE `game_player` SET `turn_leave` = NULL WHERE `game_id` = '.mysql_ureal_escape_string($this->id).' AND `turn_leave` >= '.mysql_ureal_escape_string($turn);
    mysql_uquery($sql);
    $sql = 'UPDATE `game_player` SET `turn_ready` = '.mysql_ureal_escape_string($turn).' WHERE `game_id` = '.mysql_ureal_escape_string($this->id).' AND `turn_ready` > '.mysql_ureal_escape_string($turn);
    mysql_uquery($sql);
    $sql = 'DELETE FROM `player_diplomacy` WHERE `game_id` = '.mysql_ureal_escape_string($this->id).' AND `turn` > '.mysql_ureal_escape_string($turn);
    mysql_uquery($sql);
    $sql = 'DELETE FROM `player_history` WHERE `game_id` = '.mysql_ureal_escape_string($this->id).' AND `turn` > '.mysql_ureal_escape_string($turn);
    mysql_uquery($sql);
    //$sql = 'DELETE FROM `player_order` WHERE `game_id` = '.mysql_ureal_escape_string($this->id).' AND `turn_ordered` > '.mysql_ureal_escape_string($turn);
    //mysql_uquery($sql);
    $sql = 'UPDATE `player_order` SET `datetime_execution` = NULL, `turn_executed` = NULL WHERE `game_id` = '.mysql_ureal_escape_string($this->id).' AND `turn_scheduled` >= '.mysql_ureal_escape_string($turn);
    mysql_uquery($sql);
    $sql = 'DELETE FROM `player_spygame_value` WHERE `game_id` = '.mysql_ureal_escape_string($this->id).' AND `turn` > '.mysql_ureal_escape_string($turn);
    mysql_uquery($sql);
    $sql = 'DELETE FROM `territory_owner` WHERE `game_id` = '.mysql_ureal_escape_string($this->id).' AND `turn` > '.mysql_ureal_escape_string($turn);
    mysql_uquery($sql);
    $sql = 'DELETE FROM `territory_player_troops_history` WHERE `game_id` = '.mysql_ureal_escape_string($this->id).' AND `turn` > '.mysql_ureal_escape_string($turn);
    mysql_uquery($sql);

    $this->current_turn = $turn;
    $this->ended = null;
    $this->save();
  }

  public function compute_auto() {
    $return = false;

    // Turn limit
    $flag_turn_limit = $this->current_turn < $this->turn_limit;

    // Turn interval
    $time_supposed = floor( ($this->updated + $this->turn_interval) / 60 );
    $time_actual = floor( time() / 60);
    $flag_turn_interval = $time_supposed <= $time_actual;

    // Checking if every player is ready
    $game_players = $this->get_game_player_list();
    $flag_players_ready = true;
    while( (list( $key, $game_player) = each( $game_players )) && $flag_players_ready  ) {
      $flag_players_ready = $game_player['turn_leave'] || $game_player['turn_ready'] > $this->current_turn;
    }

    if( $flag_turn_limit && ( $flag_turn_interval || $flag_players_ready ) ) {
      $return = $this->compute();
    }
    return $return;
  }

  public function compute() {
    $return = false;
    if( !$this->has_ended() ) {
      $current_turn = $this->current_turn;
      $next_turn = $this->current_turn + 1;

      $options = $this->get_parameters();

      $player_troops_list = $this->get_territory_player_troops_list($current_turn);
      foreach( $player_troops_list as $player_troops_row ) {
        $intermediate_troops_array[ $player_troops_row['territory_id'] ][ $player_troops_row['player_id'] ] = $player_troops_row['quantity'];
      }

      // Removing quitting players
      $order_list = $this->get_ready_orders( 'quit_game' );
      foreach( $order_list as $order ) {
        $order->execute();
      }

      $order_list = $this->get_ready_orders( 'give_troops' );
      foreach( $order_list as $order ) {
        $order->execute();
      }

      $order_list = $this->get_ready_orders( 'move_troops' );
      foreach( $order_list as $order ) {
        $order->execute( $intermediate_troops_array );
      }

      $territories = Territory::db_get_by_world_id( $this->world_id );
      // Updating territories ownership and battle on contested territories
      foreach( $territories as $territory ) {
        /* @var $territory Territory */
        $previous_owner = $territory->get_owner( $this, $current_turn );
        $new_owner = $territory->get_owner( $this, $next_turn );

        if( $territory->is_contested( $this, $next_turn ) ) {
          // Diplomacy checking and parties forming
          $diplomacy = array();
          $attacks = array();
          $losses = array();

          $player_troops = $this->get_territory_player_troops_list($next_turn, $territory->id);
          foreach( $player_troops as $key => $attacker_row ) {
            $this->set_player_history(
              $attacker_row['player_id'],
              $next_turn,
              time(),
              "There's a battle for the control of this territory",
              $territory->id
            );

            /* @var $player Player */
            $player = Player::instance($attacker_row['player_id']);
            foreach( $player->get_last_player_diplomacy_list($this->id) as $diplomacy_row ) {
              $diplomacy[ $diplomacy_row['from_player_id'] ][ $diplomacy_row['to_player_id'] ] = $diplomacy_row['status'] == 'Ally';
            }
            // Battle
            $attacker_efficiency = 1 / $options['TROOPS_EFFICACITY'];
            $attacker_damages = round($attacker_efficiency * $attacker_row['quantity']);

            // Building the attacks directions
            foreach( $player_troops as $defender_row ) {
              if( $attacker_row['player_id'] != $defender_row['player_id'] &&
                ! $diplomacy[ $attacker_row['player_id'] ][ $defender_row['player_id'] ] ) {
                $attacks[ $attacker_row['player_id'] ][] = $defender_row['player_id'];
              }
            }

            // Damages spread between the opposing forces
            foreach( $attacks[ $attacker_row['player_id'] ] as $defender_player_id ) {
              if( !isset( $losses[ $defender_player_id ][ $attacker_row['player_id'] ] ) ) {
                $losses[ $defender_player_id ][ $attacker_row['player_id'] ] = 0;
              }
              $losses[ $defender_player_id ][ $attacker_row['player_id'] ] =
                round($attacker_damages / count( $attacks[ $attacker_row['player_id'] ] ) );
            }
          }

          // Cleaning up
          foreach( $player_troops as $key => $player_row ) {

            $total_damages = array_sum( $losses[ $player_row['player_id'] ] );
            $total_losses = min( $player_row['quantity'], $total_damages );
            $ratio = 1;
            if( $total_damages > $total_losses ) {
              $ratio = $total_losses / $total_damages;
            }

            foreach( $losses[ $player_row['player_id'] ] as $attacker_player_id => $damages ) {
              $player = Player::instance($attacker_player_id);
              if( $diplomacy[ $player_row['player_id'] ][ $attacker_player_id ] ) {
                $this->set_player_history(
                  $player_row['player_id'],
                  $next_turn,
                  time(),
                  $player->name . "'s troops backstabbed yours !",
                  $territory->id
                );
              }
              $this->set_territory_player_troops_history($next_turn, $player_row['territory_id'], $player_row['player_id'], round( - $damages * $ratio ), 'Combat', $player->id);
            }

            if( $total_losses == $player_row['quantity'] ) {
              $this->set_player_history(
                $player_row['player_id'],
                $next_turn,
                time(),
                "All of your ".$player_row['quantity']." troops have been killed",
                $territory->id
              );
            }
          }

          // Recalculating ownership after battle
          $territory->compute_territory_owner( $this, $next_turn );
        }
      }

      $order_list = $this->get_ready_orders( 'give_territory' );
      foreach( $order_list as $order ) {
        $order->execute();
      }
      $player_list = Player::db_get_by_game($this->id, true);

      // Revenues and recruit
      foreach( $player_list as $player ) {
        $area = 0;
        $territory_previous_owner_list = $player->get_territory_owner_list(null, $this->id, $current_turn);
        foreach( $territory_previous_owner_list as $territory_owner_row ) {

          if( !$territory_owner_row['contested'] ) {
            $territory = Territory::instance($territory_owner_row['territory_id']);
            $area += $territory->get_area();
          }
        }

        $ratio = -$area * 0.00002 + 3;
        if( $ratio < 1 ) $ratio = 1;

        $revenue = round( $area * $ratio );

        $this->set_player_history(
          $player->id,
          $next_turn,
          time(),
          "You got a revenue of ".$revenue,
          null);
        $troops_maintenance = 0;
        $troops_list = $this->get_territory_player_troops_list($current_turn, null, $player->id);
        foreach( $troops_list as $territory_player_troops_row ) {
          $is_home = false;

          foreach( $territory_previous_owner_list as $territory_previous_owner_row ) {
            if( $territory_previous_owner_row['territory_id'] == $territory_player_troops_row['territory_id'] ) {
              $is_home = true;
              break;
            }
          }

          if( $is_home ) {
            $troops_maintenance += $territory_player_troops_row['quantity'] * $options['HOME_TROOPS_MAINTENANCE'];
          }else {
            $troops_maintenance += $territory_player_troops_row['quantity'] * $options['AWAY_TROOPS_MAINTENANCE'];
          }
        }

        // Desertion
        if( $troops_maintenance <= $revenue ) {
          $this->set_player_history(
            $player->id,
            $next_turn,
            time(),
            "You spent ".$troops_maintenance." to maintain your troops",
            null
          );
        }else {
          $this->set_player_history(
            $player->id,
            $next_turn,
            time(),
            "Desertion ! You need to spend ".$troops_maintenance." to maintain your troops but you don't have enough revenue",
            null
          );
          $ratio_desertion = 1 - $revenue / $troops_maintenance;
          $troops_list = $this->get_territory_player_troops_list($next_turn, null, $player->id);
          foreach( $troops_list as $troops_row ) {
            $territory = Territory::instance($troops_row['territory_id']);
            $deserters = floor( $troops_row['quantity'] * $ratio_desertion );
            if( $deserters > 0 ) {
              $player->set_territory_player_troops_history($this->id, $next_turn, $troops_row['territory_id'], - $deserters, 'Desertion');
              $this->set_player_history(
                $player->id,
                $next_turn,
                time(),
                $deserters." troops deserted",
                $troops_row['territory_id']
              );
              // Recalculating ownership after desertion
              $territory->compute_territory_owner( $this, $next_turn );
            }
          }

          $troops_maintenance = $revenue;

          $this->set_player_history(
            $player->id,
            $next_turn,
            time(),
            "You spent ".$troops_maintenance." to maintain your troops",
            null);
        }

        $recruit_budget = $revenue - $troops_maintenance;

        // Is there a capital (after move) ?
        $capital_id = null;
        $territory_current_owner_list = $player->get_territory_owner_list(null, $this->id, $current_turn + 1);
        foreach( $territory_current_owner_list as $territory_owner_row ) {
          if( $territory_owner_row['capital'] ) {
            $capital_id = $territory_owner_row['territory_id'];
            break;
          }
        }

        if( $capital_id !== null ) {
          $troops_recruited = floor( $recruit_budget / $options['RECRUIT_TROOPS_PRICE'] );
          $player->set_territory_player_troops_history($this->id, $next_turn, $capital_id, $troops_recruited, 'Recruitement');
          $this->set_player_history(
            $player->id,
            $next_turn,
            time(),
            "You recruited ".$troops_recruited." new troops at your capital",
            $capital_id
          );
        }else {
          $this->set_player_history(
            $player->id,
            $next_turn,
            time(),
            "You don't have any capital this turn, recruitement cancelled !",
            null
          );
        }
      }

      $order_list = $this->get_ready_orders( 'change_capital' );
      foreach( $order_list as $order ) {
        $order->execute();
      }

      $this->current_turn++;
      $this->updated = time();

      $return = $this->save();

      if( $return ) {
        if( $this->current_turn == $this->turn_limit ) {
          $this->ended = time();

          foreach( $player_list as $player ) {
            $member = Member::instance( $player->member_id );
            if( php_mail($member->email, SITE_NAME." | Game ended", $player->get_email_game_end( $this ), true) ) {
              Page::add_message( __("Message sent to %s", $player->name) );
            }else {
              Page::add_message( __("Message failed to %s", $player->name) , Page::PAGE_MESSAGE_WARNING);
              Page::add_message(var_export( error_get_last(), 1 ), Page::PAGE_MESSAGE_WARNING);
            }
          }

          $return = $this->save();
        }else {
          foreach( $player_list as $player ) {
            $member = Member::instance( $player->member_id );
            if( php_mail($member->email, SITE_NAME." | Turn ".$this->current_turn." computed", $player->get_email_game_new_turn( $this ), true)) {
              Page::add_message( __("Message sent to %s", $player->name) );
            }else {
              Page::add_message( __("Message failed to %s", $player->name) , Page::PAGE_MESSAGE_WARNING);
              Page::add_message(var_export( error_get_last(), 1 ), Page::PAGE_MESSAGE_WARNING);
            }
          }
        }
      }
    }
    return $return;
  }

  public function get_ready_orders( $class_name = null ) {
    $where = '';
    if( ! is_null( $class_name )) {
      $order_type = Order_Type::db_get_by_class_name( $class_name );
      $where .= '
AND `order_type_id` = '.mysql_ureal_escape_string($order_type->id);
    }
    $sql = "
SELECT `id`, `order_type_id`
FROM `".Player_Order::get_table_name()."`
WHERE `game_id` = ".mysql_ureal_escape_string( $this->id )."
AND `turn_scheduled` = ".mysql_ureal_escape_string( $this->current_turn )."
AND `turn_executed` IS NULL
".$where."
ORDER BY `order_type_id`";

    return Player_Order::sql_to_list( $sql );
  }

  public function db_get_ready_game_list() {
    $sql = "
SELECT *
FROM `".self::get_table_name()."`
WHERE `updated` IS NOT NULL
AND `current_turn` < `turn_limit`
AND `updated` + `turn_interval` < NOW()";

    return self::sql_to_list( $sql );
  }

  public function db_get_nonended_game_list() {
    $sql = "
SELECT *
FROM `".self::get_table_name()."`
WHERE `ended` IS NULL";

    return self::sql_to_list( $sql );
  }

  public function add_player( Player $player ) {
    $return = false;

    if( $this->can_join( $player ) ) {
      $this->set_game_player( $player->id, -1 );
      if( $this->started ) {
        $empty_territories = $this->get_territory_owner_list(null, $this->current_turn, false);

        do {
          shuffle( $empty_territories );
          $empty_territory_owner_row = array_pop( $empty_territories );
          $starting_territory = Territory::instance($empty_territory_owner_row['territory_id']);
        }while( !$starting_territory->is_capturable() );

        $this->bootstrap_player( $player, $starting_territory->id );
        $message = new Shout();
        $message->game_id = $this->id;
        $message->shouter_id = $player->id;
        $message->text = '[joined the game]';
        $message->date_sent = time();
        $message->save();
      }

      $return = true;
    }

    return $return;
  }

  public function bootstrap_player( Player $player, $territory_id ) {
    $player_list = Player::db_get_by_game( $this->id );
    foreach( $player_list as $playerB ) {
      if( $player != $playerB ) {
        $this->set_player_diplomacy( $this->current_turn, $player->id, $playerB->id, 'Enemy' );
        $this->set_player_diplomacy( $this->current_turn, $playerB->id, $player->id, 'Enemy' );
      }
    }

    $this->set_territory_player_troops_history($this->current_turn, $territory_id, $player->id, 1000, 'Init');
    $this->set_territory_owner($territory_id, $this->current_turn, $player->id, 0, 1);

    $member = Member::instance( $player->member_id );
    if( php_mail($member->email, SITE_NAME." | Game started", $player->get_email_game_new_turn( $this ), true)) {
      Page::add_message("Message sent to ".$player->name);
    }else {
      Page::add_message("Message failed to ".$player->name, Page::PAGE_MESSAGE_WARNING);
      Page::add_message(var_export( error_get_last(), 1 ), Page::PAGE_MESSAGE_WARNING);
    }
  }

  public function get_shortest_path( Territory $origin_territory, Territory $destination_territory, Player $player, $avoid_enemies = true ) {
    $territories = array();
    $links = array();

    // TODO : Avoid enemies
    if( $avoid_enemies ) {
      $diplomacy = array( $player->id => true );
      $diplomacy_list = $player->get_last_player_diplomacy_list( $this->id );
      foreach( $diplomacy_list as $diplomacy_row ) {
        $diplomacy[ $diplomacy_row['to_player_id'] ] = $diplomacy_row['status'] != 'Enemy';
      }

      $traversable = array();
      $ownership_list = $this->get_territory_owner_list(null, $this->current_turn);
      foreach( $ownership_list as $ownership_row ) {
        $traversable[ $ownership_row['territory_id'] ] = $ownership_row['owner_id'] === null || $diplomacy[ $ownership_row['owner_id'] ];
      }
    }

    $world = World::instance( $this->world_id );

    foreach( $world->territories as $territory ) {
      if( $territory->is_passable() && (!$avoid_enemies || $traversable[ $territory->id ]) ) {
        $territories[ $territory->id ] = $territory;
        foreach( $territory->get_territory_neighbour_list() as $territory_neighbour_row ) {
          $neighbour = Territory::instance( $territory_neighbour_row['neighbour_id'] );
          if( $neighbour->is_passable() && (!$avoid_enemies || $traversable[ $neighbour->id ]) ) {
            $distance = Vertex::distance( $territory->get_centroid(), $neighbour->get_centroid() );
            $links[ $territory->id ][ $neighbour->id ] = $distance + 10000;
            $links[ $neighbour->id ][ $territory->id ] = $distance + 10000;
          }
        }
      }
    }


    $shortest_path = $origin_territory->get_shortest_paths_to($territories, $links);

    $current_territory_id = $destination_territory->id;

    $path = array();
    $i = 0;
    do {
      array_push( $path, $current_territory_id );

      $current_territory_id = $shortest_path[ $current_territory_id ];
    }while( $current_territory_id != $origin_territory->id && $i++ < 1000 );
    $path = array_reverse($path);

    unset( $shortest_path );

    return $path;
  }

  public function html_get_game_list_form() {
    $turn_interval_list = array(
      600 => "Crazy short - 10 min",
      3600 => "Short - Hourly",
      86400 => "Medium - Daily",
      604800 => "Long - Weekly",
    );

    $options = $this->parameters;

    $return = '
    <fieldset>
      <legend>'.__('Create a game !').'</legend>
      '.HTMLHelper::genererInputHidden('id', $this->get_id()).'
      <p class="field">'.HTMLHelper::genererInputText('name', $this->get_name(), array(), __('Name').'*').'</p>
      <p class="field">'.HTMLHelper::genererSelect('turn_interval', $turn_interval_list, $this->get_turn_interval(), array(), __('Turn Interval').'*').'</p>
      <p class="field">'.HTMLHelper::genererSelect('world_id', World::db_get_select_list(), $this->world_id, array(), __('World').'*').'</p>
      <p class="field">'.HTMLHelper::genererInputText('turn_limit', $this->get_turn_limit(), array('title' => __('Game will stop after a fixed amount of turns')), __('Turn Limit').'*').'</p>
      <p class="field">'.HTMLHelper::genererInputText('min_players', $this->get_min_players(), array('title' => __('Number of players required to automatically launch the game')), __('Minimum nb of players')).'</p>
      <p class="field">'.HTMLHelper::genererInputText('max_players', $this->get_max_players(), array(), __('Maximum nb of players')).'</p>
    </fieldset>
    <fieldset>
      <legend>'.__('Game options').'</legend>
      <p class="field">'.HTMLHelper::genererInputText('parameters[HOME_TROOPS_MAINTENANCE]', $options['HOME_TROOPS_MAINTENANCE'], array(), __('Home troops cost')).'</p>
      <p class="field">'.HTMLHelper::genererInputText('parameters[AWAY_TROOPS_MAINTENANCE]', $options['AWAY_TROOPS_MAINTENANCE'], array(), __('Away troops cost')).'</p>
      <p class="field">'.HTMLHelper::genererInputText('parameters[RECRUIT_TROOPS_PRICE]', $options['RECRUIT_TROOPS_PRICE'], array(), __('Recruit troop price')).'</p>
      <p class="field">'.HTMLHelper::genererInputText('parameters[TROOPS_EFFICACITY]', $options['TROOPS_EFFICACITY'], array(), __('Troops efficacity (1 damage/x troops)')).'</p>
      <p class="field">'.HTMLHelper::genererInputCheckBox('parameters[ALLOW_JOIN_MIDGAME]', '0', $options['ALLOW_JOIN_MIDGAME'], array(), __('Allow players to join mid-game')).'</p>
    </fieldset>';

    return $return;
  }

  // /CUSTOM

}