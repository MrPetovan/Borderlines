<?php
/**
 * Class World
 *
 */

require_once( DATA."model/world_model.class.php" );

class World extends World_Model {

  // CUSTOM

  const TILE_DIR = 'cache/world/';

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

      foreach (glob(DIR_ROOT . self::TILE_DIR . '/tile_'.$this->id.'*') as $removeFile) {
        unlink($removeFile);
      }

      $graph = new Graph();
      $graph->randomVertexGenerationDisk( $this->size_x, 100, 110 );
      $graph->randomNoncrossingEdgeGeneration( 2, 150 );

      $this->territories = Territory::find_in_graph( $graph );

      foreach( $this->get_territories() as $territory ) {
        $territory->world_id = $this->id;
        $territory->save();
      }

      $neighbour_array = array();
      foreach( $this->get_territories() as $territory ) {
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

  public function draw( $options = array() ) {
    $defaults = array(
        'territories' => null,
        'game_id' => null,
        'turn' => null,
        'size_x' => $this->size_x,
        'size_y' => $this->size_y,
        'offset_x' => 0,
        'offset_y' => 0,
        'ratio' => 1
    );

    foreach( $defaults as $key => $value ) {
      if( !isset($options[$key] ) ) $options[ $key ] = $value;
    }

    extract( $options );

    if( $territories === null ) {
      $territories = $this->get_territories();
    }else {
      $size_x = 0;
      $size_y = 0;
      $offset_x = $this->size_x;
      $offset_y = $this->size_y;
      foreach( $territories as $key => $area ) {
        foreach( $area->vertices as $pointAire ) {
          $size_x = max( $size_x, $pointAire->x );
          $size_y = max( $size_y, $pointAire->y );
          $offset_x = min( $offset_x, $pointAire->x );
          $offset_y = min( $offset_y, $pointAire->y );
        }
      }
      $offset_x -= 10;
      $offset_y -= 10;
      $size_x = $size_x + 10;
      $size_y = $size_y + 10;
    }

    $font = DIR_ROOT.'/style/arial.ttf';

    $img = imagecreatetruecolor(($size_x - $offset_x) * $ratio, ($size_y - $offset_y) * $ratio);
    imagesavealpha($img, true);

    // Fill the image with transparent color
    $transparent = imagecolorallocatealpha($img,0x00,0x00,0x00,127);
    $veil = imagecolorallocatealpha($img,0x00,0x00,0x00,64);

    $white = imagecolorallocate($img, 255, 255, 255);
    $red = imagecolorallocate($img, 255, 0, 0);
    $green = imagecolorallocate($img, 0, 255, 0);
    $blue = imagecolorallocate($img, 0, 0, 255);
    $black = imagecolorallocatealpha($img, 0, 0, 0, 0);
    $grey = imagecolorallocate($img, 211, 211, 211);

    $sand = imagecolorallocate($img, 255, 200, 0);
    //$earth = imagecolorallocate($img, 50, 21, 12);
    $earth = imagecolorallocate($img, 101, 159, 0);

    $marine = imagecolorallocate($img, 0, 100, 255);
    $sea = imagecolorallocate($img, 0, 161, 185);

    $player_colors = array();
    $players = array();
    if( $game_id !== null ) {
      /* @var $game Game */
      $game = Game::instance($game_id);

      foreach( $game->get_game_player_list() as $game_player_row ) {
        $player = Player::instance($game_player_row['player_id']);
        $color_array = toColorCode($player->name);
        $player_colors[ $player->id ] = imagecolorallocate($img, $color_array['R'], $color_array['G'], $color_array['B']);
        $players[] = $player;
      }
    }

    imagefill($img, 0, 0, $grey);

    $divisor = 100;

    $offset_grid_x = $offset_x % 100;
    $offset_grid_y = $offset_y % 100;

    for($iw=1; $iw < $size_x / $divisor; $iw++){imageline($img, ($iw * $divisor - $offset_grid_x) * $ratio, 0, ($iw * $divisor - $offset_grid_x) * $ratio, $size_x * $ratio, $white);}
    for($ih=1; $ih < $size_y / $divisor; $ih++){imageline($img, 0, ($ih * $divisor - $offset_grid_y) * $ratio, $size_x * $ratio, ($ih * $divisor - $offset_grid_y) * $ratio, $white);}

    foreach( $territories as $key => $area ) {
      $polygon = array();
      foreach( $area->vertices as $pointAire ) {
        $polygon[] = ($pointAire->x - $offset_x) * $ratio;
        $polygon[] = self::y( $size_y, $offset_y, $pointAire->y ) * $ratio;
      }

      $color = $white;
      if( $game_id !== null ) {
        $owner_id = $area->get_owner($game_id, $turn);
        if( $owner_id ) {
          $color = $player_colors[ $owner_id ];
        }
      }

      imagefilledpolygon( $img, $polygon, count( $area->vertices ), $color );

      if( $game_id !== null ) {
        if( $area->is_capital($game_id, $turn) ) {
          imagefilledpolygon( $img, $polygon, count( $area->vertices ), $veil );
        }
        if( $area->is_contested($game_id, $turn) ) {
          $tile_contested = imagecreatefrompng( DIR_ROOT.'img/img_css/conflict.png');
          imagesettile($img, $tile_contested);
          imagefilledpolygon( $img, $polygon, count( $area->vertices ), IMG_COLOR_TILED );
        }
      }

    }
    foreach( $territories as $key => $area ) {
      $lastPoint = $area->vertices[ count( $area ) - 1 ];
      foreach( $area->vertices as $point ) {
        imageline($img, ($lastPoint->x - $offset_x) * $ratio, self::y($size_y, $offset_y, $lastPoint->y) * $ratio, ($point->x - $offset_x) * $ratio, self::y($size_y, $offset_y, $point->y) * $ratio, $sand);

        $lastPoint = $point;
      }
    }
    foreach( $territories as $key => $area ) {
      $centroid = $area->get_centroid();

      $name = $area->name;
      $bbox = imagettfbbox( 10, 0, $font, $name );
      //var_dump( $bbox );
      $textwidth = $bbox[2] - $bbox[0];

      // Ajout d'ombres au texte
      imagettftext($img, 10, 0, ($centroid->x - $offset_x - $textwidth / 2) * $ratio + 1, self::y($size_y, $offset_y, $centroid->y ) * $ratio + 1, $grey, $font, $name);

      // Ajout du texte
      imagettftext($img, 10, 0, ($centroid->x - $offset_x - $textwidth / 2) * $ratio, self::y($size_y, $offset_y, $centroid->y) * $ratio, $black, $font, $name);
      /*foreach( $area->vertices as $vertex ) {
        // Ajout d'ombres au texte
        imagettftext($img, 15, 0, $vertex->x + 3, $size_y - ($vertex->y + 3), $grey, $font, $vertex);
        // Ajout du texte
        imagettftext($img, 15, 0, $vertex->x + 2, $size_y - ($vertex->y + 2), $black, $font, $vertex);
      }*/
    }

    if( $game_id != null ) {
      foreach( $players as $key => $player ) {
        imagefilledpolygon( $img,
          array(
              5, $key * 15 + 5,
              25, $key * 15 + 5,
              25, $key * 15 + 15,
              5, $key * 15 + 15
          ),
          4,
          $player_colors[ $player->id ]
        );

        // Ajout d'ombres au texte
        imagettftext($img, 10, 0, 30 + 1, $key * 15 + 15 +1, $grey, $font, $player->name);

        // Ajout du texte
        imagettftext($img, 10, 0, 30, $key * 15 + 15, $black, $font, $player->name);
      }
    }

    return $img;
  }

  public static function y($size_y, $offset_y, $y) {
    return $size_y - $y;
  }

  public function drawImg( $options = array() ) {
    $defaults = array(
        'with_map' => false,
        'territories' => null,
        'game_id' => null,
        'turn' => null,
        'size_x' => $this->size_x,
        'size_y' => $this->size_y,
        'offset_x' => 0,
        'offset_y' => 0
    );

    foreach( $defaults as $key => $value ) {
      if( !isset($options[$key] ) ) $options[ $key ] = $value;
    }

    extract( $options );

    $image = $this->draw( $options );
    ob_start();
    imagepng($image);
    $imagevariable = ob_get_clean();

    if( $territories === null ) {
      $territories = $this->get_territories();
    }else {
      $size_x = 0;
      $size_y = 0;
      $offset_x = $this->size_x;
      $offset_y = $this->size_y;
      foreach( $territories as $key => $area ) {
        foreach( $area->vertices as $pointAire ) {
          $size_x = max( $size_x, $pointAire->x );
          $size_y = max( $size_y, $pointAire->y );
          $offset_x = min( $offset_x, $pointAire->x );
          $offset_y = min( $offset_y, $pointAire->y );
        }
      }
      $size_x = $size_x - $offset_x + 20;
      $size_y = $size_y - $offset_y + 20;
      $offset_x -= 10;
      $offset_y -= 10;
    }

    if( $with_map ) {
      echo '
      <map name="world">';
      foreach( $territories as $territory ) {
        $coords = array();
        foreach( $territory->vertices as $vertex ) {
          $coords[] = ( $vertex->x - $offset_x ) .','. ($size_y - ($vertex->y - $offset_y));
        }
        $owner_id = $territory->get_owner($game_id, $turn);
        if( $owner_id != null ) {
          $owner = Player::instance($owner_id);
        }
        echo '
        <area
          territory="'.$territory->id.'"
          shape="polygon"
          coords="'.implode(',', $coords).'"
          href="'.Page::get_url('show_territory', array('id' => $territory->id)).'"
          title="'.$territory->name.' ('.($owner_id?$owner->name:'Nobody').')'.
                  ($territory->is_capital($game_id, $turn)?' [Capital]':'').
                  ($territory->is_contested($game_id, $turn)?' <Contested>':'').
                '" />';
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