<?php
  include_once('data/static/html_functions.php');

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url($PAGE_CODE).'&id='.$resource->id;
  $PAGE_TITRE = 'Resource : Showing "'.$resource->name.'"';
?>
<div class="texte_contenu">
<?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Showing "<?php echo $resource->name?>"</h3>
    <div class="informations formulaire">

            <p class="field">
              <span class="libelle">Public</span>
              <span class="value"><?php echo $tab_visible[$resource->public]?></span>
            </p>    </div>
    <p><a href="<?php echo get_page_url('admin_resource_mod', true, array('id' => $resource->id))?>">Modifier cet objet Resource</a></p>
    <h4>Player Resource History</h4>
<?php

  $player_resource_history_list = $resource->get_player_resource_history_list();

  if(count($player_resource_history_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Game Id</th>
          <th>Player Id</th>
          <th>Turn</th>
          <th>Datetime</th>
          <th>Delta</th>
          <th>Reason</th>
          <th>Player Order Id</th>          <th>Action</th>
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
        $player_order_id_player_order = Player_Order::instance( $player_resource_history['player_order_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_game_view', true, array('id' => $game_id_game->id)).'">'.$game_id_game->name.'</a></td>
        <td><a href="'.get_page_url('admin_player_view', true, array('id' => $player_id_player->id)).'">'.$player_id_player->name.'</a></td>
        <td>'.$player_resource_history['turn'].'</td>
        <td>'.$player_resource_history['datetime'].'</td>
        <td>'.$player_resource_history['delta'].'</td>
        <td>'.$player_resource_history['reason'].'</td>
        <td><a href="'.get_page_url('admin_player_order_view', true, array('id' => $player_order_id_player_order->id)).'">'.$player_order_id_player_order->id.'</a></td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $resource->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $resource->id).'

              '.HTMLHelper::genererInputHidden('game_id', $game_id_game->id).'
              '.HTMLHelper::genererInputHidden('player_id', $player_id_player->id).'
              '.HTMLHelper::genererInputHidden('player_order_id', $player_order_id_player_order->id).'              '.HTMLHelper::genererButton('action',  'del_player_resource_history', array('type' => 'submit'), 'Supprimer').'
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
  $liste_valeurs_player_order = Player_Order::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $resource->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $resource->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('game_id', $liste_valeurs_game, null, array(), 'Game' )?><a href="<?php echo get_page_url('admin_game_mod')?>">Créer un objet Game</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('player_id', $liste_valeurs_player, null, array(), 'Player' )?><a href="<?php echo get_page_url('admin_player_mod')?>">Créer un objet Player</a>
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
        <p class="field">
          <?php echo HTMLHelper::genererSelect('player_order_id', $liste_valeurs_player_order, null, array(), 'Player Order' )?><a href="<?php echo get_page_url('admin_player_order_mod')?>">Créer un objet Player Order</a>
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_player_resource_history', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_resource')?>">Revenir à la liste des objets Resource</a></p>
  </div>
</div>