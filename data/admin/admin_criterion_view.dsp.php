<?php

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url(PAGE_CODE).'&id='.$criterion->id;
  $PAGE_TITRE = 'Criterion : Showing "'.$criterion->name.'"';
?>
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Showing "<?php echo $criterion->name?>"</h3>
    <div class="informations formulaire">

<?php
      $option_list = array();
      $category_list = Category::db_get_all();
      foreach( $category_list as $category)
        $option_list[ $category->id ] = $category->name;
?>
      <p class="field">
        <span class="libelle">Category Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_category_view', true, array('id' => $criterion->category_id ) )?>"><?php echo $option_list[ $criterion->category_id ]?></a></span>
      </p>
    </div>
    <p><a href="<?php echo get_page_url('admin_criterion_mod', true, array('id' => $criterion->id))?>">Modifier cet objet Criterion</a></p>
    <h4>Territory Criterion</h4>
<?php

  $territory_criterion_list = $criterion->get_territory_criterion_list();

  if(count($territory_criterion_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Territory Id</th>
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

 
        $territory_id_territory = Territory::instance( $territory_criterion['territory_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_territory_view', true, array('id' => $territory_id_territory->id)).'">'.$territory_id_territory->name.'</a></td>
        <td>'.$territory_criterion['percentage'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $criterion->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $criterion->id).'

              '.HTMLHelper::genererInputHidden('territory_id', $territory_id_territory->id).'              '.HTMLHelper::genererButton('action',  'del_territory_criterion', array('type' => 'submit'), 'Supprimer').'
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
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $criterion->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $criterion->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('territory_id', $liste_valeurs_territory, null, array(), 'Territory' )?><a href="<?php echo get_page_url('admin_territory_mod')?>">Créer un objet Territory</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('percentage', null, array(), 'Percentage*' )?>
          
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_territory_criterion', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_criterion')?>">Revenir à la liste des objets Criterion</a></p>
  </div>
</div>