<?php
  $vertex = Vertex::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_territory_vertex':
        if( $vertex->id ) {
          $flag_set_territory_vertex = $vertex->set_territory_vertex(
            ($value = getValue('territory_id')) == ''?null:$value
          );
          if( ! $flag_set_territory_vertex ) {
            Page::add_message( '$vertex->set_territory_vertex : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_vertex':
        if( $vertex->id ) {
          $flag_del_territory_vertex = $vertex->del_territory_vertex(
            ($value = getValue('territory_id')) == ''?null:$value
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
