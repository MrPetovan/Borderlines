<?php
/**
 * Class Translation
 *
 */

require_once( DATA."model/translation_model.class.php" );

class Translation extends Translation_Model {

  // CUSTOM

  public static function get_untranslated_count( $locale = LOCALE ) {
    $sql = 'SELECT COUNT(*) FROM `translation` WHERE (`translation` IS NULL OR `translation` = "") AND `locale` = "'.$locale.'"';
    $res = mysql_uquery($sql);
    $translate_number = array_pop( mysql_fetch_row($res) );

    return $translate_number;
  }

  // /CUSTOM

}