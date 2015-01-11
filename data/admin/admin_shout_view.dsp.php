<?php

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url(PAGE_CODE).'&id='.$shout->id;
  $PAGE_TITRE = 'Shout : Showing "'.$shout->id.'"';
?>
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Showing "<?php echo $shout->id?>"</h3>
    <div class="informations formulaire">

            <p class="field">
              <span class="libelle">Date Sent</span>
              <span class="value"><?php echo guess_time($shout->date_sent, GUESS_DATETIME_LOCALE)?></span>
            </p>
<?php
      $option_list = array();
      $player_list = Player::db_get_all();
      foreach( $player_list as $player)
        $option_list[ $player->id ] = $player->name;
?>
      <p class="field">
        <span class="libelle">Shouter Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_player_view', true, array('id' => $shout->shouter_id ) )?>"><?php echo $option_list[ $shout->shouter_id ]?></a></span>
      </p>

            <p class="field">
              <span class="libelle">Text</span>
              <span class="value"><?php echo is_array($shout->text)?nl2br(parameters_to_string( $shout->text )):$shout->text?></span>
            </p>
<?php
      $option_list = array(null => 'Pas de choix');
      $game_list = Game::db_get_all();
      foreach( $game_list as $game)
        $option_list[ $game->id ] = $game->name;
?>
      <p class="field">
        <span class="libelle">Game Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_game_view', true, array('id' => $shout->game_id ) )?>"><?php echo $option_list[ $shout->game_id ]?></a></span>
      </p>
    </div>
    <p><a href="<?php echo get_page_url('admin_shout_mod', true, array('id' => $shout->id))?>">Modifier cet objet Shout</a></p>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_shout')?>">Revenir à la liste des objets Shout</a></p>
  </div>
</div>