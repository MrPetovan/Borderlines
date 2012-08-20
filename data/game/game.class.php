<?php
/**
 * Class Game
 *
 */

require_once( DATA."model/game_model.class.php" );

class Game extends Game_Model {

  // CUSTOM

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

      $member = Member::instance( $player->member_id );
      if( php_mail($member->email, SITE_NAME." | Game started", $player->get_email_game_new_turn( $this ), true)) {
        Page::add_message("Message sent to ".$player->name);
      }else {
        Page::add_message("Message failed to ".$player->name, Page::PAGE_MESSAGE_WARNING);
        Page::add_message(var_export( error_get_last(), 1 ), Page::PAGE_MESSAGE_WARNING);
      }
    }
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
      $this->current_turn++;

      $player_list = Player::db_get_by_game( $this->id );

      // Duplicating troops record before moves
      $player_troops_list = $this->get_territory_player_troops_list($this->current_turn - 1);
      foreach( $player_troops_list as $player_troops_row ) {
        $this->set_territory_player_troops(
                $this->current_turn,
                $player_troops_row['territory_id'],
                $player_troops_row['player_id'],
                $player_troops_row['quantity']
        );
      }

      $player_order_list = Player_Order::get_ready_orders( $this->id );

      $order_list = array();
      foreach( $player_order_list as $order ) {
        $order_type = Order_Type::instance( $order->get_order_type_id() );
        $class = $order_type->get_class_name();
        require_once ('data/order_type/'.strtolower( $class ).'.class.php');
        $order_list[] = $class::instance( $order->get_id() );
      }

      // Orders execution
      foreach( $order_list as $order ) {
        $order->execute();
      }

      $territories = Territory::db_get_by_world_id( $this->world_id );
      // Updating territories ownership and battle on contested territories
      foreach( $territories as $territory ) {
        /* @var $territory Territory */
        $previous_owner = $territory->get_current_owner($this->id, $this->current_turn - 1 );
        $new_owner = $territory->get_current_owner($this->id, $this->current_turn );

        if( $new_owner !== null ) {
          if( $new_owner === 0 ) {
            $player_troops = $territory->get_territory_player_troops_list($this->id, $this->current_turn);

            // Diplomacy checking and parties forming
            $diplomacy = array();
            $attacks = array();
            $losses = array();
            foreach( $player_troops as $key => $attacker_row ) {
              $this->set_player_history(
                $attacker_row['player_id'],
                $this->current_turn,
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
                  $this->current_turn,
                  time(),
                  $player->name . "'s troops ".$verb." ".$damages." of yours",
                  $territory->id
                );
              }

              if( $new_quantity == 0 ) {
                $this->del_territory_player_troops($this->current_turn, $player_row['territory_id'], $player_row['player_id']);
                $this->set_player_history(
                  $player_row['player_id'],
                  $this->current_turn,
                  time(),
                  "All of your ".$player_row['quantity']." troops have been killed",
                  $territory->id
                );
              }else {
                $this->set_territory_player_troops($this->current_turn, $player_row['territory_id'], $player_row['player_id'], $new_quantity);
                $this->set_player_history(
                  $player_row['player_id'],
                  $this->current_turn,
                  time(),
                  "You lost ".array_sum( $losses[ $player_row['player_id'] ] )." on ".$player_row['quantity']." troops in battle",
                  $territory->id);
              }
            }

            $territory->get_current_owner($this->id, $this->current_turn, true );
          }
        }
      }

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