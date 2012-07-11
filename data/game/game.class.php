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
    
    $this->save();
    
    Player_Order::db_truncate_by_game( $this->id );
    $this->del_player_resource_history();
    //$this->del_game_player();
  }
  
  public function start() {
    $this->started = time();
    $this->updated = time();
    
    $this->save();
  
    $player_list = Player::db_get_by_game( $this->id );
    
    $resources = Resource::db_get_select_list();
    foreach( $player_list as $player ) {
      foreach( $resources as $resource_id => $resource_name ) {
        $this->set_player_resource_history( $player->id, $resource_id, $this->current_turn, guess_time( time(), GUESS_DATE_MYSQL), 1000, "Init ($resource_name)", null );
      }

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
    
      foreach( $player_list as $player ) {
        $territory_gain = $player->get_resource_sum( 4 ) * 0.1;
        $message = "Territory gain";
        $this->set_player_resource_history( $player->id, 5, $this->current_turn - 1, guess_time( time(), GUESS_DATE_MYSQL ), $territory_gain, $message, null );
      }
      
      $player_order_list = Player_Order::get_ready_orders( $this->id );
      
      foreach( $player_order_list as $order ) {
        $order_type = Order_Type::instance( $order->get_order_type_id() );
        $class = $order_type->get_class_name();
        require_once ('data/order_type/'.strtolower( $class ).'.class.php');
        $order_list[] = $class::instance( $order->get_id() );
      }
      
      //Pre-execution (soldiers go to war)
      foreach( $order_list as $order ) {
        $order->pre_execute();
      }
      // Execution (soldiers return)
      foreach( $order_list as $order ) {
        $order->execute();
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