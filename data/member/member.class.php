<?php
/**
 * Classe décrivant un compte utilisateur, qu'il soit prospect ou enregistré
 *
 */

// Flags binaires de séparation des tests de validité d'un compte
// Vérification de l'existence de l'adresse email
define('MEMBER_NEW_USER_CHECK', 1);
// Vérification des infos personnelles (Prénom, Nom, Code Postal, Pays)
// Vérification du nom seul
define('MEMBER_NAME_CHECK', 2);
// Vérification complète
define('MEMBER_PERSONAL_INFO_CHECK', 4);
define('MEMBER_COMPLETE_INFO_CHECK', MEMBER_PERSONAL_INFO_CHECK | MEMBER_NAME_CHECK);


// Vérification de la date de naissance
// Vérification de l'existence de la date
define('MEMBER_BIRTHDAY_CHECK', 8);
// Vérification complète (existence + majeur)
define('MEMBER_GROWNUP_CHECK', 16 | MEMBER_BIRTHDAY_CHECK);

// Sélectionneur de type de formulaire de création
define('MEMBER_FORM_ABONNEMENT', 1);
define('MEMBER_FORM_NEWSLETTER', 2);
define('MEMBER_FORM_TEST', 3);
define('MEMBER_FORM_ADMIN', 4);

class Member extends DBObject {
  // Champs BD
  protected $_prenom = '';
  protected $_nom = '';
  protected $_pays = '';
  protected $_genre = 'F';
  protected $_date_naissance = '';
  protected $_email = '';
  protected $_password = '';
  protected $_niveau = 0;
  protected $_visible = false;
  protected $_code_validation = '';
  protected $_date_inscription = '';
  protected $_remember_token = '';
  protected $_date_connexion = '';
  protected $_origin = '';

  // Champs locaux
  /**
   * Stocke l'e-mail de confirmation dans les formulaires d'enregistrement
   *
   * @var string
   */
  public $email2 = null;
  /**
   * Stocke le mot de passe en clair dans les formulaires d'enregistrement
   *
   * @var string
   */
  public $password2 = null;
  /**
   * Stocke la valeur de la checkbox CGV dans le formulaire d'enregistrement
   *
   * @var int ( 0 | 1 )
   */
  public $cgv_accepte = null;

  public function __construct($id = null) {
    parent::__construct($id);
    if(is_null($id))
      $this->set_date_inscription(time());
  }

	/* ACCESSEURS */
  public static function get_table_name() { return "member";}

  public function get_name()    { return $this->_prenom.' '.$this->nom;}

  public function get_date_naissance()    { return guess_time($this->_date_naissance);}
  public function get_date_inscription()  { return guess_time($this->_date_inscription);}
  public function get_date_connexion()    { return guess_time($this->_date_connexion);}

  public function is_visible() { return ($this->visible == 1); }

  /* MUTATEURS */
  public function set_date_naissance($date) { $this->_date_naissance = guess_time($date, GUESS_DATE_MYSQL);}
  public function set_date_inscription($date) { $this->_date_inscription = guess_time($date, GUESS_DATE_MYSQL);}
  public function set_date_connexion($date) { $this->_date_connexion = guess_time($date, GUESS_DATE_MYSQL);}

  public function set_password($password, $is_crypted = true) {
    if($is_crypted) {
      $this->_password = $password;
    } else {
      $this->password2 = null;
      $this->_password = Member::password_crypt($password);
    }
  }

  public function set_visible($visible) {
    if($visible) $data = 1; else $data = 0; $this->_visible = $data;
  }

  /* FONCTIONS STATIQUES */

  /**
   * Fonction unique de cryptage du mot de passe, utilisée lors de
   * l'enregistrement et lors du login (pour la comparaison)
   *
   * @param string $clear_password Le mot de passe en clair
   * @return string
   */
  public static function password_crypt($clear_password) {
    return sha1($clear_password);
  }

  /**
   * Retourne l'ID de l'utilisateur courant, il peut s'agir d'un membre
   * enregistré ou d'un prospect en session.
   *
   * A ne pas utiliser pour donner l'accès aux pages privées
   *
   * @return int
   */
  public static function get_current_user_id() {
    if(isset($_SESSION['sess']['logged_in'])) {
      return $_SESSION['sess']['logged_in'];
    }else {
      return false;
    }
  }

  /**
   * Définit l'ID de l'utilisateur courant
   *
   * @param int $id
   * @return bool
   */
  public static function set_current_user_id($id) {
    $_SESSION['sess']['logged_in'] = $id;
    return true;
  }

