<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

  function l10n_parse_dir( $dir ) {
    $return = array();

    foreach(scandir( $dir ) as $file ) {
      if( strpos($file, '.') !== 0 ) {
        if( is_dir( $dir. DIRECTORY_SEPARATOR . $file) ) {
          $return = array_merge( $return, l10n_parse_dir($dir. DIRECTORY_SEPARATOR . $file));
        }else {
          $return = array_merge( $return, l10n_parse_file($dir. DIRECTORY_SEPARATOR . $file));
        }
      }
    }

    return $return;
  }

  function l10n_parse_file( $file ) {
    $matches = array();
    $content = file_get_contents( $file );
    $pattern = "/__\('((?:\\\'|[^'])*)'/";
    preg_match_all($pattern, $content, $matches);
    return $matches[1];
  }

  if( getValue('parse') ) {
    $trad_list = l10n_parse_dir( DATA );

    foreach( explode(',', LOCALES) as $locale ) {
      $new_l10n_replacements = array();
      $l10n_replacements = array();
      $translation_file = DIR_ROOT. 'lang' . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . 'translation.inc.php';
      if( is_file( $translation_file)) {
        include( $translation_file );
      }

      foreach( $trad_list as $key ) {
        $i10n_key = stripslashes($key);
        if( isset( $l10n_replacements[$i10n_key] ) ) {
          $new_l10n_replacements[ $i10n_key ] = $l10n_replacements[ $i10n_key ];
        }else {
          $new_l10n_replacements[ $i10n_key ] = null;
        }
      }
      $file_content = <<<FILE
<?php
  \$i10n_replacements = array(
[REPLACE]
  );
FILE;
      $array_content = '';
      foreach( $new_l10n_replacements as $key => $value ) {
        $array_content .= '
    \''.str_replace("'", "\'", $key).'\' => '.mysql_ureal_escape_string($value).',';
      }
      file_put_contents($translation_file, str_replace('[REPLACE]', $array_content, $file_content));
    }

    //var_debug( $trad_list );
  }

?>
