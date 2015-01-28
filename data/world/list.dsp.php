<?php
  $PAGE_TITRE = "Administration des Worlds";

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = World::db_get_all($page_no, $nb_per_page, true);
  $nb_total = World::db_count_all(true);
?>
<h2><?php echo __('World List')?>
  <?php if( $current_player->can_create_world() ):?>
  <a href="<?php echo Page::get_page_url('world_create')?>" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-plus"></span> <?php echo __('Create a new world')?></a>
  <?php endif;?>
</h2>
<?php echo nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page)?>
<table class="table table-condensed table-striped table-hover">
  <thead>
    <tr>
      <th>Name</th>
      <th>Size X</th>
      <th>Size Y</th>
      <th>Generation Method</th>
      <th>Territory count</th>
      <th>Creator</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
<?php
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $world) {
      $player = Player::instance( $world->created_by );
      $action = '';
      if( $world->get_territory_count() == 0 && ( is_admin() || $current_player->id == $world->created_by ) ) {
        $action = '<a href="'.Page::get_url(PAGE_CODE, array('world_id' => $world->id, 'action' => 'generate')).'">Regenerate</a>';
      }
      echo '
    <tr>
      <td><a href="'.Page::get_url('show_world', array('id' => $world->id)).'">'.$world->name.'</a></td>
      <td>'.$world->size_x.'</td>
      <td>'.$world->size_y.'</td>
      <td>'.$world->generation_method.'</td>
      <td>' . $world->get_territory_count() . '</td>
      <td><a href="'.Page::get_url('show_player', array('id' => $player->id)).'">'.$player->name.'</a></td>
      <td>'.$action.'</td>
    </tr>';
    }
?>
  </tbody>
</table>
<?php echo nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page)?>