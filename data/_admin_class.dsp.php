  $PAGE_TITRE = "Administration des <?php echo $class_name ?>s";
  include_once('data/static/html_functions.php');

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = <?php echo $class_php_identifier ?>::db_get_all($page_no, $nb_per_page, true);
  $nb_total = <?php echo $class_php_identifier ?>::db_count_all(true);

    echo '
<div class="texte_contenu">';

	admin_menu(PAGE_CODE);

	echo '
  <div class="texte_texte">
    <h3>Liste des <?php echo $class_name ?>s</h3>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
    <table>
      <thead>
        <tr>
          <th>Sel.</th>
          <th><?php echo to_readable($name_field)?></th><?php
foreach( $table_columns as $column_name => $column_props ) {
  if( $column_name != $name_field && $column_name != "id" )
    echo '
          <th>'.$column_props['Comment'].'</th>';
} ?>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.get_page_url('admin_<?php echo $class_db_identifier?>_mod').'">Ajouter manuellement un objet <?php echo $class_name ?></a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $<?php echo $class_db_identifier?>) {
      echo '
        <tr>
          <td><input type="checkbox" name="<?php echo $class_db_identifier?>_id[]" value="'.$<?php echo $class_db_identifier?>->get_id().'"/></td>
          <td><a href="'.htmlentities_utf8(get_page_url('admin_<?php echo $class_db_identifier?>_view', true, array('id' => $<?php echo $class_db_identifier?>->get_id()))).'">'.$<?php echo $class_db_identifier?>->get_<?php echo $name_field?>().'</a></td>
<?php
foreach( $table_columns as $column_name => $column_props ) {
  if( array_key_exists($column_name, $foreign_keys)) {
    $foreign_table = $foreign_keys[$column_name];
    echo "';
      $".$foreign_table."_temp = ".to_camel_case($foreign_table, true)."::instance( $".$class_db_identifier."->get_".$column_name."());
      echo '
          <td>'.$".$foreign_table."_temp->get_name().'</td>";
  }elseif( $column_name != $name_field && $column_name != "id" )
    switch ($column_props['SimpleType']) {
      case 'varchar':
      case 'char':
      default:
        echo "
          <td>'.$".$class_db_identifier."->get_".$column_name."().'</td>";
        break;
      case 'datetime':
      case 'time':
      case 'date':
        echo "
          <td>'.guess_time($".$class_db_identifier."->get_".$column_name."(), GUESS_DATE_FR).'</td>";
        break;
      case 'tinyint' :
        echo "
          <td>'.\$tab_visible[$".$class_db_identifier."->get_".$column_name."()].'</td>";
        break;
    }
} ?>

          <td><a href="'.htmlentities_utf8(get_page_url('admin_<?php echo $class_db_identifier?>_mod', true, array('id' => $<?php echo $class_db_identifier?>->get_id()))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
        </tr>';
    }
    echo '
      </tbody>
    </table>
    <p>Pour les objets <?php echo $class_name?> sélectionnés :
      <select name="action">
        <option value="delete">Delete</option>
      </select>
      <input type="submit" name="submit" value="Valider"/>
    </p>
    </form>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
  </div>
</div>';