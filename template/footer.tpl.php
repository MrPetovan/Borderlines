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
<?php
  $dir_js_name = 'js';
  $dir_js_path = DIR_ROOT . $dir_js_name . '/';
  $js_array = array();
  if (is_dir($dir_js_path)) {
    if ($dir_js = opendir($dir_js_path)) {
      while (($file_js = readdir($dir_js)) !== false) {
        if($file_js != '.' && $file_js != '..' && !is_dir($dir_js_path.$file_js)) {
          $js_array[] = URL_ROOT.$dir_js_name.'/'.$file_js;
        }
      }
      closedir($dir_js);
    }
  }
  sort($js_array);
  foreach ($js_array as $js_file) {
    echo '
  <script type="text/javascript" src="'.$js_file.'"></script>';
  }
?>
  <script type="text/javascript">
    jQuery(function(){
      while (domReadyQueue.length) {
        domReadyQueue.shift()(jQuery);
      }
    });
  </script>