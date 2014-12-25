<?php

  if(!isset($order_type_mod)) {
    $order_type_mod = Order_Type::instance( getValue('id') );
  }

  if(is_null($order_type_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = 'Order Type : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$order_type_mod->get_id();
    $PAGE_TITRE = 'Order Type : Mettre à jour les informations pour "'.$order_type_mod->get_name().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(Order_Type::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'objet Order Type ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">
    <div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.$form_url.'" method="post">
        '.$order_type_mod->html_get_form();

  // CUSTOM

  //Custom content

  // /CUSTOM

        echo '
        <p>'.HTMLHelper::submit('order_type_submit', 'Sauvegarder les changements').'</p>
      </form>';

      if( $order_type_mod->id ) {
        echo '
      <p><a href="'.get_page_url('admin_order_type_view', true, array('id' => $order_type_mod->get_id())).'">Revenir à la page de l\'objet "'.$order_type_mod->get_name().'"</a></p>';
      }
      echo '
      <p><a href="'.get_page_url('admin_order_type').'">Revenir à la liste des objets Order Type</a></p>
    </div>
  </div>';