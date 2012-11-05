<?php
class Change_Capital extends Player_Order {
  public function plan( Order_Type $order_type, Player $player, $params ) {
    $return = null;
    $has_already_been_ordered = false;

    $orders = Player_Order::db_get_planned_by_player_id( $player->id, $player->current_game->id );
    foreach( $orders as $player_order ) {
      if( $order_type->id == $player_order->order_type_id ) {
        $has_already_been_ordered = true;
      }
    }
    if(! $has_already_been_ordered ) {
      parent::plan( $order_type, $player, $params );

      // Executes the turn after ordered
      $this->turn_scheduled = $player->current_game->current_turn + 1;

      $return = $this->save();
    }else {
      $return = false;
    }

    return $return;
  }

  public function execute() {
    $return = false;

    $return_code = -1;

    /* @var $player Player */
    $player = Player::instance( $this->player_id );

    $parameters = $this->parameters;
    if( isset( $parameters['territory_id'] ) ) {
      /* @var $territory Territory */
      $territory = Territory::instance( $parameters['territory_id'] );

      if( $territory ) {
        if( $territory->get_owner($player->current_game->id, $player->current_game->current_turn + 1) == $this->player_id ) {
          $sql = '
UPDATE `territory_owner`
SET `capital` = 0
WHERE `game_id` = '.mysql_ureal_escape_string($player->current_game->id).'
AND `turn` = '.mysql_ureal_escape_string($player->current_game->current_turn + 1).'
AND `owner_id` = '.mysql_ureal_escape_string($this->player_id);
          mysql_uquery($sql);

          $sql = '
UPDATE `territory_owner`
SET `capital` = 1
WHERE `game_id` = '.mysql_ureal_escape_string($player->current_game->id).'
AND `turn` = '.mysql_ureal_escape_string($player->current_game->current_turn + 1).'
AND `territory_id` = '.mysql_ureal_escape_string($territory->id);
          mysql_uquery($sql);

          $player->current_game->set_player_history(
            $player->id,
            $player->current_game->current_turn + 1,
            time(),
            "Your capital has been successfully moved",
            $territory->id
          );
          $return_code = 0;
        }else {
          $player->current_game->set_player_history(
            $player->id,
            $player->current_game->current_turn + 1,
            time(),
            "You don't own the territory, capital moving cancelled !",
            $territory->id
          );
          $return_code = 1;
        }
      }else {
        $return_code = 2;
      }
    }else {
      $return_code = 3;
    }

    $this->datetime_execution = time();
    $this->turn_executed = $player->current_game->current_turn + 1;
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
      $title = __($title. ' to %s', $params['territory']->name);
    }else {
      $title = __($title);
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
    <legend>'.$title.'</legend>
    <p>'.__('Moving your capital takes two turns, and may be cancelled if you don\'t own the territory on the execution turn').'</p>';
    $return .= '
    '.HTMLHelper::genererInputHidden('url_return', Page::get_page_url( $params['page_code'], true, $page_params ) );
    if( isset( $params['territory'] ) ) {
      if( !isset( $territory_list[ $params['territory']->id ]) ) {
        $return .= '
    <p>'.__('Warning : You don\'t own this territory right now').'</p>';
      }
      $return .= '
    '.HTMLHelper::genererInputHidden('parameters[territory_id]', $params['territory']->id);
    }else {
      $return .= '
    <p>'.HTMLHelper::genererSelect( 'parameters[territory_id]', $territory_list, null, array(), __('Move capital to') ).'</p>';
    }
    $return .= '
    <p>'.HTMLHelper::genererButton( 'action', 'change_capital', array('type' => 'submit'), __('Move, move, move !') ).'</p>
  </fieldset>
</form>';

    return $return;
  }
}
?>