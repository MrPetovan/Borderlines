<?php
  $PAGE_TITRE = 'World : Showing "'.$world->name.'"';

  $territories = $world->territories;
?>
<h2>Showing "<?php echo $world->name?>"</h2>
<div class="informations formulaire">

  <h3>Map</h3>
<?php
  //$world->initializeTerritories();
  echo $world->drawImg(true);
?>
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
  <tfoot>
    <tr>
      <td colspan="3"><?php echo count( $world->territories )?> territories</td>
    </tr>
  </tfoot>
  <tbody>
<?php
    foreach( $world->territories as $territory ) {
      echo '
    <tr>
      <td><a href="'.get_page_url('show_territory', true, array('id' => $territory->id)).'">'.$territory->name.'</a></td>
      <td>'.$territory->getArea().' km²</td>
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