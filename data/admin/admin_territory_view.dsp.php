<?php
  include_once('data/static/html_functions.php');

  $territory = Territory::instance( getValue('id') );

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url($PAGE_CODE).'&id='.$territory->get_id();
  $PAGE_TITRE = 'Territory : Consultation de "'.$territory->get_name().'"';
?>
<div class="texte_contenu">
<?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Consultation des données pour "<?php echo $territory->get_name()?>"</h3>
    <div class="informations formulaire">

<?php
      $option_list = array();
      $world_list = World::db_get_all();
      foreach( $world_list as $world)
        $option_list[ $world->id ] = $world->name;
?>
      <p class="field">
        <span class="libelle">World Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_world_view', true, array('id' => $territory->get_world_id() ) )?>"><?php echo $option_list[ $territory->get_world_id() ]?></a></span>
      </p>
    </div>
    <p><a href="<?php echo get_page_url('admin_territory_mod', true, array('id' => $territory->get_id()))?>">Modifier cet objet Territory</a></p>
    <h4>Territory Criterion</h4>
<?php

  $territory_criterion_list = $territory->get_territory_criterion_list();

  if(count($territory_criterion_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Criterion Id</th>
          <th>Percentage</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="3"><?php echo count( $territory_criterion_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $territory_criterion_list as $territory_criterion ) {

 
        $criterion_id_criterion = Criterion::instance( $territory_criterion['criterion_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_criterion_view', true, array('id' => $criterion_id_criterion->get_id())).'">'.$criterion_id_criterion->get_name().'</a></td>
        <td>'.$territory_criterion['percentage'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $territory->get_id())).'" method="post">
              '.HTMLHelper::genererInputHidden('territory_id', $territory->get_id()).'

              '.HTMLHelper::genererInputHidden('criterion_id', $criterion_id_criterion->get_id()).'              '.HTMLHelper::genererButton('action',  'del_territory_criterion', array('type' => 'submit'), 'Supprimer').'
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

  $liste_valeurs_criterion = Criterion::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $territory->get_id()))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('territory_id', $territory->get_id() )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('criterion_id', $liste_valeurs_criterion, null, array(), 'Criterion' )?><a href="<?php echo get_page_url('admin_criterion_mod')?>">Créer un objet Criterion</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('percentage', null, array(), 'Percentage' )?>
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_territory_criterion', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Territory Neighbour</h4>
<?php

  $territory_neighbour_list = $territory->get_territory_neighbour_list();

  if(count($territory_neighbour_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Neighbour Id</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="2"><?php echo count( $territory_neighbour_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $territory_neighbour_list as $territory_neighbour ) {

         echo '
        <tr>
        <td>'.$territory_neighbour['neighbour_id'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $territory->get_id())).'" method="post">
              '.HTMLHelper::genererInputHidden('territory_id', $territory->get_id()).'

              '.HTMLHelper::genererInputHidden('neighbour_id', $territory_neighbour['neighbour_id']).'              '.HTMLHelper::genererButton('action',  'del_territory_neighbour', array('type' => 'submit'), 'Supprimer').'
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
?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $territory->get_id()))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('territory_id', $territory->get_id() )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('neighbour_id', null, array(), 'Neighbour Id' )?>
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_territory_neighbour', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Territory Vertex</h4>
<?php

  $territory_vertex_list = $territory->get_territory_vertex_list();

  if(count($territory_vertex_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Vertex Id</th>          <th>Action</th>
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

 
        $vertex_id_vertex = Vertex::instance( $territory_vertex['vertex_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_vertex_view', true, array('id' => $vertex_id_vertex->get_id())).'">'.$vertex_id_vertex->get_name().'</a></td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $territory->get_id())).'" method="post">
              '.HTMLHelper::genererInputHidden('territory_id', $territory->get_id()).'

              '.HTMLHelper::genererInputHidden('vertex_id', $vertex_id_vertex->get_id()).'              '.HTMLHelper::genererButton('action',  'del_territory_vertex', array('type' => 'submit'), 'Supprimer').'
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

  $liste_valeurs_vertex = Vertex::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $territory->get_id()))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('territory_id', $territory->get_id() )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('vertex_id', $liste_valeurs_vertex, null, array(), 'Vertex' )?><a href="<?php echo get_page_url('admin_vertex_mod')?>">Créer un objet Vertex</a>
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_territory_vertex', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_territory')?>">Revenir à la liste des objets Territory</a></p>
  </div>
</div>