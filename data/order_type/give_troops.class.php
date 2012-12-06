<?php
class Give_Troops extends Player_Order {
  public function plan( Order_Type $order_type, Player $player, $params ) {
    $valid = isset( $params['from_territory_id'] ) && isset( $params['to_player_id'] ) && isset( $params['count'] );

    if( $valid ) {
      $locale = localeconv();
      $count = preg_replace('/[^\d\\'.$locale['decimal_point'].']/', '', $params['count']);
      $params['count'] = $count;
      $valid = strval( intval($params['count']) ) === $params['count'] && $params['count'] > 0;
    }
    if( $valid ) {
      $territory = Territory::instance($params['from_territory_id']);
      $valid = $territory->id !== null;
    }
    if( $valid ) {
      $valid = count( $player->current_game->get_game_player_list($params['to_player_id']) ) != 0;
    }
    if( $valid ) {
      parent::plan( $order_type, $player, $params );

      $soldiers_gifted = $params['count'];
      $from_territory_id = $params['from_territory_id'];
      $player = Player::instance( $this->player_id );

      $game = $player->current_game;

      $player_territory = $game->get_territory_player_troops_list( $game->current_turn, $from_territory_id, $player->id );

      $valid = count( $player_territory ) > 0;
    }
    if( $valid ) {
      $available_soldiers = $player_territory[0]['quantity'];
      if( $available_soldiers < $soldiers_gifted ) {
        $params['count'] = $available_soldiers;
      }

      $this->parameters = $params;

      $valid = $this->save();
    }
    return $valid;
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
        $current_game = $player->current_game;
        $game_id = $current_game->id;
        $order_turn = $current_game->current_turn;
        $next_turn = $current_game->current_turn + 1;

        $from_troops_before = $current_game->get_territory_player_troops_list( $next_turn, $from_territory->id, $player->id );

        if( count( $from_troops_before ) ) {
          $from_troops_before = $from_troops_before[0]['quantity'];

          // Correction in case of order error
          $parameters['count'] = min( $parameters['count'], $from_troops_before );

          $player->set_territory_player_troops_history( $game_id, $next_turn, $from_territory->id, - $parameters['count'], 'Troops gift', $to_player->id );
          $to_player->set_territory_player_troops_history( $game_id, $next_turn, $from_territory->id, $parameters['count'], 'Troops gift', $player->id );
          $return_code = 0;
          $return = true;
        }else {
          $return_code = 2;
        }
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
    }else {
      $territory_select = $game->get_territory_player_troops_list($game->current_turn, $params['from_territory']->id, $params['current_player']->id);
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
    <legend><img src="'.IMG.'img_html/troops_gift.png" alt="" /> '.$title.'</legend>
    <div class="content">';
    if( !isset( $params['from_territory'] ) || count( $territory_select ) != 0 ) {
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
    </div>
  </fieldset>
</form>';

    return $return;
  }
}
?>