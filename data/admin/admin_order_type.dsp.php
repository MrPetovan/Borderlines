<?php
  $PAGE_TITRE = "Administration des Order Types";
  include_once('data/static/html_functions.php');

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = Order_Type::db_get_all($page_no, $nb_per_page, true);
  $nb_total = Order_Type::db_count_all(true);

    echo '
<div class="texte_contenu">';

	admin_menu(PAGE_CODE);

	echo '
  <div class="texte_texte">
    <h3>Liste des Order Types</h3>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
    <form action="'.Page::get_url(PAGE_CODE).'" method="post">
    <table>
      <thead>
        <tr>
          <th>Sel.</th>
          <th>Name</th>
          <th>Class Name</th>
          <th>Active</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.Page::get_url('admin_order_type_mod').'">Ajouter manuellement un objet Order Type</a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $order_type) {
      echo '
        <tr>
          <td><input type="checkbox" name="order_type_id[]" value="'.$order_type->id.'"/></td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_order_type_view', array('id' => $order_type->id))).'">'.$order_type->get_name().'</a></td>

          <td>'.$order_type->class_name.'</td>
          <td>'.$tab_visible[$order_type->active].'</td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_order_type_mod', array('id' => $order_type->id))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
        </tr>';
    }
    echo '
      </tbody>
    </table>
    <p>Pour les objets Order Type sélectionnés :
      <select name="action">
        <option value="delete">Delete</option>
      </select>
      <input type="submit" name="submit" value="Valider"/>
    </p>
    </form>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
  </div>
</div>';