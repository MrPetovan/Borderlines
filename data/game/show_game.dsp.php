<?php
  $PAGE_TITRE = __('Game : Showing "%s"', $game->name);

  $creator = Player::instance( $game->created_by );
?>
<h2><?php echo __('Showing "%s"', $game->name )?></h2>
<div class="informations formulaire">
  <p class="field">
    <span class="label"><?php echo __('Status')?></span>
    <span class="value"><?php echo __($game->status_string)?></span>
  </p>
  <p class="field">
    <span class="label"><?php echo __('Current Turn')?></span>
    <span class="value"><?php echo $game->current_turn.'/'.$game->turn_limit?></span>
  </p>
  <p class="field">
    <span class="label"><?php echo __('Turn Interval')?></span>
    <span class="value"><?php echo __('%s seconds', $game->turn_interval)?></span>
  </p>
<?php if( !$game->started && $game->min_players ) { ?>
  <p class="field">
    <span class="label"><?php echo __('Min Players')?></span>
    <span class="value"><?php echo $game->min_players?></span>
  </p>
<?php }?>
<?php if( $game->max_players ) {?>
  <p class="field">
    <span class="label"><?php echo __('Max Players')?></span>
    <span class="value"><?php echo $game->max_players?></span>
  </p>
<?php }?>
  <p class="field">
    <span class="label"><?php echo __('Created')?></span>
    <span class="value"><?php echo guess_time($game->created, GUESS_DATETIME_LOCALE)?>
    by <a href="<?php echo get_page_url('show_player', true, array('id' => $game->created_by ) )?>"><?php echo $creator->name?></a></span>
  </p>
<?php if( $game->started ) {?>
  <p class="field">
    <span class="label"><?php echo __('Started')?></span>
    <span class="value"><?php echo guess_time($game->started, GUESS_DATETIME_LOCALE)?></span>
  </p>
<?php }?>
<?php if( $game->updated && ! $game->ended ) {?>
  <p class="field">
    <span class="label"><?php echo __('Updated')?></span>
    <span class="value"><?php echo guess_time($game->updated, GUESS_DATETIME_LOCALE)?></span>
  </p>
<?php }?>
<?php if( $game->ended ) {?>
  <p class="field">
    <span class="label"><?php echo __('Ended')?></span>
    <span class="value"><?php echo guess_time($game->ended, GUESS_DATETIME_LOCALE)?></span>
  </p>
<?php }elseif( $game->updated ) { ?>
  <p class="field">
    <span class="label"><?php echo __('Next turn')?></span>
    <span class="value"><?php echo guess_time( $game->updated + $game->turn_interval, GUESS_DATETIME_LOCALE ) ?></span>
  </p>
<?php }?>
<?php $world = World::instance($game->world_id);?>
  <p class="field">
    <span class="label"><?php echo __('World')?></span>
    <span class="value">
      <a href="<?php echo Page::get_url('show_world', array('game_id' => $game->id))?>"><?php echo $world->name?></a>
    </span>
  </p>
</div>
<h3><?php echo __('Players')?></h3>
<?php

  if(count($game_player_list)) {
?>
<table>
  <thead>
    <tr>
      <th><?php echo __('#')?></th>
      <th><?php echo __('Player')?></th>
      <th><?php echo __('Turn Ready')?></th>
      <th><?php echo __('Controlled territory')?></th>
      <th><?php echo __('Total troops')?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="5"><?php echo $game->max_players?__('%s/%s players', count( $game_player_list ), $game->max_players):__('%s players', count( $game_player_list ))?></td>
    </tr>
  </tfoot>
  <tbody>
<?php
    foreach( $game_player_list as $key => $game_player_row ) {
      $player_id_player = Player::instance( $game_player_row['player_id'] );
      echo '
    <tr>
      <td class="num">'.($key + 1).'</td>
      <td><a href="'.get_page_url('show_player', true, array('id' => $player_id_player->id)).'">'.$player_id_player->name.'</a></td>';
      if( $game_player_row['turn_leave'] ) {
        echo '
      <td colspan="3">'.__('Left the game on turn %s', $game_player_row['turn_leave']).'</td>';
      }else {
        echo '
      <td class="num">'.$game_player_row['turn_ready'].'</td>
      <td class="num">'.l10n_number( $player_area[ 'player_' . $game_player_row['player_id'] ] ).' km²</td>
      <td class="num">'.l10n_number( $player_troops[ $game_player_row['player_id'] ] ).' <img src="'.IMG.'img_html/helmet.png" alt="Troops" title="Troops"/></td>';
      }
      echo '
    </tr>';
    }
?>
  </tbody>
</table>
<?php
  }else {
    echo '
<p>'.__('No player yet').'</p>';
  }

  $is_in_a_game = $current_player->get_current_game() != false;
  $is_playing_in = $is_in_a_game || $game->get_game_player_list( $current_player->id );
  if( !$game->started && !$is_playing_in ) {
    echo '
<p><a href="'.Page::get_page_url(PAGE_CODE, false, array('action' => 'join', 'id' => $game->id)).'">'.__('Join this game').'</a></p>';
  }

  if( is_admin() ) {
    echo '<p><a href="'.Page::get_url('admin_game_view', array('id' => $game->id )).'">'.__('Manage game').'</a></p>';
  }
?>
<p><a href="<?php echo get_page_url('game_list')?>"><?php echo __('Return to game list')?></a></p>