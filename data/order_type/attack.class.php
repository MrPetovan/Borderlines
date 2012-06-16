<?php
class Attack extends Player_Order {
  public function plan( Order_Type $order_type, Player $player, $params ) {
    parent::plan( $order_type, $player, $params );

    $soldiers_sent = $params['count'];
    $attacking_player = Player::instance( $this->player_id );
    $defending_player = Player::instance( $params['player_id'] );

    $available_soldiers = $attacking_player->get_resource_sum( 2 );
    if( $available_soldiers < $soldiers_sent ) {
      $soldiers_sent = $available_soldiers;
      
      $params['count'] = $soldiers_sent;
    }
    $message = 'Sending '.$soldiers_sent.' soldiers to attack '.$defending_player->name;
    $attacking_player->set_player_resource_history( $attacking_player->current_game->id, 2, $attacking_player->current_game->current_turn, guess_time( mktime(), GUESS_DATE_MYSQL ), - $soldiers_sent, $message, $this->id );
    
    $this->parameters = serialize( $params );
    
    return $this->save();
  }

  public function execute() {
    $return = false;
    
    $return_code = -1;
    
    $attacking_player = Player::instance( $this->get_player_id() );
    
    $parameters = unserialize( $this->get_parameters() );
    
    if( isset( $parameters['count'] ) && isset( $parameters['player_id'] ) ) {
    
      $defending_player = Player::instance( $parameters['player_id'] );
      
      if( $defending_player ) {
        $game_id = $attacking_player->current_game->id;
        $resource_turn = $attacking_player->current_game->current_turn + 1;
      
      
        $return_code = 0;

        $soldiers_sent = $parameters['count'];
        
        $soldiers_defending = $defending_player->get_resource_sum( 2 );
        $territory_defended = $defending_player->get_resource_sum( 4 );
        
        $attacker_efficiency = (mt_gaussrand() * 0.1 + 1) * 0.1;
        
        $defender_losses = round( $attacker_efficiency * $soldiers_sent );
        
        // Pas de défenseurs
        if( $soldiers_defending == 0 ) {
          $attacker_losses = 0;
          $territory_gained = min( $defender_losses, $territory_defended );
          $defender_losses = 0;
        }else {
          $defender_efficiency = (mt_gaussrand() * 0.1 + 1) * 0.1 + ( $soldiers_defending / $territory_defended / 10 );

          $attacker_losses = round( $defender_efficiency * $soldiers_defending );
          
          $territory_gained = min( $territory_defended, round( $territory_defended * ( $defender_losses / $soldiers_defending ) ) );
        }
        
        /*var_debug(
          $soldiers_sent,
          $soldiers_defending,
          $territory_defended,
          $attacker_efficiency,
          $defender_efficiency,
          $attacker_losses,
          $defender_losses,
          $territory_gained
        );*/
        $soldiers_returned = $soldiers_sent - $attacker_losses;
        
        $attacker_message = 'Attacking '.$defending_player->get_name().' with '.$soldiers_sent.' soldiers : '.$attacker_losses.' losses, '.$soldiers_returned.' returned, '.$territory_gained.' territory gained';
        $defender_message = 'Defending against '.$attacking_player->get_name().' with '.$soldiers_defending.' soldiers : '.$defender_losses.' losses, '.$territory_gained.' territory lost';

        $attacking_player->set_player_resource_history( $game_id, 2, $resource_turn, guess_time( mktime(), GUESS_DATE_MYSQL ), max( 0, $soldiers_returned ), $attacker_message, $this->get_id() );
        $attacking_player->set_player_resource_history( $game_id, 4, $resource_turn, guess_time( mktime(), GUESS_DATE_MYSQL ), $territory_gained, $attacker_message, $this->get_id() );
        $defending_player->set_player_resource_history( $game_id, 2, $resource_turn, guess_time( mktime(), GUESS_DATE_MYSQL ), - min( $defender_losses, $soldiers_defending ), $defender_message, $this->get_id() );
        $defending_player->set_player_resource_history( $game_id, 4, $resource_turn, guess_time( mktime(), GUESS_DATE_MYSQL ), - $territory_gained, $defender_message, $this->get_id() );
        
        $return = true;
      }
    }
    
    $this->datetime_execution = time();
    $this->return = $return_code;
    $this->db_save();
    
    return $return;
  }
  
  /**
   * Generate HTML form for the action
   * Mandatory parameters :
   * - current_player (Player) : The player attacking
   * - page_code (string) : The page code where the form is displayed
   * Optional parameters :
   * - target_player (Player) : The player attacked
   * - page_params (array) : Current page parameters where the form is displayed
   */
  public static function get_html_form( $params ) {
    $title = 'Attack a player';
    
    if( isset( $params['target_player'] ) ) {
      $title = 'Attack '.$params['target_player']->get_name();
    }
    
    $page_params = array();
    if( isset( $params['page_params'] ) ) {
      $page_params = $params['page_params'];
    }
    
    $game = $params['current_player']->get_current_game();
    $game_player_list = $game->get_game_player_list( );
    foreach( $game_player_list as $game_player ) {
      if( $game_player['player_id'] == $params['current_player']->id ) continue;
      $player = Player::instance( $game_player['player_id'] );
      $player_list[  $player->id ] = $player->name;
    }
    $return = '
<form action="'.Page::get_page_url( 'order' ).'" method="post">
  <fieldset>
    <legend>'.$title.'</legend>
    '.HTMLHelper::genererInputHidden('url_return', Page::get_page_url( $params['page_code'], true, $page_params ) ).'
    <p>'.HTMLHelper::genererInputText( 'parameters[count]', 0, array(), 'Invading army size', null ).'</p>';
    if( !isset( $params['target_player'] ) ) {
      $return .= '
    <p>'.HTMLHelper::genererSelect( 'parameters[player_id]', $player_list, null, array(), 'Target player' ).'</p>';
    }else {
      $return .= '
    '.HTMLHelper::genererInputHidden('parameters[player_id]', $params['target_player']->get_id());
    }
    $return .= '
    <p>'.HTMLHelper::genererButton( 'action', 'attack', array('type' => 'submit'), "Charge !" ).'</p>
  </fieldset>
</form>';

    return $return;
  }
}
?>