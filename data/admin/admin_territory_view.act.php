<?php
  $territory = Territory::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_player_history':
        if( $territory->id ) {
          $flag_set_player_history = $territory->set_player_history(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('player_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('datetime')) == ''?null:$value,
            getValue('reason')
          );
          if( ! $flag_set_player_history ) {
            Page::add_message( '$territory->set_player_history : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_player_history':
        if( $territory->id ) {
          $flag_del_player_history = $territory->del_player_history(
            getValue('game_id'),
            getValue('player_id')
          );
        }
        break;
      case 'set_territory_criterion':
        if( $territory->id ) {
          $flag_set_territory_criterion = $territory->set_territory_criterion(
            ($value = getValue('criterion_id')) == ''?null:$value,
            ($value = getValue('percentage')) == ''?null:$value
          );
          if( ! $flag_set_territory_criterion ) {
            Page::add_message( '$territory->set_territory_criterion : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_criterion':
        if( $territory->id ) {
          $flag_del_territory_criterion = $territory->del_territory_criterion(
            ($value = getValue('criterion_id')) == ''?null:$value
          );
        }
        break;
      case 'set_territory_economy_history':
        if( $territory->id ) {
          $flag_set_territory_economy_history = $territory->set_territory_economy_history(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('delta')) == ''?null:$value,
            getValue('reason')
          );
          if( ! $flag_set_territory_economy_history ) {
            Page::add_message( '$territory->set_territory_economy_history : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_economy_history':
        if( $territory->id ) {
          $flag_del_territory_economy_history = $territory->del_territory_economy_history(
            getValue('game_id')
          );
        }
        break;
      case 'set_territory_neighbour':
        if( $territory->id ) {
          $flag_set_territory_neighbour = $territory->set_territory_neighbour(
            ($value = getValue('neighbour_id')) == ''?null:$value,
            getValue('guid1'),
            getValue('guid2')
          );
          if( ! $flag_set_territory_neighbour ) {
            Page::add_message( '$territory->set_territory_neighbour : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_neighbour':
        if( $territory->id ) {
          $flag_del_territory_neighbour = $territory->del_territory_neighbour(
            getValue('neighbour_id')
          );
        }
        break;
      case 'set_territory_player_status':
        if( $territory->id ) {
          $flag_set_territory_player_status = $territory->set_territory_player_status(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('player_id')) == ''?null:$value,
            ($value = getValue('supremacy')) == ''?null:$value
          );
          if( ! $flag_set_territory_player_status ) {
            Page::add_message( '$territory->set_territory_player_status : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_player_status':
        if( $territory->id ) {
          $flag_del_territory_player_status = $territory->del_territory_player_status(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('player_id')) == ''?null:$value
          );
        }
        break;
      case 'set_territory_player_troops_history':
        if( $territory->id ) {
          $flag_set_territory_player_troops_history = $territory->set_territory_player_troops_history(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('player_id')) == ''?null:$value,
            ($value = getValue('delta')) == ''?null:$value,
            getValue('reason'),
            ($value = getValue('reason_player_id')) == ''?null:$value
          );
          if( ! $flag_set_territory_player_troops_history ) {
            Page::add_message( '$territory->set_territory_player_troops_history : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_player_troops_history':
        if( $territory->id ) {
          $flag_del_territory_player_troops_history = $territory->del_territory_player_troops_history(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('player_id')) == ''?null:$value,
            ($value = getValue('reason_player_id')) == ''?null:$value
          );
        }
        break;
      case 'set_territory_status':
        if( $territory->id ) {
          $flag_set_territory_status = $territory->set_territory_status(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('owner_id')) == ''?null:$value,
            ($value = getValue('contested')) == ''?null:$value,
            ($value = getValue('conflict')) == ''?null:$value,
            ($value = getValue('capital')) == ''?null:$value,
            ($value = getValue('revenue_suppression')) == ''?null:$value
          );
          if( ! $flag_set_territory_status ) {
            Page::add_message( '$territory->set_territory_status : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_status':
        if( $territory->id ) {
          $flag_del_territory_status = $territory->del_territory_status(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('owner_id')) == ''?null:$value
          );
        }
        break;
      default:
        break;
    }
  }
  
  // CUSTOM

  //Custom content

  // /CUSTOM
