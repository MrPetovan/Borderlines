<?php  
  $list = Player_Order::get_ready_orders();
  
  $player_order_log = Player_Order::db_get_order_log();

  if( $action = getValue('action') ) {
    switch( $action ) {
      case "init" : {
        $players = Player::db_get_all();
        Player_Order::db_truncate();
        $resources = Resource::db_get_select_list();
        foreach( $players as $player ) {
          $player->del_player_resource_history();
          
          
          foreach( $resources as $resource_id => $resource_name ) {
            $player->set_player_resource_history( $resource_id, guess_date( time(), GUESS_DATE_MYSQL), 1000, "Init ($resource_name)", null );
          }
        }
        
        Page::set_message('init game OK');
        
        Page::page_redirect( PAGE_CODE );
        break;
      }
      case "compute" : {
        $players = Player::db_get_all();
        foreach( $players as $player ) {
          $territory_gain = $player->get_resource_sum( 4 ) * 0.1;
          $message = "Territory gain";
          $player->set_player_resource_history( 5, guess_date( mktime(), GUESS_DATE_MYSQL ), $territory_gain, $message, null );
        }
        
        foreach( $list as $order ) {
          $order_type = Order_Type::instance( $order->get_order_type_id() );
          $class = $order_type->get_class_name();
          require_once ('data/order_type/'.strtolower( $class ).'.class.php');
          $order = $class::instance( $order->get_id() );
          $order->execute();
        }

        Page::set_message('compute OK');
        
        Page::page_redirect( PAGE_CODE );
        break;
      }
    }
  
    
  }
?>