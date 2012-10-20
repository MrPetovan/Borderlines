<?php
  $PAGE_TITRE = __('World : Showing "%s"', $world->name );

  $is_current_turn = $turn == $current_game->current_turn;

  /* @var $world World */
?>

<?php if( $is_current_turn ) :?>
<h2><?php echo __('Showing "%s"', $world->name)?></h2>
<?php else :?>
<h2><?php echo __('Showing "%s" on turn %s', $world->name, $turn)?></h2>
<?php endif;?>

<ul>
<?php for( $i = 0; $i <= $current_game->current_turn; $i ++ ) :?>
  <li>
      <?php if( $i == $turn ) : ?>
    <span><?php echo __('Turn %s', $i)?></span>
      <?php else:?>
    <a href="<?php echo Page::get_url('show_world', array('id' => $current_game->world_id, 'game_id' => $current_game->id, 'turn' => $i))?>">
        <?php echo __('Turn %s', $i)?>
    </a>
      <?php endif;?>
  </li>
<?php endfor;?>
</ul>

<h3><?php echo __('Map')?></h3>
<?php
  if( $world->size_x > 968 ) {
?>
<svg id="map"/>
     <script type="text/javascript">
  var po = org.polymaps;
  var map = po.map()
    .container(document.getElementById('map'))
    .add(po.image().url('<?php echo Page::get_url('world_get_tile', array('w' => $world->id, 'x' => '{X}', 'y' => '{Y}', 'z' => '{Z}'))?>'))
    .add(po.interact())
    .center({lat: 36, lon:-169})
    .zoom(3);
     </script>
<?php
  }else {

    echo $world->drawImg(array(
      'with_map' => true,
      'game_id' => $current_game->id,
      'turn' => $turn
    ));
  }
?>
<h3><?php echo __('Territories')?></h3>
<?php if(count($world->territories)) :?>
<table>
  <thead>
    <tr>
      <th><?php echo __('Name')?></th>
      <th><?php echo __('Area')?></th>
      <th><?php echo __('Owner')?></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="3"><?php echo __('%s territories', count( $world->territories ))?></td>
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
      <td>'.l10n_number( $territory->get_area() ).' km²</td>
      <td>'.($owner_id?'<a href="'.Page::get_url('show_player', array('id' => $owner->id)).'">'.$owner->name.'</a>':__('Nobody')).'</td>
    </tr>';
    }
?>
  </tbody>
</table>

<?php else: ?>

<p><?php echo __('No territories yet')?></p>

<?php endif; //if(count($world->territories))?>