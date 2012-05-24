<?php
  include_once('data/static/html_functions.php');

  $player = Player::instance( getValue('id') );

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url($PAGE_CODE).'&id='.$player->get_id();
  $PAGE_TITRE = 'Player : Consultation de "'.$player->get_name().'"';
?>
<div class="texte_contenu">
<?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Consultation des données pour "<?php echo $player->get_name()?>"</h3>
    <div class="informations formulaire">

<?php
      $option_list = array();
      $member_list = Member::db_get_all();
      foreach( $member_list as $member)
        $option_list[ $member->id ] = $member->name;
?>
      <p class="field">
        <span class="libelle">Member Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_member_view', true, array('id' => $player->get_member_id() ) )?>"><?php echo $option_list[ $player->get_member_id() ]?></a></span>
      </p>
    </div>
    <p><a href="<?php echo get_page_url('admin_player_mod', true, array('id' => $player->get_id()))?>">Modifier cet objet Player</a></p>
    <h4>Player Resource History</h4>
<?php

  $player_resource_history_list = $player->get_player_resource_history_list();

  if(count($player_resource_history_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Resource Id</th>
          <th>Datetime</th>
          <th>Delta</th>
          <th>Reason</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5"><?php echo count( $player_resource_history_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $player_resource_history_list as $player_resource_history ) {

 
        $resource_id_resource = Resource::instance( $player_resource_history['resource_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_resource_view', true, array('id' => $resource_id_resource->get_id())).'">'.$resource_id_resource->get_name().'</a></td>
        <td>'.$player_resource_history['datetime'].'</td>
        <td>'.$player_resource_history['delta'].'</td>
        <td>'.$player_resource_history['reason'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $player->get_id())).'" method="post">
              '.HTMLHelper::genererInputHidden('player_id', $player->get_id()).'

              '.HTMLHelper::genererInputHidden('resource_id', $resource_id_resource->get_id()).'
              '.HTMLHelper::genererInputHidden('datetime', $player_resource_history['datetime']).'              '.HTMLHelper::genererButton('action',  'del_player_resource_history', array('type' => 'submit'), 'Supprimer').'
            </form>
          </td>
        </tr>';
      }
?>
      </tbody>
    </table>
<?php
  }else {
    echo '<p>Il n\'y a pas d\'éléments à afficher</p>';
  }

  $liste_valeurs_resource = Resource::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $player->get_id()))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('player_id', $player->get_id() )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('resource_id', $liste_valeurs_resource, null, array(), 'Resource' )?><a href="<?php echo get_page_url('admin_resource_mod')?>">Créer un objet Resource</a>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('datetime', null, array(), 'Datetime' )?>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('delta', null, array(), 'Delta' )?>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('reason', null, array(), 'Reason' )?>
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_player_resource_history', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
    <h4>Player Spygame Value</h4>
<?php

  $player_spygame_value_list = $player->get_player_spygame_value_list();

  if(count($player_spygame_value_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Value Guid</th>
          <th>Datetime</th>
          <th>Real Value</th>
          <th>Masked Value</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5"><?php echo count( $player_spygame_value_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $player_spygame_value_list as $player_spygame_value ) {

         echo '
        <tr>
        <td>'.$player_spygame_value['value_guid'].'</td>
        <td>'.$player_spygame_value['datetime'].'</td>
        <td>'.$player_spygame_value['real_value'].'</td>
        <td>'.$player_spygame_value['masked_value'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $player->get_id())).'" method="post">
              '.HTMLHelper::genererInputHidden('player_id', $player->get_id()).'

              '.HTMLHelper::genererInputHidden('value_guid', $player_spygame_value['value_guid']).'
              '.HTMLHelper::genererInputHidden('datetime', $player_spygame_value['datetime']).'              '.HTMLHelper::genererButton('action',  'del_player_spygame_value', array('type' => 'submit'), 'Supprimer').'
            </form>
          </td>
        </tr>';
      }
?>
      </tbody>
    </table>
<?php
  }else {
    echo '<p>Il n\'y a pas d\'éléments à afficher</p>';
  }
?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $player->get_id()))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('player_id', $player->get_id() )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('value_guid', null, array(), 'Value Guid' )?>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('datetime', null, array(), 'Datetime' )?>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('real_value', null, array(), 'Real Value' )?>
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('masked_value', null, array(), 'Masked Value' )?>
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_player_spygame_value', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_player')?>">Revenir à la liste des objets Player</a></p>
  </div>
</div>