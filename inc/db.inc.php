<?php

if (!defined('DEBUG_SQL')) {
  define('DEBUG_SQL', false);
}

/**
 * Fonction de connextion MySQL standard + sélection de base
 *
 * @param string $host
 * @param string $user
 * @param string $pass
 * @param string $base
 * @return resource | false
 */
function mysql_uconnect($host, $user, $pass, $base) {
  $link = mysql_connect($host, $user, $pass);
  if (is_resource($link)) {
    $return = mysql_selectdb($base);
  } else {
    $return = false;
  }

  mysql_uquery("SET NAMES UTF8");

  return $return;
}

function mysql_udeconnect() {
  return mysql_close();
}

/**
 * Fonction récursive d'échappement SQL de paramètres
 *
 * @example mysql_ureal_escape_string ( null, '1', 1, array(2, '2') )
 * @param mixed
 *
 * @return string
 */
function mysql_ureal_escape_string() {
  $values = func_get_args();
  if (count($values) > 1) {
    foreach ($values as $key => $value) {
      $values[$key] = mysql_ureal_escape_string($value);
    }
    $return = implode(',', $values);
  } else {
    $value = $values[0];
    if (is_null($value)) {
      $return = 'NULL';
    } elseif (is_numeric($value)) {
      $return = number_format($value, get_dec_count( $value ), '.','');
    } elseif (is_array($value)) {
      foreach ($value as $key => $value_item) {
        $values[$key] = mysql_ureal_escape_string($value_item);
      }
      $return = implode(',', $values);
    } else {
      $return = "'" . mysql_real_escape_string($value) . "'";
    }
  }
  return $return;
}

function mysql_uquery($query, $link_identifier = null) {
  if (DEBUG_SQL) {
    mysql_log($query);
  }

  $queries = preg_split("/;+(?=([^'|^\\\']*['|\\\'][^'|^\\\']*['|\\\'])*[^'|^\\\']*[^'|^\\\']$)/", $query);
  $query = array_pop( $queries );
  if( count( $queries ) > 0 ) {
    foreach( $queries as $subquery ) {
      mysql_uquery($subquery, $link_identifier);
    }
  }
  setlocale(LC_NUMERIC, 'en_US.utf8');
  if (is_null($link_identifier)) {
    $res = mysql_query($query);
  } else {
    $res = mysql_query($query, $link_identifier);
  }
  setlocale(LC_NUMERIC, LOCALE.'.utf8');
  if ($res) {
    return $res;
  } else {
    if (is_null($link_identifier)) {
      $error = mysql_error();
    } else {
      $error = mysql_error($link_identifier);
    }

    if (PROD) {

    } else {
      var_debug(debug_backtrace());
      echo "<p>$query</p><p>Erreur : $error</p>";
      die();
    }
  }
}

function mysql_timestamp_to_mysql_date($timestamp) {
  return date('Y-m-d H:i:s', $timestamp);
}

function mysql_date_to_timestamp($date) {
  $return = false;
  if (preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/', $date, $matches)) {
    /**
     * $matches :
     * 1 = Annee
     * 2 = Mois
     * 3 = Jour
     * 4 = Heures
     * 5 = Minutes
     * 6 = Secondes
     */
    if ($matches[2] != 0 && $matches[3] != 0) {
      $return = mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
    }
  }
  return $return;
}

function mysql_log($query = null) {
  static $array_queries = array();
  if (is_null($query)) {
    echo "<pre>";
    print_r($array_queries);
    echo "</pre>";
  } else {
    $array_backtrace = debug_backtrace();
    $i = 1;
    while (strpos($array_backtrace[$i]['file'], "db_object") !== false) {
      $i++;
    }
    $array_queries[] = array('query' => $query, 'backtrace' => '<strong>' . $array_backtrace[$i]['file'] . '</strong> at line <strong>' . $array_backtrace[$i]['line'] . '</strong>');
  }
}

function mysql_fetch_to_array($res) {
  $return = array();

  if( $res !== null ) {
    while ($row = mysql_fetch_assoc($res)) {
      foreach ($row as $param => $value) {
        $row[$param] = correctype($value);
      }
      $return[] = $row;
    }
    mysql_free_result($res);
  }

  return $return;
}
function mysql_fetch_one($query) {
  $return = null;

  $res = mysql_uquery($query);

  if( $res !== null ) {
    $row = mysql_fetch_row($res);
    if( is_array($row) ) {
      $return = correctype($row[0]);
    }
    mysql_free_result($res);
  }

  return $return;
}

?>