  include_once('data/static/html_functions.php');

  if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $<?php echo $class_db_identifier ?> = new <?php echo $class_php_identifier ?>($id);
  }else {
    $id = null;
    $<?php echo $class_db_identifier ?> = new <?php echo $class_php_identifier ?>();
  }

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url($PAGE_CODE).'&id='.$id;
  $PAGE_TITRE = 'Utilisateur : Consultation de "'.$<?php echo $class_db_identifier ?>->get_<?php echo $name_field?>().'"';
?>
<div class="texte_contenu">';
<_?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Consultation des données pour "<_?php echo $<?php echo $class_db_identifier ?>->get_pseudo()?>"</h3>
    <div class="informations formulaire">
<?php
foreach( $table_columns as $column_name => $column_props ) {
  if( $column_name != $name_field )
    switch ($column_props['SimpleType']) {
      case 'varchar':
      case 'char':
      default:
        echo '
          <p class="field">
            <span class="libelle">'.to_readable($column_name).'</span>
            <span class="value"><?php echo $'.$class_db_identifier.'->get_'.$column_name.'()]?></span>
          </p>';
        break;
      case 'datetime':
      case 'time':
      case 'date':
        echo '
          <p class="field">
            <span class="libelle">'.to_readable($column_name).'</span>
            <span class="value"><?php echo guess_date($'.$class_db_identifier.'->get_'.$column_name.'(), GUESS_DATE_FR)?></span>
          </p>';
        break;
      case 'tinyint' :
        echo '
          <p class="field">
            <span class="libelle">'.to_readable($column_name).'</span>
            <span class="value"><?php echo $tab_visible[$".$class_db_identifier."->get_".$column_name."()]?></span>
          </p>';
        break;
    }
} ?>
    </div>
    <p><a href="<_?php echo get_page_url('admin_<?php echo $class_db_identifier ?>_mod', true, array('id' => $<?php echo $class_db_identifier ?>->get_id()))?>">Modifier cet <?php echo $class_name?></a></p>
  </div>
</div>