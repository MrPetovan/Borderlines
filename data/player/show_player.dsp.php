<?php
  $PAGE_TITRE = __('Player : Showing "%s"', $player->name);
?>
<h2><?php echo __('Player %s', $player->name)?></h2>
<div class="informations formulaire">
  <!--<div class="field form-group">
    <span class="label"><?php echo __('Joined')?></span>
    <span class="value"><?php echo guess_time($player->created, GUESS_DATETIME_LOCALE)?></span>
  </div>
  <div class="field form-group">
    <span class="label"><?php echo __('Status')?></span>
    <span class="value"><?php echo guess_time($player->created, GUESS_DATETIME_LOCALE)?></span>
  </div>-->
</div>
<h3><?php echo __('Games')?></h3>
<table>
  <thead>
    <tr>
      <th><?php echo __('Game')?></th>
      <th><?php echo __('Number of players')?></th>
      <th><?php echo __('Territory gain')?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td></td>
      <td><?php echo __('Total area')?></td>
      <td><?php echo l10n_number( array_sum( $game_player_area ) )?> km²</td>
    </tr>
  </tfoot>
  <tbody>
<?php foreach( $game_player_list as $game_player_row ):
  $game = Game::instance($game_player_row['game_id']);
  $game_player_count = count( $game->get_game_player_list() );
?>
    <tr>
      <td><a href="<?php echo Page::get_url('show_game', array('id' => $game->id))?>"><?php echo $game->name?></a></td>
      <td><?php echo l10n_number($game_player_count)?></td>
      <td>
     <?php if( $game_player_area[ $game->id ] !== null ) {
       echo l10n_number($game_player_area[ $game->id ]).' km²';
     }else {
       echo __('Game running');
     }?>
      </td>
    </tr>
<?php endforeach;?>
  </tbody>
</table>