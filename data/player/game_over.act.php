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
              $game_player = array_pop( $current_game->get_game_player_list( $current_player->id ) );
              $current_game->set_game_player($current_player->id, $game_player['turn_ready'], $current_game->current_turn);
            }else {
              $current_game->del_game_player($current_player->id);
            }
            Page::redirect( 'game_list' );
          }else {
            Page::redirect( 'dashboard' );
          }
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
