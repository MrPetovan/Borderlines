<?php
  $PAGE_TITRE = "Administration des Territorys";
  include_once('data/static/html_functions.php');

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = Territory::db_get_all($page_no, $nb_per_page);
  $nb_total = Territory::db_count_all();

    echo '
<div class="texte_contenu">';

	admin_menu(PAGE_CODE);

	echo '
  <div class="texte_texte">
    <h3>Liste des Territorys</h3>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
    <table>
      <thead>
        <tr>
          <th>Sel.</th>
          <th>Name</th>
          <th>World Id</th>        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.get_page_url('admin_territory_mod').'">Ajouter manuellement un objet Territory</a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $territory) {
      echo '
        <tr>
          <td><input type="checkbox" name="territory_id[]" value="'.$territory->get_id().'"/></td>
          <td><a href="'.htmlentities_utf8(get_page_url('admin_territory_view', true, array('id' => $territory->get_id()))).'">'.$territory->get_name().'</a></td>
';
      $world_temp = World::instance( $territory->get_world_id());
      echo '
          <td>'.$world_temp->get_name().'</td>
          <td><a href="'.htmlentities_utf8(get_page_url('admin_territory_mod', true, array('id' => $territory->get_id()))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
        </tr>';
    }
    echo '
      </tbody>
    </table>
    <p>Pour les objets Territory sélectionnés :
      <select name="action">
        <option value="delete">Delete</option>
      </select>
      <input type="submit" name="submit" value="Valider"/>
    </p>
    </form>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
  </div>
</div>';