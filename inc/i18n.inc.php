<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

  function load_translations( $locale ) {
    $i18n_replacements = array();

    $sql = 'SELECT `code`, `translation` FROM `translation` WHERE `locale` = '. mysql_ureal_escape_string( $locale );
    $res = mysql_uquery($sql);

    while( $row = mysql_fetch_assoc($res) ) {
      $i18n_replacements[ $row['code'] ] = $row['translation'];
    }

    return $i18n_replacements;
  }

  function __($format, $args = null) {
    global $i18n_replacements;
    if( $args !== null ) {
      if( !is_array( $args ) ) {
        $args = array_slice(func_get_args(), 1);
      }
    }


    if( $format !== '' ) {
      if(array_key_exists( $format, $i18n_replacements ) ) {
        if( $i18n_replacements[ $format ] !== null ) {
          $format = $i18n_replacements[ $format ];
        }
      }else {
        $translation = Translation::instance();
        $translation->code = $format;
        $translation->locale = LOCALE;
        $translation->context = PAGE_CODE;

        $success = $translation->save();

        $i18n_replacements[ $format ] = $format;
      }
    }

    return vsprintf( $format, $args );
  }

  function l10n_number($number, $decimals = 0) {
    $locale = localeconv();
    return number_format(
      $number,
      $decimals,
      $locale['decimal_point'],
      $locale['thousands_sep']
    );
 }
?>
