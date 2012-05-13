<?php
  $PAGE_TITRE = "Page introuvable";

  header("HTTP/1.1 404 Not Found");
?>
<h2>Page introuvable</h2>
<p>Code page : <?php echo PAGE_CODE?></p>
<?php //var_debug($_SERVER);?>
