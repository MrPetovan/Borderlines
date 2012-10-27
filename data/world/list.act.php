<?php
  $member = Member::get_current_user();
  $current_player = Player::get_current( $member );

  $world_mod = World::instance();

  if(isset($_POST['world_submit']) ) {
    if( $current_player->can_create_world() ) {
      unset($_POST['world_submit']);

      $world_mod->load_from_html_form($_POST, $_FILES);
      $world_mod->created = time();
      $world_mod->created_by = $current_player->id;
      $tab_error = $world_mod->check_valid();

      if($tab_error === true) {
        function fatalErrorHandler()
        {
          # Getting last error
          $error = error_get_last();

          # Checking if last error is a fatal error
          if(($error['type'] === E_ERROR) || ($error['type'] === E_USER_ERROR))
          {
            # Here we handle the error, displaying HTML, logging, ...
            echo 'Sorry, a serious error has occured in ' . $error['file'];
            Page::add_message( $error['message'], Page::PAGE_MESSAGE_ERROR );
            Page::redirect('world_list');
          }
        }

        # Registering shutdown function
        register_shutdown_function('fatalErrorHandler');
        set_time_limit(60);
        $world_mod->initialize_territories();
        Page::add_message( __('World successfuly created') );
      }else {
        $html_msg = '';
        World::manage_errors( $tab_error, $html_msg );
        Page::add_message( $html_msg, Page::PAGE_MESSAGE_ERROR );
      }
    }else {
      Page::add_message( __('You created a world less than a hour ago, wait a bit before playing God again !'), Page::PAGE_MESSAGE_WARNING );
    }
  }