<?php
/**
 * Class Territory
 *
 */

require_once( DATA."model/territory_model.class.php" );

class Territory extends Territory_Model {

  // CUSTOM

  public function get_vertices() {
    $vertices = unserialize( $this->_vertices );
    if( !is_array( $vertices ) ) {
      $vertices = array();
    }

    return $vertices;
  }

  public function set_vertices( $value ) {
    $this->_vertices = serialize( $value );
  }

  public static function get_random_country_name() {
    $countries = array();
    $fh = fopen( __DIR__.'/country_names_and_code_elements_txt.txt', 'r' );
    while( $row = fgetcsv( $fh, 1000, ';' ) ) {
        if( strpos( $row[0], ' ' ) === false )
            $countries[] = $row[0];
    }
    fclose($fh);

    // header line
    unset( $countries[ 0 ] );

    return self::get_random_name( $countries );
  }

  public static function get_random_capital_name() {
    /*$simpleXML = simplexml_load_file( __DIR__.'/list_capital_cities_html.txt' );
    $capitals = array();
    foreach( $simpleXML->body->table->tr as $tr ) {
      if( isset( $tr->td[0]->a ) ) {
        $capitals[] = (string)$tr->td[0]->a;
      }
    }
    file_put_contents( __DIR__.'/list_capital_cities.txt', implode("\n", $capitals));*/

    $capitals = explode("\n", file_get_contents(__DIR__.'/list_capital_cities.txt'));

    return self::get_random_name( $capitals );
  }

  public static function get_random_name( $list ) {
    $first_idx = mt_rand( 0, count( $list ) - 1 );
    $last_idx = mt_rand( 0, count( $list ) - 1 );

    $voyels = array( 'A', 'E', 'I', 'O', 'U', 'Y' );

    // first part, up to a voyel excluded from the middle
    $first_name = strtoupper($list[ $first_idx ]);
    $first_pos = floor( strlen( $first_name ) / 2 );
    //echo 'first half : '.substr( $first_name, 0, $first_pos ).' = '.$first_name[ $first_pos ].'<br/>';
    while( isset( $first_name[ $first_pos ] ) && ! in_array( $first_name[ $first_pos ], $voyels ) ) {
        $first_pos++;
        //echo substr( $first_name, 0, $first_pos ).' = '.$first_name[ $first_pos ].'<br/>';
    }
    //echo '<hr/>';
    $first_name = substr( $first_name, 0, $first_pos );

    // last part, from a voyel included from the middle
    $last_name = strtoupper($list[ $last_idx ]);
    $last_pos = floor( strlen( $last_name ) / 2 );
    //echo 'last half : '.substr( $last_name, $last_pos ).' = '.$last_name[ $last_pos ].'<br/>';
    while( isset( $last_name[ $last_pos ] ) && !in_array( $last_name[ $last_pos ], array( 'A', 'E', 'I', 'O', 'U', 'Y' ) ) ) {
        $last_pos--;
        //echo substr( $last_name, $last_pos ).' = '.$last_name[ $last_pos ].'<br/>';
    }
    //echo '<hr/>';
    $last_name = substr( $last_name, $last_pos );

    //echo '<p><strong>'.$first_name.' '.$last_name.'</strong></p>';

    $return = ucfirst( strtolower( $first_name.$last_name ) );
    //echo '<p><strong>'.$return.'</strong></p>';

    return $return;
  }

  public function add_vertex( Vertex $vertex ) {
    $vertices = $this->vertices;
    $vertices[] = $vertex;
    $this->vertices = $vertices;
  }

  public function contains( Vertex $vertex ) {
    return in_array( $vertex, $this->vertices );
  }

