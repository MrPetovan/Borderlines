<?php
  
?>
<h2>Create a player</h2>
<p>In order to play in games, you need to create an avatar of yourself. You can give it any name.</p>
<form action="<?php echo Page::get_url(PAGE_CODE)?>" method="post">
  <p><?php echo HTMLHelper::genererInputText('name', '', array(), 'Name')?></p>
  <p><?php echo HTMLHelper::genererButton('action', 'create', array('type' => 'submit'), 'Create your avatar')?></p>
</form>