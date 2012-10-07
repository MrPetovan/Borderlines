<?php
/**
 * Dispatcher général du site
 *
 *
 */

  session_start();

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

  //Relative URL used, calculate absolute URL
  $protocol = strtolower(substr($_SERVER['SERVER_PROTOCOL'], 0, strpos($_SERVER['SERVER_PROTOCOL'], "/", 0)));
  // Constante principale, c'est l'URL absolue de la base du site
  define("URL_ROOT", $protocol."://".$_SERVER['HTTP_HOST'].URL_ROOT_RELATIVE.'/');
  // PATH absolu de la base du site
  define('DIR_ROOT', dirname($_SERVER['SCRIPT_FILENAME']) .'/');
  // PATH du répertoire d'inclusions
  define('DATA', DIR_ROOT.'data/');
  // PATH du répertoire d'inclusions
  define('INC', DIR_ROOT.'inc/');
  // PATH du répertoire de librairies
  define('LIB', DIR_ROOT.'lib/');
  // PATH du répertoire des templates
  define('TPL', DIR_ROOT.'template/');
  // URL du répertoire des images
  define('IMG', URL_ROOT.'img/');
  // URL du répertoire des images utilisées dans les fichiers HTML
  define('IMG_HTML', IMG.'img_html/');
  // URL des fichiers flash
  define('URL_FLASH', URL_ROOT.'flash/');

  // Constante de debug SQL général
  define('DEBUG_SQL', false);

  set_include_path(implode(PATH_SEPARATOR, array(
    INC,
    LIB,
    get_include_path(),
  )));

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

  // Fichier de paramétrage
  require_once(INC.'constantes.inc.php');
  // Fonctions MySQL
  require_once(INC.'db.inc.php');
  // Fonctions générales
  require_once(INC.'fonctions.inc.php');
  // i18n functions
  require_once(INC.'i18n.inc.php');
  // Fonctions liées aux pages
  require_once(INC.'page.inc.php');
  // Fonctions système de fichier
  require_once(INC.'files.inc.php');
  // Fonctions envoi de mail
  require_once(INC.'PHPMailer/class.phpmailer.php');
  // Fonctions html
  require_once(INC.'html.class.php');

  //Includes classes
  require_once('data/db_object.class.php');

  require_once( DATA.'order_type/iorder.php');
  require_once( INC.'borderlines.inc.php');

  $flag_action = false;
  if(! mysql_uconnect(DB_HOST, DB_USER, DB_PASS, DB_BASE)) {
    //$data_include['dsp'] = 'error_db.php';
    $data_include['title'] = 'Base de donnée inaccessible';
    $data_include['tpl'] = 'pagelayout_error.tpl.php';
    $data_include['dsp'] = false;

  }else {
    mysql_uquery("SET NAMES 'utf8'");
    if(is_admin()) {
      error_reporting(E_ALL);
    }
    if(isset($_GET[PARAM_PAGE])) {
      $PAGE_CODE = $_GET[PARAM_PAGE];
      unset($_GET[PARAM_PAGE]);
    }else {
      $PAGE_CODE = PAGE_DEFAUT;
    }

    $CURRENT_PAGE = Page::db_get_page_by_code( $PAGE_CODE );

    if($CURRENT_PAGE) {
      //Origin & Mail
      $redirect = false;

      if(!isset($_SESSION['origin'])) {
        $_SESSION['origin'] = 'site';
      }

      if(isset($_GET['origin']) && count($_POST) == 0) {
        $redirect = true;

        $_SESSION['origin'] = $_GET['origin'];
        unset($_GET['origin']);
      }
      if(isset($_GET['email']) && !Member::get_logged_user()) {
        if($member = Member::db_get_membre_by_email($_GET['email'])) {
          if($member->get_niveau() == 0) {
            $redirect = true;
            unset($_GET['email']);
            Member::set_current_user_id($member->get_id());
          }
        }
      }

      if($redirect) {
        redirect(get_page_url($PAGE_CODE, true, $_GET));
      }

      //TPL
      if(!$CURRENT_PAGE->get_tpl()) {
        //Template par defaut
        $CURRENT_PAGE->set_tpl(PAGELAYOUT_DEFAUT);
        $_SESSION['current_tpl'] = PAGELAYOUT_DEFAUT;
      }else {

        if($CURRENT_PAGE->get_tpl() == "SESSION_PAGELAYOUT" && isset($_SESSION['current_tpl']) && $_SESSION['current_tpl'] != '') {
          $CURRENT_PAGE->set_tpl($_SESSION['current_tpl']);
        }else {
          $_SESSION['current_tpl'] = $CURRENT_PAGE->get_tpl_file();
        }
      }
      if($CURRENT_PAGE->get_login_required() && !is_logged_in()
        || $CURRENT_PAGE->get_admin_required() && !is_admin()) {
        $_SESSION['sess']['url_redirect'] = $_SERVER['REQUEST_URI'];
        page_redirect(PAGE_LOGIN);
      }
    }else {
      $PAGE_CODE = PAGE_ERROR;
      $CURRENT_PAGE = Page::db_get_page_by_code( $PAGE_CODE );
    }
  }

  $CURRENT_USER = Member::get_current_user();

  //setlocale( LC_TIME, 'en_US.UTF8');
  if( isset($_POST['setlocale']) ) {
    $locale = $_POST['locale'];
  }else {
    $locale = isset($_COOKIE['locale']) ? $_COOKIE['locale'] : $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    $locale = str_replace( '-', '_', array_pop( array_reverse( explode( ',', $locale) ) ) );
  }

  if( !in_array( $locale, $locale_array = explode(',', LOCALES ) ) ) {
    $locale = $locale_array[0];
  }

  setcookie("locale", $locale, time() + 30 * 24 * 3600);

  define('LOCALE', $locale);

  $i18n_replacements = load_translations( $locale );

  setlocale(LC_ALL, $locale . '.UTF8' );

  define('PAGE_CODE', $PAGE_CODE);
  // ACT
  if($CURRENT_PAGE->get_act()) {
    require($CURRENT_PAGE->get_act());
  }

  //DSP
  if($CURRENT_PAGE->get_dsp()) {
    $PAGE_TITRE = '';

    ob_start();
    require($CURRENT_PAGE->get_dsp());
    $PAGE_CONTENU = ob_get_clean();

    require(TPL.$CURRENT_PAGE->get_tpl_file());
  }

  if(DEBUG_SQL){
    mysql_log();
  }
?>