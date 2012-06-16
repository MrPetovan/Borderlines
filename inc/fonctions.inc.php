<?php
/**
 * Affiche le contenu des variables passées en paramètre
 *
 * @param mixed
 */
  function var_debug() {
    echo "<pre>";
    foreach (func_get_args() as $var) {
      var_dump($var);
      echo "\n";
    }
    echo "</pre>";
  }

  function framework_log($message) {
    error_log('['.date('Y/m/d H:i:s').'] ['.$_SERVER['REMOTE_ADDR'].'] '.str_replace("\n","",$message)."\n", 3, DIR_ROOT.'log/'.strtolower( SITE_NAME ).'.log');
  }

/**
 * Redirection standard
 *
 * @param string $url URL de redirection
 */
function redirect($url) {
  header("Location: ".$url);
  exit;
}

/**
 * Redirection dans le site
 *
 * @param string $url URL de redirection (après le ?)
 */
function site_redirect($url = '') {
  header("Location: ".URL_ROOT."?".$url);
  exit;
}


function htmlspecialchars_utf8($string, $quote_style = ENT_COMPAT) {
  return htmlspecialchars($string, $quote_style, 'UTF-8');
}

function htmlentities_utf8($string, $quote_style = ENT_COMPAT) {
  return htmlentities($string, $quote_style, 'UTF-8');
}

function wash_utf8($string) {
  return htmlentities_utf8(stripslashes($string));
}

function wash_utf8_r($param) {
  if(is_array($param)) {
    foreach ($param as $key => $value) {
      $param[$key] = wash_utf8_r($value);
    }

    return $param;
  }else {
    return wash_utf8($param);
  }
}

function str_crop($string, $length, $end="...") {
  $tmp_string = $string;

  if(strlen($tmp_string) > $length) {
    $tmp_string = substr($tmp_string, 0, $length - strlen($end)).$end;
  }

  return $tmp_string;
}

function str_rand($length = 8)
{
  $pass = '';
  # first character is capitalize
  //$pass =  chr(mt_rand(65,90));    // A-Z

  # rest are either 0-9 or a-z
  for($k=0; $k < $length; $k++)
  {
    $probab = mt_rand(1,10);

    if($probab <= 8)   // a-z probability is 80%
    $pass .= chr(mt_rand(97,122));
    else            // 0-9 probability is 20%
    $pass .= chr(mt_rand(48, 57));
  }
  return $pass;
}

function str_price($float) {
  $number = round($float * 100, 0);
  return substr($number, 0, -2).','.substr($number, -2).' &euro;';
}

/*function wash_utf8($string) {
return htmlentities_utf8(stripslashes($string));
}*/

define('SECOND', 1);
define('MINUTE', 60 * SECOND);
define('HOUR', 60 * MINUTE);
define('DAY', 24 * HOUR);
define('WEEK', 7 * DAY);
define('MONTH', 30 * DAY);
define('YEAR', 365 * DAY);

function seconds_to_string($seconds, $short = false) {
  $years = floor($seconds / YEAR);
  $seconds -= $years  * YEAR;
  $months = floor($seconds / MONTH);
  $seconds -= $months * MONTH;
  $weeks = floor($seconds / WEEK);
  $seconds -= $weeks * WEEK;
  $days = floor($seconds / DAY);
  $seconds -= $days * DAY;
  $hours = floor($seconds / HOUR);
  $seconds -= $hours * HOUR;
  $minutes = floor($seconds / MINUTE);
  $seconds -= $minutes * MINUTE;

  if($years != 0) {
    $chaine[] = $years . " an" . ($years > 1 ? 's' : '');
  }
  if($months != 0) {
    $chaine[] = $months . " mois";
  }
  if($weeks != 0) {
    $chaine[] = $weeks . " semaine" . ($weeks > 1 ? 's' : '');
  }
  if($days != 0) {
    $chaine[] = $days . " jour" . ($days > 1 ? 's' : '');
  }
  if($hours != 0) {
    $chaine[] = $hours . " heure" . ($hours > 1 ? 's' : '');
  }
  if($minutes != 0) {
    $chaine[] = $minutes . " min.";
  }
  if($seconds != 0) {
    $chaine[] = $seconds . " s.";
  }

  if(count($chaine) > 1) {
    $element_fin = array_pop($chaine);
    $return = implode(', ', $chaine);
    $return .= " et " . $element_fin;
  }elseif(count($chaine) == 1) {
    $return = $chaine[0];
  }else {
    $return = false;
  }

  return $return;
}

