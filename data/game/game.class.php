<?php
/**
 * Classe Game
 *
 */

require_once( DATA."model/game_model.class.php" );

class Game extends Game_Model {

  // CUSTOM
  
  public function init() {
    $this->current_turn = 1;
    $this->ended = null;
    $this->updated = null;
    $this->started = time();
    $this->save();
  
    $game_player_list = $this->get_game_player_list( );
    Player_Order::db_truncate_by_game( $this->id );
    $resources = Resource::db_get_select_list();
    foreach( $game_player_list as $game_player ) {
      $player = Player::instance( $game_player['player_id'] );
      $player->del_player_resource_history( $this->id );

      foreach( $resources as $resource_id => $resource_name ) {
        $this->set_player_resource_history( $player->id, $resource_id, $this->current_turn, guess_time( time(), GUESS_DATE_MYSQL), 1000, "Init ($resource_name)", null );
      }
    }
  }

  public function compute() {
    $game_player_list = $this->get_game_player_list( );
    foreach( $game_player_list as $game_player ) {
      $player = Player::instance( $game_player['player_id'] );
      $territory_gain = $player->get_resource_sum( 4 ) * 0.1;
      $message = "Territory gain";
      $this->set_player_resource_history( $player->id, 5, $this->current_turn, guess_time( mktime(), GUESS_DATE_MYSQL ), $territory_gain, $message, null );
    }
    
    $player_order_list = Player_Order::get_ready_orders( $this->id );
    
    foreach( $player_order_list as $order ) {
      $order_type = Order_Type::instance( $order->get_order_type_id() );
      $class = $order_type->get_class_name();
      require_once ('data/order_type/'.strtolower( $class ).'.class.php');
      $order = $class::instance( $order->get_id() );
      $order->execute();
    }
    $this->current_turn++;
    $this->updated = time();
    return $this->save();
  }

  // /CUSTOM

}