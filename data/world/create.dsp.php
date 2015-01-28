<h2><?php echo __('Create a World')?></h2>
<form class="formulaire" action="<?php echo Page::get_page_url( PAGE_CODE )?>" method="post">
  <?php echo $world_mod->html_creation_form()?>
  <p><?php echo HTMLHelper::submit('world_submit', __('Add a world') )?></p>
</form>