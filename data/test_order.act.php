<?php
  if( isset( $_GET['action'] ) ) {
    switch( $_GET['action'] ) {
      case "init_db" : {
        $players = Player::db_get_all();
        Player_Order::db_truncate();
        foreach( $players as $player ) {
          $player->del_player_resource_history();
          $player->set_player_resource_history( 1, guess_date( time(), GUESS_DATE_MYSQL), 10000, "Init" );
          $player->set_player_resource_history( 2, guess_date( time(), GUESS_DATE_MYSQL), 1000, "Init" );
          $player->set_player_resource_history( 3, guess_date( time(), GUESS_DATE_MYSQL), 1000, "Init" );

          $order = Player_Order::instance();
          $order->set_order_type_id( 2 );
          $order->set_player_id( $player->get_id() );
          $order->set_datetime_order( guess_date( time(), GUESS_DATE_MYSQL) );
          $order->set_datetime_scheduled( guess_date( time(), GUESS_DATE_MYSQL) );
          $order->set_parameters( serialize(array( 'count' => '1000' )) );
          $order->db_save();
        }
        
        $html_msg = '<p>Init DB Ok !</p>';
        break;
      }
      case "test" : {
        $list = Player_Order::get_ready_orders();
        
        foreach( $list as $order ) {
          $order_type = Order_Type::instance( $order->get_order_type_id() );
          $class = $order_type->get_class_name();
          require_once ('data/order_type/'.strtolower( $class ).'.class.php');
          $order = $class::instance( $order->get_id() );
          $order->execute();
        }
        var_debug( $order );
        break;
      }
    }
  }
?>