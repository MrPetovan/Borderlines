<?php
  $member = Member::get_current_user();
  $current_player = Player::get_current($member);

  /* @var $game Game */
  $game = Game::instance( getValue('id') );

  if( !$game->id ) {
    Page::add_message('Unknown game', Page::PAGE_MESSAGE_ERROR);
    Page::redirect('game_list');
  }

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
      case 'join' : {
        if( $game->add_player( $current_player ) ) {
          Page::add_message('You successfully joined the game !');
        }
        break;
      }
    }
    Page::redirect( PAGE_CODE, array('id' => $game->id ) );
  }

  $game_player_list = $game->get_game_player_list();
  foreach( $game_player_list as $game_player_row ) {
    $player_troops[ $game_player_row['player_id'] ] = 0;
    $player_area[ $game_player_row['player_id'] ] = 0;
  }
  $territory_player_troops_list = $game->get_territory_player_troops_list($game->current_turn);
  foreach( $territory_player_troops_list as $territory_player_troops_row ) {
    $player_troops[ $territory_player_troops_row['player_id'] ] += $territory_player_troops_row['quantity'];
  }
  $territory_owner_list = $game->get_territory_owner_list(null, $game->current_turn);
  foreach( $territory_owner_list as $territory_owner_row ) {
    $territory = Territory::instance( $territory_owner_row['territory_id'] );
    $player_area[ $territory_owner_row['owner_id'] ] += $territory->area;
  }