<?php
class Move_Troops extends Player_Order {
  public function plan( Order_Type $order_type, Player $player, $params ) {
    $valid = isset( $params['from_territory_id'] ) && isset( $params['to_territory_id'] ) && isset( $params['count'] );
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
      $territory = Territory::instance($params['to_territory_id']);
      $valid = $territory->id !== null;
    }
    if( $valid ) {
      $valid = count( $territory->get_territory_neighbour_list( $params['from_territory_id'] ) ) > 0;
    }
    if( $valid ) {
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
      $valid = $this->save();
    }
    return $valid;
  }

  public function execute( array &$intermediate_troops_array ) {
    $return = false;

    $return_code = -1;

    /* @var $player Player */
    $player = Player::instance( $this->player_id );

    $parameters = $this->parameters;
    if( isset( $parameters['count'] ) && isset( $parameters['from_territory_id'] ) && isset( $parameters['to_territory_id'] ) ) {

      /* @var $from_territory Territory */
      /* @var $to_territory Territory */
      $from_territory = Territory::instance( $parameters['from_territory_id'] );
      $to_territory = Territory::instance( $parameters['to_territory_id'] );

      if( $from_territory && $to_territory ) {
        $game_id = $player->current_game->id;

        if( self::check_move($from_territory, $to_territory, $player, $player->current_game) ) {
          $order_turn = $player->current_game->current_turn;
          $next_turn = $player->current_game->current_turn + 1;

          $from_troops_before = $player->get_territory_player_troops_list( $game_id, $next_turn, $from_territory->id );

          if( count( $from_troops_before ) && isset( $intermediate_troops_array[ $from_territory->id ][ $player->id ] ) && $intermediate_troops_array[ $from_territory->id ][ $player->id ] > 0 ) {
            $from_troops_before = $from_troops_before[0]['quantity'];

            // Capping with actual number of soldiers before any moves
            $parameters['count'] = min( $intermediate_troops_array[ $from_territory->id ][ $player->id ], $parameters['count']);
            $intermediate_troops_array[ $from_territory->id ][ $player->id ] -= $parameters['count'];

            $from_troops_after = $from_troops_before - $parameters['count'];
            if( $from_troops_after > 0 ) {
              $player->set_territory_player_troops( $game_id, $next_turn, $from_territory->id, $from_troops_after );
            }else {
              $player->del_territory_player_troops( $game_id, $next_turn, $from_territory->id );
            }

            $to_troops_before = $player->get_territory_player_troops_list( $game_id, $next_turn, $to_territory->id );
            if( count( $to_troops_before ) ) {
              $to_troops_before = $to_troops_before[0]['quantity'];
            }else {
              $to_troops_before = 0;
            }

            $to_troops_after = $to_troops_before + $parameters['count'];
            if( $to_troops_after > 0 ) {
              $player->set_territory_player_troops( $game_id, $next_turn, $to_territory->id, $to_troops_after );
            }else {
              $player->del_territory_player_troops( $game_id, $next_turn, $to_territory->id );
            }
            $return = true;
            $return_code = 0;
          }else {
            // Illegal move
            $return_code = 4;
          }
        }else {
          // No troops available for move
          $return_code = 3;
        }
      }else {
        // Unexisting territories
        $return_code = 2;
      }
    }else {
      // Missing parameters
      $return_code = 1;
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
    $title = 'Move your troops';

    $player = $params['current_player'];
    $game = $player->current_game;

    if( isset( $params['from_territory'] ) ) {
      $title .= ' from %s';
      $territory_name = $params['from_territory']->name;
      $image = 'territory_out.png';
    }
    if( isset( $params['to_territory'] ) ) {
      $title .= ' to %s';
      $territory_name = $params['to_territory']->name;
      $image = 'territory_in.png';
    }

    $page_params = array();
    if( isset( $params['page_params'] ) ) {
      $page_params = $params['page_params'];
    }

    $neighbour_list = array();

    if( isset( $params['to_territory'] ) && !isset( $params['from_territory'] ) ) {
      $territory_neighbour_list = $params['to_territory']->get_territory_neighbour_list();
      $territory_occupied_list = $params['current_player']->get_territory_player_troops_list( $game->id, $game->current_turn );

      foreach( $territory_neighbour_list as $neighbour_array ) {
        $neighbour = Territory::instance( $neighbour_array['neighbour_id'] );
        $neighbour_list[ $neighbour->id ] = $neighbour->name;
        // No troops in neighbour territory
        $neighbour_status[ $neighbour->id ] = 1;

        foreach( $territory_occupied_list as $territory_occupied_array ) {
          if( $territory_occupied_array['territory_id'] == $neighbour_array['neighbour_id'] ) {
            $neighbour_status[ $neighbour->id ] = 0;
            $neighbour_list[ $neighbour->id ] = $neighbour->name. ' ('.l10n_number($territory_occupied_array['quantity']).' <img src="'.IMG.'img_html/helmet.png" alt="'.__('Troops').'" title="'.__('Troops').'"/>)';
            break;
          }
        }
      }
    }

    if( isset( $params['from_territory'] ) && !isset( $params['to_territory'] ) ) {
      $territory_neighbour_list = $params['from_territory']->get_territory_neighbour_list();
      $troops = $params['current_player']->get_territory_player_troops_list( $game->id, $game->current_turn, $params['from_territory']->id);
      if( count( $troops ) ) {
        foreach( $territory_neighbour_list as $neighbour_array ) {
          $neighbour = Territory::instance( $neighbour_array['neighbour_id'] );
          $neighbour_list[ $neighbour->id ] = $neighbour->name;
          $neighbour_status[ $neighbour->id ] = 0;
          if( !self::check_move($params['from_territory'], $neighbour, $player, $game)) {
            // Move not allowed
            $neighbour_status[ $neighbour->id ] = 2;
          }
        }
      }
    }

    $return = '
<form action="'.Page::get_page_url( 'order' ).'" method="post">
  <fieldset>
    <legend><img src="'.IMG.'img_html/'.$image.'" alt="" /> '.__($title, $territory_name).'</legend>';
    if( count( $neighbour_list ) ) {
      $return .= HTMLHelper::genererInputHidden('url_return', Page::get_page_url( $params['page_code'], true, $page_params ) ).'
    <p>'.HTMLHelper::genererInputText( 'parameters[count]', 0, array(), __('Troop size'), null ).'</p>';
      if( isset( $params['from_territory'] ) ) {
        $return .= '
    '.HTMLHelper::genererInputHidden('parameters[from_territory_id]', $params['from_territory']->id);
      }else {
        $return .= '
    <p>'.__('Move from:').'</p>
    <ul>';

        foreach( $neighbour_list as $neighbour_id => $neighbour_name ) {
          $return .= '
      <li>
        <label'.($neighbour_status[ $neighbour_id ] != 0?' class="disabled"':'').'>
          <input type="radio" name="parameters[from_territory_id]" value="'.$neighbour_id.'"'.($neighbour_status[ $neighbour_id ] != 0?' disabled="disabled"':'').' />
          '.$neighbour_name.'
          '.($neighbour_status[ $neighbour_id ] == 1?' <span>('.__('No troops').')</span>':'').'
        </label>
      </li>';
        }
        $return .= '
    </ul>';
      }
      if( isset( $params['to_territory'] ) ) {
        $return .= '
    '.HTMLHelper::genererInputHidden('parameters[to_territory_id]', $params['to_territory']->id);
      }else {
        $return .= '
    <p>'.__('Move to:').'</p>
    <ul>';

        foreach( $neighbour_list as $neighbour_id => $neighbour_name ) {
          $return .= '
      <li>
        <label'.($neighbour_status[ $neighbour_id ] != 0?' class="disabled"':'').'>
          <input type="radio" name="parameters[to_territory_id]" value="'.$neighbour_id.'"'.($neighbour_status[ $neighbour_id ] != 0?' disabled="disabled"':'').' />
          '.$neighbour_name.'
          '.($neighbour_status[ $neighbour_id ] == 2?' <span>('.__('No retreat possible').')</span>':'').'
        </label>
      </li>';
        }
        $return .= '
    </ul>';
      }

    $return .= '
    <p>'.HTMLHelper::genererButton( 'action', 'move_troops', array('type' => 'submit'), __('March!') ).'</p>';
    }else {
      if( isset( $troops ) ) {
        $return .= '
    <p>'.__('You don\'t have any troops in this territory').'</p>';
      }else {
        $return .= '
    <p>'.__('You don\'t have any troops in neighbouring territories').'</p>';
      }
    }
    $return .= '
  </fieldset>
</form>';

    return $return;
  }

  public static function check_move(Territory $from_territory, Territory $to_territory, Player $player, Game $game) {
    // Retreat only computing : troops in enemy territory can't move to another enemy territory
    $allow_move = !$from_territory->is_contested($game->id);
    // Owning target territory or territory empty = pass
    if( !$allow_move ) {
      $to_owner_id = $to_territory->get_owner($game->id);
      $allow_move = $to_owner_id === null || $to_owner_id == $player->id;
    }
    // Marking target territory player as ally = pass
    if( !$allow_move  ) {
      $player_diplomacy = array_pop( $player->get_player_diplomacy_list($game->id, null, $to_owner_id) );
      $allow_move = $player_diplomacy['status'] == 'Ally';
    }
    return $allow_move;
  }
}
?>