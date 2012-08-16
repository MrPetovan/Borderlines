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
      case 'set_territory_neighbour':
        if( $territory->id ) {
          $flag_set_territory_neighbour = $territory->set_territory_neighbour(
            ($value = getValue('neighbour_id')) == ''?null:$value
          );
          if( ! $flag_set_territory_neighbour ) {
            Page::add_message( '$territory->set_territory_neighbour : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_neighbour':
        if( $territory->id ) {
          $flag_del_territory_neighbour = $territory->del_territory_neighbour(
            ($value = getValue('neighbour_id')) == ''?null:$value
          );
        }
        break;
      case 'set_territory_owner':
        if( $territory->id ) {
          $flag_set_territory_owner = $territory->set_territory_owner(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('owner_id')) == ''?null:$value
          );
          if( ! $flag_set_territory_owner ) {
            Page::add_message( '$territory->set_territory_owner : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_owner':
        if( $territory->id ) {
          $flag_del_territory_owner = $territory->del_territory_owner(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('owner_id')) == ''?null:$value
          );
        }
        break;
      case 'set_territory_player_troops':
        if( $territory->id ) {
          $flag_set_territory_player_troops = $territory->set_territory_player_troops(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('player_id')) == ''?null:$value,
            ($value = getValue('quantity')) == ''?null:$value
          );
          if( ! $flag_set_territory_player_troops ) {
            Page::add_message( '$territory->set_territory_player_troops : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_player_troops':
        if( $territory->id ) {
          $flag_del_territory_player_troops = $territory->del_territory_player_troops(
            ($value = getValue('game_id')) == ''?null:$value,
            ($value = getValue('turn')) == ''?null:$value,
            ($value = getValue('player_id')) == ''?null:$value
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
