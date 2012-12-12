<?php
  $PAGE_TITRE = __('Territory : Showing "%s"', $territory->name );

  /* @var $territory Territory */
  /* @var $current_game Game */
  /* @var $current_player Player */
  /* @var $territory_owner Player */
  $territory_params = array();
  if( $current_game ) {
    $territory_status = $territory->get_territory_status($current_game, $turn);
    $territory_owner = Player::instance($territory_status['owner_id']);

    $territory_params = array('game_id' => $current_game->id );
  }

  $neighbour_list = array();
  foreach( $territory->get_territory_neighbour_list() as $territory_neighbour ) {
    $neighbour_list[] = Territory::instance($territory_neighbour['neighbour_id']);
  }
  $world = World::instance( $territory->world_id );

  $is_ajax = strrpos(PAGE_CODE, 'ajax') === strlen( PAGE_CODE ) - 4;

  $orders = Player_Order::db_get_planned_by_player_id($current_player->id, $current_game->id);

  $is_current_turn = $turn == $current_game->current_turn;

  $is_contested = $territory_status['contested'];
?>

<?php if( $is_current_turn ) :?>
<h2><?php echo __('"%s"', $territory->name)?></h2>
<?php else :?>
<h2><?php echo __('"%s" on turn %s', $territory->name, $turn)?></h2>
<?php endif;?>
<div class="informations formulaire">
  <p>
    <span class="label"><?php echo __('Area')?></span>
    <span class="value"><?php echo l10n_number( $territory->get_area() )?> km²</span>
  </p>
  <p>
    <span class="label"><?php echo __('Border length')?></span>
    <span class="value"><?php echo l10n_number( $territory->get_perimeter() )?> km</span>
  </p>
  <p>
    <span class="label"><?php echo __('Capital city')?></span>
    <span class="value"><?php echo $territory->capital_name?></span>
  </p>
<?php if( $current_game ) :?>
  <p>
    <span class="label"><?php echo __('Current owner')?></span>
    <span class="value"><?php echo $territory_owner->id?'<a href="'.Page::get_url('show_player', array('id' => $territory_owner->id)).'">'.$territory_owner->name.'</a>':__('Nobody')?></span>
  </p>
<?php
  $distance = $territory->get_distance_to_capital($current_game, $turn);

  if( $distance ) {
?>
  <p>
    <span class="label"><?php echo __('Distance to the owner\'s capital')?></span>
    <span class="value"><?php echo $distance?l10n_number( $distance ).' km':__('No capital')?></span>
  </p>
<?php
  }
  $corruption_ratio = $territory->get_corruption_ratio_from_distance( $distance );
  $economy_ratio = $territory->get_economy_ratio( $current_game, $turn );
?>
  <p>
    <span class="label"><?php echo __('Economy ratio')?></span>
    <span class="value"><?php echo l10n_number( round( $economy_ratio * 100 ) ).' %'?></span>
  </p>
  <p>
    <span class="label"><?php echo __('Corruption')?></span>
    <span class="value"><?php echo l10n_number( round( $corruption_ratio * 100 ) ).' %'?></span>
  </p>

  <p>
    <span class="label"><?php echo __('Status')?></span>
    <span class="value"><?php echo $is_contested?__('Contested'):__('Stable')?></span>
  </p>
<?php if( $is_contested ) :?>
  <p>
    <span class="label"><?php echo __('Revenue Suppression')?></span>
    <span class="value"><?php echo l10n_number( $territory_status['revenue_suppression'] * 100 )?> %</span>
  </p>
<?php endif;?>
<?php endif; //if( $current_game ) :?>
</div>
<?php if( !$is_ajax ) :?>
<p>
    <?php echo $world->drawImg(array(
        'with_map' => true,
        'territories' => array_merge( array($territory), $neighbour_list),
        'game_id' => $current_game->id,
        'turn' => $turn
    ));?>
</p>
<p><a href="<?php echo Page::get_url('show_world', array('id' => $territory->world_id, 'game_id' => $current_game->id, 'turn' => $turn ) )?>"><?php echo __('Return to world map')?></a></p>
<h3><?php echo __('Neighbours')?></h3>
<table>
  <thead>
    <tr>
      <th><?php echo __('Name')?></th>
      <th><?php echo __('Area')?></th>
      <th><?php echo __('Current owner')?></th>
      <th><?php echo __('Status')?></th>
    </tr>
  </thead>
  <tbody>
