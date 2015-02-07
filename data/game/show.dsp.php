<?php
  $PAGE_TITRE = __('Game : Showing "%s"', $game->name);

  $creator = Player::instance( $game->created_by );
?>
<ul class="nav nav-tabs">
  <li role="presentation" class="inactive">
    <a><?php echo $game->name?></a>
  </li>
  <li role="presentation">
    <a href="<?php echo Page::get_url('game_map', array('game_id' => $game->id))?>"><?php echo icon('world', '') . __('World map')?></a>
  </li>
<?php if( $is_player_active ):?>
  <li role="presentation">
    <a href="<?php echo Page::get_url('game_diplomacy')?>"><?php echo icon('diplomacy', '') . __('Diplomacy')?></a>
  </li>
  <li role="presentation">
    <a href="<?php echo Page::get_url('game_economy')?>"><?php echo icon('coins', '') . __('Economy')?></a>
  </li>
<?php endif;?>
  <li role="presentation" class="active">
    <a href="<?php echo Page::get_url(PAGE_CODE, array('id' => $game->id))?>"><?php echo icon('information', '') . __('Game Info')?></a>
  </li>
</ul>
<?php
  $ratio = 1140 / $world->size_x;
  $options = array(
    'with_map' => false,
    'game_id' => $game->id,
    'turn' => $game->current_turn,
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
  <div class="informations formulaire">
    <div class="field form-group">
      <span class="label"><?php echo __('Status')?></span>
      <span class="value"><?php echo __($game->status_string)?></span>
    </div>
    <div class="field form-group">
      <span class="label"><?php echo __('Current Turn')?></span>
      <span class="value"><?php echo $game->current_turn.'/'.$game->turn_limit?></span>
    </div>
    <div class="field form-group">
      <span class="label"><?php echo __('Turn Interval')?></span>
      <span class="value"><?php echo __('%s seconds', $game->turn_interval)?></span>
    </div>
<?php if( !$game->started && $game->min_players ) { ?>
    <div class="field form-group">
      <span class="label"><?php echo __('Min Players')?></span>
      <span class="value"><?php echo $game->min_players?></span>
    </div>
<?php }?>
<?php if( $game->max_players ) {?>
    <div class="field form-group">
      <span class="label"><?php echo __('Max Players')?></span>
      <span class="value"><?php echo $game->max_players?></span>
    </div>
<?php }?>
    <div class="field form-group">
      <span class="label"><?php echo __('Created')?></span>
      <span class="value"><?php echo guess_time($game->created, GUESS_DATETIME_LOCALE)?>
      by <a href="<?php echo get_page_url('show_player', true, array('id' => $game->created_by ) )?>"><?php echo $creator->name?></a></span>
    </div>
<?php if( $game->started ) {?>
    <div class="field form-group">
      <span class="label"><?php echo __('Started')?></span>
      <span class="value"><?php echo guess_time($game->started, GUESS_DATETIME_LOCALE)?></span>
    </div>
<?php }?>
<?php if( $game->updated && ! $game->ended ) {?>
    <div class="field form-group">
      <span class="label"><?php echo __('Updated')?></span>
      <span class="value"><?php echo guess_time($game->updated, GUESS_DATETIME_LOCALE)?></span>
    </div>
<?php }?>
<?php if( $game->ended ) {?>
    <div class="field form-group">
      <span class="label"><?php echo __('Ended')?></span>
      <span class="value"><?php echo guess_time($game->ended, GUESS_DATETIME_LOCALE)?></span>
    </div>
<?php }elseif( $game->updated ) { ?>
    <div class="field form-group">
      <span class="label"><?php echo __('Next turn')?></span>
      <span class="value"><?php echo guess_time( $game->updated + $game->turn_interval, GUESS_DATETIME_LOCALE ) ?></span>
    </div>
<?php }?>
    <div class="field form-group">
      <span class="label"><?php echo __('World')?></span>
      <span class="value">
        <a href="<?php echo Page::get_url('show_world', array('game_id' => $game->id))?>"><?php echo $world->name?></a>
      </span>
    </div>
  </div>
<?php  endforeach;?>
<h3><?php echo __('Bureaucracy table')?></h3>
<table>
  <tr>
    <th><?php echo __('Territories')?></th>
  <?php for($i = 1; $i < $game->get_average_territories_by_player(); $i++ ):?>
    <th class="num"><?php echo $i?></th>
  <?php endfor;?>
    <th class="num"><?php echo $game->get_average_territories_by_player()?>+</th>
  </tr>
  <tr>
    <th><?php echo __('Revenue ratio')?></th>
  <?php for($i = 1; $i <= $game->get_average_territories_by_player(); $i++ ):?>
    <td class="num"><?php echo round($game->get_bureaucracy_ratio($i) * 100)?>%</td>
  <?php endfor;?>
  </tr>
</table>
</div>
<h3><?php echo __('Players')?></h3>
<?php
    if(count($game_player_list)) {
?>
  <table class="table table-condensed table-hover">
    <thead>
      <tr>
        <th class="num"><?php echo __('#')?></th>
        <th><?php echo __('Player')?></th>
        <th class="num"><?php echo __('Turn Active')?></th>
        <th class="num"><?php echo __('Controlled territory')?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="5"><?php echo $game->max_players?__('%s/%s players', count( $game_player_list ), $game->max_players):__('%s players', count( $game_player_list ))?></td>
      </tr>
    </tfoot>
    <tbody>
<?php
      foreach( $game_player_list as $key => $game_player_row ) {
        $player_id_player = Player::instance( $game_player_row['player_id'] );
        echo '
      <tr>
        <td class="num">'.($key + 1).'</td>
        <td><a href="'.get_page_url('show_player', true, array('id' => $player_id_player->id)).'">'.$player_id_player->name.'</a></td>';
        if( $game_player_row['turn_leave'] ) {
          echo '
        <td colspan="2">'.__('Left the game on turn %s', $game_player_row['turn_leave']).'</td>';
        }else {
          if( $game_player_row['turn_ready'] == $game->current_turn + 1 ) {
            $ready_string = '<span class="label label-success"><span class="glyphicon glyphicon-ok"></span> ' . __('Ready for next turn') . '</span>';
          }elseif( $game_player_row['turn_ready'] == $game->current_turn ) {
            $ready_string = '<span class="label label-info"><span class="glyphicon glyphicon-hourglass"></span> ' . __('Last turn') . '</span>';
          }else {
            $ready_string = '<span class="label label-default"><span class="glyphicon glyphicon-question-sign"></span> ' . __('Turn %s', $game_player_row['turn_ready']) . '</span>';
          }


          echo '
        <td class="num">' . $ready_string . '</td>
        <td class="num">' . l10n_number( $player_area[ 'player_' . $game_player_row['player_id'] ] ) . ' km²</td>';
        }
        echo '
      </tr>';
      }
?>
    </tbody>
  </table>
<?php
    }else {
      echo '
  <p>'.__('No player yet').'</p>';
    }

    $is_in_a_game = $current_player->get_current_game() != false;
    $is_playing_in = $is_in_a_game || $game->get_game_player_list( $current_player->id );
    if( !$game->started && !$is_playing_in ) {
      echo '
  <p><a href="'.Page::get_page_url(PAGE_CODE, false, array('action' => 'join', 'id' => $game->id)).'">'.__('Join this game').'</a></p>';
    }

    if( is_admin() ) {?>
  <p><a href="<?php echo Page::get_url( PAGE_CODE, array('action' => 'revert', 'id' => $game->id, 'turn' => $game->current_turn - 1 ) )?>" class="btn btn-danger">Revert to previous turn</a></p>
  <p><a href="<?php echo Page::get_url( PAGE_CODE, array('action' => 'reset', 'id' => $game->id ) )?>" class="btn btn-danger">Reset game</a></p>
  <p><a href="<?php echo Page::get_url( PAGE_CODE, array('action' => 'start', 'id' => $game->id ) )?>" class="btn btn-danger">Start game</a></p>
  <p><a href="<?php echo Page::get_url( PAGE_CODE, array('action' => 'compute', 'id' => $game->id ) )?>" class="btn btn-danger">Compute orders</a></p>
  <p><a href="<?php echo Page::get_url('admin_game_view', array('id' => $game->id ))?>" class="btn btn-default"><?php echo __('Manage game')?></a></p>
<?php  }?>
  <p><a href="<?php echo get_page_url('game_list')?>"><?php echo __('Return to game list')?></a></p>
</div>