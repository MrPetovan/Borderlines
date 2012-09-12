<?php
// ver 3

class Graph {
  // List of every vertex in the graph, indexed by vertex guid
  public $vertices = array();
  // Directed two-dimensional array with weight : $edges[ Start vertex guid ][ End vertex guid ] = weight
  public $edges = array();
  // Two-dimensional array of every distance between vertices, computed during vertex generation
  public $distances = array();

  public function add_vertex( Vertex $vertex, $minDist = null, $maxDist = null ) {
    $vertexCreationFlag = count( $this->vertices ) == 0;
    $distanceTemp = array();

    foreach( $this->vertices as $vertexItem ) {
      $distance = Vertex::distance( $vertex, $vertexItem );

      $distanceTemp[ $vertexItem->guid ] = $distance;

      if( !is_null( $minDist ) && $distance < $minDist ) {
        $vertexCreationFlag = false;

        break;
      }

      if( !is_null( $maxDist ) ) {
        $vertexCreationFlag = $vertexCreationFlag || ($distance < $maxDist);
      }else {
        $vertexCreationFlag = true;
      }
    }

    if( $vertexCreationFlag ) {
      foreach( $distanceTemp as $guid => $distance ) {
        $this->distances[ $vertex->guid ][ $guid ] = $distance;
        $this->distances[ $guid ][ $vertex->guid ] = $distance;
      }

      $this->vertices[ $vertex->guid ] = $vertex;
    }

    return $vertexCreationFlag;
  }

  public function addEdgeGuid( $vertexStartGuid, $vertexEndGuid, $isDirected = false ) {
    $vertexStart = $this->vertices[ $vertexStartGuid ];
    $vertexEnd = $this->vertices[ $vertexEndGuid ];

    return $this->addEdge( $vertexStart, $vertexEnd, $isDirected );
  }

  public function addEdge( Vertex $vertexStart, Vertex $vertexEnd, $isDirected = false ) {
    $return = false;
    // TODO : Check existence
    if( isset( $this->vertices[ $vertexStart->guid ] ) && isset( $this->vertices[ $vertexEnd->guid ] )) {
      $distance = $this->distances[ $vertexStart->guid ][ $vertexEnd->guid ];

      $this->edges[ $vertexStart->guid ][ $vertexEnd->guid ] = $distance;
      if( ! $isDirected ) {
        $this->edges[ $vertexEnd->guid ][ $vertexStart->guid ] = $distance;
      }
      $return = true;
    }
    return $return;
  }

  public function dropEdges() {
    $this->edges = array();
  }

  public function randomVertexGenerationSimple( $sizeX = 500, $sizeY = 500, $numVertices = 10 ) {
    for( ;$numVertices != 0; $numVertices--) {
      $this->add_vertex( new Vertex( rand(0, $sizeX - 1), rand(0, $sizeY - 1) ) );
    }
  }

  public function randomVertexGenerationDisk( $sizeX, $minDist, $maxDist ) {
    $sizeY = $sizeX;
    // Initial vertex generation
    $this->add_vertex( new Vertex( round( $sizeX / 2 ), round( $sizeY / 2 ), '⊗' ) );

    $initialVertex = reset( $this->vertices );

    $i = 0;

    // Vertex creation procedure
    $maxMissedVertices = 1000;
    $missedVertexCount = 0;
    while( $missedVertexCount < $maxMissedVertices ) {
      $x = rand(0, $sizeX - 1);
      $y = rand(0, $sizeY - 1);

      $vertex = new Vertex( $x, $y, num2alpha( $i ) );

      $vertexCreationFlag = false;

      if( Vertex::distance( $vertex, $initialVertex ) <= ($sizeX / 2) ) {
        $vertexCreationFlag = $this->add_vertex( $vertex, $minDist, $maxDist );
      }

      if( $vertexCreationFlag ) {
        $missedVertexCount = 0;
        $i++;
      }else {
        $missedVertexCount++;
      }
    }
  }

  public function randomNoncrossingEdgeGeneration( $relationNumber = 2, $maxDist = null, $isDirected = false ) {
    $edgeNumber = 0;
    foreach( $this->distances as $guid => $distanceList ) {
      asort( $distanceList );
      $edgeByVertexNumber = 0;
      while( (list( $vertexGuid, $distance ) = each( $distanceList ) ) && $edgeByVertexNumber < $relationNumber ) {
        $edgeDrawingFlag = true;

        // Existence check
        if( isset( $this->edges[$guid][$vertexGuid] ) )
          $edgeDrawingFlag = false;

        // Distance check
        if( $edgeDrawingFlag && !is_null( $maxDist ) && $distance > $maxDist * 1.9 )
          $edgeDrawingFlag = false;

        // Same vertex, different orientation check
        if( $edgeDrawingFlag && !isset( $this->edges[$vertexGuid][$guid] ) ) {
          // Crossing check
          foreach( $this->edges as $guidA => $lines ) {
            foreach( $lines as $guidB => $dummy ) {
              if( Vertex::isCrossing( $this->vertices[ $guid ], $this->vertices[ $vertexGuid ], $this->vertices[ $guidA ], $this->vertices[ $guidB ] ) ) {
                continue 3; // continue while loop
              }
            }
          }
        }

        // Adding new edge
        if( $edgeDrawingFlag ) {
          $this->addEdgeGuid( $guid, $vertexGuid, $isDirected );
          $edgeByVertexNumber++;
          $edgeNumber++;
        }

      }
    }
    return $edgeNumber;
  }

