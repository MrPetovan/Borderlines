<?php
  $resource_list = Resource::db_get_all();
  
?>
<h2>Dashboard</h2>
<p>Welcome <?php echo $current_player->get_name()?> !</p>
<p><a href="<?php echo Page::get_page_url('player_list')?>">Player list</a></p>
<h3>Current Game</h3>
<?php
  $game_status = "Created";
  if( $current_game->ended ) {
    $game_status = "Ended";
  }elseif( $current_game->started ) {
    $game_status = "Started";
  }
?>
<ul>
  <li>Name : <?php echo $current_game->name ?></li>
  <li>Turn : <?php echo $current_game->current_turn.'/'.$current_game->turn_limit ?></li>
  <li>Turn interval : <?php echo $current_game->turn_interval ?> seconds</li>
  <li>Status : <?php echo $game_status ?></li>
  <li>Created : <?php echo guess_time( $current_game->created, GUESS_TIME_FR ) ?></li>
<?php if( $current_game->started ) { ?>
  <li>Started : <?php echo guess_time( $current_game->started, GUESS_TIME_FR ) ?></li>
<?php } ?>
<?php if( $current_game->updated ) { ?>
  <li>Last turn : <?php echo guess_time( $current_game->updated, GUESS_TIME_FR ) ?></li>
  <li>Next turn : <?php echo guess_time( $current_game->updated + $current_game->turn_interval, GUESS_TIME_FR ) ?></li>
<?php } ?>
<?php if( $current_game->ended ) { ?>
  <li>Ended : <?php echo guess_time( $current_game->ended, GUESS_TIME_FR ) ?></li>
<?php } ?>
</ul>
<h3>Resources</h3>
<?php
  $sums = $current_player->get_resource_sum_list( $current_game->id );
?>
<ul>
<?php
  foreach( $sums as $sum ) {
    $resource = Resource::instance($sum['id']);
    echo '
  <li>'.$resource->get_name().' : '.$sum['sum'].'</li>';
  }
?>
</ul>
<h3>Resource history</h3>
<table>
  <thead>
    <tr>
      <th rowspan="2">Turn</th>
      <th rowspan="2">Event</th>
      <th colspan="<?php echo count( $resource_list )?>">Resource</th>
    </tr>
    <tr>
<?php
  foreach( $resource_list as $resource ) {
    echo '
      <th>'.$resource->get_name().'</th>';
  }
?>
    </tr>
  </thead>
  <tbody>
    <tr>
<?php
  $history = $current_player->get_resource_history();
  $current_player_order_id = null;
  $flag_first = true;
  $resource_delta = array();
  foreach( $resource_list as $resource ) {
    $resource_delta[ $resource->id ] = 0;
  }
  $event_list = array();
  $key = -1;
  foreach( $history as $history_item ) {
    if( is_null( $history_item['player_order_id'] ) || $current_player_order_id != $history_item['player_order_id'] ) {
      $key++;
      foreach( $resource_list as $resource ) {
        $resource_delta[ $resource->id ] = 0;
      }
      $current_player_order_id = $history_item['player_order_id'];
    }
    $event_list[ $key ]['reason'] = $history_item['reason'];
    $event_list[ $key ]['turn'] = $history_item['turn'];
    $event_list[ $key ]['resource_delta'][ $history_item['resource_id'] ] = $history_item['delta'];
  }

  foreach( $event_list as $event ) {
    echo '
    <tr>
      <td class="date">'.$event['turn'].'</td>
      <td>'.$event['reason'].'</td>';
    foreach( $resource_list as $resource ) {
      if( isset( $event['resource_delta'][ $resource->id ] ) ) {
        $delta = $event['resource_delta'][ $resource->id ];
        echo '
      <td class="num">'. ($delta > 0?'+':'') . $delta .'</td>';
      }else {
        echo '
      <td></td>';
      }
    }
    echo '
    </tr>';
  }
?>
</table>
<h3>Orders</h3>
<h4>Orders planned</h4>
<?php
  $orders = Player_Order::db_get_planned_by_player_id( $current_player->id, $current_game->id );
?>
<table>
  <tr>
    <th>Order Type</th>
    <th>Order</th>
    <th>Scheduled</th>
    <th>Parameters</th>
    <th>Action</th>
  </tr>
<?php
  foreach( $orders as $player_order ) {
    $order_type = Order_Type::instance( $player_order->order_type_id );
    $parameters = unserialize( $player_order->parameters );
    $param_string = '';
    foreach( $parameters as $key => $value ) {
      if( $key == 'player_id' ) {
        $player = Player::instance( $value );
        $value = $player->name;
      }
      $param_string[] = ucfirst( $key ).' : '.$value;
    }
    $param_string = implode('<br/>', $param_string);
    echo '
  <tr>
    <td>'.$order_type->name .'</td>
    <td>'.guess_time( $player_order->datetime_order, GUESS_TIME_FR ) .'</td>
    <td>'.guess_time( $player_order->datetime_scheduled, GUESS_TIME_FR ) .'</td>
    <td>'.$param_string.'</td>
    <td>
      <form action="'.Page::get_page_url('order').'" method="post">
        '.HTMLHelper::genererInputHidden('url_return', Page::get_page_url( PAGE_CODE ) ).'
        '.HTMLHelper::genererInputHidden('id', $player_order->get_id() ).'
        <button type="submit" name="action" value="cancel">Cancel</button>
      </form>
    </td>
  </tr>';
  }
?>
</table>
<h4>New order</h4>
<?php
  foreach( Order_Type::db_get_all() as $order_type ) {
    $class = $order_type->class_name;
    
    require_once(DATA.'order_type/'.$order_type->class_name.'.class.php');
    
    echo $class::get_html_form( array('page_code' => PAGE_CODE, 'current_player' => $current_player ) );
  }
?>