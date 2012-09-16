<?php
/**
 * Class Game
 *
 */

require_once( DATA."model/game_model.class.php" );

class Game extends Game_Model {

  // CUSTOM

  const HOME_TROOPS_MAINTENANCE = 1;
  const AWAY_TROOPS_MAINTENANCE = 2;
  const RECRUIT_TROOPS_PRICE = 4;

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

  public function reset() {
    $this->current_turn = 0;
    $this->started = null;
    $this->ended = null;
    $this->updated = null;

    Player_Order::db_truncate_by_game( $this->id );
    $this->del_player_resource_history();
    //$this->del_game_player();

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

    $territories = Territory::db_get_by_world_id( $this->world_id );

    shuffle( $territories );

    foreach( $player_list as $player ) {
      foreach( $player_list as $playerB ) {
        if( $player != $playerB ) {
          $this->set_player_diplomacy( $this->current_turn, $player->id, $playerB->id, 'Enemy' );
        }
      }

      $starting_territory = array_pop( $territories );

      $this->set_territory_player_troops($this->current_turn, $starting_territory->id, $player->id, 1000);
      $this->set_territory_owner($starting_territory->id, $this->current_turn, $player->id, 0, 1);

      $member = Member::instance( $player->member_id );
      if( php_mail($member->email, SITE_NAME." | Game started", $player->get_email_game_new_turn( $this ), true)) {
        Page::add_message("Message sent to ".$player->name);
      }else {
        Page::add_message("Message failed to ".$player->name, Page::PAGE_MESSAGE_WARNING);
        Page::add_message(var_export( error_get_last(), 1 ), Page::PAGE_MESSAGE_WARNING);
      }
    }
  }

  public function revert($turn) {
    $turn = max( 0, (int)$turn );

    $sql = 'DELETE FROM `player_diplomacy` WHERE `game_id` = '.mysql_ureal_escape_string($this->id).' AND `turn` > '.mysql_ureal_escape_string($turn);
    mysql_uquery($sql);
    $sql = 'DELETE FROM `player_history` WHERE `game_id` = '.mysql_ureal_escape_string($this->id).' AND `turn` > '.mysql_ureal_escape_string($turn);
    mysql_uquery($sql);
    $sql = 'DELETE FROM `player_order` WHERE `game_id` = '.mysql_ureal_escape_string($this->id).' AND `turn_ordered` > '.mysql_ureal_escape_string($turn);
    mysql_uquery($sql);
    $sql = 'UPDATE `player_order` SET `datetime_execution` = NULL, `turn_executed` = NULL WHERE `game_id` = '.mysql_ureal_escape_string($this->id).' AND `turn_ordered` = '.mysql_ureal_escape_string($turn);
    mysql_uquery($sql);
    $sql = 'DELETE FROM `player_spygame_value` WHERE `game_id` = '.mysql_ureal_escape_string($this->id).' AND `turn` > '.mysql_ureal_escape_string($turn);
    mysql_uquery($sql);
    $sql = 'DELETE FROM `territory_owner` WHERE `game_id` = '.mysql_ureal_escape_string($this->id).' AND `turn` > '.mysql_ureal_escape_string($turn);
    mysql_uquery($sql);
    $sql = 'DELETE FROM `territory_player_troops` WHERE `game_id` = '.mysql_ureal_escape_string($this->id).' AND `turn` > '.mysql_ureal_escape_string($turn);
    mysql_uquery($sql);

    $this->current_turn = $turn;
    $this->save();
  }

  public function compute_auto() {
    $flag_turn_limit = $this->current_turn < $this->turn_limit;
    $flag_turn_interval = $this->updated + $this->turn_interval < time();

    // Checking if every player is ready
    $game_players = $this->get_game_player_list();
    $flag_players_ready = true;
    while( (list( $key, $game_player) = each( $game_players )) && $flag_players_ready  ) {
      $flag_players_ready = $game_player['turn_ready'] > $this->current_turn;
    }

    if( $flag_turn_limit && ( $flag_turn_interval || $flag_players_ready ) ) {
      $this->compute();
    }
  }

