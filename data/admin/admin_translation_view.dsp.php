<?php
  include_once('data/static/html_functions.php');

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url(PAGE_CODE).'&id='.$translation->id;
  $PAGE_TITRE = 'Translation : Showing "'.$translation->id.'"';
?>
<div class="texte_contenu">
<?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Showing "<?php echo $translation->id?>"</h3>
    <div class="informations formulaire">

            <p class="field">
              <span class="libelle">Code</span>
              <span class="value"><?php echo $translation->code?></span>
            </p>
            <p class="field">
              <span class="libelle">Locale</span>
              <span class="value"><?php echo $translation->locale?></span>
            </p>
            <p class="field">
              <span class="libelle">Translation</span>
              <span class="value"><?php echo $translation->translation?></span>
            </p>
            <p class="field">
              <span class="libelle">Context</span>
              <span class="value"><?php echo $translation->context?></span>
            </p>    </div>
    <p><a href="<?php echo get_page_url('admin_translation_mod', true, array('id' => $translation->id))?>">Modifier cet objet Translation</a></p>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_translation')?>">Revenir à la liste des objets Translation</a></p>
  </div>
</div>