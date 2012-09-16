<?php
/**
 * Class Conversation
 *
 */

require_once( DATA."model/conversation_model.class.php" );

class Conversation extends Conversation_Model {

  // CUSTOM

  public function get_current_recipients() {
    $sql = '
SELECT `id`
FROM `player` p
JOIN `conversation_player` c_p ON c_p.`player_id` = p.`id`
WHERE c_p.`conversation_id` = '.mysql_ureal_escape_string( $this->id ).'
AND c_p.`left` IS NULL';
    return Player::sql_to_list($sql);
  }

  // /CUSTOM

}