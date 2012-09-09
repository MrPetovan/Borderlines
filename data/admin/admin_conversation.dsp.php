<?php
  $PAGE_TITRE = "Administration des Conversations";
  include_once('data/static/html_functions.php');

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = Conversation::db_get_all($page_no, $nb_per_page, true);
  $nb_total = Conversation::db_count_all(true);

    echo '
<div class="texte_contenu">';

	admin_menu(PAGE_CODE);

	echo '
  <div class="texte_texte">
    <h3>Liste des Conversations</h3>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
    <table>
      <thead>
        <tr>
          <th>Sel.</th>
          <th>Id</th>
          <th>Player Id</th>
          <th>Game Id</th>
          <th>Subject</th>
          <th>Created</th>        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.get_page_url('admin_conversation_mod').'">Ajouter manuellement un objet Conversation</a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $conversation) {
      echo '
        <tr>
          <td><input type="checkbox" name="conversation_id[]" value="'.$conversation->get_id().'"/></td>
          <td><a href="'.htmlentities_utf8(get_page_url('admin_conversation_view', true, array('id' => $conversation->get_id()))).'">'.$conversation->get_id().'</a></td>
';
      $player_temp = Player::instance( $conversation->get_player_id());
      echo '
          <td>'.$player_temp->get_name().'</td>';
      $game_temp = Game::instance( $conversation->get_game_id());
      echo '
          <td>'.$game_temp->get_name().'</td>
          <td>'.$conversation->get_subject().'</td>
          <td>'.guess_time($conversation->get_created(), GUESS_DATE_FR).'</td>
          <td><a href="'.htmlentities_utf8(get_page_url('admin_conversation_mod', true, array('id' => $conversation->get_id()))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
        </tr>';
    }
    echo '
      </tbody>
    </table>
    <p>Pour les objets Conversation sélectionnés :
      <select name="action">
        <option value="delete">Delete</option>
      </select>
      <input type="submit" name="submit" value="Valider"/>
    </p>
    </form>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
  </div>
</div>';