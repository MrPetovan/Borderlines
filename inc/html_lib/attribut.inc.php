<?php
  define( 'A_SELECTED', "selected" );
  define( 'A_DISABLED', "disabled" );
  define( 'A_READONLY', "readonly" );
  define( 'A_CHECKED', "checked" );

  function array_insert_pos( array &$tab, $pos, $element )
  {
    // position négative, interdire
    if ( $pos < 0 )
      die( "Impossible d'insérer l'élément à la position ".$pos );

    // position supérieure au nombre d'élément ou tableau vide
    if ( ( $pos >= count( $tab ) ) || ( count( $tab ) == 0 ) )
    {
      // insérer en dernière position
      $tab[] = $element;
    }
    // insérer au début
    else if ( $pos == 0 )
    {
      // fusionner un tableau contenant l'élément à ajouter avec la tableau existant
      $tab = array_merge( Array( $element ), $tab );
    }
    else
    {
      // récupérer tout les éléments de 0 à pos-1
      $tab_gauche = array_slice( $tab, 0, $pos -1 );
      // récupérer tout les éléments de pos à la fin
      $tab_droit = array_slice( $tab, $pos );
      // ajouter l'élément à la fin du tableau de gauche
      array_push( $tab, $element );
      // fusionner les deux tableaux
      $tab = array_merge( $tab_gauche, $tab_droit );
    }
  }

  function array_delete_key( array &$tab, $key )
  {
    if ( array_key_exists( $key, $tab ) )
    {
      unset( $tab[$key] );
    }
  }

  class Attributs {

    public $attributs = Array();

    public function nbAttributs()
    {
      return count( $this->attributs );
    }

    public function ajouter( $nom, $valeur )
    {
      if ( $nom != '' )
      {
        if ( $valeur != '' )
          $this->attributs[$nom] = $valeur;
        else
          array_delete_key( $this->attributs, $nom );
      }
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
            $this->attributs[$param] = $param;
          else
          {
            $nom = substr( $param, 0, $pos );
            $valeur = substr( $param, $pos + 1 );
            $this->attributs[$nom] = $valeur;
          }
        }
      }
    }

    public function genererHTML()
    {
      $result = '';
      foreach( $this->attributs as $nom => $valeur )
      {
        switch( $nom )
        {
          case A_SELECTED:
          case A_DISABLED:
          case A_READONLY:
          case A_CHECKED:
            $result = $result.' '.$nom;
            break;
          default:
            $result = $result.' '.$nom.'="'.$valeur.'"';
            break;
        }
      }
      return $result;
    }
    
    public function get( $nom_cle )
    {
      if ( array_key_exists( $nom_cle, $this->attributs ) )
        return $this->attributs[$nom_cle];
      else
        return null;
    }
  }
?>
