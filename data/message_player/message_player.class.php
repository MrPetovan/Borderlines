<?php
/**
 * Class Message_Player
 *
 */

require_once( DATA."message/message.class.php" );

class Message_Player extends Message {

  protected $_conversation_id = null;
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
      while($data = mysql_fetch_assoc($res)) {
        $new_message_player = new Message_Player();
        foreach( $data as $field => $value ) {
          try {
            $new_message_player->$field = $value;
          }catch(Exception $e){}
        }
        $return[] = $new_message_player;
      }
      mysql_free_result($res);
    }else {
      $return = false;
    }

    return $return;
  }

}