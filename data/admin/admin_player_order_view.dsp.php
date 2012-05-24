<?php
  include_once('data/static/html_functions.php');

  $player_order = Player_Order::instance( getValue('id') );

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url($PAGE_CODE).'&id='.$player_order->get_id();
  $PAGE_TITRE = 'Player Order : Consultation de "'.$player_order->get_id().'"';
?>
<div class="texte_contenu">
<?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Consultation des données pour "<?php echo $player_order->get_id()?>"</h3>
    <div class="informations formulaire">

<?php
      $option_list = array();
      $order_type_list = Order_Type::db_get_all();
      foreach( $order_type_list as $order_type)
        $option_list[ $order_type->id ] = $order_type->name;
?>
      <p class="field">
        <span class="libelle">Order Type Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_order_type_view', true, array('id' => $player_order->get_order_type_id() ) )?>"><?php echo $option_list[ $player_order->get_order_type_id() ]?></a></span>
      </p>

<?php
      $option_list = array();
      $player_list = Player::db_get_all();
      foreach( $player_list as $player)
        $option_list[ $player->id ] = $player->name;
?>
      <p class="field">
        <span class="libelle">Player Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_player_view', true, array('id' => $player_order->get_player_id() ) )?>"><?php echo $option_list[ $player_order->get_player_id() ]?></a></span>
      </p>

            <p class="field">
              <span class="libelle">Datetime Order</span>
              <span class="value"><?php echo $player_order->get_datetime_order()?></span>
            </p>
            <p class="field">
              <span class="libelle">Datetime Scheduled</span>
              <span class="value"><?php echo $player_order->get_datetime_scheduled()?></span>
            </p>
            <p class="field">
              <span class="libelle">Datetime Execution</span>
              <span class="value"><?php echo $player_order->get_datetime_execution()?></span>
            </p>
            <p class="field">
              <span class="libelle">Parameters</span>
              <span class="value"><?php echo $player_order->get_parameters()?></span>
            </p>    </div>
    <p><a href="<?php echo get_page_url('admin_player_order_mod', true, array('id' => $player_order->get_id()))?>">Modifier cet objet Player Order</a></p>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_player_order')?>">Revenir à la liste des objets Player Order</a></p>
  </div>
</div>