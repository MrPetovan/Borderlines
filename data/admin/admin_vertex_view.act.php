<?php

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_territory_vertex':
        $vertex = Vertex::instance( getValue('vertex_id') );
        if( $vertex->id ) {
          $flag_set_territory_vertex = $vertex->set_territory_vertex(
            getValue('territory_id')
          );
        }
        break;
      case 'del_territory_vertex':
        $vertex = Vertex::instance( getValue('vertex_id') );
        if( $vertex->id ) {
          $flag_del_territory_vertex = $matchup->del_territory_vertex(
            getValue('territory_id')
          );
        }
        break;
      default:
        break;
    }
  }
