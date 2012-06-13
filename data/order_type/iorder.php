<?php
  interface IOrder {
    public function plan( $player, $params );
    public function execute();
    public function cancel();
    public static function get_html_form( $params ) ;
  }
?>