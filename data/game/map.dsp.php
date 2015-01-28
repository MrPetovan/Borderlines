<?php
  $PAGE_TITRE = __('World : Showing "%s"', $world->name );

  $is_current_turn = $turn == $current_game->current_turn;

  /* @var $world World */
  /* @var $current_player Player */
  $territory_params = array('game_id' => $current_game->id, 'turn' => $turn);

  $game_parameters = $current_game->get_parameters();
?>
<ul class="nav nav-tabs">
  <li role="presentation" class="active">
    <a href="<?php echo Page::get_url(PAGE_CODE)?>"><?php echo icon('world', '') . __('World map')?></a>
  </li>
  <li role="presentation">
    <a href="<?php echo Page::get_url('game_diplomacy')?>"><?php echo icon('diplomacy', '') . __('Diplomacy')?></a>
  </li>
  <li role="presentation">
    <a href="<?php echo Page::get_url('game_economy')?>"><?php echo icon('coins', '') . __('Economy')?></a>
  </li>
</ul>
<nav>
  <ul class="pagination pagination-sm">
    <!--<li>
      <a href="#" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>-->
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
    <!--<li>
      <a href="#" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>-->
  </ul>
</nav>
<?php
  if( $is_current_turn ) {
    $turn_ready = array_shift( $current_player->get_game_player_list( $current_game->id ) );
    if( $turn_ready['turn_ready'] <= $current_game->current_turn ) {
      echo '<p>'.__('Status').' : <img src="'.IMG.'img_html/delete.png" alt="" /> '.__('Not ready for the next turn').' <a href="'.Page::get_url(PAGE_CODE, array('action' => 'ready')).'" class="btn btn-default">'.__('Toggle').'</a></p>';
    }else {
      echo '<p>'.__('Status').' : <img src="'.IMG.'img_html/accept.png" alt="" /> '.__('Ready for the next turn').' <a href="'.Page::get_url(PAGE_CODE, array('action' => 'notready')).'" class="btn btn-default">'.__('Toggle').'</a></p>';
    }
  }

  $return = $current_game->get_territory_troops_vision_list($current_player->id, $turn);

  $player_vision_troops_list = $return['troops_list'];
  $territory_status_list = $return['territory_status_list'];

  $ratio = 1140 / $world->size_x;
?>


<div id="world_map" style="position: relative; height: <?php echo $world->size_y * $ratio?>px">
<?php
  //$ratio = 1;
  echo $world->drawImg(array(
    'with_map' => true,
    'game_id' => $current_game->id,
    'turn' => $turn,
    'ratio' => $ratio,
    'no_names' => true,
    'force' => is_admin()
  ));
?>
<?php

  $orders = array();
  if( $is_current_turn ) {
    $orders = Player_Order::db_get_planned_by_player_id( $current_player->id, $current_game->id, $turn );
  }

  $troops_by_territory = array();
  foreach($player_vision_troops_list as $territory_id => $territory_troops_list) {
    foreach($territory_troops_list[$turn] as $territory_player_troops_row) {
      if( $territory_player_troops_row['player_id'] == $current_player->id ) {
        foreach($orders as $order) {
          $order_type = Order_Type::instance($order->order_type_id);

          if( $order_type->class_name == 'move_troops'
            && $order->parameters['from_territory_id'] == $territory_player_troops_row['territory_id'] ) {
            $territory_player_troops_row['quantity'] -= $order->parameters['count'];
          }
        }
      }

      if( $territory_player_troops_row['quantity'] > 0 ) {
        $troops_by_territory[$territory_player_troops_row['territory_id']][] = $territory_player_troops_row;
      }
    }
  }

  foreach($orders as $order) {
    $order_type = Order_Type::instance($order->order_type_id);

    if( $order_type->class_name == 'move_troops' ) {
      $territory_player_troops_row['territory_id'] = $order->parameters['from_territory_id'];
      $territory_player_troops_row['quantity'] = $order->parameters['count'];
      $territory_player_troops_row['player_id'] = $current_player->id;
      $territory_player_troops_row['game_id'] = $current_game->id;
      $territory_player_troops_row['turn'] = $order->turn_scheduled;
      $territory_player_troops_row['planned_order_id'] = $order->id;

      $troops_by_territory[$order->parameters['to_territory_id']][] = $territory_player_troops_row;
    }
  }


  foreach($territory_status_list as $territory_status_row) {
    $territory = Territory::instance($territory_status_row['territory_id']);

    if( $territory_status_row['conflict'] ) {
      $status = icon('territory_conflict');
    }elseif( $territory_status_row['contested'] ) {
      $status = icon('territory_contested');
    }else {
      $status = icon('territory_stable');
    }

    $status = '';

    if( $territory_status_row['can_see_troops'] ) {
      if( $territory_status_row['vision_is_direct'] ) {
        $vision = icon('vision_clear');
      }elseif($territory_status_row['vision_is_current']) {
        $vision = icon('vision_shared');
      }else {
        $vision = icon('vision_history');
      }
    }else {
      $vision = icon('vision_fogofwar');
    }

    $owner = Player::instance($territory_status_row['owner_id']);

    $capital = '';
    if( $territory_status_row['capital'] ) {
      $capital = icon('capital_territory');
    }

    $capital = '';

    $capture_troops = icon('capture_troops') . l10n_number( ceil( $territory->area / $game_parameters['TROOPS_CAPTURE_POWER'] ) );


    $move_classes = array('troops_list');
    if( $territory->is_passable() ) {
      $move_classes[] = 'receive_from_' . $territory->id;
      foreach($territory->get_territory_neighbour_list() as $territory_neighbour_row) {
        /* @var $neighbour Territory */
        $neighbour = Territory::instance($territory_neighbour_row['neighbour_id']);

        if( $neighbour->is_passable() ) {
          $move_classes[] = 'receive_from_' . $neighbour->id;
        }
      }
    }

    $centroid = $territory->get_centroid();
    echo '
      <div style="left:' . round($centroid->x * $ratio) .'px; top:' . round(($world->size_y - $centroid->y) * $ratio) . 'px;" class="territory_summary_wrapper">
        <div data-territory-id="' . $territory->id . '" title="' . $territory->name . ' (' . ($owner->id ? $owner->name : __('Nobody')) . ')" class="territory_summary">
          <h3 data-territory-id="' . $territory->id . '" title="' . $territory->name . ' (' . ($owner->id ? $owner->name : __('Nobody')) . ')">' . $status . $vision . $territory->name . $capital . $capture_troops . '</h3>
          <ul class="' . implode(' ', $move_classes) . '" data-territory-id="' . $territory->id . '" id="territory_' . $territory->id . '">';
    if( isset($troops_by_territory[$territory->id]) ) {
      foreach($troops_by_territory[$territory->id] as $territory_player_troops_row ) {
        /* @var $player Player */
        $player = Player::instance($territory_player_troops_row['player_id']);
        $troops_territory = Territory::instance($territory_player_troops_row['territory_id']);
        $classes = array('text-right');
        if( $player->id == $current_player->id && $is_current_turn ) {
          $classes[] = 'moveable';
        }
        if( $territory->id != $troops_territory->id ) {
          $classes[] = 'moved';
        }

        echo '
            <li
              ' . (count($classes) > 0 ? ' class="' . implode(' ', $classes) . '"' : '') . '
              data-player-id="' . $player->id . '"
              data-quantity="' . $territory_player_troops_row['quantity'] . '"
              data-from-territory-id="' . $troops_territory->id . '"
              data-planned-order-id="' . ( isset($territory_player_troops_row['planned_order_id']) ? $territory_player_troops_row['planned_order_id'] : '' ) . '"
            >
                ' . $player->name . ': <span class="value">' . l10n_number( $territory_player_troops_row['quantity'], 0 ) . '</span>' .
                icon('troops') .
                '
                  <button class="btn btn-xs split"><span class="glyphicon glyphicon-scissors" title="' . __('Split Troops') . '"></span></button>
                  <button class="btn btn-xs cancel"><span class="glyphicon glyphicon-remove" title="' . __('Cancel Move') . '"></span></button>
            </li>';
      }
    }
      echo '
          </ul>
        </div>
      </div>';
  }

