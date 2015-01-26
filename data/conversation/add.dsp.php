<?php
  $form_url = get_page_url(PAGE_CODE);
  if( $game_id === null ) {
    $PAGE_TITRE = 'General Conversation : Create';
    $add_array = array('general' => getValue('general'));
  }else {
    $PAGE_TITRE = 'Game Conversation : Create';
    $add_array = array();
  }

  if( getValue('return_url') ) {
    $add_array['return_url'] = getValue('return_url');
  }

  $other_player_list = array();
  if( $game_id !== null ) {
    $game_player_list = $current_player->current_game->get_game_player_list();
    foreach( $game_player_list as $game_player_row ) {
      $player = Player::instance($game_player_row['player_id']);
      $other_player_list[ $player->id ] = $player->name;
    }
  }else {
    $other_player_list = Player::db_get_select_list();
  }

?>
  <h3><?php echo $PAGE_TITRE ?></h3>
  <form class="formulaire" action="<?php echo Page::get_url(PAGE_CODE, $add_array)?>" method="post">
    <fieldset>
      <legend>To :</legend>
<?php if( count( $recipient_list ) ) : ?>
      <ul>
  <?php foreach( $recipient_list as $player_id ) :
    $player = Player::instance($player_id);
  ?>
        <li>
          <?php echo HTMLHelper::hidden('recipient_list[]', $player->id)?>
          <?php echo $player->name?>
          <?php echo HTMLHelper::button('remove_recipient', $player->id, array('type' => 'submit'), 'Remove')?>
        </li>
  <?php endforeach; ?>
      </ul>
<?php else: ?>
      <p>No recipient yet</p>
<?php endif;?>
      <p class="field">
        <label for="player_id">Add recipient :</label>
        <select name="player_id" id="player_id">
<?php
  foreach( $other_player_list as $player_id => $player_name ) :
    if( $player_id != $current_player->id && !in_array($player_id, $recipient_list)) :
?>
          <option value="<?php echo $player_id?>"><?php echo $player_name?></option>
<?php
    endif;
  endforeach;
?>
        </select>
        <?php echo HTMLHelper::submit('add_to', 'Add a recipient')?>
      </p>

    </fieldset>
    <fieldset>
      <legend>Text fields</legend>
      <p class="field">
        <label for="subject">Subject *</label>
        <input type="text" class="input_text" size="80" name="conversation[subject]" id="subject" value="<?php echo $conversation_mod->subject?>"/>
      </p>
      <p><label for="text">Text</label></p>
      <textarea name="message[text]" cols="80" rows="10" id="text"><?php echo $message_mod->text?></textarea>
    </fieldset>
    <p>
      <?php echo HTMLHelper::submit('conversation_submit', 'Launch conversation')?>
    </p>
  </form>
  <p><a href="<?php echo Page::get_url('conversation_list')?>">Return to conversation list</a></p>