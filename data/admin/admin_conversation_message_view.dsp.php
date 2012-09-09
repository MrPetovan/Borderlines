<?php
  include_once('data/static/html_functions.php');

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url($PAGE_CODE).'&id='.$conversation_message->id;
  $PAGE_TITRE = 'Conversation Message : Showing "'.$conversation_message->id.'"';
?>
<div class="texte_contenu">
<?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Showing "<?php echo $conversation_message->id?>"</h3>
    <div class="informations formulaire">

<?php
      $option_list = array();
      $conversation_list = Conversation::db_get_all();
      foreach( $conversation_list as $conversation)
        $option_list[ $conversation->id ] = $conversation->name;
?>
      <p class="field">
        <span class="libelle">Conversation Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_conversation_view', true, array('id' => $conversation_message->conversation_id ) )?>"><?php echo $option_list[ $conversation_message->conversation_id ]?></a></span>
      </p>

            <p class="field">
              <span class="libelle">Sender Id</span>
              <span class="value"><?php echo $conversation_message->sender_id?></span>
            </p>
<?php
      $option_list = array();
      $player_list = Player::db_get_all();
      foreach( $player_list as $player)
        $option_list[ $player->id ] = $player->name;
?>
      <p class="field">
        <span class="libelle">Receiver Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_player_view', true, array('id' => $conversation_message->receiver_id ) )?>"><?php echo $option_list[ $conversation_message->receiver_id ]?></a></span>
      </p>

            <p class="field">
              <span class="libelle">Text</span>
              <span class="value"><?php echo $conversation_message->text?></span>
            </p>
            <p class="field">
              <span class="libelle">Created</span>
              <span class="value"><?php echo guess_time($conversation_message->created, GUESS_DATE_FR)?></span>
            </p>    </div>
    <p><a href="<?php echo get_page_url('admin_conversation_message_mod', true, array('id' => $conversation_message->id))?>">Modifier cet objet Conversation Message</a></p>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_conversation_message')?>">Revenir à la liste des objets Conversation Message</a></p>
  </div>
</div>