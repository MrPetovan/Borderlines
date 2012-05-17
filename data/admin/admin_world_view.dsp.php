<?php
  include_once('data/static/html_functions.php');

  $world = World::instance( getValue('id') );

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url($PAGE_CODE).'&id='.$world->get_id();
  $PAGE_TITRE = 'World : Consultation de "'.$world->get_name().'"';
?>
<div class="texte_contenu">
<?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Consultation des données pour "<?php echo $world->get_name()?>"</h3>
    <div class="informations formulaire">
    </div>
    <p><a href="<?php echo get_page_url('admin_world_mod', true, array('id' => $world->get_id()))?>">Modifier cet objet World</a></p>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_world')?>">Revenir à la liste des objets World</a></p>
  </div>
</div>