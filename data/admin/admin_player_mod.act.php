<?php

  if(isset($_POST['player_submit'])) {
    unset($_POST['player_submit']);

    $player_mod = Player::instance( getValue('id') );

    $player_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $player_mod->check_valid();

    if($tab_error === true) {
      $player_mod->db_save();

      /*echo '<a href="'.Page::get_page_url(PAGE_CODE, false, array('id' => $player_mod->get_id())).'">Lien '.Page::get_page_url(PAGE_CODE, false, array('id' => $player_mod->get_id())).'</a>';
      die();*/
      //page_redirect(PAGE_CODE, array('id' => $player_mod->get_id()));
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM