<?php
  function imagelineglow($image, $x1, $y1, $x2, $y2, $color, $gradient = 5) {
    if( $gradient > 0 ) {
      $white = imagecolorallocate($image, 255, 255, 255);

      $initial_opacity = 10;
      $last_opacity = 0;
      for( $t1 = 0 ; $t1 < $gradient; $t1++ ) {
        $opacity = 117 * pow( $t1, 4 ) / pow( ($gradient - 1), 4) + $initial_opacity;
        $current_opacity = 127 - floor($opacity - $last_opacity);
        $last_opacity = $opacity;

        $color_gradient = imagecolortoalpha($image, $color, $current_opacity);

        // Thickness
        $t = ($gradient - $t1);
        //$thick = $i * 2 + 1;
        //$t = $thick / 2 - 0.5;
        if ($x1 == $x2 || $y1 == $y2) {
          imagefilledrectangle($image, round(min($x1, $x2) - $t), round(min($y1, $y2) - $t), round(max($x1, $x2) + $t), round(max($y1, $y2) + $t), $color_gradient);
        }else {
          $k = ($y2 - $y1) / ($x2 - $x1); //y = kx + q
          $a = $t / sqrt(1 + pow($k, 2));
          $points = array(
              round($x1 - (1+$k)*$a), round($y1 + (1-$k)*$a),
              round($x1 - (1-$k)*$a), round($y1 - (1+$k)*$a),
              round($x2 + (1+$k)*$a), round($y2 - (1-$k)*$a),
              round($x2 + (1-$k)*$a), round($y2 + (1+$k)*$a),
          );
          imagefilledpolygon($image, $points, 4, $color_gradient);
        }
      }
      imageline($image, $x1, $y1, $x2, $y2, $white);
    }else {
      imageline($image, $x1, $y1, $x2, $y2, $color);
    }
    return true;
  }

  function imagecolortoalpha( $image, $color, $alpha ) {
    $rgb = sscanf(str_pad( dechex($color), 6, '0', STR_PAD_LEFT), '%2x%2x%2x');
    return imagecolorallocatealpha($image, $rgb[0], $rgb[1], $rgb[2], $alpha);
  }
  function imagepolygonglow( $image, $points, $num_points, $color, $gradient = 5 ) {

  }
?>
