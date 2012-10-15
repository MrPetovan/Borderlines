<?php
class Quit_Game extends Player_Order {

  public function execute() {
    $return = false;

    $return_code = -1;

    /* @var $player Player */
    $player = Player::instance( $this->player_id );

    $current_game = $player->current_game;

    $next_turn = $current_game->current_turn + 1;

    $parameters = $this->parameters;

    // Removing quitting players
    $current_game->del_territory_player_troops($next_turn, null, $player->id);
    foreach( Player_Order::db_get_planned_by_player_id($player->id, $current_game->id) as $order ) {
      /* @var $order Player_Order */
      $order->cancel();
    }

    foreach( $current_game->get_territory_owner_list(null, $next_turn, $player->id) as $territory_owner) {
      $current_game->set_territory_owner($territory_owner['territory_id'], $next_turn, null, 0, 0);
    }

    $game_player = array_pop( $current_game->get_game_player_list( $player->id ) );
    $current_game->set_game_player($player->id, $game_player['turn_ready'], $current_game->current_turn);

    $this->datetime_execution = time();
    $this->turn_executed = $current_game->current_turn + 1;
    $this->return = $return_code;
    $this->save();

    return $return;
  }

  /**
   * Generate HTML form for the action
   * Mandatory parameters :
   * - current_player (Player) : The player quitting
   * - page_code (string) : The page code where the form is displayed
   * Optional parameters :
   * - page_params (array) : Current page parameters where the form is displayed
   */
  public static function get_html_form( $params ) {
    $title = 'Move your capital';

    /* @var $current_player Player */
    $current_player = $params['current_player'];

    $game = $params['current_player']->current_game;

    $territory_owner_list = $current_player->get_territory_owner_list(null, $game->id, $game->current_turn);

    $territory_list = array();
    foreach( $territory_owner_list as $territory_owner_row ) {
      $territory = Territory::instance( $territory_owner_row['territory_id'] );
      $territory_list[ $territory->id ] = $territory->name;
    }

    if( isset( $params['territory'] ) ) {
      $title .= ' to '.$params['territory']->name;
    }

    $page_params = array();
    if( isset( $params['page_params'] ) ) {
      $page_params = $params['page_params'];
    }

    if( !isset( $params['territory'] ) ) {

    }

    $return = '
<form action="'.Page::get_page_url( 'order' ).'" method="post">
  <fieldset>
    <legend>'.$title.'</legend>';
    if( !isset( $params['territory'] ) || isset( $territory_list[ $params['territory']->id ]) ) {
      $return .= '
    <p>Moving your capital takes two turns, and may be cancelled if you don\'t own the territory</p>
    '.HTMLHelper::genererInputHidden('url_return', Page::get_page_url( $params['page_code'], true, $page_params ) );
      if( isset( $params['territory'] ) ) {
        $return .= '
    '.HTMLHelper::genererInputHidden('parameters[territory_id]', $params['territory']->id);
      }else {
        $return .= '
    <p>'.HTMLHelper::genererSelect( 'parameters[territory_id]', $territory_list, null, array(), 'Move capital to' ).'</p>';
      }
      $return .= '
    <p>'.HTMLHelper::genererButton( 'action', 'change_capital', array('type' => 'submit'), "Move, move, move !" ).'</p>';
    }else {
      $return .= '
    <p>You don\'t own this territory</p>';
    }
    $return .= '
  </fieldset>
</form>';

    return $return;
  }
}
?>