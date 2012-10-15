<?php
  $PAGE_TITRE = "Administration des utilisateurs";

  if(isset($_GET['p'])) {
    $page = $_GET['p'];
  }else {
    $page = 1;
  }
  $nb_per_page = NB_PER_PAGE;
  $tab = Member::db_get_all($page, $nb_per_page);
  $nb_total = Member::db_count_all();

    echo '
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Liste des utilisateurs</h3>
    '.nav_page(PAGE_CODE, $nb_total, $page, $nb_per_page).'
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
    <table class="table table-condensed table-striped table-hover">
      <thead>
        <tr>
          <!--<th>Sel.</th>-->
          <th>Nom</th>
          <th>Email</th>
          <th>Niveau</th>
          <th>Inscription</th>
          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.get_page_url('admin_member_mod').'">Ajouter manuellement un utilisateur</a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    $tab_level = Member::get_tab_level();
    foreach($tab as $member) {
      echo '
        <tr>
          <!--<td><input type="checkbox" name="member_id[]" value="'.$member->get_id().'"/></td>-->
          <td><a href="'.htmlentities_utf8(get_page_url('admin_member_view', true, array('id' => $member->get_id()))).'">'.$member->get_prenom().' '.$member->get_nom().'</a></td>
          <td>'.$member->get_email().'</td>
          <td>'.$tab_level[$member->get_niveau()].'</td>
          <td>'.guess_time($member->get_date_inscription(), GUESS_DATE_FR).'</td>
          <td><a href="'.htmlentities_utf8(get_page_url('admin_member_mod', true, array('id' => $member->get_id()))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
        </tr>';
    }
    echo '
      </tbody>
    </table>
    </form>
    '.nav_page(PAGE_CODE, $nb_total, $page, $nb_per_page).'
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
      <p>'.HTMLHelper::hidden('export_csv_type', 'liste_membres').
          HTMLHelper::submit('export_csv', 'Exporter la liste des utilisateurs au format Excel').'</p>
    </form>
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
      <p>'.HTMLHelper::hidden('export_csv_type', 'actifs').
          HTMLHelper::submit('export_csv', 'Exporter la liste des actifs').'</p>
    </form>
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
      <p>'.HTMLHelper::hidden('export_csv_type', 'actifs_0-14j').
          HTMLHelper::submit('export_csv', 'Exporter la liste des actifs à J-14 au format Excel').'</p>
    </form>
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
      <p>'.HTMLHelper::hidden('export_csv_type', 'actifs_14-21j').
          HTMLHelper::submit('export_csv', 'Exporter la liste des actifs de J-14 à J-21 au format Excel').'</p>
    </form>
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
      <p>'.HTMLHelper::hidden('export_csv_type', 'actifs_+28j').
          HTMLHelper::submit('export_csv', 'Exporter la liste des actifs de plus de J-28 au format Excel').'</p>
    </form>
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
      <p>'.HTMLHelper::hidden('export_csv_type', 'abonnement_courts_Jplus1').
          HTMLHelper::submit('export_csv', 'Exporter la liste des abonnements courts à échéance J+1 au format Excel').'</p>
    </form>
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
      <p>'.HTMLHelper::hidden('export_csv_type', 'prospect_test_ou_lettre_3-6j').
          HTMLHelper::submit('export_csv', 'Exporter la liste des prospects (lettre info ou test) de J-6 à J-3 au format Excel').'</p>
    </form>
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
      <p>'.HTMLHelper::hidden('export_csv_type', 'prospect_test_ou_lettre_6-9j').
          HTMLHelper::submit('export_csv', 'Exporter la liste des prospects (lettre info ou test) de J-9 à J-6 au format Excel').'</p>
    </form>
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
      <p>'.HTMLHelper::hidden('export_csv_type', 'prospect_test_ou_lettre_9-12j').
          HTMLHelper::submit('export_csv', 'Exporter la liste des prospects (lettre info ou test) de J-12 à J-9 au format Excel').'</p>
    </form>
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
      <p>'.HTMLHelper::hidden('export_csv_type', 'prospect_test_ou_lettre_12-16j').
          HTMLHelper::submit('export_csv', 'Exporter la liste des prospects (lettre info ou test) de J-16 à J-12 au format Excel').'</p>
    </form>
  </div>
</div>';
?>
