<?php
  $criterion = Criterion::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_territory_criterion':
        if( $criterion->id ) {
          $flag_set_territory_criterion = $criterion->set_territory_criterion(
            getValue('territory_id'),
            getValue('percentage')
          );
        }
        break;
      case 'del_territory_criterion':
        if( $criterion->id ) {
          $flag_del_territory_criterion = $criterion->del_territory_criterion(
            getValue('territory_id')
          );
        }
        break;
      default:
        break;
    }
  }