function seconds_to_time($seconds) {
  $minutes = floor($seconds / MINUTE);
  $seconds -= $minutes * MINUTE;

  $chaine = '';
  if($minutes < 10) {
    $chaine .= '0';
  }
  $chaine .= $minutes . ":";
  if($seconds < 10) {
    $chaine .= '0';
  }
  $chaine .= $seconds;

  return $chaine;
}

/**
   * Teste le type MIME d'un fichier pour savoir si c'est une image
   *
   * @param string $type
   * @return bool
   */
function is_image_mime($type) {
  $type_autorise = array(
  'image/gif',
  'image/jpeg',
  'image/png',
  'image/pjpeg');
  return in_array($type, $type_autorise);
}

/**
   * Teste l'extension d'un fichier pour savoir si c'est une image
   *
   * @param string $chemin du fichier
   * @return bool
   */
function is_image_ext($file) {
  $extension = substr($file, strrpos($file, '.') + 1);
  $extension_autorise = array(
  'gif',
  'jpeg',
  'jpg',
  'png'
  );
  return in_array($extension, $extension_autorise);
}

function rstripslashes($array) {
  if(is_array($array)) {
    foreach($array as $key => $value) {
      $array[$key] = rstripslashes($value);
    }
  }else {
    return stripslashes($array);
  }

  return $array;
}


/**
   * Parse a time/date generated with strftime().
   *
   * This function is the same as the original one defined by PHP (Linux/Unix only),
   *  but now you can use it on Windows too.
   *  Limitation : Only this format can be parsed %S, %M, %H, %d, %m, %Y
   *
   * @author Lionel SAURON
   * @version 1.0
   * @public
   *
   * @param $sDate(string)    The string to parse (e.g. returned from strftime()).
   * @param $sFormat(string)  The format used in date  (e.g. the same as used in strftime()).
   * @return (array)          Returns an array with the <code>$sDate</code> parsed, or <code>false</code> on error.
   */
