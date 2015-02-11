<?php
  $PAGE_TITRE = __('Game: Economy' );

  /* @var $world World */
  /* @var $current_player Player */
  $territory_params = array('game_id' => $current_game->id);

  $is_current_turn = $turn == $current_game->current_turn;

  $game_parameters = $current_game->get_parameters();
?>
<ul class="nav nav-tabs">
  <li role="presentation" class="inactive">
    <a><?php echo $current_game->name?></a>
  </li>
  <li role="presentation">
    <a href="<?php echo Page::get_url('game_map')?>"><?php echo icon('world', '') . __('World map')?></a>
  </li>
  <li role="presentation">
    <a href="<?php echo Page::get_url('game_diplomacy')?>"><?php echo icon('diplomacy', '') . __('Diplomacy')?></a>
  </li>
  <li role="presentation" class="active">
    <a href="<?php echo Page::get_url(PAGE_CODE)?>"><?php echo icon('coins', '') . __('Economy')?></a>
  </li>
  <li role="presentation">
    <a href="<?php echo Page::get_url('game_show', array('id' => $current_game->id))?>"><?php echo icon('information', '') . __('Game Info')?></a>
  </li>
</ul>
<?php
  $world_id = $current_game->world_id;
  $world = World::instance( $world_id );

  $ratio = 1140 / $world->size_x;
  $options = array(
    'with_map' => false,
    'game_id' => $current_game->id,
    'turn' => $current_game->current_turn,
    'ratio' => $ratio,
    'no_names' => true,
    'force' => is_admin()
  );
?>
<style>
  .map_background:before {
    background-image: url('<?php echo $world->getImgUrl($options)?>');
  }
</style>
<div class="map_background">

<h2><?php echo __('Economy')?></h2>
<ul class="pagination pagination-sm">
  <?php for( $i = 0; $i <= $current_game->current_turn; $i ++ ) :?>
  <li<?php if( $i == $turn ) : ?> class="active"<?php endif;?>><a href="<?php echo Page::get_url(PAGE_CODE, array('game_id' => $current_game->id, 'turn' => $i))?>">
    <?php if( $i == 0 ) : ?>
      <?php echo __('Start');?>
    <?php elseif( $i == $current_game->current_turn ) : ?>
      <?php echo __('Current Turn');?>
    <?php else:?>
      <span class="glyphicon glyphicon-time" title="<?php echo __('Turn')?>" aria-hidden="true"></span><span class="sr-only"><?php echo __('Turn')?></span> <?php echo $i?>

    <?php endif;?>
  </a></li>
  <?php endfor;?>
</ul>
<h3><?php echo __('Territory Summary')?></h3>
<?php
    $previous_territory_summary = array();
    if( $turn > 1 ) {
      $previous_territory_summary = $current_player->get_territory_economy_summary($current_game, $turn - 1);
    }
    $territory_summary = $current_player->get_territory_economy_summary($current_game, $turn);
?>
<table class="table table-hover table-condensed accordion">
  <thead>
    <tr>
      <th><?php echo __('Territory')?></th>
      <th><?php echo __('Type')?></th>
      <th class="num"><?php echo __('Area')?></th>
      <th><?php echo __('Status')?></th>
      <th class="num"><?php echo __('Economy')?></th>
      <th><?php echo __('Forecast')?></th>
      <th class="num"><?php echo __('Suppression')?></th>
      <th class="num"><?php echo __('Revenue')?></th>
    </tr>
  </thead>
  <tfoot>

  </tfoot>
  <tbody>
