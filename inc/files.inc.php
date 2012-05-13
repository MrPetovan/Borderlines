<?php

  /**
   * mkdir récursif
   *
   * @param string $path Le chemin à créer
   * @return true/false
   */
  function _mkdir($path) {
    if(!file_exists($path)) {
      if(is_dir(dirname($path))) {
        return mkdir($path);
      }else {
        return _mkdir(dirname($path));
      }
    }else {
      return true;
    }
  }
?>