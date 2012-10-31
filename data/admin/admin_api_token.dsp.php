<?php
  $PAGE_TITRE = "Administration des Api Tokens";

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = Api_Token::db_get_all($page_no, $nb_per_page, true);
  $nb_total = Api_Token::db_count_all(true);

    echo '
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Liste des Api Tokens</h3>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
    <form action="'.Page::get_url(PAGE_CODE).'" method="post">
    <table class="table table-condensed table-striped table-hover">
      <thead>
        <tr>
          <th>Sel.</th>
          <th>Id</th>
          <th>Hash</th>
          <th>Player Id</th>
          <th>Created</th>
          <th>Expires</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.Page::get_url('admin_api_token_mod').'">Ajouter manuellement un objet Api Token</a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $api_token) {
      echo '
        <tr>
          <td><input type="checkbox" name="api_token_id[]" value="'.$api_token->id.'"/></td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_api_token_view', array('id' => $api_token->id))).'">'.$api_token->get_id().'</a></td>

          <td>'.$api_token->hash.'</td>';
      $player_temp = Player::instance( $api_token->player_id);
      echo '
          <td>'.$player_temp->name.'</td>
          <td>'.guess_time($api_token->created, GUESS_DATETIME_LOCALE).'</td>
          <td>'.guess_time($api_token->expires, GUESS_DATETIME_LOCALE).'</td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_api_token_mod', array('id' => $api_token->id))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
        </tr>';
    }
    echo '
      </tbody>
    </table>
    <p>Pour les objets Api Token sélectionnés :
      <select name="action">
        <option value="delete">Delete</option>
      </select>
      <input type="submit" name="submit" value="Valider"/>
    </p>
    </form>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
  </div>
</div>';