<?php
  include_once('data/static/html_functions.php');

  $vertex = Vertex::instance( getValue('id') );

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url($PAGE_CODE).'&id='.$vertex->get_id();
  $PAGE_TITRE = 'Vertex : Consultation de "'.$vertex->get_name().'"';
?>
<div class="texte_contenu">
<?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Consultation des données pour "<?php echo $vertex->get_name()?>"</h3>
    <div class="informations formulaire">

            <p class="field">
              <span class="libelle">X</span>
              <span class="value"><?php echo $vertex->get_x()?></span>
            </p>
            <p class="field">
              <span class="libelle">Y</span>
              <span class="value"><?php echo $vertex->get_y()?></span>
            </p>    </div>
    <p><a href="<?php echo get_page_url('admin_vertex_mod', true, array('id' => $vertex->get_id()))?>">Modifier cet objet Vertex</a></p>
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
        <td><a href="'.get_page_url('admin_territory_view', true, array('id' => $territory_id_territory->get_id())).'">'.$territory_id_territory->get_name().'</a></td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $vertex->get_id())).'" method="post">
              '.HTMLHelper::genererInputHidden('vertex_id', $vertex->get_id()).'

              '.HTMLHelper::genererInputHidden('territory_id', $territory_id_territory->get_id()).'              '.HTMLHelper::genererButton('action',  'del_territory_vertex', array('type' => 'submit'), 'Supprimer').'
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
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $vertex->get_id()))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('vertex_id', $vertex->get_id() )?>
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