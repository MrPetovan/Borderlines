<?php
  $world = World::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       default:
        break;
    }
  }
  
  // CUSTOM

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
      case 'generate': {
        $world->initialize_territories();
        Page::redirect(PAGE_CODE, array('id' => $world->id));
        break;
      }
       default:
        break;
    }
  }

  // /CUSTOM
