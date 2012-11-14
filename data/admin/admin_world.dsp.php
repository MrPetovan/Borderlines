<?php
  $PAGE_TITRE = "Administration des Worlds";

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = World::db_get_all($page_no, $nb_per_page, true);
  $nb_total = World::db_count_all(true);

    echo '
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Liste des Worlds</h3>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
    <form action="'.Page::get_url(PAGE_CODE).'" method="post">
    <table class="table table-condensed table-striped table-hover">
      <thead>
        <tr>
          <th>Sel.</th>
          <th>Name</th>
          <th>Size X</th>
          <th>Size Y</th>
          <th>Generation Method</th>
          <th>Generation Parameters</th>
          <th>Created</th>
          <th>Created By</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.Page::get_url('admin_world_mod').'">Ajouter manuellement un objet World</a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $world) {
      echo '
        <tr>
          <td><input type="checkbox" name="world_id[]" value="'.$world->id.'"/></td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_world_view', array('id' => $world->id))).'">'.$world->get_name().'</a></td>

          <td>'.(is_array($world->size_x)?nl2br(parameters_to_string($world->size_x)):$world->size_x).'</td>
          <td>'.(is_array($world->size_y)?nl2br(parameters_to_string($world->size_y)):$world->size_y).'</td>
          <td>'.(is_array($world->generation_method)?nl2br(parameters_to_string($world->generation_method)):$world->generation_method).'</td>
          <td>'.(is_array($world->generation_parameters)?nl2br(parameters_to_string($world->generation_parameters)):$world->generation_parameters).'</td>
          <td>'.guess_time($world->created, GUESS_DATETIME_LOCALE).'</td>';
      $player_temp = Player::instance( $world->created_by);
      echo '
          <td>'.$player_temp->name.'</td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_world_mod', array('id' => $world->id))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
        </tr>';
    }
    echo '
      </tbody>
    </table>
    <p>Pour les objets World sélectionnés :
      <select name="action">
        <option value="delete">Delete</option>
      </select>
      <input type="submit" name="submit" value="Valider"/>
    </p>
    </form>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
  </div>
</div>';