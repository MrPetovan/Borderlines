<?php
  class Train_Spy extends Player_Order {
    public function execute() {
      $return = false;
      
      $return_code = -1;
      
      $player = Player::instance( $this->get_player_id() );
      $parameters = unserialize( $this->get_parameters() );
      
      if( isset( $parameters['count'] ) ) {
        $budget_spent = $parameters['count'];
        
        $return_code = 0;
        
        $available_budget = $player->get_resource_sum( 5 );
        if( $available_budget < $budget_spent ) {
          $budget_spent = $available_budget;
          
          $return_code = 1;
        }
        
        $spies_trained = $budget_spent * 1;
        
        $message = 'Training '.$spies_trained.' spies for -'.$budget_spent;
        
        $player->set_player_resource_history( 5, guess_date( mktime(), GUESS_DATE_MYSQL ), - $budget_spent, $message, $this->get_id() );
        $player->set_player_resource_history( 3, guess_date( mktime(), GUESS_DATE_MYSQL ), $spies_trained, $message, $this->get_id() );
        
        $return = true;
      }
      
      $this->datetime_execution = time();
      $this->return = $return_code;
      $this->db_save();
      
      return $return;
    }
    
    public static function get_html_form( $params ) {
      $title = 'Train spies';

      $page_params = array();
      if( isset( $params['page_params'] ) ) {
        $page_params = $params['page_params'];
      }
      
      $return = '
<form action="'.Page::get_page_url( 'order' ).'" method="post">
  <fieldset>
    <legend>'.$title.'</legend>
    <p>1 budget spent = 1 soldier trained</p>
    '.HTMLHelper::genererInputHidden('url_return', Page::get_page_url( $params['page_code'], true, $page_params ) ).'
    <p>'.HTMLHelper::genererInputText( 'parameters[count]', 0, array(), 'Intelligence budget increase', null ).'</p>
    <p>'.HTMLHelper::genererButton( 'action', 'train_spy', array('type' => 'submit'), "Train 00 agents" ).'</p>
  </fieldset>
</form>';

      return $return;
    }
  }
?>