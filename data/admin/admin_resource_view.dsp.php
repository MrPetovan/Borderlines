<?php
  include_once('data/static/html_functions.php');

  $resource = Resource::instance( getValue('id') );

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url($PAGE_CODE).'&id='.$resource->get_id();
  $PAGE_TITRE = 'Resource : Consultation de "'.$resource->get_name().'"';
?>
<div class="texte_contenu">
<?php echo admin_menu(PAGE_CODE);?>
  <div class="texte_texte">
    <h3>Consultation des données pour "<?php echo $resource->get_name()?>"</h3>
    <div class="informations formulaire">
    </div>
    <p><a href="<?php echo get_page_url('admin_resource_mod', true, array('id' => $resource->get_id()))?>">Modifier cet objet Resource</a></p>
    <h4>Player Resource History</h4>
<?php

  $player_resource_history_list = $resource->get_player_resource_history_list();

  if(count($player_resource_history_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Player Id</th>
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

 
        $player_id_player = Player::instance( $player_resource_history['player_id'] );        echo '
        <tr>
        <td><a href="'.get_page_url('admin_player_view', true, array('id' => $player_id_player->get_id())).'">'.$player_id_player->get_name().'</a></td>
        <td>'.$player_resource_history['datetime'].'</td>
        <td>'.$player_resource_history['delta'].'</td>
        <td>'.$player_resource_history['reason'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $resource->get_id())).'" method="post">
              '.HTMLHelper::genererInputHidden('resource_id', $resource->get_id()).'

              '.HTMLHelper::genererInputHidden('player_id', $player_id_player->get_id()).'
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

  $liste_valeurs_player = Player::db_get_select_list();?>
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $resource->get_id()))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('resource_id', $resource->get_id() )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererSelect('player_id', $liste_valeurs_player, null, array(), 'Player' )?><a href="<?php echo get_page_url('admin_player_mod')?>">Créer un objet Player</a>
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
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_resource')?>">Revenir à la liste des objets Resource</a></p>
  </div>
</div>