<?php
  if( is_null( $game_id = getValue('id') ) ) {
    $game_list = Game::db_get_all();
  }else {
    $game = Game::instance( $game_id );
  
    $list = Player_Order::get_ready_orders( $game->id );
    
    $player_order_log = Player_Order::db_get_order_log( $game->id );

    if( $action = getValue('action') ) {
      switch( $action ) {
        case "reset" : {
          $game->reset();
          
          Page::set_message('reset game OK');
          break;
        }
        case "start" : {
          $game->start();
          
          Page::set_message('start game OK');
          break;
        }
        case "compute" : {
          if( $game->compute() ) {
            Page::set_message('compute OK');
          }else {
            Page::set_message('compute KO', Page::PAGE_MESSAGE_ERROR);
          }
          break;
        }
      }
      //Page::page_redirect( PAGE_CODE, array('id' => $game->id ) );
    }
  }
?>