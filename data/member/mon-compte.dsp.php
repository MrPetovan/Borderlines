<?php
  $PAGE_TITRE = 'Mon compte';

  include_once('data/member/html_functions.php');

  $current_user = new Member(Member::get_current_user_id());

  if($current_user->get_niveau() == 1) {
    page_redirect('admin_member');
  }
?>
<div class="texte_contenu">
    <?php echo mon_compte_menu(PAGE_CODE) ?>
  <div class="texte_texte">
    <h3>Mon compte</h3>
    <h4>Mes identifiants</h4>
    <div class="informations formulaire">
      <p><span class="libelle">Email : </span><span class="value"><?php echo $current_user->get_email()?></span></p>
      <p><span class="libelle">Mot de passe : </span><span class="value">**********</span></p>
      <p></p>
    </div>
    <p><a class="picto_lien modifier_mdp" href="<?php echo get_page_url('mon-compte-identifiants')?>">Modifier mes identifiants</a></p>
    <h4>Mes informations personnelles</h4>
    <div class="informations formulaire">
      <p><span class="libelle">Nom : </span><span class="value"><?php echo $current_user->get_nom()?></span></p>
      <p><span class="libelle">Prénom : </span><span class="value"><?php echo $current_user->get_prenom()?></span></p>
<?php /*
      <p><span class="libelle">Code Postal : </span><span class="value"><?php echo $current_user->get_code_postal()?></span></p>
      <p><span class="libelle">Ville : </span><span class="value"><?php echo $current_user->get_ville()?></span></p>

      <p><span class="libelle">Pays : </span><span class="value"><?php echo $current_user->get_pays()?></span></p>
      <p><span class="libelle">Genre : </span><span class="value"><?php echo ($current_user->get_genre() == 'F')?'Femme':'Homme'?></span></p>
      <p><span class="libelle">Date de naissance : </span><span class="value"><?php echo date('d/m/Y', $current_user->get_date_naissance())?></span></p>
*/ ?>
      <p></p>
    </div>
    <p><a class="picto_lien modifier_infos" href="<?php echo get_page_url('mon-compte-infos')?>">Modifier mes informations personnelles</a></p>
  </div>
</div>
