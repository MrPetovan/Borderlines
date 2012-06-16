<h2>Player list</h2>
<?php
  $player_list = Player::db_get_all();
  $resource_list = Resource::db_get_all();
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
  foreach( $player_list as $player ) {
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
    <td><a href="'.Page::get_page_url('show_player', false, array('id' => $player->get_id())).'">Details</a></td>
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