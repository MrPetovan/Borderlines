<?php
  $PAGE_TITRE = __('Game: Diplomacy' );

  /* @var $world World */
  /* @var $current_player Player */
  $territory_params = array('game_id' => $current_game->id);

  $game_parameters = $current_game->get_parameters();
?>
<ul class="nav nav-tabs">
  <li role="presentation">
    <a href="<?php echo Page::get_url('game_map')?>"><?php echo icon('world', '') . __('World map')?></a>
  </li>
  <li role="presentation">
    <a href="<?php echo Page::get_url('game_diplomacy')?>"><?php echo icon('diplomacy', '') . __('Diplomacy')?></a>
  </li>
  <li role="presentation" class="active">
    <a href="<?php echo Page::get_url(PAGE_CODE)?>"><?php echo icon('coins', '') . __('Economy')?></a>
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
        $game_parameters['TERRITORY_BASE_REVENUE']
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

      echo '
    <tr>
      <td><a href="'.Page::get_url('show_territory', array('game_id' => $current_game->id, 'id' => $territory->id)).'">'.$territory->name.'</a></td>
      <td>'.($territory_row['capital']?'Capital':'Province').'</td>
      <td class="num">'.l10n_number( $territory->area ).' km²</td>
      <td>'.$status.'</td>
      <td class="num">'.l10n_number( $territory_row['economy_ratio'] * 100 ).' %</td>
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
  $revenue_before_bureaucracy = $total_revenue;

  $bureaucracy_ratio = $current_game->get_bureaucracy_ratio(count($current_player->get_territory_status_list(null, $current_game->id, $turn )));

  $revenue = $revenue_before_bureaucracy * $bureaucracy_ratio;

  $troops_home = 0;
  $troops_away = 0;
  $troops_list = $current_game->get_territory_player_troops_list($turn, null, $current_player->id);
  $territory_status_list = $current_game->get_territory_status_list(null, $turn, $current_player->id);
  foreach( $troops_list as $territory_player_troops_row ) {
    $is_home = false;

    foreach( $territory_status_list as $territory_previous_owner_row ) {
      if( $territory_previous_owner_row['territory_id'] == $territory_player_troops_row['territory_id'] ) {
        $is_home = true;
        break;
      }
    }

    if( $is_home ) {
      $troops_home += $territory_player_troops_row['quantity'];
    }else {
      $troops_away += $territory_player_troops_row['quantity'];
    }
  }

  $options = $current_game->get_parameters();

  $troops_maintenance = $troops_home * $options['HOME_TROOPS_MAINTENANCE'] + $troops_away * $options['AWAY_TROOPS_MAINTENANCE'];

  $recruit_budget = $revenue - $troops_maintenance;

  // Is there a capital ?
  $capital = $current_player->get_capital($current_game);

  $troops_recruited = 0;
  if( $capital->id !== null ) {
    $troops_recruited = floor( $recruit_budget / $options['RECRUIT_TROOPS_PRICE'] );
  }
?>
<table class="table table-condensed">
  <tr>
    <th><?php echo __('Total revenue for turn %s', $turn)?></th>
    <td class="num"></td>
    <td class="num"><?php echo l10n_number( $revenue_before_bureaucracy, 0 ) . icon('coin')?></td>
  </tr>
  <tr>
    <th><?php echo __('Bureaucracy ratio for turn %s', $turn)?></th>
    <td class="num"><?php echo l10n_number( round($bureaucracy_ratio * 100, 2), 2 ) . '%' . icon('bureaucracy')?></td>
    <td class="num">-<?php echo l10n_number( $revenue_before_bureaucracy * (1 - $bureaucracy_ratio), 0 ) . icon('coin')?></td>
  </tr>
  <tr>
    <th><?php echo __('Revenue after bureaucracy')?></th>
    <td class="num"></td>
    <td class="num"><?php echo l10n_number( $revenue, 0 ) . icon('coin')?></td>
  </tr>
  <tr>
    <th><?php echo __('Troops home')?></th>
    <td class="num">
      <?php echo l10n_number( $troops_home, 0 ) . icon('troops')?>@
      <?php echo l10n_number( $options['HOME_TROOPS_MAINTENANCE'], 0 ) . icon('coin')?>
    </td>
    <td class="num">
      <?php echo l10n_number( $troops_home * $options['HOME_TROOPS_MAINTENANCE'], 0 ) . icon('coin')?>
    </td>
  </tr>
  <tr>
    <th><?php echo __('Troops away')?></th>
    <td class="num">
      <?php echo l10n_number( $troops_away, 0 ) . icon('troops')?>@
      <?php echo l10n_number( $options['AWAY_TROOPS_MAINTENANCE'], 0) . icon('coin')?>
    </td>
    <td class="num">
      <?php echo l10n_number( $troops_away * $options['AWAY_TROOPS_MAINTENANCE'], 0) . icon('coin')?>
    </td>
  </tr>
  <tr>
    <th><?php echo __('Total troops maintenance')?></th>
    <td class="num"></td>
    <td class="num">
      -<?php echo l10n_number( $troops_maintenance, 0 ) . icon('coin')?>
    </td>
  </tr>
  <tr>
    <th><?php echo __('Recruiting budget')?></th>
    <td class="num"></td>
    <td class="num">
      <?php echo l10n_number( $recruit_budget, 0 ) . icon('coin')?>
    </td>
  </tr>
  <tr>
    <th><?php echo __('Total troops recruited')?></th>
    <td class="num"></td>
    <td class="num">
      <?php echo l10n_number( $troops_recruited, 0 ) . icon('troops')?>@
      <?php echo l10n_number( $options['RECRUIT_TROOPS_PRICE'], 0) . icon('coin')?>
    </td>
  </tr>
</table>

</div>