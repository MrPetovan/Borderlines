<?php
  $member = Member::instance( Member::get_current_user_id() );
  $player_list = Player::db_get_by_member_id( $member->get_id() );
  $current_player = array_shift( $player_list );

  if( $action = getValue('action') ) {
    if( $action == "cancel" ) {
      if( $player_order_id = getValue('id') ) {
        $player_order = Player_Order::instance( $player_order_id );

        if( $player_order && $current_player->id == $player_order->player_id ) {
          $player_order = Player_Order::factory($player_order->order_type_id, $player_order->id);

          if( $player_order->cancel() ) {
            Page::set_message('Order successfuly canceled');
          }else {
            Page::set_message( 'Error while canceling order', Page::PAGE_MESSAGE_ERROR );
          }
        }else {
          Page::set_message( 'Error while canceling order', Page::PAGE_MESSAGE_ERROR );
        }
      }else {
        Page::set_message( 'Error while canceling order', Page::PAGE_MESSAGE_ERROR );
      }
    }else {
      $order_type = Order_Type::db_get_by_class_name($action);

      $player_order = Player_Order::factory_by_class($action);

      $player_order->plan( $order_type, $current_player, getValue('parameters') );

      // TODO : Check parameters
      // $player_order->check();

      if( $player_order->save() ) {
        Page::set_message( 'Order successfully saved' );
      }else {
        Page::set_message( 'Error while saving order', Page::PAGE_MESSAGE_ERROR );
      }
    }

    if( is_null( $url_return = getValue( 'url_return' ) ) ) {
      $url_return = Page::get_page_url( 'dashboard', true );
    }

    redirect( $url_return );
  }else {
    Page::page_redirect('dashboard');
  }
?>