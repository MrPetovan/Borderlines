  <footer>
    <div class="container">
<?php
  if(DEBUG_SQL) {
    mysql_log();
  }
?>


    <form action="<?php echo Page::get_url(PAGE_CODE, $_GET)?>" method="post">
      <p>2012<?php echo date('Y') != 2012?' - '.date('Y'):''?> &copy; Hypolite |
        <a href="<?php echo Page::get_url('help_api')?>">API</a> |
        <?php echo __('Change language:')?>
        <select name="locale">
          <?php foreach( explode(',', LOCALES) as $locale ) :?>
          <option value="<?php echo $locale?>"<?php echo $locale == LOCALE?' selected="selected"':''?>><?php echo __($locale)?></option>
          <?php endforeach;?>
        </select>
        <button type="submit" name="setlocale" value="1"><?php echo __('Set')?></button> |
        <?php $translation_count = Translation::get_untranslated_count( LOCALE );?>
        <a href="<?php echo Page::get_page_url('translate') ?>"><?php echo __('Translation')?><?php echo $translation_count>0?' ('.$translation_count.')':''?></a>
      </p>
    </form>
    </div>
  </footer>