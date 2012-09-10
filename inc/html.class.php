<?php
  class HTMLHelper
  {
    protected static function genererAttributs( $attributs )
    {
      $result = '';
      foreach( array_keys( $attributs ) as $attr_name  )
      {
        switch ( $attr_name )
        {
          case 'selected':
          case 'disabled':
          case 'readonly':
          case 'checked':
           $result = $result.' '.$attr_name.'="'.$attr_name.'"';
            break;
          default:
            $result = $result.' '.$attr_name.'="'.$attributs[$attr_name].'"';
            break;
        }
      }
      return $result;
    }

    protected static function genererLabel($label_text, &$attributs) {
      if(isset($attributs['id'])) {
        $id = $attributs['id'];
      }else {
        if(isset($attributs['name'])) {
          $id = $attributs['name'];
        }else {
          $id = 'input_'.rand(1000,9999);
        }
        $attributs['id'] = $id;
      }

      $return = '
<label for="'.$id.'">'.$label_text.'</label>';

      return $return;
    }

    public static function genererInput( $type, $name = null, $valeur_defaut = null, $attributs = array(), $label_text = null )
    {
      $return = '';
      if(! is_null($name)) {
        $attributs['name'] = $name;
      }
      if(! is_null($valeur_defaut)) {
        $attributs['value'] = $valeur_defaut;
      }

      $label_position_right = 0;
      if(isset($attributs['label_position'])) {
        if($attributs['label_position'] == 'right') {
          $label_position_right = 1;
        }
        unset($attributs['label_position']);
      }

      $label = '';
      if(! is_null($label_text)) {
        $label = self::genererLabel($label_text, $attributs);
      }

      $input = '
<input type="'.$type.'"'.self::genererAttributs( $attributs ).'/>';



      if($label_position_right) {
        $return = $input.$label;
      }else {
        $return = $label.$input;
      }

      return $return;
    }

    public static function button( $name = null, $valeur_defaut = null, $attributs = array(), $label_text = null ) {
      return self::genererButton( $name, $valeur_defaut, $attributs, $label_text);
    }

    public static function genererButton( $name = null, $valeur_defaut = null, $attributs = array(), $label_text = null )
    {
      $return = '';
      if(! is_null($name)) {
        $attributs['name'] = $name;
      }
      if(! is_null($valeur_defaut)) {
        $attributs['value'] = $valeur_defaut;
      }

      $return = '
<button '.self::genererAttributs( $attributs ).'>'.(!is_null( $label_text) ?$label_text:$valeur_defaut).'</button>';

      return $return;
    }

    public static function hidden( $name, $valeur_defaut, $attributs = array()) {
      return self::genererInputHidden( $name, $valeur_defaut, $attributs );
    }
    public static function genererInputHidden( $name, $valeur_defaut, $attributs = array())
    {
      return self::genererInput( 'hidden', $name, $valeur_defaut, $attributs );
    }

    public static function genererInputText( $name = null, $valeur_defaut = null, $attributs = array(), $label_text = null, $texte_aide = null )
    {
      if(isset($attributs['class'])) {
        $attributs['class'] .= ' input_text';
      }else {
        $attributs['class'] = 'input_text';
      }

      if(is_null($valeur_defaut) && !is_null($texte_aide)) {
        $attributs['onfocus'] = "if(this.value == '".wash_utf8($texte_aide)."') this.value = ''";
        $attributs['onblur'] = "if(this.value == '') this.value = '".wash_utf8($texte_aide)."'";
        $valeur_defaut = $texte_aide;
      }
      return self::genererInput( 'text', $name, $valeur_defaut, $attributs, $label_text);
    }

    public static function genererInputPassword( $name = null, $valeur_defaut = null, $attributs = array(), $label_text = null )
    {
      if(isset($attributs['class'])) {
        $attributs['class'] .= ' input_text';
      }else {
        $attributs['class'] = 'input_text';
      }
      return self::genererInput( 'password', $name, $valeur_defaut, $attributs, $label_text);
    }

    public static function checkbox( $name = null, $valeur_defaut = null, $valeur = null, $attributs = array(), $label_text = null) {
      return self::genererInputCheckBox( $name, $valeur_defaut, $valeur, $attributs, $label_text);
    }
    public static function genererInputCheckBox( $name = null, $valeur_defaut = null, $valeur = null, $attributs = array(), $label_text = null)
    {
      if(!is_null($valeur)) {
        if($valeur) {
          $attributs['checked'] = 'checked';
        }
      }
      if(isset($attributs['class'])) {
        $attributs['class'] .= ' input_checkbox';
      }else {
        $attributs['class'] = 'input_checkbox';
      }
      return self::genererInputHidden($name, '0').self::genererInput( 'checkbox', $name, $valeur_defaut, $attributs, $label_text );
    }

    public static function genererInputRadio( $name = null, $valeur_defaut = null, $valeur = null, $attributs = array(), $label_text = null)
    {
      if(isset($attributs['class'])) {
        $attributs['class'] .= ' input_radio';
      }else {
        $attributs['class'] = 'input_radio';
      }
      if(!is_null($valeur)) {
        if($valeur_defaut === $valeur) {
          $attributs['checked'] = 'checked';
        }
      }
      return self::genererInput( 'radio', $name, $valeur_defaut, $attributs, $label_text );
    }

    public static function submit( $name = null, $valeur_defaut = null, $attributs = array() ) {
      return self::genererInputSubmit( $name, $valeur_defaut, $attributs );
    }
    public static function genererInputSubmit( $name = null, $valeur_defaut = null, $attributs = array() )
    {
      if(isset($attributs['class'])) {
        $attributs['class'] .= ' input_submit';
      }else {
        $attributs['class'] = 'input_submit';
      }
      return self::genererInput( 'submit', $name, $valeur_defaut, $attributs );
    }

    public static function genererInputImage( $name = null, $valeur_defaut = null, $attributs = array() )
    {
      if(isset($attributs['class'])) {
        $attributs['class'] .= ' input_image';
      }else {
        $attributs['class'] = 'input_image';
      }
      return self::genererInput( 'image', $name, $valeur_defaut, $attributs );
    }


    public static function genererTextArea( $name = null, $valeur_defaut = null, $attributs = array(), $label_text = null )
    {
      $return = '';
      if(! is_null($name)) {
        $attributs['name'] = $name;
      }
      if(!is_null($label_text)) {
        $return .= self::genererLabel($label_text, $attributs);
      }
      //Atributs obligatoires
      if(!isset($attributs['rows'])) {
        $attributs['rows'] = 3;
      }
      if(!isset($attributs['cols'])) {
        $attributs['cols'] = 50;
      }

      $return .= '
<textarea'.self::genererAttributs( $attributs ).'>'.$valeur_defaut.'</textarea>';

      return $return;
    }

    public static function genererSelect( $name = null, $liste_valeurs = array(), $valeur_defaut = null, $attributs = array(), $label_text = null )
    {
      $return = '';
      if(! is_null($name)) {
        $attributs['name'] = $name;
      }
      if(!is_null($label_text)) {
        $return .= self::genererLabel($label_text, $attributs);
      }
      $return .= '
<select'.self::genererAttributs( $attributs ).'>';
      foreach($liste_valeurs as $key => $value) {
        $return .= '
  <option value="'.wash_utf8($key).'"'.($valeur_defaut == $key?' selected="selected"':'').'>'.wash_utf8($value).'</option>';
      }
      $return .= '
</select>';

      return $return;
    }

    public static function genererInputFile($name = null, $valeur_defaut = null, $attributs = array(), $suppr_label, $label_text = null) {
      $return = '';
      if(! is_null($name)) {
        $attributs['name'] = $name;
      }
      if(! is_null($label_text)) {
        $return .= self::genererLabel($label_text, $attributs);
      }

      $return .= '
<div>';

      if($valeur_defaut) {
        if(!isset($attributs['name'])) {
          $attributs['name'] = 'input_file_'.rand(1000,9999);
        }
        $name = $attributs['name'];

        if(!isset($attributs['id'])) {
          $attributs['id'] = $name;
        }
        $id = $attributs['id'];



        $check_attributs = array(
          'id' => $name.'_del',
          'value' => '1',
          'onchange' => "if(this.checked == false) document.getElementById('$id').value = ''"
        );

        $attributs['onchange'] = 'document.getElementById(\''.$check_attributs['id'].'\').checked = true';

        $hidden_attributs = array(
          'id' => $name.'_file'
        );

        $return .= self::genererInputHidden($name.'_file', $valeur_defaut, $hidden_attributs);

        if(file_exists(DIR_ROOT.$valeur_defaut)) {
          if(is_image_ext($valeur_defaut)) {
            $valeur_preview = '
  <img src="'.URL_ROOT.$valeur_defaut.'" alt="'.$valeur_defaut.'"/>';
          }else {
            $valeur_preview = '
  <a href="'.URL_ROOT.$valeur_defaut.'">'.$valeur_defaut.'</a>';
          }
        }else {
          $valeur_preview = $valeur_defaut;
        }

        $return .= '
  <p>'.$valeur_preview.self::genererInputCheckBox($name.'_del', null, null, $check_attributs).self::genererLabel($suppr_label, $check_attributs).'</p>';
      }
      $return .= '
  <p>'.self::genererInput( 'file', $name, $valeur_defaut, $attributs ).'</p>
</div>';

      return $return;
    }
  }

?>