<?php
class Give_Territory extends Player_Order {
  public function execute() {
    $return = false;

    $return_code = -1;

    /* @var $player Player */
    $player = Player::instance( $this->player_id );

    $parameters = $this->parameters;
    if( isset( $parameters['territory_id'] ) && array_key_exists( 'to_player_id' , $parameters ) ) {
      if( ! $parameters['to_player_id'] ) $parameters['to_player_id'] = null;
      /* @var $territory Territory */
      $territory = Territory::instance( $parameters['territory_id'] );
      $to_player = Player::instance( $parameters['to_player_id'] );

      if( $territory->id == $parameters['territory_id'] && $to_player->id == $parameters['to_player_id'] ) {
        if( $territory->get_owner( $player->current_game, $player->current_game->current_turn + 1 ) == $this->player_id ) {
          $territory_owner_list = array_pop( $territory->get_territory_owner_list($player->current_game->id, $player->current_game->current_turn + 1) );
          $territory->set_territory_owner($player->current_game->id, $player->current_game->current_turn + 1, $parameters['to_player_id'], $territory_owner_list['contested'], 0);

          if( $parameters['to_player_id'] === null ) {
            $message = 'You successfully left the territory';
          }else {
            $message = 'You successfully gave the territory to '.$to_player->name;
          }

          $player->current_game->set_player_history(
            $player->id,
            $player->current_game->current_turn + 1,
            time(),
            $message,
            $territory->id
          );
          $return_code = 0;
          $return = true;
        }else {
          if( $parameters['to_player_id'] === null ) {
            $message = 'You don\'t own the territory, you can\'t leave it';
          }else {
            $message = 'You don\'t own the territory, you can\'t give it to '.$to_player->name;
          }
          $player->current_game->set_player_history(
            $player->id,
            $player->current_game->current_turn + 1,
            time(),
            $message,
            $territory->id
          );
          $return_code = 1;
          $return = false;
        }
      }
    }

    $this->turn_executed = $player->current_game->current_turn + 1;
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
    $title = 'Give ';

    /* @var $game Game */
    $game = $params['current_player']->get_current_game();

    if( isset( $params['territory'] ) ) {
      $title .= '%s';
      $title = __( $title, $params['territory']->name );
    }
    if( isset( $params['to_player'] ) ) {
      $title .= 'a territory to %';
      $title = __( $title, $params['to_player']->name );
    }

    $page_params = array();
    if( isset( $params['page_params'] ) ) {
      $page_params = $params['page_params'];
    }

    $territory_select = array();
    if( !isset( $params['territory'] )  ) {
      $territory_owner_list = $game->get_territory_owner_list(null, $game->current_turn, $params['current_player']->id);
      foreach( $territory_owner_list as $territory_owner_row ) {
        $territory = Territory::instance( $territory_owner_row['territory_id'] );
        $territory_select[ $territory->id ] = $territory->name;
      }
    }else {
      $territory_select = $game->get_territory_owner_list($params['territory']->id, $game->current_turn, $params['current_player']->id);
    }

    $player_select = array(
        null => 'Leave the territory'
    );
    if( !isset( $params['to_player'] ) ) {
      $player_list = Player::db_get_by_game($game->id, true);
      foreach( $player_list as $player ) {
        if( $player != $params['current_player'] ) {
          $player_select[ $player->id ] = $player->name;
        }
      }
    }

    $return = '
<form action="'.Page::get_page_url( 'order' ).'" method="post">
  <fieldset>
    <legend>'.$title.'</legend>';
    if( !isset( $params['territory'] ) || count( $territory_select ) != 0 ) {
      $return .= HTMLHelper::genererInputHidden('url_return', Page::get_page_url( $params['page_code'], true, $page_params ) );
      if( isset( $params['territory'] ) ) {
        $return .= '
    '.HTMLHelper::genererInputHidden('parameters[territory_id]', $params['territory']->id);
      }else {
        $return .= '
    <p>'.HTMLHelper::genererSelect( 'parameters[territory_id]', $territory_select, null, array(), __('Give') ).'</p>';
      }
      if( isset( $params['to_player'] ) ) {
        $return .= '
    '.HTMLHelper::genererInputHidden('parameters[to_player_id]', $params['to_player']->id);
      }else {
        $return .= '
    <p>'.HTMLHelper::genererSelect( 'parameters[to_player_id]', $player_select, null, array(), __('Give to') ).'</p>';
      }

    $return .= '
    <p>'.HTMLHelper::genererButton( 'action', strtolower(__CLASS__), array('type' => 'submit'), __('Give territory') ).'</p>';
    }else {
      $return .= '
    <p>'.__('You don\'t have any territory to give').'</p>';
    }
    $return .= '
  </fieldset>
</form>';

    return $return;
  }
}
?>