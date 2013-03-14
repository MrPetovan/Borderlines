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
    $countries = array_reverse($countries);
    array_pop( $countries );
    $countries = array_reverse($countries);

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
   *
   * @return float
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
   *
   * @return Vertex
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

  public function get_shortest_paths_to( array $territories, array $neighbours ) {
    $seenTerritories = array();
    $distances[ $this->id ] = 0;
    $previousTerritory = array();

    // Dijkstra <3
    $i = 0;
    while( count( $seenTerritories ) != count( $territories ) && $i++ < count( $territories ) ) {
      $currentTerritory = $territories[ get_key_for_minimum_value( $distances, $seenTerritories ) ];
      $seenTerritories[ $currentTerritory->id ] = $currentTerritory;

      if( isset( $neighbours[ $currentTerritory->id ] ) ) {
        foreach( $neighbours[ $currentTerritory->id ] as $destinationTerritoryId => $weight ) {
          if(
            !isset( $distances[ $destinationTerritoryId ] )
            || $distances[ $currentTerritory->id ] + $weight < $distances[ $destinationTerritoryId ]
          ) {
            $distances[ $destinationTerritoryId ] = $distances[ $currentTerritory->id ] + $weight;
            $previousTerritory[ $destinationTerritoryId ] = $currentTerritory->id;
          }
        }
      }
    }
    //var_debug( $this, $territories, $neighbours );die();

    return $previousTerritory;
  }

  public static function db_remove_by_world_id($world_id) {
    $sql = "
DELETE FROM `".self::get_table_name()."`
WHERE `world_id` = ".mysql_ureal_escape_string($world_id);

    return mysql_uquery($sql);
  }

  public function get_neighbour( $guid1, $guid2 ) {
    $sql = '
SELECT `neighbour_id` AS `id`
FROM `territory_neighbour`
WHERE `territory_id` = '.$this->id.'
AND (`guid1` = "'.$guid1.'" AND `guid2` = "'.$guid2.'"
OR `guid1` = "'.$guid2.'" AND `guid2` = "'.$guid1.'")';
    return self::sql_to_object($sql);
  }

  public function get_economy_ratio( Game $game, $turn = null ) {
    $return = null;

    $where = '';
    if( !is_null( $turn ) ) {
      $where = '
AND `turn` <= '.mysql_ureal_escape_string( $turn );
    }
    $sql = '
SELECT IFNULL( SUM( `delta` ), 0 )
FROM `territory_economy_history`
WHERE `territory_id` = '.mysql_ureal_escape_string($this->id).'
AND `game_id` = '.mysql_ureal_escape_string($game->id).$where;
    $res = mysql_uquery( $sql );
    $row = mysql_fetch_row( $res );
    if( $row ) {
      $return = array_shift( $row );
    }

    return $return;
  }

  public function get_owner( Game $game, $turn = null ) {
    $return = null;

    if( is_null( $turn ) ) {
      $turn = $game->current_turn;
    }

    $territory_status_list = $this->get_territory_status_list( $game->id, $turn );

    if( count( $territory_status_list ) ) {
      $territory_status_row = array_shift( $territory_status_list );
    }else {
      $territory_status_row = $this->compute_territory_status( $game, $turn );
    }

    $return = $territory_status_row['owner_id'];

    return Player::instance( $return );
  }

  public function is_contested( Game $game, $turn = null ) {
    $return = null;

    if( is_null( $turn ) ) {
      $turn = $game->current_turn;
    }

    $territory_status_list = $this->get_territory_status_list( $game->id, $turn );

    if( count( $territory_status_list ) ) {
      $territory_status_row = array_shift( $territory_status_list );
    }else {
      $territory_status_row = $this->compute_territory_status( $game, $turn );
    }

    $return = $territory_status_row['contested'] == 1;

    return $return;
  }

  public function is_capital( Game $game, $turn = null ) {
    $return = null;

    if( is_null( $turn ) ) {
      $turn = $game->current_turn;
    }

    $territory_status_list = $this->get_territory_status_list( $game->id, $turn );

    if( count( $territory_status_list ) ) {
      $territory_status_row = array_shift( $territory_status_list );
    }else {
      $territory_status_row = $this->compute_territory_status( $game, $turn );
    }

    $return = $territory_status_row['capital'] == 1;

    return $return;
  }

  public function resolve_combat( Game $game, $turn = null ) {
    //var_debug( "{$this->name}->resolve_combat( {$game->name}, $turn ) ");
    $return = null;

    if( is_null( $turn ) ) {
      $turn = $game->current_turn;
    }

    $game_parameters = $game->get_parameters();

    // Diplomacy checking and parties forming
    $diplomacy = array();
    $attacks = array();
    $losses = array();

    $player_troops = $game->get_territory_player_troops_list($turn, $this->id);
    foreach( $player_troops as $key => $attacker_row ) {
      /* @var $player Player */
      $player = Player::instance($attacker_row['player_id']);
      $last_diplomacy = $player->get_last_player_diplomacy( $game, $turn );

      foreach( $last_diplomacy as $diplomacy_row ) {
        $diplomacy[ $diplomacy_row['from_player_id'] ][ $diplomacy_row['to_player_id'] ] = $diplomacy_row['status'];
      }
    }
    foreach( $player_troops as $key => $attacker_row ) {
      $game->set_player_history(
        $attacker_row['player_id'],
        $turn,
        time(),
        "There's a battle for the control of this territory",
        $this->id
      );

      // Building the attacks directions
      foreach( $player_troops as $defender_row ) {
        if( $attacker_row['player_id'] != $defender_row['player_id'] &&
          $diplomacy[ $attacker_row['player_id'] ][ $defender_row['player_id'] ] == 'Enemy' ) {
          $attacks[ $attacker_row['player_id'] ][] = $defender_row['player_id'];
        }
      }
    }
    foreach( $player_troops as $key => $attacker_row ) {
      // Battle
      $attacker_efficiency = 1 / $game_parameters['TROOPS_EFFICACITY'];
      $attacker_damages = round($attacker_efficiency * $attacker_row['quantity']);

      // Damages spread between the opposing forces
      foreach( $attacks[ $attacker_row['player_id'] ] as $defender_player_id ) {
        if( !isset( $losses[ $defender_player_id ][ $attacker_row['player_id'] ] ) ) {
          $losses[ $defender_player_id ][ $attacker_row['player_id'] ] = 0;
        }
        // Backstabbing (defender consider attacker as an ally)
        if( $diplomacy[ $defender_player_id ][ $attacker_row['player_id'] ] == 'Ally' ) {
          $attack_mul = 2;
        }else {
          $attack_mul = 1;
        }

        $losses[ $defender_player_id ][ $attacker_row['player_id'] ] =
          round($attacker_damages * $attack_mul / count( $attacks[ $attacker_row['player_id'] ] ) );
      }
    }

    // Cleaning up
    foreach( $player_troops as $key => $player_row ) {

      $total_damages = array_sum( $losses[ $player_row['player_id'] ] );
      $total_losses = min( $player_row['quantity'], $total_damages );
      $ratio = 1;
      if( $total_damages > $total_losses ) {
        $ratio = $total_losses / $total_damages;
      }

      foreach( $losses[ $player_row['player_id'] ] as $attacker_player_id => $damages ) {
        $player = Player::instance($attacker_player_id);
        if( $diplomacy[ $player_row['player_id'] ][ $attacker_player_id ] == 'Ally' ) {
          $game->set_player_history(
            $player_row['player_id'],
            $turn,
            time(),
            $player->name . "'s troops backstabbed yours !",
            $this->id
          );
        }
        $game->set_territory_player_troops_history($turn, $player_row['territory_id'], $player_row['player_id'], round( - $damages * $ratio ), 'Combat', $player->id);
      }

      if( $total_losses == $player_row['quantity'] ) {
        $game->set_player_history(
          $player_row['player_id'],
          $turn,
          time(),
          "All of your ".$player_row['quantity']." troops have been killed",
          $this->id
        );
      }
    }
  }
  public function get_territory_status( Game $game, $turn = null ) {
    //var_debug( "{$this->name}->get_territory_status( {$game->name}, $turn ) ");
    $return = null;

    if( is_null( $turn ) ) {
      $turn = $game->current_turn;
    }

    $territory_status_list = $this->get_territory_status_list( $game->id, $turn );
    if( count( $territory_status_list ) ) {
      $return = array_pop( $territory_status_list );
    }else {
      $return = $this->compute_territory_status( $game, $turn );
    }

    return $return;
  }

  public function compute_territory_status( Game $game, $turn = null ) {
    //var_debug( "{$this->name}->compute_territory_status( {$game->name}, $turn ) ");
    if( is_null( $turn ) ) {
      $turn = $game->current_turn;
    }

    $game_parameters = $game->get_parameters();
    $last_owner = Player::instance();
    $is_contested = false;
    $is_conflict = false;
    $is_capital = false;
    $economy_ratio = 0;

    $troops_disturbing = 0;

    $player_capturing_id = null;
    $player_capturing_troops = null;

    $player_supremacy_troops = array();

    $territory_player_troops_list = $game->get_territory_player_troops_list($turn, $this->id);

    if( $turn > 0 ) {
      $last_owner_check = $this->get_owner( $game, $turn - 1 );

      // Left player check
      $game_player_row = array_pop( $game->get_game_player_list($last_owner_check->id) );
      if( $game_player_row['turn_leave'] == null ) {
        $last_owner = $last_owner_check;
      }
      unset( $last_owner_check );

      $territory_status_list = $this->get_territory_status_list( $game->id, $turn - 1 );
      $is_capital = $territory_status_list[0]['capital'] == 1;
    }

    //var_debug( "Last owner : {$last_owner->name}", $territory_player_troops_list);

    // Default : ownership continues
    //$new_owner = $last_owner;
    $new_owner_id = $last_owner->id;

    if( count( $territory_player_troops_list ) == 1 ) {
      if( $last_owner->id === null ) {
        // Empty territory
        $player_capturing_id = $territory_player_troops_list[0]['player_id'];
        $player_capturing_troops = $territory_player_troops_list[0]['quantity'];
      }else {
        $invader = Player::instance( $territory_player_troops_list[0]['player_id'] );
        $player_diplomacy_list = $invader->get_last_player_diplomacy_list($game->id);

        // If invader marked the previous owner as enemy, he captures the territory
        foreach( $player_diplomacy_list as $player_diplomacy ) {
          if( $player_diplomacy['to_player_id'] == $last_owner->id && $player_diplomacy['status'] == 'Enemy' ) {
            $player_capturing_id = $invader->id;
            $player_capturing_troops = $territory_player_troops_list[0]['quantity'];
            $troops_disturbing = $territory_player_troops_list[0]['quantity'];
          }
        }
      }

      $player_supremacy_troops[ $territory_player_troops_list[0]['player_id'] ] = $territory_player_troops_list[0]['quantity'];
    }else {
      $all_allies = true;
      $all_allies_with_owner = true;

      foreach( $territory_player_troops_list as $player_territory_from ) {
        /* @var $player_from Player */
        $player_from = Player::instance( $player_territory_from['player_id'] );

        $troops[ $player_territory_from['player_id'] ] = $player_territory_from['quantity'];
        $player_supremacy_troops[ $player_territory_from['player_id'] ] = $player_territory_from['quantity'];

        $player_diplomacy_list = $player_from->get_last_player_diplomacy_list($game->id);

        foreach( $territory_player_troops_list as $player_territory_to ) {
          foreach( $player_diplomacy_list as $key => $player_diplomacy ) {
            if( $player_diplomacy['to_player_id'] == $player_territory_to['player_id'] ) {
              if( $player_diplomacy['status'] == 'Enemy' ) {
                $player_supremacy_troops[ $player_territory_from['player_id'] ] -= $player_territory_to['quantity'];
                $all_allies = false;
              }
              if( $player_diplomacy['status'] == 'Ally' ) {
                $player_supremacy_troops[ $player_territory_from['player_id'] ] += $player_territory_to['quantity'];
              }
            }
          }
        }
        foreach( $player_diplomacy_list as $key => $player_diplomacy ) {
          if( $player_diplomacy['to_player_id'] == $last_owner->id ) {
            if( $player_diplomacy['status'] == 'Enemy' ) {
              $all_allies_with_owner = false;
              if( isset( $troops[ $player_diplomacy['from_player_id'] ] ) ) {
                $troops_disturbing += $troops[ $player_diplomacy['from_player_id'] ];
              }
            }
          }
        }
      }

      if( $all_allies ) {
        if($last_owner->id === null || !$all_allies_with_owner ) {
          $troops = array();

          foreach( $territory_player_troops_list as $player_territory ) {
            $troops[ $player_territory['player_id'] ] = $player_territory['quantity'];

            $this->set_territory_player_status(
              $game->id,
              $turn,
              $player_territory['player_id'],
              1
            );
          }

          // Previous owner wiped out or empty territory
          if(array_search($last_owner->id, array_keys( $troops)) === false) {
            // The player with most troops gets the territory
            arsort( $troops );
            reset( $troops );
            list( $player_id, $troops ) = each( $troops );

            $player_capturing_id = $player_id;
            $player_capturing_troops = $troops;
          }
        }
      }else {
        // Contested territory
        $is_conflict = true;
      }
    }

    // Supremacy
    foreach( $player_supremacy_troops as $player_id => $supremacy_score ) {
      $this->set_territory_player_status(
        $game->id,
        $turn,
        $player_id,
        $supremacy_score >= 0?1:0
      );
    }

    $area = $this->get_area();

    // Capital status
    if( !$this->is_capturable() ) {
      $new_owner_id = null;
      $is_capital = false;
    }else {
      // Capture try
      if( $player_capturing_troops > 0 ) {
        if( $player_capturing_troops * $game_parameters['TROOPS_CAPTURE_POWER'] >= $area ) {
          $troops_disturbing = 0;
          $new_owner_id = $player_capturing_id;
        }else{
          $is_contested = true;
        }
      }

      // In case of changed owner, reset the capital state
      $is_capital = $is_capital && ($last_owner->id == $new_owner_id);
    }

    // Revenue suppression
    $revenue_suppression = min( 1, round( ($troops_disturbing * $game_parameters['TROOPS_CAPTURE_POWER']) / $area, 2) );

    // Status update
    $this->set_territory_status($game->id, $turn, $new_owner_id, $is_contested?1:0, $is_conflict?1:0, $is_capital?1:0, $revenue_suppression);

    $return = array('owner_id' => $new_owner_id, 'contested' => $is_contested, 'conflict' => $is_conflict, 'capital' => $is_capital, 'revenue_suppression' => $revenue_suppression);

    return $return;
  }

  public function get_distance_to_capital( Game $game, $turn = null ) {
    if( is_null( $turn ) ) {
      $turn = $game->current_turn;
    }
    $return = null;
    $territory_status = $this->get_owner( $game, $turn );
    if( $territory_status->id !== null ) {
      $capital = $territory_status->get_capital( $game, $turn );
      $distance = null;
      if( $capital->id !== null ) {
        $return = Vertex::distance( $capital->get_centroid(), $this->get_centroid() );
      }
    }
    return $return;
  }

  public function get_corruption_ratio( Game $game, $turn = null ) {
    if( is_null( $turn ) ) {
      $turn = $game->current_turn;
    }
    $distance_to_capital = $this->get_distance_to_capital( $game, $turn );

    $return = $this->get_corruption_ratio_from_distance($distance_to_capital);

    return $return;
  }

  public function get_corruption_ratio_from_distance( $distance_to_capital ) {
    $return = null;
    if( $distance_to_capital !== null ) {
      $return = $distance_to_capital / 10000;
    }else {
      $return = .5;
    }
    return $return;
  }

  public static function get_by_world(World $world, Game $game = null, $turn = null, $sort_field = null, $sort_direction = null) {
    $return = null;

    if( $game === null || $game->id === null) {
      $return = $world->territories;
    }else {
      if( $turn === null ) $turn = $game->current_turn;

      $order_by = '';
      switch( $sort_field ) {
        case 'name': $order_by = 't.`name`'; break;
        case 'owner': $order_by = 'ISNULL(t_o.`owner_id`), t_o.`owner_id`'; break;
      }
      if( $order_by != '' ) {
        $order_by = '
ORDER BY '.$order_by;
        if( $sort_direction !== null && !$sort_direction ) {
          $order_by .= ' DESC';
        }
      }

      $sql = '
SELECT `id`
FROM `'.self::get_table_name().'` t
JOIN `territory_status` t_o ON
  t_o.`territory_id` = t.`id`
  AND t_o.`game_id` = '.$game->id.'
  AND `turn` = '.$turn.'
WHERE `world_id` = '.$world->id.$order_by;
      $return = self::sql_to_list($sql);
    }

    return $return;
  }

  // /CUSTOM

}