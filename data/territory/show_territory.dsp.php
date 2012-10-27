<?php
  $PAGE_TITRE = __('Territory : Showing "%s"', $territory->name );

  /* @var $territory Territory */
  /* @var $current_game Game */
  /* @var $current_player Player */
  $territory_owner_id = $territory->get_owner( $current_game->id, $turn );
  if( $territory_owner_id !== null ) {
    $owner = Player::instance($territory_owner_id);
  }

  $neighbour_list = array();
  foreach( $territory->get_territory_neighbour_list() as $territory_neighbour ) {
    $neighbour_list[] = Territory::instance($territory_neighbour['neighbour_id']);
  }
  $world = World::instance( $territory->world_id );

  $is_ajax = strrpos(PAGE_CODE, 'ajax') === strlen( PAGE_CODE ) - 4;

  $orders = Player_Order::db_get_planned_by_player_id($current_player->id, $current_game->id);

  $is_current_turn = $turn == $current_game->current_turn;
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
  <p>
    <span class="label"><?php echo __('Current owner')?></span>
    <span class="value"><?php echo $territory_owner_id?'<a href="'.Page::get_url('show_player', array('id' => $owner->id)).'">'.$owner->name.'</a>':__('Nobody')?></span>
  </p>
  <p>
    <span class="label"><?php echo __('Status')?></span>
    <span class="value"><?php echo $territory->is_contested( $current_game->id, $turn )?__('Contested'):__('Stable')?></span>
  </p>
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
      $territory_owner_row = array_pop( $neighbour->get_territory_owner_list( $current_game->id, $turn ) );
      if( $territory_owner_row['owner_id'] !== null ) {
        $owner = Player::instance($territory_owner_row['owner_id']);
      }
      echo '
    <tr>
      <td><a href="'.Page::get_url('show_territory', array('id' => $neighbour->id)).'">'.$neighbour->name.'</a></td>
      <td>'.l10n_number( $neighbour->get_area() ).' km²</td>
      <td>'.($territory_owner_row['owner_id']?'<a href="'.Page::get_url('show_player', array('id' => $owner->id)).'">'.$owner->name.'</a>':__('Nobody')).'</td>
      <td>'.($territory_owner_row['contested']?__('Contested'):__('Stable')).'</td>
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
      <th><?php echo __('Quantity')?></th>
    </tr>
  </thead>
<?php
    $current_turn = null;
    $player_troops = 0;
    foreach( $territory->get_territory_player_troops_list( $current_game->id ) as $territory_player_troops ) {
      $player = Player::instance( $territory_player_troops['player_id'] );

      if( $current_turn != $territory_player_troops['turn']) {
        $is_current = $territory_player_troops['turn'] == $turn;

        if( $current_turn !== null ) {
          echo '
  </tbody>';
        }
        echo '
  <tbody class="archive'.($is_current?' current':'').'">
    <tr class="title">
      <th colspan="2">'.__('Turn %s', $territory_player_troops['turn']).'</th>
    </tr>';

        $current_turn = $territory_player_troops['turn'];
      }

      if( $is_current && $player == $current_player ) {
        $player_troops = $territory_player_troops['quantity'];
      }

      echo '
    <tr>
      <td><a href="'.Page::get_url('show_player', array('id' => $player->id)).'">'.$player->name.'</a></td>
      <td>'.l10n_number( $territory_player_troops['quantity'] ).' <img src="'.IMG.'img_html/helmet.png" alt="'.__('Troops').'" title="'.__('Troops').'"/></td>
    </tr>';
    }
?>
  </tbody>
</table>

<?php if( $is_current_turn ) :?>

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
      <td class="num"><?php echo l10n_number( $player_troops ).' <img src="'.IMG.'img_html/helmet.png" alt="'.__('Troops').'" title="'.__('Troops').'"/>'?></td>
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
      <td class="num">'.l10n_number( $planned_order['count'] ).' <img src="'.IMG.'img_html/helmet.png" alt="'.__('Troops').'" title="'.__('Troops').'"/></td>
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
<?php
    echo Player_Order::get_html_form_by_class(
      'move_troops',
      array('current_player' => $current_player, 'from_territory' => $territory),
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
?>

<?php endif; //if( $turn == $current_game->current_turn )?>

<?php if( !$is_ajax ) :?>
<p><a href="<?php echo Page::get_url('show_world', array('id' => $territory->world_id, 'game_id' => $current_game->id, 'turn' => $turn ) )?>"><?php echo __('Return to world map')?></a></p>
<?php endif; //if( !$is_ajax )?>