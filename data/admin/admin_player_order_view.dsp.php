<?php

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url(PAGE_CODE).'&id='.$player_order->id;
  $PAGE_TITRE = 'Player Order : Showing "'.$player_order->id.'"';
?>
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Showing "<?php echo $player_order->id?>"</h3>
    <div class="informations formulaire">

<?php
      $option_list = array();
      $game_list = Game::db_get_all();
      foreach( $game_list as $game)
        $option_list[ $game->id ] = $game->name;
?>
      <p class="field">
        <span class="libelle">Game Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_game_view', true, array('id' => $player_order->game_id ) )?>"><?php echo $option_list[ $player_order->game_id ]?></a></span>
      </p>

<?php
      $option_list = array();
      $order_type_list = Order_Type::db_get_all();
      foreach( $order_type_list as $order_type)
        $option_list[ $order_type->id ] = $order_type->name;
?>
      <p class="field">
        <span class="libelle">Order Type Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_order_type_view', true, array('id' => $player_order->order_type_id ) )?>"><?php echo $option_list[ $player_order->order_type_id ]?></a></span>
      </p>

<?php
      $option_list = array();
      $player_list = Player::db_get_all();
      foreach( $player_list as $player)
        $option_list[ $player->id ] = $player->name;
?>
      <p class="field">
        <span class="libelle">Player Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_player_view', true, array('id' => $player_order->player_id ) )?>"><?php echo $option_list[ $player_order->player_id ]?></a></span>
      </p>

            <p class="field">
              <span class="libelle">Datetime Order</span>
              <span class="value"><?php echo guess_time($player_order->datetime_order, GUESS_DATETIME_LOCALE)?></span>
            </p>
            <p class="field">
              <span class="libelle">Datetime Execution</span>
              <span class="value"><?php echo guess_time($player_order->datetime_execution, GUESS_DATETIME_LOCALE)?></span>
            </p>
            <p class="field">
              <span class="libelle">Turn Ordered</span>
              <span class="value"><?php echo is_array($player_order->turn_ordered)?nl2br(parameters_to_string( $player_order->turn_ordered )):$player_order->turn_ordered?></span>
            </p>
            <p class="field">
              <span class="libelle">Turn Scheduled</span>
              <span class="value"><?php echo is_array($player_order->turn_scheduled)?nl2br(parameters_to_string( $player_order->turn_scheduled )):$player_order->turn_scheduled?></span>
            </p>
            <p class="field">
              <span class="libelle">Turn Executed</span>
              <span class="value"><?php echo is_array($player_order->turn_executed)?nl2br(parameters_to_string( $player_order->turn_executed )):$player_order->turn_executed?></span>
            </p>
            <p class="field">
              <span class="libelle">Parameters</span>
              <span class="value"><?php echo is_array($player_order->parameters)?nl2br(parameters_to_string( $player_order->parameters )):$player_order->parameters?></span>
            </p>
            <p class="field">
              <span class="libelle">Return</span>
              <span class="value"><?php echo is_array($player_order->return)?nl2br(parameters_to_string( $player_order->return )):$player_order->return?></span>
            </p>
<?php
      $option_list = array(null => 'Pas de choix');
      $player_order_list = Player_Order::db_get_all();
      foreach( $player_order_list as $player_order)
        $option_list[ $player_order->id ] = $player_order->name;
?>
      <p class="field">
        <span class="libelle">Parent Player Order Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_player_order_view', true, array('id' => $player_order->parent_player_order_id ) )?>"><?php echo $option_list[ $player_order->parent_player_order_id ]?></a></span>
      </p>
    </div>
    <p><a href="<?php echo get_page_url('admin_player_order_mod', true, array('id' => $player_order->id))?>">Modifier cet objet Player Order</a></p>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_player_order')?>">Revenir à la liste des objets Player Order</a></p>
  </div>
</div>