<?php

  if(isset($_POST['game_submit'])) {
    unset($_POST['game_submit']);

    $game_mod = Game::instance( getValue('id') );

    $game_mod->load_from_html_form($_POST, $_FILES);
    $tab_error = $game_mod->check_valid();

    if($tab_error === true) {
      $game_mod->save();

      Page::set_message( 'Record successfuly saved' );
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM