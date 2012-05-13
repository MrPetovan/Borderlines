<?php
  if(isset($page_mod)) {

  }else {
    if(isset($_GET['id'])) {
      $id = $_GET['id'];
      $page_mod = DBObject::instance('Page', $id);
    }else {
      $id = null;

      if(isset($_POST) && count($_POST) > 0) {
        $data = $_POST;

        $page_mod = new Page(
          $id,
          $data['code'],
          $data['act'],
          $data['dsp'],
          $data['tpl'],
          $data['login_required'],
          $data['admin_required']
        );
      }else {
        $page_mod = DBObject::instance('Page');
      }
    }
  }

  if(is_null($page_mod->get_id())) {
    $PAGE_TITRE = 'Ajouter une page';
    $subtitle = 'Ajouter une page';
  }else {
    $PAGE_TITRE = 'Modifier la page "'.$page_mod->get_code().'"';
    $subtitle = 'Modifier la page';
  }

  if(isset($tab_error)) {
    if(isset($_SESSION['tab_error'])) {
      $tab_error = $_SESSION['tab_error'];
      unset($_SESSION['tab_error']);
    }
    if(is_array($tab_error)) {
      if(count($tab_error) == 0) {
        echo '<p class="msg">Les informations sur la page ont été enregistrées.</p>';
      }else {
        foreach ($tab_error as $error) {
          $tab_msg[] = Page::get_message_erreur($error);
        }
        $tab_msg = array_unique($tab_msg);
        foreach ($tab_msg as $msg_error) {
          echo '<p class="error">'.wash_utf8($msg_error).'</p>';
        }
      }
    }
  }

  echo '<h2>Administration des pages</h2>
  <h3>'.$subtitle.'</h3>
  <div class="wording">';
  echo $page_mod->html_get_form(get_page_url(PAGE_CODE), $_POST);
  echo '</div>';
?>