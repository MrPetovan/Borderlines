<?php
  $criterion = Criterion::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_territory_criterion':
        if( $criterion->id ) {
          $flag_set_territory_criterion = $criterion->set_territory_criterion(
            ($value = getValue('territory_id')) == ''?null:$value,
            ($value = getValue('percentage')) == ''?null:$value
          );
          if( ! $flag_set_territory_criterion ) {
            Page::add_message( '$criterion->set_territory_criterion : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_territory_criterion':
        if( $criterion->id ) {
          $flag_del_territory_criterion = $criterion->del_territory_criterion(
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