<?php

    $total_territory = 0;
    $total_revenue = 0;
    foreach( $territory_summary as $territory_row ) {
      $territory = Territory::instance( $territory_row['territory_id'] );

      $territory_revenue =
        $game_parameters['ECONOMY_BASE_REVENUE']
        * ( $territory_row['economy_ratio'] )
        * ( 1 - $territory_row['revenue_suppression'] );

      $total_territory += $territory->area;
      $total_revenue += $territory_revenue;

      if( $territory_row['conflict'] ) {
        $status = icon('territory_conflict').__('Conflict');
      }elseif( $territory_row['contested'] ) {
        $status = icon('territory_contested').__('Contested');
      }else {
        $status = icon('territory_stable').__('Stable');
      }
      if( $territory_row['conflict'] ) {
        $economy_evolution = min(abs($game_parameters['ECONOMY_MODIFIER_WAR']), $territory_row['economy_ratio'] * 100);
        $economy_evolution_sign = '-';
      }else {
        $economy_evolution = min($game_parameters['ECONOMY_MODIFIER_PEACE'], 100 - $territory_row['economy_ratio'] * 100);
        $economy_evolution_sign = '+';
      }

      if( $economy_evolution == 0 ) {
        $economy_evolution_sign = '~';
      }

      echo '
    <tr>
      <td><a href="'.Page::get_url('show_territory', array('game_id' => $current_game->id, 'id' => $territory->id)).'">'.$territory->name.'</a></td>
      <td>'.($territory_row['capital']?'Capital':'Province').'</td>
      <td class="num">'.l10n_number( $territory->area ).' km²</td>
      <td>'.$status.'</td>
      <td class="num">'.l10n_number( $territory_row['economy_ratio'] * 100 ).' %</td>
      <td>(' . $economy_evolution_sign . l10n_number( $economy_evolution ).' %)</td>
      <td class="num">'.l10n_number( $territory_row['revenue_suppression'] * 100 ).' %</td>
      <td class="num">'.l10n_number( $territory_revenue ) . icon('coin') . '</td>
    </tr>';
    }
?>
  </tbody>
  <tfoot>
    <tr>
      <th></th>
      <th class="num"><?php echo __('Total')?></th>
      <td class="num"><?php echo l10n_number( $total_territory )?> km²</td>
      <th colspan="2"></th>
      <th class="num"><?php echo __('Total')?></th>
      <td class="num"><?php echo l10n_number( $total_revenue ) . icon('coin')?></td>
    </tr>
  </tfoot>
</table>
<?php
  $economy_forecast = $current_game->get_economy_summary($current_player->id, $turn - 1);
?>
<h3><?php echo __('Economy summary')?></h3>
<table class="table table-condensed">
  <tr>
    <th><?php echo __('Total revenue')?></th>
    <td class="num"></td>
    <td class="num"><?php echo l10n_number( $economy_forecast['revenue_before_bureaucracy'], 0 ) . icon('coin')?></td>
  </tr>
  <tr>
    <th><?php echo __('Bureaucracy ratio')?></th>
    <td class="num"><?php echo l10n_number( round($economy_forecast['bureaucracy_ratio'] * 100, 2), 2 ) . '%' . icon('bureaucracy')?></td>
    <td class="num">-<?php echo l10n_number( $economy_forecast['revenue_before_bureaucracy'] * (1 - $economy_forecast['bureaucracy_ratio']), 0 ) . icon('coin')?></td>
  </tr>
  <tr>
    <th><?php echo __('Revenue after bureaucracy')?></th>
    <td class="num"></td>
    <td class="num"><?php echo l10n_number( $economy_forecast['revenue'], 0 ) . icon('coin')?></td>
  </tr>
  <tr>
    <th><?php echo __('Troops home')?></th>
    <td class="num">
      <?php echo l10n_number( $economy_forecast['troops_home'], 0 ) . icon('troops')?>@
      <?php echo l10n_number( $current_game->parameters['TROOPS_HOME_MAINTENANCE'], 0 ) . icon('coin')?>
    </td>
    <td class="num">
      <?php echo l10n_number( $economy_forecast['troops_home'] * $current_game->parameters['TROOPS_HOME_MAINTENANCE'], 0 ) . icon('coin')?>
    </td>
  </tr>
  <tr>
    <th><?php echo __('Troops away')?></th>
    <td class="num">
      <?php echo l10n_number( $economy_forecast['troops_away'], 0 ) . icon('troops')?>@
      <?php echo l10n_number( $current_game->parameters['TROOPS_AWAY_MAINTENANCE'], 0) . icon('coin')?>
    </td>
    <td class="num">
      <?php echo l10n_number( $economy_forecast['troops_away'] * $current_game->parameters['TROOPS_AWAY_MAINTENANCE'], 0) . icon('coin')?>
    </td>
  </tr>
  <tr>
    <th><?php echo __('Total troops maintenance')?></th>
    <td class="num"></td>
    <td class="num">
      -<?php echo l10n_number( $economy_forecast['troops_maintenance'], 0 ) . icon('coin')?>
    </td>
  </tr>
  <tr>
    <th><?php echo __('Recruiting budget')?></th>
    <td class="num"></td>
    <td class="num">
      <?php echo l10n_number( $economy_forecast['recruit_budget'], 0 ) . icon('coin')?>
    </td>
  </tr>
  <tr>
    <th><?php echo __('Total troops recruited')?></th>
    <td class="num"></td>
    <td class="num">
      <?php echo l10n_number( $economy_forecast['troops_recruited'], 0 ) . icon('troops')?>@
      <?php echo l10n_number( $current_game->parameters['TROOPS_RECRUIT_PRICE'], 0) . icon('coin')?>
    </td>
  </tr>
