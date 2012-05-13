<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
    <title><?php echo SITE_NAME ?> - <?php echo wash_utf8($PAGE_TITRE); ?></title>
<?php include('template/head.tpl.php'); ?>
</head>
<body>
  <div id="page">
    <div id="header">
      <h1><a href="<?php echo URL_ROOT?>"><?php echo SITE_NAME ?></a></h1>
    </div>
    <div id="content">
      <ul id="menu_admin">
        <li><a href="<?php echo get_page_url("admin_member")?>" >Gestion Utilisateurs</a></li>
        <li><a href="<?php echo get_page_url("admin_page")?>" >Gestion Pages</a></li>
        <li><a href="<?php echo get_page_url('logout')?>">Logout</a></li>
      </ul>
      <div id="editorial_content">
        <div class="texte_texte">
<?php echo $PAGE_CONTENU; ?>
        </div>
      </div>
    </div>
    <div id="footer">
      <p>&copy; Hypolite |
        <a href="<?php echo get_page_url('mon-compte')?>">Mon compte</a>
      </p>
      <p>
        <a href="<?php echo URL_ROOT?>">Accueil</a> |
      </p>
    </div>
  </div>
</body>
</html>