?>
</div>
<?php if( $is_current_turn ) :?>
<h4><?php echo __('Orders planned')?></h4>
<?php
?>
<table class="table table-hover table-condensed">
  <thead>
    <tr>
      <th><?php echo __('Order Type')?></th>
      <th><?php echo __('Ordered')?></th>
      <th><?php echo __('Scheduled')?></th>
      <th><?php echo __('Parameters')?></th>
      <th><?php echo __('Action')?></th>
    </tr>
  </thead>
  <tbody>
<?php
  if( count($orders) ) {
    foreach( $orders as $player_order ) {
      $order_type = Order_Type::instance( $player_order->order_type_id );
      $parameters = $player_order->parameters;
      $param_string = array();
      foreach( $parameters as $key => $value ) {
        if( strpos($key, 'player_id') !== false ) {
          $player = Player::instance( $value );
          $value = '<a href="'.Page::get_url('show_player', array('id' => $player->id)).'">'.$player->name.'</a>';
        }
        if( strpos($key, 'territory_id') !== false ) {
          $territory = Territory::instance( $value );
          $value = '<a href="'.Page::get_url('show_territory', array('game_id' => $current_game->id, 'id' => $territory->id)).'">'.$territory->name.'</a>';
        }
        $param_string[] = ucwords( str_replace( array('_id','_'), array('', ' '), $key ) ).' : '.$value;
      }
      $param_string = implode('<br/>', $param_string);
      echo '
  <tr>
    <td>'.__( $order_type->name ).'</td>
    <td>'.$player_order->turn_ordered.'</td>
    <td>'.$player_order->turn_scheduled.'</td>
    <td>'.$param_string.'</td>
    <td>
      <form action="'.Page::get_page_url('order').'" method="post">
        '.HTMLHelper::genererInputHidden('url_return', Page::get_page_url( PAGE_CODE ) ).'
        '.HTMLHelper::genererInputHidden('id', $player_order->get_id() ).'
        <button type="submit" name="action" value="cancel">'.__('Cancel').'</button>
      </form>
    </td>
  </tr>';
    }
  }else {
    echo '<td colspan="5">' . __('No orders planned yet') . '</td>';
  }
?>
  </tbody>
</table>
<?php endif; //if( $is_current_turn )?>
<div class="modal fade" id="troops-modal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo __('Split troops')?></h4>
      </div>
      <div class="modal-body">
        <form class="form-inline">
          <p class="clearfix text-center">
            <span class="troops pull-left"><input type="text" disabled class="value text-right disabled form-control"/><?php echo icon('troops')?></span>
            <->
            <span class="troops pull-right"><input type="text" class="value text-right form-control"/><?php echo icon('troops')?></span>
          </p>
        </form>
        <div class="form-group">
          <div id="slider-troops"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->