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
    <form action="'.Page::get_url(PAGE_CODE).'" method="post">
    <table>
      <thead>
        <tr>
          <th>Sel.</th>
          <th>Name</th>
          <th>World Id</th>
          <th>Current Turn</th>
          <th>Turn Interval</th>
          <th>Turn Limit</th>
          <th>Min Players</th>
          <th>Max Players</th>
          <th>Created</th>
          <th>Started</th>
          <th>Updated</th>
          <th>Ended</th>
          <th>Created By</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.Page::get_url('admin_game_mod').'">Ajouter manuellement un objet Game</a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $game) {
      echo '
        <tr>
          <td><input type="checkbox" name="game_id[]" value="'.$game->id.'"/></td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_game_view', array('id' => $game->id))).'">'.$game->get_name().'</a></td>
';
      $world_temp = World::instance( $game->world_id);
      echo '
          <td>'.$world_temp->name.'</td>
          <td>'.$game->current_turn.'</td>
          <td>'.$game->turn_interval.'</td>
          <td>'.$game->turn_limit.'</td>
          <td>'.$game->min_players.'</td>
          <td>'.$game->max_players.'</td>
          <td>'.guess_time($game->created, GUESS_TIME_LOCALE).'</td>
          <td>'.guess_time($game->started, GUESS_TIME_LOCALE).'</td>
          <td>'.guess_time($game->updated, GUESS_TIME_LOCALE).'</td>
          <td>'.guess_time($game->ended, GUESS_TIME_LOCALE).'</td>';
      $player_temp = Player::instance( $game->created_by);
      echo '
          <td>'.$player_temp->name.'</td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_game_mod', array('id' => $game->id))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
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