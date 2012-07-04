<?php
  $PAGE_TITRE = "Administration des Shouts";
  include_once('data/static/html_functions.php');

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = Shout::db_get_all($page_no, $nb_per_page, true);
  $nb_total = Shout::db_count_all(true);

    echo '
<div class="texte_contenu">';

	admin_menu(PAGE_CODE);

	echo '
  <div class="texte_texte">
    <h3>Liste des Shouts</h3>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
    <table>
      <thead>
        <tr>
          <th>Sel.</th>
          <th>Id</th>
          <th>Date Sent</th>
          <th>Shouter Id</th>
          <th>Text</th>
          <th>Game Id</th>        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.get_page_url('admin_shout_mod').'">Ajouter manuellement un objet Shout</a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $shout) {
      echo '
        <tr>
          <td><input type="checkbox" name="shout_id[]" value="'.$shout->get_id().'"/></td>
          <td><a href="'.htmlentities_utf8(get_page_url('admin_shout_view', true, array('id' => $shout->get_id()))).'">'.$shout->get_id().'</a></td>

          <td>'.guess_time($shout->get_date_sent(), GUESS_DATE_FR).'</td>';
      $player_temp = Player::instance( $shout->get_shouter_id());
      echo '
          <td>'.$player_temp->get_name().'</td>
          <td>'.$shout->get_text().'</td>';
      $game_temp = Game::instance( $shout->get_game_id());
      echo '
          <td>'.$game_temp->get_name().'</td>
          <td><a href="'.htmlentities_utf8(get_page_url('admin_shout_mod', true, array('id' => $shout->get_id()))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
        </tr>';
    }
    echo '
      </tbody>
    </table>
    <p>Pour les objets Shout sélectionnés :
      <select name="action">
        <option value="delete">Delete</option>
      </select>
      <input type="submit" name="submit" value="Valider"/>
    </p>
    </form>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
  </div>
</div>';