<?php
  $resource_list = Resource::db_get_all();

  /* @var $current_player Player */
  /* @var $current_game Game */
?>
<h2>Dashboard</h2>
<p>Welcome <?php echo $current_player->get_name()?> !</p>
<h3>Current Game</h3>
<ul>
  <li>Name : <a href="<?php echo Page::get_page_url('show_game', false, array('id' => $current_game->id))?>"><?php echo $current_game->name ?></a></li>
  <li>Turn : <?php echo $current_game->current_turn.'/'.$current_game->turn_limit ?></li>
  <li>Turn interval : <?php echo $current_game->turn_interval ?> seconds</li>
  <li>Status : <?php echo $current_game->status_string ?></li>
  <li>Created : <?php echo guess_time( $current_game->created, GUESS_TIME_LOCALE ) ?></li>
<?php if( $current_game->started ) { ?>
  <li>Started : <?php echo guess_time( $current_game->started, GUESS_TIME_LOCALE ) ?></li>
<?php } ?>
<?php if( $current_game->updated ) { ?>
  <li>Last turn : <?php echo guess_time( $current_game->updated, GUESS_TIME_LOCALE ) ?></li>
  <?php } ?>
<?php if( $current_game->ended ) { ?>
  <li>Ended : <?php echo guess_time( $current_game->ended, GUESS_TIME_LOCALE ) ?></li>
<?php }elseif( $current_game->updated ) { ?>
  <li>Next turn : <?php echo guess_time( $current_game->updated + $current_game->turn_interval, GUESS_TIME_LOCALE ) ?></li>
<?php }?>
</ul>
<?php if( $current_game->has_ended() ) {?>
<p>This game is over, check <a href="<?php echo Page::get_page_url('player_list', false, array('game_id' => $current_game->id))?>">the final scoreboard</a> !</p>
<?php } ?>
<?php
  // If game started
  if( $current_game->started ) {
?>
<p><a href="<?php echo Page::get_page_url('player_list')?>">Player list</a></p>
<h4>Wall</h4>
<form action="<?php echo Page::get_url('shout', array('game_id' => $current_game->id ))?>" method="post">
  <p><input type="text" name="text" value=""/><button type="submit" name="action" value="shout">Say</button></p>
</form>
<div id="shoutwall">
<?php
    $shouts = Shout::db_get_by_game_id( $current_game->id );
    foreach( array_reverse( $shouts ) as $shout ) {
      $player = Player::instance($shout->shouter_id);
      echo '
  <div class="shout"><strong>'.wash_utf8($player->name).'</strong>: '.wash_utf8($shout->text).'</div>';
    }
?>
</div>
<h3>Controlled territories</h3>
<?php
    $territory_owner_list = $current_player->get_territory_owner_list(null, $current_game->id, $current_game->current_turn );
?>
<table>
  <tr>
    <th>Territories</th>
    <th>Area</th>
    <th>Status</th>
  </tr>
<?php
    foreach( $territory_owner_list as $territory_owner_row ) {
      $territory = Territory::instance( $territory_owner_row['territory_id'] );
      echo '
  <tr>
    <td><a href="'.Page::get_url('show_territory', array('id' => $territory->id)).'">'.$territory->name.'</a></td>
    <td class="num">'.$territory->get_area().' km²</td>
    <td>'.($territory->is_contested($current_game->id, $current_game->current_turn)?'Contested':'Stable').'</td>
  </tr>';
    }
?>
</table>
<h3>Troops summary</h3>
<?php
    $player_territories = $current_player->get_territory_player_troops_list($current_game->id, $current_game->current_turn );
?>
<table>
  <tr>
    <th>Number</th>
    <th>Territory</th>
    <th>Owner</th>
    <th>Status</th>
  </tr>
<?php
    foreach( $player_territories as $player_territory ) {
      $territory = Territory::instance( $player_territory['territory_id'] );

      $owner_id = $territory->get_owner($current_game->id, $current_game->current_turn);
      $owner = Player::instance( $owner_id );
      echo '
  <tr>
    <td class="num">'.$player_territory['quantity'].'</td>
    <td><a href="'.Page::get_url('show_territory', array('id' => $territory->id)).'">'.$territory->name.'</a></td>
    <td>';

      if( $owner == $current_player ) {
        echo 'Yourself';
      }else {
        echo '<a href="'.Page::get_url('show_player', array('id' => $owner->id)).'">'.$owner->name.'</a>';
      }

      echo '
    </td>
    <td>'.($territory->is_contested($current_game->id, $current_game->current_turn)?'Contested':'Stable').'</td>
  </tr>';
    }
?>
</table>
<h3>Diplomacy</h3>
<?php
    $player_diplomacy_list = $current_player->get_last_player_diplomacy_list($current_game->id, $current_game->current_turn );
?>
<table>
  <tr>
    <th>Player</th>
    <th>Status</th>
    <th>Change</th>
  </tr>
<?php
    foreach( $player_diplomacy_list as $player_diplomacy ) {
      $player = Player::instance( $player_diplomacy['to_player_id'] );
      $new_status = $player_diplomacy['status'] == 'Enemy'?'Ally':'Enemy';
      echo '
  <tr>
    <td><a href="'.Page::get_url('show_player', array('id' => $player->id)).'">'.$player->name.'</a></td>
    <td>'.$player_diplomacy['status'].'</td>
    <td><a href="'.Page::get_url( PAGE_CODE, array('action' => 'change_diplomacy_status', 'to_player_id' => $player->id, 'new_status' => $new_status)).'">Change</a></td>
  </tr>';
    }
?>
</table>
<h3>Message history</h3>
<?php
    $player_history_list = $current_player->get_player_history_list($current_game->id);
?>
<table>
  <tr>
    <th>Turn</th>
    <th>Reason</th>
    <th>Territory</th>
  </tr>
<?php
    foreach( $player_history_list as $player_history_row ) {
      $territory = null;
      if( $player_history_row['territory_id'] ) {
        $territory = Territory::instance( $player_history_row['territory_id'] );
      }
      echo '
  <tr>
    <td>'.$player_history_row['turn'].'</td>
    <td>'.$player_history_row['reason'].'</td>
    <td>'.($territory?'<a href="'.Page::get_url('show_territory', array('id' => $territory->id)).'">'.$territory->name.'</a>':'').'</td>
  </tr>';
    }
?>
</table>

<?php
    // If game started and not ended
    if( ! $current_game->has_ended() ) {
?>
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
        $parameters = $player_order->parameters;
        $param_string = array();
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
    <td>'.guess_time( $player_order->datetime_order, GUESS_TIME_LOCALE ) .'</td>
    <td>'.guess_time( $player_order->datetime_scheduled, GUESS_TIME_LOCALE ) .'</td>
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
<?php
      $turn_ready = array_shift( $current_player->get_game_player_list( $current_game->id ) );
      if( $turn_ready['turn_ready'] <= $current_game->current_turn ) {
        echo '<p><a href="'.Page::get_url(PAGE_CODE, array('action' => 'ready')).'">I\'m ready for the next turn</a></p>';
      }else {
        echo '<p><a href="'.Page::get_url(PAGE_CODE, array('action' => 'ready')).'">I\'m not ready for the next turn yet</a></p>';
      }
    }
  }