<?php
  $PAGE_TITRE = 'World : Showing "'.$world->name.'"';

?>
<h2>Showing "<?php echo $world->name?>"</h2>
<div class="informations formulaire">

  <h3>Map</h3>
<?php echo $world->drawImg(array(
  'with_map' => true,
  'game_id' => $current_game->id
));?>
<h3>Territories</h3>
<?php
  if(count($world->territories)) {
?>
<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Area</th>
      <th>Owner</th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="3"><?php echo count( $world->territories )?> territories</td>
    </tr>
  </tfoot>
  <tbody>
<?php
    foreach( $world->territories as $territory ) {
      /* @var $territory Territory */
      $owner_id = $territory->get_owner($current_game->id);
      if( $owner_id != null ) {
        $owner = Player::instance($owner_id);
      }
      echo '
    <tr>
      <td><a href="'.get_page_url('show_territory', true, array('id' => $territory->id)).'">'.$territory->name.'</a></td>
      <td>'.$territory->get_area().' km²</td>
      <td>'.($owner_id?'<a href="'.Page::get_url('show_player', array('id' => $owner->id)).'">'.$owner->name.'</a>':'Nobody').'</td>
    </tr>';
    }
?>
  </tbody>
</table>
<?php
  }else {
    echo '
<p>No territories yet</p>';
  }

?>
<p><a href="<?php echo get_page_url('world_list')?>">Return to world list</a></p>