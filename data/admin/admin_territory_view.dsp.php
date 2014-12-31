<?php

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url(PAGE_CODE).'&id='.$territory->id;
  $PAGE_TITRE = 'Territory : Showing "'.$territory->name.'"';
?>
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Showing "<?php echo $territory->name?>"</h3>
    <div class="informations formulaire">

            <p class="field">
              <span class="libelle">Capital Name</span>
              <span class="value"><?php echo is_array($territory->capital_name)?nl2br(parameters_to_string( $territory->capital_name )):$territory->capital_name?></span>
            </p>
<?php
      $option_list = array();
      $world_list = World::db_get_all();
      foreach( $world_list as $world)
        $option_list[ $world->id ] = $world->name;
?>
      <p class="field">
        <span class="libelle">World Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_world_view', true, array('id' => $territory->world_id ) )?>"><?php echo $option_list[ $territory->world_id ]?></a></span>
      </p>

            <p class="field">
              <span class="libelle">Vertices</span>
              <span class="value"><?php echo is_array($territory->vertices)?nl2br(parameters_to_string( $territory->vertices )):$territory->vertices?></span>
            </p>
            <p class="field">
              <span class="libelle">Passable</span>
              <span class="value"><?php echo $tab_visible[$territory->passable]?></span>
            </p>
            <p class="field">
              <span class="libelle">Capturable</span>
              <span class="value"><?php echo $tab_visible[$territory->capturable]?></span>
            </p>
            <p class="field">
              <span class="libelle">Background</span>
              <span class="value"><?php echo is_array($territory->background)?nl2br(parameters_to_string( $territory->background )):$territory->background?></span>
            </p>    </div>
    <p><a href="<?php echo get_page_url('admin_territory_mod', true, array('id' => $territory->id))?>">Modifier cet objet Territory</a></p>
    <h4>Player History</h4>
<?php

  $player_history_list = $territory->get_player_history_list();

  if(count($player_history_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Game Id</th>
          <th>Player Id</th>
          <th>Turn</th>
          <th>Datetime</th>
          <th>Reason</th>          <th>Action</th>
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
        $player_id_player = Player::instance( $player_history['player_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_game_view', true, array('id' => $game_id_game->id)).'">'.$game_id_game->name.'</a></td>
        <td><a href="'.get_page_url('admin_player_view', true, array('id' => $player_id_player->id)).'">'.$player_id_player->name.'</a></td>
        <td>'.$player_history['turn'].'</td>
        <td>'.$player_history['datetime'].'</td>
        <td>'.$player_history['reason'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $territory->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $territory->id).'

              '.HTMLHelper::genererInputHidden('game_id', $game_id_game->id).'
              '.HTMLHelper::genererInputHidden('player_id', $player_id_player->id).'              '.HTMLHelper::genererButton('action',  'del_player_history', array('type' => 'submit'), 'Supprimer').'
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
  $liste_valeurs_player = Player::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $territory->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $territory->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('game_id', $liste_valeurs_game, null, array(), 'Game' )?><a href="<?php echo get_page_url('admin_game_mod')?>">Créer un objet Game</a>
        </p>
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
        <p><?php echo HTMLHelper::genererButton('action',  'set_player_history', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Territory Criterion</h4>
<?php

  $territory_criterion_list = $territory->get_territory_criterion_list();

  if(count($territory_criterion_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Criterion Id</th>
          <th>Percentage</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="3"><?php echo count( $territory_criterion_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $territory_criterion_list as $territory_criterion ) {

 
        $criterion_id_criterion = Criterion::instance( $territory_criterion['criterion_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_criterion_view', true, array('id' => $criterion_id_criterion->id)).'">'.$criterion_id_criterion->name.'</a></td>
        <td>'.$territory_criterion['percentage'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $territory->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $territory->id).'

              '.HTMLHelper::genererInputHidden('criterion_id', $criterion_id_criterion->id).'              '.HTMLHelper::genererButton('action',  'del_territory_criterion', array('type' => 'submit'), 'Supprimer').'
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

  $liste_valeurs_criterion = Criterion::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $territory->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $territory->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('criterion_id', $liste_valeurs_criterion, null, array(), 'Criterion' )?><a href="<?php echo get_page_url('admin_criterion_mod')?>">Créer un objet Criterion</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('percentage', null, array(), 'Percentage*' )?>
          
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_territory_criterion', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Territory Economy History</h4>
<?php

  $territory_economy_history_list = $territory->get_territory_economy_history_list();

  if(count($territory_economy_history_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Game Id</th>
          <th>Turn</th>
          <th>Delta</th>
          <th>Reason</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5"><?php echo count( $territory_economy_history_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $territory_economy_history_list as $territory_economy_history ) {

 
        $game_id_game = Game::instance( $territory_economy_history['game_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_game_view', true, array('id' => $game_id_game->id)).'">'.$game_id_game->name.'</a></td>
        <td>'.$territory_economy_history['turn'].'</td>
        <td>'.$territory_economy_history['delta'].'</td>
        <td>'.$territory_economy_history['reason'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $territory->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $territory->id).'

              '.HTMLHelper::genererInputHidden('game_id', $game_id_game->id).'              '.HTMLHelper::genererButton('action',  'del_territory_economy_history', array('type' => 'submit'), 'Supprimer').'
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
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $territory->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $territory->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('game_id', $liste_valeurs_game, null, array(), 'Game' )?><a href="<?php echo get_page_url('admin_game_mod')?>">Créer un objet Game</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('turn', null, array(), 'Turn*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('delta', null, array(), 'Delta*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('reason', null, array(), 'Reason*' )?>
          
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_territory_economy_history', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Territory Neighbour</h4>
<?php

  $territory_neighbour_list = $territory->get_territory_neighbour_list();

  if(count($territory_neighbour_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Neighbour Id</th>
          <th>Guid1</th>
          <th>Guid2</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="4"><?php echo count( $territory_neighbour_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $territory_neighbour_list as $territory_neighbour ) {

         echo '
        <tr>
        <td>'.$territory_neighbour['neighbour_id'].'</td>
        <td>'.$territory_neighbour['guid1'].'</td>
        <td>'.$territory_neighbour['guid2'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $territory->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $territory->id).'

              '.HTMLHelper::genererInputHidden('neighbour_id', $territory_neighbour['neighbour_id']).'              '.HTMLHelper::genererButton('action',  'del_territory_neighbour', array('type' => 'submit'), 'Supprimer').'
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
?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $territory->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $territory->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('neighbour_id', null, array(), 'Neighbour Id*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('guid1', null, array(), 'Guid1*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('guid2', null, array(), 'Guid2*' )?>
          
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_territory_neighbour', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Territory Player Status</h4>
<?php

  $territory_player_status_list = $territory->get_territory_player_status_list();

  if(count($territory_player_status_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Game Id</th>
          <th>Turn</th>
          <th>Player Id</th>
          <th>Supremacy</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5"><?php echo count( $territory_player_status_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $territory_player_status_list as $territory_player_status ) {

 
        $game_id_game = Game::instance( $territory_player_status['game_id'] );
        $player_id_player = Player::instance( $territory_player_status['player_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_game_view', true, array('id' => $game_id_game->id)).'">'.$game_id_game->name.'</a></td>
        <td>'.$territory_player_status['turn'].'</td>
        <td><a href="'.get_page_url('admin_player_view', true, array('id' => $player_id_player->id)).'">'.$player_id_player->name.'</a></td>
        <td>'.$territory_player_status['supremacy'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $territory->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $territory->id).'

              '.HTMLHelper::genererInputHidden('game_id', $game_id_game->id).'
              '.HTMLHelper::genererInputHidden('turn', $territory_player_status['turn']).'
              '.HTMLHelper::genererInputHidden('player_id', $player_id_player->id).'              '.HTMLHelper::genererButton('action',  'del_territory_player_status', array('type' => 'submit'), 'Supprimer').'
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
  $liste_valeurs_player = Player::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $territory->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $territory->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('game_id', $liste_valeurs_game, null, array(), 'Game' )?><a href="<?php echo get_page_url('admin_game_mod')?>">Créer un objet Game</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('turn', null, array(), 'Turn*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('player_id', $liste_valeurs_player, null, array(), 'Player' )?><a href="<?php echo get_page_url('admin_player_mod')?>">Créer un objet Player</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('supremacy', null, array(), 'Supremacy*' )?>
          
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_territory_player_status', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Territory Player Troops History</h4>
<?php

  $territory_player_troops_history_list = $territory->get_territory_player_troops_history_list();

  if(count($territory_player_troops_history_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Game Id</th>
          <th>Turn</th>
          <th>Player Id</th>
          <th>Delta</th>
          <th>Reason</th>
          <th>Reason Player Id</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="7"><?php echo count( $territory_player_troops_history_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $territory_player_troops_history_list as $territory_player_troops_history ) {

 
        $game_id_game = Game::instance( $territory_player_troops_history['game_id'] );
        $player_id_player = Player::instance( $territory_player_troops_history['player_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_game_view', true, array('id' => $game_id_game->id)).'">'.$game_id_game->name.'</a></td>
        <td>'.$territory_player_troops_history['turn'].'</td>
        <td><a href="'.get_page_url('admin_player_view', true, array('id' => $player_id_player->id)).'">'.$player_id_player->name.'</a></td>
        <td>'.$territory_player_troops_history['delta'].'</td>
        <td>'.$territory_player_troops_history['reason'].'</td>
        <td>'.$territory_player_troops_history['reason_player_id'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $territory->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $territory->id).'

              '.HTMLHelper::genererInputHidden('game_id', $game_id_game->id).'
              '.HTMLHelper::genererInputHidden('turn', $territory_player_troops_history['turn']).'
              '.HTMLHelper::genererInputHidden('player_id', $player_id_player->id).'
              '.HTMLHelper::genererInputHidden('reason_player_id', $territory_player_troops_history['reason_player_id']).'              '.HTMLHelper::genererButton('action',  'del_territory_player_troops_history', array('type' => 'submit'), 'Supprimer').'
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
  $liste_valeurs_player = Player::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $territory->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $territory->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('game_id', $liste_valeurs_game, null, array(), 'Game' )?><a href="<?php echo get_page_url('admin_game_mod')?>">Créer un objet Game</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('turn', null, array(), 'Turn*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('player_id', $liste_valeurs_player, null, array(), 'Player' )?><a href="<?php echo get_page_url('admin_player_mod')?>">Créer un objet Player</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('delta', null, array(), 'Delta*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('reason', null, array(), 'Reason*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('reason_player_id', null, array(), 'Reason Player Id' )?>
          
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_territory_player_troops_history', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Territory Status</h4>
<?php

  $territory_status_list = $territory->get_territory_status_list();

  if(count($territory_status_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Game Id</th>
          <th>Turn</th>
          <th>Owner Id</th>
          <th>Contested</th>
          <th>Conflict</th>
          <th>Capital</th>
          <th>Revenue Suppression</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8"><?php echo count( $territory_status_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $territory_status_list as $territory_status ) {

 
        $game_id_game = Game::instance( $territory_status['game_id'] );
        $owner_id_player = Player::instance( $territory_status['owner_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_game_view', true, array('id' => $game_id_game->id)).'">'.$game_id_game->name.'</a></td>
        <td>'.$territory_status['turn'].'</td>
        <td><a href="'.get_page_url('admin_player_view', true, array('id' => $owner_id_player->id)).'">'.$owner_id_player->name.'</a></td>
        <td>'.$territory_status['contested'].'</td>
        <td>'.$territory_status['conflict'].'</td>
        <td>'.$territory_status['capital'].'</td>
        <td>'.$territory_status['revenue_suppression'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $territory->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $territory->id).'

              '.HTMLHelper::genererInputHidden('game_id', $game_id_game->id).'
              '.HTMLHelper::genererInputHidden('turn', $territory_status['turn']).'
              '.HTMLHelper::genererInputHidden('player_id', $owner_id_player->id).'              '.HTMLHelper::genererButton('action',  'del_territory_status', array('type' => 'submit'), 'Supprimer').'
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
  $liste_valeurs_player = Player::db_get_select_list( true );?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $territory->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $territory->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('game_id', $liste_valeurs_game, null, array(), 'Game' )?><a href="<?php echo get_page_url('admin_game_mod')?>">Créer un objet Game</a>
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
          <?php echo HTMLHelper::genererInputText('conflict', null, array(), 'Conflict*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('capital', null, array(), 'Capital*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('revenue_suppression', null, array(), 'Revenue Suppression*' )?>
          
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_territory_status', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_territory')?>">Revenir à la liste des objets Territory</a></p>
  </div>
</div>