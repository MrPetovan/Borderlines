<?php
  interface IOrder {
    public function execute();
    //public function cancel(  );
    public function plan( $player, $params );
  }
?>