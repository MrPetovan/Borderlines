<?php
  include_once('data/static/html_functions.php');

  $category = Category::instance( getValue('id') );

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url($PAGE_CODE).'&id='.$category->get_id();
  $PAGE_TITRE = 'Category : Consultation de "'.$category->get_name().'"';
?>
<div class="texte_contenu">
<?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Consultation des données pour "<?php echo $category->get_name()?>"</h3>
    <div class="informations formulaire">
    </div>
    <p><a href="<?php echo get_page_url('admin_category_mod', true, array('id' => $category->get_id()))?>">Modifier cet objet Category</a></p>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_category')?>">Revenir à la liste des objets Category</a></p>
  </div>
</div>