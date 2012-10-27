<?php
/**
 * Class World
 *
 */

require_once( DATA."model/world_model.class.php" );

class World extends World_Model {

  // CUSTOM

  const TILE_DIR = 'cache/world/';

  public function set_generation_parameters($params) { $this->_generation_parameters = serialize($params);}

  protected $territories = null;
  protected $_generation_method = 'simple';

  public function get_territories() {
    if( is_null( $this->territories )) {
      $this->territories = Territory::db_get_by_world_id($this->id);
    }
    return $this->territories;
  }

  public function get_generation_parameters( $all = false ) {
    $simple_defaults = array(
      'minVertexDist' => 100,
      'maxVertexDist' => 110,
      'relationNumber' => 2,
      'maxEdgeDist' => 150,
    );
    $voronoi_defaults = array(
      'distFromEdges' => 20,
      'territoryNumber' => 100
    );
    if( $all ) {
      $defaults = array_merge( $simple_defaults, $voronoi_defaults );
    }else {
      switch( $this->generation_method ) {
        case 'simple' : {
          $defaults = $simple_defaults;
          break;
        }
        case 'voronoi' : {
          $defaults = $voronoi_defaults;
          break;
        }
      }
    }

    $options = unserialize($this->_generation_parameters);

    foreach( $defaults as $key => $value ) {
      if( !isset($options[$key] ) ) $options[ $key ] = $value;
    }
    return $options;
  }

