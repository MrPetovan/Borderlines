<?php

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url(PAGE_CODE).'&id='.$player->id;
  $PAGE_TITRE = 'Player : Showing "'.$player->name.'"';
?>
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Showing "<?php echo $player->name?>"</h3>
    <div class="informations formulaire">

<?php
      $option_list = array();
      $member_list = Member::db_get_all();
      foreach( $member_list as $member)
        $option_list[ $member->id ] = $member->name;
?>
      <p class="field">
        <span class="libelle">Member Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_member_view', true, array('id' => $player->member_id ) )?>"><?php echo $option_list[ $player->member_id ]?></a></span>
      </p>

            <p class="field">
              <span class="libelle">Active</span>
              <span class="value"><?php echo $tab_visible[$player->active]?></span>
            </p>
            <p class="field">
              <span class="libelle">Api Key</span>
              <span class="value"><?php echo $player->api_key?></span>
            </p>
            <p class="field">
              <span class="libelle">Created</span>
              <span class="value"><?php echo guess_time($player->created, GUESS_DATETIME_LOCALE)?></span>
            </p>    </div>
    <p><a href="<?php echo get_page_url('admin_player_mod', true, array('id' => $player->id))?>">Modifier cet objet Player</a></p>
    <h4>Conversation Player</h4>
