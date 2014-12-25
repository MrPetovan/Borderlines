<?php
  $member = Member::get_current_user();
  $current_player = Player::get_current( $member );

  $player_list = Player::db_get_all();

  $game_player_area_sum_list = array();
  foreach( $player_list as $player ) {
    $game_player_area_list[ 'player_' . $player->id ] = $player->get_game_player_area();
    $game_player_area_sum_list[ 'player_' . $player->id ] = array_sum($game_player_area_list[ 'player_' . $player->id ]);
    $game_player_count_list[ 'player_' . $player->id ] = count($game_player_area_list[ 'player_' . $player->id ]);
  }

  array_multisort($game_player_area_sum_list, SORT_DESC, $player_list);
?>