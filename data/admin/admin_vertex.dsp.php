<?php
  $PAGE_TITRE = "Administration des Vertexs";
  include_once('data/static/html_functions.php');

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = Vertex::db_get_all($page_no, $nb_per_page, true);
  $nb_total = Vertex::db_count_all(true);

    echo '
<div class="texte_contenu">';

	admin_menu(PAGE_CODE);

	echo '
  <div class="texte_texte">
    <h3>Liste des Vertexs</h3>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
    <table>
      <thead>
        <tr>
          <th>Sel.</th>
          <th>Name</th>
          <th>Guid</th>
          <th>X</th>
          <th>Y</th>        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.get_page_url('admin_vertex_mod').'">Ajouter manuellement un objet Vertex</a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $vertex) {
      echo '
        <tr>
          <td><input type="checkbox" name="vertex_id[]" value="'.$vertex->get_id().'"/></td>
          <td><a href="'.htmlentities_utf8(get_page_url('admin_vertex_view', true, array('id' => $vertex->get_id()))).'">'.$vertex->get_name().'</a></td>

          <td>'.$vertex->get_guid().'</td>
          <td>'.$vertex->get_x().'</td>
          <td>'.$vertex->get_y().'</td>
          <td><a href="'.htmlentities_utf8(get_page_url('admin_vertex_mod', true, array('id' => $vertex->get_id()))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
        </tr>';
    }
    echo '
      </tbody>
    </table>
    <p>Pour les objets Vertex sélectionnés :
      <select name="action">
        <option value="delete">Delete</option>
      </select>
      <input type="submit" name="submit" value="Valider"/>
    </p>
    </form>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
  </div>
</div>';