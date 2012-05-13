<?php
  require_once( 'attribut.inc.php' );
  
  abstract class HTMLRoot
  {
    abstract public function genererHTML();
  }
  
  class HTMLTexte extends HTMLRoot
  {
    public $texte;
    
    public function __construct( $texte )
    {
      $this->texte = $texte;
    }
  
    public function genererHTML()
    {
      return $this->texte;
    }
  }
  
  abstract class HTMLBalise extends HTMLRoot implements Iterator
  {
    public $attributs;
    public $elements = Array();
    
    abstract public function nomBalise();
    
    public function __construct()
    {
      $this->attributs = new Attributs();
    }
    
    public function nbElements()
    {
      return count( $this->elements );
    }
    
    public function ajouterAttribut( $nom, $valeur )
    {
      $this->attributs->ajouter( $nom, $valeur );
    }
    
    public function ajouterAttributs()
    {
      if ( func_num_args() > 0 )
      {
        for ( $cpt = 0; $cpt < func_num_args(); $cpt++ )
        {
          $param = func_get_arg( $cpt );
          $pos = strpos( $param, '=' );
          if ( $pos === false )
            $this->ajouterAttribut( $param, $param );
          else
          {            
            $nom = substr( $param, 0, $pos );
            $valeur = substr( $param, $pos + 1 );
            $this->ajouterAttribut( $nom, $valeur );
          }
        }
      }
    }
    
    public function ajouterAttributBooleen( $nom, $estPositionne )
    {
      if ( $estPositionne )
        $this->ajouterAttribut( $nom, $nom );
      else
        $this->ajouterAttribut( $nom, '' );
    }
    
    public function ajouterElement( HTMLRoot $element )
    {
      if ( !is_null( $element ) )
        $this->elements[] = $element;
    }
    
    public function ajouterElementPos( HTMLRoot $element, $pos )
    {
      if ( !is_null( $element ) )
        array_insert_pos( $this->elements, $pos, $element );
    }
    
    public function ajouterTexte( $texte )
    {
      $this->ajouterElement( new HTMLTexte( $texte ) );
    }
    
    public function supprimerElement( $pos )
    {
      unset( $this->elements[$pos] );
    }
    
    public function genererHTML()
    {
      if ( $this->NbElements() != 0 )
      {
        $result = "\r\n".'<'.$this->nomBalise().$this->attributs->genererHTML().'>';
        
        foreach ( $this->elements as $element )
        {
          $result = $result.$element->genererHTML();
        }
        
        $result = $result."\r\n".'</'.$this->nomBalise().'>';
        
        return $result;
      }
      else
      {
        return "\r\n".'<'.$this->nomBalise().$this->attributs->genererHTML().'/>';
      }
    }
    
    public function setCssClass( $nom_class )
    {
      $this->ajouterAttribut( 'class', $nom_class );
    }
    
    public function setName( $nom )
    {
      $this->ajouterAttribut( 'name', $nom );
    }
    
    public function setId( $id )
    {
      $this->ajouterAttribut( 'id', $id );
    }
    
    /* Implémentation de Iterator */
    protected $CurrentIndex;
    
    public function current()
    {
      return $this->elements[$this->CurrentIndex];
    }
    
    public function key()
    {
      return $this->CurrentIndex;
    }
    
    public function next()
    {
      $this->CurrentIndex++;
    }
    
    public function rewind()
    {
      $this->CurrentIndex = 0;
    }
    
    public function valid()
    {
      return ( $this->CurrentIndex < $this->nbElements() );
    }
  }
  
  abstract class HTMLBaliseText extends HTMLBalise
  {
    protected $texte_element = null;
    
    public function __construct()
    {
      parent::__construct();
      if ( func_num_args() > 0 )
        $this->setTexte( func_get_arg( 0 ) );
    }    
    
    public function setTexte( $texte )
    {
      if ( is_null( $this->texte_element ) )
      {
        $this->texte_element = new HTMLTexte( $texte );
        $this->ajouterElement( $this->texte_element );
      }
      else
        $this->texte_element->texte = $texte;
    }
  }
  
  class HTMLDiv extends HTMLBalise
  {
    public function nomBalise()
    {
      return 'div';
    }
    
    public function setDisplay( $isVisible )
    {
      if ( $isVisible )
        $this->ajouterAttribut( 'style', 'display:none' );
      else
        $this->ajouterAttribut( 'style', '' );
    }
    
    public function setHeight( $height )
    {
      $this->ajouterAttribut( 'height', $height );
    }
    
    public function setWidth( $width )
    {
      $this->ajouterAttribut( 'width', $width );
    }
  }
  
  class HTMLP extends HTMLBalise
  {
    public function nomBalise()
    {
      return 'p';
    }
  }
?>
