<?php
  include_once('data/static/html_functions.php');

  if(isset($_GET['id'])) {
    $id = $_GET['id'];
    $member = new Member($id);
  }else {
    $id = null;
    $member = new Member();
  }

  $tab_level = Member::get_tab_level();

  $form_url = get_page_url($PAGE_CODE).'&id='.$id;
  $PAGE_TITRE = 'Utilisateur : Consultation de "'.$member->get_prenom().' '.$member->get_nom().'"';
?>
<!--<div class="texte_header">
  <p class="bandeau">Administration</p>
  <img src="<?php echo IMG?>img_html/13login_header.jpg" alt=""/>
  <div class="edito">
    <h2>Administration des utilisateurs</h2>
    <p>Vous pouvez modifier les données des utilisateurs. Attention, les changements sont irréversibles.</p>
  </div>
</div>-->
<div class="texte_contenu">
<?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Consultation des données pour "<?php echo $member->get_prenom().' '.$member->get_nom()?>"</h3>
    <div class="informations formulaire">
      <p class="field">
        <span class="libelle">Niveau</span>
        <span class="value"><?php echo $tab_level[$member->get_niveau()]?></span>
      </p>
      <p class="field">
        <span class="libelle">Genre</span>
        <span class="value"><?php echo $member->get_genre()?></span>
      </p>
      <p class="field">
        <span class="libelle">Nom</span>
        <span class="value"><?php echo $member->get_prenom().' '.$member->get_nom()?></span>
      </p>
      <p class="field">
        <span class="libelle">Email</span>
        <span class="value"><?php echo $member->get_email()?></span>
      </p>
      <p class="field">
        <span class="libelle">Date inscription</span>
        <span class="value"><?php echo guess_time($member->get_date_inscription(), GUESS_DATE_FR)?></span>
      </p>
      <p class="field">
        <span class="libelle">Date de naissance</span>
        <span class="value"><?php echo guess_time($member->get_date_naissance(), GUESS_DATE_FR)?></span>
      </p>
      <p class="field">
        <span class="libelle">Origine</span>
        <span class="value"><?php echo $member->get_origin()?></span>
      </p>
      <p></p>
    </div>
    <p><a href="<?php echo get_page_url('admin_member_mod', true, array('id' => $member->get_id()))?>">Modifier cet utilisateur</a></p>
  </div>
</div>
