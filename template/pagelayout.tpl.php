<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
  <title><?php echo SITE_NAME?> - <?php echo wash_utf8($PAGE_TITRE); ?></title>
  <?php include('template/head.tpl.php'); ?>
</head>
<body>
  <div id="page">
    <div id="header">
      <h1><a href="<?php echo URL_ROOT?>">Geo</a></h1>
<?php if(Member::get_current_user_id()) {?>
        <p> <a href="<?php echo get_page_url('mon-compte') ?>"><img id="header_compte" src="<?php echo IMG; ?>img_html/header_compte_link.png" alt="Mon compte"/></a></p>
<?php }else {?>
        <form id="form_login" action="<?php echo get_page_url('login')?>" method="post">

        <p><a href="<?php echo get_page_url('mon-compte')?>"><img src="<?php echo IMG; ?>img_html/header_compte.png" alt="Mon compte"/></a></p>
        <p>
          <input type="text" class="input_text" name="email" value="Entrez votre email" onclick="if(this.value = 'Entrez votre email') this.value = ''" />
        </p>
        <p>
          <input type="password" class="input_text" name="pass" value="12345678" onclick="if(this.value = 'Entrez votre mot de passe') this.value = ''"/>
        </p>
        <input type="image" class="input_image" name="submit_login" src="<?php echo IMG; ?>img_html/btn_ok_rose.png" alt="Ok" value="Ok"/>

      </form>
<?php } ?>
   </div>
    <div id="content">
          <div id="<?php echo get_current_page()?>">
<?php echo $PAGE_CONTENU; ?>
          </div>
    </div>
    <div id="footer">
      <p>&copy; Hypolite</p>
    </div>
<?php
  if(DEBUG_SQL) {
    mysql_log();
  }
?>
  </div>
</body>
</html>