  /**
   * Supprime l'ID de l'utilisateur de la session
   *
   * @return bool
   */
  public static function del_current_user_id() {
    unset($_SESSION['sess']['logged_in']);
    return true;
  }

  /**
   * Retourne l'objet Member de l'utilisateur courant.
   *
   * Note : à ne pas utiliser pour vérifier l'accès aux pages privées
   *
   * @see get_current_user_id
   * @return Member
   */
  public static function get_current_user() {
    if(Member::get_current_user_id()) {
      return new Member(Member::get_current_user_id());
    }else {
      return false;
    }
  }

  /**
   * Retourne l'objet Member de l'utilisateur courant il est enregistré, false
   * si prospect ou pas d'utilisateur courant.
   *
   * A utiliser pour vérifier l'accès aux pages privées.
   *
   * @return Member
   */
  public static function get_logged_user() {
    $member = Member::get_current_user();
    if(!$member || $member->niveau == 0) {
      $member = false;
    }
    return $member;
  }

  /**
   * Retourne le tableau des niveaux d'utilisateur et de leur correspondance
   * texte
   *
   * @return array
   */
  public static function get_tab_level() {
    return array(
      0 => "Utilisateur",
      1 => "Administrateur"
    );
  }

  /* FONCTIONS SQL */
  public static function db_get_membre_by_email($email) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `email` LIKE ".mysql_ureal_escape_string($email)."
LIMIT 0,1";

    return self::sql_to_object($sql);
  }

  public static function db_get_membre_by_code($code) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `code_validation` LIKE ".mysql_ureal_escape_string($code)."
