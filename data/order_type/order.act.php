<?php
  $member = Member::instance( Member::get_current_user_id() );
  $current_player = Player::get_current( $member );

  if( $action = getValue('action') ) {
    if( $action == "cancel" ) {
      if( $player_order_id = getValue('id') ) {
        $player_order = Player_Order::instance( $player_order_id );

        if( $player_order && $current_player->id == $player_order->player_id ) {
          $player_order = Player_Order::factory($player_order->order_type_id, $player_order->id);

          if( $player_order->cancel() ) {
            Page::set_message( __('Order successfuly canceled') );
          }else {
            Page::set_message( __('Error while canceling order'), Page::PAGE_MESSAGE_ERROR );
          }
        }else {
          Page::set_message( __('Error while canceling order'), Page::PAGE_MESSAGE_ERROR );
        }
      }else {
        Page::set_message( __('Error while canceling order'), Page::PAGE_MESSAGE_ERROR );
      }
    }else {
      $order_type = Order_Type::db_get_by_class_name($action);

      $player_order = Player_Order::factory_by_class($action);

      if( $player_order->plan( $order_type, $current_player, getValue('parameters') ) ) {
        Page::set_message( __('Order successfully saved') );
      }else {
        Page::set_message( __('Error while saving order'), Page::PAGE_MESSAGE_ERROR );
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