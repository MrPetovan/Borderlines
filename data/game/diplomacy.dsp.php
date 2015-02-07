<?php
  $PAGE_TITRE = __('Game: Diplomacy' );

  /* @var $world World */
  /* @var $current_player Player */
  $territory_params = array('game_id' => $current_game->id);
?>
<ul class="nav nav-tabs">
  <li role="presentation" class="inactive">
    <a><?php echo $current_game->name?></a>
  </li>
  <li role="presentation">
    <a href="<?php echo Page::get_url('game_map')?>"><?php echo icon('world', '') . __('World map')?></a>
  </li>
  <li role="presentation" class="active">
    <a href="<?php echo Page::get_url(PAGE_CODE)?>"><?php echo icon('diplomacy', '') . __('Diplomacy')?></a>
  </li>
  <li role="presentation">
    <a href="<?php echo Page::get_url('game_economy')?>"><?php echo icon('coins', '') . __('Economy')?></a>
  </li>
  <li role="presentation">
    <a href="<?php echo Page::get_url('game_show', array('id' => $current_game->id))?>"><?php echo icon('information', '') . __('Game Info')?></a>
  </li>
</ul>
<?php
  $world_id = $current_game->world_id;
  $world = World::instance( $world_id );

  $ratio = 1140 / $world->size_x;
  $options = array(
    'with_map' => false,
    'game_id' => $current_game->id,
    'turn' => $current_game->current_turn,
    'ratio' => $ratio,
    'no_names' => true,
    'force' => is_admin()
  );
?>
<style>
  .map_background:before {
    background-image: url('<?php echo $world->getImgUrl($options)?>');
  }
</style>
<div class="map_background">
<h3><?php echo __('Wall')?></h3>
<form action="<?php echo Page::get_url('shout', array('game_id' => $current_game->id, 'url_return' => Page::get_url(PAGE_CODE) ))?>" method="post">
  <p><?php echo '['.guess_time(time(), GUESS_TIME_LOCALE).']'?> <strong><?php echo wash_utf8($current_player->name)?></strong> : <input type="text" name="text" size="80" value=""/><button type="submit" name="action" value="shout">Say</button></p>
</form>
<div id="shoutwall" class="well">
<?php
    $shouts = Shout::db_get_by_game_id( $current_game->id );
    foreach( array_reverse( $shouts ) as $shout ) {
      $player = Player::instance($shout->shouter_id);
      echo '
  <div class="shout">['.guess_time($shout->date_sent, GUESS_TIME_LOCALE).'] <strong>'.wash_utf8($player->name).'</strong>: '.wash_utf8($shout->text).'</div>';
    }
?>
</div>
<h3><?php echo __('Diplomacy')?></h3>
<?php
    $player_diplomacy_list = $current_player->get_player_latest_diplomacy_list($current_game->id);
?>
<form action="<?php echo Page::get_url( PAGE_CODE, array('url_return' => Page::get_url(PAGE_CODE) ) )?>" method="POST">
  <table class="table table-hover">
    <thead>
      <tr>
        <th><?php echo __('Player')?></th>
        <th colspan="3"><?php echo __('Status')?></th>
        <th><?php echo icon('vision_clear') . __('Shared vision')?></th>
      </tr>
    </thead>
    <tbody>
<?php
    $diplo = array('Ally', 'Neutral', 'Enemy');
    foreach( $player_diplomacy_list as $player_diplomacy ) {
      $player = Player::instance( $player_diplomacy['to_player_id'] );
      $new_status = $player_diplomacy['status'] == 'Enemy'?'Ally':'Enemy';
      echo '
      <tr>
        <td><a href="'.Page::get_url('show_player', array('id' => $player->id)).'">'.$player->get_player_name_with_diplomacy($current_game, $current_game->current_turn, $current_player).'</a></td>';
      foreach( $diplo as $status ) {
        echo '
        <td>
          '.HTMLHelper::radio('status[' . $player->id . ']', $status, $player_diplomacy['status'], array('label_position' => 'right', 'id' => 'status_' . $player->id . '_' . $status), __($status) ).'
        </td>';
      }
      echo '
        <td>
          '.HTMLHelper::checkbox('shared_vision[' . $player->id . ']', 1, $player_diplomacy['shared_vision'], array('label_position' => 'right'), icon('vision_shared').__('Shared vision') ).'';
      echo '
        </td>
      </tr>';
    }
?>
    </tbody>
  </table>
  <p>
    <?php echo HTMLHelper::button('action', 'change_diplomacy_status', array('type' => 'submit'), __('Update diplomatic status'))?>
  </p>
</form>
<h3>Active conversations</h3>
<?php
  $conversation_list = Conversation_Player::db_get_by_game($current_player->id, $current_game->id);
?>
<form action="<?php echo Page::get_url(PAGE_CODE, $_GET)?>" method="post">
  <table class="table table-hover">
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
        <td colspan="6"><?php echo __('%s elements', count($conversation_list) )?> | <a href="<?php echo Page::get_url('conversation_add', array('return_url' => Page::get_url('game_diplomacy')))?>"><?php echo __('Open a new conversation')?></a></td>
      </tr>
    </tfoot>
    <tbody>
<?php
  foreach($conversation_list as $conversation) {
    $is_current = $conversation->game_id == $current_game->id;

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
</form>

</div>
