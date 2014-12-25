<?php

  $tab_visible = array('0' => 'Non', '1' => 'Oui');

  $form_url = get_page_url(PAGE_CODE).'&id='.$api_token->id;
  $PAGE_TITRE = 'Api Token : Showing "'.$api_token->id.'"';
?>
<div class="texte_contenu">
  <div class="texte_texte">
    <h3>Showing "<?php echo $api_token->id?>"</h3>
    <div class="informations formulaire">

            <p class="field">
              <span class="libelle">Hash</span>
              <span class="value"><?php echo is_array($api_token->hash)?nl2br(parameters_to_string( $api_token->hash )):$api_token->hash?></span>
            </p>
<?php
      $option_list = array();
      $player_list = Player::db_get_all();
      foreach( $player_list as $player)
        $option_list[ $player->id ] = $player->name;
?>
      <p class="field">
        <span class="libelle">Player Id</span>
        <span class="value"><a href="<?php echo get_page_url('admin_player_view', true, array('id' => $api_token->player_id ) )?>"><?php echo $option_list[ $api_token->player_id ]?></a></span>
      </p>

            <p class="field">
              <span class="libelle">Created</span>
              <span class="value"><?php echo guess_time($api_token->created, GUESS_DATETIME_LOCALE)?></span>
            </p>
            <p class="field">
              <span class="libelle">Expires</span>
              <span class="value"><?php echo guess_time($api_token->expires, GUESS_DATETIME_LOCALE)?></span>
            </p>    </div>
    <p><a href="<?php echo get_page_url('admin_api_token_mod', true, array('id' => $api_token->id))?>">Modifier cet objet Api Token</a></p>
    <h4>Api Log</h4>
<?php

  $api_log_list = $api_token->get_api_log_list();

  if(count($api_log_list)) {
?>
    <table>
      <thead>
        <tr>
          <th>Method</th>
          <th>Params</th>
          <th>Allowed</th>
          <th>Success</th>
          <th>Created</th>          <th>Action</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6"><?php echo count( $api_log_list )?> lignes</td>
        </tr>
      </tfoot>
      <tbody>
<?php
      foreach( $api_log_list as $api_log ) {

         echo '
        <tr>
        <td>'.$api_log['method'].'</td>
        <td>'.$api_log['params'].'</td>
        <td>'.$api_log['allowed'].'</td>
        <td>'.$api_log['success'].'</td>
        <td>'.$api_log['created'].'</td>          <td>
            <form action="'.get_page_url(PAGE_CODE, true, array('id' => $api_token->id)).'" method="post">
              '.HTMLHelper::genererInputHidden('id', $api_token->id).'
              '.HTMLHelper::genererButton('action',  'del_api_log', array('type' => 'submit'), 'Supprimer').'
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
    <form action="<?php echo get_page_url(PAGE_CODE, true, array('id' => $api_token->id))?>" method="post" class="formulaire">
      <?php echo HTMLHelper::genererInputHidden('id', $api_token->id )?>
      <fieldset>
        <legend>Ajouter un élément</legend>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('method', null, array(), 'Method*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('params', null, array(), 'Params*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('allowed', null, array(), 'Allowed*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('success', null, array(), 'Success*' )?>
          
        </p>
        <p class="field">
          <?php echo HTMLHelper::genererInputText('created', null, array(), 'Created*' )?>
          <span><?php echo guess_time(time(), GUESS_TIME_MYSQL)?></span>
        </p>
        <p><?php echo HTMLHelper::genererButton('action',  'set_api_log', array('type' => 'submit'), 'Ajouter un élément')?></p>
      </fieldset>
    </form>
<?php
  // CUSTOM

  //Custom content

  // /CUSTOM
?>
    <p><a href="<?php echo get_page_url('admin_api_token')?>">Revenir à la liste des objets Api Token</a></p>
  </div>
</div>