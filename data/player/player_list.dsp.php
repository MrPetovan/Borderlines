<?php
  $PAGE_TITRE = __('Player list');
?>
<h2><?php echo __('Player list')?></h2>
<?php

  if(count($player_list)) {
?>
<table>
  <thead>
    <tr>
      <th><?php echo __('#')?></th>
      <th><?php echo __('Player')?></th>
      <th><?php echo __('Games played')?></th>
      <th><?php echo __('Total territory')?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="4"><?php echo __('%s players', count( $player_list ))?></td>
    </tr>
  </tfoot>
  <tbody>
<?php
    foreach( $player_list as $key => $player ) {
      echo '
    <tr>
      <td class="num">'.($key + 1).'</td>
      <td><a href="'.get_page_url('show_player', true, array('id' => $player->id)).'">'.$player->name.'</a></td>
      <td class="num">'.l10n_number( $game_player_count_list[ 'player_' . $player->id ] ).'</td>
      <td class="num">'.l10n_number( $game_player_area_sum_list[ 'player_' . $player->id ] ).' km²</td>
    </tr>';
    }
?>
  </tbody>
</table>
<?php
  }
?>
<h3><?php echo __('Area calculation')?></h3>
<p><?php echo __('At the start of each game, you are loaned the area of your first territory. That means that this area is always substracted from your total area score.')?></p>
<p><?php echo __('From there, every other territory you control at the end of the game adds up its area to your total territory.')?></p>
<p><?php echo __('That produces three cases :')?></p>
<ul>
  <li><?php echo __('You have only your starting territory until the end of the game : your total territory gained for this game is 0.')?></li>
  <li><?php echo __('You control 1 or more territories on top of your starting territory at the end of the game : their area is added to your total territory gained for this game.')?></li>
  <li><?php echo __('You left the game or have been wiped out : your total score for the game is negative.')?></li>
</ul>