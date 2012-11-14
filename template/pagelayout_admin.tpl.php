<!DOCTYPE html>
<html>
<head>
    <title><?php echo SITE_NAME ?> - <?php echo wash_utf8($PAGE_TITRE); ?></title>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Script-Type" content="text/javascript" />
    <meta http-equiv="Content-Style-Type" content="text/css" />
    <meta name="robots" content="noindex,nofollow" />
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico" />
    <link rel="icon" type="image/x-icon" href="img/favicon.ico" />

    <link rel="stylesheet" type="text/css" href="<?php echo URL_ROOT ?>style/bootstrap.min.css" media="all" />
    <link rel="stylesheet" type="text/css" href="<?php echo URL_ROOT ?>style/admin.css" media="all" />
</head>
<body>
  <div class="navbar navbar-inverse">
    <div class="navbar-inner">
      <div class="container">
        <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </a>
        <a class="brand" href="<?php echo URL_ROOT?>"><?php echo SITE_NAME ?></a>
        <div class="nav-collapse collapse">
          <ul class="nav">
<?php
    $data_dir = opendir(DIR_ROOT.'data');

    $exclude = array('.', '..', 'admin', 'static', 'model');
    $menu_array = array();

    while($dir = readdir( $data_dir )) {
      if( ! in_array( $dir, $exclude ) && is_dir(DIR_ROOT.'data/'.$dir )  ) {
        $menu_array['admin_'.$dir] = to_readable($dir);
      }
    }
    foreach ($menu_array as $code_page => $libelle) {
      if( Page::get_url($code_page, array(), false) != '' ) {
        echo '
            <li'.($code_page == PAGE_CODE?' class="active"':'').'><a href="'.Page::get_url($code_page).'">'.wash_utf8($libelle).'</a></li>';
      }
    }
?>
            <li><a href="<?php Page::get_url('logout')?>">Logout</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </div>
  </div>

  <div class="container">
    <?php Page::display_messages();?>
    <?php echo $PAGE_CONTENU; ?>
  </div>

  <script type="text/javascript" src="<?php echo URL_ROOT.'js/bootstrap.min.js'?>"></script>
</body>
</html>
