<?php
  $PAGE_TITRE = 'Territory : Showing "'.$territory->name.'"';

  /* @var $territory Territory */
  $territory_owner_row = array_pop( $territory->get_territory_owner_list( $current_game->id, $current_game->current_turn ) );
  if( $territory_owner_row['owner_id'] !== null ) {
    $owner = Player::instance($territory_owner_row['owner_id']);
  }

  $neighbour_list = array();
  foreach( $territory->get_territory_neighbour_list() as $territory_neighbour ) {
    $neighbour_list[] = Territory::instance($territory_neighbour['neighbour_id']);
  }
  $world = World::instance( $territory->world_id );
?>
<h2>Showing "<?php echo $territory->name?>"</h2>
<div class="informations formulaire">
  <p>
      <span class="label">Area</span>
      <span class="value"><?php echo $territory->get_area()?> km²</span>
  </p>
  <p>
    <span class="label">Border length</span>
    <span class="value"><?php echo $territory->get_perimeter()?> km</span>
  </p>
  <p>
    <span class="label">Capital city</span>
    <span class="value"><?php echo $territory->capital_name?></span>
  </p>
  <p>
    <span class="label">Current owner</span>
    <span class="value"><?php echo $territory_owner_row['owner_id']?'<a href="'.Page::get_url('show_player', array('id' => $owner->id)).'">'.$owner->name.'</a>':'Nobody'?></span>
  </p>
  <p>
    <span class="label">Status</span>
    <span class="value"><?php echo $territory_owner_row['is_contested']?'Contested':'Stable'?></span>
  </p>
</div>
<?php
  echo $world->drawImg(true, array_merge( array($territory), $neighbour_list), $current_game->id);
?>
<h3>Neighbours</h3>
<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Area</th>
      <th>Current owner</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
<?php
    foreach( $neighbour_list as $neighbour ) {
      $territory_owner_row = array_pop( $neighbour->get_territory_owner_list( $current_game->id, $current_game->current_turn ) );
      if( $territory_owner_row['owner_id'] !== null ) {
        $owner = Player::instance($territory_owner_row['owner_id']);
      }
      echo '
    <tr>
      <td><a href="'.Page::get_url('show_territory', array('id' => $neighbour->id)).'">'.$neighbour->name.'</a></td>
      <td>'.$neighbour->get_area().' km²</td>
      <td>'.($territory_owner_row['owner_id']?'<a href="'.Page::get_url('show_player', array('id' => $owner->id)).'">'.$owner->name.'</a>':'Nobody').'</td>
      <td>'.($territory_owner_row['is_contested']?'Contested':'Stable').'</td>
    </tr>';
    }
?>
  </tbody>
</table>
<?php
  if( $current_game ) {
?>
<h3>Troops</h3>
<table>
  <tr>
    <th>Turn</th>
    <th>Player</th>
    <th>Quantity</th>
  </tr>
<?php
    foreach( $territory->get_territory_player_troops_list( $current_game->id ) as $territory_player_troops ) {
      //$neighbour = Territory::instance($territory_neighbour['neighbour_id']);
      $game = Game::instance( $territory_player_troops['game_id'] );
      $player = Player::instance( $territory_player_troops['player_id'] );
      $is_current = $territory_player_troops['turn'] == $current_game->current_turn;
      echo '
  <tr'.($is_current?' class="current"':'').'>
    <td>'.$territory_player_troops['turn'].'</td>
    <td><a href="'.Page::get_url('show_player', array('id' => $player->id)).'">'.$player->name.'</a></td>
    <td>'.$territory_player_troops['quantity'].'</td>
  </tr>';
    }
?>
</table>
<?php
    $class = 'move_troops';

    require_once(DATA.'order_type/'.$class.'.class.php');

    echo $class::get_html_form( array(
      'page_code' => PAGE_CODE,
      'page_params' => array('id' => $territory->id ),
      'current_player' => $current_player,
      'to_territory' => $territory
    ));

    echo $class::get_html_form( array(
      'page_code' => PAGE_CODE,
      'page_params' => array('id' => $territory->id ),
      'current_player' => $current_player,
      'from_territory' => $territory
    ));

    $class = 'change_capital';

    require_once(DATA.'order_type/'.$class.'.class.php');

    echo $class::get_html_form( array(
      'page_code' => PAGE_CODE,
      'page_params' => array('id' => $territory->id ),
      'current_player' => $current_player,
      'territory' => $territory
    ));
  }
?>
<p><a href="<?php echo Page::get_url('show_world', array('id' => $territory->world_id ) )?>">Return to world map</a></p>