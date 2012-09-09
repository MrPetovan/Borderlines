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
    <form action="'.Page::get_url(PAGE_CODE).'" method="post">
    <table>
      <thead>
        <tr>
          <th>Sel.</th>
          <th>Id</th>
          <th>Date Sent</th>
          <th>Shouter Id</th>
          <th>Text</th>
          <th>Game Id</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.Page::get_url('admin_shout_mod').'">Ajouter manuellement un objet Shout</a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $shout) {
      echo '
        <tr>
          <td><input type="checkbox" name="shout_id[]" value="'.$shout->id.'"/></td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_shout_view', array('id' => $shout->id))).'">'.$shout->get_id().'</a></td>

          <td>'.guess_time($shout->date_sent, GUESS_TIME_LOCALE).'</td>';
      $player_temp = Player::instance( $shout->shouter_id);
      echo '
          <td>'.$player_temp->name.'</td>
          <td>'.$shout->text.'</td>';
      $game_temp = Game::instance( $shout->game_id);
      echo '
          <td>'.$game_temp->name.'</td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_shout_mod', array('id' => $shout->id))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
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