  public function compute() {
    $return = false;
    if( !$this->has_ended() ) {
      $current_turn = $this->current_turn;
      $next_turn = $this->current_turn + 1;

      // Duplicating troops record before moves
      $player_troops_list = $this->get_territory_player_troops_list($current_turn);
      foreach( $player_troops_list as $player_troops_row ) {
        $this->set_territory_player_troops(
                $next_turn,
                $player_troops_row['territory_id'],
                $player_troops_row['player_id'],
                $player_troops_row['quantity']
        );
      }

      $order_list = $this->get_ready_orders( 'move_troops' );
      foreach( $order_list as $order ) {
        $order->execute();
      }

      $territories = Territory::db_get_by_world_id( $this->world_id );
      // Updating territories ownership and battle on contested territories
      foreach( $territories as $territory ) {
        /* @var $territory Territory */
        $previous_owner = $territory->get_owner($this->id, $current_turn );
        $new_owner = $territory->get_owner($this->id, $next_turn );

        if( $territory->is_contested($this->id, $next_turn) ) {
          // Diplomacy checking and parties forming
          $diplomacy = array();
          $attacks = array();
          $losses = array();

          $player_troops = $territory->get_territory_player_troops_list($this->id, $next_turn);
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
            $attacker_efficiency = (mt_gaussrand() * 0.1 + 1) * 0.1;
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

            $new_quantity = max( 0, $player_row['quantity'] - array_sum( $losses[ $player_row['player_id'] ] ) );

            foreach( $losses[ $player_row['player_id'] ] as $attacker_player_id => $damages ) {
              $player = Player::instance($attacker_player_id);
              if( $diplomacy[ $player_row['player_id'] ][ $attacker_player_id ] ) {
                $verb = 'backstabbed';
              }else {
                $verb = 'killed';
              }
              $this->set_player_history(
                $player_row['player_id'],
                $next_turn,
                time(),
                $player->name . "'s troops ".$verb." ".$damages." of yours",
                $territory->id
              );
            }

            if( $new_quantity == 0 ) {
              $this->del_territory_player_troops($next_turn, $player_row['territory_id'], $player_row['player_id']);
              $this->set_player_history(
                $player_row['player_id'],
                $next_turn,
                time(),
                "All of your ".$player_row['quantity']." troops have been killed",
                $territory->id
              );
            }else {
              $this->set_territory_player_troops($next_turn, $player_row['territory_id'], $player_row['player_id'], $new_quantity);
              $this->set_player_history(
                $player_row['player_id'],
                $next_turn,
                time(),
                "You lost ".array_sum( $losses[ $player_row['player_id'] ] )." on ".$player_row['quantity']." troops in battle",
                $territory->id);
            }
          }

          // Recalculating ownership after battle
          $territory->compute_territory_owner($this->id, $next_turn );
        }
      }

      $order_list = $this->get_ready_orders( 'change_capital' );
      foreach( $order_list as $order ) {
        $order->execute();
      }

      $player_list = Player::db_get_by_game($this->id);

      // Revenues and recruit
      foreach( $player_list as $player ) {
        $capital_id = null;
        $area = 0;
        $territory_previous_owner_list = $player->get_territory_owner_list(null, $this->id, $current_turn);
        foreach( $territory_previous_owner_list as $territory_owner_row ) {

          if( !$territory_owner_row['contested'] ) {
            $territory = Territory::instance($territory_owner_row['territory_id']);
            $area += $territory->get_area();
          }
          if( $territory_owner_row['capital'] ) {
            $capital_id = $territory_owner_row['territory_id'];
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
        $troops_list = $player->get_territory_player_troops_list($this->id, $current_turn);
        foreach( $troops_list as $territory_player_troops_row ) {
          $is_home = true;

          foreach( $territory_previous_owner_list as $territory_previous_owner_row ) {
            if( $territory_previous_owner_row['territory_id'] == $territory_player_troops_row['territory_id']
                && $territory_previous_owner_row['territory_id'] != $player->id) {
              $is_home = false;
              break;
            }
          }

          if( $is_home ) {
            $troops_maintenance += $territory_player_troops_row['quantity'] * self::HOME_TROOPS_MAINTENANCE;
          }else {
            $troops_maintenance += $territory_player_troops_row['quantity'] * self::AWAY_TROOPS_MAINTENANCE;
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
          $troops_list = $player->get_territory_player_troops_list($this->id, $next_turn);
          foreach( $troops_list as $troops_row ) {
            $territory = Territory::instance($troops_row['territory_id']);
            $deserters = floor( $troops_row['quantity'] * $ratio_desertion );
            $player->set_territory_player_troops($this->id, $next_turn, $troops_row['territory_id'], $troops_row['quantity'] - $deserters);
            $this->set_player_history(
              $player->id,
              $next_turn,
              time(),
              $deserters." troops deserted",
              $troops_row['territory_id']
            );
          }

          $troops_maintenance = $revenue;
        }

        $this->set_player_history(
          $player->id,
          $next_turn,
          time(),
          "You spent ".$troops_maintenance." to maintain your troops",
          null);

        $recruit_budget = $revenue - $troops_maintenance;

        if( $capital_id !== null ) {
          $troops_recruited = floor( $recruit_budget / self::RECRUIT_TROOPS_PRICE );
          $capital_territory_troops = array_pop( $player->get_territory_player_troops_list($this->id, $next_turn, $capital_id) );
          $player->set_territory_player_troops($this->id, $next_turn, $capital_id, $capital_territory_troops['quantity'] + $troops_recruited);
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

      $this->current_turn++;
      $this->updated = time();

      if( $this->current_turn == $this->turn_limit ) {
        $this->ended = time();

        foreach( $player_list as $player ) {
          $member = Member::instance( $player->member_id );
          if( php_mail($member->email, SITE_NAME." | Game ended", $player->get_email_game_end( $this ), true) ) {
            Page::add_message("Message sent to ".$player->name);
          }else {
            Page::add_message("Message failed to ".$player->name, Page::PAGE_MESSAGE_WARNING);
            Page::add_message(var_export( error_get_last(), 1 ), Page::PAGE_MESSAGE_WARNING);
          }
        }
      }else {
        foreach( $player_list as $player ) {
          $member = Member::instance( $player->member_id );
          if( php_mail($member->email, SITE_NAME." | New turn", $player->get_email_game_new_turn( $this ), true)) {
            Page::add_message("Message sent to ".$player->name);
          }else {
            Page::add_message("Message failed to ".$player->name, Page::PAGE_MESSAGE_WARNING);
            Page::add_message(var_export( error_get_last(), 1 ), Page::PAGE_MESSAGE_WARNING);
          }
        }
      }

      $return = $this->save();
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
SELECT id
FROM `".Player_Order::get_table_name()."`
WHERE `game_id` = ".mysql_ureal_escape_string( $this->id )."
AND `turn_scheduled` = ".mysql_ureal_escape_string( $this->current_turn )."
AND `turn_executed` IS NULL
".$where."
ORDER BY `order_type_id`";

    $order_list = array();
    foreach( Player_Order::sql_to_list( $sql ) as $order ) {
      $order_type = Order_Type::instance( $order->order_type_id );
      $class = $order_type->class_name;
      require_once ('data/order_type/'.strtolower( $class ).'.class.php');
      $order_list[] = $class::instance( $order->id );
    }

    return $order_list;
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

  public function html_get_game_list_form() {
    $turn_interval_list = array(
      600 => "Crazy short - 10 min",
      3600 => "Short - Hourly",
      86400 => "Medium - Daily",
      604800 => "Long - Weekly",
    );

    $return = '
    <fieldset>
      <legend>Create a game !</legend>
      '.HTMLHelper::genererInputHidden('id', $this->get_id()).'
      <p class="field">'.HTMLHelper::genererInputText('name', $this->get_name(), array(), "Name*").'</p>
      <p class="field">'.HTMLHelper::genererSelect('turn_interval', $turn_interval_list, $this->get_turn_interval(), array(), "Turn Interval*").'</p>
      <p class="field">'.HTMLHelper::genererSelect('world_id', World::db_get_select_list(), $this->world_id, array(), "World*").'</p>
      <p class="field">'.HTMLHelper::genererInputText('turn_limit', $this->get_turn_limit(), array('title' => 'Game will stop after a fixed amount of turns'), "Turn Limit*").'</p>
      <p class="field">'.HTMLHelper::genererInputText('min_players', $this->get_min_players(), array('title' => 'Number of players required to automatically launch the game'), "Minimum nb of players").'</p>
      <p class="field">'.HTMLHelper::genererInputText('max_players', $this->get_max_players(), array(), "Maximum nb of players").'</p>
    </fieldset>';

    return $return;
  }

  public function add_player( $player ) {
    $return = false;

    if( !$this->started ) {
      if( !$player->get_current_game() ) {
        if( !$this->max_players || count( $this->get_game_player_list() ) < $this->max_players ) {
          $this->set_game_player( $player->id, -1 );

          $return = true;
        }else {
          Page::add_message('Game is already complete', Page::PAGE_MESSAGE_ERROR);
        }
      }else {
        Page::add_message('You are already in a game !', Page::PAGE_MESSAGE_ERROR);
      }
    }else {
      Page::add_message('Game already started', Page::PAGE_MESSAGE_ERROR);
    }

    return $return;
  }

  // /CUSTOM

}