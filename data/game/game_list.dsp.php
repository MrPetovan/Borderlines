<table>
  <tr>
    <th>Game</th>
    <th>Status</th>
    <th>Turn</th>
    <th>Players</th>
    <th>Min</th>
    <th>Max</th>
    <th>Creator</th>
    <th>Join</th>
  </tr>
<?php
  $is_in_a_game = $current_player->get_current_game() != false;

  foreach( $game_list as $game ) {
    $is_playing_in = $is_in_a_game || $game->get_game_player_list( $current_player->id );
    
    $player = Player::instance( $game->created_by );
    echo '
  <tr>
    <td><a href="'.Page::get_page_url('show_game', false, array('id' => $game->id)).'">'.$game->name.'</a></td>
    <td>'.$game->status_string.'</td>
    <td>'.$game->current_turn.'/'.$game->turn_limit.'</td>
    <td>'.count( $game->get_game_player_list() ).'</td>
    <td>'.$game->min_players.'</td>
    <td>'.$game->max_players.'</td>
    <td><a href="'.Page::get_page_url('show_player', false, array('id' => $player->id)).'">'.$player->name.'</a></td>
    <td>'.(!$game->started && !$is_playing_in?'<a href="'.Page::get_page_url(PAGE_CODE, false, array('action' => 'join', 'game_id' => $game->id)).'">Join</a>':'').'</td>
  </tr>';
  }
?>
</table>
<?php
  if( is_admin() ) {

    echo '
<form class="formulaire" action="'.Page::get_page_url( PAGE_CODE ).'" method="post">
  '.$game_mod->html_get_game_list_form().'
  <p>'.HTMLHelper::submit('game_submit', 'Add a game').'</p>
</form>';
  }