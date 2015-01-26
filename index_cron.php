<?php
/**
 * Dispatcher général du site
 *
 *
 */

  session_start();

  ini_set('display_errors', 1);
  error_reporting(E_ALL);

  /**
   * Détermination des PATH et URL absolus pour être utilisés dans tout le site
   *
   * Note : tous les PATH et URL se finissent par '/'
   */

  $URL_ROOT_RELATIVE = dirname($_SERVER['PHP_SELF']);
  if($URL_ROOT_RELATIVE[strlen($URL_ROOT_RELATIVE) - 1] == '/') {
    $URL_ROOT_RELATIVE = substr($URL_ROOT_RELATIVE, 0, -1);
  }

  define('URL_ROOT_RELATIVE', $URL_ROOT_RELATIVE);

  // Constante principale, c'est l'URL absolue de la base du site
  define("URL_ROOT", 'http://scramblednations.com/');
  // PATH absolu de la base du site
  define('DIR_ROOT', dirname($_SERVER['SCRIPT_FILENAME']) .'/');
  // PATH du répertoire d'inclusions
  define('DATA', DIR_ROOT.'data/');
  // PATH du répertoire d'inclusions
  define('INC', DIR_ROOT.'inc/');
  // PATH du répertoire des templates
  define('TPL', DIR_ROOT.'template/');

  // Constante de debug SQL général
  define('DEBUG_SQL', false);

  // Suppression des antislashes
  if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value)
    {
      $value = is_array($value) ?
               array_map('stripslashes_deep', $value) :
               stripslashes($value);
      return $value;
    }

    $_POST = array_map('stripslashes_deep', $_POST);
    $_GET = array_map('stripslashes_deep', $_GET);
    $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
  }

  $flag_prod = true;
  // Fichier de paramétrage
  require_once(INC.'constantes.inc.php');
  // Fonctions MySQL
  require_once(INC.'db.inc.php');
  // Fonctions générales
  require_once(INC.'fonctions.inc.php');
  // i18n functions
  require_once(INC.'i18n.inc.php');
  // Extending GD functions
  require_once(INC.'gd.inc.php');
  // Fonctions liées aux pages
  require_once(INC.'page.inc.php');
  // Fonctions système de fichier
  require_once(INC.'files.inc.php');
  // Fonctions envoi de mail
  require_once(INC.'PHPMailer/class.phpmailer.php');

  //Includes classes
  require_once('data/db_object.class.php');

  require_once( DATA.'order_type/iorder.php');
  require_once( INC.'borderlines.inc.php');

  if( isset( $_SERVER['REMOTE_ADDR'] ) ) redirect(URL_ROOT);

  $flag_action = false;
  if(! mysql_uconnect(DB_HOST, DB_USER, DB_PASS, DB_BASE)) {
    echo "DB connection error";
    die();
  }else {
    mysql_uquery("SET NAMES 'utf8'");
  }

  define('LOCALE', 'en-US.utf8');

  $options = getopt('cgw:');

  echo date('[Y-m-d H:i:s]').' '.implode(' ', $_SERVER['argv'])."\n";

  if( isset($options['c'])) {
    $game_list = Game::db_get_nonended_game_list();

    foreach( $game_list as $game ) {
      if( $game->started ) {
        $game->compute_auto();
      }else {
        if( $game->min_players && count( $game->get_game_player_list() ) >= $game->min_players ) {
          $game->start();
        }
      }
    }
  }

  if( isset($options['g'])) {
    echo "Generating worlds\n";
    var_dump($options);
    if( isset($options['w'])) {
      $world = World::instance( $options['w'] );
      echo $world->name."\n";
      $world->initialize_territories();
    }
  }


?>