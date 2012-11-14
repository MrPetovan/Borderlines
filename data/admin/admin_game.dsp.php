<?php
  $PAGE_TITRE = "Administration des Games";

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = Game::db_get_all($page_no, $nb_per_page, true);
  $nb_total = Game::db_count_all(true);

    echo '
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Liste des Games</h3>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
    <form action="'.Page::get_url(PAGE_CODE).'" method="post">
    <table class="table table-condensed table-striped table-hover">
      <thead>
        <tr>
          <th>Sel.</th>
          <th>Name</th>
          <th>Version</th>
          <th>World Id</th>
          <th>Current Turn</th>
          <th>Turn Interval</th>
          <th>Turn Limit</th>
          <th>Min Players</th>
          <th>Max Players</th>
          <th>Parameters</th>
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

          <td>'.(is_array($game->version)?nl2br(parameters_to_string($game->version)):$game->version).'</td>';
      $world_temp = World::instance( $game->world_id);
      echo '
          <td>'.$world_temp->name.'</td>
          <td>'.(is_array($game->current_turn)?nl2br(parameters_to_string($game->current_turn)):$game->current_turn).'</td>
          <td>'.(is_array($game->turn_interval)?nl2br(parameters_to_string($game->turn_interval)):$game->turn_interval).'</td>
          <td>'.(is_array($game->turn_limit)?nl2br(parameters_to_string($game->turn_limit)):$game->turn_limit).'</td>
          <td>'.(is_array($game->min_players)?nl2br(parameters_to_string($game->min_players)):$game->min_players).'</td>
          <td>'.(is_array($game->max_players)?nl2br(parameters_to_string($game->max_players)):$game->max_players).'</td>
          <td>'.(is_array($game->parameters)?nl2br(parameters_to_string($game->parameters)):$game->parameters).'</td>
          <td>'.guess_time($game->created, GUESS_DATETIME_LOCALE).'</td>
          <td>'.guess_time($game->started, GUESS_DATETIME_LOCALE).'</td>
          <td>'.guess_time($game->updated, GUESS_DATETIME_LOCALE).'</td>
          <td>'.guess_time($game->ended, GUESS_DATETIME_LOCALE).'</td>';
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