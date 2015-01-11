<?php

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url(PAGE_CODE).'&id='.$message->id;
  $PAGE_TITRE = 'Message : Showing "'.$message->id.'"';
?>
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Showing "<?php echo $message->id?>"</h3>
    <div class="informations formulaire">

<?php
      $option_list = array();
      $conversation_list = Conversation::db_get_all();
      foreach( $conversation_list as $conversation)
        $option_list[ $conversation->id ] = $conversation->name;
?>
      <p class="field">
        <span class="libelle">Conversation Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_conversation_view', true, array('id' => $message->conversation_id ) )?>"><?php echo $option_list[ $message->conversation_id ]?></a></span>
      </p>

<?php
      $option_list = array(null => 'Pas de choix');
      $player_list = Player::db_get_all();
      foreach( $player_list as $player)
        $option_list[ $player->id ] = $player->name;
?>
      <p class="field">
        <span class="libelle">Player Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_player_view', true, array('id' => $message->player_id ) )?>"><?php echo $option_list[ $message->player_id ]?></a></span>
      </p>

            <p class="field">
              <span class="libelle">Text</span>
              <span class="value"><?php echo is_array($message->text)?nl2br(parameters_to_string( $message->text )):$message->text?></span>
            </p>
            <p class="field">
              <span class="libelle">Created</span>
              <span class="value"><?php echo guess_time($message->created, GUESS_DATETIME_LOCALE)?></span>
            </p>    </div>
    <p><a href="<?php echo get_page_url('admin_message_mod', true, array('id' => $message->id))?>">Modifier cet objet Message</a></p>
    <h4>Message Recipient</h4>
<?php

  $message_recipient_list = $message->get_message_recipient_list();

  if(count($message_recipient_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Player Id</th>
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

 
        $player_id_player = Player::instance( $message_recipient['player_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_player_view', true, array('id' => $player_id_player->id)).'">'.$player_id_player->name.'</a></td>
        <td>'.$message_recipient['read'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $message->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $message->id).'

              '.HTMLHelper::genererInputHidden('player_id', $player_id_player->id).'              '.HTMLHelper::genererButton('action',  'del_message_recipient', array('type' => 'submit'), 'Supprimer').'
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
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $message->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $message->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('player_id', $liste_valeurs_player, null, array(), 'Player' )?><a href="<?php echo get_page_url('admin_player_mod')?>">Créer un objet Player</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('read', null, array(), 'Read' )?>
          <span><?php echo guess_time(time(), GUESS_TIME_MYSQL)?></span>
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_message_recipient', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_message')?>">Revenir à la liste des objets Message</a></p>
  </div>
</div>