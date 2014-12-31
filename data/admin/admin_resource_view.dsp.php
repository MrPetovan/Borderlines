<?php

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url(PAGE_CODE).'&id='.$resource->id;
  $PAGE_TITRE = 'Resource : Showing "'.$resource->name.'"';
?>
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Showing "<?php echo $resource->name?>"</h3>
    <div class="informations formulaire">

            <p class="field">
              <span class="libelle">Public</span>
              <span class="value"><?php echo $tab_visible[$resource->public]?></span>
            </p>    </div>
    <p><a href="<?php echo get_page_url('admin_resource_mod', true, array('id' => $resource->id))?>">Modifier cet objet Resource</a></p>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_resource')?>">Revenir à la liste des objets Resource</a></p>
  </div>
</div>