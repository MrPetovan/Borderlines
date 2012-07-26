<?php

class Vertex {
  public $guid;
  public $x;
  public $y;
  public $label;
  
  static $distanceTable = array();
  static $angleTable = array();
  static $anglePolarTable = array();
  
  function __construct( $coordX, $coordY, $guid = null, $label = null ) {
    $this->x = $coordX;
    $this->y = $coordY;
    $this->guid = $guid;
    $this->label = $label;

    if( is_null( $this->guid ) ) {
      $this->guid = str_pad( $coordX, 0, '0', STR_PAD_LEFT ).','.str_pad( $coordY, 0, '0', STR_PAD_LEFT );
    }
  }
  
  public function __toString() {
    $return = is_null( $this->label )?$this->guid:$this->label;
    
    return (string)$return;
  }
  
  static function distance( $vertex1, $vertex2 ) {
    
    //if( !isset( self::$distanceTable[ $vertex1->guid ][ $vertex2->guid ] ) ) {
    
      $a = $vertex2->x - $vertex1->x;
      $b = $vertex2->y - $vertex1->y;
      
      $distance = round( sqrt( pow( $a, 2 ) + pow( $b, 2 ) ), 1 );
      
      self::$distanceTable[ $vertex1->guid ][ $vertex2->guid ] = $distance;
      self::$distanceTable[ $vertex2->guid ][ $vertex1->guid ] = $distance;
    //}

    return self::$distanceTable[ $vertex1->guid ][ $vertex2->guid ];
  }
  
  static function getDistanceTable() {
    return self::$distanceTable;
  }
  
  static function angle( $vertexA, $vertexB, $vertexC ) {
    if( ! isset( self::$angleTable[ $vertexA->guid . ';' . $vertexB->guid . ';' . $vertexC->guid ] ) ) {
      $AB = Vertex::distance( $vertexA, $vertexB );
      $BC = Vertex::distance( $vertexB, $vertexC );
      $AC = Vertex::distance( $vertexC, $vertexA );
      
      // Al-Khashi theorem
      self::$angleTable[ $vertexA->guid.';'.$vertexB->guid.';'.$vertexC->guid ] =
        rad2deg( acos( ( pow( $AB, 2 ) + pow( $BC, 2 ) - pow( $AC, 2 ) ) / ( 2 * $AB * $BC ) ) );
    }
    return self::$angleTable[ $vertexA->guid.';'.$vertexB->guid.';'.$vertexC->guid ];
  }
  
  /* Polar angle difference between $vertexA and $vertexC, $vertexB is the origin, $vertexA is 0 angle */
  static function anglePolar( $vertexA, $vertexB, $vertexC ) {
    if( ! isset( self::$anglePolarTable[ $vertexA->guid . ';' . $vertexB->guid . ';' . $vertexC->guid ] ) ) {
      $Ax = $vertexA->x - $vertexB->x;
      $Ay = $vertexA->y - $vertexB->y;
      $Cx = $vertexC->x - $vertexB->x;
      $Cy = $vertexC->y - $vertexB->y;
      
      if( $Ax <= 0 && pow( $Ay, 2 ) == 0 ) {
        $Apolar = 180;
      }else {
        $Apolar = rad2deg( 2 * atan( $Ay / ( $Ax + sqrt( pow( $Ax, 2 ) + pow( $Ay, 2 ) ) ) ) );
      }
      if( $Cx + sqrt( pow( $Cx, 2 ) + pow( $Cy, 2 ) ) == 0 ) {
        $Cpolar = 180;
      }else {
        $Cpolar = rad2deg( 2 * atan( $Cy / ( $Cx + sqrt( pow( $Cx, 2 ) + pow( $Cy, 2 ) ) ) ) );
      }

      $result = $Cpolar - $Apolar;
      if( $result < 0 ) $result += 360;

      self::$anglePolarTable[ $vertexA->guid.';'.$vertexB->guid.';'.$vertexC->guid ] = $result;
    }
    return self::$anglePolarTable[ $vertexA->guid.';'.$vertexB->guid.';'.$vertexC->guid ];
  }
  
