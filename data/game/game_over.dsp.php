<?php
  if( $current_game->has_ended() ) {
?>
<h3><?php echo __('Results')?></h3>
<?php
  }else {
?>
<h3><?php echo __('Quit your current game ?')?></h3>
<p><?php echo __('Are you sure you want to quit the current game ?')?></p>
<p><?php echo __('If you quit now, any order you gave for the next turn will be ignored during next turn computing.')?></p>
<p><?php echo __('In addition, all your troops will be disbanded and your territories will become independant again.')?></p>
<p><?php echo __('Note: You can always cancel the Quit Game order if you change your mind until the next turn.')?></p>
<form action="<?php echo Page::get_url(PAGE_CODE)?>" method="post">
  <p>
    <?php echo HTMLHelper::button('quit', 'yes', array(), 'Yes, I want to quit')?>
    <?php echo HTMLHelper::button('quit', 'no', array(), 'No, bring me back to the game !')?>
  </p>
</form>
<?php
  }
?>