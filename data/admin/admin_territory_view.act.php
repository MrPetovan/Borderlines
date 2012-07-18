<?php
  $territory = Territory::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
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
      case 'set_territory_vertex':
        if( $territory->id ) {
          $flag_set_territory_vertex = $territory->set_territory_vertex(
            ($value = getValue('vertex_id')) == ''?null:$value
          );
          if( ! $flag_set_territory_vertex ) {
            Page::add_message( '$territory->set_territory_vertex : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_vertex':
        if( $territory->id ) {
          $flag_del_territory_vertex = $territory->del_territory_vertex(
            ($value = getValue('vertex_id')) == ''?null:$value
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