if(function_exists("strptime") == false) {
  function strptime($sDate, $sFormat) {
    $aResult = array (
    'tm_sec'   => 0,
    'tm_min'   => 0,
    'tm_hour'  => 0,
    'tm_mday'  => 1,
    'tm_mon'   => 0,
    'tm_year'  => 0,
    'tm_wday'  => 0,
    'tm_yday'  => 0,
    'unparsed' => $sDate,
    );

    while($sFormat != "") {
      // ===== Search a %x element, Check the static string before the %x =====
      $nIdxFound = strpos($sFormat, '%');
      if($nIdxFound === false) {
        // There is no more format. Check the last static string.
        $aResult['unparsed'] = ($sFormat == $sDate) ? "" : $sDate;
        break;
      }

      $sFormatBefore = substr($sFormat, 0, $nIdxFound);
      $sDateBefore   = substr($sDate,   0, $nIdxFound);

      if($sFormatBefore != $sDateBefore) break;

      // ===== Read the value of the %x found =====
      $sFormat = substr($sFormat, $nIdxFound);
      $sDate   = substr($sDate,   $nIdxFound);

      $aResult['unparsed'] = $sDate;

      $sFormatCurrent = substr($sFormat, 0, 2);
      $sFormatAfter   = substr($sFormat, 2);

      $nValue = -1;
      $sDateAfter = "";
      switch($sFormatCurrent) {
        case '%S': // Seconds after the minute (0-59)

        sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);

        if(($nValue < 0) || ($nValue > 59)) return false;

        $aResult['tm_sec']  = $nValue;
        break;

        // ----------
        case '%M': // Minutes after the hour (0-59)
        sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);

        if(($nValue < 0) || ($nValue > 59)) return false;

        $aResult['tm_min']  = $nValue;
        break;

        // ----------
        case '%H': // Hour since midnight (0-23)
        sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);

        if(($nValue < 0) || ($nValue > 23)) return false;

        $aResult['tm_hour']  = $nValue;
        break;

        // ----------
        case '%d': // Day of the month (1-31)
        sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);

        if(($nValue < 1) || ($nValue > 31)) return false;

        $aResult['tm_mday']  = $nValue;
        break;

        // ----------
        case '%m': // Months since January (0-11)
        sscanf($sDate, "%2d%[^\\n]", $nValue, $sDateAfter);

        if(($nValue < 1) || ($nValue > 12)) return false;

        $aResult['tm_mon']  = ($nValue - 1);
        break;

        // ----------
        case '%Y': // Years since 1900
        sscanf($sDate, "%4d%[^\\n]", $nValue, $sDateAfter);

        if($nValue < 1900) return false;

        $aResult['tm_year']  = ($nValue - 1900);
        break;

        // ----------
        default: break 2; // Break Switch and while
      }

      // ===== Next please =====
      $sFormat = $sFormatAfter;
      $sDate   = $sDateAfter;

      $aResult['unparsed'] = $sDate;

    } // END while($sFormat != "")


    // ===== Create the other value of the result array =====
    $nParsedDateTimestamp = mktime($aResult['tm_hour'], $aResult['tm_min'], $aResult['tm_sec'],
    $aResult['tm_mon'] + 1, $aResult['tm_mday'], $aResult['tm_year'] + 1900);

    // Before PHP 5.1 return -1 when error
    if(($nParsedDateTimestamp === false)
    ||($nParsedDateTimestamp === -1)) return false;

    $aResult['tm_wday'] = (int) strftime("%w", $nParsedDateTimestamp); // Days since Sunday (0-6)
    $aResult['tm_yday'] = (strftime("%j", $nParsedDateTimestamp) - 1); // Days since January 1 (0-365)

    return $aResult;
  } // END of function
} // END if(function_exists("strptime") == false)


define("GUESS_DATE_TIMESTAMP", 1);
define("GUESS_DATE_MYSQL", 2);
define("GUESS_DATE_FR", 3);
/**
   * Teste timestamp, date mysql (YYYY-MM-DD HH:MM:SS) et date FR (JJ/MM/AAAA)
   * Retourne par défaut un timestamp (possiblement négatif), ou une date mysql ou une date FR
   *
   * @param mixed $date
   */
function guess_date($date, $return_flag = GUESS_DATE_TIMESTAMP) {
  $return = null;
  $data_input = false;
  static $array_actions = array(
    GUESS_DATE_TIMESTAMP => array(
      GUESS_DATE_MYSQL => 1,
      GUESS_DATE_FR => 2,
    ),
    GUESS_DATE_MYSQL => array(
      GUESS_DATE_TIMESTAMP => 3,
      GUESS_DATE_FR => 4,
    ),
    GUESS_DATE_FR => array(
      GUESS_DATE_TIMESTAMP => 5,
      GUESS_DATE_MYSQL => 6,
    )
  );

  if(is_numeric($date)) {
    $data_input = GUESS_DATE_TIMESTAMP;
  }elseif(preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/', $date, $matches)) {
    $data_matches = $matches;
    $data_input = GUESS_DATE_MYSQL;
  }elseif(preg_match('#([0-9]{2})/([0-9]{2})/([0-9]{4})#', $date, $matches)) {
    $data_matches = $matches;
    $data_input = GUESS_DATE_FR;
    if(checkdate($data_matches[2], $data_matches[1], $data_matches[3]) ===false) {
      $data_input = false;
    }
  }
  
  if($data_input !== false) {
    $return = $date;
    if(isset($array_actions[$data_input][$return_flag])) {
      switch($array_actions[$data_input][$return_flag]) {
        case 1 : $return = mysql_timestamp_to_mysql_date($date); break;
        case 2 : $return = date('d/m/Y', $date); break;

        case 3 : $return = mysql_date_to_timestamp($date); break;
        case 4 : $return = $data_matches[3]."/".$data_matches[2]."/".$data_matches[1]; break;

        case 5 : $return = mktime(0,0,0,$data_matches[2],$data_matches[1], $data_matches[3]); break;
        case 6 : $return = $data_matches[3]."-".$data_matches[2]."-".$data_matches[1]." 00:00:00"; break;
      }
    }
  }
  //var_debug('guess_time', $date, $return);
  return $return;
}

