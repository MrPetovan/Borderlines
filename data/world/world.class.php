<?php
/**
 * Class World
 *
 */

require_once( DATA."model/world_model.class.php" );

class World extends World_Model {

  // CUSTOM

  protected $territories = null;

  public function get_territories() {
    if( is_null( $this->territories )) {
      $this->territories = Territory::db_get_by_world_id($this->id);
    }
    return $this->territories;
  }

  public function initialize_territories() {
    $return = true;

    if( is_null( $this->id ) ) {
      $return = $this->save();
    }

    if( $return ) {
      Territory::db_remove_by_world_id( $this->id );

      $graph = new Graph();
      $graph->randomVertexGenerationDisk( $this->size_x, 100, 120 );
      $graph->randomNoncrossingEdgeGeneration( 2, 200 );

      $this->territories = Territory::find_in_graph( $graph );

      foreach( $this->territories as $territory ) {
        $territory->world_id = $this->id;
        $territory->save();
      }

      $neighbour_array = array();
      foreach( $this->territories as $territory ) {
        $vertices = $territory->vertices;
        $currentVertex = array_shift( $vertices );
        $vertices[] = $currentVertex;
        foreach( $vertices as $vertex ) {
          $neighbour_array[ $currentVertex->guid ][ $vertex->guid ][] = $territory;
          $neighbour_array[ $vertex->guid ][ $currentVertex->guid ][] = $territory;
          $currentVertex = $vertex;
        }
      }

      foreach( $neighbour_array as $startVertexGuid => $array ) {
        foreach( $array as $endVertexGuid => $neighbours ) {
          if( count( $neighbours ) == 2 ) {

            $neighbours[0]->set_territory_neighbour( $neighbours[1]->id );
            $neighbours[1]->set_territory_neighbour( $neighbours[0]->id );
          }
        }
      }

      //var_dump( $this->territories );
    }
    return $return;
  }

  public function save( $flags = 0 ) {
    $return = parent::save( $flags );

    /*if( $return === true ) {
        foreach( $this->territories as $territory ) {
            $territory->world_id = $this->id;
            $territory->save();
        }
    }*/
    return $return;
  }

  public function draw() {

    $font = DIR_ROOT.'/style/arial.ttf';

    $img = imagecreatetruecolor($this->size_x,$this->size_y);
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

    for($iw=1; $iw < $this->size_x / $divisor; $iw++){imageline($img, $iw * $divisor, 0, $iw * $divisor, $this->size_x, $white);}
    for($ih=1; $ih < $this->size_y / $divisor; $ih++){imageline($img, 0, $ih * $divisor, $this->size_x, $ih * $divisor, $white);}

    foreach( $this->territories as $key => $area ) {
      $polygon = array();
      foreach( $area->vertices as $pointAire ) {
        $polygon[] = $pointAire->x;
        $polygon[] = $this->size_y - $pointAire->y;
      }
      imagefilledpolygon( $img, $polygon, count( $area->vertices ), $earth );
    }
    foreach( $this->territories as $key => $area ) {
      $lastPoint = $area->vertices[ count( $area ) - 1 ];
      foreach( $area->vertices as $point ) {
        imageline($img, $lastPoint->x, $this->size_y - $lastPoint->y, $point->x, $this->size_y - $point->y, $sand);

        $lastPoint = $point;
      }
    }
    foreach( $this->territories as $key => $area ) {
      $centroid = $area->get_centroid();

      $name = $area->name;
      $bbox = imagettfbbox( 10, 0, $font, $name );
      //var_dump( $bbox );
      $textwidth = $bbox[2] - $bbox[0];

      // Ajout d'ombres au texte
      imagettftext($img, 10, 0, ($centroid->x - $textwidth / 2) + 1, $this->size_y - ($centroid->y + 1), $grey, $font, $name);

      // Ajout du texte
      imagettftext($img, 10, 0, ($centroid->x - $textwidth / 2), $this->size_y - $centroid->y, $black, $font, $name);
      /*foreach( $area->vertices as $vertex ) {
        // Ajout d'ombres au texte
        imagettftext($img, 15, 0, $vertex->x + 3, $this->size_y - ($vertex->y + 3), $grey, $font, $vertex);
        // Ajout du texte
        imagettftext($img, 15, 0, $vertex->x + 2, $this->size_y - ($vertex->y + 2), $black, $font, $vertex);
      }*/
    }

    return $img;
  }

  public function drawImg( $with_map = false ) {
    $image = $this->draw( );
    ob_start();
    imagepng($image);
    $imagevariable = ob_get_clean();
    if( $with_map ) {
      echo '
      <map name="world">';
      foreach( $this->territories as $territory ) {
        $coords = array();
        foreach( $territory->vertices as $vertex ) {
          $coords[] = $vertex->x .','. ($this->size_y - $vertex->y);
        }
        echo '
        <area shape="polygon" coords="'.implode(',', $coords).'" href="'.Page::get_url('show_territory', array('id' => $territory->id)).'" title="'.$territory->name.'">';
      }
      echo '
      </map>
      <img usemap="#world" src="data:image/png;base64,'.base64_encode( $imagevariable ).'">';
    }else {
      echo '<img src="data:image/png;base64,'.base64_encode( $imagevariable ).'"/>';
    }
  }

  // /CUSTOM

}