<?php

define('SPY_TIMEOUT', 120);

function spygame( $spy1, $spy2, $value ) {
  $return = null;

  if( $spy2 == 0 ) {
    $spy2 = 1;
  }

  $diffAddPercent = $spy1 / ($spy1 + $spy2);

  $uncoveringValueChance = $diffAddPercent;

  $missionResult = mt_gaussrand() * 0.25 + 0.5;

  if( $missionResult < $uncoveringValueChance ) {
    if( $missionResult < 0 ) $missionResult = 0; // Réussite parfaite
    $factor = mt_gaussrand() * 0.3;
    $deviation = $factor * $missionResult / $uncoveringValueChance;

    $return = round( $value * (1 + $deviation) );
  }

  return $return;
}

/**
 * Generates a s-curve (or z-curve) function given the wanted y limits on the
 * left and the right and the brackets for x values to reach those limits and
 * computes the value for the given number
 *
 * @param float $number The input value
 * @param float $left_limit The y limit at $left_bracket
 * @param float $right_limit The y limite at $right_bracket
 * @param float $left_bracket The x where y is $left_limit
 * @param float $right_bracket The x where y is $right_limit
 * @return float
 *
 * @see http://www.pmean.com/04/scurve.html
 */
function s($number, $left_limit = 0, $right_limit = 1, $left_bracket = 0, $right_bracket = 10) {

  $return = 0;

  $sharpness = 10 / ($right_bracket - $left_bracket);
  $shift = sqrt($right_bracket - $left_bracket);
  $low = min($left_limit, $right_limit);
  $high = max($left_limit, $right_limit);

  if( $left_limit < $right_limit ) {
    $sharpness = -$sharpness;
    $shift = -$shift;
  }

  $return = $low + 1 / (1 / ($high - $low) + exp( $sharpness * $number - $shift));

  //var_dump("s($number, $left_limit, $right_limit, $left_bracket, $right_bracket)");
  //var_dump("\$return = $low + 1 / (1 / ($high - $low) + exp( $sharpness * $number - $shift));");
  //var_dump($return);

  return $return;
}