<?php
  $PAGE_TITRE = "Administration des Games";
  include_once('data/static/html_functions.php');

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = Game::db_get_all($page_no, $nb_per_page, true);
  $nb_total = Game::db_count_all(true);

    echo '
<div class="texte_contenu">';

	admin_menu(PAGE_CODE);

	echo '
  <div class="texte_texte">
    <h3>Liste des Games</h3>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
    <table>
      <thead>
        <tr>
          <th>Sel.</th>
          <th>Name</th>
          <th>Current Turn</th>
          <th>Turn Interval</th>
          <th>Turn Limit</th>
          <th>Min Players</th>
          <th>Max Players</th>
          <th>Created</th>
          <th>Started</th>
          <th>Updated</th>
          <th>Ended</th>
          <th>Created By</th>        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.get_page_url('admin_game_mod').'">Ajouter manuellement un objet Game</a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $game) {
      echo '
        <tr>
          <td><input type="checkbox" name="game_id[]" value="'.$game->get_id().'"/></td>
          <td><a href="'.htmlentities_utf8(get_page_url('admin_game_view', true, array('id' => $game->get_id()))).'">'.$game->get_name().'</a></td>

          <td>'.$game->get_current_turn().'</td>
          <td>'.$game->get_turn_interval().'</td>
          <td>'.$game->get_turn_limit().'</td>
          <td>'.$game->get_min_players().'</td>
          <td>'.$game->get_max_players().'</td>
          <td>'.guess_time($game->get_created(), GUESS_DATE_FR).'</td>
          <td>'.guess_time($game->get_started(), GUESS_DATE_FR).'</td>
          <td>'.guess_time($game->get_updated(), GUESS_DATE_FR).'</td>
          <td>'.guess_time($game->get_ended(), GUESS_DATE_FR).'</td>';
      $player_temp = Player::instance( $game->get_created_by());
      echo '
          <td>'.$player_temp->get_name().'</td>
          <td><a href="'.htmlentities_utf8(get_page_url('admin_game_mod', true, array('id' => $game->get_id()))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
        </tr>';
    }
    echo '
      </tbody>
    </table>
    <p>Pour les objets Game sélectionnés :
      <select name="action">
        <option value="delete">Delete</option>
      </select>
      <input type="submit" name="submit" value="Valider"/>
    </p>
    </form>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
  </div>
</div>';