<?php
    foreach( $neighbour_list as $neighbour ) {
      $territory_status_row = array_pop( $neighbour->get_territory_status_list( $current_game->id, $turn ) );
      if( $territory_status_row['owner_id'] !== null ) {
        $territory_owner = Player::instance($territory_status_row['owner_id']);
      }
      echo '
    <tr>
      <td><a href="'.Page::get_url('show_territory', array_merge( $territory_params, array('id' => $neighbour->id) )).'">'.$neighbour->name.'</a></td>
      <td>'.l10n_number( $neighbour->get_area() ).' km²</td>
      <td>'.($territory_status_row['owner_id']?'<a href="'.Page::get_url('show_player', array('id' => $territory_owner->id)).'">'.$territory_owner->name.'</a>':__('Nobody')).'</td>
      <td>'.($territory_status_row['contested']?__('Contested'):__('Stable')).'</td>
    </tr>';
    }
?>
  </tbody>
</table>

<?php endif; //if( !$is_ajax )?>

<h3><?php echo __('Troops')?></h3>
<table class="accordion">
  <thead>
    <tr>
      <th><?php echo __('Player')?></th>
      <th colspan="2"><?php echo __('Quantity')?></th>
    </tr>
  </thead>
<?php
    $current_turn = null;
    $player_troops = 0;

    $territory_player_status_list = $current_game->get_territory_player_status_list(null, $territory->id);

    $supremacy = array();
    foreach( $territory_player_status_list as $territory_player_status_row ) {
      $supremacy[ $territory_player_status_row['turn'] ][ $territory_player_status_row['player_id'] ] = $territory_player_status_row['supremacy'];
    }

    foreach( $current_game->get_territory_player_troops_list( null, $territory->id ) as $territory_player_troops ) {
      /* @var $player Player */
      $player = Player::instance( $territory_player_troops['player_id'] );

      if( $current_turn !== $territory_player_troops['turn']) {
        $is_current = $territory_player_troops['turn'] == $turn;

        if( $current_turn !== null ) {
          echo '
  </tbody>';
        }
        echo '
  <tbody class="archive'.($is_current?' current':'').'">
    <tr class="title">
      <th colspan="3">'.__('Turn %s', $territory_player_troops['turn']).'</th>
    </tr>';

        $current_turn = $territory_player_troops['turn'];
      }

      if( $is_current && $player == $current_player ) {
        $player_troops = $territory_player_troops['quantity'];
      }

      $troops_history = $player->get_territory_player_troops_history_list($current_game->id, $current_turn, $territory->id);
      foreach( $troops_history as $troops_history_row ) {
        echo '
    <tr>
      <td></td>
      <td>'.__( $troops_history_row['reason'] ).'</td>
      <td class="num">'.($troops_history_row['delta']>=0?'+':'').l10n_number( $troops_history_row['delta'] ).' <img src="'.IMG.'img_html/troops.png" alt="'.__('Troops').'" title="'.__('Troops').'"/></td>
    </tr>';
      }

      echo '
    <tr>
      <td><a href="'.Page::get_url('show_player', array('id' => $player->id)).'">'.$player->name.'</a></td>
      <td class="num">'.(!isset( $supremacy[$current_turn][$player->id] ) || $supremacy[$current_turn][$player->id]?__('Supremacy').' <img src="'.IMG.'img_html/lightning.png" alt=""/>':__('Retreat').' <img src="'.IMG.'img_html/link_break.png" alt=""/>'). '</td>
      <td class="num">' . l10n_number( $territory_player_troops['quantity'] ).' <img src="'.IMG.'img_html/troops.png" alt="'.__('Troops').'" title="'.__('Troops').'"/></td>
    </tr>';
    }
?>
  </tbody>
</table>

<?php if( $is_current_turn && $current_game && !$current_game->has_ended() ) :?>