LIMIT 0,1";

    return self::sql_to_object($sql);
  }

  public static function db_get_by_remember_token($token) {
    $sql = "
SELECT `id` FROM `".self::get_table_name()."`
WHERE `remember_token` LIKE ".mysql_ureal_escape_string($token)."
LIMIT 0,1";

    return self::sql_to_object($sql);
  }

  private static function sql_between_date($attribute, $jour_deb = null, $jour_fin = null) {
    $sql_date = '1';
    if(!is_null($jour_deb) || !is_null($jour_fin)) {
      if(is_null($jour_deb)) {
        $str_deb = "'0000-00-00 00:00:00'";
      }else {
        $str_deb = "DATE_SUB(NOW(), INTERVAL $jour_deb DAY)";
      }
      if(is_null($jour_fin)) {
        $str_fin = "NOW()";
      }else {
        $str_fin = "DATE_SUB(NOW(), INTERVAL $jour_fin DAY)";
      }
      $sql_date = "
".$attribute." > $str_deb
AND ".$attribute." <= $str_fin";
    }
    return $sql_date;
  }

  protected static function export_csv($sql) {
    $res = mysql_uquery($sql);

    ob_start();
    if (mysql_num_rows($res) != 0) {
      // titre des colonnes
      $fields = mysql_num_fields($res);
      for($i = 0; $i < $fields; $i++) {
        echo mysql_field_name($res, $i).";";
      }
      echo "\r\n";

      // données de la table
      while ($arrSelect = mysql_fetch_assoc($res)) {
       foreach($arrSelect as $elem) {
        echo "$elem;";
       }
       echo "\r\n";
      }
    }
    return ob_get_clean();
  }


  /* FONCTIONS HTML */

  /**
   * Formulaire d'édition partie Administration
   *
   * @param string $form_url URL de la page action
   * @return string
   */
  public function html_get_form($form_url) {
    $return = '
    <fieldset>
      <legend>Text fields</legend>
      <div class="field form-group">'.HTMLHelper::genererInputText('prenom', $this->prenom, array(), "Prénom *").'</div>
      <div class="field form-group">'.HTMLHelper::genererInputText('nom', $this->nom, array(), "Nom *").'</div>
      <div class="field form-group">'.HTMLHelper::genererInputText('pays', $this->pays, array(), "Pays *").'</div>
      <div class="field form-group">
        <label>Vous êtes un(e)*</label>'.
          HTMLHelper::genererInputRadio('genre', 'F', $this->genre, array('id' => 'radio_genre_mme', 'checked' => 'checked', 'label_position' => 'right'), "Mme" ).
          HTMLHelper::genererInputRadio('genre', 'M', $this->genre, array('id' => 'radio_genre_mlle', 'label_position' => 'right'), "Mlle" ).
          HTMLHelper::genererInputRadio('genre', 'H', $this->genre, array('id' => 'radio_genre_m', 'label_position' => 'right'), "M" ).'
      </div>
      <div class="field form-group">'.HTMLHelper::genererInputText('date_naissance', $this->date_naissance, array(), "Date de naissance *").'</div>

      <div class="field form-group">'.HTMLHelper::genererInputText('email', $this->email, array(), "Email").'</div>
      <div class="field form-group"><label for="select_level">Level</label>
      <select id="select_level" name="level">';
    foreach(Member::get_tab_level() as $key_level => $name_level) {
      $return .= '
        <option value="'.$key_level.'"'.(($key_level == $this->niveau)?' selected="selected"':'').'>'.$name_level.'</option>';
    }
    $return .= '
      </select></div>
    </fieldset>
    <fieldset>
      <legend>Change Password</legend>
      <div class="field form-group">'.HTMLHelper::genererInputText('password_admin', null, array(), "Password").'</div>
      <!--<div class="field form-group">'.HTMLHelper::genererInputText('password2', null, array(), "Re-Type Password").'</div>-->
    </fieldset>';

    return $return;
  }

  /**
   * Formulaire d'enregistrement
   *
   * @param int $type Type de formulaire défini par les constantes MEMBER_FORM_*
   * @return string
   */
  public function html_get_abonnement_form($type = MEMBER_FORM_ABONNEMENT) {
    $return = '';

    //$this->load_from_html_form($post, array());
    if( MEMBER_FORM_ABONNEMENT == $type ) {
      $return = HTMLHelper::genererInputHidden('origin', $this->origin, array());
    }

    if( MEMBER_FORM_ADMIN == $type ) {
      $return .= HTMLHelper::genererInputHidden('id', $this->id);
    }

    if( MEMBER_FORM_ABONNEMENT != $type ) {
      $return .= '
          <div class="field form-group">'.HTMLHelper::genererInputText('prenom', $this->prenom, array(), 'Prénom <span class="oblig">*</span>' ).'</div>';
      $return .= '
          <div class="field form-group">'.HTMLHelper::genererInputText('nom', $this->nom, array(), 'Nom <span class="oblig">*</span>' ).'</div>';
    }
    $return .= '
          <div class="field form-group">'.HTMLHelper::genererInputText('email', $this->email, array(), 'Votre email <span class="oblig">*</span>' ).'</div>';
    if( MEMBER_FORM_ADMIN != $type ) {
      $return .= '
          <div class="field form-group">'.HTMLHelper::genererInputText('email2', $this->email2, array(), 'Confirmation email <span class="oblig">*</span>' ).'</div>';
    }

    if( MEMBER_FORM_ABONNEMENT == $type ) {
      $return .= '
          <div class="field form-group">'.HTMLHelper::genererInputPassword('password', $this->password, array(), 'Mot de passe <span class="oblig">*</span>' ).'</div>
          <div class="field form-group">'.HTMLHelper::genererInputPassword('password2', $this->password2, array(), 'Confirmation mot de passe <span class="oblig">*</span>' ).'</div>';
    }

    if( MEMBER_FORM_ADMIN == $type ) {
      $return .= '
          <div class="field form-group">'.HTMLHelper::genererInputPassword('password_admin', null, array(), 'Changer le mot de passe' ).'</div>';
    }

    if( MEMBER_FORM_NEWSLETTER == $type || MEMBER_FORM_ADMIN == $type) {
      $return .= '
          <div class="field form-group">';
      $liste_pays = array(
      'FR' => 'France',
      'DE' => 'Allemagne',
      'AD' => 'Andorre',
      'AT' => 'Autriche',
      'BE' => 'Belgique',
      'BG' => 'Bulgarie',
      'DK' => 'Danemark',
      'ES' => 'Espagne',
      'EE' => 'Estonie',
      'FI' => 'Finlande',
      'GR' => 'Grèce',
      'HU' => 'Hongrie',
      'IE' => 'Irlande',
      'IS' => 'Islande',
      'IT' => 'Italie',
      'LV' => 'Lettonie',
      'LI' => 'Liechtenstein',
      'LT' => 'Lituanie',
      'LU' => 'Luxembourg',
      'MT' => 'Malte',
      'NO' => 'Norvège',
      'NL' => 'Pays-Bas',
      'PL' => 'Pologne',
      'PT' => 'Portugal',
      'CY' => 'République de Chypre',
      'CZ' => 'République Tchèque',
      'RO' => 'Roumanie',
      'GB' => 'Royaume Uni',
      'SK' => 'Slovaquie',
      'SI' => 'Slovénie',
      'SE' => 'Suède',
      'CH' => 'Suisse',
      '--' => '-- Autre');
      $return .= HTMLHelper::genererSelect('pays', $liste_pays, $this->pays, array(), 'Pays <span class="oblig">*</span>').'</div>';
    }
    if( MEMBER_FORM_NEWSLETTER == $type || MEMBER_FORM_ADMIN == $type) {
      $liste_jour = array('' => '--');
      for($i = 1; $i <= 31; $i ++) {
        $n = ($i < 10)? '0'.$i : $i;
        $liste_jour[$n] = $n;
      }

      $liste_mois = array('' => '--');
      for($i = 1; $i <= 12; $i ++) {
        $n = ($i < 10)? '0'.$i : $i;
        $liste_mois[$n] = $n;
      }

      $liste_annee = array('' => '----');
      for($i = date('Y'); $i > 1900; $i --) {
        $liste_annee[$i] = $i;
      }

      $date_naiss = null;
      $date_naiss_jour = null;
      $date_naiss_mois = null;
      $date_naiss_annee = null;

      if($this->date_naissance) {
        $date_naiss = date('d/m/Y', $this->date_naissance);
        list($date_naiss_jour, $date_naiss_mois, $date_naiss_annee) = explode('/',$date_naiss);
      }
      $return .= '
            <div class="field form-group">
              <label>Date de naissance <span class="oblig">*</span></label>
              '.HTMLHelper::genererSelect('date_naissance_jour', $liste_jour, $date_naiss_jour).
           HTMLHelper::genererSelect('date_naissance_mois', $liste_mois, $date_naiss_mois).
           HTMLHelper::genererSelect('date_naissance_annee', $liste_annee, $date_naiss_annee).'
            </div>';
    }
    if(MEMBER_FORM_ADMIN != $type) {
      $return .= '
          <p><span class="oblig">*</span> Champ obligatoire</p>';
    }

    return $return;
  }

