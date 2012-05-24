<?php
  include_once('data/static/html_functions.php');

  if(!isset($player_order_mod)) {
    $player_order_mod = Player_Order::instance( getValue('id') );
  }

  if(is_null($player_order_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = 'Player Order : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$player_order_mod->get_id();
    $PAGE_TITRE = 'Player Order : Mettre à jour les informations pour "'.$player_order_mod->get_id().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(Player_Order::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'objet Player Order ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">';
    admin_menu(PAGE_CODE);

    echo '<div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.$form_url.'" method="post">
        '.$player_order_mod->html_get_form(MEMBER_FORM_ADMIN);

  // CUSTOM

  //Custom content

  // /CUSTOM

        echo '
        <p>'.HTMLHelper::submit('player_order_submit', 'Sauvegarder les changements').'</p>
      </form>';

      if( $player_order_mod->id ) {
        echo '
      <p><a href="'.get_page_url('admin_player_order_view', true, array('id' => $player_order_mod->get_id())).'">Revenir à la page de l\'objet "'.$player_order_mod->get_id().'"</a></p>';
      }
      echo '
      <p><a href="'.get_page_url('admin_player_order').'">Revenir à la liste des objets Player Order</a></p>
    </div>
  </div>';