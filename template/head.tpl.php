    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta name="robots" content="index,follow" />
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico" />
    <link rel="icon" type="image/x-icon" href="img/favicon.ico" />

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="<?php echo URL_ROOT ?>style/commun.css" media="all" />
    <link rel="stylesheet" type="text/css" href="<?php echo URL_ROOT ?>style/jquery.cluetip.css" media="all" />
    <link rel="stylesheet" type="text/css" href="<?php echo URL_ROOT ?>style/jquery.tooltip.css" media="all" />

    <!--[if IE 7]>
    <link rel="stylesheet" type="text/css" href="style/ie7.css" media="all" />
    <![endif]-->
    <!--[if IE 6]>
    <link rel="stylesheet" type="text/css" href="style/ie6.css" media="all" />
    <![endif]-->
<?php
  $dir_js_name = 'js';
  $dir_js_path = DIR_ROOT . $dir_js_name . '/';
  $js_array = array();
  if (is_dir($dir_js_path)) {
    if ($dir_js = opendir($dir_js_path)) {
      while (($file_js = readdir($dir_js)) !== false) {
        if($file_js != '.' && $file_js != '..') {
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