/**
 * Retourne la chaîne de caractère d'erreur en fonction du code correspondant
 *
 * @see Member->check_valid
 * @param int $num_error Code d'erreur
 * @return string
 */
  public static function get_message_erreur($num_error) {
    switch($num_error) {
      case 1 : $return = "Le champ <strong>Prénom</strong> est obligatoire."; break;
      case 2 : $return = "Le champ <strong>Nom</strong> est obligatoire."; break;
      case 3 : $return = "Le champ <strong>Mot de passe</strong> est obligatoire."; break;
      case 4 : $return = "Le champ <strong>Confirmez votre mot de passe</strong> est obligatoire."; break;
      case 5 : $return = "Le mot de passe et la confirmation ne sont pas identiques, veuillez réessayer."; break;
      case 6 : $return = "Le champ <strong>Email</strong> est obligatoire."; break;
      case 7 : $return = "Le champ <strong>Confirmez votre email</strong> est obligatoire."; break;
      case 8 : $return = "L'email et la confirmation ne sont pas identiques, veuillez réessayer."; break;
      case 9 : $return = "L'email saisi est invalide, veuillez réessayer."; break;
      case 11 : $return = "Cet email est déjà associé à un compte, veuillez en saisir un autre."; break;
      case 12 : $return = 'Vous devez accepter les <a href="'.get_page_url('conditions-generales-de-vente').'" target="_blank">Conditions Générales d\'Utilisation</a> pour pouvoir continuer.'; break;
      case 13 : $return = "Le champ <strong>Adresse</strong> est obligatoire."; break;
      case 14 : $return = "Le champ <strong>Code Postal</strong> est obligatoire."; break;
      case 15 : $return = "Le champ <strong>Ville</strong> est obligatoire."; break;
      case 16 : $return = "Le champ <strong>Pays</strong> est obligatoire."; break;
      case 17 : $return = "Le champ <strong>Genre</strong> est obligatoire."; break;
      case 18 : $return = "La <strong>Date de naissance</strong> saisie est invalide."; break;
      case 19 : $return = "Vous devez être majeur pour pouvoir bénéficier des offres."; break;
      default: $return = "Erreur de saisie, veuillez vérifier les champs.";
    }
    return $return;
  }

  /**
   * Effectue les vérifications basiques pour mettre à jour les champs
   * Retourne true si pas d'erreur, une liste de codes d'erreur sinon :
   * -  1 : Prenom vide
   * -  2 : Nom vide
   *
   * -  3 : Password vide
   * -  4 : Password contrôle vide
   * -  5 : Password et contrôle différents
   *
   * -  6 : Email vide
   * -  7 : Email contrôle vide
   * -  8 : Email et contrôle différents
   * -  9 : Email invalide
   * - 11 : Email existant
   *
   * - 12 : CGV non acceptées
   *
   * - 13 : Adresse vide
   * - 14 : Code postal vide
   * - 15 : Ville vide
   * - 16 : Pays vide
   * - 17 : Genre vide
   * - 18 : Date de naissance vide
   * - 19 : Date de naissance invalide
   *
   * @param int $flags Flags augmentant l'étendue des tests
   * @return true | array
   */
  public function check_valid($flags = 0) {
    $return = array();

    if($flags & MEMBER_NAME_CHECK) {
      $return[] = Member::check_compulsory($this->prenom, 1);
      $return[] = Member::check_compulsory($this->nom, 2);
    }

    if(($code = Member::check_compulsory($this->email, 6)) === true) {
      if(($code = Member::check_email($this->email, 9)) === true) {
        if($flags & MEMBER_NEW_USER_CHECK) {
          $check_new_member = Member::db_get_membre_by_email( $this->email );
          if($check_new_member === false) {
            $code = true;
          }else {
            if($check_new_member->niveau == 0) {
              $this->set_id($check_new_member->id);
              $code = true;
            }else {
              $code = 11;
            }
          }
        }
      }
    }
    $return[] = $code;
    if(isset($this->email2)) {
      if(($code = Member::check_compulsory($this->email2, 7)) === true) {
        $code = Member::check_equal($this->email, $this->email2, 8);
      }
      $return[] = $code;
    }

    if($this->niveau != 0) {
      $return[] = Member::check_compulsory($this->password, 3);
      if(isset($this->password2)) {
        if(($code = Member::check_compulsory($this->password2, 4)) === true) {
          $code = Member::check_equal($this->password, $this->password2, 5);
        }
        $return[] = $code;
      }
    }
    if($flags & MEMBER_PERSONAL_INFO_CHECK) {
      /*$return[] = Member::check_compulsory($this->code_postal, 14);
      $return[] = Member::check_compulsory($this->ville, 15);
      $return[] = Member::check_compulsory($this->adresse, 13);
      $return[] = Member::check_compulsory($this->genre, 17);
      */
      $return[] = Member::check_compulsory($this->pays, 16);
      if($flags & MEMBER_BIRTHDAY_CHECK) {
        if(($code = Member::check_compulsory($this->date_naissance, 18)) === true && $flags & MEMBER_GROWNUP_CHECK) {
          $code = ((time() - $this->date_naissance) > 18 * 365 * 24 * 60 * 60)?true:19;
        }
        $return[] = $code;
      }
    }

    if(isset($this->cgv_accepte)) {
      $return[] = ($this->cgv_accepte == 1)?true:12;
    }

    $return = array_unique($return);
    if(($true_key = array_search(true, $return, true)) !== false) {
      unset($return[$true_key]);
    }
    if(count($return) == 0) $return = true;
    return $return;
  }

  public function load_from_html_form($post_data, $file_data) {
    $return = array();
    if(isset($post_data['date_naissance_jour']) && isset($post_data['date_naissance_mois']) && isset($post_data['date_naissance_annee'])) {
      $post_data['date_naissance'] = mktime( 0, 0, 0, $post_data['date_naissance_mois'], $post_data['date_naissance_jour'], $_POST['date_naissance_annee']);
    }
    if(isset($post_data['email2'])) {
      $this->email2 = $post_data['email2'];
    }
    if(isset($post_data['password2'])) {
      $this->password2 = $post_data['password2'];
    }
    if(isset($post_data['cgv_accepte'])) {
      $this->cgv_accepte = $post_data['cgv_accepte'];
    }

    parent::load_from_html_form($post_data, $file_data);

    return $return;
  }

  /**
   * Corps de l'e-mail d'oubli de mot de passe
   *
   * @see php_mail
   * @param string $new_password Le nouveau mot de passe en clair
   * @return string
   */
  public function get_email_forgotten_password($new_password) {
    $return = '
      <td width="698" style="vertical-align:top; padding-left:80px; padding-right:80px; font-size: 14px; color:#444444;">
        <p>Bonjour '.wash_utf8($this->prenom).',</p>
        <p>Vous recevez cet email car vous avez demandé un nouveau mot de passe sur le site '.SITE_NAME.'</p>
        <table border="0" cellspacing="0" cellpadding="0" style="color:#FFFFFF; background-color:#ff569d; margin:20px 70px 20px 70px; width: 350px; font-family: Arial, sans serif; height:100%">
          <tr>
            <td style="border-bottom:solid thin #FFFFFF; height:35px; padding:2px 5px 0 10px;">
              <p style="font-size: 16px; line-height: 30px; font-weight: bold">
                <img src="'.IMG.'img_html/mails_auto_puce.gif" alt="" style="vertical-align:middle;">Vos nouveaux identifiants</p>
            </td>
          </tr>
          <tr>
            <td style="padding:10px; color:#FFFFFF; font-size: 14px">
              <p><span style="font-weight:bold;">Votre email :</span> '.$this->email.'<br>
              <span style="font-weight:bold;">Votre nouveau mot de passe :</span> '.wash_utf8($new_password).'</p>
            </td>
          </tr>
        </table>
        <p><span style="font-weight:bold;">Nous vous conseillons de modifier au plus tôt ce mot de passe </span> dans la rubrique mon compte <a style="color:#F22A83;" href="'.get_page_url('mon-compte-identifiants').'">en cliquant ici</a>.</p>
      </td>';

    return $return;
  }

  /**
   * Corps de l'e-mail de confirmation d'inscription
   *
   * @see php_mail
   * @param string $new_password Le nouveau mot de passe en clair
   * @return string
   */
  public function get_email_confirmation($password) {
    $return = '
      <td width="698" style="vertical-align:top; padding-left:80px; padding-right:80px; font-size: 14px; color:#444444;">
        <p>Bonjour '.wash_utf8($this->prenom).',</p>
        <p>Votre inscription a bien été prise en compte. Merci d\'avoir choisi '.SITE_NAME.' !</p>
        <table border="0" cellspacing="0" cellpadding="0" style="color:#FFFFFF; background-color:#ff569d; margin:20px 70px 20px 70px; width: 350px; font-family: Arial, sans serif; height:100%">
          <tr>
            <td style="border-bottom:solid thin #FFFFFF; height:35px; padding:2px 5px 0 10px;">
              <p style="font-size: 16px; line-height: 30px; font-weight: bold">
                <img src="'.IMG.'img_html/mails_auto_puce.gif" alt="" style="vertical-align:middle;">Rappel de vos identifiants</p>
            </td>
          </tr>
          <tr>
            <td style="padding:10px; color:#FFFFFF; font-size: 14px">
              <p><span style="font-weight:bold;">Votre email :</span> '.$this->email.'<br>
              <span style="font-weight:bold;">Votre mot de passe :</span> '.wash_utf8($password).'</p>
            </td>
          </tr>
        </table>
        <p><span style="font-weight:bold;">Pour accéder à votre compte,</span> <a style="color:#F22A83;" href="'.get_page_url('mon-compte').'">cliquez sur ce lien</a>.</p>
      </td>';

    return $return;
  }

  /**
   * Corps de l'e-mail de confirmation de modifications des identifiants
   *
   * @param unknown_type $new_password
   * @return unknown
   */
  public function get_email_modif_identifiants($new_password) {
    $return = '
      <td width="698" style="vertical-align:top; padding-left:80px; padding-right:80px; font-size: 14px; color:#444444;">
        <p>Bonjour '.wash_utf8($this->prenom).',</p>
        <p>Vous avez souhaité modifier vos identifiants. Ils sont pris en compte dès à présent.</p>
        <table border="0" cellspacing="0" cellpadding="0" style="color:#FFFFFF; background-color:#ff569d; margin:20px 70px 20px 70px; width: 350px; font-family: Arial, sans serif; height:100%">
          <tr>
            <td style="border-bottom:solid thin #FFFFFF; height:35px; padding:2px 5px 0 10px;">
              <p style="font-size: 16px; line-height: 30px; font-weight: bold">
                <img src="'.IMG.'img_html/mails_auto_puce.gif" alt="" style="vertical-align:middle;">Vos nouveaux identifiants</p>
            </td>
          </tr>
          <tr>
            <td style="padding:10px; color:#FFFFFF; font-size: 14px">
              <p><span style="font-weight:bold;">Votre email :</span> '.$this->email.'<br>
              <span style="font-weight:bold;">Votre nouveau mot de passe :</span> '.wash_utf8($new_password).'</p>
            </td>
          </tr>
        </table>
      </td>';

    return $return;
  }
}
?>