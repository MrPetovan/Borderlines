<?php
  $resource_list = Resource::db_get_all();

  /* @var $current_player Player */
  /* @var $current_game Game */
?>
<h2><?php echo __('Dashboard')?></h2>
<p><?php echo __('Welcome %s !', $current_player->get_name())?></p>
<div class="informations formulaire">
  <p>
    <span class="label"><?php echo __('Current game')?></span>
    <span class="value"><a href="<?php echo Page::get_page_url('show_game', false, array('id' => $current_game->id))?>"><?php echo $current_game->name ?></a></span>
  </p>
  <p>
    <span class="label"><?php echo __('Turn')?></span>
    <span class="value num"><?php echo $current_game->current_turn.'/'.$current_game->turn_limit ?></span>
  </p>
  <p>
    <span class="label"><?php echo __('Turn interval')?></span>
    <span class="value num"><?php echo __('%s seconds', $current_game->turn_interval)?></span>
  </p>
  <p>
    <span class="label"><?php echo __('Status')?></span>
    <span class="value"><?php echo __($current_game->status_string)?></span>
  </p>
  <p>
    <span class="label"><?php echo __('Created')?></span>
    <span class="value"><?php echo guess_time( $current_game->created, GUESS_DATETIME_LOCALE )?></span>
  </p>
