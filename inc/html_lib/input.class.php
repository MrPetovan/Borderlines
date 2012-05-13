<?php
  require_once( 'html.class.php' );
  
  class HTMLForm extends HTMLBalise
  {
    public function nomBalise()
    {
      return 'form';
    }
    
    public function setMethode( $methode )
    {
      $this->ajouterAttribut( 'method', $methode );
    }
    
    public function setAction( $action )
    {
      $this->ajouterAttribut( 'action', $action );
    }
    
    public function setOnSubmit( $onSubmit )
    {
      $this->ajouterAttribut( 'onsubmit', $onSubmit );
    }
  }
  
  class HTMLLabel extends HTMLBaliseText
  {
    public function nomBalise()
    {
      return 'label';
    }
    
    public function __construct()
    {
      if ( func_num_args() > 1 )
      {
        parent::__construct( func_get_arg( 0 ) );
        $this->setFor( func_get_arg( 1 ) );
      }
      else
        if ( func_num_args() > 0 )
          parent::__construct( func_get_arg( 0 ) );
        else
          parent::__construct();
        
    }
    
    public function setFor( $id )
    {
      $this->ajouterAttribut( 'for', $id );
    }
  }
  
  abstract class HTMLInput extends HTMLBalise
  {
    public $label = null;
    public $labelAGauche = true;
  
    abstract protected function inputType();
    
    public function nomBalise()
    {
      return 'input';
    }
    
    public function __construct()
    {
      parent::__construct();
      $this->ajouterAttribut( 'type', $this->inputType() );
    }
    
    public function ajouterLabel( $texte, $labelAGauche = True )
    {
      if ( $this->existeLabel() )
        unset( $this->label );
      
      if ( !is_null( $this->attributs->get( 'id' ) ) )
        $this->label = new HTMLLabel( $texte, $this->attributs->get( 'id' ) );
      else
        $this->label = new HTMLLabel( $texte );
        
      $this->labelAGauche = $labelAGauche;
      
      return $this->label;
    }
    
    public function existeLabel()
    {
      return !is_null( $this->label );
    } 
    
    public function genererHTML()
    {
      $result = parent::genererHTML();
      if ( $this->existeLabel() )
        if ( $this->labelAGauche )
          $result = $this->label->genererHTML().$result;
        else
          $result = $result.$this->label->genererHTML();
          
      return $result;
    }
    
    public function setId( $id )
    {
      parent::setId( $id );
      if ( $this->existeLabel() )
        $this->label->setFor( $id );
    }
    
    public function setValue( $valeur )
    {
      $this->ajouterAttribut( 'value', $valeur );
    }
  }
  
  class HTMLInputHidden extends HTMLInput
  {
    protected function inputType()
    {
      return 'hidden';
    }
  }
  
  class HTMLInputText extends HTMLInput
  {
    protected function inputType()
    {
      return 'text';
    }
  }
  
  class HTMLInputCheckBox extends HTMLInput
  {
    protected function inputType()
    {
      return 'checkbox';
    }
    
    public function setChecked( $isChecked )
    {
      $this->ajouterAttributBooleen( A_CHECKED, $isChecked );
    }
  }
  
  class HTMLTextArea extends HTMLBaliseText
  {    
    public function nomBalise()
    {
      return 'textarea';
    }
    
    public function setCols( $cols )
    {
      $this->ajouterAttribut( 'cols', $cols );
    }
    
    public function setRows( $rows )
    {
      $this->ajouterAttribut( 'rows', $rows );
    }
  }
  
  class HTMLOption extends HTMLBaliseText
  {
    public function nomBalise()
    {
      return 'option';
    }
    
    public function genererHTML()
    {
      return str_replace( "\r\n", "\r\n\t", parent::genererHTML() );
    }
    
    public function setValue( $valeur )
    {
      $this->ajouterAttribut( 'value', $valeur );
    }
    
    public function setTitle( $titre )
    {
      $this->ajouterAttribut( 'title', $titre );
    }
    
    public function setSelected( $isSelected )
    {
      $this->ajouterAttributBooleen( A_SELECTED, $isSelected );
    }
  }
  
  class HTMLSelect extends HTMLBalise
  {
    public function nomBalise()
    {
      return 'select';
    }
    
    public function ajouterOption( $texte, $valeur )
    {
      $option = new HTMLOption( $texte );
      $option->setValue( $valeur );
      $this->ajouterElement( $option );
      return $option;
    }
    
    public function genererOptions( $valeurs, $valeur )
    {
      foreach( $valeurs as $val => $lib )
      {
        $option = $this->ajouterOption( $lib, $val );
        if ( $val == $valeur )
          $option->setSelected( true );
      }
    }
    
    public function setOnChange( $onChange )
    {
      $this->ajouterAttribut( 'onchange', $onChange );
    }
  }
?>
