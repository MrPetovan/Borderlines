<?php
  $allowed = false;
  $success = false;
  $content = null;

  $method = getValue('m', '', true);
  $api_token = null;

  $compare_sig = true;

  if( $method == '' ) {
    $allowed = true;
    $success = true;
    $content = array(
        'request_token'                     => array('player_id' => 'Player Id', 'sig' => 'Signature = sha1([player_id][api_key])'),
        'get_game'                          => array('game_id' => 'Game Id'),
        'get_current_game'                  => array(),
        'get_last_game'                     => array(),
        'get_world'                         => array('[world_id]' => 'World Id (optional)'),
        'get_territory'                     => array('territory_id' => 'Territory Id'),
        'get_last_player_diplomacy_list'    => array(),
        'change_diplomacy_status'           => array('player_id' => 'Player Id', 'status' => 'New diplomatic status (Ally/Neutral/Enemy)'),
        'ready'                             => array(),
        'notready'                          => array(),
        'get_game_player_list'              => array('[game_id]' => 'Game Id (optional)'),
        'get_territory_summary'             => array('[turn]' => 'Turn (optional)'),
        'get_territory_status_list'          => array('[turn]' => 'Turn (optional)'),
        'get_territory_player_troops_list'  => array('[turn]' => 'Turn (optional)'),
        'get_player_history_list'           => array(),
        'get_shout_list'                    => array('[game_related]' => 'Game related (optional)'),
        'get_territory_owner'               => array('territory_id' => 'Territory Id', '[turn]' => 'Turn (optional)'),
        'get_territory_is_contested'        => array('territory_id' => 'Territory Id', '[turn]' => 'Turn (optional)'),
        'get_territory_is_capital'          => array('territory_id' => 'Territory Id', '[turn]' => 'Turn (optional)'),
        'get_territory_neighbour_list'      => array('territory_id' => 'Territory Id'),
        'get_territory_list'                => array('[world_id]' => 'World Id (optional)', '[game_id]' => 'Game Id (optional)', '[turn]' => 'Turn (optional)', '[sort_field]' => 'Sort field (name/owner)(optional)', '[sort_direction]' => 'Sort Direction (1/0)(optional)'),
        'move_troops'                       => array('from_territory_id' => 'Source Territory Id', 'to_territory_id' => 'Destination Territory Id', 'count' => 'Number of troops moved'),
        'give_troops'                       => array('from_territory_id' => 'Source Territory Id', 'to_player_id' => 'Recipient Player Id', 'count' => 'Number of troops gifted'),
        'give_tribute'                      => array('to_player_id' => 'Recipient Player Id', 'count' => 'Tribute amount'),
        'change_capital'                    => array('territory_id' => 'Destination Territory Id'),
        'quit_game'                         => array(),
        'get_planned_orders'                => array(),
        'cancel_order'                      => array('planned_order_id' => 'Planned Order Id'),
    );
  }elseif( $method == 'request_token' ) {
    $player_id = getValue('player_id');
    $sig = getValue('sig');

    $api_token = Api_Token::request_token($player_id, $compare_sig, $sig);

    if( $api_token ) {
      $success = true;
      $allowed = true;
      $content = $api_token->get_public_vars();
    }
  }else {
    $token = getValue('token', null, true);
    $api_token = Api_Token::db_get_by_hash($token);

    if( !$compare_sig || ($api_token && $api_token->hash == $token && $api_token->expires > time() ) ) {
      $allowed = true;
      /* @var $current_player Player */
      $current_game = null;
      if( $api_token && $api_token->player_id ) {
        $current_player = Player::instance($api_token->player_id);
        $current_game = $current_player->last_game;
      }

      switch( $method ) {
        case 'get_game' : {
          $current_game = Game::instance(getValue('game_id'));
          if( $current_game && $current_game->id ) {
            $success = true;
            $content = $current_game->get_public_vars();
          }
          break;
        }
        case 'get_current_game' : {
          if( $current_game && $current_game->id ) {
            $success = true;
            $content = $current_game->get_public_vars();
          }
          break;
        }
        case 'get_last_game' : {
          $current_game = $current_player->get_last_game();
          if( $current_game && $current_game->id ) {
            $success = true;
            $content = $current_game->get_public_vars();
          }
          break;
        }
        case 'get_world' : {
          $world = null;
          if( $world_id = getValue('world_id') ) {
            $world = World::instance($world_id);
          }else {
            if( $current_game ) {
              $world = World::instance( $current_game->world_id );
            }
          }
          if( $world ) {
            $success = true;
            $content = $world->get_public_vars();
          }
          break;
        }
        case 'get_territory' : {
          $territory = Territory::instance(getValue('territory_id'));
          if( $territory->id ) {
            $success = true;
            $content = $territory->get_public_vars();
          }
          break;
        }
        case 'get_order_type' : {
          $order_type = Order_Type::instance(getValue('order_type_id'));
          if( $order_type->id ) {
            $success = true;
            $content = $order_type->get_public_vars();
          }
          break;
        }
        case 'ready' : {
          if( $current_game && !$current_game->has_ended() ) {
            $success = $current_player->set_game_player( $current_game->id, $current_game->current_turn + 1 );
          }
          break;
        }
        case 'notready' : {
          if( $current_game && !$current_game->has_ended() ) {
            $success = $current_player->set_game_player( $current_game->id, $current_game->current_turn );
          }
          break;
        }
        case 'get_last_player_diplomacy_list' : {
          if( $current_game ) {
            $content = $current_player->get_player_latest_diplomacy_list($current_game->id, $current_game->current_turn );
          }
          break;
        }
        case 'change_diplomacy_status' : {
          if( $current_game && !$current_game->has_ended() ) {
            $success = $current_player->set_player_diplomacy( $current_game->id, $current_game->current_turn, getValue('player_id'), getValue('status'));
          }
          break;
        }
        case 'get_game_player_list' : {
          if( $game_id = getValue('game_id') ) {
            $current_game = Game::instance($game_id);
          }
          if( $current_game->id ) {
            $success = true;
            $content = $current_game->get_game_player_list();
          }
          break;
        }
        case 'get_territory_summary' : {
          if( !$turn = getValue('turn') ) {
            $turn = $current_game->current_turn;
          }
          if( $current_game->id ) {
            $success = true;
            $content = $current_player->get_territory_summary($current_game->id, $turn);
          }
          break;
        }
        case 'get_territory_status_list' : {
          if( !$turn = getValue('turn') ) {
            $turn = $current_game->current_turn;
          }
          if( $current_game->id ) {
            $success = true;
            $content = $current_player->get_territory_status_list(null, $current_game->id, $turn);
          }
          break;
        }
        case 'get_territory_player_troops_list' : {
          if( !$turn = getValue('turn') ) {
            $turn = $current_game->current_turn;
          }
          if( $current_game->id ) {
            $success = true;
            $content = $current_game->get_territory_player_troops_list( $turn, null, $current_player->id );
          }
          break;
        }
        case 'get_player_history_list' : {
          if( $current_game->id && !$current_game->has_ended() ) {
            $success = true;
            $content = $current_player->get_player_history_list($current_game->id);
          }
          break;
        }
        case 'get_planned_orders' : {
          if( $current_game->id && !$current_game->has_ended() ) {
            $success = true;
            $orders = Player_Order::db_get_planned_by_player_id( $current_player->id, $current_game->id );
            $content = array();
            foreach( $orders as $order ) {
              $content[] = $order->get_public_vars();
            }
          }
          break;
        }
        case 'get_shout_list' : {
          $game_id = isset($_REQUEST['game_related'])?$current_game->id:null;
          $success = true;
          $shouts = Shout::db_get_by_game_id( $game_id );
          $content = array();
          foreach( $shouts as $shout ) {
            $content[] = $shout->get_public_vars();
          }
          break;
        }
        case 'get_territory_owner' : {
          if( !$turn = getValue('turn') ) {
            $turn = $current_game->current_turn;
          }
          $territory = Territory::instance(getValue('territory_id'));
          if( $territory->id && $current_game && $current_game->id ) {
            $success = true;
            $content = $territory->get_owner( $current_game, $turn );
          }
          break;
        }
        case 'get_territory_is_contested' : {
          if( !$turn = getValue('turn') ) {
            $turn = $current_game->current_turn;
          }
          $territory = Territory::instance(getValue('territory_id'));
          if( $territory->id && $current_game && $current_game->id ) {
            $success = true;
            $content = $territory->is_contested( $current_game, $turn );
          }
          break;
        }
        case 'get_territory_is_capital' : {
          if( !$turn = getValue('turn') ) {
            $turn = $current_game->current_turn;
          }
          $territory = Territory::instance(getValue('territory_id'));
          if( $territory->id && $current_game && $current_game->id ) {
            $success = true;
            $content = $territory->is_capital( $current_game, $turn );
          }
          break;
        }
        case 'get_territory_neighbour_list' : {
          $territory = Territory::instance(getValue('territory_id'));
          if( $territory->id ) {
            $success = true;
            $neighbours = $territory->get_territory_neighbour_list();
            $content = array();
            foreach( $neighbours as $neighbour_row ) {
              $neighbour = Territory::instance($neighbour_row['neighbour_id']);
              $content[] = $neighbour->get_public_vars();
            }
          }
          break;
        }
        case 'get_territory_list' : {
          $game_id = getValue('game_id');
          $turn = getValue('turn');
          $world_id = getValue('world_id');
          $sort_field = getValue('sort_field', 'name');
          $sort_direction = getValue('sort_direction', 1);
          $world = null;
          if( $game_id ) {
            $current_game = Game::instance($game_id);
          }elseif( $world_id === null ) {
            if( $current_game->world_id ) {
              $world_id = $current_game->world_id;

              if( $turn === null ) {
                $turn = $current_game->current_turn;
              }
            }
          }else {
            $current_game = null;
          }
          if( $world_id ) {
            $world = World::instance($world_id);
          }

          if( $world ) {
            $success = true;
            $territory_list = Territory::get_by_world($world, $current_game, $turn, $sort_field, $sort_direction);
            $content = array();
            foreach( $territory_list as $territory ) {
              $content[] = $territory->get_public_vars();
            }
          }
          break;
        }
        case 'move_troops' :
        case 'give_troops' :
        case 'give_tribute' :
        case 'change_capital' :
        case 'quit_game' :
        {
          if( $current_game->id && !$current_game->has_ended() ) {
            $parameters = getValues();

            $order_type = Order_Type::db_get_by_class_name($method);
            $player_order = Player_Order::factory_by_class($method);

            $success = $player_order->plan( $order_type, $current_player, $parameters );

            $content = array('planned_order_id' => $player_order->id);
          }
          break;
        }
        case 'cancel_order' : {
          $player_order_id = getValue('planned_order_id');
          $player_order = Player_Order::instance( $player_order_id );

          if( $player_order && $player_order->id && $current_player->id == $player_order->player_id ) {
            $player_order = Player_Order::factory($player_order->order_type_id, $player_order->id);

            $success = $player_order->cancel();
          }
          break;
        }
      }
    }
  }

  if( !$api_token ) {
    /* @var $api_token Api_Token */
    $api_token = Api_Token::instance();
  }

  $api_token->set_api_log($method, str_replace("\n", '&', trim(parameters_to_string(getValues()))), $allowed?1:0, $success?1:0, time());
?>
