<?php
  $member = Member::get_current_user();
  $current_player = Player::get_current( $member );

  /* @var $current_game Game */

  if( $current_player ) {
    // Game retrival
    if( $current_game = $current_player->current_game ) {
      // In game OR game ended
      if( $current_game->has_ended() ) {
      }else {
        if( $quit = getValue('quit') ) {
          if( $quit == 'yes' ) {
            if( $current_game->started ) {
              $order_type = Order_Type::db_get_by_class_name('quit_game');
              $player_order = Player_Order::factory_by_class('quit_game');
              $player_order->plan( $order_type, $current_player );
              $player_order->save();

              $current_player->set_game_player( $current_game->id, $current_game->current_turn + 1 );

              Page::set_message(__('Your quit request has been successfully taken into account.'));
              Page::set_message(__('You will be able to join another game after next turn computing.'));
            }else {
              Page::set_message(__('You have successfully left the game.'));
              Page::set_message(__('You can immediately join a new game.'));
              $current_game->del_game_player($current_player->id);
              Page::redirect( 'game_list' );
            }
          }
          Page::redirect( 'dashboard' );
        }
      }
    }else {
      // No game ever played
      Page::redirect( 'game_list' );
    }
  }else {
    // No player created
    Page::redirect( 'create_player' );
  }
?>
