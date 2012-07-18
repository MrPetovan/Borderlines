<?php
  include_once('data/static/html_functions.php');

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url($PAGE_CODE).'&id='.$vertex->id;
  $PAGE_TITRE = 'Vertex : Showing "'.$vertex->name.'"';
?>
<div class="texte_contenu">
<?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Showing "<?php echo $vertex->name?>"</h3>
    <div class="informations formulaire">

            <p class="field">
              <span class="libelle">Guid</span>
              <span class="value"><?php echo $vertex->guid?></span>
            </p>
            <p class="field">
              <span class="libelle">X</span>
              <span class="value"><?php echo $vertex->x?></span>
            </p>
            <p class="field">
              <span class="libelle">Y</span>
              <span class="value"><?php echo $vertex->y?></span>
            </p>    </div>
    <p><a href="<?php echo get_page_url('admin_vertex_mod', true, array('id' => $vertex->id))?>">Modifier cet objet Vertex</a></p>
    <h4>Territory Vertex</h4>
<?php

  $territory_vertex_list = $vertex->get_territory_vertex_list();

  if(count($territory_vertex_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Territory Id</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="2"><?php echo count( $territory_vertex_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $territory_vertex_list as $territory_vertex ) {

 
        $territory_id_territory = Territory::instance( $territory_vertex['territory_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_territory_view', true, array('id' => $territory_id_territory->id)).'">'.$territory_id_territory->name.'</a></td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $vertex->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $vertex->id).'

              '.HTMLHelper::genererInputHidden('territory_id', $territory_id_territory->id).'              '.HTMLHelper::genererButton('action',  'del_territory_vertex', array('type' => 'submit'), 'Supprimer').'
            </form>
          </td>
        </tr>';
      }
?>
      </tbody>
    </table>
<?php
  }else {
    echo '<p>Il n\'y a pas d\'éléments à afficher</p>';
  }

  $liste_valeurs_territory = Territory::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $vertex->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $vertex->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('territory_id', $liste_valeurs_territory, null, array(), 'Territory' )?><a href="<?php echo get_page_url('admin_territory_mod')?>">Créer un objet Territory</a>
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_territory_vertex', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_vertex')?>">Revenir à la liste des objets Vertex</a></p>
  </div>
</div>