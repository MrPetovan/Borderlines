<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
  <title><?php echo SITE_NAME?> - <?php echo wash_utf8($PAGE_TITRE); ?></title>
  <?php include('template/head.tpl.php'); ?>
</head>
<body class="clean">
  <?php Page::display_messages();?>
  <?php echo $PAGE_CONTENU; ?>
  <?php include('template/body_bottom.tpl.php'); ?>
</body>
</html>