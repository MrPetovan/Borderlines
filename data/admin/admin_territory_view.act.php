<?php
  $territory = Territory::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_territory_criterion':
        if( $territory->id ) {
          $flag_set_territory_criterion = $territory->set_territory_criterion(
            getValue('criterion_id'),
            getValue('percentage')
          );
        }
        break;
      case 'del_territory_criterion':
        if( $territory->id ) {
          $flag_del_territory_criterion = $territory->del_territory_criterion(
            getValue('criterion_id')
          );
        }
        break;
      case 'set_territory_neighbour':
        if( $territory->id ) {
          $flag_set_territory_neighbour = $territory->set_territory_neighbour(
            getValue('neighbour_id')
          );
        }
        break;
      case 'del_territory_neighbour':
        if( $territory->id ) {
          $flag_del_territory_neighbour = $territory->del_territory_neighbour(
            getValue('neighbour_id')
          );
        }
        break;
      case 'set_territory_vertex':
        if( $territory->id ) {
          $flag_set_territory_vertex = $territory->set_territory_vertex(
            getValue('vertex_id')
          );
        }
        break;
      case 'del_territory_vertex':
        if( $territory->id ) {
          $flag_del_territory_vertex = $territory->del_territory_vertex(
            getValue('vertex_id')
          );
        }
        break;
      default:
        break;
    }
  }
