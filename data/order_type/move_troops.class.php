<?php
class Move_Troops extends Player_Order {
  public function plan( Order_Type $order_type, Player $player, $params ) {
    parent::plan( $order_type, $player, $params );

    $soldiers_sent = $params['count'];
    $from_territory_id = $params['from_territory_id'];
    $player = Player::instance( $this->player_id );

    $game = $player->current_game;

    $player_territory = $player->get_territory_player_troops_list( $game->id, $game->current_turn, $from_territory_id );

    if( count( $player_territory ) ) {
      $available_soldiers = $player_territory[0]['quantity'];
      if( $available_soldiers < $soldiers_sent ) {
        $params['count'] = $available_soldiers;
      }

      $this->parameters = $params;
    }

    return $this->save();
  }

  public function execute() {
    $return = false;

    $return_code = -1;

    /* @var $player Player */
    $player = Player::instance( $this->player_id );

    $parameters = $this->parameters;
    if( isset( $parameters['count'] ) && isset( $parameters['from_territory_id'] ) && isset( $parameters['to_territory_id'] ) ) {

      $from_territory = Territory::instance( $parameters['from_territory_id'] );
      $to_territory = Territory::instance( $parameters['to_territory_id'] );

      if( $from_territory && $to_territory ) {
        $game_id = $player->current_game->id;
        $order_turn = $player->current_game->current_turn;

        $from_troops_before = $player->get_territory_player_troops_list( $game_id, $order_turn, $from_territory->id );

        if( count( $from_troops_before ) ) {
          $from_troops_before = $from_troops_before[0]['quantity'];

          $from_troops_after = max( $from_troops_before - $parameters['count'], 0 );

          // Correction in case of order error
          $parameters['count'] = $from_troops_before - $from_troops_after;

          if( $from_troops_after > 0 ) {
            $player->set_territory_player_troops( $game_id, $order_turn, $from_territory->id, $from_troops_after );
          }else {
            $player->del_territory_player_troops( $game_id, $order_turn, $from_territory->id );
          }

          $to_troops_before = $player->get_territory_player_troops_list( $game_id, $order_turn, $to_territory->id );

          if( count( $to_troops_before ) ) {
            $to_troops_before = $to_troops_before[0]['quantity'];
          }else {
            $to_troops_before = 0;
          }

          $to_troops_after = $to_troops_before + $parameters['count'];

          if( $to_troops_after > 0 ) {
            $player->set_territory_player_troops( $game_id, $order_turn, $to_territory->id, $to_troops_after );
          }elseif( count( $to_troops_before ) ) {
            $player->del_territory_player_troops( $game_id, $order_turn, $to_territory->id );
          }
        }

        $return = true;
      }
    }

    $this->datetime_execution = time();
    $this->return = $return_code;
    $this->save();

    return $return;
  }

  /**
   * Generate HTML form for the action
   * Mandatory parameters :
   * - current_player (Player) : The player moving
   * - page_code (string) : The page code where the form is displayed
   * Optional parameters :
   * - from_territory (Territory) : The territory from
   * - to_territory (Territory) : The territory to
   * - page_params (array) : Current page parameters where the form is displayed
   */
  public static function get_html_form( $params ) {
    $title = 'Move your troops';

    $game = $params['current_player']->get_current_game();

    if( isset( $params['from_territory'] ) ) {
      $title .= ' from '.$params['from_territory']->name;
    }
    if( isset( $params['to_territory'] ) ) {
      $title .= ' to '.$params['to_territory']->name;
    }

    $page_params = array();
    if( isset( $params['page_params'] ) ) {
      $page_params = $params['page_params'];
    }

    if( isset( $params['from_territory'] ) && !isset( $params['to_territory'] ) ) {
      $territory_neighbour_list = $params['from_territory']->get_territory_neighbour_list();

      $neighbour_list = array();
      foreach( $territory_neighbour_list as $neighbour_array ) {
        $neighbour = Territory::instance( $neighbour_array['neighbour_id'] );
        $neighbour_list[  $neighbour->id ] = $neighbour->name;
      }
    }

    if( isset( $params['to_territory'] ) && !isset( $params['from_territory'] ) ) {
      $territory_neighbour_list = $params['to_territory']->get_territory_neighbour_list();
      $territory_occupied_list = $params['current_player']->get_territory_player_troops_list( $game->id, $game->current_turn );

      $neighbour_list = array();
      foreach( $territory_neighbour_list as $neighbour_array ) {
        foreach( $territory_occupied_list as $territory_occupied_hash ) {
          if( $territory_occupied_hash['territory_id'] == $neighbour_array['neighbour_id'] ) {
            $neighbour = Territory::instance( $neighbour_array['neighbour_id'] );
            $neighbour_list[  $neighbour->id ] = $neighbour->name;
          }
        }
      }
    }

    $return = '
<form action="'.Page::get_page_url( 'order' ).'" method="post">
  <fieldset>
    <legend>'.$title.'</legend>';
    if( count( $neighbour_list ) ) {
      $return .= HTMLHelper::genererInputHidden('url_return', Page::get_page_url( $params['page_code'], true, $page_params ) ).'
    <p>'.HTMLHelper::genererInputText( 'parameters[count]', 0, array(), 'Troop size', null ).'</p>';
      if( isset( $params['from_territory'] ) ) {
        $return .= '
    '.HTMLHelper::genererInputHidden('parameters[from_territory_id]', $params['from_territory']->id);
      }else {
        $return .= '
    <p>'.HTMLHelper::genererSelect( 'parameters[from_territory_id]', $neighbour_list, null, array(), 'Move from' ).'</p>';
      }
      if( isset( $params['to_territory'] ) ) {
        $return .= '
    '.HTMLHelper::genererInputHidden('parameters[to_territory_id]', $params['to_territory']->id);
      }else {
        $return .= '
    <p>'.HTMLHelper::genererSelect( 'parameters[to_territory_id]', $neighbour_list, null, array(), 'Move to' ).'</p>';
      }

    $return .= '
    <p>'.HTMLHelper::genererButton( 'action', 'move_troops', array('type' => 'submit'), "March !" ).'</p>';
    }else {
      $return .= '
    <p>You don\'t have any troops in neighbouring territories</p>';
    }
    $return .= '
  </fieldset>
</form>';

    return $return;
  }
}
?>