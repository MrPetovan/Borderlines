<?php
class Move_Troops extends Player_Order {
  public function plan( Order_Type $order_type, Player $player, $params, $turn = null, $player_order_id = null ) {
    $valid =
      isset( $params['from_territory_id'] )
      && (isset( $params['to_territory_id'] ) || isset( $params['to_future_territory_id'] ))
      && isset( $params['count'] );
    if( $valid ) {
      $locale = localeconv();
      $count = preg_replace('/[^\d\\'.$locale['decimal_point'].']/', '', $params['count']);
      $params['count'] = $count;
      $valid = strval( intval($params['count']) ) === $params['count'] && $params['count'] > 0;
    }
    if( $valid ) {
      $from_territory = Territory::instance($params['from_territory_id']);
      $valid = $from_territory->id !== null;
    }
    if( $valid ) {
      if( isset( $params['to_territory_id'] ) ) {
        $territory_id = $params['to_territory_id'];
      }else {
        $territory_id = $params['to_future_territory_id'];
      }
      $to_territory = Territory::instance( $territory_id );
      $valid = $to_territory->id !== null;
    }
    if( $valid ) {
      $valid = $to_territory->is_passable();
    }
    $game = $player->current_game;

    if( $turn === null && !isset( $params['to_future_territory_id'] ) ) {
      // Standard order

      if( $valid ) {
        $valid = count( $to_territory->get_territory_neighbour_list( $params['from_territory_id'] ) ) > 0;
      }
      if( $valid ) {
        $valid = self::check_move($from_territory, $to_territory, $player, $player->current_game);
      }
      if( $valid ) {
        parent::plan( $order_type, $player, $params, $turn, $player_order_id );

        $player_territory = $game->get_territory_player_troops_list( $game->current_turn, $params['from_territory_id'], $player->id );

        $valid = count( $player_territory ) > 0;
      }
      if( $valid ) {
        $available_soldiers = $player_territory[0]['quantity'];
        if( $available_soldiers < $params['count'] ) {
          $params['count'] = $available_soldiers;
        }

        $this->parameters = $params;

        if( $valid ) {
          $valid = $this->save();
        }
      }

    }else {

      // Future order
      if( $turn !== null ) {

        if( $valid ) {
          Page::add_message('- <a href="'.Page::get_url('show_territory', array('game_id' => $game->id, 'id' => $to_territory->id)).'">'.$to_territory->name.'</a>');
          $valid = parent::plan( $order_type, $player, $params, $turn, $player_order_id );
        }

      }else {

        if( $valid ) {
          // Planification
          $path = $game->get_shortest_path($from_territory, $to_territory, $player, $params['avoid_enemies']);

          $turn = $game->current_turn;

          $from = $from_territory->id;
          $previous_player_order_id = null;
          Page::add_message( __('Your troops will take %s turns to get there, going through :', count($path)) );
          foreach( $path as $to ) {
            $params = array(
                'from_territory_id' => $from,
                'to_territory_id' => $to,
                'count' => $params['count']
            );

            $player_order = new self();
            $valid = $player_order->plan($order_type, $player, $params, $turn++, $previous_player_order_id);

            var_debug( $params, $valid );

            $previous_player_order_id = $player_order->id;
            $from = $to;
          }
        }
      }
    }
    return $valid;
  }

