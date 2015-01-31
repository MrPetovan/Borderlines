<?php
  Member::del_current_user_id();
  setcookie('adrd_remember_me', '', time()-60*60*24*30);
  session_destroy();

  $error_code_login = null;

  if(PAGE_CODE != 'logout') {
    if(isset($_POST['submit_login']) || isset($_POST['submit_login_x'])) {
      if(isset($_POST['email'])) {
        if(isset($_POST['pass'])) {
          if($membre = Member::db_get_membre_by_email($_POST['email'])) {
            //var_debug($_POST['pass'], Member::password_crypt($_POST['pass']), $membre->get_password());
            //var_debug($membre, Member::password_crypt($_POST['pass']), $membre->get_password());
            if(Member::password_crypt($_POST['pass']) == $membre->get_password()) {
              if(isset($_POST['remember_me'])) {
                $remember_token = md5($membre->get_prenom().'-'.$membre->get_nom().'-'.time().'-'.mt_rand());
                $membre->set_remember_token($remember_token);
                $membre->db_save();
                setcookie(strtolower( SITE_NAME ).'_remember_me', $remember_token, time()+60*60*24*30);
              }
              $error_code_login = '';
            }else {
              $error_code_login = 5;
            }
          }else {
            $error_code_login = 4;
          }
        }else {
          $error_code_login = 3;
        }
      }else {
        $error_code_login = 2;
      }
    }
  }else {
    site_redirect();
  }

  if(!is_null($error_code_login) && $error_code_login === '') {
    Member::set_current_user_id($membre->get_id());

    if(isset($_SESSION['sess']['url_redirect'])) {
      $url = $_SESSION['sess']['url_redirect'];
      unset($_SESSION['sess']['url_redirect']);
    }else {
      $url = get_page_url('dashboard');
    }

    if($membre->get_niveau() == 1) {
      $url = get_page_url('admin_member');
    }

    redirect($url);
  }

  if(isset($_SESSION['sess']['url_redirect'])) {
    unset($_SESSION['sess']['url_redirect']);
  }
?>