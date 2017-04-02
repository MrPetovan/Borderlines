<?php
/**
 * Class Message_Player
 *
 */

require_once( DATA."message/message.class.php" );

class Message_Player extends Message {

  protected $_conversation_id = null;
  protected $_message_id = null;
  protected $_sender_id = null;
  protected $_text = null;
  protected $_created = null;
  // Coming from message_player
  protected $_read = null;
  protected $_recipient_id = null;

  public static function get_visible_by_player( $conversation_id, $player_id ) {
    $sql = '
SELECT *, m.`player_id` AS `sender_id`, m_r.`player_id` AS `recipient_id`
FROM `'.self::get_table_name().'` m
JOIN `message_recipient` m_r ON m_r.`message_id` = m.`id`
WHERE m_r.`player_id` = '.  mysql_ureal_escape_string($player_id).'
AND m.`conversation_id` = '. mysql_ureal_escape_string($conversation_id);

    return self::sql_to_list($sql);
  }

  public static function set_read_by_conversation( $conversation_id, $player_id ) {
    $sql = '
UPDATE `message_recipient` m_r, `message` m
SET m_r.read = NOW()
WHERE m_r.read IS NULL
AND m_r.`message_id` = m.`id`
AND m_r.`player_id` = '.  mysql_ureal_escape_string($player_id).'
AND m.`conversation_id` = '. mysql_ureal_escape_string($conversation_id);
    mysql_uquery($sql);
  }

  protected static function sql_to_list($sql) {
    $res = mysql_uquery($sql);

    if($res) {
      $return = array();
      while($data = $res->fetch_assoc()) {
        $new_message_player = new Message_Player();
        foreach( $data as $field => $value ) {
          try {
            $new_message_player->$field = $value;
          }catch(Exception $e){}
        }
        $return[] = $new_message_player;
      }
      mysqli_free_result($res);
    }else {
      $return = false;
    }

    return $return;
  }

  public static function db_get_unread_count($player_id, $game_id = null) {
    $where = '';
    if( $game_id !== null ) {
      if( $game_id ) {
        $where = '
AND c.`game_id` IS NOT NULL';
      }else {
        $where = '
AND c.`game_id` IS NULL';
      }
    }
    $sql = '
SELECT IFNULL( COUNT(DISTINCT m.`conversation_id`), 0) AS `count`
FROM `message_recipient` m_r
JOIN `message` m ON m.`id` = m_r.`message_id`
JOIN `conversation` c ON c.`id` = m.`conversation_id`
WHERE m_r.`read` IS NULL
AND m_r.`player_id` = '.  mysql_ureal_escape_string($player_id).$where;
    $res = mysql_uquery($sql);
    if( $res ){
      $row = $res->fetch_row();
      $count = array_pop( $row );
    }else {
      $count = 0;
    }
    return $count;
  }
}