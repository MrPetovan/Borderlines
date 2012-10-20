<?php

  if( isset($_GET['w']) && isset($_GET['x']) && isset($_GET['y']) && isset($_GET['z']) ) {

    $dir = 'cache/world/';

    $world = World::instance($_GET['w']);

    $ratio = pow( 2, $_GET['z']) / $world->size_x * 256;

    $options = array(
        'offset_x' => $_GET['x'] * 256 / $ratio,
        'offset_y' => $world->size_y - ($_GET['y'] + 1) * 256 / $ratio,
        'size_x' => ($_GET['x'] + 1) * 256 / $ratio,
        'size_y' => $world->size_y -  ($_GET['y']) * 256 / $ratio,
        'ratio' => $ratio
    );

    //var_debug($options);

    if($world) {
      $filename = 'tile_'.$world->id.'_'.$_GET['x'].'_'.$_GET['y'].'_'.$_GET['z'].'.png';
      if( !is_file( DIR_ROOT . $dir . $filename ) || getValue('force') ) {
        $image = $world->draw( $options );
        imagepng($image, DIR_ROOT . $dir . $filename );
        $last_modified = time();
      }else {
        $last_modified = filemtime(DIR_ROOT . $dir . $filename);
      }
      header('Pragma: cache');
      header('Cache-Control: public');
      header('Last-Modified: '. gmdate('D, d M Y H:i:s', $last_modified) .' GMT');
      //header('Expires: '. gmdate('D, d M Y H:i:s', time() + 60*60*24*30) .' GMT');
      header('Content-type: image/png');
      readfile( DIR_ROOT . $dir . $filename );
      //echo '<img src="'.URL_ROOT . $dir . $filename.'"/>';
    }
    exit;
  }
?>