<h3><?php echo __('Planned movements')?></h3>
<?php
  $planned_orders = array();
  foreach( $orders as $order ) {
    if( $order->order_type_id == 6 ) {
      $params = $order->parameters;
      if( $params['from_territory_id'] == $territory->id || $params['to_territory_id'] == $territory->id ) {
        $planned_order = array(
            'origin' => null,
            'count' => 0,
            'destination' => null,
            'order_id' => $order->id
        );
        $direction = $params['from_territory_id'] == $territory->id;
        if( $direction ) {
          $destination = Territory::instance($params['to_territory_id']);
          $planned_order['destination'] = $destination;
          $planned_order['count'] = '-'.$params['count'];
          $player_troops -= $params['count'];
        }else {
          $origin = Territory::instance($params['from_territory_id']);
          $planned_order['origin'] = $origin;
          $planned_order['count'] = '+'.$params['count'];
          $player_troops += $params['count'];
        }
        $planned_orders[] = $planned_order;
      }
    }
  }

  if( count( $planned_orders ) ) { ?>
<table>
  <thead>
    <tr>
      <th><?php echo __('Origin')?></th>
      <th><?php echo __('Movement')?></th>
      <th><?php echo __('Destination')?></th>
      <th><?php echo __('Action')?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <th><?php echo __('On turn %s', $current_game->current_turn + 1 )?></th>
      <td class="num"><?php echo l10n_number( $player_troops ).' <img src="'.IMG.'img_html/troops.png" alt="'.__('Troops').'" title="'.__('Troops').'"/>'?></td>
      <td></td>
      <td></td>
    </tr>
  </tfoot>
  <tbody>
<?php
  foreach( $planned_orders as $planned_order ) {
    echo '
    <tr>
      <td>'.($planned_order['origin']?'<a href="'.Page::get_url('show_territory', array('id' => $planned_order['origin']->id)).'">'.$planned_order['origin']->name.'</a>':'').'</td>
      <td class="num">'.l10n_number( $planned_order['count'] ).' <img src="'.IMG.'img_html/troops.png" alt="'.__('Troops').'" title="'.__('Troops').'"/></td>
      <td>'.($planned_order['destination']?'<a href="'.Page::get_url('show_territory', array('id' => $planned_order['destination']->id)).'">'.$planned_order['destination']->name.'</a>':'').'</td>
      <td>
        <form action="'.Page::get_url('order').'" method="post">
          '.HTMLHelper::genererInputHidden('url_return', Page::get_url( PAGE_CODE, array('id' => $territory->id) ) ).'
          '.HTMLHelper::genererInputHidden('id', $planned_order['order_id'] ).'
          <button type="submit" name="action" value="cancel">'.__('Cancel').'</button>
        </form>
      </td>
    </tr>';
  }
?>
  </tbody>
</table>
<?php }else{?>
<p><?php echo __("You don't have planned any order in this territory")?>
<?php } ?>
<h3>Issue an order</h3>
<script>
  $( function () {
    $( ".orders" ).accordion({
      collapsible: true,
      header: "legend",
      fillSpace: 0,
      autoHeight: 0,
      active: false
    });
  })
</script>
<div class="orders">
<?php
    echo Player_Order::get_html_form_by_class(
      'move_troops',
      array('current_player' => $current_player, 'from_territory' => $territory),
      array('id' => $territory->id )
    );

    echo Player_Order::get_html_form_by_class(
      'move_troops',
      array('current_player' => $current_player, 'from_territory' => $territory, 'future' => 1),
      array('id' => $territory->id )
    );

    echo Player_Order::get_html_form_by_class(
      'move_troops',
      array('current_player' => $current_player, 'to_territory' => $territory),
      array('id' => $territory->id )
    );

    echo Player_Order::get_html_form_by_class(
      'give_troops',
      array('current_player' => $current_player, 'from_territory' => $territory),
      array('id' => $territory->id )
    );

    echo Player_Order::get_html_form_by_class(
      'change_capital',
      array('current_player' => $current_player, 'territory' => $territory),
      array('id' => $territory->id )
    );

    echo Player_Order::get_html_form_by_class(
      'give_territory',
      array('current_player' => $current_player, 'territory' => $territory),
      array('id' => $territory->id )
    );
?>
</div>
<?php endif; //if( $is_current_turn && !$game->has_ended() )?>

<?php if( !$is_ajax ) :?>
<p><a href="<?php echo Page::get_url('show_world', array('id' => $territory->world_id, 'game_id' => $current_game->id, 'turn' => $turn ) )?>"><?php echo __('Return to world map')?></a></p>
<?php endif; //if( !$is_ajax )?>