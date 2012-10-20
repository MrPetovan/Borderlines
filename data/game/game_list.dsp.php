<table>
  <tr>
    <th><?php echo __('Game')?></th>
    <th><?php echo __('World')?></th>
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
    $world = World::instance( $game->world_id );
    echo '
  <tr>
    <td><a href="'.Page::get_url('show_game', array('id' => $game->id)).'">'.$game->name.'</a></td>
    <td><a href="'.Page::get_url('show_world', array('game_id' => $game->id)).'">'.$world->name.'</a></td>
    <td>'.$game->status_string.'</td>
    <td>'.$game->current_turn.'/'.$game->turn_limit.'</td>
    <td>'.count( $game->get_game_player_list() ).'</td>
    <td>'.$game->min_players.'</td>
    <td>'.$game->max_players.'</td>
    <td><a href="'.Page::get_page_url('show_player', false, array('id' => $player->id)).'">'.$player->name.'</a></td>
    <td>
      '.(!$game->started && !$current_game ?'<a href="'.Page::get_url(PAGE_CODE, array('action' => 'join', 'game_id' => $game->id)).'">'.__('Join').'</a>':'').'
      '.($current_game && $current_game->id == $game->id?'<a href="'.Page::get_url('game_over').'">'.__('Quit').'</a>':'').'
      '.(($current_player->id == $game->created_by || is_admin()) && $game->started === null?'<a href="'.Page::get_url(PAGE_CODE, array('action' => 'cancel', 'game_id' => $game->id)).'">'.__('Cancel').'</a>':'').'
      '.(($current_player->id == $game->created_by || is_admin()) && $game->started === null?'<a href="'.Page::get_url(PAGE_CODE, array('action' => 'start', 'game_id' => $game->id)).'">'.__('Start').'</a>':'').'
    </td>
  </tr>';
  }
?>
</table>
<?php
  if( $current_player->can_create_game() ) {

    echo '
<form class="formulaire" action="'.Page::get_page_url( PAGE_CODE ).'" method="post">
  '.$game_mod->html_get_game_list_form().'
  <p>'.HTMLHelper::submit('game_submit', __('Add a game') ).'</p>
</form>';
  }
?>

<h4><?php echo __('Wall')?></h4>
<form action="<?php echo Page::get_url('shout')?>" method="post">
  <input type="hidden" name="url_return" value="<?php echo Page::get_url(PAGE_CODE)?>" />
  <p><?php echo '['.guess_time(time(), GUESS_TIME_LOCALE).']'?> <strong><?php echo wash_utf8($current_player->name)?></strong> : <input type="text" name="text" size="80" value=""/><button type="submit" name="action" value="shout">Say</button></p>
</form>
<div id="shoutwall">
<?php
    $shouts = Shout::db_get_by_game_id( null );
    foreach( array_reverse( $shouts ) as $shout ) {
      $player = Player::instance($shout->shouter_id);
      echo '
  <div class="shout">['.guess_time($shout->date_sent, GUESS_TIME_LOCALE).'] <strong>'.wash_utf8($player->name).'</strong>: '.wash_utf8($shout->text).'</div>';
    }
?>
</div>