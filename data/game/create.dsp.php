<form class="formulaire" action="<?php echo Page::get_page_url( PAGE_CODE )?>" method="post">
  <?php echo $game_mod->html_get_game_list_form() ?>
  <p>>?php echo HTMLHelper::submit('game_submit', __('Add a game') )?></p>
</form>