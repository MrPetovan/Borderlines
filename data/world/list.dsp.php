<?php
  $PAGE_TITRE = "Administration des Worlds";

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = World::db_get_all($page_no, $nb_per_page, true);
  $nb_total = World::db_count_all(true);
?>
<h2>Liste des Worlds</h2>
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
      if( count( $world->territories ) == 0 && ( is_admin() || $current_player->id == $world->created_by ) ) {
        $action = '<a href="'.Page::get_url(PAGE_CODE, array('world_id' => $world->id, 'action' => 'generate')).'">Regenerate</a>';
      }
      echo '
    <tr>
      <td><a href="'.Page::get_url('show_world', array('id' => $world->id)).'">'.$world->name.'</a></td>
      <td>'.$world->size_x.'</td>
      <td>'.$world->size_y.'</td>
      <td>'.$world->generation_method.'</td>
      <td>'.count( $world->territories ).'</td>
      <td><a href="'.Page::get_url('show_player', array('id' => $player->id)).'">'.$player->name.'</a></td>
      <td>'.$action.'</td>
    </tr>';
    }
?>
  </tbody>
</table>
<?php
  if( $current_player->can_create_world() ) {
    echo '
<form class="formulaire" action="'.Page::get_page_url( PAGE_CODE ).'" method="post">
  '.$world_mod->html_creation_form().'
  <p>'.HTMLHelper::submit('world_submit', __('Add a world') ).'</p>
</form>';
  }
?>