  public function draw($sizeX = 500, $sizeY = 500, $isDirected = false) {

    $font = __DIR__.'/arial.ttf';

    $img = imagecreatetruecolor($sizeX,$sizeY);
    imagesavealpha($img, true);

    // Fill the image with transparent color
    $color = imagecolorallocatealpha($img,0x00,0x00,0x00,127);

    $white = imagecolorallocate($img, 255, 255, 255);
    $red = imagecolorallocate($img, 255, 0, 0);
    $green = imagecolorallocate($img, 0, 255, 0);
    $blue = imagecolorallocate($img, 0, 0, 255);
    $black = imagecolorallocatealpha($img, 0, 0, 0, 0);
    $grey = imagecolorallocate($img, 211, 211, 211);

    $sand = imagecolorallocate($img, 255, 200, 0);
    $earth = imagecolorallocate($img, 50, 21, 12);
    $earth = imagecolorallocate($img, 101, 159, 0);

    $marine = imagecolorallocate($img, 0, 100, 255);
    $sea = imagecolorallocate($img, 0, 161, 185);

    imagefill($img, 0, 0, $grey);

    $divisor = 100;

    for($iw=1; $iw < $sizeX / $divisor; $iw++){imageline($img, $iw * $divisor, 0, $iw * $divisor, $sizeX, $white);}
    for($ih=1; $ih < $sizeY / $divisor; $ih++){imageline($img, 0, $ih * $divisor, $sizeX, $ih * $divisor, $white);}

    foreach( $this->vertices as $vertex ) {
      imagerectangle($img, $vertex->x + 1, $sizeY - ($vertex->y + 1), $vertex->x - 1, $sizeY - ($vertex->y - 1), $black);
      // Ajout d'ombres au texte
      //imagettftext($img, 15, 0, $vertex->x + 3, $sizeY - ($vertex->y + 3), $grey, $font, $vertex);

      // Ajout du texte
      //imagettftext($img, 15, 0, $vertex->x + 2, $sizeY - ($vertex->y + 2), $black, $font, $vertex);
    }
    $alength = 15;
    $awidth = 3;
    foreach( $this->edges as $vertexStartGuid => $edges ) {
      foreach( $edges as $vertexEndGuid => $distance ) {
        if( $isDirected ) {
          $dx = $this->vertices[$vertexEndGuid]->x + ($this->vertices[$vertexStartGuid]->x - $this->vertices[$vertexEndGuid]->x) * $alength / $distance;
          $dy = $this->vertices[$vertexEndGuid]->y + ($this->vertices[$vertexStartGuid]->y - $this->vertices[$vertexEndGuid]->y) * $alength / $distance;

          $k = $awidth / $alength;

          $x2o = $this->vertices[$vertexEndGuid]->x - $dx;
          $y2o = $dy - $this->vertices[$vertexEndGuid]->y;

          $x3 = $y2o * $k + $dx;
          $y3 = $x2o * $k + $dy;

          $x4 = $dx - $y2o * $k;
          $y4 = $dy - $x2o * $k;

          imageline($img, $this->vertices[$vertexStartGuid]->x, $sizeY - $this->vertices[$vertexStartGuid]->y, $dx, $sizeY - $dy, $marine);
          imagefilledpolygon($img, array($this->vertices[$vertexEndGuid]->x, $sizeY - $this->vertices[$vertexEndGuid]->y, $x3, $sizeY - $y3, $x4, $sizeY - $y4), 3, $marine);
        }else {
          imageline(
            $img,
            $this->vertices[$vertexStartGuid]->x,
            $sizeY - $this->vertices[$vertexStartGuid]->y,
            $this->vertices[$vertexEndGuid]->x,
            $sizeY - $this->vertices[$vertexEndGuid]->y,
            $marine
          );
        }
      }
    }

    foreach( $this->vertices as $vertex ) {
      // Ajout d'ombres au texte
      imagettftext($img, 15, 0, $vertex->x + 3, $sizeY - ($vertex->y + 3), $grey, $font, $vertex);
      // Ajout du texte
      imagettftext($img, 15, 0, $vertex->x + 2, $sizeY - ($vertex->y + 2), $black, $font, $vertex);
    }

    return $img;
  }

  public function drawImg( $sizeX = 500, $sizeY = 500, $isDirected = false ) {
    $image = $this->draw( $sizeX, $sizeY, $isDirected );
    ob_start();
    imagepng($image);
    $imagevariable = ob_get_clean();
    echo '<img src="data:image/png;base64,'.base64_encode( $imagevariable ).'"/>';
  }
}