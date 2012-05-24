<?php

  if(isset($_POST['player_order_submit'])) {
    unset($_POST['player_order_submit']);

    $player_order_mod = Player_Order::instance( getValue('id') );

    $player_order_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $player_order_mod->check_valid();

    if($tab_error === true) {
      $player_order_mod->db_save();

      /*echo '<a href="'.Page::get_page_url(PAGE_CODE, false, array('id' => $player_order_mod->get_id())).'">Lien '.Page::get_page_url(PAGE_CODE, false, array('id' => $player_order_mod->get_id())).'</a>';
      die();*/
      //page_redirect(PAGE_CODE, array('id' => $player_order_mod->get_id()));
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM