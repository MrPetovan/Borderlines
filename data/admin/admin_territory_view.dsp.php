<?php
  include_once('data/static/html_functions.php');

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url(PAGE_CODE).'&id='.$territory->id;
  $PAGE_TITRE = 'Territory : Showing "'.$territory->name.'"';
?>
<div class="texte_contenu">
<?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Showing "<?php echo $territory->name?>"</h3>
    <div class="informations formulaire">

            <p class="field">
              <span class="libelle">Capital Name</span>
              <span class="value"><?php echo $territory->capital_name?></span>
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
              <span class="value"><?php echo $territory->vertices?></span>
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
    <h4>Territory Neighbour</h4>
<?php

  $territory_neighbour_list = $territory->get_territory_neighbour_list();

  if(count($territory_neighbour_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Neighbour Id</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="2"><?php echo count( $territory_neighbour_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $territory_neighbour_list as $territory_neighbour ) {

         echo '
        <tr>
        <td>'.$territory_neighbour['neighbour_id'].'</td>          <td>
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
        <p><?php echo HTMLHelper::genererButton('action',  'set_territory_neighbour', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Territory Owner</h4>
<?php

  $territory_owner_list = $territory->get_territory_owner_list();

  if(count($territory_owner_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Game Id</th>
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

 
        $game_id_game = Game::instance( $territory_owner['game_id'] );
        $owner_id_player = Player::instance( $territory_owner['owner_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_game_view', true, array('id' => $game_id_game->id)).'">'.$game_id_game->name.'</a></td>
        <td>'.$territory_owner['turn'].'</td>
        <td><a href="'.get_page_url('admin_player_view', true, array('id' => $owner_id_player->id)).'">'.$owner_id_player->name.'</a></td>
        <td>'.$territory_owner['contested'].'</td>
        <td>'.$territory_owner['capital'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $territory->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $territory->id).'

              '.HTMLHelper::genererInputHidden('game_id', $game_id_game->id).'
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
          <?php echo HTMLHelper::genererInputText('capital', null, array(), 'Capital*' )?>
          
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_territory_owner', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Territory Player Troops</h4>
<?php

  $territory_player_troops_list = $territory->get_territory_player_troops_list();

  if(count($territory_player_troops_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Game Id</th>
          <th>Turn</th>
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

 
        $game_id_game = Game::instance( $territory_player_troops['game_id'] );
        $player_id_player = Player::instance( $territory_player_troops['player_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_game_view', true, array('id' => $game_id_game->id)).'">'.$game_id_game->name.'</a></td>
        <td>'.$territory_player_troops['turn'].'</td>
        <td><a href="'.get_page_url('admin_player_view', true, array('id' => $player_id_player->id)).'">'.$player_id_player->name.'</a></td>
        <td>'.$territory_player_troops['quantity'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $territory->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $territory->id).'

              '.HTMLHelper::genererInputHidden('game_id', $game_id_game->id).'
              '.HTMLHelper::genererInputHidden('turn', $territory_player_troops['turn']).'
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
    <p><a href="<?php echo get_page_url('admin_territory')?>">Revenir à la liste des objets Territory</a></p>
  </div>
</div>