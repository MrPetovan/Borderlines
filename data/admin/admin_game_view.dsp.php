<?php

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url(PAGE_CODE).'&id='.$game->id;
  $PAGE_TITRE = 'Game : Showing "'.$game->name.'"';
?>
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Showing "<?php echo $game->name?>"</h3>
    <div class="informations formulaire">

<?php
      $option_list = array();
      $world_list = World::db_get_all();
      foreach( $world_list as $world)
        $option_list[ $world->id ] = $world->name;
?>
      <p class="field">
        <span class="libelle">World Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_world_view', true, array('id' => $game->world_id ) )?>"><?php echo $option_list[ $game->world_id ]?></a></span>
      </p>

            <p class="field">
              <span class="libelle">Current Turn</span>
              <span class="value"><?php echo $game->current_turn?></span>
            </p>
            <p class="field">
              <span class="libelle">Turn Interval</span>
              <span class="value"><?php echo $game->turn_interval?></span>
            </p>
            <p class="field">
              <span class="libelle">Turn Limit</span>
              <span class="value"><?php echo $game->turn_limit?></span>
            </p>
            <p class="field">
              <span class="libelle">Min Players</span>
              <span class="value"><?php echo $game->min_players?></span>
            </p>
            <p class="field">
              <span class="libelle">Max Players</span>
              <span class="value"><?php echo $game->max_players?></span>
            </p>
            <p class="field">
              <span class="libelle">Created</span>
              <span class="value"><?php echo guess_time($game->created, GUESS_DATETIME_LOCALE)?></span>
            </p>
            <p class="field">
              <span class="libelle">Started</span>
              <span class="value"><?php echo guess_time($game->started, GUESS_DATETIME_LOCALE)?></span>
            </p>
            <p class="field">
              <span class="libelle">Updated</span>
              <span class="value"><?php echo guess_time($game->updated, GUESS_DATETIME_LOCALE)?></span>
            </p>
            <p class="field">
              <span class="libelle">Ended</span>
              <span class="value"><?php echo guess_time($game->ended, GUESS_DATETIME_LOCALE)?></span>
            </p>
<?php
      $option_list = array();
      $player_list = Player::db_get_all();
      foreach( $player_list as $player)
        $option_list[ $player->id ] = $player->name;
?>
      <p class="field">
        <span class="libelle">Created By</span>
        <span class="value"><a href="<?php echo get_page_url('admin_player_view', true, array('id' => $game->created_by ) )?>"><?php echo $option_list[ $game->created_by ]?></a></span>
      </p>
    </div>
    <p><a href="<?php echo get_page_url('admin_game_mod', true, array('id' => $game->id))?>">Modifier cet objet Game</a></p>
    <h4>Game Player</h4>
