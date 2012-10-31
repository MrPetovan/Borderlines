<?php
  $api_token = Api_Token::instance( getValue('id') );

  if(!is_null(getValue('action'))) {
    switch( getValue('action') ) {
       case 'set_api_log':
        if( $api_token->id ) {
          $flag_set_api_log = $api_token->set_api_log(
            getValue('method'),
            ($value = getValue('params')) == ''?null:$value,
            ($value = getValue('allowed')) == ''?null:$value,
            ($value = getValue('success')) == ''?null:$value,
            ($value = getValue('created')) == ''?null:$value
          );
          if( ! $flag_set_api_log ) {
            Page::add_message( '$api_token->set_api_log : ' . mysql_error(), Page::PAGE_MESSAGE_ERROR );
          }
        }
        break;
      case 'del_api_log':
        if( $api_token->id ) {
          $flag_del_api_log = $api_token->del_api_log(
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
