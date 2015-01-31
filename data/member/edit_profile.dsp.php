<?php
  $PAGE_TITRE = 'Modifier mes informations personnelles';

  include_once('data/member/html_functions.php');

  $flag_show_form = true;
  $html_msg = '';

  if(!isset($member)) {
    $member = Member::get_current_user();
  }

  if(!$member) {
    page_redirect('abonnement');
  }

  if(isset($tab_error)) {
    if($tab_error === true) {
      $html_msg = '<div class="msg">Vos nouvelles informations personnelles ont été correctement enregistrées.</div>';
    }else {
      foreach ($tab_error as $error) {
        $tab_msg[] = Member::get_message_erreur($error);
      }
      $tab_msg = array_unique($tab_msg);
      $html_msg = '<div class="error">';
      foreach ($tab_msg as $msg_error) {
        $html_msg .= '
  <p>'.wash_utf8($msg_error).'</p>';
      }
      $html_msg .= '</div>';
    }
  }
?>
<div class="texte_header">
  <p class="bandeau">Mon compte</p>
  <div class="edito">
    <h2>Mon compte</h2>
    <p>Retrouvez ici toutes les informations concernant vos informations et votre abonnement.</p>
  </div>
</div>
<div class="texte_contenu">
<?php echo mon_compte_menu(PAGE_CODE, Member::get_logged_user()) ?>
  <div class="texte_texte">
    <h3>Modifier mes informations personnelles</h3>
    <?php echo $html_msg; ?>
    <h4>Informations personnelles</h4>
    <p class="texte_intro">Laissez les champs vides si vous ne voulez pas changer les informations.</p>
    <form id="edit_infos_form" action="<?php echo get_page_url(get_current_page())?>" method="post">
      <div class="informations formulaire">
        <p class="radio_line"><label class="full_width">&nbsp;</label>
<?php echo
          HTMLHelper::genererInputRadio('genre', 'F', $member->get_genre(), array('id' => 'radio_genre_mme', 'checked' => 'checked', 'label_position' => 'right'), "Mme" ).
          HTMLHelper::genererInputRadio('genre', 'M', $member->get_genre(), array('id' => 'radio_genre_mlle', 'label_position' => 'right'), "Mlle" ).
          HTMLHelper::genererInputRadio('genre', 'H', $member->get_genre(), array('id' => 'radio_genre_m', 'label_position' => 'right'), "M" )
?>
        </p>
        <div class="field form-group"><?php echo HTMLHelper::genererInputText('prenom', $member->get_prenom(), array(), "Prénom*" );?></div>
        <div class="field form-group"><?php echo HTMLHelper::genererInputText('nom', $member->get_nom(), array(), "Nom*" );?></div>
<?php
      $liste_jour = array('' => '--');
      for($i = 1; $i <= 31; $i ++) {
        $n = ($i < 10)? '0'.$i : $i;
        $liste_jour[$n] = $n;
      }

      $liste_mois = array('' => '--');
      for($i = 1; $i <= 12; $i ++) {
        $n = ($i < 10)? '0'.$i : $i;
        $liste_mois[$n] = $n;
      }

      $liste_annee = array('' => '----');
      for($i = date('Y'); $i > 1900; $i --) {
        $liste_annee[$i] = $i;
      }

      $date_naiss = null;
      $date_naiss_jour = null;
      $date_naiss_mois = null;
      $date_naiss_annee = null;

      if($member->get_date_naissance()) {
        $date_naiss = date('d/m/Y', $member->get_date_naissance());
        list($date_naiss_jour, $date_naiss_mois, $date_naiss_annee) = explode('/',$date_naiss);
      }
?>
        <div class="field form-group">
          <label>Date de naissance <span class="oblig">*</span></label>
          <?php echo HTMLHelper::genererSelect('date_naissance_jour', $liste_jour, $date_naiss_jour).
       HTMLHelper::genererSelect('date_naissance_mois', $liste_mois, $date_naiss_mois).
       HTMLHelper::genererSelect('date_naissance_annee', $liste_annee, $date_naiss_annee)?>
        </div>
        <p></p>
      </div>
      <p class="right"><label>&nbsp;</label><?php echo HTMLHelper::genererInputSubmit('save_profile', "Save changes" );?></p>
      </form>
<?php /*
      <h4>Adresse</h4>

      <p class="texte_intro">Laissez les champs vides si vous ne voulez pas changer les informations.</p>
      <form id="edit_infos_form" action="<?php echo get_page_url(get_current_page())?>" method="post">
      <div class="informations formulaire">
        <div class="field form-group"><?php echo HTMLHelper::genererInputText('adresse', $member->get_adresse(), array(), "Adresse*" );?></div>
        <div class="field form-group"><?php echo HTMLHelper::genererInputText('code_postal', $member->get_code_postal(), array(), "Code Postal*" );?></div>
        <div class="field form-group"><?php echo HTMLHelper::genererInputText('ville', $member->get_ville(), array(), "Ville*" );?></div>
        <div class="field form-group"><?php
          $liste_pays = array('France' => 'France');
          echo HTMLHelper::genererSelect('pays', $liste_pays, $member->get_pays(), array(), 'Pays*');?>
        </div>
      </div>
      <p class="right"><label>&nbsp;</label><?php echo HTMLHelper::genererInputSubmit('save_profile', "Save changes" );?></p>
      </form>
      <h4>Vos abonnements</h4>
      <form id="edit_infos_form" action="<?php echo get_page_url(get_current_page())?>" method="post">
      <?php echo HTMLHelper::hidden('change_inscr', '1')?>
      <div class="informations formulaire">
        <p><?php echo
          HTMLHelper::hidden('inscr_newsletter_old', $member->get_inscr_newsletter()).
          HTMLHelper::genererInputCheckBox('inscr_newsletter', '1', $member->get_inscr_newsletter(), array('label_position' => 'right'), 'Informations et offres de notre part.')?>
        </p>
        <p><?php echo
          HTMLHelper::hidden('inscr_partner_old', $member->get_inscr_partner()).
          HTMLHelper::genererInputCheckBox('inscr_partner', '1', $member->get_inscr_partner(), array('label_position' => 'right'), 'Informations et offres de la part de nos partenaires.')?>
        </p>
      </div>
      <p class="right"><label>&nbsp;</label><?php echo HTMLHelper::genererInputSubmit('save_profile', "Save changes" );?></p>
    </form>
*/ ?>
  </div>
</div>