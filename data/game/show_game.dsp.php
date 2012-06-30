<?php
  $PAGE_TITRE = 'Game : Showing "'.$game->name.'"';

  $creator = Player::instance( $game->created_by );
?>
<h2>Showing "<?php echo $game->name?>"</h2>
<div class="informations formulaire">
  <p class="field">
    <span class="libelle">Status</span>
    <span class="value"><?php echo $game->status_string?></span>
  </p>
  <p class="field">
    <span class="libelle">Current Turn</span>
    <span class="value"><?php echo $game->current_turn.'/'.$game->turn_limit?></span>
  </p>
  <p class="field">
    <span class="libelle">Turn Interval</span>
    <span class="value"><?php echo $game->turn_interval?> seconds</span>
  </p>
<?php if( !$game->started && $game->min_players ) { ?>
  <p class="field">
    <span class="libelle">Min Players</span>
    <span class="value"><?php echo $game->min_players?></span>
  </p>
<?php }?>
<?php if( $game->max_players ) {?>
  <p class="field">
    <span class="libelle">Max Players</span>
    <span class="value"><?php echo $game->max_players?></span>
  </p>
<?php }?>
  <p class="field">
    <span class="libelle">Created</span>
    <span class="value"><?php echo guess_time($game->created, GUESS_DATE_FR)?>
    by <a href="<?php echo get_page_url('show_player', true, array('id' => $game->created_by ) )?>"><?php echo $creator->name?></a></span>
  </p>
<?php if( $game->started ) {?>
  <p class="field">
    <span class="libelle">Started</span>
    <span class="value"><?php echo guess_time($game->started, GUESS_DATE_FR)?></span>
  </p>
<?php }?>
<?php if( $game->updated && ! $game->ended ) {?>
  <p class="field">
    <span class="libelle">Updated</span>
    <span class="value"><?php echo guess_time($game->updated, GUESS_DATE_FR)?></span>
  </p>
<?php }?>
<?php if( $game->ended ) {?>
  <p class="field">
    <span class="libelle">Ended</span>
    <span class="value"><?php echo guess_time($game->ended, GUESS_DATE_FR)?></span>
  </p>
<?php }elseif( $game->updated ) { ?>
  <p class="field">
    <span class="libelle">Next turn</span>
    <span class="value"><?php echo guess_time( $game->updated + $game->turn_interval, GUESS_TIME_FR ) ?></span>
  </p>
<?php }?>
</div>
<h3>Players</h3>
<?php

  $game_player_list = $game->get_game_player_list();

  if(count($game_player_list)) {
?>
<table>
  <thead>
    <tr>
      <th>Player</th>
      <th>Turn Ready</th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="3"><?php echo count( $game_player_list ).($game->max_players?'/'.$game->max_players:'')?> players</td>
    </tr>
  </tfoot>
  <tbody>
<?php
    foreach( $game_player_list as $game_player ) {
      $player_id_player = Player::instance( $game_player['player_id'] );
      echo '
    <tr>
      <td><a href="'.get_page_url('show_player', true, array('id' => $player_id_player->id)).'">'.$player_id_player->name.'</a></td>
      <td>'.$game_player['turn_ready'].'</td>
    </tr>';
    }
?>
  </tbody>
</table>
<?php
  }else {
    echo '
<p>No player yet</p>';
  }
  
  $is_in_a_game = $current_player->get_current_game() != false;
  $is_playing_in = $is_in_a_game || $game->get_game_player_list( $current_player->id );
  if( !$game->started && !$is_playing_in ) {
    echo '
<p><a href="'.Page::get_page_url(PAGE_CODE, false, array('action' => 'join', 'id' => $game->id)).'">Join this game</a></p>';
  }
  
  if( is_admin() ) {
    echo '<p><a href="'.Page::get_url('compute_orders', array('id' => $game->id )).'">Manage game</a></p>';
  }
?>
<p><a href="<?php echo get_page_url('game_list')?>">Return to game list</a></p>