  /**
   * Checks the presence of a vertex in the territory
   * Returns :
   * 0 : Vertex not in territory
   * 1 : Vertex in territory
   * 2 : Vertex is a node of the territory
   *
   * @see http://stackoverflow.com/questions/217578/vertex-in-territory-aka-hit-test
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

  public function get_perimeter() {
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
   * Calculates the area of any given non-crossing territory
   * @see http://www.mathopenref.com/coordterritoryarea2.html
   */
  public function get_area() {
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
   * Gets the coordinates of the centroid of the territory
   * @see http://stackoverflow.com/questions/2792443/finding-the-centroid-of-a-territory
   */
  public function get_centroid() {
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

  public static function find_in_graph( Graph $graph ) {
    $territoryList = array();

    $vertices = $graph->vertices;
    $edges = $graph->edges;

    foreach( $vertices as $guid => $vertex ) {
      $territoryList = array_merge( $territoryList, self::finder_recursive( $graph, new Territory(), $vertex, null ) );
    }

    return $territoryList;
  }

  static $pathTable = array();

  public static function finder_recursive( Graph $graph, $currentTerritory, $currentVertex, $lastVertex ) {
    // Interdiction path table
    // Struct : array[ vertexGuid1 ][ vertexGuid2 ]
    $territories = array();
    $return = false;
    $vertices = $graph->vertices;
    $edges = $graph->edges;

    if( is_null( $lastVertex) || !isset( self::$pathTable[ $lastVertex->guid ][ $currentVertex->guid ] ) ) {
      // The path loops = area found
      if( in_array( $currentVertex, $currentTerritory->vertices ) ) {
        // Working backward to find the closure vertex, exclude non-area included vertices
        $territory = new Territory();
        $territory->name = self::get_random_country_name();
        $territory->capital_name = self::get_random_capital_name();
        $currentTerritoryVertices = $currentTerritory->vertices;
        do {
          $newVertex = array_pop( $currentTerritoryVertices );
          $territory->add_vertex( $newVertex );
        }while( $currentVertex != $newVertex );
        $currentTerritory = clone $territory;

        // If the territory area doesn't include the central vertex
        if( $currentTerritory->includes( reset( $vertices ) ) !== 1 ) {

          // Update the interdiction table
          $vertices = $currentTerritory->vertices;
          $j = count( $vertices ) - 1;
          for( $k = 0; $k < count( $vertices ); $k++ ) {
            //self::$pathTable[ $currentTerritory[ $j ]->guid ][ $currentTerritory[ $k ]->guid ] = true;
            self::$pathTable[ $vertices[ $k ]->guid ][ $vertices[ $j ]->guid ] = true;

            $j ++;
            if( $j == count( $vertices ) ) $j = 0;
          }

          $return = $currentTerritory;
        }
      }else {
        $currentTerritory->add_vertex( $currentVertex );

        if( is_null( $lastVertex ) ) {
          // First vertex : we search every line from the vertex
          $territoryList = array();
          foreach( array_keys( $edges[ $currentVertex->guid ] ) as $guid ) {
            $territory = self::finder_recursive( $graph, new Territory(), $vertices[ $guid ], $currentVertex );

            if( $territory !== false ) $territoryList[] = $territory;

            $return = $territoryList;
          }
        }else{
          // Existing line : we follow the first available path with the smallest angle
          $angleList = array();
          foreach( array_keys( $edges[ $currentVertex->guid ] ) as $guid ) {
            // Stop condition : already passed through here in this direction
            if(
              $lastVertex->guid != $guid &&
              !isset( self::$pathTable[ $currentVertex->guid ][ $vertices[ $guid ]->guid ] )
            ) {
              $angleList[ $guid ] = Vertex::anglePolar( $lastVertex, $currentVertex, $vertices[ $guid ]);
            }
          }
          asort( $angleList );

          list( $guid, $angle ) = each( $angleList );
          if( ! is_null( $guid ) ) {
            $return = self::finder_recursive( $graph, $currentTerritory, $vertices[ $guid ], $currentVertex );
          }
        }
      }
    }

    return $return;
  }

  public static function db_remove_by_world_id($world_id) {
    $sql = "
DELETE FROM `".self::get_table_name()."`
WHERE `world_id` = ".mysql_ureal_escape_string($world_id);

    return mysql_uquery($sql);
  }

  public function get_owner( $game_id, $turn = null ) {
    $return = null;

    if( is_null( $turn ) ) {
      /* @var $game Game */
      $game = Game::instance( $game_id );

      $turn = $game->current_turn;
    }

    $territory_owner_list = $this->get_territory_owner_list( $game_id, $turn );

    if( count( $territory_owner_list ) ) {
      $territory_owner_row = array_shift( $territory_owner_list );
    }else {
      $territory_owner_row = $this->compute_territory_owner( $game_id, $turn );
    }

    $return = $territory_owner_row['owner_id'];

    return $return;
  }

  public function is_contested( $game_id, $turn = null ) {
    $return = null;

    if( is_null( $turn ) ) {
      /* @var $game Game */
      $game = Game::instance( $game_id );

      $turn = $game->current_turn;
    }

    $territory_owner_list = $this->get_territory_owner_list( $game_id, $turn );

    if( count( $territory_owner_list ) ) {
      $territory_owner_row = array_shift( $territory_owner_list );
    }else {
      $territory_owner_row = $this->compute_territory_owner( $game_id, $turn );
    }

    $return = $territory_owner_row['contested'] == 1;

    return $return;
  }

  public function is_capital( $game_id, $turn = null ) {
    $return = null;

    if( is_null( $turn ) ) {
      /* @var $game Game */
      $game = Game::instance( $game_id );

      $turn = $game->current_turn;
    }

    $territory_owner_list = $this->get_territory_owner_list( $game_id, $turn );

    if( count( $territory_owner_list ) ) {
      $territory_owner_row = array_shift( $territory_owner_list );
    }else {
      $territory_owner_row = $this->compute_territory_owner( $game_id, $turn );
    }

    $return = $territory_owner_row['capital'] == 1;

    return $return;
  }

  public function compute_territory_owner( $game_id, $turn ) {
    $player_territories = $this->get_territory_player_troops_list($game_id, $turn);

    if( $turn > 1 ) {
      $last_owner_id = $this->get_owner($game_id, $turn - 1);

      $territory_owner_list = $this->get_territory_owner_list( $game_id, $turn - 1 );
      $is_capital = $territory_owner_list[0]['capital'] == 1;
    }else {
      $last_owner_id = null;
      $is_capital = false;
    }

    $is_contested = false;
    // Default : ownership continues
    $owner_id = $last_owner_id;

    if( count( $player_territories ) == 1 ) {
      if( $last_owner_id === null ) {
        // Empty territory
        $owner_id = $player_territories[0]['player_id'];
      }else {
        $invader = Player::instance( $player_territories[0]['player_id'] );
        $player_diplomacy_list = $invader->get_last_player_diplomacy_list($game_id);

        // If invader marked the previous owner as enemy, he captures the territory
        foreach( $player_diplomacy_list as $player_diplomacy ) {
          if( $player_diplomacy['to_player_id'] == $last_owner_id && $player_diplomacy['status'] == 'Enemy' ) {
            $owner_id = $invader->id;
          }
        }
      }
    }else {
      $all_allies = true;
      $all_allies_with_owner = true;

      foreach( $player_territories as $player_territory_from ) {
        /* @var $player_from Player */
        $player_from = Player::instance( $player_territory_from['player_id'] );

        $player_diplomacy_list = $player_from->get_last_player_diplomacy_list($game_id);

        foreach( $player_territories as $player_territory_to ) {
          foreach( $player_diplomacy_list as $key => $player_diplomacy ) {
            if( $player_diplomacy['to_player_id'] == $player_territory_to['player_id'] ) {
              if( $player_diplomacy['status'] == 'Enemy' ) {
                $all_allies = false;
              }
            }
          }
        }
        foreach( $player_diplomacy_list as $key => $player_diplomacy ) {
          if( $player_diplomacy['to_player_id'] == $last_owner_id ) {
            if( $player_diplomacy['status'] == 'Enemy' ) {
              $all_allies_with_owner = false;
            }
          }
        }
      }

      if( $all_allies ) {
        if( !$all_allies_with_owner ) {
          $troops = array();

          foreach( $player_territories as $player_territory ) {
            $troops[ $player_territory['player_id'] ] = $player_territory['quantity'];
          }

          // Previous owner wiped out
          if(!array_search($last_owner_id, array_keys( $troops)) !== false) {
            // The player with most troops gets the territory
            asort( $troops );
            reset( $troops );
            list( $player_id, $troops ) = each( $troops );

            $owner_id = $player_id;
          }
        }
      }else {
        // Contested territory
        $is_contested = true;
      }
    }

    // In case of changed owner, reset the capital state
    $is_capital = $is_capital && ($last_owner_id != $owner_id);

    $this->set_territory_owner($game_id, $turn, $owner_id, $is_contested?1:0, $is_capital?1:0);

    return array('owner_id' => $owner_id, 'contested' => $is_contested, 'capital' => $is_capital);
  }

  // /CUSTOM

}