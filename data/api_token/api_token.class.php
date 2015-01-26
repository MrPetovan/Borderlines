<?php
/**
 * Class Api_Token
 *
 */

require_once( DATA."model/api_token_model.class.php" );

class Api_Token extends Api_Token_Model {

  // CUSTOM

  public static function db_get_current_by_player_id($player_id) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `expires` > NOW()
AND `player_id` = ".mysql_ureal_escape_string($player_id).'
ORDER BY `id` DESC';

    return self::sql_to_object($sql);
  }

  public static function get_current_token($player_id) {
    $api_token = self::db_get_current_by_player_id($player_id);

    if( !$api_token ) {
      $api_token = self::request_token($player_id);
    }

    return $api_token;
  }

  public static function request_token($player_id, $compare_sig = false, $sig = null ) {
    $return = null;

    $current_player = Player::instance($player_id);

    if( $player_id !== null && $current_player->id == $player_id ) {
      $sig_compare = sha1( $player_id . $current_player->api_key );

      if( !$compare_sig || ($sig_compare == $sig) ) {
        $previous_tokens = Api_Token::db_get_by_player_id($current_player->id);
        foreach( $previous_tokens as $previous_token ) {
          $previous_token->expires = time();
          $previous_token->save();
        }

        $api_token = Api_Token::instance();
        $api_token->hash = sha1( time() . $current_player->id . $current_player->api_key . 'py(_à(^)dfùvb^sué_à');
        $api_token->player_id = $current_player->id;
        $api_token->created = time();
        $api_token->expires = time() + 3600;
        $error = $api_token->save();

        $return = $api_token;
      }
    }
    return $return;
  }

  // /CUSTOM

}