<?php if( $current_game->started ) { ?>
  <p>
    <span class="label"><?php echo __('Started')?></span>
    <span class="value"><?php echo guess_time( $current_game->started, GUESS_DATETIME_LOCALE )?></span>
  </p>
<?php } ?>
<?php if( $current_game->updated ) { ?>
  <p>
    <span class="label"><?php echo __('Last turn')?></span>
    <span class="value"><?php echo guess_time( $current_game->updated, GUESS_DATETIME_LOCALE )?></span>
  </p>
<?php } ?>
<?php if( $current_game->ended ) { ?>
  <p>
    <span class="label"><?php echo __('Ended')?></span>
    <span class="value"><?php echo guess_time( $current_game->ended, GUESS_DATETIME_LOCALE )?></span>
  </p>
<?php }elseif( $current_game->updated ) { ?>
  <p>
    <span class="label"><?php echo __('Next automatic turn')?></span>
    <span class="value"><?php echo guess_time( $current_game->updated + $current_game->turn_interval, GUESS_DATETIME_LOCALE )?></span>
  </p>
<?php } ?>
<?php if( $current_game->has_ended() ) {?>
<p>This game is over, check <a href="<?php echo Page::get_page_url('player_list', false, array('game_id' => $current_game->id))?>">the final scoreboard</a> !</p>
<?php }else {
        $turn_ready = array_shift( $current_player->get_game_player_list( $current_game->id ) );?>
  <p>
    <span class="label"><?php echo __('Your turn status')?></span>
    <span class="value">
  <?php
  if( $turn_ready['turn_ready'] <= $current_game->current_turn ) {
        echo '<img src="'.IMG.'img_html/delete.png" alt="" /> '.__('Not ready for the next turn').' <a href="'.Page::get_url(PAGE_CODE, array('action' => 'ready')).'">'.__('Toggle').'</a></p>';
      }else {
        echo '<img src="'.IMG.'img_html/accept.png" alt="" /> '.__('Ready for the next turn').' <a href="'.Page::get_url(PAGE_CODE, array('action' => 'notready')).'">'.__('Toggle').'</a></p>';
      } ?>
    </span>
  </p>
<p><a href="<?php echo Page::get_url('game_over', array('id' => $current_game->id))?>"><img src="<?php echo IMG.'img_html/door_open.png'?>" alt="" /> <?php echo __('Quit game') ?></a></p>

<?php } ?>
</div>
<?php
  // If game started
  if( $current_game->started ) {
?>

<p>
  <a href="<?php echo Page::get_url('show_world', array('game_id' => $current_game->id))?>">
    <img src="<?php echo IMG.'img_html/world.png'?>" alt=""/>
    <?php echo __('Go to the world map')?>
  </a>
</p>
<h4><?php echo __('Wall')?></h4>
<form action="<?php echo Page::get_url('shout', array('game_id' => $current_game->id ))?>" method="post">
  <p><?php echo '['.guess_time(time(), GUESS_TIME_LOCALE).']'?> <strong><?php echo wash_utf8($current_player->name)?></strong> : <input type="text" name="text" size="80" value=""/><button type="submit" name="action" value="shout">Say</button></p>
</form>
<div id="shoutwall">
<?php
    $shouts = Shout::db_get_by_game_id( $current_game->id );
    foreach( array_reverse( $shouts ) as $shout ) {
      $player = Player::instance($shout->shouter_id);
      echo '
  <div class="shout">['.guess_time($shout->date_sent, GUESS_TIME_LOCALE).'] <strong>'.wash_utf8($player->name).'</strong>: '.wash_utf8($shout->text).'</div>';
    }
?>
</div>
<h3>Territory Summary</h3>
<?php
    $territory_summaries = array();
    $territory_summaries[ $current_game->current_turn - 1 ] = $current_player->get_territory_summary($current_game->id, $current_game->current_turn - 1);
    $territory_summaries[ $current_game->current_turn ] = $current_player->get_territory_summary($current_game->id, $current_game->current_turn);
?>
<table class="accordion">
<?php

    foreach( $territory_summaries as $turn => $territory_summary ) {
      $is_current = $turn == $current_game->current_turn;

      echo '
  <tbody class="archive'.($is_current?' current':'').'">
    <tr class="title">
      <th colspan="6">'.__('Turn %s', $turn).'</th>
    </tr>
    <tr>
      <th>'.__('Territory').'</th>
      <th>'.__('Owner').'</th>
      <th>'.__('Type').'</th>
      <th>'.__('Area').'</th>
      <th>'.__('Status').'</th>
      <th>'.__('Troops').'</th>
    </tr>';

      $total_troops = 0;
      $total_territory = 0;
      $total_contested = 0;
      foreach( $territory_summary as $territory_row ) {
        $territory = Territory::instance( $territory_row['territory_id'] );
        $owner = Player::instance( $territory_row['owner_id'] );
        $total_troops += $territory_row['quantity'];
        if( $owner == $current_player ) {
          if( $territory_row['contested'] ) {
            $total_contested += $territory->area;
          }
          $total_territory += $territory->area;
        }
        echo '
    <tr>
      <td><a href="'.Page::get_url('show_territory', array('id' => $territory->id)).'">'.$territory->name.'</a></td>
      <td>';
        if( $owner == $current_player ) {
          echo 'Yourself';
        }else {
          echo '<a href="'.Page::get_url('show_player', array('id' => $owner->id)).'">'.$owner->name.'</a>';
        }
        echo '
      </td>
      <td>'.($territory_row['capital']?'Capital':'Province').'</td>
      <td class="num">'.l10n_number( $territory->area ).' km²</td>
      <td>'.($territory_row['contested']?'<img src="'.IMG.'img_html/bomb.png" alt=""/> '.__('Contested'):'<img src="'.IMG.'img_html/accept.png" alt=""/> '.__('Stable')).'</td>
      <td class="num">'.l10n_number( $territory_row['quantity'] ).' <img src="'.IMG.'img_html/helmet.png" alt="'.__('Troops').'" title="'.__('Troops').'"/></td>
    </tr>';
      }
      echo '
  </tbody>';
    }
?>
  <tbody>
    <tr>
      <th colspan="2"></th>
      <th><?php echo __('Total')?></th>
      <td class="num"><?php echo l10n_number( $total_territory )?> km²</td>
      <th><?php echo __('Total')?></th>
      <td class="num"><?php echo l10n_number( $total_troops ).' <img src="'.IMG.'img_html/helmet.png" alt="'.__('Troops').'" title="'.__('Troops').'"/>' ?></td>
    </tr>
  </tbody>
</table>
<p>
  <a href="<?php echo Page::get_url('show_world', array('game_id' => $current_game->id))?>">
    <img src="<?php echo IMG.'img_html/world.png'?>" alt=""/>
    <?php echo __('Go to the world map')?>
  </a>
</p>
<h3><?php echo __('Diplomacy')?></h3>
<?php
    $player_diplomacy_list = $current_player->get_last_player_diplomacy_list($current_game->id, $current_game->current_turn );
?>
<table>
  <tr>
    <th><?php echo __('Player')?></th>
    <th><?php echo __('Status')?></th>
    <th><?php echo __('Change')?></th>
  </tr>
<?php
    foreach( $player_diplomacy_list as $player_diplomacy ) {
      $player = Player::instance( $player_diplomacy['to_player_id'] );
      $new_status = $player_diplomacy['status'] == 'Enemy'?'Ally':'Enemy';
      echo '
  <tr>
    <td><a href="'.Page::get_url('show_player', array('id' => $player->id)).'">'.$player->name.'</a></td>
    <td>'.__($player_diplomacy['status']).'</td>
    <td><a href="'.Page::get_url( PAGE_CODE, array('action' => 'change_diplomacy_status', 'to_player_id' => $player->id, 'new_status' => $new_status)).'">'.__('Change').'</a></td>
  </tr>';
    }
?>
</table>
<h3><?php echo __('Economy')?></h3>
<?php
  $capital_id = null;
  $area = 0;
  $previous_turn = $current_game->current_turn;
  $territory_previous_owner_list = $current_player->get_territory_owner_list(null, $current_game->id, $previous_turn);
  foreach( $territory_previous_owner_list as $territory_owner_row ) {

    if( !$territory_owner_row['contested'] ) {
      $territory = Territory::instance($territory_owner_row['territory_id']);
      $area += $territory->get_area();
    }
    if( $territory_owner_row['capital'] ) {
      $capital_id = $territory_owner_row['territory_id'];
    }
  }

  $ratio = -$area * 0.00002 + 3;
  if( $ratio < 1 ) $ratio = 1;

  $revenue = round( $area * $ratio );

  $troops_home = 0;
  $troops_away = 0;
  $troops_list = $current_player->get_territory_player_troops_list($current_game->id, $previous_turn);
  foreach( $troops_list as $territory_player_troops_row ) {
    $is_home = false;

    foreach( $territory_previous_owner_list as $territory_previous_owner_row ) {
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

  $troops_maintenance = $troops_home * Game::HOME_TROOPS_MAINTENANCE + $troops_away * Game::AWAY_TROOPS_MAINTENANCE;

  $recruit_budget = $revenue - $troops_maintenance;

  // Is there a capital (after move) ?
  $capital_id = null;
  $territory_current_owner_list = $current_player->get_territory_owner_list(null, $current_game->id, $previous_turn);
  foreach( $territory_current_owner_list as $territory_owner_row ) {
    if( $territory_owner_row['capital'] ) {
      $capital_id = $territory_owner_row['territory_id'];
      break;
    }
  }
  $troops_recruited = 0;
  if( $capital_id !== null ) {
    $troops_recruited = floor( $recruit_budget / Game::RECRUIT_TROOPS_PRICE );
  }
?>
<div class="informations formulaire">
  <p>
    <span class="label"><?php echo __('Total area of stable territory on turn %s', $previous_turn)?></span>
    <span class="value num"><?php echo l10n_number( round($area), 0 )?> km²</span>
  </p>
  <p>
    <span class="label"><?php echo __('Economy ratio')?></span>
    <span class="value num"><?php echo l10n_number( $ratio, 2 )?></span>
  </p>
  <p>
    <span class="label"><?php echo __('Total revenue for turn %s', $previous_turn)?></span>
    <span class="value num"><?php echo l10n_number( $revenue, 0 )?>  <img src="<?php echo IMG.'img_html/coins.png'?>" alt="" title="" /></span>
  </p>
  <p>
    <span class="label"><?php echo __('Troops home')?></span>
    <span class="value num">
      <?php echo l10n_number( $troops_home, 0 )?> <img src="<?php echo IMG.'img_html/helmet.png'?>" alt="Troops" title="Troops" />
      @ <?php echo l10n_number( Game::HOME_TROOPS_MAINTENANCE, 0 )?> <img src="<?php echo IMG.'img_html/coins.png'?>" alt="" title="" />
      = <?php echo l10n_number( $troops_home * Game::HOME_TROOPS_MAINTENANCE, 0 )?> <img src="<?php echo IMG.'img_html/coins.png'?>" alt="" title="" />
    </span>
  </p>
  <p>
    <span class="label"><?php echo __('Troops away')?></span>
    <span class="value num">
      <?php echo l10n_number( $troops_away, 0 )?> <img src="<?php echo IMG.'img_html/helmet.png'?>" alt="Troops" title="Troops" />
      @ <?php echo l10n_number( Game::AWAY_TROOPS_MAINTENANCE, 0)?> <img src="<?php echo IMG.'img_html/coins.png'?>" alt="" title="" />
      = <?php echo l10n_number( $troops_away * Game::AWAY_TROOPS_MAINTENANCE, 0)?> <img src="<?php echo IMG.'img_html/coins.png'?>" alt="" title="" />
    </span>
  </p>
  <p>
    <span class="label"><?php echo __('Total troops maintenance')?></span>
    <span class="value num">
      <?php echo l10n_number( $troops_maintenance, 0 )?> <img src="<?php echo IMG.'img_html/coins.png'?>" alt="" title="" />
    </span>
  </p>
  <p>
    <span class="label"><?php echo __('Recruiting budget')?></span>
    <span class="value num">
      <?php echo l10n_number( $recruit_budget, 0 )?> <img src="<?php echo IMG.'img_html/coins.png'?>" alt="" title="" />
    </span>
  </p>
  <p>
    <span class="label"><?php echo __('Total troops recruited')?></span>
    <span class="value num">
      <?php echo l10n_number( $troops_recruited, 0 )?> <img src="<?php echo IMG.'img_html/helmet.png'?>" alt="Troops" title="Troops" />
      @ <?php echo l10n_number( Game::RECRUIT_TROOPS_PRICE, 0)?> <img src="<?php echo IMG.'img_html/coins.png'?>" alt="" title="" />
    </span>
  </p>
</div>
<h3><?php echo __('Message history')?></h3>
<?php
    $player_history_list = $current_player->get_player_history_list($current_game->id);
?>
<table class="accordion">
<?php
    $current_turn = null;
    foreach( $player_history_list as $player_history_row ) {
      if( $current_turn != $player_history_row['turn']) {
        $is_current = $player_history_row['turn'] == $current_game->current_turn;

        if( $current_turn !== null ) {
          echo '
  </tbody>';
        }
        echo '
  <tbody class="archive'.__($is_current?' current':'').'">
    <tr class="title">
      <th colspan="2">'.__('Turn %s', $player_history_row['turn']).'</th>
    </tr>
    <tr>
      <th>'.__('Territory').'</th>
      <th>'.__('Reason').'</th>
    </tr>';

        $current_turn = $player_history_row['turn'];
      }
      $territory = null;
      if( $player_history_row['territory_id'] ) {
        $territory = Territory::instance( $player_history_row['territory_id'] );
      }
      echo '
    <tr>
      <td>'.($territory?'<a href="'.Page::get_url('show_territory', array('id' => $territory->id)).'">'.$territory->name.'</a>':'').'</td>
      <td>'.$player_history_row['reason'].'</td>
    </tr>';
    }
?>
  </tbody>
</table>

<?php
    // If game started and not ended
    if( ! $current_game->has_ended() ) {
?>
<h3><?php echo __('Orders')?></h3>
<h4><?php echo __('Orders planned')?></h4>
<?php
      $orders = Player_Order::db_get_planned_by_player_id( $current_player->id, $current_game->id );
?>
<table>
  <tr>
    <th><?php echo __('Order Type')?></th>
    <th><?php echo __('Ordered')?></th>
    <th><?php echo __('Scheduled')?></th>
    <th><?php echo __('Parameters')?></th>
    <th><?php echo __('Action')?></th>
  </tr>
<?php
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
            $value = '<a href="'.Page::get_url('show_territory', array('id' => $territory->id)).'">'.$territory->name.'</a>';
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
?>
</table>
<?php
      if( $turn_ready['turn_ready'] <= $current_game->current_turn ) {
        echo '<p>'.__('Status').' : <img src="'.IMG.'img_html/delete.png" alt="" /> '.__('Not ready for the next turn').' <a href="'.Page::get_url(PAGE_CODE, array('action' => 'ready')).'">'.__('Toggle').'</a></p>';
      }else {
        echo '<p>'.__('Status').' : <img src="'.IMG.'img_html/accept.png" alt="" /> '.__('Ready for the next turn').' <a href="'.Page::get_url(PAGE_CODE, array('action' => 'notready')).'">'.__('Toggle').'</a></p>';
      }
    }
  }