  static function aireTriangle( $vertexA, $vertexB, $vertexC ) {
    $AB = Vertex::distance( $vertexA, $vertexB );
    $BC = Vertex::distance( $vertexB, $vertexC );
    $AC = Vertex::distance( $vertexC, $vertexA );
    
    return sqrt( ( $AB + $BC + $AC) * ( - $AB + $BC + $AC ) * ( $AB - $BC + $AC ) * ( $AB + $BC - $AC ) ) / 4;
  }
  
  static function isCrossing( $vertexA, $vertexB, $vertexC, $vertexD ) {
    //var_dump( "isCrossing", $vertexA->guid, $vertexB->guid, $vertexC->guid, $vertexD->guid );
    $isCrossing = false;
    
    if(
      $vertexA == $vertexC && $vertexB == $vertexD ||
      $vertexA == $vertexD && $vertexB == $vertexC ) {
      // All identical vertices
      $isCrossing = true;
    }elseif(
      $vertexA != $vertexC && $vertexB != $vertexD &&
      $vertexA != $vertexD && $vertexB != $vertexC
    ) {
      // If only one vertex id identical => not crossing
      
      
      
      // Division by zero protection
      $v1 = ($vertexA->x == $vertexB->x);
      $v2 = ($vertexC->x == $vertexD->x);
      
      if( ! ($v1 && $v2) ) {
        if( $v1 ) {
          // AB is vertical
          $aCD = ($vertexD->y - $vertexC->y) / ($vertexD->x - $vertexC->x);
          $bCD = $vertexC->y - $aCD * $vertexC->x;
          
          $commonY= $aCD * $vertexA->x + $bCD;
          
          $isCrossing =
            $commonY > min( $vertexA->y, $vertexB->y ) &&
            $commonY < max( $vertexA->y, $vertexB->y ) &&
            $commonY > min( $vertexC->y, $vertexD->y ) &&
            $commonY < max( $vertexC->y, $vertexD->y );
        }elseif( $v2 ) {
          // CD is vertical
          $aAB = ($vertexB->y - $vertexA->y) / ($vertexB->x - $vertexA->x);
          $bAB = $vertexA->y - $aAB * $vertexA->x;

          $commonY= $aAB * $vertexC->x + $bAB;
          
          $isCrossing =
            $commonY > min( $vertexA->y, $vertexB->y ) &&
            $commonY < max( $vertexA->y, $vertexB->y ) &&
            $commonY > min( $vertexC->y, $vertexD->y ) &&
            $commonY < max( $vertexC->y, $vertexD->y );
        }else {
          // a : directional coefficient / b : origin oodonnée
          $aAB = ($vertexB->y - $vertexA->y) / ($vertexB->x - $vertexA->x);
          $aCD = ($vertexD->y - $vertexC->y) / ($vertexD->x - $vertexC->x);
          
          // AB and CD are not parallel
          if( $aAB != $aCD ) {
            $bAB = $vertexA->y - $aAB * $vertexA->x;
            $bCD = $vertexC->y - $aCD * $vertexC->x;
            // X coordinate of the extended line crossing
            $commonX = ( $bCD - $bAB ) / ( $aAB - $aCD );
            
            // Lines are crossing between the given vertices
            $isCrossing =
              $commonX > min( $vertexA->x, $vertexB->x ) &&
              $commonX < max( $vertexA->x, $vertexB->x ) &&
              $commonX > min( $vertexC->x, $vertexD->x ) &&
              $commonX < max( $vertexC->x, $vertexD->x );
          }
        }
      }
    }
    //var_dump( $isCrossing );
    //if( $isCrossing ) var_dump( "isCrossing", $vertexA->guid, $vertexB->guid, $vertexC->guid, $vertexD->guid );
    return $isCrossing;
  }  
}