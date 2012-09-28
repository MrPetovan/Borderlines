<table>
  <tr>
    <th><?php echo __('Game')?></th>
    <th><?php echo __('Status')?></th>
    <th><?php echo __('Turn')?></th>
    <th><?php echo __('Players')?></th>
    <th><?php echo __('Min')?></th>
    <th><?php echo __('Max')?></th>
    <th><?php echo __('Creator')?></th>
    <th><?php echo __('Action')?></th>
  </tr>
<?php
  foreach( $game_list as $game ) {
    $current_game = $current_player->current_game;

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
    <td>
      '.(!$game->started && !$current_game ?'<a href="'.Page::get_url(PAGE_CODE, array('action' => 'join', 'game_id' => $game->id)).'">'.__('Join').'</a>':'').'
      '.($current_game && $current_game->id == $game->id?'<a href="'.Page::get_url('game_over').'">'.__('Quit').'</a>':'').'
    </td>
  </tr>';
  }
?>
</table>
<?php
  if( is_admin() ) {

    echo '
<form class="formulaire" action="'.Page::get_page_url( PAGE_CODE ).'" method="post">
  '.$game_mod->html_get_game_list_form().'
  <p>'.HTMLHelper::submit('game_submit', __('Add a game') ).'</p>
</form>';
  }