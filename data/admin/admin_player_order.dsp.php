<?php
  $PAGE_TITRE = "Administration des Player Orders";

  $page_no = getValue('p', 1);
  $nb_per_page = NB_PER_PAGE;
  $tab = Player_Order::db_get_all($page_no, $nb_per_page, true);
  $nb_total = Player_Order::db_count_all(true);

    echo '
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Liste des Player Orders</h3>
    '.nav_page(PAGE_CODE, $nb_total, $page_no, $nb_per_page).'
    <form action="'.Page::get_url(PAGE_CODE).'" method="post">
    <table class="table table-condensed table-striped table-hover">
      <thead>
        <tr>
          <th>Sel.</th>
          <th>Id</th>
          <th>Game Id</th>
          <th>Order Type Id</th>
          <th>Player Id</th>
          <th>Datetime Order</th>
          <th>Datetime Execution</th>
          <th>Turn Ordered</th>
          <th>Turn Scheduled</th>
          <th>Turn Executed</th>
          <th>Parameters</th>
          <th>Return</th>
          <th>Parent Player Order Id</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.$nb_total.' éléments | <a href="'.Page::get_url('admin_player_order_mod').'">Ajouter manuellement un objet Player Order</a></td>
        </tr>
      </tfoot>
      <tbody>';
    $tab_visible = array('0' => 'Non', '1' => 'Oui');
    foreach($tab as $player_order) {
      echo '
        <tr>
          <td><input type="checkbox" name="player_order_id[]" value="'.$player_order->id.'"/></td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_player_order_view', array('id' => $player_order->id))).'">'.$player_order->get_id().'</a></td>
';
      $game_temp = Game::instance( $player_order->game_id);
      echo '
          <td>'.$game_temp->name.'</td>';
      $order_type_temp = Order_Type::instance( $player_order->order_type_id);
      echo '
          <td>'.$order_type_temp->name.'</td>';
      $player_temp = Player::instance( $player_order->player_id);
      echo '
          <td>'.$player_temp->name.'</td>
          <td>'.guess_time($player_order->datetime_order, GUESS_DATETIME_LOCALE).'</td>
          <td>'.guess_time($player_order->datetime_execution, GUESS_DATETIME_LOCALE).'</td>
          <td>'.(is_array($player_order->turn_ordered)?nl2br(parameters_to_string($player_order->turn_ordered)):$player_order->turn_ordered).'</td>
          <td>'.(is_array($player_order->turn_scheduled)?nl2br(parameters_to_string($player_order->turn_scheduled)):$player_order->turn_scheduled).'</td>
          <td>'.(is_array($player_order->turn_executed)?nl2br(parameters_to_string($player_order->turn_executed)):$player_order->turn_executed).'</td>
          <td>'.(is_array($player_order->parameters)?nl2br(parameters_to_string($player_order->parameters)):$player_order->parameters).'</td>
          <td>'.(is_array($player_order->return)?nl2br(parameters_to_string($player_order->return)):$player_order->return).'</td>';
      $player_order_temp = Player_Order::instance( $player_order->parent_player_order_id);
      echo '
          <td>'.$player_order_temp->name.'</td>
          <td><a href="'.htmlentities_utf8(Page::get_url('admin_player_order_mod', array('id' => $player_order->id))).'"><img src="'.IMG.'img_html/pencil.png" alt="Modifier" title="Modifier"/></a></td>
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