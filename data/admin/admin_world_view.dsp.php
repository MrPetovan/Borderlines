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
              <span class="value"><?php echo $world->size_x?></span>
            </p>
            <p class="field">
              <span class="libelle">Size Y</span>
              <span class="value"><?php echo $world->size_y?></span>
            </p>
            <p class="field">
              <span class="libelle">Generation Method</span>
              <span class="value"><?php echo $world->generation_method?></span>
            </p>
            <p class="field">
              <span class="libelle">Generation Parameters</span>
              <span class="value"><?php echo $world->generation_parameters?></span>
            </p>
            <p class="field">
              <span class="libelle">Created</span>
              <span class="value"><?php echo guess_time($world->created, GUESS_DATETIME_LOCALE)?></span>
            </p>    </div>
    <p><a href="<?php echo get_page_url('admin_world_mod', true, array('id' => $world->id))?>">Modifier cet objet World</a></p>
<?php
  // CUSTOM
?>
     <h3>Map</h3>
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

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_world')?>">Revenir à la liste des objets World</a></p>
  </div>
</div>