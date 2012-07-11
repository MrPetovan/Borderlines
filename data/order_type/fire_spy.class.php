<?php
  class Fire_Spy extends Player_Order {
    public function execute() {
      $return = false;
      
      $return_code = -1;
      
      $player = Player::instance( $this->get_player_id() );
      $parameters = $this->parameters;
      
      if( isset( $parameters['count'] ) ) {
        $game_id = $player->current_game->id;
        $resource_turn = $player->current_game->current_turn;
        $spies_fired = $parameters['count'];
        
        $return_code = 0;
        
        $available_spies = $player->get_resource_sum( 3 );
        if( $available_spies < $spies_fired ) {
          $spies_fired = $available_spies;
          
          $return_code = 1;
        }
        
        $budget_gained = round( $spies_fired * 0.5 );
        
        $message = 'Firing '.$spies_fired.' spies for +'.$budget_gained;
        
        $player->set_player_resource_history( $game_id, 3, $resource_turn, guess_time( mktime(), GUESS_DATE_MYSQL ), - $spies_fired, $message, $this->get_id() );
        $player->set_player_resource_history( $game_id, 5, $resource_turn, guess_time( mktime(), GUESS_DATE_MYSQL ), $budget_gained, $message, $this->get_id() );
        
        $return = true;
      }
      
      $this->datetime_execution = time();
      $this->return = $return_code;
      $this->db_save();
      
      return $return;
    }
    
    /**
     * Generate HTML form for the action
     * Mandatory parameters :
     * - page_code (string) : The page code where the form is displayed
     * Optional parameters :
     * - page_params (array) : Current page parameters where the form is displayed
     */
    public static function get_html_form( $params ) {
      $title = 'Dismiss spies';
      
      $page_params = array();
      if( isset( $params['page_params'] ) ) {
        $page_params = $params['page_params'];
      }
      
      $return = '
<form action="'.Page::get_page_url( 'order' ).'" method="post">
  <fieldset>
    <legend>'.$title.'</legend>
    <p>2 spies fired = 1 budget earned</p>
    '.HTMLHelper::genererInputHidden('url_return', Page::get_page_url( $params['page_code'], true, $page_params ) ).'
    <p>'.HTMLHelper::genererInputText( 'parameters[count]', 0, array(), 'Intelligence budget cutback', null ).'</p>
    <p>'.HTMLHelper::genererButton( 'action', 'fire_spy', array('type' => 'submit'), "Find a job for Mr. Bond" ).'</p>
  </fieldset>
</form>';

      return $return;
    }
  }
?>