<?php
?>
<h2>
  <?php echo __('Translation')?>
  [<?php echo __(LOCALE)?>]
  <?php if( $search ):?>
  (<?php echo $translate_number?> <?php echo __('found')?>)
  <?php else:?>
  (<?php echo $translate_number?> <?php echo __('left')?>)
  <?php endif;?>
</h2>

<form action="<?php echo Page::get_url(PAGE_CODE)?>" method="get">
  <p>
    <input type="hidden" name="page" value="<?php echo PAGE_CODE?>" />
    <input type="text" name="search" value="<?php echo $search?>" placeholder="<?php echo __('Look for a translation')?>" />
    <input type="submit" value="<?php echo __('Search')?>" />
  </p>
</form>
<form action="<?php echo Page::get_url(PAGE_CODE)?>" method="post">
  <p><input type="submit" value="<?php echo __('Save')?>"/></p>
  <table>
    <tr>
      <th><?php echo __('To translate')?></th>
      <th><?php echo __('Language')?></th>
      <th><?php echo __('Context')?></th>
      <th><?php echo __('Translation')?></th>
    </tr>
<?php
  $tabindex = 1;
  foreach( $translate_array as $translation ):
    ?>
    <tr>
      <td>
        <?php echo wash_utf8( $translation['code'] )?>
      </td>
      <td><?php echo __($translation['locale'])?></td>
      <td><a href="<?php echo Page::get_url($translation['context'])?>"><?php echo $translation['context']?></a></td>
      <td>
        <?php echo HTMLHelper::genererInputText('translation['.$translation['id'].']', $translation['translation'], array('size' => 50, 'tabindex' => $tabindex ++))?>
      </td>
    </tr>
    <?php
  endforeach;
?>
  </table>
  <p><input type="submit" value="<?php echo __('Save')?>"/></p>
</form>
<h3><?php echo __('Translation stats')?></h3>
<table>
<?php
    $current_locale = null;
    $current_rank = null;

    foreach( $stats_array as $stats_row ) {
      if( $stats_row['locale'] != $current_locale ) {
        echo '
    <tr class="title">
      <th colspan="3">'.__($stats_row['locale']).'</th>
    </tr>';
        $current_locale = $stats_row['locale'];
        $current_rank = 1;
      }

      echo '
    <tr>
      <td class="num">'.$current_rank++.'</td>
      <td>'.$stats_row['name'].'</td>
      <td class="num">'.$stats_row['count'].'</td>
    </tr>';
    }
?>
</table>