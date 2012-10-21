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
