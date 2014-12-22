<?php
class Give_Tribute extends Player_Order {
  public function plan( Order_Type $order_type, Player $player, $params ) {
    $valid = isset( $params['to_player_id'] ) && isset( $params['count'] );

    if( $valid ) {
      $locale = localeconv();
      $count = preg_replace('/[^\d\\'.$locale['decimal_point'].']/', '', $params['count']);
      $params['count'] = $count;
      $valid = strval( intval($params['count']) ) === $params['count'] && $params['count'] > 0;
    }
    if( $valid ) {
      $valid = count( $player->current_game->get_game_player_list($params['to_player_id']) ) != 0;
    }
    if( $valid ) {
      parent::plan( $order_type, $player, $params );

      $money_gifted = $params['count'];
      $player = Player::instance( $this->player_id );

      $game = $player->current_game;
      /* @var $game Game */
      $available_money = $player->get_revenue($game, $game->current_turn + 1);
      if( $available_money < $money_gifted ) {
        $params['count'] = $available_money;
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
    if( isset( $parameters['count'] ) && isset( $parameters['to_player_id'] ) ) {

      /* @var $to_player Player */
      $to_player = Player::instance( $parameters['to_player_id'] );

      if( $to_player ) {
        $current_game = $player->current_game;
        $game_id = $current_game->id;
        $order_turn = $current_game->current_turn;
        $next_turn = $current_game->current_turn + 1;

        $revenue = $player->get_revenue($current_game, $next_turn);

        // Correction in case of order error
        $parameters['count'] = min( $parameters['count'], $revenue );

        $current_game->set_player_history($player->id, $next_turn, time(), "You gave a tribute of " . $parameters['count'] . " to " . $to_player->name);
        $current_game->set_player_history($to_player->id, $next_turn, time(), "You received a tribute of " . $parameters['count'] . " from " . $player->name);

        $this->parameters = $parameters;

        $return_code = 0;
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


    /* @var $game Game */
    $game = $params['current_player']->get_current_game();

    if( isset( $params['to_player'] ) ) {
      $title = __('Give a money tribute to %s', $params['to_player']->name);
    }else {
      $title = __('Give a money tribute');
    }

    $page_params = array();
    if( isset( $params['page_params'] ) ) {
      $page_params = $params['page_params'];
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
    <legend><img src="'.IMG.'img_html/give_tribute.png" alt="" /> '.$title.'</legend>
    <div class="content">';
    $return .= HTMLHelper::genererInputHidden('url_return', Page::get_page_url( $params['page_code'], true, $page_params ) ).'
      <p>'.HTMLHelper::genererInputText( 'parameters[count]', 0, array(), __('Tribute amount'), null ).'</p>';
    if( isset( $params['to_player'] ) ) {
      $return .= '
      '.HTMLHelper::genererInputHidden('parameters[to_player_id]', $params['to_player']->id);
    }else {
      $return .= '
      <p>'.HTMLHelper::genererSelect( 'parameters[to_player_id]', $player_select, null, array(), __('Give to') ).'</p>';
    }
    $return .= '
      <p>'.HTMLHelper::genererButton( 'action', strtolower(__CLASS__), array('type' => 'submit'), __('Kaching!') ).'</p>';
    $return .= '
    </div>
  </fieldset>
</form>';

    return $return;
  }
}
?>