define("GUESS_TIME_TIMESTAMP", 1);
define("GUESS_TIME_MYSQL", 2);
define("GUESS_TIME_FR", 3);
/**
   * Teste timestamp, date mysql (YYYY-MM-DD HH:MM:SS) et date FR (JJ/MM/AAAA)
   * Retourne par défaut un timestamp (possiblement négatif), ou une date mysql ou une date FR
   *
   * @param mixed $date
   */
function guess_time($date, $return_flag = GUESS_TIME_TIMESTAMP) {
  $return = null;
  $data_input = false;
  static $array_actions = array(
    GUESS_TIME_TIMESTAMP => array(
      GUESS_TIME_MYSQL => 1,
      GUESS_TIME_FR => 2,
    ),
    GUESS_TIME_MYSQL => array(
      GUESS_TIME_TIMESTAMP => 3,
      GUESS_TIME_FR => 4,
    ),
    GUESS_TIME_FR => array(
      GUESS_TIME_TIMESTAMP => 5,
      GUESS_TIME_MYSQL => 6,
    )
  );

  if(is_numeric($date)) {
    $data_input = GUESS_TIME_TIMESTAMP;
  }elseif(preg_match('/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/', $date, $matches)) {
    $data_matches = $matches;
    $data_input = GUESS_TIME_MYSQL;
  }elseif(preg_match('#([0-9]{2})/([0-9]{2})/([0-9]{4}) ([0-9]{2}):([0-9]{2}):([0-9]{2})#', $date, $matches)) {
    $data_matches = $matches;
    $data_input = GUESS_TIME_FR;
    if(checkdate($data_matches[2], $data_matches[1], $data_matches[3]) === false) {
      $data_input = false;
    }
  }

  if($data_input !== false) {
    $return = $date;
    if(isset($array_actions[$data_input][$return_flag])) {
      switch($array_actions[$data_input][$return_flag]) {
        case 1 : $return = mysql_timestamp_to_mysql_date($date); break;
        case 2 : $return = date('d/m/Y H:m:s', $date); break;

        case 3 : $return = mysql_date_to_timestamp($date); break;
        case 4 : $return = $data_matches[3]."/".$data_matches[2]."/".$data_matches[1].' '.$data_matches[4].':'.$data_matches[5].':'.$data_matches[6]; break;

        case 5 : $return = mktime( $data_matches[4], $data_matches[5], $data_matches[6], $data_matches[2], $data_matches[1], $data_matches[3]); break;
        case 6 : $return = $data_matches[3]."-".$data_matches[2]."-".$data_matches[1].' '.$data_matches[4].':'.$data_matches[5].':'.$data_matches[6]; break;
      }
    }
  }
  //var_debug('guess_time', $date, $return);
  return $return;
}

