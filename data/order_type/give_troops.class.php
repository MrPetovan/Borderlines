<?php
class Give_Troops extends Player_Order {
  public function plan( Order_Type $order_type, Player $player, $params ) {
    parent::plan( $order_type, $player, $params );

    $soldiers_gifted = $params['count'];
    $from_territory_id = $params['from_territory_id'];
    $player = Player::instance( $this->player_id );

    $game = $player->current_game;

    $player_territory = $player->get_territory_player_troops_list( $game->id, $game->current_turn, $from_territory_id );

    if( count( $player_territory ) ) {
      $available_soldiers = $player_territory[0]['quantity'];
      if( $available_soldiers < $soldiers_gifted ) {
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
    if( isset( $parameters['count'] ) && isset( $parameters['from_territory_id'] ) && isset( $parameters['to_player_id'] ) ) {

      $from_territory = Territory::instance( $parameters['from_territory_id'] );
      $to_player = Player::instance( $parameters['to_player_id'] );

      if( $from_territory && $to_player ) {
        $game_id = $player->current_game->id;
        $order_turn = $player->current_game->current_turn;
        $next_turn = $player->current_game->current_turn + 1;

        $from_troops_before = $player->get_territory_player_troops_list( $game_id, $next_turn, $from_territory->id );

        if( count( $from_troops_before ) ) {
          $from_troops_before = $from_troops_before[0]['quantity'];

          $from_troops_after = max( $from_troops_before - $parameters['count'], 0 );

          // Correction in case of order error
          $parameters['count'] = $from_troops_before - $from_troops_after;

          if( $from_troops_after > 0 ) {
            $player->set_territory_player_troops( $game_id, $next_turn, $from_territory->id, $from_troops_after );
          }else {
            $player->del_territory_player_troops( $game_id, $next_turn, $from_territory->id );
          }

          $to_troops_before = $to_player->get_territory_player_troops_list( $game_id, $next_turn, $from_territory->id );

          if( count( $to_troops_before ) ) {
            $to_troops_before = $to_troops_before[0]['quantity'];
          }else {
            $to_troops_before = 0;
          }

          $to_troops_after = $to_troops_before + $parameters['count'];

          if( $to_troops_after > 0 ) {
            $to_player->set_territory_player_troops( $game_id, $next_turn, $from_territory->id, $to_troops_after );
          }elseif( count( $to_troops_before ) ) {
            $to_player->del_territory_player_troops( $game_id, $next_turn, $from_territory->id );
          }
        }

        $return = true;
      }
    }

    $this->turn_executed = $next_turn;
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
    $title = 'Give your troops';

    /* @var $game Game */
    $game = $params['current_player']->get_current_game();

    if( isset( $params['from_territory'] ) ) {
      $title .= ' in '.$params['from_territory']->name;
    }
    if( isset( $params['to_player'] ) ) {
      $title .= ' to '.$params['to_player']->name;
    }

    $page_params = array();
    if( isset( $params['page_params'] ) ) {
      $page_params = $params['page_params'];
    }

    $territory_select = array();
    if( !isset( $params['from_territory'] )  ) {
      $territory_player_troops_list = $game->get_territory_player_troops_list($game->current_turn, null, $params['current_player']->id);
      foreach( $territory_player_troops_list as $territory_player_troops_row ) {
        $territory = Territory::instance( $territory_player_troops_row['territory_id'] );
        $territory_select[ $territory->id ] = $territory->name. ' (' . l10n_number($territory_player_troops_row['quantity']) . ')';
      }
    }

    $player_select = array();
    if( !isset( $params['to_player'] ) ) {
      $player_list = Player::db_get_by_game($game->id, true);
      foreach( $player_list as $player ) {
        if( $player != $params['current_player'] ) {
          $player_select[  $player->id ] = $player->name;
        }
      }
    }

    $return = '
<form action="'.Page::get_page_url( 'order' ).'" method="post">
  <fieldset>
    <legend>'.$title.'</legend>';
    if( !isset( $params['from_territory'] ) || count( $territory_select ) == 0 ) {
      $return .= HTMLHelper::genererInputHidden('url_return', Page::get_page_url( $params['page_code'], true, $page_params ) ).'
    <p>'.HTMLHelper::genererInputText( 'parameters[count]', 0, array(), 'Troop size', null ).'</p>';
      if( isset( $params['from_territory'] ) ) {
        $return .= '
    '.HTMLHelper::genererInputHidden('parameters[from_territory_id]', $params['from_territory']->id);
      }else {
        $return .= '
    <p>'.HTMLHelper::genererSelect( 'parameters[from_territory_id]', $territory_select, null, array(), 'Give from' ).'</p>';
      }
      if( isset( $params['to_player'] ) ) {
        $return .= '
    '.HTMLHelper::genererInputHidden('parameters[to_player_id]', $params['to_player']->id);
      }else {
        $return .= '
    <p>'.HTMLHelper::genererSelect( 'parameters[to_player_id]', $player_select, null, array(), 'Give to' ).'</p>';
      }

    $return .= '
    <p>'.HTMLHelper::genererButton( 'action', strtolower(__CLASS__), array('type' => 'submit'), "March !" ).'</p>';
    }else {
      $return .= '
    <p>You don\'t have any troops to give</p>';
    }
    $return .= '
  </fieldset>
</form>';

    return $return;
  }
}
?>