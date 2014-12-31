<?php
  $PAGE_TITRE = "Administration des Messages";

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = Message::db_get_all($page_no, $nb_per_page, true);
  $nb_total = Message::db_count_all(true);

    echo '
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Liste des Messages</h3>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
    <form action="'.Page::get_url(PAGE_CODE).'" method="post">
    <table class="table table-condensed table-striped table-hover">
      <thead>
        <tr>
          <th>Sel.</th>
          <th>Id</th>
          <th>Conversation Id</th>
          <th>Player Id</th>
          <th>Text</th>
          <th>Created</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.Page::get_url('admin_message_mod').'">Ajouter manuellement un objet Message</a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $message) {
      echo '
        <tr>
          <td><input type="checkbox" name="message_id[]" value="'.$message->id.'"/></td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_message_view', array('id' => $message->id))).'">'.$message->get_id().'</a></td>
';
      $conversation_temp = Conversation::instance( $message->conversation_id);
      echo '
          <td>'.$conversation_temp->name.'</td>';
      $player_temp = Player::instance( $message->player_id);
      echo '
          <td>'.$player_temp->name.'</td>
          <td>'.(is_array($message->text)?nl2br(parameters_to_string($message->text)):$message->text).'</td>
          <td>'.guess_time($message->created, GUESS_DATETIME_LOCALE).'</td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_message_mod', array('id' => $message->id))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
        </tr>';
    }
    echo '
      </tbody>
    </table>
    <p>Pour les objets Message sélectionnés :
      <select name="action">
        <option value="delete">Delete</option>
      </select>
      <input type="submit" name="submit" value="Valider"/>
    </p>
    </form>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
  </div>
</div>';