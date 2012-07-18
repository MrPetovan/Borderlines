<h2>Player list</h2>

<?php
  $resource_list = Resource::db_get_all();

  if( $current_game->has_ended() ) {
    $player_list = Player::db_get_leaderboard_list( $current_game->id );
?>
<h3>Final results for game <?php echo $current_game->name ?></h3>
<table>
  <tr>
    <th>Rank</th>
    <th>Player</th>
<?php
  foreach( $resource_list as $resource ) {
    echo '
    <th>'. $resource->get_name() .'</th>';
  }
?>
    <th>Action</th>
  </tr>
<?php
  $i = 1;
  foreach( $player_list as $key => $player ) {
    echo '
  <tr>
    <td>#'.($i++).'</td>
    <td><a href="'.Page::get_page_url('show_player', false, array('id' => $player->get_id())).'">'.$player->get_name().'</a></td>';
    foreach( $resource_list as $resource ) {
      $value = $player->get_resource_sum( $resource->get_id(), null, $current_game->id );
      echo '
    <td class="num">'.(is_null( $value )?'N/C':$value).'</li>';
    }
    echo '
    <td><a href="'.Page::get_page_url('show_player', false, array('id' => $player->get_id())).'">Details</a></td>
  </tr>';
  }
?>
</table>
<?php
  }else {
    $game_player_list = $current_game->get_game_player_list( );
?>
<table>
  <tr>
    <th>Player</th>
<?php
  foreach( $resource_list as $resource ) {
    echo '
    <th>'. $resource->get_name() .'</th>';
  }
?>
    <th>Action</th>
  </tr>
<?php
  foreach( $game_player_list as $game_player ) {
    $player = Player::instance( $game_player['player_id'] );

    if( $player == $current_player ) {
      echo '
  <tr>
    <td><a href="'.Page::get_page_url('dashboard').'">'.$player->get_name().'</a></td>';
      foreach( $resource_list as $resource ) {
        $value = $player->get_resource_sum( $resource->get_id() );
          echo '
    <td class="num">'.(is_null( $value )?'N/C':$value).'</li>';
      }
      echo '
    <td><a href="'.Page::get_page_url('dashboard').'">Details</a></td>
  </tr>';
    }else {
      echo '
  <tr>
    <td><a href="'.Page::get_page_url('show_player', false, array('id' => $player->get_id())).'">'.$player->get_name().'</a></td>';
      foreach( $resource_list as $resource ) {
        if( $resource->is_public() ) {
          $value = $player->get_resource_sum( $resource->get_id() );
          echo '
    <td class="num">'.(is_null( $value )?'N/C':$value).'</li>';
        }else {
          $spied = $current_player->get_spied_value(
            'player'.$player->get_id().'-resource'.$resource->get_id(),
            $player,
            $player->get_resource_sum( $resource->get_id() )
          );

          $value = $spied['masked_value'];
          
          echo '
    <td class="num">'.(is_null( $value )?'N/C':$value).'</td>';
        }
      }
        echo '
    <td><a href="'.Page::get_page_url('show_player', false, array('id' => $player->get_id())).'">Details</a></td>
  </tr>';
    }
  }
?>
</table>
<?php
  }
?>