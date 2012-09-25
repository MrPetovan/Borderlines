<?php
  include_once('data/static/html_functions.php');

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url(PAGE_CODE).'&id='.$conversation->id;
  $PAGE_TITRE = 'Conversation : Showing "'.$conversation->id.'"';
?>
  <h3>Showing "<?php echo $conversation->subject?>"</h3>
  <h4>Current participants :</h4>
  <ul>
<?php
  $leave_list = array();
  $conversation_player_list = $conversation->get_conversation_player_list();
  foreach( $conversation_player_list as $conversation_player_row ) :
    $player = Player::instance($conversation_player_row['player_id']);
    if( $conversation_player_row['left'] ) {
      $leave_list[ guess_time( $conversation_player_row['left'] ) ] = $player;
    }
?>
    <li>
      <a href="<?php echo Page::get_url('show_player', array('id' => $player->id))?>"><?php echo $player->name?></a>
      <?php echo $conversation_player_row['left']?' (left)':''?>
    </li>
<?php
  endforeach;
?>
  </ul>
  <h4>Messages</h4>
<?php
  ksort( $leave_list );
  reset( $leave_list );
  $current_leave_time = key( $leave_list );
  $message_player_list = Message_Player::get_visible_by_player($conversation->id, $current_player->id);
  Message_Player::set_read_by_conversation($conversation->id, $current_player->id);
  foreach( $message_player_list as $message_player ) :
    $sender = Player::instance($message_player->sender_id);
    if( $current_leave_time && $current_leave_time < $message_player->created ) :
?>
  <p><strong>On <?php echo guess_time($current_leave_time, GUESS_DATETIME_LOCALE)?>, <?php echo $leave_list[ $current_leave_time ]->name?> left</strong></p>
<?php
      unset( $leave_list[ $current_leave_time ] );
      reset( $leave_list );
      $current_leave_time = key( $leave_list );
    endif;
?>
  <div>
    <p><strong>
      <?php echo $message_player->read?'':'[Unread]'?>
      On <?php echo guess_time($message_player->created, GUESS_DATETIME_LOCALE)?>,
      <a href="<?php echo Page::get_url('show_player', array('id' => $sender->id))?>"><?php echo $sender->name?></a>
      posted:</strong>
    </p>
    <p><?php echo nl2br($message_player->text, true)?></p>
  </div>
<?php
  endforeach;
  if( count( $leave_list ) ) {
    foreach( $leave_list as $current_leave_time => $player_left ) {
?>
  <p>On <?php echo guess_time($current_leave_time, GUESS_DATETIME_LOCALE)?>, <?php echo $player_left->name?> left</p>
<?php
    }
  }
?>

  <form action="<?php echo $form_url?>" method="post">
    <fieldset>
      <legend>Answer</legend>
      <textarea name="message[text]" cols="80" rows="10"></textarea>
      <p><?php echo HTMLHelper::submit('conversation_submit', 'Answer')?></p>
    </fieldset>
  </form>
  <p><a href="<?php echo get_page_url('conversation_list')?>">Revenir à la liste des objets Conversation</a></p>