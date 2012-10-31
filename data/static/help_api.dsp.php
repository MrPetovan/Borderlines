<?php
$json = file_get_contents(Page::get_url('api'));
$array = json_decode($json, true);
?>
<h2><?php echo __('Scrambled Nations API')?></h2>
<h3><?php echo __('Principles')?></h3>
<ul>
  <li>Access the API via <strong><?php echo Page::get_url('api', array('m' => '[method]'))?></strong></li>
  <li>All return values are JSON</li>
  <li>HTTP Header 403 means that the token is absent, invalid or expired</li>
  <li>HTTP Header 500 means that the parameters are wrong or that the method failed for any reason.</li>
</ul>
<h3><?php echo __('Protocol')?></h3>
<ol>
  <li>Request a token using the request_token method. The token lives for 1 hour.</li>
  <li>Add a token parameter to any method with the hash given by request_token.</li>
</ol>
<h3><?php echo __('Methods')?></h3>
<?php foreach( $array as $method => $params ) :?>
<h4><?php echo $method?></h4>
<ul>
  <?php foreach( $params as $param => $description ) :?>
  <li><strong><?php echo $param?></strong> : <?php echo $description?></li>
  <?php endforeach; ?>
</ul>
<?php endforeach; ?>
