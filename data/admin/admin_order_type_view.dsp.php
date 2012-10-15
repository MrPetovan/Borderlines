<?php
  include_once('data/static/html_functions.php');

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url(PAGE_CODE).'&id='.$order_type->id;
  $PAGE_TITRE = 'Order Type : Showing "'.$order_type->name.'"';
?>
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Showing "<?php echo $order_type->name?>"</h3>
    <div class="informations formulaire">

            <p class="field">
              <span class="libelle">Class Name</span>
              <span class="value"><?php echo $order_type->class_name?></span>
            </p>
            <p class="field">
              <span class="libelle">Active</span>
              <span class="value"><?php echo $tab_visible[$order_type->active]?></span>
            </p>    </div>
    <p><a href="<?php echo get_page_url('admin_order_type_mod', true, array('id' => $order_type->id))?>">Modifier cet objet Order Type</a></p>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_order_type')?>">Revenir à la liste des objets Order Type</a></p>
  </div>
</div>