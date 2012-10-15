<?php

  include_once('data/static/html_functions.php');

  if(!isset($member_mod)) {
    if(isset($_GET['id'])) {
      $id = $_GET['id'];
      $member_mod = new Member($id);
    }else {
      $id = null;
      $member_mod = new Member();
    }
  }

  if(is_null($member_mod->get_id())) {
    $form_url = get_page_url(PAGE_CODE);
    $PAGE_TITRE = 'Utilisateur : Ajouter';
  }else {
    $form_url = get_page_url(PAGE_CODE).'&id='.$id;
    $PAGE_TITRE = 'Utilisateur : Mettre à jour les informations pour "'.$member_mod->get_prenom().' '.$member_mod->get_nom().'"';
  }

  $html_msg = '';

  if(isset($tab_error)) {
    if(Member::manage_errors($tab_error, $html_msg) === true) {
      $html_msg = '<p class="msg">Les informations de l\'utilisateur ont été correctement enregistrées.</p>';
    }
  }

  echo '
  <div class="texte_contenu">
    <div class="texte_texte">
      <h3>'.$PAGE_TITRE.'</h3>
      '.$html_msg.'
      <form class="formulaire" action="'.get_page_url(PAGE_CODE, true, array('id' => $member_mod->get_id())).'" method="post">
        '.$member_mod->html_get_form().'
        <p>'.HTMLHelper::submit('member_submit', 'Sauvegarder les changements').'</p>
      </form>
    </div>
  </div>';
?>