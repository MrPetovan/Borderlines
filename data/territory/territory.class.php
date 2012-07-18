<?php
/**
 * Class Territory
 *
 */

require_once( DATA."model/territory_model.class.php" );

class Territory extends Territory_Model {

  // CUSTOM
  
  protected $vertices = array();
  
  public function __construct($id = null) {
    parent::__construct($id);
    
    if( !is_null( $id ) ) {
      $vertex_id_list = $this->get_territory_vertex_list();
      foreach( $vertex_id_list as $vertex_id ) {
        $this->addVertex( Vertex::instance( $vertex_id ) );
      }
    }
  }

  public function addVertex( Vertex $vertex ) {
    $this->vertices[] = $vertex;
  }
  
  public function contains( Vertex $vertex ) {
    return in_array( $vertex, $this->vertices );
  }
  
  /**
   * Checks the presence of a vertex in the polygon
   * Returns :
   * 0 : Vertex not in polygon
   * 1 : Vertex in polygon
   * 2 : Vertex is a node of the polygon
   * 
   * @see http://stackoverflow.com/questions/217578/vertex-in-polygon-aka-hit-test
   */
  public function includes( Vertex $vertex ) {
    $return = 0;
    //Whole area check : does it include the center ?
    $includes_vertex = false;
    $origin = new Vertex(-1, -1);
    
    $aireVertexAKey = count( $this->vertices ) - 1;
    for( $aireVertexBKey = 0; $aireVertexBKey < count( $this->vertices ); $aireVertexBKey++ ) {
      if( $vertex->guid == $this->vertices[ $aireVertexBKey ]->guid ) {
        $return = 2;
        break;
      }

      if( Vertex::isCrossing( $origin, $vertex, $this->vertices[ $aireVertexAKey ], $this->vertices[ $aireVertexBKey ] ) )
        $includes_vertex = !$includes_vertex;

      $aireVertexAKey++;
      if( $aireVertexAKey == count( $this->vertices ) ) {
        $aireVertexAKey = 0;
      }
    }
    
    if( $return == 0 ) {
      $return = (int) $includes_vertex;
    }

    return $return;
  }
  
  public function getPerimeter() {
    $return = null;
    
    if( count( $this->vertices ) >= 2 ) {
      $perimeterVertices = $this->vertices;
      $perimeterVertices[] = $perimeterVertices[0];
      $perimeter = 0;
      for( $i = 0; $i < count( $this->vertices ); $i++ ) {
        $perimeter += Vertex::distance( $perimeterVertices[ $i ], $perimeterVertices[ $i + 1 ]);
      }
      
      $return = $perimeter;
    }
    
    return $return;
  }
  
  /**
   * Calculates the area of any given non-crossing polygon
   * @see http://www.mathopenref.com/coordpolygonarea2.html
   */
  public function getArea() {
    $return = null;
    if( count( $this->vertices ) >= 3 ) {
      $areaVertices = $this->vertices;
      $areaVertices[] = $areaVertices[0];
      $area = 0;
      for( $i = 0; $i < count( $this->vertices ); $i++ ) {
        $area += $areaVertices[ $i ]->x * $areaVertices[ $i + 1 ]->y;
        $area -= $areaVertices[ $i + 1 ]->x * $areaVertices[ $i ]->y;
      }
      
      $return = abs( $area / 2 );
    }
    
    return $return;
  }
  
