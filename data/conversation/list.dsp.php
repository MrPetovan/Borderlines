<?php
  if( getValue('archive') ) {
    $PAGE_TITRE = "Archived ";
  }else {
    $PAGE_TITRE = "Current ";
  }
  if( getValue('general') ) {
    $PAGE_TITRE .= "general conversations";
    $add_array = array('general' => getValue('general'));
  }else {
    $PAGE_TITRE .= "game conversations";
    $add_array = array();
  }
  $PAGE_TITRE = __($PAGE_TITRE . ' list');
?>
<h3><?php echo $PAGE_TITRE?></h3>
<p><?php echo __('Go to:')?></p>
<ul>
  <li>
<?php if( $unread_count = Message_Player::db_get_unread_count($current_player->id, false)) :?>
    <a href="<?php echo Page::get_url(PAGE_CODE, array('general' => 1))?>"><strong><?php echo __('General conversations (%s)', $unread_count)?></strong></a>
<?php else:?>
    <a href="<?php echo Page::get_url(PAGE_CODE, array('general' => 1))?>"><?php echo __('General conversations')?></a>
<?php endif;?>
    (<a href="<?php echo Page::get_url(PAGE_CODE, array('archive' => 1, 'general' => 1))?>"><?php echo __('Archive')?></a>)
  </li>
  <li>
  <?php if( $unread_count = Message_Player::db_get_unread_count($current_player->id, true)) :?>
    <a href="<?php echo Page::get_url(PAGE_CODE)?>"><strong><?php echo __('Current game conversations (%s)', $unread_count)?></strong></a>
  <?php else:?>
    <a href="<?php echo Page::get_url(PAGE_CODE)?>"><?php echo __('Current game conversations')?></a>
  <?php endif;?>
    (<a href="<?php echo Page::get_url(PAGE_CODE, array('archive' => 1))?>"><?php echo __('Archive')?></a>)
  </li>
</ul>
<form action="<?php echo Page::get_url(PAGE_CODE, $_GET)?>" method="post">
  <table class="table table-hover accordion">
    <thead>
      <tr>
        <th><?php echo __('Sel.')?></th>
        <th><?php echo __('Subject')?></th>
        <th><?php echo __('Recipients')?></th>
        <th><?php echo __('Created')?></th>
        <th><?php echo __('Last message')?></th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="6"><?php echo __('%s elements', count($conversation_list) )?> | <a href="<?php echo Page::get_url('conversation_add', $add_array)?>"><?php echo __('Open a new conversation')?></a></td>
      </tr>
    </tfoot>
<?php
  $current_game_id = null;
  foreach($conversation_list as $conversation) {
    $is_current = $conversation->game_id == $current_game->id;

    if( $current_game_id != $conversation->game_id ) {
      $game = Game::instance($conversation->game_id);
      if( $current_game_id !== 'null' ) {
        echo '
    </tbody>';
      }
      echo '
    <tbody class="archive'.($is_current?' current':'').'">
      <tr class="title">
        <th colspan="5">'.__('Game "%s"', $game->name).'</th>
      </tr>
      <tr>
        <th>'.__('Sel.').'</th>
        <th>'.__('Subject').'</th>
        <th>'.__('Recipients').'</th>
        <th>'.__('Created').'</th>
        <th>'.__('Last message').'</th>
      </tr>';
      $current_game_id = $conversation->game_id;
    }

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
    $is_unread = false;
    if( count( $conversation_message_list ) ) {
      $last_message = array_pop( $conversation_message_list );
      $last_poster = Player::instance($last_message->sender_id);
      $string = __('%s at %s', $last_poster->name, guess_time($last_message->created, GUESS_DATETIME_LOCALE) );

      if( $last_message->read === null ) {
        $is_unread = true;
      }
    }else {
      $string = __('No message yet');
    }
?>
      <tr<?php echo $is_unread?' class="current"':''?>>
        <td><input type="checkbox" name="conversation_id[]" value="<?php echo $conversation->id ?>"/></td>
        <td><a href="<?php echo Page::get_url('conversation_view', array('id' => $conversation->id))?>"><?php echo $conversation->subject?></a></td>
        <td><?php echo $conversation->left?__('(left)'):implode(', ', $to)?></td>
        <td><?php echo guess_time( $conversation->get_created(), GUESS_DATETIME_LOCALE )?></td>
        <td><?php echo $string?></td>
      </tr>
<?php
  }
?>
    </tbody>
  </table>
  <p><?php echo __('For selected conversations :')?>
    <select name="action">
<?php if( getValue('archive') ) :?>
      <option value="unarchive"><?php echo __('Unarchive')?></option>
<?php else:?>
      <option value="archive"><?php echo __('Archive')?></option>
<?php endif;?>
      <option value="leave"><?php echo __('Leave')?></option>
    </select>
    <input type="submit" name="submit" value="<?php echo __('Ok')?>"/>
  </p>
</form>