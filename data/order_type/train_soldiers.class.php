<?php
  class Train_Soldiers extends Player_Order implements IOrder {
    public function execute() {
      $return = false;
      
      $player = Player::instance( $this->get_player_id() );
      $parameters = unserialize( $this->get_parameters() );
      
      if( isset( $parameters['count'] ) ) {
        $player->set_player_resource_history( 1, guess_date( mktime(), GUESS_DATE_MYSQL ), - $parameters['count'], 'Entraînement ('.$parameters['count'].')' );
        $player->set_player_resource_history( 2, guess_date( mktime(), GUESS_DATE_MYSQL ), $parameters['count'], 'Entraînement ('.$parameters['count'].')' );
        
        $return = true;
      }
      
      return $return;
    }
    
    public function plan( $player, $params ) {}
  }
?>