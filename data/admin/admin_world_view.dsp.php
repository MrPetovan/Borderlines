<?php

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url(PAGE_CODE).'&id='.$world->id;
  $PAGE_TITRE = 'World : Showing "'.$world->name.'"';
?>
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Showing "<?php echo $world->name?>"</h3>
    <div class="informations formulaire">

            <p class="field">
              <span class="libelle">Size X</span>
              <span class="value"><?php echo is_array($world->size_x)?nl2br(parameters_to_string( $world->size_x )):$world->size_x?></span>
            </p>
            <p class="field">
              <span class="libelle">Size Y</span>
              <span class="value"><?php echo is_array($world->size_y)?nl2br(parameters_to_string( $world->size_y )):$world->size_y?></span>
            </p>
            <p class="field">
              <span class="libelle">Generation Method</span>
              <span class="value"><?php echo is_array($world->generation_method)?nl2br(parameters_to_string( $world->generation_method )):$world->generation_method?></span>
            </p>
            <p class="field">
              <span class="libelle">Generation Parameters</span>
              <span class="value"><?php echo is_array($world->generation_parameters)?nl2br(parameters_to_string( $world->generation_parameters )):$world->generation_parameters?></span>
            </p>
            <p class="field">
              <span class="libelle">Created</span>
              <span class="value"><?php echo guess_time($world->created, GUESS_DATETIME_LOCALE)?></span>
            </p>
<?php
      $option_list = array(null => 'Pas de choix');
      $player_list = Player::db_get_all();
      foreach( $player_list as $player)
        $option_list[ $player->id ] = $player->name;
?>
      <p class="field">
        <span class="libelle">Created By</span>
        <span class="value"><a href="<?php echo get_page_url('admin_player_view', true, array('id' => $world->created_by ) )?>"><?php echo $option_list[ $world->created_by ]?></a></span>
      </p>
    </div>
    <p><a href="<?php echo get_page_url('admin_world_mod', true, array('id' => $world->id))?>">Modifier cet objet World</a></p>
<?php
  // CUSTOM
?>
     <h3>Map</h3>
<?php if( 1 == 2 ) {?>
     <!--<iframe width="256" height="256" style="border:none" src="<?php echo Page::get_url('world_get_tile', array('w' => $world->id, 'x' => 0, 'y' => 0, 'z' => 1, 'force' => true))?>"></iframe><iframe width="256" height="256" style="border:none" src="<?php echo Page::get_url('world_get_tile', array('w' => $world->id, 'x' => 1, 'y' => 0, 'z' => 1, 'force' => true))?>"></iframe>
     <br/>
     <iframe width="256" height="256" style="border:none" src="<?php echo Page::get_url('world_get_tile', array('w' => $world->id, 'x' => 0, 'y' => 1, 'z' => 1, 'force' => true))?>"></iframe><iframe width="256" height="256" style="border:none" src="<?php echo Page::get_url('world_get_tile', array('w' => $world->id, 'x' => 1, 'y' => 1, 'z' => 1, 'force' => true))?>"></iframe>-->
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
<?php }else {
  $ratio = min( 968 / $world->size_x, 1);
  echo $world->drawImg(array(
    'with_map' => true,
    'ratio' => $ratio
  ));
}?>
<p><a href="<?php echo get_page_url('admin_world_view', true, array('id' => $world->id, 'action' => 'generate'))?>">Regenerate territories</a></p>
<h3>Territories</h3>
<?php
  if(count($world->territories)) {
?>
<table>
  <thead>
    <tr>
      <th>Name</th>
      <th>Area</th>
    </tr>
  </thead>
  <tbody>
<?php
    $total = 0;
    foreach( $world->territories as $territory ) {
      $total += $territory->get_area();
      echo '
    <tr>
      <td><a href="'.get_page_url('show_territory', true, array('id' => $territory->id)).'">'.$territory->name.'</a></td>
      <td>'.$territory->get_area().' km²</td>
    </tr>';
    }
?>
  </tbody>
  <tfoot>
    <tr>
      <td><?php echo count( $world->territories )?> territories</td>
      <td>Average : <?php echo round( $total / count( $world->territories ), 1 )?> km²</td>
    </tr>
  </tfoot>
</table>
<p><a href="<?php echo get_page_url('admin_world_view', true, array('id' => $world->id, 'action' => 'generate'))?>">Regenerate territories</a></p>
<?php
  }else {
?>
<p>No territories yet</p>
<p><a href="<?php echo get_page_url('admin_world_view', true, array('id' => $world->id, 'action' => 'generate'))?>">Generate territories</a></p>
<?php
  }
?>

<?php
  $lake_nb = 0;
  $lake_area = 0;
  $mountain_nb = 0;
  $mountain_area = 0;
  $sea_nb = 0;
  $sea_area = 0;
  $capturable_nb = 0;
  $capturable_area = 0;
  foreach( $world->territories as $territory ) {
    if( strpos($territory->name, 'Lake') === 0 ) {
      $lake_nb ++;
      $lake_area += $territory->area;
    }elseif( strpos($territory->name, 'Mountain') === 0) {
      $mountain_nb ++;
      $mountain_area += $territory->area;
    }elseif( strpos($territory->name, 'Sea') === 0) {
      $sea_nb ++;
      $sea_area += $territory->area;
    }else {
      $capturable_nb ++;
      $capturable_area += $territory->area;
    }
  }
?>
<table class="table table-condensed table-striped">
  <caption>Territory stats</caption>
  <thead>
    <tr>
      <th>Type</th>
      <th class="num">Nb</th>
      <th class="num">Area total</th>
      <th class="num">Area avg</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>Sea</td>
      <td class="num"><?php echo l10n_number($sea_nb)?></td>
      <td class="num"><?php echo l10n_number($sea_area)?> km²</td>
      <td class="num"><?php echo $sea_nb != 0?l10n_number($sea_area / $sea_nb):0?> km²</td>
    </tr>
    <tr>
      <td>Lake</td>
      <td class="num"><?php echo l10n_number($lake_nb)?></td>
      <td class="num"><?php echo l10n_number($lake_area)?> km²</td>
      <td class="num"><?php echo $lake_nb != 0?l10n_number($lake_area / $lake_nb):0?> km²</td>
    </tr>
    <tr>
      <td>Mountain</td>
      <td class="num"><?php echo l10n_number($mountain_nb)?></td>
      <td class="num"><?php echo l10n_number($mountain_area)?> km²</td>
      <td class="num"><?php echo $mountain_nb != 0?l10n_number($mountain_area / $mountain_nb):0?> km²</td>
    </tr>
    <tr>
      <td>Capturable</td>
      <td class="num"><?php echo l10n_number($capturable_nb)?></td>
      <td class="num"><?php echo l10n_number($capturable_area)?> km²</td>
      <td class="num"><?php echo $capturable_nb != 0?l10n_number($capturable_area / $capturable_nb):0?> km²</td>
    </tr>
  </tbody>

</table>
<?php

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_world')?>">Revenir à la liste des objets World</a></p>
  </div>
</div>