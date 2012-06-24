<?php
  if( is_null( $game_id = getValue('id') ) ) {
?>
<ul>
<?php
  foreach( $game_list as $game ) {
    echo '
  <li><a href="'.Page::get_page_url(PAGE_CODE, false, array('id' => $game->id)).'">'.$game->name.'</a></li>';
  }
?>
</ul>
<?php
  }else {
    if( $game->has_ended() ) {
      $game_status = "Ended";
    }elseif( $game->started ) {
      $game_status = "Started";
    }else {
      $game_status = "Waiting players";
    }
?>
<h2>Compute orders</h2>
<h2>Game <?php echo $game->name?></h2>
<ul>
  <li>Name : <?php echo $game->name ?></li>
  <li>Turn : <?php echo $game->current_turn.'/'.$game->turn_limit ?></li>
  <li>Turn interval : <?php echo $game->turn_interval ?> seconds</li>
  <li>Status : <?php echo $game_status ?></li>
  <li>Created : <?php echo guess_time( $game->created, GUESS_TIME_FR ) ?></li>
<?php if( $game->started ) { ?>
  <li>Started : <?php echo guess_time( $game->started, GUESS_TIME_FR ) ?></li>
<?php } ?>
<?php if( $game->updated ) { ?>
  <li>Last turn : <?php echo guess_time( $game->updated, GUESS_TIME_FR ) ?></li>
  <li>Next turn : <?php echo guess_time( $game->updated + $game->turn_interval, GUESS_TIME_FR ) ?></li>
<?php } ?>
<?php if( $game->ended ) { ?>
  <li>Ended : <?php echo guess_time( $game->ended, GUESS_TIME_FR ) ?></li>
<?php } ?>
</ul>
<p><a href="<?php echo Page::get_page_url( PAGE_CODE, false, array('action' => 'reset', 'id' => $game->id ) )?>">Reset game</a></p>
<p><a href="<?php echo Page::get_page_url( PAGE_CODE, false, array('action' => 'start', 'id' => $game->id ) )?>">Start game</a></p>
<p><a href="<?php echo Page::get_page_url( PAGE_CODE, false, array('action' => 'compute', 'id' => $game->id ) )?>">Compute orders</a></p>
<table border="1">
  <tr>
    <th>Id</th>
    <th>Order Type</th>
    <th>Player</th>
    <th>Order</th>
    <th>Scheduled</th>
    <th>Parameters</th>
  </tr>
<?php
  foreach( $list as $player_order ) {
    $order_type = Order_Type::instance( $player_order->order_type_id );
    $player = Player::instance( $player_order->player_id );
    $parameters = unserialize( $player_order->parameters );
    echo '
  <tr>
    <td><a href="'.Page::get_page_url('admin_player_order', false, array('id' => $player_order->id )).'">'.$player_order->id .'</a></td>
    <td><a href="'.Page::get_page_url('admin_order_type', false, array('id' => $order_type->id )).'">'.$order_type->name .'</a></td>
    <td><a href="'.Page::get_page_url('admin_player', false, array('id' => $player->id )).'">'.$player->name .'</a></td>
    <td>'.guess_time( $player_order->datetime_order, GUESS_TIME_FR ) .'</td>
    <td>'.guess_time( $player_order->datetime_scheduled, GUESS_TIME_FR ) .'</td>
    <td>'.var_export($parameters, true).'</td>
  </tr>';
  }
?>
</table>
<table border="1">
  <tr>
    <th>Id</th>
    <th>Order Type</th>
    <th>Player</th>
    <th>Order</th>
    <th>Scheduled</th>
    <th>Parameters</th>
    <th>Return</th>
  </tr>
  <tr>
    <td>
      <table>
<?php
  $current_player_order_id = null;
  foreach( $player_order_log as $player_order ) {
    $player_order_id = $player_order['id'];
    
    if( $current_player_order_id != $player_order_id ) {
      $order_type = Order_Type::instance( $player_order['order_type_id'] );
      $player = Player::instance( $player_order['order_player_id'] );
      $parameters = unserialize( $player_order['parameters'] ); 
      echo '
      </table>
    </td>
  </tr>
  <tr>
    <td><a href="'.Page::get_page_url('admin_player_order', false, array('id' => $player_order_id )).'">'.$player_order_id .'</a></td>
    <td><a href="'.Page::get_page_url('admin_order_type', false, array('id' => $order_type->id )).'">'.$order_type->name .'</a></td>
    <td><a href="'.Page::get_page_url('admin_player', false, array('id' => $player->id )).'">'.$player->name .'</a></td>
    <td>'.guess_time( $player_order['datetime_execution'], GUESS_TIME_FR ) .'</td>
    <td>'.var_export($parameters, true).'</td>
  </tr>
  <tr>
    <td></td>
    <td colspan="4">
      <table>';
      
      $current_player_order_id = $player_order_id;
    }
    
    $player = Player::instance( $player_order['player_id'] );
    $resource = Resource::instance( $player_order['resource_id'] );
    
    echo '
        <tr>
          <td><a href="'.Page::get_page_url('admin_player', false, array('id' => $player->id )).'">'.$player->name .'</a></td>
          <td class="num">'. ($player_order['delta'] > 0?'+':'') . $player_order['delta'] .'</td>
          <td><a href="'.Page::get_page_url('admin_resource', false, array('id' => $resource->id )).'">'.$resource->name .'</a></td>
          <td>'.$player_order['reason'].'</td>
        </tr>';
  }
?>
      </table>
    </td>
  </tr>
</table>
<?php } ?>