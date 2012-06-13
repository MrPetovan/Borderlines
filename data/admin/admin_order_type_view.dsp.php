<?php
  include_once('data/static/html_functions.php');

  $order_type = Order_Type::instance( getValue('id') );

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url($PAGE_CODE).'&id='.$order_type->get_id();
  $PAGE_TITRE = 'Order Type : Consultation de "'.$order_type->get_name().'"';
?>
<div class="texte_contenu">
<?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Consultation des données pour "<?php echo $order_type->get_name()?>"</h3>
    <div class="informations formulaire">

            <p class="field">
              <span class="libelle">Class Name</span>
              <span class="value"><?php echo $order_type->get_class_name()?></span>
            </p>
            <p class="field">
              <span class="libelle">Target Player</span>
              <span class="value"><?php echo $tab_visible[$".$class_db_identifier."->get_".$column_name."()]?></span>
            </p>    </div>
    <p><a href="<?php echo get_page_url('admin_order_type_mod', true, array('id' => $order_type->get_id()))?>">Modifier cet objet Order Type</a></p>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_order_type')?>">Revenir à la liste des objets Order Type</a></p>
  </div>
</div>