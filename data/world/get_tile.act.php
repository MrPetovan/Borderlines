<?php

  if( isset($_GET['w']) && isset($_GET['x']) && isset($_GET['y']) && isset($_GET['z']) ) {

    $world = World::instance($_GET['w']);

    if($world->id) {
      $dir = 'cache/world/'.$world->id.'/tile/';
      $filename = 'tile_'.$_GET['x'].'_'.$_GET['y'].'_'.$_GET['z'].'.png';
      $ratio = pow( 2, $_GET['z']) / $world->size_x * 256;
      $options = array(
          'offset_x' => $_GET['x'] * 256 / $ratio,
          'offset_y' => $world->size_y - ($_GET['y'] + 1) * 256 / $ratio,
          'size_x' => ($_GET['x'] + 1) * 256 / $ratio,
          'size_y' => $world->size_y -  ($_GET['y']) * 256 / $ratio,
          'ratio' => $ratio
      );
      $world->cache_draw($dir . $filename, $options, true, getValue('force'));
    }
    exit;
  }
?>