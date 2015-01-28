<?php
  $dir_js_name = 'js';
  $dir_js_path = DIR_ROOT . $dir_js_name . '/';
  $js_array = array();
  if (is_dir($dir_js_path)) {
    if ($dir_js = opendir($dir_js_path)) {
      while (($file_js = readdir($dir_js)) !== false) {
        if($file_js != '.' && $file_js != '..' && !is_dir($dir_js_path.$file_js)) {
          $js_array[] = URL_ROOT.$dir_js_name.'/'.$file_js;
        }
      }
      closedir($dir_js);
    }
  }
  sort($js_array);
  foreach ($js_array as $js_file) {
    echo '
  <script type="text/javascript" src="'.$js_file.'"></script>';
  }
?>
  <script type="text/javascript">
    jQuery(function(){
      while (domReadyQueue.length) {
        domReadyQueue.shift()(jQuery);
      }
    });
  </script>