  /**
   * Gets the coordinates of the centroid of the polygon
   * @see http://stackoverflow.com/questions/2792443/finding-the-centroid-of-a-polygon
   */
  public function getCentroid() {
    $return = null;
    
    if( count( $this->vertices ) >= 3 ) {
      $centroid = new Vertex(0,0);
      $signedArea = 0;
      $x0 = $y0 = $x1 = $y1 = 0;
      $a = 0;  // Partial signed area
      $vertices = $this->vertices;
      $vertices[] = $vertices[0];

      // For all vertices except last
      for ($i = 0; $i < count( $vertices ) - 1; $i++)
      {
          $x0 = $vertices[$i]->x;
          $y0 = $vertices[$i]->y;
          $x1 = $vertices[$i+1]->x;
          $y1 = $vertices[$i+1]->y;
          $a = $x0 * $y1 - $x1 * $y0;
          $signedArea += $a;
          $centroid->x += ($x0 + $x1) * $a;
          $centroid->y += ($y0 + $y1) * $a;
      }

      $signedArea *= 0.5;
      $centroid->x /= (6 * $signedArea);
      $centroid->y /= (6 * $signedArea);

      $return = $centroid;
    }
    return $return;
  }
  
  public static function find_polygons_in_graph( Graph $graph ) {
    $polygonList = array();

    $vertices = $graph->vertices;
    $edges = $graph->edges;
    
    foreach( $vertices as $guid => $vertex ) {      
      $polygonList = array_merge( $polygonList, self::polygon_finder_recursive( $graph, array(), $vertex, null, false ) );
    }
    
    return $polygonList;
  }
  
  public static function polygon_finder_recursive( Graph $graph, $currentPolygon, $currentVertex, $lastVertex ) {    
    // Interdiction path table
    // Struct : array[ vertexGuid1 ][ vertexGuid2 ]
    static $pathTable = array();
    $polygons = array();
    $return = false;
    $vertices = $graph->vertices;
    $edges = $graph->edges;
    
    if( is_null( $lastVertex) || !isset( $pathTable[ $lastVertex->guid ][ $currentVertex->guid ] ) ) {
      // The path loops = area found
      if( in_array( $currentVertex, $currentPolygon ) ) {

        // Working backward to find the closure vertex, exclude non-area included vertices
        $polygon = new Polygon;
        do {
          $newVertex = array_pop( $currentPolygon );
          $polygon->addVertex( $newVertex );
        }while( $currentVertex != $newVertex );
        $currentPolygon = $polygon;

        // If the polygon area doesn't include the central vertex
        if( $polygon->includes( reset( $vertices ) ) !== 1 ) {
        
          // Update the interdiction table
          $j = count( $currentPolygon ) - 1;
          for( $k = 0; $k < count( $currentPolygon ); $k++ ) {
            //$pathTable[ $currentPolygon[ $j ]->guid ][ $currentPolygon[ $k ]->guid ] = true;
            $pathTable[ $currentPolygon[ $k ]->guid ][ $currentPolygon[ $j ]->guid ] = true;

            $j ++;
            if( $j == count( $currentPolygon ) ) $j = 0;
          }

          $return = $currentPolygon;
        }
      }else {
        $currentPolygon[] = $currentVertex;

        if( is_null( $lastVertex ) ) {
          // First vertex : we search every line from the vertex
          $polygonList = array();
          foreach( array_keys( $edges[ $currentVertex->guid ] ) as $guid ) {
            $polygon = self::polygon_finder_recursive( $graph, $currentPolygon, $vertices[ $guid ], $currentVertex );
            
            if( $polygon !== false ) $polygonList[] = $polygon;

            $return = $polygonList;
          }
        }else{        
          // Existing line : we follow the first available path with the smallest angle      
          $angleList = array();
          foreach( array_keys( $edges[ $currentVertex->guid ] ) as $guid ) {
            // Stop condition : already passed through here in this direction
            if(
              $lastVertex->guid != $guid &&
              !isset( $pathTable[ $currentVertex->guid ][ $vertices[ $guid ]->guid ] )
            ) {
              $angleList[ $guid ] = Vertex::anglePolar( $lastVertex, $currentVertex, $vertices[ $guid ]);
            }
          }
          asort( $angleList );

          list( $guid, $angle ) = each( $angleList );
          if( ! is_null( $guid ) ) {
            $return = self::polygon_finder_recursive( $graph, $currentPolygon, $vertices[ $guid ], $currentVertex );
          }
        }
      }
    }

    return $return;
  }

  // /CUSTOM

}