<?php
  $PAGE_TITRE = "Administration des Player Orders";
  include_once('data/static/html_functions.php');

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = Player_Order::db_get_all($page_no, $nb_per_page, true);
  $nb_total = Player_Order::db_count_all(true);

    echo '
<div class="texte_contenu">';

	admin_menu(PAGE_CODE);

	echo '
  <div class="texte_texte">
    <h3>Liste des Player Orders</h3>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
    <form action="'.get_page_url(PAGE_CODE).'" method="post">
    <table>
      <thead>
        <tr>
          <th>Sel.</th>
          <th>Id</th>
          <th>Order Type Id</th>
          <th>Player Id</th>
          <th>Datetime Order</th>
          <th>Datetime Scheduled</th>
          <th>Datetime Execution</th>
          <th>Parameters</th>
          <th>Return</th>        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.get_page_url('admin_player_order_mod').'">Ajouter manuellement un objet Player Order</a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $player_order) {
      echo '
        <tr>
          <td><input type="checkbox" name="player_order_id[]" value="'.$player_order->get_id().'"/></td>
          <td><a href="'.htmlentities_utf8(get_page_url('admin_player_order_view', true, array('id' => $player_order->get_id()))).'">'.$player_order->get_id().'</a></td>
';
      $order_type_temp = Order_Type::instance( $player_order->get_order_type_id());
      echo '
          <td>'.$order_type_temp->get_name().'</td>';
      $player_temp = Player::instance( $player_order->get_player_id());
      echo '
          <td>'.$player_temp->get_name().'</td>
          <td>'.$player_order->get_datetime_order().'</td>
          <td>'.$player_order->get_datetime_scheduled().'</td>
          <td>'.$player_order->get_datetime_execution().'</td>
          <td>'.$player_order->get_parameters().'</td>
          <td>'.$player_order->get_return().'</td>
          <td><a href="'.htmlentities_utf8(get_page_url('admin_player_order_mod', true, array('id' => $player_order->get_id()))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
        </tr>';
    }
    echo '
      </tbody>
    </table>
    <p>Pour les objets Player Order sélectionnés :
      <select name="action">
        <option value="delete">Delete</option>
      </select>
      <input type="submit" name="submit" value="Valider"/>
    </p>
    </form>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
  </div>
</div>';