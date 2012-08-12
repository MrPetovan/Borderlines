<?php
  $PAGE_TITRE = 'Territory : Showing "'.$territory->name.'"';

  /* @var $territory Territory */
?>
<h2>Showing "<?php echo $territory->name?>"</h2>
<div class="informations formulaire">
  <p><span class="label">Area</span><span class="value"><?php echo $territory->get_area()?> km²</span></p>
  <p><span class="label">Border length</span><span class="value"><?php echo $territory->get_perimeter()?> km</span></p>
  <p><span class="label">Capital city</span><span class="value"><?php echo $territory->capital_name?></span></p>
</div>
<h3>Neighbours</h3>
<ul>
<?php
  foreach( $territory->get_territory_neighbour_list() as $territory_neighbour ) {
    $neighbour = Territory::instance($territory_neighbour['neighbour_id']);
    echo '
  <li><a href="'.Page::get_url('show_territory', array('id' => $neighbour->id)).'">'.$neighbour->name.'</a></li>';
  }
?>
</ul>
<h3>Troops</h3>
<table>
  <tr>
    <th>Game</th>
    <th>Turn</th>
    <th>Player</th>
    <th>Quantity</th>
  </tr>
<?php
  foreach( $territory->get_territory_player_troops_list() as $territory_player_troops ) {
    //$neighbour = Territory::instance($territory_neighbour['neighbour_id']);
    $game = Game::instance( $territory_player_troops['game_id'] );
    $player = Player::instance( $territory_player_troops['player_id'] );
    echo '
  <tr>
    <td><a href="'.Page::get_url('show_game', array('id' => $game->id)).'">'.$game->name.'</a></td>
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
?>
<p><a href="<?php echo Page::get_url('show_world', array('id' => $territory->world_id ) )?>">Return to world map</a></p>