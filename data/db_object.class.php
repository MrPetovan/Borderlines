<?php
 /**
   * Classe d'abstraction d'objets reliés à une table en BD
   *
   * Pré-requis SQL :
   * - Un champ Clé primaire de nom "id"
   *
   * Pré-requis PHP :
   * - Une classe dérivant de DBObject (avec toutes les méthodes abstraites
   *   implémentées)
   * - Pour l'hydratation, les membres de la classe doivent commencer par un
   *   "_" et ensuite reproduire le nom exact du champ SQL correspondant.
   *   Minuscules et "_" uniquement conseillé.
   *
   * Notes :
   * - Les getter et les setter standards sont générés à la volée par la
   *   fonction magique __call, de la forme get_{nom champ sql} et
   *   set_[nom champ sql}. Ils peuvent être surchargés
   * - Les fonctions *_class sont dûes à une limitation de PHP qui fait que le
   *   mot clé self n'est pas typé de la classe fille quand appelée depuis une
   *   fonction héritée. D'où l'utilisation de get_class() et le renvoit à la
   *   fonction sans "_class"
   * - Les objets chargés depuis la base sont en singleton, chaque instanciation
   *   d'un objet avec le même "id" appelera le même objet. Cela permet de
   *   réduire le nombre de requête en base, et d'être sûr de modifier les
   *   propriétés du même objet tout le long du script
   *
   */

  abstract class DBObject {
    /**
     * Identifiant SQL de l'objet. N'est défini que si l'objet a sa contrepartie
     * en base
     *
     * @var int
     */
    protected $_id;
    /**
     * Indique si les propriétés annexes de l'objet ont été chargées ou pas. Un
     * objet en état "raw" ne possède que ses attributs hydratés depuis la base
     *
     * @var unknown_type
     */
    protected $raw;

    /**
     * Tableau des objets singleton, organisé par classe et par ID.
     *
     * @var array
     */
    protected static $object_array;


    /**
     * Fonction de singleton
     * @example $user = Member::instance( 12 )
     *
     * @param int $id
     * @return object
     */
    public static function instance( $id = null ) {
      $class = get_called_class ( );
    
      if( !is_null( $id )) {
        //$array = debug_backtrace();

        if( !isset( self::$object_array[ $class ][ $id ])) {
          self::$object_array[ $class ][ $id ] = new $class($id);
        }

        return self::$object_array[ $class ][ $id ] ;
      }else {
        return new $class();
      }
    }

    protected function __construct($id = null) {
      if(!is_null($id)) {
        $this->db_load_from_id($id);
        $this->raw = true;
      }
    }

    public function get_id() { return $this->_id;}
    public function set_id($id) { $this->_id = $id;}

    /**
     * Retourne le nom de la table MySQL associée
     *
     * @abstract
     * @static
     * @access protected
     * @return string Nom de la table
     */
    protected static abstract function get_table_name();

    /**
     * Vérifie la validité des données de l'objet avant la sauvegarde en base.
     * Doit être surchargée dans les classes objet
     *
     * @return bool
     */
    public function check_valid() {
      return true;
    }


    /**
     * Trie un tableau de retour de check_valid() en éliminant les valeurs "true" = test passé.
     * Si le tableau est vide à la fin, l'objet est valide.
     *
     * @param array $return
     * @return array
     */
    protected static function check_valid_array($return) {
      sort($return, SORT_NUMERIC);
      $return = array_unique($return);
      if(($true_key = array_search(true, $return, true)) !== false) {
        unset($return[$true_key]);
      }
      if(count($return) == 0) $return = true;

      return $return;
    }

    public function __get( $property ) {
      $function_name = "get_".$property;
      return $this->$function_name();
    }
    public function __set( $property, $value ) {
      $function_name = "set_".$property;
      $this->$function_name( $value );
    }

    public function __call($method, $args) {
      if(substr($method, 0, strlen("get_")) == "get_") {
        $var = substr($method, strlen("get"));

        if(property_exists($this, $var)) {
          // Appelle __get
          return $this->$var;
        }else {
          error_log('[Framework] __call:get : Class variable doesn\'t exist : '.$var);
          throw new Exception('[Framework] __call:get : Class variable doesn\'t exist : '.$var);
          return null;
        }
      }

      if(substr($method, 0, strlen("set_")) == "set_") {
        $var = substr($method, strlen("set"));
        $value = current($args);
        if( property_exists($this, $var) ) {
          // Appelle __set
          $this->$var = $value;
        }else {
          error_log('[Framework] __call:set : Class variable doesn\'t exist : '.$var);
          throw new Exception('[Framework] __call:set : Class variable doesn\'t exist : '.$var);
          return null;
        }
      }
    }

    /**
     * Retourne un tableau clé->valeur de toutes les propriétés innées (=SQL)
     * de l'objet. Ces propriétés sont indiquées par un _ devant le nom SQL
     *
     * @return array
     */
    public function get_db_members() {
      $return = array();
      $array = get_object_vars($this);
      foreach ( $array as $key => $value ) {
        if( strpos( $key, '_' ) === 0 ) {
          $return[ substr( $key, 1 ) ] = $value;
        }
      }
      return $return;
    }

    /**
     * Fonction de vérification de l'existence d'un objet identifié.
     *
     * @param int $id
     * @return bool
     */
    public static function db_exists($id){
      $return = false;
      
      $sql = "SELECT * FROM `".static::get_table_name()."` WHERE `id` = ".mysql_ureal_escape_string($id);

      $res = mysql_uquery($sql);
      if($res && mysql_num_rows($res) == 1) {
        $return = true;
      }

      return $return;
    }

    /**
     * Fonction standard de récupération d'un objet identifié.
     *
     * @param int $id
     * @return object|false
     */
    public static function db_get_by_id($id) {
      if( static::db_exists($id) ) {
        return static::instance( $id );
      }else {
        return false;
      }
    }

    /**
     * Fonction de mise en forme des messages d'erreur de validité d'un objet
     *
     * @param array $tab_error
     * @param string $html_msg
     * @return bool
     */
    public static function manage_errors($tab_error, &$html_msg) {
      $return = false;
      if($tab_error === true) {
        $return = true;
      }else {
        foreach ($tab_error as $error) {
          $tab_msg[] = static::get_message_erreur( $error );
        }
        $tab_msg = array_unique($tab_msg);
        $html_msg = '<div class="error">';
        foreach ($tab_msg as $msg_error) {
          $html_msg .= '<p>'.$msg_error.'</p>';
        }
        $html_msg .= '</div>';
      }
      return $return;
    }

    /**
     * Fonction d'hydratation de l'objet
     *
     * @param int $id
     */
    public function db_load_from_id($id) {
      $sql = "SELECT * FROM `".$this->get_table_name()."` WHERE `id` = ".mysql_ureal_escape_string($id);

      $res = mysql_uquery($sql);

      if($res && mysql_num_rows($res) == 1) {
        $data = mysql_fetch_assoc($res);

        foreach($data as $var_name => $value) {
          $var_name = "_$var_name";
          $this->$var_name = $value;
        }

        $return = true;
      }else {
        $return = false;
      }
      return $return;
    }

    /**
     * Fonction standard d'écriure de l'objet en base. Effectue les vérifications
     * de validité (en fonction des flags), crée l'objet s'il n'existait pas, le
     * met à jour s'il existait (vérification sur _id)
     *
     * @param int $flags
     * @return bool;
     */
    public function db_save($flags = 0) {
      if(($return = $this->check_valid($flags)) === true) {
        if(is_null($this->get_id())) {
          return $this->db_add();
        }else {
          return $this->db_update();
        }
      }
      return $return;
    }

    public function db_update() {
      $sql = "UPDATE ".$this->get_table_name()." SET";
      $champs_sql = array();
      foreach($this as $nom_champ => $value) {
        if(strpos($nom_champ, '_') === 0) {
          $nom_champ_sql = substr($nom_champ, 1);
          $champs_sql[] = "`$nom_champ_sql`             = ".mysql_ureal_escape_string($this->$nom_champ);
        }
      }
      $sql .= implode('
 ,', $champs_sql);

      $sql .= "
WHERE `id` = ".mysql_ureal_escape_string($this->get_id());
      return mysql_uquery($sql);
    }

    protected function db_add() {
      $sql = "INSERT INTO `".$this->get_table_name()."` SET";
      $champs_sql = array();
      foreach($this as $nom_champ => $value) {
        if(strpos($nom_champ, '_') === 0) {
          $nom_champ_sql = substr($nom_champ, 1);
          $champs_sql[] = "`$nom_champ_sql`             = ".mysql_ureal_escape_string($this->$nom_champ);
        }
      }
      $sql .= implode('
 ,', $champs_sql);

      $return = mysql_uquery($sql);
      $this->set_id(mysql_insert_id());
//var_debug($sql);
      return $return;
    }

    public function db_delete() {
      $sql = "DELETE FROM `".$this->get_table_name()."` WHERE `id` = ".mysql_ureal_escape_string($this->id);

      if( isset( self::$object_array[ get_class($this) ][ $this->id ])) {
        unset( self::$object_array[ get_class($this) ][ $this->id ] );
      }

      return mysql_uquery($sql);
    }

    /**
     * Fonction retournant une liste de tous les objets d'une table
     *
     * @return array Tableau des objets
     * @static
     */
    public static function db_get_all($page = null, $limit = NB_PER_PAGE) {
      $sql = 'SELECT `id` FROM `'.static::get_table_name().'` ORDER BY `id`';

      if(!is_null($page) && is_numeric($page)) {
        $start = ($page - 1) * $limit;
        $sql .= ' LIMIT '.$start.','.$limit;
      }

      return static::sql_to_list( $sql );
    }
    
    public static function db_count_all() {
      $sql = "SELECT COUNT(`id`) FROM `".static::get_table_name().'`';
      $res = mysql_uquery($sql);
      return array_pop(mysql_fetch_row($res));
    }

    public static function db_get_select_list() { return array(); }

    /**
     * Fonction retournant une liste d'objets en fonction d'une requête SQL
     *
     * La requête doit contenir un champ "id".
     *
     * @param $sql string Requête SQL à exécuter
     * @param $class string Classe des objets à créer
     * @return array Tableau des objets
     * @static
     */
    protected static function sql_to_list($sql) {
      $res = mysql_uquery($sql);

      if($res) {
        $return = array();
        while($data = mysql_fetch_assoc($res)) {
          $return[$data['id']] = static::instance( $data['id'] );
        }
        mysql_free_result($res);
      }else {
        $return = false;
      }

      return $return;
    }

    /**
     * Fonction retournant un objet unique en fonction d'une requête SQL
     *
     * La requête doit contenir un champ "id".
     *
     * @param $sql string Requête SQL à exécuter
     * @return object Objet de la classe passée en paramètre
     * @static
     */
    protected static function sql_to_object($sql) {
      $res = mysql_uquery($sql);

      if($res && mysql_num_rows($res) > 0) {
        $data = mysql_fetch_assoc($res);
        $return = static::instance( $data['id'] );
      }else {
        $return = false;
      }

      return $return;
    }

    /**
     * Fonction de traitement des champs image d'un objet
     *
     * @param array $tab_champs un tableau du type [nom_du_champ_image_de_l'objet] => [nom_de_l'input_image]
     * @param int $error numéro de la première erreur
     * @param string $path_image chemin de sauvegarde des images (relatif à la racine du site)
     * @param array $tab_size un tableau du type "minsize" => array( [largeur], [hauteur] ), "maxsize" => ...
     * @param array $post_data le tableau $_POST
     * @param array $files_data le tableau $_FILES
     *
     */
    protected function save_form_image_file($tab_champs, $error = 0, $path_image = '', $tab_size = array(),  $post_data = null, $files_data = null) {
      $return = array();

      if(is_null($post_data)) {
        $post_data = $_POST;
      }
      if(is_null($files_data)) {
        $post_data = $_FILES;
      }

      foreach($tab_champs as $image_name => $field_name) {

        $get_function_name = 'get_'.$image_name;
        $set_function_name = 'set_'.$image_name;

        if(method_exists($this, $get_function_name) && method_exists($this, $set_function_name)) {

          if(isset($post_data[$field_name.'_del'])) {

            if(isset($post_data[$field_name.'_file']) && file_exists(DIR_ROOT.$post_data[$field_name.'_file'])) {
              unlink(DIR_ROOT.$post_data[$field_name.'_file']);
            }

            $this->$set_function_name('');
          }
          //FILES existence check
          if(isset($files_data[$field_name])) {

            $image_info = $files_data[$field_name];

            //File error check
            if(isset($image_info['error']) && $image_info['error'] === 0) {

              //File type check
              if(is_image_mime($image_info['type'])) {

                $size = getimagesize($image_info['tmp_name']);

                $sizeok = false;

                /*
                 * MinSize : La taille doit être supérieure ou égale
                 * MaxSize : La taille doit être inférieure ou égale
                 */

                if(
                  (
                    !isset($tab_size['min_size'][$image_name]) ||
                    isset($tab_size['min_size'][$image_name]) &&
                    $size[0] >= $tab_size['min_size'][$image_name][0] &&
                    $size[1] >= $tab_size['min_size'][$image_name][1]
                  ) && (
                    !isset($tab_size['max_size'][$image_name]) ||
                    isset($tab_size['max_size'][$image_name]) &&
                    $size[0] <= $tab_size['max_size'][$image_name][0] &&
                    $size[1] <= $tab_size['max_size'][$image_name][1]
                  )
                ) {
                  $sizeok = true;
                }

                //File size check
                if($sizeok) {

                  _mkdir(DIR_ROOT.$path_image);

                  $new_path = $path_image.$field_name.'_'.rand(1000000, 9999999).'_'.$image_info['name'];

                  move_uploaded_file($image_info['tmp_name'], DIR_ROOT.$new_path);

                  $this->$set_function_name($new_path);

                }else {
                  $return[] = $error + 4; // Image size
                }
              }else {
                $return[] = $error + 3; // Type mime
              }
            }elseif($image_info['error'] != 4) {
              $return[] = $error + 2; // Image error
            }
          }else {
            $return[] = $error + 1; // Images inexistante $_FILES
          }
        }else {
          $return[] = $error; //Champ invalide
        }
        $error += 5;
      }

      return $return;
    }

    /**
     * Population d'un objet à partir d'un tableau $_POST. Les noms des champs
     * doivent correspondre aux noms des propriétés de l'objet
     *
     * @param array $post_data
     */
    public function load_from_html_form($post_data) {
      if(ini_get('magic_quote_gpc')) {
        $post_data = rstripslashes($post_data);
      }
      
      foreach ($post_data as $name => $value) {
        $sql_name = '_'.$name;
        
        if($name != "id" && property_exists( $this, $sql_name ) ) {
          $this->__set($name, $value);
        }
      }
    }


    /**
     * Vérifie que deux valeurs sont égales dans le cadre de la vérification de
     * validité d'un objet. Comparaison stricte ou non.
     *
     * @param mixed $value
     * @param mixed $value2
     * @param int $code_erreur
     * @param bool $strict
     * @return true|int
     */
    public static function check_equal($value, $value2, $code_erreur = false, $strict = false) {
      $return = true;
      if($strict) {
        if($value !== $value2) $return = $code_erreur;
      }else {
        if($value != $value2) $return = $code_erreur;
      }
      return $return;
    }

    /**
     * Vérifie qu'une valeur est renseignée ou non. Vérification null strict ou chaîne vide.
     *
     * @param mixed $value
     * @param int $code_erreur
     * @param bool $null_strict
     * @return true|int
     */
    public static function check_compulsory($value, $code_erreur = false, $null_strict = false) {
      $return = true;
      if($null_strict) {
        if(is_null($value)) $return = $code_erreur;
      }else {
        if($value == '') $return = $code_erreur;
      }
      return $return;
    }

     
    /**
     * Vérifie la validité syntaxique d'une adresse email.
     *
     * @param string $email
     * @param int $code_erreur
     * @return true|int
     */
    public static function check_email($email, $code_erreur = false) {
      $return = true;
      if(filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $return = $code_erreur;
      }
      return $return;
    }

    /**
     * Vérifie la validité d'une date de naissance
     *
     * @param sting $date
     * @param mixed $code_erreur
     * @return mixed TRUE ou $code_erreur
     */
    public static function check_birthdate($date, $code_erreur = false) {
      $return = $code_erreur;
      $date = guess_date($date);
      if($date) {
        $date_array = getdate($date);
        if(is_array($date_array)) {
          if(checkdate($date_array['mon'], $date_array['mday'], $date_array['year'])) {
            $return = true;
          }
        }
      }
      return $return;
    }
  }
?>