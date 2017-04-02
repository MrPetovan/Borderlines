<?php
/**
 * Fonction "magique" PHP 5
 *
 * Pour une classe $classname donnée, cherche à inclure le fichier depuis
 * data/$classname/$classname.class.php
 *
 * Pour Member : data/member/member.class.php
 *
 * @param string $classname
 */
function __autoload($classname) {
  //var_debug($classname, 'data/'.strtolower($classname).'/'.strtolower($classname).'.class.php');
  include_once('data/'.strtolower($classname).'/'.strtolower($classname).'.class.php');
}

define('SITE_NAME', 'Site');

define('PARAM_PAGE', 'page');
define('PARAM_ACTION', 'action');

// Liste des hostname de prod, séparer par des |
define("PROD_HOST", 'host.com|xxx.net' );

define("DB_CHARSET", "utf8");

// Codes des pages standard
define('PAGE_DEFAUT', 'accueil');
define('PAGE_LOGIN', 'login');
define('PAGE_ERROR', 'erreur');

// Available languages, comma-separated
define('LOCALES', 'en_US');

// Vérification du nom de domaine pour détermination PROD/DEV
function match_host($host, $match) {
  return substr($host, -strlen($match)) == $match;
}

if( !isset( $flag_prod ) ) {
  $host_array = explode('|', PROD_HOST);
  $flag_prod = false;
  do {
    $flag_prod = $flag_prod || match_host( $_SERVER['HTTP_HOST'], current($host_array));
  }while(!$flag_prod && next($host_array));
}
/**
 * Configurations basées sur l'environnement
 */
if( $flag_prod) {
  define('PROD', true);
  error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
  /**
   * Flag URL Rewriting local (génération des url)
   *
   * Attention : le fichier .htaccess à la racine doit être manuellement supprimé si le
   * rewriting n'est pas activé sur le site
   */
  define('REWRITE_URL_ACTIVE', false);

  // Accès MySQL
  define('DB_HOST', 'localhost');
  define('DB_USER', 'user');
  define('DB_PASS', 'password');
  define('DB_BASE', 'base');
  define('MYSQLDUMP_PATH', '');

  // Configuration envoi de mail
  define("SMTP_HOST", "");
  define("SMTP_USER", "");
  define("SMTP_PASS", "");

}else {
  define('PROD', false);

  error_reporting(E_ALL);

  define('REWRITE_URL_ACTIVE', false);

  define('DB_HOST', 'localhost');
  define('DB_USER', 'user');
  define('DB_PASS', 'password');
  define('DB_BASE', 'base');
  define('MYSQLDUMP_PATH', 'path/to/mysqldump');

  define("SMTP_HOST", "smtp.example.com");
  define("SMTP_USER", "");
  define("SMTP_PASS", "");
}

/******************************
 * Constantes locales au site *
 ******************************/

// ID du membre administrateur
define("ID_ADMIN", 1);
// Niveau utilisateur d'administration
define("ADMIN_LEVEL", 1);
// Longueur des lignes d'un mail texte
define('MAIL_WORDWRAP', 80);

// Paramètre expéditeur des mails
define("ADMIN_EMAIL", "admin@host.com");
define("ADMIN_EMAIL_SENDER", SITE_NAME);
// Template de page par défaut
define('PAGELAYOUT_DEFAUT' , 'pagelayout.tpl.php');

define('REWRITE_PATTERN_DEFAULT', '{page}.html');
?>