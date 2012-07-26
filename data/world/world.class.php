<?php
/**
 * Class World
 *
 */

require_once( DATA."model/world_model.class.php" );

class World extends World_Model {

  // CUSTOM

  protected $territories = array();

  public function initializeTerritories() {
    $graph = new Graph();
    $graph->randomVertexGenerationDisk( 500, 150, 200 );
    $graph->randomNoncrossingEdgeGeneration( 2, 200 );
    
    $this->territories = Territory::find_in_graph( $graph );

    //$graph->drawImg();
/*
    foreach( $polygon_list as $polygon_id => $polygon ) {
      $territory = new Territory();
      $territory->polygon = $polygon;
      $territory->initializePopulation();
      $this->territories[] = $territory;
      
      $vertices = $polygon->getVertices();
      $currentVertex = array_shift( $vertices );
      $vertices[] = $currentVertex;
      foreach( $vertices as $vertex ) {
        $neighbour_array[ $currentVertex->id ][ $vertex->id ][] = $polygon_id;
        $neighbour_array[ $vertex->id ][ $currentVertex->id ][] = $polygon_id;
        $currentVertex = $vertex;
      }
    }
    
    foreach( $neighbour_array as $startVertexGuid => $array ) {
      foreach( $array as $endVertexGuid => $neighbours ) {
        if( count( $neighbours ) == 2 ) {
          $neighbourA = $this->territories[ $neighbours[0] ];
          $neighbourB = $this->territories[ $neighbours[1] ];
          $neighbourA->addNeighbour( $neighbourB );
          $neighbourB->addNeighbour( $neighbourA );
        }
      }
    }
*/
    //var_dump( $this->territories );
  }
  
  public function save( $flags = 0 ) {
    $return = parent::save( $flags );
    
    if( $return === true ) {
        foreach( $this->territories as $territory ) {
            $territory->world_id = $this->id;
            $territory->save();
        }
    }
    return $return;
  }

  // /CUSTOM

}