<h2>Dashboard</h2>
<p>Welcome <?php echo $current_player->get_name()?> !</p>
<p><a href="<?php echo Page::get_page_url('player_list')?>">Player list</a></p>
<h3>Resources</h3>
<?php
  $sums = $current_player->get_resource_sum_list();
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
      <th>Date</th>
      <th>Event</th>
      <th>Changes</th>
      <th>Resource</th>
    </tr>
  </thead>
  <tbody>
<?php
  $history = $current_player->get_resource_history();
  foreach( $history as $event ) {
    $resource = Resource::instance($event['resource_id']);
    echo '
    <tr>
      <td class="date">'.guess_time( $event['datetime'], GUESS_DATE_FR ).'</td>
      <td>'.$event['reason'].'</td>
      <td class="num">'. ($event['delta'] > 0?'+':'') . $event['delta'] .'</td>
      <td>'. $resource->get_name() .'</td>
    </tr>';
  }
?>
</table>
<h3>Orders</h3>
<h4>Orders planned</h4>
<?php
  $orders = Player_Order::db_get_planned_by_player_id( $current_player->get_id() );
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