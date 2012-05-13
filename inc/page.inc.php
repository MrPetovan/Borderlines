<?php
  include_once("fonctions.inc.php");

  function get_current_page() {
    return PAGE_CODE;
  }

  function get_page_url($page, $root = true, $params = array()) {
    return Page::get_page_url($page, $root, $params);
  }

  function get_action_url($page, $root = true, $params = array()) {
    return Page::get_action_url($page, $root, $params);
  }

  /**
   * Redirection selon le code de la page
   *
   * @param string $page Code de la page
   */
  function page_redirect($page, $params = array()) {
    Page::page_redirect($page, $params);
  }

  /**
   * Fonction permettant de savoir si l'utilisateur courant est enregistré et connecté.
   * Retourne l'identifiant de l'utilisateur courant ou false si visiteur.
   *
   * @return int Identifiant de l'utilisateur ou false
   */
  function is_logged_in() {
    if(isset($_COOKIE['adrd_remember_me'])) {
      $membre = Member::db_get_by_remember_token($_COOKIE['adrd_remember_me']);
      if($membre) {
        $_SESSION['sess']['logged_in'] = $membre->get_id();
      }
    }
    if(isset($_SESSION['sess']['logged_in'])) {
      return $_SESSION['sess']['logged_in'];
    }else {
      return false;
    }
  }

  /**
   * Fonction permettant de déterminer si l'utilisateur courant est administrateur
   *
   * @return bool Droits administrateur ou pas
   */
  function is_admin() {
    if(!defined('IS_ADMIN')) {
      if($user_id = Member::get_current_user_id()) {
        $membre = new Member($user_id);

        define('IS_ADMIN', ADMIN_LEVEL == $membre->get_niveau());
      }else {
        define('IS_ADMIN', false);
      }
    }
    return IS_ADMIN;
  }
?>