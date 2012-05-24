<?php
  function admin_menu($elt_actif = 'admin_member') {
    $data_dir = opendir(DIR_ROOT.'data');

    $exclude = array('.', '..', 'admin', 'static', 'model');
    $menu_array = array();

    while($dir = readdir( $data_dir )) {
      if( ! in_array( $dir, $exclude ) && is_dir(DIR_ROOT.'data/'.$dir )  ) {
        $menu_array['admin_'.$dir] = to_readable($dir);
      }
    }

    echo '
    <ul class="menu">';
    foreach ($menu_array as $code_page => $libelle) {
      echo '
      <li'.($code_page == $elt_actif?' class="actif"':'').'><a href="'.get_page_url($code_page).'">'.wash_utf8($libelle).'</a></li>';
    }
    echo '
        <li><a href="'.get_page_url('logout').'">'.wash_utf8('Se déconnecter').'</a></li>
	  </ul>';
  }


?>