  public function execute( array &$intermediate_troops_array ) {
    $return = false;

    $return_code = -1;

    /* @var $player Player */
    $player = Player::instance( $this->player_id );
    $current_game = $player->current_game;
    $game_id = $current_game->id;

    $parameters = $this->parameters;
    $order_turn = $player->current_game->current_turn;
    $next_turn = $player->current_game->current_turn + 1;

    if( isset( $parameters['count'] ) && isset( $parameters['from_territory_id'] ) && isset( $parameters['to_territory_id'] ) ) {
      /* @var $from_territory Territory */
      /* @var $to_territory Territory */
      $from_territory = Territory::instance( $parameters['from_territory_id'] );
      $to_territory = Territory::instance( $parameters['to_territory_id'] );

      if( $from_territory && $to_territory ) {

        if( $to_territory->is_passable() ) {

          if( self::check_move($from_territory, $to_territory, $player, $player->current_game) ) {

            if( isset( $intermediate_troops_array[ $from_territory->id ][ $player->id ] ) && $intermediate_troops_array[ $from_territory->id ][ $player->id ] > 0 ) {
              // Capping with actual number of soldiers before any moves
              $parameters['actual_count'] = min( $intermediate_troops_array[ $from_territory->id ][ $player->id ], $parameters['count']);
              $intermediate_troops_array[ $from_territory->id ][ $player->id ] -= $parameters['actual_count'];

              $player->set_territory_player_troops_history($game_id, $next_turn, $from_territory->id, - $parameters['actual_count'], 'Move from');
              $player->set_territory_player_troops_history($game_id, $next_turn, $to_territory->id, $parameters['actual_count'], 'Move to');

              $return = true;
              if( $parameters['actual_count'] == $parameters['count'] ) {
                $return_code = 0;
              }else {
                $return_code = 6;
              }
            }else {
              // No troops left available for move
              $return_code = 4;
            }
          }else {
            // Illegal move
            $return_code = 3;
          }
        }else {
          // Territory not passable
          $return_code = 5;
        }
      }else {
        // Unexisting territories
        $return_code = 2;
      }
    }else {
      // Missing parameters
      $return_code = 1;
    }

    if( $return_code !== 0 ) {
      $current_player_order_id = $this->id;
      $subsequent_orders = array();
      $orders_modified = 0;
      do {

        if( count( $subsequent_orders ) > 0 ) {
          $player_order = array_pop( $subsequent_orders );

          if( $return_code == 6 ) {
            $local_parameters = $player_order->parameters;
            $local_parameters['count'] = $parameters['actual_count'];
            $player_order->parameters = $local_parameters;
          }else {
            $player_order->return = 7;
            $player_order->turn_executed = $next_turn;
            $player_order->datetime_execution = time();
          }
          $player_order->save();

          $orders_modified++;

          $current_player_order_id = $player_order->id;
        }

        $subsequent_orders = array_merge( $subsequent_orders, Move_Troops::db_get_by_parent_player_order_id($current_player_order_id) );

      }while( count( $subsequent_orders ) > 0 );

      if( $orders_modified > 0 ) {
        if( $return_code == 6 ) {
          $player->set_player_history($game_id, $next_turn, time(), 'Not enough troops to complete an automatic move order, the subsequent '.$orders_modified.' orders have been modified accordingly', $from_territory->id);
        }else {
          $player->set_player_history($game_id, $next_turn, time(), 'Unable to complete an automatic move order, the subsequent '.$orders_modified.' orders have been canceled', $from_territory->id);
        }
      }
    }

    $this->turn_executed = $next_turn;
    $this->datetime_execution = time();
    $this->return = $return_code;
    $this->parameters = $parameters;
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

    $page_params = array();
    if( isset( $params['page_params'] ) ) {
      $page_params = $params['page_params'];
    }

    $status_array = array(
      0 => '',
      1 => '<span>('.__('No troops').')</span>',
      2 => '<span>('.__('No retreat possible').')</span>',
      3 => '<span>('.__('Impassable territory').')</span>'
    );

    if( isset( $params['future'] ) && isset( $params['from_territory'] ) ) {
      $image = 'territory_out_future.png';

      $destination_list = array();
      $destination_id_list = array();
      $destination_status = array();
      $world = World::instance($params['from_territory']->world_id);
      $territory_distance = $world->get_territories_distances_from($params['from_territory']);
      $territory_list = $world->territories;
      $troops = $game->get_territory_player_troops_list( $game->current_turn, $params['from_territory']->id, $params['current_player']->id );
      if( count( $troops ) ) {
        foreach( $territory_list as $territory ) {
          if( $territory != $params['from_territory'] ) {
            if( $territory->is_passable() ) {
              $destination_id_list[ $territory->id ] = $territory->id;
              $destination_list[ $territory->id ] = $territory->name;
              // Impassable territory
              $destination_status[ $territory->id ] = 3;
              // Move allowed
              $destination_status[ $territory->id ] = 0;
            }
          }
        }
        array_multisort($territory_distance, $destination_list, $destination_id_list, $destination_status);
      }

      $return = '
<form action="'.Page::get_page_url( 'order' ).'" method="post">
  <fieldset>
    <legend><img src="'.IMG.'img_html/'.$image.'" alt="" /> '.__($title . ' to a distant territory', $territory_name).'</legend>
    <div class="content">';
      if( $params['from_territory']->is_passable() ) {
        if( count( $destination_list ) && array_search( 0, $destination_status) !== false ) {
          $return .= HTMLHelper::genererInputHidden('url_return', Page::get_page_url( $params['page_code'], true, $page_params ) ).'
      <p>'.HTMLHelper::genererInputText( 'parameters[count]', 0, array(), __('Troop size'), null ).'</p>';
          $return .= '
        '.HTMLHelper::genererInputHidden('parameters[from_territory_id]', $params['from_territory']->id);
          $return .= '
      <p>'.HTMLHelper::genererInputCheckbox('parameters[avoid_enemies]', 1, 1, array(), __('Avoid enemy territories') ).'</p>
      <p>'.__('Move to:').'</p>
      <ul>';

          foreach( $destination_list as $destination_id => $destination_name ) {
            $distance_string = '';
            if( $destination_status[ $destination_id ] == 0 ) {
              if( $territory_distance[ $destination_id ] <= 1 ) {
                $distance_string = __('(%s turn)', $territory_distance[ $destination_id ]);
              }else {
                $distance_string = __('(%s turns)', $territory_distance[ $destination_id ]);
              }
            }
            $return .= '
        <li>
          <label'.($destination_status[ $destination_id ] != 0?' class="disabled"':'').'>
            <input type="radio" name="parameters[to_future_territory_id]" value="'.$destination_id_list[ $destination_id ].'"'.($destination_status[ $destination_id ] != 0?' disabled="disabled"':'').' />
            '.$destination_name.'
            '.$status_array[ $destination_status[ $destination_id ] ].'
            '.$distance_string.'
          </label>
        </li>';
          }
          $return .= '
      </ul>';

        $return .= '
      <p>'.HTMLHelper::genererButton( 'action', 'move_troops', array('type' => 'submit'), __('March!') ).'</p>';
        }else {
          $return .= '
      <p>'.__('You don\'t have any troops in this territory').'</p>';
        }
      }else {
        $return .= '
      <p>'.__('This territory is not passable').'</p>';
      }
      $return .= '
    </div>
  </fieldset>
</form>';
    }else {
      if( isset( $params['to_territory'] ) ) {
        $title .= ' to %s';
        $territory_name = $params['to_territory']->name;
        $image = 'territory_in.png';
      }


      $neighbour_list = array();
      if( isset( $params['to_territory'] ) && !isset( $params['from_territory'] ) ) {
        $territory_neighbour_list = $params['to_territory']->get_territory_neighbour_list();
        $territory_occupied_list = $game->get_territory_player_troops_list( $game->current_turn, null, $params['current_player']->id );

        foreach( $territory_neighbour_list as $neighbour_array ) {
          $neighbour = Territory::instance( $neighbour_array['neighbour_id'] );
          $neighbour_list[ $neighbour->id ] = $neighbour->name;
          // Impassable territory
          $neighbour_status[ $neighbour->id ] = 3;
          if( $neighbour->is_passable() ) {
            // No troops in neighbour territory
            $neighbour_status[ $neighbour->id ] = 1;

            foreach( $territory_occupied_list as $territory_occupied_array ) {
              if( $territory_occupied_array['territory_id'] == $neighbour_array['neighbour_id'] ) {
                if( self::check_move($neighbour, $params['to_territory'], $player, $game)) {
                  $neighbour_status[ $neighbour->id ] = 0;
                  $neighbour_list[ $neighbour->id ] = $neighbour->name. ' ('.l10n_number($territory_occupied_array['quantity']).' <img src="'.IMG.'img_html/troops.png" alt="'.__('Troops').'" title="'.__('Troops').'"/>)';
                }else {
                  // Move not allowed
                  $neighbour_status[ $neighbour->id ] = 2;
                }
                break;
              }
            }
          }
        }
      }

      if( isset( $params['from_territory'] ) && !isset( $params['to_territory'] ) ) {
        $territory_neighbour_list = $params['from_territory']->get_territory_neighbour_list();
        $troops = $game->get_territory_player_troops_list( $game->current_turn, $params['from_territory']->id, $params['current_player']->id );
        if( count( $troops ) ) {
          foreach( $territory_neighbour_list as $neighbour_array ) {
            $neighbour = Territory::instance( $neighbour_array['neighbour_id'] );
            $neighbour_list[ $neighbour->id ] = $neighbour->name;
            // Impassable territory
            $neighbour_status[ $neighbour->id ] = 3;
            if( $neighbour->is_passable() ) {
              // Move allowed
              $neighbour_status[ $neighbour->id ] = 0;
              if( !self::check_move($params['from_territory'], $neighbour, $player, $game)) {
                // Move not allowed
                $neighbour_status[ $neighbour->id ] = 2;
              }
            }
          }
        }
      }

      $return = '
<form action="'.Page::get_page_url( 'order' ).'" method="post">
  <fieldset>
    <legend><img src="'.IMG.'img_html/'.$image.'" alt="" /> '.__($title, $territory_name).'</legend>
    <div class="content">';
      if( ( !isset( $params['to_territory'] ) || $params['to_territory']->is_passable() )
        && ( !isset( $params['from_territory'] ) || $params['from_territory']->is_passable() ) ) {
        if( count( $neighbour_list ) && array_search( 0, $neighbour_status) !== false ) {
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
            '.$status_array[ $neighbour_status[ $neighbour_id ] ].'
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
            '.$status_array[ $neighbour_status[ $neighbour_id ] ].'
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
      }else {
      $return .= '
      <p>'.__('This territory is not passable').'</p>';
      }
    $return .= '
    </div>
  </fieldset>
</form>';
    }

    return $return;
  }

