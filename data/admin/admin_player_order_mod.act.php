<?php

  if(isset($_POST['player_order_submit'])) {
    unset($_POST['player_order_submit']);

    $player_order_mod = Player_Order::instance( getValue('id') );

    $player_order_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $player_order_mod->check_valid();

    if($tab_error === true) {
      $player_order_mod->save();

      Page::set_message( 'Record successfuly saved' );
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM