<?php
  $PAGE_TITRE = __('World : Showing "%s"', $world->name );

  $is_current_turn = $turn == $current_game->current_turn;

  /* @var $world World */
  if( $game_id ) {
    $territory_params = array('game_id' => $game_id, 'turn' => $turn);
  }else {
    $territory_params = array();
  }

?>

<?php if( $is_current_turn || !$game_id ) :?>
<h2><?php echo __('Showing "%s"', $world->name)?></h2>
<?php else :?>
<h2><?php echo __('Showing "%s" on turn %s', $world->name, $turn)?></h2>
<?php endif;?>
<?php if( $game_id ) :?>
<ul>
  <?php for( $i = 0; $i <= $current_game->current_turn; $i ++ ) :?>
  <li>
    <?php if( $i == $turn ) : ?>
    <span><?php echo __('Turn %s', $i)?></span>
    <?php else:?>
    <a href="<?php echo Page::get_url('show_world', array('game_id' => $current_game->id, 'turn' => $i))?>">
        <?php echo __('Turn %s', $i)?>
    </a>
    <?php endif;?>
  </li>
  <?php endfor;?>
</ul>
<?php endif;?>
<h3><?php echo __('Map')?></h3>
<?php
  if( 1 == 2 ) {
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
    $ratio = min( 968 / $world->size_x, 1);
    echo $world->drawImg(array(
      'with_map' => true,
      'game_id' => $current_game->id,
      'turn' => $turn,
      'ratio' => $ratio
    ));
  }

  if( $game_id ) {
?>
<h3>Territory Summary</h3>
<?php
    $territory_summary = $current_player->get_territory_summary($current_game->id, $turn);
?>
<table>
  <tr>
    <th><?php echo __('Territory')?></th>
    <th><?php echo __('Owner')?></th>
    <th><?php echo __('Type')?></th>
    <th><?php echo __('Area')?></th>
    <th><?php echo __('Status')?></th>
    <th><?php echo __('Troops')?></th>
  </tr>
<?php

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
    <td><a href="'.Page::get_url('show_territory', array_merge( $territory_params, array('id' => $territory->id) ) ).'">'.$territory->name.'</a></td>
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
?>
  <tbody>
    <tr>
      <th colspan="2"></th>
      <th><?php echo __('Total')?></th>
      <td class="num"><?php echo l10n_number( $total_territory )?> km²</td>
      <th><?php echo __('Total')?></th>
      <td class="num"><?php echo l10n_number( $total_troops )?> <img src="<?php echo IMG?>img_html/helmet.png" alt="<?php echo __('Troops')?>" title="<?php echo __('Troops')?>"/></td>
    </tr>
  </tbody>
</table>
<h3 id="territories"><?php echo __('Territories')?></h3>
<?php if(count($territory_list)) {
  $name_url_params = $params;
  if( $sort_field == 'name' ) {
    $name_url_params['sort_direction'] = abs( $name_url_params['sort_direction'] - 1 );
  }else {
    $name_url_params['sort_field'] = 'name';
    $name_url_params['sort_direction'] = 1;
  }
  $name_url = Page::get_url(PAGE_CODE, $name_url_params);

  $owner_url_params = $params;
  if( $sort_field == 'owner' ) {
    $owner_url_params['sort_direction'] = abs( $owner_url_params['sort_direction'] - 1 );
  }else {
    $owner_url_params['sort_field'] = 'owner';
    $owner_url_params['sort_direction'] = 1;
  }
  $owner_url = Page::get_url(PAGE_CODE, $owner_url_params);

?>
<table>
  <thead>
    <tr>
      <th><a href="<?php echo $name_url?>#territories"><?php echo __('Name')?></a></th>
      <th><?php echo __('Area')?></th>
      <th><a href="<?php echo $owner_url?>#territories"><?php echo __('Owner')?></a></th>
    </tr>
  </thead>
  <tfoot>
    <tr>
      <td colspan="3"><?php echo __('%s territories', count( $territory_list ))?></td>
    </tr>
  </tfoot>
  <tbody>
<?php
  foreach( $territory_list as $territory ) {
    /* @var $territory Territory */
    $owner_id = $territory->get_owner($current_game->id, $turn);
    if( $owner_id != null ) {
      $owner = Player::instance($owner_id);
    }
    echo '
    <tr>
      <td><a href="'.Page::get_url('show_territory', array_merge($territory_params, array('id' => $territory->id))).'">'.$territory->name.'</a></td>
      <td>'.l10n_number( $territory->get_area() ).' km²</td>
      <td>'.($owner_id?'<a href="'.Page::get_url('show_player', array('id' => $owner->id)).'">'.$owner->name.'</a>':__('Nobody')).'</td>
    </tr>';
  }
?>
  </tbody>
</table>
<?php
    }else {
?>

<p><?php echo __('No territories yet')?></p>

<?php
    } //if(count($world->territories))
  }else {
?>
<h3><?php echo __('Generation Parameters')?></h3>
<ul class="informations formulaire">
  <li>
    <span class="label"><?php echo __('Width')?></span>
    <span class="value"><?php echo l10n_number($world->size_x)?> km²</span>
  </li>
  <li>
    <span class="label"><?php echo __('Height')?></span>
    <span class="value"><?php echo l10n_number($world->size_y)?> km²</span>
  </li>
  <li>
    <span class="label"><?php echo __('Generation method')?></span>
    <span class="value"><?php echo __($world->generation_method)?></span>
  </li>
<?php foreach( $world->generation_parameters as $param => $value ) {?>
  <li>
    <span class="label"><?php echo __($param)?></span>
    <span class="value"><?php echo $value?></span>
  </li>
<?php }?>
</ul>

<h3 id="territories"><?php echo __('Territories')?></h3>
<?php
    if(count($territory_list)) {
?>
<table>
  <thead>
    <tr>
      <th><?php echo __('Name')?></th>
      <th><?php echo __('Area')?></th>
    </tr>
  </thead>
  <tbody>
<?php
  $total = 0;
  foreach( $territory_list as $territory ) {
    /* @var $territory Territory */
    $total += $territory->area;
    echo '
    <tr>
      <td><a href="'.Page::get_url('show_territory', array_merge($territory_params, array('id' => $territory->id))).'">'.$territory->name.'</a></td>
      <td class="num">'.l10n_number( $territory->get_area() ).' km²</td>
    </tr>';
  }
?>
  </tbody>
  <tfoot>
    <tr>
      <td><?php echo __('%s territories', count( $territory_list ))?></td>
      <td class="num"><?php echo __('Average: %s km²', l10n_number($total / count( $territory_list )))?></td>
    </tr>
  </tfoot>
</table>
<?php
    }else {
?>
<p><?php echo __('No territories yet')?></p>
<?php
    } //if(count($world->territories))
  }
?>
