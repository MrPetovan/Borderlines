<?php

class Page extends DBObject {

  protected $_code = '';
  protected $_act = '';
  protected $_dsp = '';
  protected $_tpl = '';
  protected $_login_required = 0;
  protected $_admin_required = 0;
  protected $_rewrite_pattern = '';

  const PAGE_MESSAGE_NOTICE = 1;
  const PAGE_MESSAGE_WARNING = 2;
  const PAGE_MESSAGE_ERROR = 3;

	/* ACCESSEURS */
  public static function get_table_name() {
    return "page";
  }

  public function get_tpl_file() {
    if($this->get_tpl() && $this->get_tpl() != "SESSION_PAGELAYOUT") {
      return $this->_tpl;
    }else {
      return PAGELAYOUT_DEFAUT;
    }
  }

  public function is_login_required() { return ($this->_login_required == 1); }
  public function is_admin_required() { return ($this->_admin_required == 1); }

  public function set_login_required($login_required) {
    if($login_required) $data = 1; else $data = 0; $this->_login_required = $data;
  }
  public function set_admin_required($admin_required) {
    if($admin_required) $data = 1; else $data = 0; $this->_admin_required = $data;
  }

  public function get_rewrite_pattern_safe() {
    $return = $this->get_rewrite_pattern();
    if(!$return) {
      $return = REWRITE_PATTERN_DEFAULT;
    }
    return $return;
  }

  /**
   * Système de message simple : On remplit la variable de session avec un message.
   * Dès qu'une page demande à afficher les messages, on vide la variable.
   *
   * @param string $message
   * @param int $type
   */
  public static function set_message( $message, $type = self::PAGE_MESSAGE_NOTICE ) {
    self::add_message( $message, $type );
  }

  public static function add_message( $message, $type = self::PAGE_MESSAGE_NOTICE ) {
    $_SESSION['page']['message'][$type][] = $message;
  }

  public static function get_message( $type = self::PAGE_MESSAGE_NOTICE ) {
    $return = false;

    if( isset( $_SESSION['page']['message'][$type] ) ) {
      $return = $_SESSION['page']['message'][$type];
      unset( $_SESSION['page']['message'][$type] );
    }
    return $return;
  }

  /* FONCTIONS SQL */
  public static function db_get_page_by_code($code) {
    $return = false;

    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `code` LIKE ".mysql_ureal_escape_string($code)."
LIMIT 0,1";

    return self::sql_to_object($sql);
  }

  public static function db_get_by_tpl($token) {
    $return = false;

    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `remember_token` LIKE ".mysql_ureal_escape_string($token)."
LIMIT 0,1";

    return self::sql_to_object($sql);
  }

  /* FONCTIONS HTML */
  public function html_get_form($form_url, $post = array()) {
    if(isset($post['code']))
      { $code = $post['code']; }             else { $code = $this->get_code(); }
    if(isset($post['act']))
      { $act = $post['act']; }      else { $act = $this->get_act(); }
    if(isset($post['dsp']))
      { $dsp = $post['dsp']; }      else { $dsp = $this->get_dsp(); }
    if(isset($post['tpl']))
      { $tpl = $post['tpl']; }      else { $tpl = $this->get_tpl(); }
    if(isset($post['login_required']))
      { $login_required = $post['login_required']; } else { $login_required = $this->get_login_required(); }
    if(isset($post['admin_required']))
      { $admin_required = $post['admin_required']; } else { $admin_required = $this->get_admin_required(); }
    if(isset($post['rewrite_pattern']))
      { $rewrite_pattern = $post['rewrite_pattern']; } else { $rewrite_pattern = $this->get_rewrite_pattern(); }

    $return = '
  <form action="'.wash_utf8($form_url).'" method="post" enctype="multipart/form-data">
    '.HTMLHelper::genererInputHidden('id', $this->get_id()).'
    <fieldset>
      <legend>Text fields</legend>
      <p>'.HTMLHelper::genererInputText('code', $code, array('style' => 'width: 300px'), "Code").'</p>
      <p>'.HTMLHelper::genererInputText('act', $act, array('style' => 'width: 300px'), "Fichier ACTion").'</p>
      <p>'.HTMLHelper::genererInputText('dsp', $dsp, array('style' => 'width: 300px'), "Fichier DiSPlay").'</p>
      <p>'.HTMLHelper::genererInputText('tpl', $tpl, array('style' => 'width: 300px'), "Fichier TemPLate").'</p>
      <p>'.HTMLHelper::genererInputCheckBox('login_required', '1', $login_required, array(), "Page membre").'</p>
      <p>'.HTMLHelper::genererInputCheckBox('admin_required', '1', $admin_required, array(), "Page Administtrateur").'</p>
      <p>'.HTMLHelper::genererInputText('rewrite_pattern', $rewrite_pattern, array('style' => 'width: 300px'), "SchÃ©ma Rewrite URL").'</p>
    </fieldset>
    <fieldset>
      '.HTMLHelper::genererInputSubmit('page_submit', 'Enregistrer').'
    </fieldset>
  </form>';

    return $return;
  }


