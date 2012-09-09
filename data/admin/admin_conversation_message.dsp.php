<?php
  $PAGE_TITRE = "Administration des Conversation Messages";
  include_once('data/static/html_functions.php');

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = Conversation_Message::db_get_all($page_no, $nb_per_page, true);
  $nb_total = Conversation_Message::db_count_all(true);

    echo '
<div class="texte_contenu">';

	admin_menu(PAGE_CODE);

	echo '
  <div class="texte_texte">
    <h3>Liste des Conversation Messages</h3>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
    <table>
      <thead>
        <tr>
          <th>Sel.</th>
          <th>Id</th>
          <th>Conversation Id</th>
          <th>Sender Id</th>
          <th>Receiver Id</th>
          <th>Text</th>
          <th>Created</th>        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.get_page_url('admin_conversation_message_mod').'">Ajouter manuellement un objet Conversation Message</a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $conversation_message) {
      echo '
        <tr>
          <td><input type="checkbox" name="conversation_message_id[]" value="'.$conversation_message->get_id().'"/></td>
          <td><a href="'.htmlentities_utf8(get_page_url('admin_conversation_message_view', true, array('id' => $conversation_message->get_id()))).'">'.$conversation_message->get_id().'</a></td>
';
      $conversation_temp = Conversation::instance( $conversation_message->get_conversation_id());
      echo '
          <td>'.$conversation_temp->get_name().'</td>
          <td>'.$conversation_message->get_sender_id().'</td>';
      $player_temp = Player::instance( $conversation_message->get_receiver_id());
      echo '
          <td>'.$player_temp->get_name().'</td>
          <td>'.$conversation_message->get_text().'</td>
          <td>'.guess_time($conversation_message->get_created(), GUESS_DATE_FR).'</td>
          <td><a href="'.htmlentities_utf8(get_page_url('admin_conversation_message_mod', true, array('id' => $conversation_message->get_id()))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
        </tr>';
    }
    echo '
      </tbody>
    </table>
    <p>Pour les objets Conversation Message sélectionnés :
      <select name="action">
        <option value="delete">Delete</option>
      </select>
      <input type="submit" name="submit" value="Valider"/>
    </p>
    </form>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
  </div>
</div>';