  public static function check_move(Territory $from_territory, Territory $to_territory, Player $player, Game $game) {
    // Retreat only computing : troops with no supremacy can't move to another enemy territory
    $supremacy_from_list = $from_territory->get_territory_player_status_list($game->id, $game->current_turn, $player->id);

    $allow_move = count( $supremacy_from_list ) == 0 || $supremacy_from_list[0]['supremacy'] == 1;
    // Owning target territory = pass
    if( !$allow_move ) {
      $to_owner = $to_territory->get_owner( $game );
      $allow_move = $to_owner == $player;
    }
    // Having the supremacy in target territory = pass
    if( !$allow_move ) {
      $supremacy_to_list = $to_territory->get_territory_player_status_list($game->id, $game->current_turn);
      $supremacy_to = array();
      foreach( $supremacy_to_list as $supremacy_to_row ) {
        $supremacy_to[ $supremacy_to_row['player_id'] ] = $supremacy_to_row['supremacy'];
      }

      $allow_move = isset($supremacy_to[ $player->id ]) && $supremacy_to[ $player->id ] == 1;
    }
    // Empty territory (sea, neutral) = supremacy check
    if( !$allow_move && $to_owner->id === null ) {
      $supremacists = array_keys( $supremacy_to, 1 );
      if( count( $supremacists ) ) {
        $diplomacy_list = $player->get_last_player_diplomacy_list($game->id);
        foreach( $diplomacy_list as $diplomacy_row ) {
          if( $diplomacy_row['status'] == 'Ally' && in_array( $diplomacy_row['to_player_id'], $supremacists ) ) {
            $allow_move = true;
          }
        }
      }else {
        $allow_move = true;
      }
    }
    // Marking target territory player as ally = pass
    if( !$allow_move  ) {
      $player_diplomacy = array_pop( $player->get_player_diplomacy_list($game->id, null, $to_owner->id) );
      $allow_move = $player_diplomacy['status'] == 'Ally';
    }

    return $allow_move;
  }
}
?>