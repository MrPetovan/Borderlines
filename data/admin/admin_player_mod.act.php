<?php

  if(isset($_POST['player_submit'])) {
    unset($_POST['player_submit']);

    $player_mod = Player::instance( getValue('id') );

    $player_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $player_mod->check_valid();

    if($tab_error === true) {
      $player_mod->save();

      Page::set_message( 'Record successfuly saved' );
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM