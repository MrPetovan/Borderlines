<?php

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url(PAGE_CODE).'&id='.$conversation->id;
  $PAGE_TITRE = 'Conversation : Showing "'.$conversation->id.'"';
?>
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Showing "<?php echo $conversation->id?>"</h3>
    <div class="informations formulaire">

<?php
      $option_list = array();
      $player_list = Player::db_get_all();
      foreach( $player_list as $player)
        $option_list[ $player->id ] = $player->name;
?>
      <p class="field">
        <span class="libelle">Player Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_player_view', true, array('id' => $conversation->player_id ) )?>"><?php echo $option_list[ $conversation->player_id ]?></a></span>
      </p>

<?php
      $option_list = array(null => 'Pas de choix');
      $game_list = Game::db_get_all();
      foreach( $game_list as $game)
        $option_list[ $game->id ] = $game->name;
?>
      <p class="field">
        <span class="libelle">Game Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_game_view', true, array('id' => $conversation->game_id ) )?>"><?php echo $option_list[ $conversation->game_id ]?></a></span>
      </p>

            <p class="field">
              <span class="libelle">Subject</span>
              <span class="value"><?php echo is_array($conversation->subject)?nl2br(parameters_to_string( $conversation->subject )):$conversation->subject?></span>
            </p>
            <p class="field">
              <span class="libelle">Created</span>
              <span class="value"><?php echo guess_time($conversation->created, GUESS_DATETIME_LOCALE)?></span>
            </p>    </div>
    <p><a href="<?php echo get_page_url('admin_conversation_mod', true, array('id' => $conversation->id))?>">Modifier cet objet Conversation</a></p>
    <h4>Conversation Player</h4>
<?php

  $conversation_player_list = $conversation->get_conversation_player_list();

  if(count($conversation_player_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Player Id</th>
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

 
        $player_id_player = Player::instance( $conversation_player['player_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_player_view', true, array('id' => $player_id_player->id)).'">'.$player_id_player->name.'</a></td>
        <td>'.$conversation_player['archived'].'</td>
        <td>'.$conversation_player['left'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $conversation->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $conversation->id).'

              '.HTMLHelper::genererInputHidden('player_id', $player_id_player->id).'              '.HTMLHelper::genererButton('action',  'del_conversation_player', array('type' => 'submit'), 'Supprimer').'
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
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $conversation->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $conversation->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('player_id', $liste_valeurs_player, null, array(), 'Player' )?><a href="<?php echo get_page_url('admin_player_mod')?>">Créer un objet Player</a>
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
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_conversation')?>">Revenir à la liste des objets Conversation</a></p>
  </div>
</div>