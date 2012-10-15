<?php
  $PAGE_TITRE = "Administration des Players";
  include_once('data/static/html_functions.php');

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = Player::db_get_all($page_no, $nb_per_page, true);
  $nb_total = Player::db_count_all(true);

    echo '
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Liste des Players</h3>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
    <form action="'.Page::get_url(PAGE_CODE).'" method="post">
    <table class="table table-condensed table-striped table-hover">
      <thead>
        <tr>
          <th>Sel.</th>
          <th>Name</th>
          <th>Member Id</th>
          <th>Active</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.Page::get_url('admin_player_mod').'">Ajouter manuellement un objet Player</a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $player) {
      echo '
        <tr>
          <td><input type="checkbox" name="player_id[]" value="'.$player->id.'"/></td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_player_view', array('id' => $player->id))).'">'.$player->get_name().'</a></td>
';
      $member_temp = Member::instance( $player->member_id);
      echo '
          <td>'.$member_temp->name.'</td>
          <td>'.$tab_visible[$player->active].'</td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_player_mod', array('id' => $player->id))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
        </tr>';
    }
    echo '
      </tbody>
    </table>
    <p>Pour les objets Player sélectionnés :
      <select name="action">
        <option value="delete">Delete</option>
      </select>
      <input type="submit" name="submit" value="Valider"/>
    </p>
    </form>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
  </div>
</div>';