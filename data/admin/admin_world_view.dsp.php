<?php
  include_once('data/static/html_functions.php');

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url($PAGE_CODE).'&id='.$world->id;
  $PAGE_TITRE = 'World : Showing "'.$world->name.'"';
?>
<div class="texte_contenu">
<?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Showing "<?php echo $world->name?>"</h3>
    <div class="informations formulaire">

            <p class="field">
              <span class="libelle">Size X</span>
              <span class="value"><?php echo $world->size_x?></span>
            </p>
            <p class="field">
              <span class="libelle">Size Y</span>
              <span class="value"><?php echo $world->size_y?></span>
            </p>    </div>
    <p><a href="<?php echo get_page_url('admin_world_mod', true, array('id' => $world->id))?>">Modifier cet objet World</a></p>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_world')?>">Revenir à la liste des objets World</a></p>
  </div>
</div>