<?php

  $game_player_list = $game->get_game_player_list();

  if(count($game_player_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Player Id</th>
          <th>Turn Ready</th>
          <th>Turn Leave</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="4"><?php echo count( $game_player_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $game_player_list as $game_player ) {

 
        $player_id_player = Player::instance( $game_player['player_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_player_view', true, array('id' => $player_id_player->id)).'">'.$player_id_player->name.'</a></td>
        <td>'.$game_player['turn_ready'].'</td>
        <td>'.$game_player['turn_leave'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $game->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $game->id).'

              '.HTMLHelper::genererInputHidden('player_id', $player_id_player->id).'              '.HTMLHelper::genererButton('action',  'del_game_player', array('type' => 'submit'), 'Supprimer').'
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

  $liste_valeurs_player = Player::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $game->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $game->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('player_id', $liste_valeurs_player, null, array(), 'Player' )?><a href="<?php echo get_page_url('admin_player_mod')?>">Créer un objet Player</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('turn_ready', null, array(), 'Turn Ready*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('turn_leave', null, array(), 'Turn Leave' )?>
          
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_game_player', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Player Diplomacy</h4>
<?php

  $player_diplomacy_list = $game->get_player_diplomacy_list();

  if(count($player_diplomacy_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Turn</th>
          <th>From Player Id</th>
          <th>To Player Id</th>
          <th>Status</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5"><?php echo count( $player_diplomacy_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $player_diplomacy_list as $player_diplomacy ) {

 
        $from_player_id_player = Player::instance( $player_diplomacy['from_player_id'] );        echo '
        <tr>
        <td>'.$player_diplomacy['turn'].'</td>
        <td><a href="'.get_page_url('admin_player_view', true, array('id' => $from_player_id_player->id)).'">'.$from_player_id_player->name.'</a></td>
        <td>'.$player_diplomacy['to_player_id'].'</td>
        <td>'.$player_diplomacy['status'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $game->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $game->id).'

              '.HTMLHelper::genererInputHidden('turn', $player_diplomacy['turn']).'
              '.HTMLHelper::genererInputHidden('player_id', $from_player_id_player->id).'
              '.HTMLHelper::genererInputHidden('to_player_id', $player_diplomacy['to_player_id']).'              '.HTMLHelper::genererButton('action',  'del_player_diplomacy', array('type' => 'submit'), 'Supprimer').'
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

  $liste_valeurs_player = Player::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $game->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $game->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('turn', null, array(), 'Turn*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('from_player_id', $liste_valeurs_player, null, array(), 'Player' )?><a href="<?php echo get_page_url('admin_player_mod')?>">Créer un objet Player</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('to_player_id', null, array(), 'To Player Id*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('status', null, array(), 'Status*' )?>
          
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_player_diplomacy', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Player History</h4>
<?php

  $player_history_list = $game->get_player_history_list();

  if(count($player_history_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Player Id</th>
          <th>Turn</th>
          <th>Datetime</th>
          <th>Reason</th>
          <th>Territory Id</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6"><?php echo count( $player_history_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $player_history_list as $player_history ) {

 
        $player_id_player = Player::instance( $player_history['player_id'] );
        $territory_id_territory = Territory::instance( $player_history['territory_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_player_view', true, array('id' => $player_id_player->id)).'">'.$player_id_player->name.'</a></td>
        <td>'.$player_history['turn'].'</td>
        <td>'.$player_history['datetime'].'</td>
        <td>'.$player_history['reason'].'</td>
        <td><a href="'.get_page_url('admin_territory_view', true, array('id' => $territory_id_territory->id)).'">'.$territory_id_territory->name.'</a></td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $game->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $game->id).'

              '.HTMLHelper::genererInputHidden('player_id', $player_id_player->id).'
              '.HTMLHelper::genererInputHidden('territory_id', $territory_id_territory->id).'              '.HTMLHelper::genererButton('action',  'del_player_history', array('type' => 'submit'), 'Supprimer').'
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

  $liste_valeurs_player = Player::db_get_select_list();
  $liste_valeurs_territory = Territory::db_get_select_list( true );?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $game->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $game->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('player_id', $liste_valeurs_player, null, array(), 'Player' )?><a href="<?php echo get_page_url('admin_player_mod')?>">Créer un objet Player</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('turn', null, array(), 'Turn*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('datetime', null, array(), 'Datetime*' )?>
          <span><?php echo guess_time(time(), GUESS_TIME_MYSQL)?></span>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('reason', null, array(), 'Reason*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('territory_id', $liste_valeurs_territory, null, array(), 'Territory' )?><a href="<?php echo get_page_url('admin_territory_mod')?>">Créer un objet Territory</a>
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_player_history', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Player Resource History</h4>
<?php

  $player_resource_history_list = $game->get_player_resource_history_list();

  if(count($player_resource_history_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Player Id</th>
          <th>Resource Id</th>
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

 
        $player_id_player = Player::instance( $player_resource_history['player_id'] );
        $resource_id_resource = Resource::instance( $player_resource_history['resource_id'] );
        $player_order_id_player_order = Player_Order::instance( $player_resource_history['player_order_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_player_view', true, array('id' => $player_id_player->id)).'">'.$player_id_player->name.'</a></td>
        <td><a href="'.get_page_url('admin_resource_view', true, array('id' => $resource_id_resource->id)).'">'.$resource_id_resource->name.'</a></td>
        <td>'.$player_resource_history['turn'].'</td>
        <td>'.$player_resource_history['datetime'].'</td>
        <td>'.$player_resource_history['delta'].'</td>
        <td>'.$player_resource_history['reason'].'</td>
        <td><a href="'.get_page_url('admin_player_order_view', true, array('id' => $player_order_id_player_order->id)).'">'.$player_order_id_player_order->id.'</a></td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $game->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $game->id).'

              '.HTMLHelper::genererInputHidden('player_id', $player_id_player->id).'
              '.HTMLHelper::genererInputHidden('resource_id', $resource_id_resource->id).'
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

  $liste_valeurs_player = Player::db_get_select_list();
  $liste_valeurs_resource = Resource::db_get_select_list();
  $liste_valeurs_player_order = Player_Order::db_get_select_list( true );?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $game->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $game->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('player_id', $liste_valeurs_player, null, array(), 'Player' )?><a href="<?php echo get_page_url('admin_player_mod')?>">Créer un objet Player</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('resource_id', $liste_valeurs_resource, null, array(), 'Resource' )?><a href="<?php echo get_page_url('admin_resource_mod')?>">Créer un objet Resource</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('turn', null, array(), 'Turn*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('datetime', null, array(), 'Datetime*' )?>
          <span><?php echo guess_time(time(), GUESS_TIME_MYSQL)?></span>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('delta', null, array(), 'Delta*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('reason', null, array(), 'Reason*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('player_order_id', $liste_valeurs_player_order, null, array(), 'Player Order' )?><a href="<?php echo get_page_url('admin_player_order_mod')?>">Créer un objet Player Order</a>
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_player_resource_history', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Territory Owner</h4>
<?php

  $territory_owner_list = $game->get_territory_owner_list();

  if(count($territory_owner_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Territory Id</th>
          <th>Turn</th>
          <th>Owner Id</th>
          <th>Contested</th>
          <th>Capital</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6"><?php echo count( $territory_owner_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $territory_owner_list as $territory_owner ) {

 
        $territory_id_territory = Territory::instance( $territory_owner['territory_id'] );
        $owner_id_player = Player::instance( $territory_owner['owner_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_territory_view', true, array('id' => $territory_id_territory->id)).'">'.$territory_id_territory->name.'</a></td>
        <td>'.$territory_owner['turn'].'</td>
        <td><a href="'.get_page_url('admin_player_view', true, array('id' => $owner_id_player->id)).'">'.$owner_id_player->name.'</a></td>
        <td>'.$territory_owner['contested'].'</td>
        <td>'.$territory_owner['capital'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $game->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $game->id).'

              '.HTMLHelper::genererInputHidden('territory_id', $territory_id_territory->id).'
              '.HTMLHelper::genererInputHidden('turn', $territory_owner['turn']).'
              '.HTMLHelper::genererInputHidden('player_id', $owner_id_player->id).'              '.HTMLHelper::genererButton('action',  'del_territory_owner', array('type' => 'submit'), 'Supprimer').'
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

  $liste_valeurs_territory = Territory::db_get_select_list();
  $liste_valeurs_player = Player::db_get_select_list( true );?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $game->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $game->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('territory_id', $liste_valeurs_territory, null, array(), 'Territory' )?><a href="<?php echo get_page_url('admin_territory_mod')?>">Créer un objet Territory</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('turn', null, array(), 'Turn*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('owner_id', $liste_valeurs_player, null, array(), 'Player' )?><a href="<?php echo get_page_url('admin_player_mod')?>">Créer un objet Player</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('contested', null, array(), 'Contested*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('capital', null, array(), 'Capital*' )?>
          
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_territory_owner', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Territory Player Troops</h4>
<?php

  $territory_player_troops_list = $game->get_territory_player_troops_list();

  if(count($territory_player_troops_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Turn</th>
          <th>Territory Id</th>
          <th>Player Id</th>
          <th>Quantity</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5"><?php echo count( $territory_player_troops_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $territory_player_troops_list as $territory_player_troops ) {

 
        $territory_id_territory = Territory::instance( $territory_player_troops['territory_id'] );
        $player_id_player = Player::instance( $territory_player_troops['player_id'] );        echo '
        <tr>
        <td>'.$territory_player_troops['turn'].'</td>
        <td><a href="'.get_page_url('admin_territory_view', true, array('id' => $territory_id_territory->id)).'">'.$territory_id_territory->name.'</a></td>
        <td><a href="'.get_page_url('admin_player_view', true, array('id' => $player_id_player->id)).'">'.$player_id_player->name.'</a></td>
        <td>'.$territory_player_troops['quantity'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $game->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $game->id).'

              '.HTMLHelper::genererInputHidden('turn', $territory_player_troops['turn']).'
              '.HTMLHelper::genererInputHidden('territory_id', $territory_id_territory->id).'
              '.HTMLHelper::genererInputHidden('player_id', $player_id_player->id).'              '.HTMLHelper::genererButton('action',  'del_territory_player_troops', array('type' => 'submit'), 'Supprimer').'
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

  $liste_valeurs_territory = Territory::db_get_select_list();
  $liste_valeurs_player = Player::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $game->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $game->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('turn', null, array(), 'Turn*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('territory_id', $liste_valeurs_territory, null, array(), 'Territory' )?><a href="<?php echo get_page_url('admin_territory_mod')?>">Créer un objet Territory</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('player_id', $liste_valeurs_player, null, array(), 'Player' )?><a href="<?php echo get_page_url('admin_player_mod')?>">Créer un objet Player</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('quantity', null, array(), 'Quantity*' )?>
          
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_territory_player_troops', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
<?php
  // CUSTOM

?>
    <p><a href="<?php echo Page::get_page_url( PAGE_CODE, false, array('action' => 'revert', 'id' => $game->id, 'turn' => $game->current_turn - 1 ) )?>">Revert to previous turn</a></p>
    <p><a href="<?php echo Page::get_page_url( PAGE_CODE, false, array('action' => 'reset', 'id' => $game->id ) )?>">Reset game</a></p>
    <p><a href="<?php echo Page::get_page_url( PAGE_CODE, false, array('action' => 'start', 'id' => $game->id ) )?>">Start game</a></p>
    <p><a href="<?php echo Page::get_page_url( PAGE_CODE, false, array('action' => 'compute', 'id' => $game->id ) )?>">Compute orders</a></p>
<?php

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_game')?>">Revenir à la liste des objets Game</a></p>
  </div>
</div>