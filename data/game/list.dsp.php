<h2><?php echo __('Games')?>
  <?php if( $current_player->can_create_game() ):?>
  <a href="<?php echo Page::get_page_url('game_create')?>" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-plus"></span> <?php echo __('Create a new game')?></a>
  <?php endif;?>
</h2>
<table class="table table-hover table-striped">
  <thead>
    <tr>
      <td colspan="9" class="text-right">

      </td>
    </tr>
    <tr>
      <th><?php echo __('Game')?></th>
      <th><?php echo __('World')?></th>
      <th><?php echo __('Status')?></th>
      <th class="num"><?php echo __('Turn')?></th>
      <th class="num"><?php echo __('Players')?></th>
      <th class="num"><?php echo __('Min')?></th>
      <th class="num"><?php echo __('Max')?></th>
      <th><?php echo __('Creator')?></th>
      <th><?php echo __('Action')?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="9"><?php echo __('%s games', count($game_list))?></td>
    </tr>
  </tfoot>
  <tbody>
<?php
  foreach( $game_list as $game ) {
    $current_game = $current_player->current_game;

    $player = Player::instance( $game->created_by );
    $world = World::instance( $game->world_id );

    $status_icon = '<span class="glyphicon glyphicon-hourglass"></span>';
    if( $game->has_ended() ) {
      $status_icon = '<span class="glyphicon glyphicon-stop"></span>';
    }elseif( $game->started ) {
      $status_icon = '<span class="glyphicon glyphicon-play"></span>';
    }
    echo '
  <tr>
    <td><a href="'.Page::get_url('show_game', array('id' => $game->id)).'">'.$game->name.'</a></td>
    <td><a href="'.Page::get_url('show_world', array('game_id' => $game->id)).'">'.$world->name.'</a></td>
    <td>' . $status_icon . ' ' . __($game->status_string) . '</td>
    <td class="num">'.$game->current_turn.'/'.$game->turn_limit.'</td>
    <td class="num">'.count( $game->get_game_player_list() ).'</td>
    <td class="num">'.$game->min_players.'</td>
    <td class="num">'.$game->max_players.'</td>
    <td><a href="'.Page::get_page_url('show_player', false, array('id' => $player->id)).'">'.$player->name.'</a></td>
    <td>
      '.($game->can_join($current_player)?'<a href="'.Page::get_url(PAGE_CODE, array('action' => 'join', 'game_id' => $game->id)).'" class="btn btn-primary btn-xs">'.__('Join').'</a>':'').'
      '.($current_game && $current_game->id == $game->id?'<a href="'.Page::get_url('game_over').'" class="btn btn-danger btn-xs">'.__('Quit').'</a>':'').'
      '.(($current_player->id == $game->created_by || is_admin()) && $game->started === null?'<a href="'.Page::get_url(PAGE_CODE, array('action' => 'cancel', 'game_id' => $game->id)).'" class="btn btn-danger btn-xs">'.__('Cancel').'</a>':'').'
      '.(($current_player->id == $game->created_by || is_admin()) && $game->started === null?'<a href="'.Page::get_url(PAGE_CODE, array('action' => 'start', 'game_id' => $game->id)).'" class="btn btn-success btn-xs">'.__('Start').'</a>':'').'
    </td>
  </tr>';
  }
?>
  </tbody>
</table>
<h4><?php echo __('Wall')?></h4>
<?php if( $current_player->id ) :?>
<form action="<?php echo Page::get_url('shout')?>" method="post">
  <input type="hidden" name="url_return" value="<?php echo Page::get_url(PAGE_CODE)?>" />
  <p><?php echo '['.guess_time(time(), GUESS_TIME_LOCALE).']'?> <strong><?php echo wash_utf8($current_player->name)?></strong> : <input type="text" name="text" size="80" value=""/><button type="submit" name="action" value="shout">Say</button></p>
</form>
<?php endif;?>
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