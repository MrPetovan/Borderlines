<?php
  require_once( 'html.class.php' );

  class HTMLTD extends HTMLBalise
  {
    public function nomBalise()
    {
      return 'td';
    }
    
    public function setWidth( $width )
    {
      $this->ajouterAttribut( 'width', $width );
    }
    
    public function setColSpan( $colSpan )
    {
      $this->ajouterAttribut( 'colspan', $colSpan );
    }
  }
  
  class HTMLTH extends HTMLBaliseText
  {
    public function nomBalise()
    {
      return 'th';
    }
  }
  
  class HTMLTR extends HTMLBalise
  {
    public function nomBalise()
    {
      return 'tr';
    }
    
    public function ajouterCellule()
    {
      $cell = new HTMLTD();
      $this->ajouterElement( $cell );
      return $cell;
    }
  }
  
  class HTMLTRTH extends HTMLBalise
  {
    public function nomBalise()
    {
      return 'tr';
    }
    
    public function ajouterCellule( $texte )
    {
      $cell = new HTMLTH( $texte );
      $this->ajouterElement( $cell );
      return $cell;
    }
  }
  
  class HTMLTable extends HTMLBalise
  {
    protected $ligneEntete = null;
    
    public function nomBalise()
    {
      return 'table';
    }
    
    public function ajouterLigne()
    {
      $ligne = new HTMLTR();
      $this->ajouterElement( $ligne );
      return $ligne;
    }
    
    public function Entete()
    {
      if ( is_null( $this->ligneEntete ) )
      {
        $this->ligneEntete = new HTMLTRTH();
        $this->ajouterElementPos( $this->ligneEntete, 0 );
        return $this->ligneEntete;
      }
      else
        return $this->ligneEntete;
    }
    
    public function setWidth( $width )
    {
      $this->ajouterAttribut( 'width', $width );
    }
  }
?>
