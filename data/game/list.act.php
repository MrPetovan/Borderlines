<?php
  $member = Member::get_current_user();
  $current_player = Player::get_current( $member );

  if( $action = getValue('action' ) ) {
    switch( $action ) {
      case 'join' : {
        if( $game_id = getValue('game_id') ) {
          $game_to_join = Game::instance( $game_id );
          if( $game_to_join->id ) {
            if( $game_to_join->add_player( $current_player ) ) {
              Page::add_message( __('You successfully joined the game !') );
            }else {
              Page::add_message( __('You can\'t join this game'), Page::PAGE_MESSAGE_ERROR );
            }
          }else {
            Page::add_message( __('Unknown game'), Page::PAGE_MESSAGE_ERROR );
          }
        }else {
          Page::add_message( __('Game id parameter missing'), Page::PAGE_MESSAGE_ERROR );
        }
        Page::redirect( PAGE_CODE );
        break;
      }
      case 'cancel' : {
        $game_id = getValue('game_id');
        if( $game_id ) {
          /* @var $game_to_delete Game */
          $game_to_delete = Game::instance( $game_id );

          if( $game_to_delete->id ) {

            if( $game_to_delete->created_by == $current_player->id || is_admin() ) {
              if( $game_to_delete->started === null ) {
                $game_player_list = $game_to_delete->get_game_player_list();

                if( $game_to_delete->db_delete() ) {
                  foreach( $game_player_list as $game_player ) {
                    if( $current_player->id != $game_player['player_id'] ) {
                      $player = Player::instance( $game_player['player_id'] );
                      $member = Member::instance( $player->member_id );
                      if( php_mail($member->email, SITE_NAME." | Game canceled", $player->get_email_game_cancel( $game_to_delete ), true) ) {
                        Page::add_message( __("Message sent to %s", $player->name) );
                      }else {
                        Page::add_message( __("Message failed to %s", $player->name) , Page::PAGE_MESSAGE_WARNING);
                        Page::add_message(var_export( error_get_last(), 1 ), Page::PAGE_MESSAGE_WARNING);
                      }
                    }
                  }

                  Page::add_message( __('Game successfuly deleted') );
                }else {
                  Page::add_message( __('There was a problem during deletion, please contact an admin'), Page::PAGE_MESSAGE_ERROR );
                }
              }else {
                Page::add_message( __('You can\'t delete a game that has already started'), Page::PAGE_MESSAGE_ERROR );
              }
            }else {
              Page::add_message( __('You can\'t delete a game you\'re not the creator'), Page::PAGE_MESSAGE_ERROR );
            }
          }else {
            Page::add_message( __('Unknown game'), Page::PAGE_MESSAGE_ERROR );
          }
        }else {
          Page::add_message( __('Game id parameter missing'), Page::PAGE_MESSAGE_ERROR );
        }
        break;
      }
      case 'start' : {
        $game_id = getValue('game_id');
        if( $game_id ) {
          /* @var $game_to_start Game */
          $game_to_start = Game::instance( $game_id );

          if( $game_to_start->id ) {

            if( $game_to_start->created_by == $current_player->id || is_admin() ) {
              if( $game_to_start->started === null ) {
                $game_player_list = $game_to_start->get_game_player_list();

                if( count( $game_player_list ) >= 2 ) {
                  $game_to_start->start();
                  Page::add_message( __('Game successfuly started') );
                }else {
                  Page::add_message( __('You can\'t start a game with fewer than 2 players'), Page::PAGE_MESSAGE_ERROR );
                }
              }else {
                Page::add_message( __('You can\'t delete a game that has already started'), Page::PAGE_MESSAGE_ERROR );
              }
            }else {
              Page::add_message( __('You can\'t delete a game you\'re not the creator'), Page::PAGE_MESSAGE_ERROR );
            }
          }else {
            Page::add_message( __('Unknown game'), Page::PAGE_MESSAGE_ERROR );
          }
        }else {
          Page::add_message( __('Game id parameter missing'), Page::PAGE_MESSAGE_ERROR );
        }
        break;
      }
    }
  }

  $game_list = Game::db_get_all();
?>