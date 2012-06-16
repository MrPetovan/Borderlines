  include_once('data/static/html_functions.php');

  $<?php echo $class_db_identifier ?> = <?php echo $class_php_identifier ?>::instance( getValue('id') );

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url($PAGE_CODE).'&id='.$<?php echo $class_db_identifier ?>->get_id();
  $PAGE_TITRE = '<?php echo $class_name ?> : Consultation de "'.$<?php echo $class_db_identifier ?>->get_<?php echo $name_field?>().'"';
?>
<div class="texte_contenu">
<_?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Consultation des données pour "<_?php echo $<?php echo $class_db_identifier ?>->get_<?php echo $name_field?>()?>"</h3>
    <div class="informations formulaire">
<?php
foreach( $table_columns as $column_name => $column_props ) {
  if( $column_name != 'id') {
    if(array_key_exists( $column_name, $foreign_keys) ) {
      $foreign_table = $foreign_keys[$column_name];
      echo '
<_?php
      $option_list = array('.($column_props['Null'] != 'NO'?'null => \'Pas de choix\'':'').');
      $'.$foreign_table.'_list = '.to_camel_case($foreign_table, true).'::db_get_all();
      foreach( $'.$foreign_table.'_list as $'.$foreign_table.')
        $option_list[ $'.$foreign_table.'->id ] = $'.$foreign_table.'->name;
?>';
?>

      <p class="field">
        <span class="libelle"><?php echo $column_props['Comment'] ?></span>
        <span class="value"><a href="<_?php echo get_page_url('admin_<?php echo $foreign_table ?>_view', true, array('id' => $<?php echo $class_db_identifier ?>->get_<?php echo $column_name ?>() ) )?>"><_?php echo $option_list[ $<?php echo $class_db_identifier ?>->get_<?php echo $column_name ?>() ]?></a></span>
      </p>
<?php
    }elseif( $column_name != $name_field )
      switch ($column_props['SimpleType']) {
        case 'varchar':
        case 'char':
        default:
          echo '
            <p class="field">
              <span class="libelle">'.$column_props['Comment'].'</span>
              <span class="value"><?php echo $'.$class_db_identifier.'->get_'.$column_name.'()?></span>
            </p>';
          break;
        case 'datetime':
        case 'time':
        case 'date':
          echo '
            <p class="field">
              <span class="libelle">'.$column_props['Comment'].'</span>
              <span class="value"><?php echo guess_time($'.$class_db_identifier.'->get_'.$column_name.'(), GUESS_DATE_FR)?></span>
            </p>';
          break;
        case 'tinyint' :
          echo '
            <p class="field">
              <span class="libelle">'.$column_props['Comment'].'</span>
              <span class="value"><?php echo $tab_visible[$".$class_db_identifier."->get_".$column_name."()]?></span>
            </p>';
          break;
      }
  }
} ?>
    </div>
    <p><a href="<_?php echo get_page_url('admin_<?php echo $class_db_identifier ?>_mod', true, array('id' => $<?php echo $class_db_identifier ?>->get_id()))?>">Modifier cet objet <?php echo $class_name?></a></p>
<?php
  foreach( $sub_tables as $sub_table_info ) {
    $sub_table = $sub_table_info['table'];
    $sub_table_field = $sub_table_info['field'];
    $sub_table_pk = $primary_keys[ $sub_table ];
    $sub_table_pk_clean = array_diff($sub_table_pk, array($sub_table_field));
    $sub_table_columns = $table_columns_list[ $sub_table ];
    $sub_table_columns_clean = $sub_table_columns;
    unset($sub_table_columns_clean[ $sub_table_field] );
    $sub_table_description = $table_description[ $sub_table ];

    $th_list = '';
    foreach( $sub_table_columns_clean as $field_name => $field ) {
      $th_list .= '
          <th>'.$table_columns_list[$sub_table][$field_name]['Comment'].'</th>';
    }

?>
    <h4><?php echo $sub_table_description ?></h4>
<_?php

  $<?php echo $sub_table?>_list = $<?php echo $class_db_identifier ?>->get_<?php echo $sub_table?>_list();

  if(count($<?php echo $sub_table?>_list)) {
?>
    <table>
      <thead>
        <tr><?php echo $th_list?>
          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="<?php echo count($table_columns_list[ $sub_table ])?>"><_?php echo count( $<?php echo $sub_table?>_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<_?php
      foreach( $<?php echo $sub_table?>_list as $<?php echo $sub_table?> ) {

 <?php
    $td_list = '';
    foreach( $sub_table_columns_clean as $field_name => $field ) {
      if ( array_key_exists( $field_name,  $foreign_keys_list[ $sub_table ] )) {
        $foreign_table = $foreign_keys_list[ $sub_table ][ $field_name ];
        echo '
        $'. $field_name. '_' . $foreign_table .' = '.to_camel_case( $foreign_table, true).'::instance( $'.$sub_table.'[\''.$field_name.'\'] );';
        $td_list .= '
        <td><a href="\'.get_page_url(\'admin_'.$foreign_table.'_view\', true, array(\'id\' => $'. $field_name. '_' . $foreign_table .'->get_id())).\'">\'.$'. $field_name. '_' . $foreign_table .'->get_'.$name_field_list[ $foreign_table ].'().\'</a></td>';
      }else {
        $td_list .= '
        <td>\'.$'.$sub_table.'[\''.$field_name.'\'].\'</td>';
      }
    }
?>
        echo '
        <tr><?php echo $td_list?>
          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $<?php echo $class_db_identifier ?>->get_id())).'" method="post">
              '.HTMLHelper::genererInputHidden('<?php echo $class_db_identifier ?>_id', $<?php echo $class_db_identifier ?>->get_id()).'
<?php
    foreach( $sub_table_pk_clean as $field_name ) {
      if ( array_key_exists( $field_name,  $foreign_keys_list[ $sub_table ] )) {
        $foreign_table = $foreign_keys_list[ $sub_table ][ $field_name ];
        echo '
              \'.HTMLHelper::genererInputHidden(\''.$foreign_table.'_id\', $'. $field_name. '_' . $foreign_table .'->get_id()).\'';
      }else {
        echo '
              \'.HTMLHelper::genererInputHidden(\''.$field_name.'\', $'.$sub_table.'[\''.$field_name.'\']).\'';
      }
    }
?>
              '.HTMLHelper::genererButton('action',  'del_<?php echo $sub_table?>', array('type' => 'submit'), 'Supprimer').'
            </form>
          </td>
        </tr>';
      }
?>
      </tbody>
    </table>
<_?php
  }else {
    echo '<p>Il n\'y a pas d\'éléments à afficher</p>';
  }
<?php
  $form_field = '';
  foreach( $sub_table_columns_clean as $field_name => $field) {
    if ( array_key_exists( $field_name,  $foreign_keys_list[ $sub_table ] )) {
      $foreign_table = $foreign_keys_list[ $sub_table ][ $field_name ];
      echo '
  $liste_valeurs_'.$foreign_table.' = '.to_camel_case( $foreign_table, true ).'::db_get_select_list();';
      $form_field .= '
        <p class="field">
          <_?php echo HTMLHelper::genererSelect(\''.$field_name.'\', $liste_valeurs_'.$foreign_table.', null, array(), \''.$table_description[ $foreign_table ].'\' )?><a href="<_?php echo get_page_url(\'admin_'.$foreign_table.'_mod\')?>">Créer un objet '.to_readable($foreign_table).'</a>
        </p>';
    }else {
      $form_field .= '
        <p class="field">
          <_?php echo HTMLHelper::genererInputText(\''.$field_name.'\', null, array(), \''.$table_columns_list[$sub_table][$field_name]['Comment'].'\' )?>
        </p>';
    }
  }

?>
?>
    <form action="<_?php echo get_page_url(PAGE_CODE, true, array('id' => $<?php echo $class_db_identifier ?>->get_id()))?>" method="post" class="formulaire">
      <_?php echo HTMLHelper::genererInputHidden('<?php echo $class_db_identifier ?>_id', $<?php echo $class_db_identifier ?>->get_id() )?>
      <fieldset>
        <legend>Ajouter un élément</legend><?php echo $form_field?>

        <p><_?php echo HTMLHelper::genererButton('action',  'set_<?php echo $sub_table?>', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
<?php
  }
?>
<_?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<_?php echo get_page_url('admin_<?php echo $class_db_identifier?>')?>">Revenir à la liste des objets <?php echo $class_name ?></a></p>
  </div>
</div>