  public function initialize_territories() {
    $return = true;

    if( is_null( $this->id ) ) {
      $return = $this->save();
    }

    if( $return ) {
      foreach (glob(DIR_ROOT . self::TILE_DIR . '/tile_'.$this->id.'*') as $removeFile) {
        unlink($removeFile);
      }

      $method = 'territory_generation_'.$this->generation_method;
      $territories = $this->$method();

      $this->territories = $territories;

      Territory::db_remove_by_world_id( $this->id );

      foreach( $this->territories as $territory ) {
        $territory->name = Territory::get_random_country_name();
        $territory->capital_name = Territory::get_random_capital_name();
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
    }
    return $return;
  }

  public function territory_generation_simple() {
    $return = null;

    $options = $this->generation_parameters;

    $graph = new Graph();
    $graph->randomVertexGenerationDisk( $this->size_x, $minVertexDist, $maxVertexDist );
    $graph->randomNoncrossingEdgeGeneration( $relationNumber, $maxEdgeDist );

    $return = Territory::find_in_graph( $graph );

    unset( $graph );

    return $return;
  }

  public function territory_generation_voronoi() {
    $return = null;

    $options = $this->generation_parameters;

    require_once LIB.'php-voronoi/library/Nurbs/Voronoi.php';
    require_once LIB.'php-voronoi/library/Nurbs/Point.php';

    $bbox = new stdClass();
    $bbox->xl = 0;
    $bbox->xr = $this->size_x - 1;
    $bbox->yt = 1;
    $bbox->yb = $this->size_y;

    $xo = $options['distFromEdges'];
    $dx = $this->size_x - $options['distFromEdges'];
    $yo = $options['distFromEdges'];
    $dy = $this->size_y - $options['distFromEdges'];
    $n = $options['territoryNumber'];
    $sites = array();

    //$im = imagecreatetruecolor($width, $height);
    //$white = imagecolorallocate($im, 255, 255, 255);
    //$red = imagecolorallocate($im, 255, 0, 0);
    //$green = imagecolorallocate($im, 0, 100, 0);
    //$black = imagecolorallocate($im, 0, 0, 0);
    //imagefill($im, 0, 0, $white);
    if( 1 == 2 ) {
      $graph = new Graph();
      $graph->randomVertexGenerationDisk( $this->size_x, $options['minVertexDist'], $options['maxVertexDist'] );
      foreach( $graph->vertices as $vertex ) {
        $sites[] = new Nurbs_Point($vertex->x, $vertex->y);
      }
    }

    for ($i=0; $i < $n; $i++) {
      $point = new Nurbs_Point(rand($xo, $dx), rand($yo, $dy));
      $sites[] = $point;

      //imagerectangle($im, $point->x - 2, $point->y - 2, $point->x + 2, $point->y + 2, $black);
    }

    $voronoi = new Voronoi();
    $diagram = $voronoi->compute($sites, $bbox);
    $territory_list = array();

    foreach ($diagram['cells'] as $cell) {
      // On va agreger les points du polygone.
      $points = array();
      $vertices = array();

      if (count($cell->_halfedges) > 0) {
        $v = $cell->_halfedges[0]->getStartPoint();
        if ($v) {
          $points[] = $v->x;
          $points[] = $v->y;
          $vertices[] = new Vertex(round($v->x), round($v->y));
        } else {
          var_dump($j.': no start point');
        }

        for ($i = 0; $i < count($cell->_halfedges); $i++) {
          $halfedge = $cell->_halfedges[$i];
          $edge = $halfedge->edge;

          if ($edge->va && $edge->vb) {
            //imageline($im, $edge->va->x, $edge->va->y, $edge->vb->x, $edge->vb->y, $red);
          }

          $v = $halfedge->getEndPoint();
          if ($v) {
            $points[] = $v->x;
            $points[] = $v->y;
            $vertices[] = new Vertex(round($v->x), round($v->y));
          } else {
            var_dump($j.': no end point #'.$i);
          }
        }
      }

      // On construit le polygone
      //$color = imagecolorallocatealpha($im, rand(0, 255), rand(0, 255), rand(0, 255), 50);
      //imagefilledpolygon($im, $points, count($points) / 2, $color);

      if( count( $vertices ) > 3 ) {
        $territory = new Territory();
        $territory->vertices = $vertices;
        $territory_list[] = $territory;
      }
    }
    $return = $territory_list;

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

      $font_size = min( max( 7, 10 * $ratio), 12 );

      $bbox = imagettfbbox( $font_size, 0, $font, $name );
      $textwidth = $bbox[2] - $bbox[0];

      // Ajout d'ombres au texte
      imagettftext($img, $font_size, 0, (($centroid->x - $offset_x) * $ratio - $textwidth / 2) + 1, self::y($size_y, $offset_y, $centroid->y ) * $ratio + 1, $grey, $font, $name);

      // Ajout du texte
      imagettftext($img, $font_size, 0, (($centroid->x - $offset_x) * $ratio - $textwidth / 2), self::y($size_y, $offset_y, $centroid->y) * $ratio, $black, $font, $name);
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
        'offset_y' => 0,
        'ratio' => 1
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
          $coords[] = round(( $vertex->x - $offset_x ) * $ratio ).','. round(($size_y - ($vertex->y - $offset_y)) * $ratio);
        }
        if( $game_id ) {
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
          title="'.$territory->name.' ('.($owner_id?$owner->name:__('Nobody')).')'.
                  ($territory->is_capital($game_id, $turn)?' ['.__('Capital').']':'').
                  ($territory->is_contested($game_id, $turn)?' <'.__('Contested').'>':'').
                '" />';
        }else {
          echo '
        <area
          territory="'.$territory->id.'"
          shape="polygon"
          coords="'.implode(',', $coords).'"
          href="'.Page::get_url('show_territory', array('id' => $territory->id)).'"
          title="'.$territory->name.'" />';
        }
      }
      echo '
      </map>
      <img usemap="#world" src="data:image/png;base64,'.base64_encode( $imagevariable ).'">';
    }else {
      echo '<img src="data:image/png;base64,'.base64_encode( $imagevariable ).'"/>';
    }
  }

  public function html_creation_form() {
    $generation_types = array(
      'simple' => 'Simple',
      'voronoi' => 'Voronoi'
    );

    $options = $this->get_generation_parameters(true);

    $return = '
    <script>
    $(function(){
      $options = $(".options");
      $options.hide();
      $("." + $("#select_generation_method").val()).show();
      $("#select_generation_method").change(function(){
        $options.hide();
        $("." + $(this).val()).show();
      });
    });
    </script>
    <fieldset>
      <legend>'.__('Create a world !').'</legend>
      '.HTMLHelper::genererInputHidden('id', $this->get_id()).'
      <p class="field">'.HTMLHelper::genererInputText('name', $this->get_name(), array(), __('Name').'*').'</p>
      <p class="field">'.HTMLHelper::genererInputText('size_x', $this->get_size_x(), array(), __('Width').'*').'</p>
      <p class="field">'.HTMLHelper::genererInputText('size_y', $this->get_size_y(), array(), __('Height').'*').'</p>
      <p class="field">'.HTMLHelper::genererSelect('generation_method', $generation_types, $this->get_generation_method(), array('id' => 'select_generation_method'), __('Generation Method')).'</p>
    </fieldset>
    <fieldset class="options simple">
      <legend>'.__('Simple Generator options').'</legend>
      <p class="field">'.HTMLHelper::genererInputText('generation_parameters[minVertexDist]', $options['minVertexDist'], array(), __('Minimum point distance')).'</p>
      <p class="field">'.HTMLHelper::genererInputText('generation_parameters[maxVertexDist]', $options['maxVertexDist'], array(), __('Maximum point distance')).'</p>
      <p class="field">'.HTMLHelper::genererInputText('generation_parameters[relationNumber]', $options['relationNumber'], array(), __('Territory density')).'</p>
      <p class="field">'.HTMLHelper::genererInputText('generation_parameters[maxEdgeDist]', $options['maxEdgeDist'], array(), __('Maximum border length')).'</p>
    </fieldset>
    <fieldset class="options voronoi">
      <legend>'.__('Voronoi Generator options').'</legend>
      <!--<p class="field">'.HTMLHelper::genererInputText('generation_parameters[minVertexDist]', $options['minVertexDist'], array(), __('Minimum territory distance')).'</p>
      <p class="field">'.HTMLHelper::genererInputText('generation_parameters[maxVertexDist]', $options['maxVertexDist'], array(), __('Maximum territory distance')).'</p>-->
      <p class="field">'.HTMLHelper::genererInputText('generation_parameters[distFromEdges]', $options['distFromEdges'], array(), __('Margin from map edges')).'</p>
      <p class="field">'.HTMLHelper::genererInputText('generation_parameters[territoryNumber]', $options['territoryNumber'], array(), __('Territory count')).'</p>
    </fieldset>';
    return $return;
  }

  // /CUSTOM

}