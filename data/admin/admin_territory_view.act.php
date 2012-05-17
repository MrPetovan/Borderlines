<?php

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_territory_criterion':
        $territory = Territory::instance( getValue('territory_id') );
        if( $territory->id ) {
          $flag_set_territory_criterion = $territory->set_territory_criterion(
            getValue('criterion_id'),
            getValue('percentage')
          );
        }
        break;
      case 'del_territory_criterion':
        $territory = Territory::instance( getValue('territory_id') );
        if( $territory->id ) {
          $flag_del_territory_criterion = $matchup->del_territory_criterion(
            getValue('criterion_id')
          );
        }
        break;
      case 'set_territory_neighbour':
        $territory = Territory::instance( getValue('territory_id') );
        if( $territory->id ) {
          $flag_set_territory_neighbour = $territory->set_territory_neighbour(
            getValue('territory_id')
          );
        }
        break;
      case 'del_territory_neighbour':
        $territory = Territory::instance( getValue('territory_id') );
        if( $territory->id ) {
          $flag_del_territory_neighbour = $matchup->del_territory_neighbour(
            getValue('territory_id')
          );
        }
        break;
      case 'set_territory_vertex':
        $territory = Territory::instance( getValue('territory_id') );
        if( $territory->id ) {
          $flag_set_territory_vertex = $territory->set_territory_vertex(
            getValue('vertex_id')
          );
        }
        break;
      case 'del_territory_vertex':
        $territory = Territory::instance( getValue('territory_id') );
        if( $territory->id ) {
          $flag_del_territory_vertex = $matchup->del_territory_vertex(
            getValue('vertex_id')
          );
        }
        break;
      default:
        break;
    }
  }