/*!
\static

Sends a http request to the specified host. Using https:// requires PHP 4.3.0, and compiled in OpenSSL support.

\param http/https address, only path to send request to eZ Publish.
examples: http://ez.no, https://secure.ez.no, ssl://secure.ez.no, content/view/full/2
\param port, default 80
\param post parameters array (optional), if no post parameters are present, a get request will be send.
\param user agent, default will be eZ Publish
\param passtrough, will send result directly to client, default false

\return result if http request, or return false if an error occurs.
If pipetrough, program will end here.

*/
function sendHTTPRequest( $uri, $port = 80, $data = '', $postParameters = false, $userAgent = 'eZ Publish', $passtrough = true )
{

  preg_match( "/^((http[s]?:\/\/)([a-zA-Z0-9-_.]+))?([\/]?[~]?(\.?[^.]+[~]?)*)/i", $uri, $matches );
  var_debug($matches);
  $protocol = $matches[2];
  $host = $matches[3];
  $path = $matches[4];
  if ( !$path ) {
    $path = '/';
  }
  $method = 'POST';
  if($data == '') {
    if ( $postParameters ) {
      $method = 'POST';
      $dataCount = 0;
      foreach( array_keys( $postParameters ) as $paramName ) {
        if ( $dataCount > 0 ) {
          $data .= '&';
        }
        ++$dataCount;
        if ( !is_array( $postParameters[$paramName] ) ) {
          $data .= urlencode( $paramName ) . '=' . urlencode( $postParameters[$paramName] );
        }else {
          foreach( $postParameters[$paramName] as $value ) {
            $data .= urlencode( $paramName ) . '[]=' . urlencode( $value );
          }
        }
      }
    }else {
      $method = 'GET';
    }
  }

  if ( !$host ) {
    $host = $_SERVER['HTTP_HOST'];
    $filename = $host;
    if ( $path[0] != '/' ) {
      $path = $_SERVER['SCRIPT_NAME'] . '/' . $path;
    }else {
      $path = $_SERVER['SCRIPT_NAME'] . $path;
    }
  }elseif ( !$protocol || $protocol == 'https://' ){
    $filename = 'ssl://' . $host;
  }else {
    $filename = 'tcp://' . $host;
  }

  // make sure we have a valid hostname or call to fsockopen() will fail
  $parsedUrl = parse_url( $filename );
  $ip = isset( $parsedUrl[ 'host' ] ) ? gethostbyname( $parsedUrl[ 'host' ] ) : '';
  $checkIP = ip2long( $ip );
  if ( $checkIP == -1 or $checkIP === false ) {
    return 1;
  }

  $fp = fsockopen( $filename, $port );

  // make sure we have a valid stream resource or calls to other file
  // functions will fail
  if ( !$fp ) {
    return 2;
  }

  $request = $method . ' ' . $path . ' ' . 'HTTP/1.0' . "\r\n" .
  "Host: $host\r\n" .
  "Accept: */*\r\n" .
  "Content-type: application/x-www-form-urlencoded\r\n" .
  "Content-length: " . strlen( $data ) . "\r\n" .
  "User-Agent: $userAgent\r\n" .
  "Pragma: no-cache\r\n" .
  "Connection: close\r\n\r\n";

  var_debug($request.$data);
  fputs( $fp, $request );
  if ( $method == 'POST' ) {
    fputs( $fp, $data );
  }

  $buf = '';
  if ( $passtrough ) {
    ob_end_clean();
    $header = true;

    $character = '';
    while( $header )
    {
      $buffer = $character;
      while ( !feof( $fp ) )
      {
        $character = fgetc( $fp );
        if ( $character == "\r" )
        {
          fgetc( $fp );
          $character = fgetc( $fp );
          if ( $character == "\r" )
          {
            fgetc( $fp );
            $header = false;
          }
          break;
        }else {
          $buffer .= $character;
        }
      }

      header( $buffer );
    }

    header( 'Content-Location: ' . $uri );

    fpassthru( $fp );
    exit;
  }else {
    $buf = '';
    while ( !feof( $fp ) )
    {
      $buf .= fgets( $fp, 128 );
    }
  }

  fclose($fp);
  return $buf;
}

