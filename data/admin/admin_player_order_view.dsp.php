<?php
  include_once('data/static/html_functions.php');

  $player_order = Player_Order::instance( getValue('id') );

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url($PAGE_CODE).'&id='.$player_order->get_id();
  $PAGE_TITRE = 'Player Order : Consultation de "'.$player_order->get_id().'"';
?>
<div class="texte_contenu">
<?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Consultation des données pour "<?php echo $player_order->get_id()?>"</h3>
    <div class="informations formulaire">

<?php
      $option_list = array();
      $game_list = Game::db_get_all();
      foreach( $game_list as $game)
        $option_list[ $game->id ] = $game->name;
?>
      <p class="field">
        <span class="libelle">Game Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_game_view', true, array('id' => $player_order->get_game_id() ) )?>"><?php echo $option_list[ $player_order->get_game_id() ]?></a></span>
      </p>

<?php
      $option_list = array();
      $order_type_list = Order_Type::db_get_all();
      foreach( $order_type_list as $order_type)
        $option_list[ $order_type->id ] = $order_type->name;
?>
      <p class="field">
        <span class="libelle">Order Type Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_order_type_view', true, array('id' => $player_order->get_order_type_id() ) )?>"><?php echo $option_list[ $player_order->get_order_type_id() ]?></a></span>
      </p>

<?php
      $option_list = array();
      $player_list = Player::db_get_all();
      foreach( $player_list as $player)
        $option_list[ $player->id ] = $player->name;
?>
      <p class="field">
        <span class="libelle">Player Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_player_view', true, array('id' => $player_order->get_player_id() ) )?>"><?php echo $option_list[ $player_order->get_player_id() ]?></a></span>
      </p>

            <p class="field">
              <span class="libelle">Datetime Order</span>
              <span class="value"><?php echo guess_time($player_order->get_datetime_order(), GUESS_DATE_FR)?></span>
            </p>
            <p class="field">
              <span class="libelle">Datetime Scheduled</span>
              <span class="value"><?php echo guess_time($player_order->get_datetime_scheduled(), GUESS_DATE_FR)?></span>
            </p>
            <p class="field">
              <span class="libelle">Datetime Execution</span>
              <span class="value"><?php echo guess_time($player_order->get_datetime_execution(), GUESS_DATE_FR)?></span>
            </p>
            <p class="field">
              <span class="libelle">Turn Ordered</span>
              <span class="value"><?php echo $player_order->get_turn_ordered()?></span>
            </p>
            <p class="field">
              <span class="libelle">Turn Scheduled</span>
              <span class="value"><?php echo $player_order->get_turn_scheduled()?></span>
            </p>
            <p class="field">
              <span class="libelle">Turn Executed</span>
              <span class="value"><?php echo $player_order->get_turn_executed()?></span>
            </p>
            <p class="field">
              <span class="libelle">Parameters</span>
              <span class="value"><?php echo $player_order->get_parameters()?></span>
            </p>
            <p class="field">
              <span class="libelle">Return</span>
              <span class="value"><?php echo $player_order->get_return()?></span>
            </p>    </div>
    <p><a href="<?php echo get_page_url('admin_player_order_mod', true, array('id' => $player_order->get_id()))?>">Modifier cet objet Player Order</a></p>
    <h4>Player Resource History</h4>
<?php

  $player_resource_history_list = $player_order->get_player_resource_history_list();

  if(count($player_resource_history_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Game Id</th>
          <th>Player Id</th>
          <th>Resource Id</th>
          <th>Turn</th>
          <th>Datetime</th>
          <th>Delta</th>
          <th>Reason</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8"><?php echo count( $player_resource_history_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $player_resource_history_list as $player_resource_history ) {

 
        $game_id_game = Game::instance( $player_resource_history['game_id'] );
        $player_id_player = Player::instance( $player_resource_history['player_id'] );
        $resource_id_resource = Resource::instance( $player_resource_history['resource_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_game_view', true, array('id' => $game_id_game->get_id())).'">'.$game_id_game->get_name().'</a></td>
        <td><a href="'.get_page_url('admin_player_view', true, array('id' => $player_id_player->get_id())).'">'.$player_id_player->get_name().'</a></td>
        <td><a href="'.get_page_url('admin_resource_view', true, array('id' => $resource_id_resource->get_id())).'">'.$resource_id_resource->get_name().'</a></td>
        <td>'.$player_resource_history['turn'].'</td>
        <td>'.$player_resource_history['datetime'].'</td>
        <td>'.$player_resource_history['delta'].'</td>
        <td>'.$player_resource_history['reason'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $player_order->get_id())).'" method="post">
              '.HTMLHelper::genererInputHidden('player_order_id', $player_order->get_id()).'

              '.HTMLHelper::genererInputHidden('game_id', $game_id_game->get_id()).'
              '.HTMLHelper::genererInputHidden('player_id', $player_id_player->get_id()).'
              '.HTMLHelper::genererInputHidden('resource_id', $resource_id_resource->get_id()).'              '.HTMLHelper::genererButton('action',  'del_player_resource_history', array('type' => 'submit'), 'Supprimer').'
            </form>
          </td>
        </tr>';
      }
?>
      </tbody>
    </table>
<?php
  }else {
    echo '<p>Il n\'y a pas d\'éléments à afficher</p>';
  }

  $liste_valeurs_game = Game::db_get_select_list();
  $liste_valeurs_player = Player::db_get_select_list();
  $liste_valeurs_resource = Resource::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $player_order->get_id()))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('player_order_id', $player_order->get_id() )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('game_id', $liste_valeurs_game, null, array(), 'Game' )?><a href="<?php echo get_page_url('admin_game_mod')?>">Créer un objet Game</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('player_id', $liste_valeurs_player, null, array(), 'Player' )?><a href="<?php echo get_page_url('admin_player_mod')?>">Créer un objet Player</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('resource_id', $liste_valeurs_resource, null, array(), 'Resource' )?><a href="<?php echo get_page_url('admin_resource_mod')?>">Créer un objet Resource</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('turn', null, array(), 'Turn' )?>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('datetime', null, array(), 'Datetime' )?>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('delta', null, array(), 'Delta' )?>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('reason', null, array(), 'Reason' )?>
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_player_resource_history', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_player_order')?>">Revenir à la liste des objets Player Order</a></p>
  </div>
</div>