</table>
<?php
  if( $is_current_turn ) {
    $economy_forecast = $current_game->get_economy_summary($current_player->id);
?>
<h3><?php echo __('Forecast for next turn')?></h3>
<table class="table table-condensed">
  <tr>
    <th><?php echo __('Total revenue')?></th>
    <td class="num"></td>
    <td class="num"><?php echo l10n_number( $economy_forecast['revenue_before_bureaucracy'], 0 ) . icon('coin')?></td>
  </tr>
  <tr>
    <th><?php echo __('Bureaucracy ratio')?></th>
    <td class="num"><?php echo l10n_number( round($economy_forecast['bureaucracy_ratio'] * 100, 2), 2 ) . '%' . icon('bureaucracy')?></td>
    <td class="num">-<?php echo l10n_number( $economy_forecast['revenue_before_bureaucracy'] * (1 - $economy_forecast['bureaucracy_ratio']), 0 ) . icon('coin')?></td>
  </tr>
  <tr>
    <th><?php echo __('Revenue after bureaucracy')?></th>
    <td class="num"></td>
    <td class="num"><?php echo l10n_number( $economy_forecast['revenue'], 0 ) . icon('coin')?></td>
  </tr>
  <tr>
    <th><?php echo __('Troops home')?></th>
    <td class="num">
      <?php echo l10n_number( $economy_forecast['troops_home'], 0 ) . icon('troops')?>@
      <?php echo l10n_number( $current_game->parameters['TROOPS_HOME_MAINTENANCE'], 0 ) . icon('coin')?>
    </td>
    <td class="num">
      <?php echo l10n_number( $economy_forecast['troops_home'] * $current_game->parameters['TROOPS_HOME_MAINTENANCE'], 0 ) . icon('coin')?>
    </td>
  </tr>
  <tr>
    <th><?php echo __('Troops away')?></th>
    <td class="num">
      <?php echo l10n_number( $economy_forecast['troops_away'], 0 ) . icon('troops')?>@
      <?php echo l10n_number( $current_game->parameters['TROOPS_AWAY_MAINTENANCE'], 0) . icon('coin')?>
    </td>
    <td class="num">
      <?php echo l10n_number( $economy_forecast['troops_away'] * $current_game->parameters['TROOPS_AWAY_MAINTENANCE'], 0) . icon('coin')?>
    </td>
  </tr>
  <tr>
    <th><?php echo __('Total troops maintenance')?></th>
    <td class="num"></td>
    <td class="num">
      -<?php echo l10n_number( $economy_forecast['troops_maintenance'], 0 ) . icon('coin')?>
    </td>
  </tr>
  <tr>
    <th><?php echo __('Recruiting budget')?></th>
    <td class="num"></td>
    <td class="num">
      <?php echo l10n_number( $economy_forecast['recruit_budget'], 0 ) . icon('coin')?>
    </td>
  </tr>
  <tr>
    <th><?php echo __('Total troops recruited')?></th>
    <td class="num"></td>
    <td class="num">
      <?php echo l10n_number( $economy_forecast['troops_recruited'], 0 ) . icon('troops')?>@
      <?php echo l10n_number( $current_game->parameters['TROOPS_RECRUIT_PRICE'], 0) . icon('coin')?>
    </td>
  </tr>
</table>
  <?php }?>
</div>