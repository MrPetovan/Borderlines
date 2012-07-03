<?php
  class Fire_Soldiers extends Player_Order {
    public function execute() {
      $return = false;
      
      $return_code = -1;
      
      $player = Player::instance( $this->get_player_id() );
      $parameters = unserialize( $this->get_parameters() );
      
      if( isset( $parameters['count'] ) ) {
        $game_id = $player->current_game->id;
        $resource_turn = $player->current_game->current_turn;
        $soldiers_fired = $parameters['count'];
        
        $return_code = 0;
        
        $available_soldiers = $player->get_resource_sum( 2 );
        if( $available_soldiers < $soldiers_fired ) {
          $soldiers_fired = $available_soldiers;
          
          $return_code = 1;
        }
        
        $budget_gained = round( $soldiers_fired * 0.5 );
        
        $message = 'Firing '.$soldiers_fired.' soldiers for +'.$budget_gained;
        
        $player->set_player_resource_history( $game_id, 2, $resource_turn, guess_time( mktime(), GUESS_DATE_MYSQL ), - $soldiers_fired, $message, $this->get_id() );
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
      $title = 'Dismiss soldiers';

      $page_params = array();
      if( isset( $params['page_params'] ) ) {
        $page_params = $params['page_params'];
      }
      
      $return = '
<form action="'.Page::get_page_url( 'order' ).'" method="post">
  <fieldset>
    <legend>'.$title.'</legend>
    <p>2 soldiers fired = 1 budget earned</p>
    '.HTMLHelper::genererInputHidden('url_return', Page::get_page_url( $params['page_code'], true, $page_params ) ).'
    <p>'.HTMLHelper::genererInputText( 'parameters[count]', 0, array(), 'Military budget cutback', null ).'</p>
    <p>'.HTMLHelper::genererButton( 'action', 'fire_soldiers', array('type' => 'submit'), "Cut military budget" ).'</p>
  </fieldset>
</form>';

      return $return;
    }
  }
?>