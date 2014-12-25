<?php
  if( $allowed ) {
    if( $success ) {
      if( $content ) {
        $content = json_encode($content);
        if( $callback = getValue('callback') ) {
          echo $callback.'('.$content.');';
        }else {
          echo $content;
        }
         ;
      }else {
        echo 1;
      }
    }else {
      header('HTTP/1.1 500 Internal Server Error');
      if( $content ) {
        echo json_encode($content);
      }else {
        echo 0;
      }
    }
  }else {
    header('HTTP/1.1 403 Forbidden');
  }
?>