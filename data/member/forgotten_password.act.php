<?php
  $error_msg = null;

  if(isset($_POST['password_forgotten']) || isset($_POST['password_forgotten_x'])) {
    if(isset($_POST['email']) && $_POST['email'] != '') {
      $member = Member::db_get_membre_by_email($_POST['email']);
      if( $member ) {
        if( $member->get_niveau() < 2 ) {
          $error_msg = "";
        }else {
          $error_msg = "Ce compte est un compte partagé, il n'est pas possible de redéfinir le mot de passe, veuillez vous adresser au support pour obtenir le mot de passe.";
        }
      }else {
        $error_msg = "Cet email n'est associé à aucun compte.";
      }
    }else {
      $error_msg = "Champ email obligatoire.";
    }
  }

  if(!is_null($error_msg) && $error_msg === "") {
    $new_password = str_rand();
    $member->set_password($new_password, false);
    $tab_error = $member->db_save();

    if($tab_error === true) {
      if(php_mail(
        array($member->get_email(), $member->get_pseudo()),
        "Geo : Oubli de mot de passe.",
        $member->get_email_forgotten_password($new_password), true
      )) {
        $succes_msg = true;
      }else{
        $error_msg = 'L\'email n\'a pas pu être envoyé, cependant vous pouvez contacter directement le service client via la rubrique <a href="'.get_page_url('contact').'">Contact</a>';
      }
    }else {
      $error_msg = 'L\'email n\'a pas pu être envoyé, cependant vous pouvez contacter directement le service client via la rubrique <a href="'.get_page_url('contact').'">Contact</a>';
    }
  }
?>
