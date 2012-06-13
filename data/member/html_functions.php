<?php
  function mon_compte_menu($elt_actif = 'mon-compte', $logged_user = true, $corporate_user = false) {
    $return = '';
    $current_user = Member::get_current_user();
    if($current_user) {
      switch ($current_user->get_niveau()) {
        // Membre particulier => menu complet
        case 0 :
          $menu_array = array(
            get_page_url('mon-compte') => 'Mon compte',
      	    get_page_url('mon-compte-identifiants') => 'Modifier mes identifiants',
            get_page_url('mon-compte-infos') => 'Modifier mes informations personnelles',
        		get_page_url('logout') => 'Se déconnecter'
          );
          break;
        // Membre admin => menu admin
        case 1 :
          $menu_array = array(
            get_page_url('admin_member') => 'Admin Membres',
        		get_page_url('logout') => 'Se déconnecter'
          );
          break;
      }
      $return .= '
    <ul class="menu member">';
      foreach ($menu_array as $url_page => $libelle) {
        $return .= '
      <li'.($url_page == get_page_url($elt_actif)?' class="actif"':'').'><a href="'.$url_page.'">'.wash_utf8($libelle).'</a></li>';
      }

      $return .= '
	  </ul>';
    }
    return $return;
  }
?>