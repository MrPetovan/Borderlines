<?php
  if( isset( $html_msg ) ) {
    echo $html_msg;
  }
?>
<p><a href="<?php echo Page::get_page_url( PAGE_CODE, false, array( 'action' => 'init_db' ) )?>">Initialiser la DB</a></p>
<p><a href="<?php echo Page::get_page_url( PAGE_CODE, false, array( 'action' => 'test' ) )?>">Lancer le test</a></p>