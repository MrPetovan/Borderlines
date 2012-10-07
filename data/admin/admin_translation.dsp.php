<?php
  $PAGE_TITRE = "Administration des Translations";
  include_once('data/static/html_functions.php');

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = Translation::db_get_all($page_no, $nb_per_page, true);
  $nb_total = Translation::db_count_all(true);

    echo '
<div class="texte_contenu">';

	admin_menu(PAGE_CODE);

	echo '
  <div class="texte_texte">
    <h3>Liste des Translations</h3>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
    <form action="'.Page::get_url(PAGE_CODE).'" method="post">
    <table>
      <thead>
        <tr>
          <th>Sel.</th>
          <th>Id</th>
          <th>Code</th>
          <th>Locale</th>
          <th>Translation</th>
          <th>Context</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.Page::get_url('admin_translation_mod').'">Ajouter manuellement un objet Translation</a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $translation) {
      echo '
        <tr>
          <td><input type="checkbox" name="translation_id[]" value="'.$translation->id.'"/></td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_translation_view', array('id' => $translation->id))).'">'.$translation->get_id().'</a></td>

          <td>'.$translation->code.'</td>
          <td>'.$translation->locale.'</td>
          <td>'.$translation->translation.'</td>
          <td>'.$translation->context.'</td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_translation_mod', array('id' => $translation->id))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
        </tr>';
    }
    echo '
      </tbody>
    </table>
    <p>Pour les objets Translation sélectionnés :
      <select name="action">
        <option value="delete">Delete</option>
      </select>
      <input type="submit" name="submit" value="Valider"/>
    </p>
    </form>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
  </div>
</div>';