<?php



  if( isset( $_POST['translation'])) {
    foreach( $_POST['translation'] as $translation_id => $translation ) {
      if( $translation != '' ) {
        /* @var $translation_obj Translation */
        $translation_obj = Translation::instance($translation_id);
        $translation_obj->updated = time();
        $translation_obj->translation = $translation;
        $translation_obj->translator_id = Member::get_current_user_id();
        $translation_obj->save();
      }
    }
    Page::redirect(PAGE_CODE);
  }

  if( ($code = getValue('code')) === null && ($search = getValue('search')) === null ) {
    $sql = 'SELECT COUNT(*) FROM `translation` WHERE (`translation` IS NULL OR `translation` = "") AND `locale` = "'.LOCALE.'"';
    $res = mysql_uquery($sql);
    $translate_number = array_pop( mysqli_fetch_row($res) );

    $sql = 'SELECT DISTINCT `code` FROM `translation` WHERE (`translation` IS NULL OR `translation` = "") AND `locale` = "'.LOCALE.'" ORDER BY RAND() LIMIT 10';
    $res = mysql_uquery($sql);
    $code_array = mysql_fetch_to_array($res);


    foreach( $code_array as $code_row ) {
      $code_list[] = $code_row['code'];
    }

    $sql = 'SELECT * FROM `translation` WHERE `code` IN( '.mysql_ureal_escape_string($code_list).' ) AND (`translation` IS NULL OR `translation` = "") AND `locale` = "'.LOCALE.'" ORDER BY `code`';
    $res = mysql_uquery($sql);
    $translate_array = mysql_fetch_to_array($res);
  }elseif( $code !== null ) {
    $sql = '
SELECT *
FROM `translation`
WHERE (`translation` IS NULL OR `translation` = "")
AND `locale` = "'.LOCALE.'"
AND `code` = '. mysql_ureal_escape_string($code);
    $res = mysql_uquery($sql);
    $translate_array = mysql_fetch_to_array($res);
  }else {
    $search_string = '%'.str_replace('%', '#%', $search).'%';
    $sql = '
SELECT *
FROM `translation`
WHERE `locale` = "'.LOCALE.'"
AND (`code` LIKE '. mysql_ureal_escape_string($search_string).' ESCAPE "#" OR
  `translation` LIKE '. mysql_ureal_escape_string($search_string).' ESCAPE "#")
ORDER BY `code`';
    $res = mysql_uquery($sql);
    $translate_array = mysql_fetch_to_array($res);
    $translate_number = count($translate_array);
  }

  $sql = '
SELECT t.`locale`, CONCAT( m.`prenom`, " ", m.`nom`) AS `name`, COUNT(*) AS `count`
FROM `translation` t
JOIN `member` m ON m.`id` = t.`translator_id`
GROUP BY t.`locale`, t.`translator_id`
ORDER BY t.`locale`, COUNT(*) DESC';
  $res = mysql_uquery($sql);
  $stats_array = mysql_fetch_to_array($res);
?>