<?php

  $conversation_player_list = $player->get_conversation_player_list();

  if(count($conversation_player_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Conversation Id</th>
          <th>Archived</th>
          <th>Left</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="4"><?php echo count( $conversation_player_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $conversation_player_list as $conversation_player ) {

 
        $conversation_id_conversation = Conversation::instance( $conversation_player['conversation_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_conversation_view', true, array('id' => $conversation_id_conversation->id)).'">'.$conversation_id_conversation->id.'</a></td>
        <td>'.$conversation_player['archived'].'</td>
        <td>'.$conversation_player['left'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $player->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $player->id).'

              '.HTMLHelper::genererInputHidden('conversation_id', $conversation_id_conversation->id).'              '.HTMLHelper::genererButton('action',  'del_conversation_player', array('type' => 'submit'), 'Supprimer').'
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

  $liste_valeurs_conversation = Conversation::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $player->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $player->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('conversation_id', $liste_valeurs_conversation, null, array(), 'Conversation' )?><a href="<?php echo get_page_url('admin_conversation_mod')?>">Créer un objet Conversation</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('archived', null, array(), 'Archived' )?>
          <span><?php echo guess_time(time(), GUESS_TIME_MYSQL)?></span>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('left', null, array(), 'Left' )?>
          <span><?php echo guess_time(time(), GUESS_TIME_MYSQL)?></span>
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_conversation_player', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Game Player</h4>
<?php

  $game_player_list = $player->get_game_player_list();

  if(count($game_player_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Game Id</th>
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

 
        $game_id_game = Game::instance( $game_player['game_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_game_view', true, array('id' => $game_id_game->id)).'">'.$game_id_game->name.'</a></td>
        <td>'.$game_player['turn_ready'].'</td>
        <td>'.$game_player['turn_leave'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $player->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $player->id).'

              '.HTMLHelper::genererInputHidden('game_id', $game_id_game->id).'              '.HTMLHelper::genererButton('action',  'del_game_player', array('type' => 'submit'), 'Supprimer').'
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

  $liste_valeurs_game = Game::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $player->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $player->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('game_id', $liste_valeurs_game, null, array(), 'Game' )?><a href="<?php echo get_page_url('admin_game_mod')?>">Créer un objet Game</a>
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
    <h4>Message Recipient</h4>
<?php

  $message_recipient_list = $player->get_message_recipient_list();

  if(count($message_recipient_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Message Id</th>
          <th>Read</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="3"><?php echo count( $message_recipient_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $message_recipient_list as $message_recipient ) {

 
        $message_id_message = Message::instance( $message_recipient['message_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_message_view', true, array('id' => $message_id_message->id)).'">'.$message_id_message->id.'</a></td>
        <td>'.$message_recipient['read'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $player->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $player->id).'

              '.HTMLHelper::genererInputHidden('message_id', $message_id_message->id).'              '.HTMLHelper::genererButton('action',  'del_message_recipient', array('type' => 'submit'), 'Supprimer').'
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

  $liste_valeurs_message = Message::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $player->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $player->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('message_id', $liste_valeurs_message, null, array(), 'Message' )?><a href="<?php echo get_page_url('admin_message_mod')?>">Créer un objet Message</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('read', null, array(), 'Read' )?>
          <span><?php echo guess_time(time(), GUESS_TIME_MYSQL)?></span>
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_message_recipient', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Player Diplomacy</h4>
<?php

  $player_diplomacy_list = $player->get_player_diplomacy_list();

  if(count($player_diplomacy_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Game Id</th>
          <th>Turn</th>
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

 
        $game_id_game = Game::instance( $player_diplomacy['game_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_game_view', true, array('id' => $game_id_game->id)).'">'.$game_id_game->name.'</a></td>
        <td>'.$player_diplomacy['turn'].'</td>
        <td>'.$player_diplomacy['to_player_id'].'</td>
        <td>'.$player_diplomacy['status'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $player->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $player->id).'

              '.HTMLHelper::genererInputHidden('game_id', $game_id_game->id).'
              '.HTMLHelper::genererInputHidden('turn', $player_diplomacy['turn']).'
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

  $liste_valeurs_game = Game::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $player->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $player->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('game_id', $liste_valeurs_game, null, array(), 'Game' )?><a href="<?php echo get_page_url('admin_game_mod')?>">Créer un objet Game</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('turn', null, array(), 'Turn*' )?>
          
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

  $player_history_list = $player->get_player_history_list();

  if(count($player_history_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Game Id</th>
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

 
        $game_id_game = Game::instance( $player_history['game_id'] );
        $territory_id_territory = Territory::instance( $player_history['territory_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_game_view', true, array('id' => $game_id_game->id)).'">'.$game_id_game->name.'</a></td>
        <td>'.$player_history['turn'].'</td>
        <td>'.$player_history['datetime'].'</td>
        <td>'.$player_history['reason'].'</td>
        <td><a href="'.get_page_url('admin_territory_view', true, array('id' => $territory_id_territory->id)).'">'.$territory_id_territory->name.'</a></td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $player->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $player->id).'

              '.HTMLHelper::genererInputHidden('game_id', $game_id_game->id).'
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

  $liste_valeurs_game = Game::db_get_select_list();
  $liste_valeurs_territory = Territory::db_get_select_list( true );?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $player->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $player->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('game_id', $liste_valeurs_game, null, array(), 'Game' )?><a href="<?php echo get_page_url('admin_game_mod')?>">Créer un objet Game</a>
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

  $player_resource_history_list = $player->get_player_resource_history_list();

  if(count($player_resource_history_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Game Id</th>
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

 
        $game_id_game = Game::instance( $player_resource_history['game_id'] );
        $resource_id_resource = Resource::instance( $player_resource_history['resource_id'] );
        $player_order_id_player_order = Player_Order::instance( $player_resource_history['player_order_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_game_view', true, array('id' => $game_id_game->id)).'">'.$game_id_game->name.'</a></td>
        <td><a href="'.get_page_url('admin_resource_view', true, array('id' => $resource_id_resource->id)).'">'.$resource_id_resource->name.'</a></td>
        <td>'.$player_resource_history['turn'].'</td>
        <td>'.$player_resource_history['datetime'].'</td>
        <td>'.$player_resource_history['delta'].'</td>
        <td>'.$player_resource_history['reason'].'</td>
        <td><a href="'.get_page_url('admin_player_order_view', true, array('id' => $player_order_id_player_order->id)).'">'.$player_order_id_player_order->id.'</a></td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $player->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $player->id).'

              '.HTMLHelper::genererInputHidden('game_id', $game_id_game->id).'
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

  $liste_valeurs_game = Game::db_get_select_list();
  $liste_valeurs_resource = Resource::db_get_select_list();
  $liste_valeurs_player_order = Player_Order::db_get_select_list( true );?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $player->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $player->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('game_id', $liste_valeurs_game, null, array(), 'Game' )?><a href="<?php echo get_page_url('admin_game_mod')?>">Créer un objet Game</a>
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
    <h4>Player Spygame Value</h4>
<?php

  $player_spygame_value_list = $player->get_player_spygame_value_list();

  if(count($player_spygame_value_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Game Id</th>
          <th>Value Guid</th>
          <th>Turn</th>
          <th>Datetime</th>
          <th>Real Value</th>
          <th>Masked Value</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="7"><?php echo count( $player_spygame_value_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $player_spygame_value_list as $player_spygame_value ) {

 
        $game_id_game = Game::instance( $player_spygame_value['game_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_game_view', true, array('id' => $game_id_game->id)).'">'.$game_id_game->name.'</a></td>
        <td>'.$player_spygame_value['value_guid'].'</td>
        <td>'.$player_spygame_value['turn'].'</td>
        <td>'.$player_spygame_value['datetime'].'</td>
        <td>'.$player_spygame_value['real_value'].'</td>
        <td>'.$player_spygame_value['masked_value'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $player->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $player->id).'

              '.HTMLHelper::genererInputHidden('game_id', $game_id_game->id).'
              '.HTMLHelper::genererInputHidden('value_guid', $player_spygame_value['value_guid']).'
              '.HTMLHelper::genererInputHidden('turn', $player_spygame_value['turn']).'              '.HTMLHelper::genererButton('action',  'del_player_spygame_value', array('type' => 'submit'), 'Supprimer').'
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

  $liste_valeurs_game = Game::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $player->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $player->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('game_id', $liste_valeurs_game, null, array(), 'Game' )?><a href="<?php echo get_page_url('admin_game_mod')?>">Créer un objet Game</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('value_guid', null, array(), 'Value Guid*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('turn', null, array(), 'Turn*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('datetime', null, array(), 'Datetime*' )?>
          <span><?php echo guess_time(time(), GUESS_TIME_MYSQL)?></span>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('real_value', null, array(), 'Real Value*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('masked_value', null, array(), 'Masked Value' )?>
          
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_player_spygame_value', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Territory Owner</h4>
<?php

  $territory_owner_list = $player->get_territory_owner_list();

  if(count($territory_owner_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Territory Id</th>
          <th>Game Id</th>
          <th>Turn</th>
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
        $game_id_game = Game::instance( $territory_owner['game_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_territory_view', true, array('id' => $territory_id_territory->id)).'">'.$territory_id_territory->name.'</a></td>
        <td><a href="'.get_page_url('admin_game_view', true, array('id' => $game_id_game->id)).'">'.$game_id_game->name.'</a></td>
        <td>'.$territory_owner['turn'].'</td>
        <td>'.$territory_owner['contested'].'</td>
        <td>'.$territory_owner['capital'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $player->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $player->id).'

              '.HTMLHelper::genererInputHidden('territory_id', $territory_id_territory->id).'
              '.HTMLHelper::genererInputHidden('game_id', $game_id_game->id).'
              '.HTMLHelper::genererInputHidden('turn', $territory_owner['turn']).'              '.HTMLHelper::genererButton('action',  'del_territory_owner', array('type' => 'submit'), 'Supprimer').'
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
  $liste_valeurs_game = Game::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $player->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $player->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('territory_id', $liste_valeurs_territory, null, array(), 'Territory' )?><a href="<?php echo get_page_url('admin_territory_mod')?>">Créer un objet Territory</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('game_id', $liste_valeurs_game, null, array(), 'Game' )?><a href="<?php echo get_page_url('admin_game_mod')?>">Créer un objet Game</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('turn', null, array(), 'Turn*' )?>
          
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

  $territory_player_troops_list = $player->get_territory_player_troops_list();

  if(count($territory_player_troops_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Game Id</th>
          <th>Turn</th>
          <th>Territory Id</th>
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

 
        $game_id_game = Game::instance( $territory_player_troops['game_id'] );
        $territory_id_territory = Territory::instance( $territory_player_troops['territory_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_game_view', true, array('id' => $game_id_game->id)).'">'.$game_id_game->name.'</a></td>
        <td>'.$territory_player_troops['turn'].'</td>
        <td><a href="'.get_page_url('admin_territory_view', true, array('id' => $territory_id_territory->id)).'">'.$territory_id_territory->name.'</a></td>
        <td>'.$territory_player_troops['quantity'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $player->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $player->id).'

              '.HTMLHelper::genererInputHidden('game_id', $game_id_game->id).'
              '.HTMLHelper::genererInputHidden('turn', $territory_player_troops['turn']).'
              '.HTMLHelper::genererInputHidden('territory_id', $territory_id_territory->id).'              '.HTMLHelper::genererButton('action',  'del_territory_player_troops', array('type' => 'submit'), 'Supprimer').'
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
  $liste_valeurs_territory = Territory::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $player->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $player->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('game_id', $liste_valeurs_game, null, array(), 'Game' )?><a href="<?php echo get_page_url('admin_game_mod')?>">Créer un objet Game</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('turn', null, array(), 'Turn*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('territory_id', $liste_valeurs_territory, null, array(), 'Territory' )?><a href="<?php echo get_page_url('admin_territory_mod')?>">Créer un objet Territory</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('quantity', null, array(), 'Quantity*' )?>
          
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_territory_player_troops', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_player')?>">Revenir à la liste des objets Player</a></p>
  </div>
</div>