function php_mail($address, $subject, $body, $html_template = false, $debug = false) {
  if(class_exists('PHPMailer')) {
    $mail = new PHPMailer();
    if( SMTP_HOST != '') {
      $mail->IsSMTP(); // set mailer to use SMTP
      $mail->Host = SMTP_HOST;  // specify main and backup server
      if(SMTP_USER != '') {
        $mail->SMTPAuth = true;     // turn on SMTP authentication
        $mail->Username = SMTP_USER;  // SMTP username
        $mail->Password = SMTP_PASS; // SMTP password
      }
    }

    $mail->From = ADMIN_EMAIL;
    $mail->FromName = ADMIN_EMAIL_SENDER;

    if(is_array($address)) {
      // Cas particulier : array(email, name)
      if(count($address) == 2 && Member::check_email($address[0]) === true && !is_array($address[1]) && ($address[1] == '' || Member::check_email($address[0]) !== true)) {
        $mail->AddAddress($address[0], $address[1]);
      }else {
        //Cas général : array(email, array(email, name), email, ...)
        foreach($address as $address_item) {
          if(!is_array($address_item)) {
            $mail->AddAddress($address_item);
          }elseif(count($address_item) == 2) {
            $mail->AddAddress($address_item[0], $address_item[1]);
          }
        }
      }
    }else {
      $mail->AddAddress($address);
    }

    $mail->WordWrap = MAIL_WORDWRAP;                                 // set word wrap to 50 characters
    $mail->IsHTML($html_template);                                  // set email format to HTML
    //$mail->CharSet = 'utf8';
    if($html_template) {
      $PAGE_CONTENU = $body;
      ob_start();
      include(TPL.'pagelayout_mail.tpl.php');
      $body = ob_get_clean();
    }


    $mail->Subject = iconv('utf-8', 'iso-8859-15', $subject);
    $mail->Body    = iconv('utf-8', 'iso-8859-15', $body);
    //    $mail->AltBody = $PAGE_CONTENU;

    if($debug) {
      var_dump($body);
    }else {
      return $mail->Send();
    }
  }
}


/**
 * Fonction donnant l'OS de l'utilisateur, dépend de HTTP_USER_AGENT
 *
 * @return array( string $os, string $os_version )
 */
