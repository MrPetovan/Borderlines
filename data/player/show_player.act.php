<?php
  $member = Member::get_current_user();
  $current_player = Player::get_current($member);

  if( !is_null( $player_id = getValue('id' ) ) && is_numeric( $player_id ) ) {
    /* @var $player Player */
    $player = Player::instance( $player_id );
    if( !$player ) {
      Page::add_message( __('Unknown player'), Page::PAGE_MESSAGE_ERROR );
      Page::page_redirect( 'player_list' );
    }
  }else {
    Page::page_redirect( 'player_list' );
  }

  $game_player_list = $player->get_game_player_list();
  $game_player_area = array();
  foreach( $game_player_list as $game_player_row ) {
    $game = Game::instance( $game_player_row['game_id'] );
    if( $game_player_row['turn_leave'] ) {
      $turn = 0;
      $modifier = -1;
    }elseif($game->has_ended()) {
      $turn = $game->current_turn;
      $modifier = 1;
    }else {
      $turn = null;
      $modifier = 0;
    }
    if( $turn !== null ) {
      $game_player_area[ $game->id ] = 0;
      $territory_owner_list = $player->get_territory_owner_list(null, $game->id, $turn);
      foreach( $territory_owner_list as $territory_owner_row ) {
        if( $territory_owner_row['owner_id'] ) {
          $territory = Territory::instance( $territory_owner_row['territory_id'] );

          $game_player_area[ $game->id ] += $modifier * $territory->area;
        }
      }
    }else {
      $game_player_area[ $game->id ] = null;
    }
  }

?>