<?php
  if( getValue('archive') ) {
    $PAGE_TITRE = "Archived ";
  }else {
    $PAGE_TITRE = "Current ";
  }
  if( $game_id === null ) {
    $PAGE_TITRE .= "general conversations";
  }else {
    $PAGE_TITRE .= "game conversations";
  }
  include_once('data/static/html_functions.php');
?>
<h3><?php echo $PAGE_TITRE?> list</h3>
<p>Go to:</p>
<ul>
  <li>
    <a href="<?php echo Page::get_url(PAGE_CODE, array('general' => 1))?>">General conversations</a>
    (<a href="<?php echo Page::get_url(PAGE_CODE, array('archive' => 1, 'general' => 1))?>">Archive</a>)
  </li>
<?php if( $game_id !== null ) :?>
  <li>
    <a href="<?php echo Page::get_url(PAGE_CODE)?>">Current game conversations</a>
    (<a href="<?php echo Page::get_url(PAGE_CODE, array('archive' => 1))?>">Archive</a>)
  </li>
<?php endif;?>
</ul>
<form action="<?php echo Page::get_url(PAGE_CODE, $_GET)?>" method="post">
  <table>
    <thead>
      <tr>
        <th>Sel.</th>
        <th>To</th>
        <th>Subject</th>
        <th>Created</th>
        <th>Last message</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="6"><?php echo count($conversation_list)?> elements | <a href="<?php echo Page::get_url('conversation_add')?>">Open a new conversation</a></td>
      </tr>
    </tfoot>
    <tbody>
<?php
  foreach($conversation_list as $conversation) {
    /* @var $conversation Conversation */
    $conversation_player_list = $conversation->get_conversation_player_list();
    $to = array();
    foreach( $conversation_player_list as $conversation_player_row ) {
      if( $conversation_player_row['player_id'] != $current_player->id ) {
        $player = Player::instance($conversation_player_row['player_id']);

        $to[] = '<a href="'.Page::get_url('show_player', array('id' => $player->id)).'">'.$player->name.'</a>';
      }
    }

    $conversation_message_list = Message_Player::get_visible_by_player($conversation->id, $current_player->id);
    if( count( $conversation_message_list ) ) {
      $last_message = array_pop( $conversation_message_list );
      $last_poster = Player::instance($last_message->sender_id);
      $string = $last_poster->name.' at '.guess_time($last_message->created, GUESS_TIME_LOCALE);
    }else {
      $string = 'No message yet';
    }
?>
      <tr>
        <td><input type="checkbox" name="conversation_id[]" value="<?php echo $conversation->id ?>"/></td>
        <td><?php echo $conversation->left?'(left)':implode(', ', $to)?></td>
        <td><a href="<?php echo Page::get_url('conversation_view', array('id' => $conversation->id))?>"><?php echo $conversation->subject?></a></td>
        <td><?php echo guess_time( $conversation->get_created(), GUESS_TIME_LOCALE )?></td>
        <td><?php echo $string?></td>
      </tr>
<?php
  }
?>
    </tbody>
  </table>
  <p>For selected conversations :
    <select name="action">
<?php if( getValue('archive') ) :?>
      <option value="unarchive">Unarchive</option>
<?php else:?>
      <option value="archive">Archive</option>
<?php endif;?>
      <option value="leave">Leave</option>
    </select>
    <input type="submit" name="submit" value="Valider"/>
  </p>
</form>