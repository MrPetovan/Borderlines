<?php

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_territory_criterion':
        $criterion = Criterion::instance( getValue('criterion_id') );
        if( $criterion->id ) {
          $flag_set_territory_criterion = $criterion->set_territory_criterion(
            getValue('territory_id'),
            getValue('percentage')
          );
        }
        break;
      case 'del_territory_criterion':
        $criterion = Criterion::instance( getValue('criterion_id') );
        if( $criterion->id ) {
          $flag_del_territory_criterion = $matchup->del_territory_criterion(
            getValue('territory_id')
          );
        }
        break;
      default:
        break;
    }
  }
