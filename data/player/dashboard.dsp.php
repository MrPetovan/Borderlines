<?php
  $players = Player::db_get_by_member_id( $member->id );
  /* @var $current_player Player */
  /* @var $current_game Game */

  $game_parameters = $current_game->get_parameters();
?>
<h2><?php echo __('Dashboard')?></h2>
<p><?php echo __('Welcome %s !', $current_player->get_name())?></p>
<p>Player ID : <?php echo $current_player->id?></p>
<p>API Key : <?php echo $current_player->api_key?></p>
<p>API Signature : <?php echo sha1( $current_player->id . $current_player->api_key )?></p>
<?php if ( 1 == 2 ) {?>
<ul>
  <?php foreach( $players as $player ) {?>
  <li><a href="<?php echo Page::get_url(PAGE_CODE, array('player_id' => $player->id ))?>"><?php echo $player->name?></a></li>
  <?php } ?>
</ul>
<p><a href="<?php echo Page::get_url('create_player')?>">Create player</a></p>
<?php }?>
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
<p><?php echo __('This game is over, check <a href="%s">the final scoreboard</a> !', Page::get_url('show_game', array('id' => $current_game->id)))?></p>
<?php }else {
        $turn_ready = array_shift( $current_player->get_game_player_list( $current_game->id ) );
?>
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
<h3><?php echo __('Territory Summary')?></h3>
<?php
    $territory_summaries = array();
    $territory_summaries[ $current_game->current_turn - 1 ] = $current_player->get_territory_summary($current_game, $current_game->current_turn - 1);
    $territory_summaries[ $current_game->current_turn ] = $current_player->get_territory_summary($current_game, $current_game->current_turn);
?>
<table class="accordion">
<?php

    foreach( $territory_summaries as $turn => $territory_summary ) {
      $is_current = $turn == $current_game->current_turn;

      echo '
  <tbody class="archive'.($is_current?' current':'').'">
    <tr class="title">
      <th colspan="10">'.__('Turn %s', $turn).'</th>
    </tr>
    <tr>
      <th>'.__('Territory').'</th>
      <th>'.__('Owner').'</th>
      <th>'.__('Type').'</th>
      <th>'.__('Area').'</th>
      <th>'.__('Status').'</th>
      <th>'.__('Troops').'</th>
      <th>'.__('Economy').'</th>
      <th>'.__('Suppression').'</th>
      <th>'.__('Revenue').'</th>
    </tr>';

      $total_troops = 0;
      $total_territory = 0;
      $total_revenue = 0;
      foreach( $territory_summary as $territory_row ) {
        $territory = Territory::instance( $territory_row['territory_id'] );
        $owner = Player::instance( $territory_row['owner_id'] );
        $total_troops += $territory_row['quantity'];

        $territory_revenue = 0;
        if( $owner == $current_player ) {
          $territory_revenue =
            $game_parameters['TERRITORY_BASE_REVENUE']
            * ( $territory_row['economy_ratio'] )
            * ( 1 - $territory_row['revenue_suppression'] );
        }

        if( $owner == $current_player ) {
          $total_territory += $territory->area;
          $total_revenue += $territory_revenue;
        }

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
      <td>';
        if( $owner == $current_player ) {
          echo 'Yourself';
        }elseif( $owner->id ) {
          echo '<a href="'.Page::get_url('show_player', array('id' => $owner->id)).'">'.$owner->name.'</a>';
        }else {
          echo __('Nobody');
        }
        echo '
      </td>
      <td>'.($territory_row['capital']?'Capital':'Province').'</td>
      <td class="num">'.l10n_number( $territory->area ).' km²</td>
      <td>'.$status.'</td>
      <td class="num">'.l10n_number( $territory_row['quantity'] ) . icon('troops') . '</td>
      <td class="num">'.l10n_number( $territory_row['economy_ratio'] * 100 ).' %</td>
      <td class="num">'.l10n_number( $territory_row['revenue_suppression'] * 100 ).' %</td>
      <td class="num">'.l10n_number( $territory_revenue ) . icon('coins') . '</td>
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
      <td class="num"><?php echo l10n_number( $total_troops ) . icon('troops') ?></td>
      <th colspan="2"><?php echo __('Total')?></th>
      <td class="num"><?php echo l10n_number( $total_revenue ) . icon('coins')?></td>
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
<form action="<?php echo Page::get_url( PAGE_CODE )?>" method="POST">
  <table>
    <tr>
      <th><?php echo __('Player')?></th>
      <th colspan="3"><?php echo __('Status')?></th>
      <th><?php echo icon('vision_clear') . __('Shared vision')?></th>
    </tr>
<?php
    $diplo = array('Ally', 'Neutral', 'Enemy');
    foreach( $player_diplomacy_list as $player_diplomacy ) {
      $player = Player::instance( $player_diplomacy['to_player_id'] );
      $new_status = $player_diplomacy['status'] == 'Enemy'?'Ally':'Enemy';
      echo '
    <tr>
      <td><a href="'.Page::get_url('show_player', array('id' => $player->id)).'">'.$player->get_player_name_with_diplomacy($current_game, $current_game->current_turn, $current_player).'</a></td>';
      foreach( $diplo as $status ) {
        echo '
      <td>
        '.HTMLHelper::radio('status[' . $player->id . ']', $status, $player_diplomacy['status'], array('label_position' => 'right', 'id' => 'status_' . $player->id . '_' . $status), __($status) ).'
      </td>';
      }
      echo '
      <td>
      '.HTMLHelper::checkbox('shared_vision[' . $player->id . ']', 1, $player_diplomacy['shared_vision'], array('label_position' => 'right'), icon('vision_shared').__('Shared vision') ).'';
      echo '
      </td>
    </tr>';
    }
?>
  </table>
  <p>
    <?php echo HTMLHelper::button('action', 'change_diplomacy_status', array('type' => 'submit'), __('Update diplomatic status'))?>
  </p>
</form>
<h3><?php echo __('Economy')?></h3>
<?php
  $revenue_before_bureaucracy = $current_player->get_revenue( $current_game, $current_game->current_turn + 1 );

  $bureaucracy_ratio = $current_game->get_bureaucracy_ratio(count($current_player->get_territory_status_list(null, $current_game->id, $current_game->current_turn )));

  $revenue = $revenue_before_bureaucracy * $bureaucracy_ratio;

  $troops_home = 0;
  $troops_away = 0;
  $troops_list = $current_game->get_territory_player_troops_list($current_game->current_turn, null, $current_player->id);
  $territory_status_list = $current_game->get_territory_status_list(null, $current_game->current_turn, $current_player->id);
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
<div class="informations formulaire">
  <p>
    <span class="label"><?php echo __('Total revenue for turn %s', $current_game->current_turn)?></span>
    <span class="value num"><?php echo l10n_number( $revenue_before_bureaucracy, 0 ) . icon('coins')?></span>
  </p>
  <p>
    <span class="label"><?php echo __('Bureaucracy ratio for turn %s', $current_game->current_turn)?></span>
    <span class="value num"><?php echo l10n_number( round($bureaucracy_ratio * 100), 0 ) . '%' . icon('bureaucracy')?></span>
  </p>
  <p>
    <span class="label"><?php echo __('Revenue after bureaucracy')?></span>
    <span class="value num"><?php echo l10n_number( $revenue, 0 ) . icon('coins')?></span>
  </p>
  <p>
    <span class="label"><?php echo __('Troops home')?></span>
    <span class="value num">
      <?php echo l10n_number( $troops_home, 0 ) . icon('troops')?>@
      <?php echo l10n_number( $options['HOME_TROOPS_MAINTENANCE'], 0 ) . icon('coins')?>=
      <?php echo l10n_number( $troops_home * $options['HOME_TROOPS_MAINTENANCE'], 0 ) . icon('coins')?>
    </span>
  </p>
  <p>
    <span class="label"><?php echo __('Troops away')?></span>
    <span class="value num">
      <?php echo l10n_number( $troops_away, 0 ) . icon('troops')?>@
      <?php echo l10n_number( $options['AWAY_TROOPS_MAINTENANCE'], 0) . icon('coins')?>=
      <?php echo l10n_number( $troops_away * $options['AWAY_TROOPS_MAINTENANCE'], 0) . icon('coins')?>
    </span>
  </p>
  <p>
    <span class="label"><?php echo __('Total troops maintenance')?></span>
    <span class="value num">
      <?php echo l10n_number( $troops_maintenance, 0 ) . icon('coins')?>
    </span>
  </p>
  <p>
    <span class="label"><?php echo __('Recruiting budget')?></span>
    <span class="value num">
      <?php echo l10n_number( $recruit_budget, 0 ) . icon('coins')?>
    </span>
  </p>
  <p>
    <span class="label"><?php echo __('Total troops recruited')?></span>
    <span class="value num">
      <?php echo l10n_number( $troops_recruited, 0 ) . icon('troops')?>@
      <?php echo l10n_number( $options['RECRUIT_TROOPS_PRICE'], 0) . icon('coins')?>
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
      <td>'.($territory?'<a href="'.Page::get_url('show_territory', array('game_id' => $current_game->id, 'id' => $territory->id)).'">'.$territory->name.'</a>':'').'</td>
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
      'give_tribute',
      array('current_player' => $current_player)
    );
?>
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