function which_os ()
{
  $browser_string = ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) ? strtolower( $_SERVER['HTTP_USER_AGENT'] ) : '';
  // initialize variables
  $os = '';
  $os_version = '';
  /*
  packs the os array
  use this order since some navigator user agents will put 'macintosh' in the navigator user agent string
  which would make the nt test register true
  */
  $a_mac = array( 'mac68k', 'macppc' );// this is not used currently
  // same logic, check in order to catch the os's in order, last is always default item
  $a_unix = array( 'freebsd', 'openbsd', 'netbsd', 'bsd', 'unixware', 'solaris', 'sunos', 'sun4', 'sun5', 'suni86', 'sun', 'irix5', 'irix6', 'irix', 'hpux9', 'hpux10', 'hpux11', 'hpux', 'hp-ux', 'aix1', 'aix2', 'aix3', 'aix4', 'aix5', 'aix', 'sco', 'unixware', 'mpras', 'reliant', 'dec', 'sinix', 'unix' );
  // only sometimes will you get a linux distro to id itself...
  $a_linux = array( 'ubuntu', 'kubuntu', 'xubuntu', 'mepis', 'xandros', 'linspire', 'winspire', 'sidux', 'kanotix', 'debian', 'opensuse', 'suse', 'fedora', 'redhat', 'slackware', 'slax', 'mandrake', 'mandriva', 'gentoo', 'sabayon', 'linux' );
  $a_linux_process = array ( 'i386', 'i586', 'i686' );// not use currently
  // note, order of os very important in os array, you will get failed ids if changed
  $a_os = array( 'beos', 'os2', 'amiga', 'webtv', 'mac', 'nt', 'win', $a_unix, $a_linux );

  //os tester
  $i_count = count( $a_os );
  for ( $i = 0; $i < $i_count; $i++ )
  {
    // unpacks os array, assigns to variable
    $s_os = $a_os[$i];

    // assign os to global os variable, os flag true on success
    // !stristr($browser_string, "linux" ) corrects a linux detection bug
    if ( !is_array( $s_os ) && stristr( $browser_string, $s_os ) && !stristr( $browser_string, "linux" ) )
    {
      $os = $s_os;

      switch ( $os )
      {
        case 'win':
          if ( strstr( $browser_string, '95' ) )
          {
            $os_version = '95';
          }
          elseif ( ( strstr( $browser_string, '9x 4.9' ) ) || ( strstr( $browser_string, 'me' ) ) )
          {
            $os_version = 'me';
          }
          elseif ( strstr( $browser_string, '98' ) )
          {
            $os_version = '98';
          }
          elseif ( strstr( $browser_string, '2000' ) )// windows 2000, for opera ID
          {
            $os_version = 5.0;
            $os = 'nt';
          }
          elseif ( strstr( $browser_string, 'xp' ) )// windows 2000, for opera ID
          {
            $os_version = 5.1;
            $os = 'nt';
          }
          elseif ( strstr( $browser_string, '2003' ) )// windows server 2003, for opera ID
          {
            $os_version = 5.2;
            $os = 'nt';
          }
          elseif ( strstr( $browser_string, 'vista' ) )// windows vista, for opera ID
          {
            $os_version = 6.0;
            $os = 'nt';
          }
          elseif ( strstr( $browser_string, 'ce' ) )// windows CE
          {
            $os_version = 'ce';
          }
          break;
        case 'nt':
          if ( strstr( $browser_string, 'nt 6.1' ) )// windows 7
          {
            $os_version = 6.1;
            $os = 'nt';
          }
          elseif ( strstr( $browser_string, 'nt 6.0' ) )// windows vista/server 2008
          {
            $os_version = 6.0;
            $os = 'nt';
          }
          elseif ( strstr( $browser_string, 'nt 5.2' ) )// windows server 2003
          {
            $os_version = 5.2;
            $os = 'nt';
          }
          elseif ( strstr( $browser_string, 'nt 5.1' ) || strstr( $browser_string, 'xp' ) )// windows xp
          {
            $os_version = 5.1;//
          }
          elseif ( strstr( $browser_string, 'nt 5' ) || strstr( $browser_string, '2000' ) )// windows 2000
          {
            $os_version = 5.0;
          }
          elseif ( strstr( $browser_string, 'nt 4' ) )// nt 4
          {
            $os_version = 4;
          }
          elseif ( strstr( $browser_string, 'nt 3' ) )// nt 4
          {
            $os_version = 3;
          }
          break;
        case 'mac':
          if ( strstr( $browser_string, 'os x' ) )
          {
            $os_version = 10;
          }
          break;
        default:
          break;
      }
      break;
    }
    // check that it's an array, check it's the second to last item
    //in the main os array, the unix one that is
    elseif ( is_array( $s_os ) && ( $i == ( count( $a_os ) - 2 ) ) )
    {
      $i_count = count($s_os);
      for ($j = 0; $j < $i_count; $j++)
      {
        if ( stristr( $browser_string, $s_os[$j] ) )
        {
          $os = 'unix'; //if the os is in the unix array, it's unix, obviously...
          $os_version = ( $s_os[$j] != 'unix' ) ? $s_os[$j] : '';// assign sub unix version from the unix array
          break;
        }
      }
    }
    // check that it's an array, check it's the last item
    //in the main os array, the linux one that is
    elseif ( is_array( $s_os ) && ( $i == ( count( $a_os ) - 1 ) ) )
    {
      $i_count = count($s_os);
      for ($j = 0; $j < $i_count; $j++)
      {
        if ( stristr( $browser_string, $s_os[$j] ) )
        {
          $os = 'lin';
          // assign linux distro from the linux array, there's a default
          //search for 'lin', if it's that, set version to ''
          $os_version = ( $s_os[$j] != 'linux' ) ? $s_os[$j] : '';
          break;
        }
      }
    }
  }

  // pack the os data array for return to main function
  $os_data = array( $os, $os_version );
  return $os_data;
}



  //Nombre standard d'éléments par page pour la pagination
  define('NB_PER_PAGE', 20);
  define('NB_VISIBLE_LINKS', 12);
  /**
   * Fonction de pagination
   *
   * @param string $codepage Code de la page des liens
   * @param int $nb_total Nombre d'élément total
   * @param int $current_page Page courante
   * @param int $nb_per_page Nombre d'éléments par page
   * @return string
   */
  function nav_page($codepage, $nb_total, $current_page, $nb_per_page = NB_PER_PAGE) {
    $nb_page = ceil($nb_total / $nb_per_page);

    $return = '
    <ul class="nav">';

    if($nb_page == 1 || $current_page == 1) {
      $return .= '
      <li><a href="#" class="inactive" onclick="return false">&lt; &lt;</a></li>
      <li><a href="#" class="inactive" onclick="return false">&lt;</a></li>';
    }else {
      $return .= '
      <li><a href="'.get_page_url($codepage, true, array('p' => 1, 'nb_per_page' => $nb_per_page)).'">&lt; &lt;</a></li>
      <li><a href="'.get_page_url($codepage, true, array('p' => $current_page - 1, 'nb_per_page' => $nb_per_page)).'">&lt;</a></li>';
    }

    if($nb_page > NB_VISIBLE_LINKS) {
      $nb_liens = NB_VISIBLE_LINKS;
    }else {
      $nb_liens = $nb_page;
    }

    for($i = 1; $i <= $nb_liens; $i++) {
      if($current_page < ceil(NB_VISIBLE_LINKS / 2) || $nb_page <= NB_VISIBLE_LINKS) {
        $j = $i;
      }elseif ($current_page >= $nb_page - ceil(NB_VISIBLE_LINKS / 2)) {
        $j = $nb_page - NB_VISIBLE_LINKS + $i;
      }else {
        $j = $current_page - ceil(NB_VISIBLE_LINKS / 2) + $i;
      }
      $return .= '
      <li><a href="'.get_page_url($codepage, true, array('p' => $j, 'nb_per_page' => $nb_per_page)).'"'.($j == $current_page?' class="inactive"':'').'>'.$j.'</a></li>';
    }

    if($nb_page == 1 || $current_page == $nb_page) {
      $return .= '
      <li><a href="#" class="inactive" onclick="return false">&gt;</a></li>
      <li><a href="#" class="inactive" onclick="return false">&gt; &gt;</a></li>';
    }else {
      $return .= '
      <li><a href="'.get_page_url($codepage, true, array('p' => $current_page + 1, 'nb_per_page' => $nb_per_page)).'">&gt;</a></li>
      <li><a href="'.get_page_url($codepage, true, array('p' => $nb_page, 'nb_per_page' => $nb_per_page)).'">&gt; &gt;</a></li>';
    }

    $return .= '
    </ul>';

    return $return;
  }


  /**
   * texte_formatte => texteFormatte
   *
   * @param string $str
   * @param bool $capitalise_first_char
   * @return string
   */
  function to_camel_case($str, $capitalise_first_char = false) {
    if($capitalise_first_char) {
      $str[0] = strtoupper($str[0]);
    }
    $func = create_function('$c', 'return "_".strtoupper($c[1]);');
    return preg_replace_callback('/_([a-z])/', $func, $str);
  }

  /**
   * texte_formatte => Texte Formatte
   *
   * @param string $str
   * @return string
   */
  function to_readable($str) {
    $tmp = explode('_', $str);
    $tmp = array_map('ucfirst', $tmp);
    return implode(' ', $tmp);
  }

  /**
  * Get a value from $_POST / $_GET
  * if unavailable, take a default value
  *
  * @param string $key Value key
  * @param mixed $defaultValue (optional)
  * @return mixed Value
  */
  function getValue($key, $defaultValue = null)
  {
    if (!isset($key) OR empty($key) OR !is_string($key))
      return false;
    $ret = (isset($_POST[$key]) ? $_POST[$key] : (isset($_GET[$key]) ? $_GET[$key] : $defaultValue));

    if (is_string($ret) === true)
      $ret = urldecode(preg_replace('/((\%5C0+)|(\%00+))/i', '', urlencode($ret)));
    return !is_string($ret)? $ret : stripslashes($ret);
  }
  
  /**
   * Generates a random gaussian number
   *
   * @see http://www.protonfish.com/random.shtml
   */
  function mt_gaussrand() {
    $randmax = mt_getrandmax();
    return
      ( mt_rand() / $randmax * 2 - 1 ) +
      ( mt_rand() / $randmax * 2 - 1 ) +
      ( mt_rand() / $randmax * 2 - 1 );
  }

?>