  public static function get_message_erreur($num_error) {
    switch($num_error) {
      case 1 :
        $return = "Code vide.";
        break;
      case 2 :
        $return = "Fichier DSP vide.";
        break;
      default:
        $return = "Erreur formulaire.";
    }

    return $return;
  }

  public static function display_messages() {
    $messages['alert-danger'] = Page::get_message(Page::PAGE_MESSAGE_ERROR);
    $messages['alert-warning'] = Page::get_message(Page::PAGE_MESSAGE_WARNING);
    $messages['alert-success'] = Page::get_message(Page::PAGE_MESSAGE_NOTICE);

    if( count( $messages['alert-danger'] ) || count( $messages['alert-warning'] ) || count( $messages['alert-success'] ) ) {
      echo '
        <div id="messages">';
      foreach( $messages as $message_class => $message_list ) {
        if( $message_list ) {
          echo '
            <div class="alert '.$message_class.'">
              <ul>
                <li>'.implode('</li>
                <li>', $message_list ).'</li>
              </ul>
            </div>';
        }
      }
      echo '
        </div>';
    }
  }

  /**
   * Mets Ã  jour les champs de l'objet en fonctions des donnÃ©es POST et FILES prÃ©sente.
   * Effectue les vÃ©rifications basiques pour mettre Ã  jour les champs
   * Retourne une liste de codes d'erreur :
   * - vide : Pas d'erreur
   * -  1 : Code vide.
   * -  2 : Fichier DSP vide
   *
   * @param array $post_data DonnÃ©e POST ($_POST)
   * @param array $file_data DonnÃ©e FILES ($_FILES)
   */
  public function load_from_html_form($post_data, $file_data) {
    parent::load_from_html_form($post_data, $file_data);
    $return = array();

    if(isset($post_data['code']) && $post_data['code'] != '') {
      $this->set_code($post_data['code']);
    }elseif($this->get_code() == '') {
      $return[] = 1;
    }

    if(isset($post_data['dsp']) && $post_data['dsp'] != '') {
      $this->set_dsp($post_data['dsp']);
    }elseif($this->get_dsp() == '') {
      $return[] = 2;
    }

    return $return;
  }

  public static function get_url($code_page, $params = array(), $root = true) {
    return self::get_page_url($code_page, $root, $params);
  }

  /**
   * Retourne l'URL de la page selon son code
   *
   * @param string $page Code de la page
   * @return string URL de la page
   */
  public static function get_page_url($code_page, $root = true, $params = array()) {
    $return = '';
    if($root) $return = URL_ROOT;
    $page = Page::db_get_page_by_code($code_page);
    if($page) {
      if($page->get_dsp()) {
        if(REWRITE_URL_ACTIVE) {
          if(($rewrite_pattern = $page->get_rewrite_pattern_safe()) && count($params) != 0) {
            $rewrite_pattern = str_replace("{page}", $code_page, $rewrite_pattern);
            foreach($params as $name => $value) {
              if(strpos($rewrite_pattern, "{".$name."}") !== false) {
                $rewrite_pattern = str_replace("{".$name."}", urlencode($value), $rewrite_pattern);
                unset($params[$name]);
              }
            }
            //ParamÃ¨tres supplÃ©mentaires : ?param1=value1&param2=value2...
            if(count($params)) {
              $rewrite_pattern .= "?";
            }
            $return .= $rewrite_pattern;
          }else {
            $return .= $code_page.".html";
          }
        }else {
          $return .= "?".PARAM_PAGE."=".$code_page;
          if(isset($params[PARAM_PAGE])) {
            unset($params[PARAM_PAGE]);
          }
          if(count($params)) {
            $return .= "&";
          }
        }

        if(count($params)) {
          //param1=value1&param2=value2...
          $params_url = array();
          foreach($params as $name => $value) {
            $params_url[] = $name . "=" . urlencode($value);
          }
          $return .= implode('&', $params_url);
        }
      }
    }
    unset($page);
    return $return;
  }

  /**
   * Retourne l'URL de l'action selon son code
   *
   * @param string $code_page Code de l'action
   * @return string URL de l'action
   */
  /*public static function get_action_url($code_page, $root = true, $params = array()) {
    $return = '';
    if($root) $return = URL_ROOT;
    $page = Page::db_get_page_by_code($code_page);
    if($page) {
      if($page->get_act()) {
        if(REWRITE_URL_ACTIVE) {
          $return .= $code_page.".submit.html";
          if(count($params)) {
            $return .= "?";
          }
        }else {
          $return .= "?".PARAM_PAGE."=".$code_page;
          if(isset($params[PARAM_PAGE])) {
            unset($params[PARAM_PAGE]);
          }
          if(count($params)) {
            $return .= "&";
          }
        }
        if(count($params)) {
          //param1=value1&param2=value2...
          foreach($params as $name => $value) {
            $params_url[] = $name."=".$value;
          }
          $return .= implode('&', $params_url);
        }
      }
    }
    unset($page);
    return $return;
  }*/

  /**
   * Redirection selon le code de la page
   *
   * @param string $page Code de la page
   */
  public static function page_redirect($page, $params = array()) {
    if($page) {
      $redirect = Page::get_page_url($page, true, $params);
      redirect($redirect);
    }
  }

  public static function redirect( $page, $params = array()) {
    self::page_redirect($page, $params );
  }
}
?>