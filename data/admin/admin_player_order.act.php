<?php
  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['player_order_id']) && is_array($_POST['player_order_id'])) {
        foreach($_POST['player_order_id'] as $player_order_id) {

          $player_order = Player_Order::instance( $player_order_id );
          switch($_POST['action']) {
            case 'delete' :
              $player_order->db_delete();
              break;
          }
        }
      }
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM