<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
  <title><?php echo SITE_NAME?> - <?php echo wash_utf8($PAGE_TITRE); ?></title>
  <?php include('template/head.tpl.php'); ?>
</head>
<body>
  <div id="page">
    <header>
      <h1><a href="<?php echo URL_ROOT?>"><?php echo SITE_NAME?></a></h1>
      <p>Testeurs : rendez-vous le 17 Juin pour <a href="http://www.aeriesguard.com/forum/Jeux-video/Borderlines-Alpha-fermee">une nouvelle partie de test</a></p>
      <!--<p>Testeurs : rendez-vous sur <a href="http://www.aeriesguard.com/IRC">le canal IRC d'Aerie's Guard</a></p>-->
<?php if(Member::get_current_user_id()) {?>
      <nav role="main">
        <ul>
          <li><a href="<?php echo Page::get_page_url('dashboard') ?>">Play</a></li>
          <li><a href="<?php echo Page::get_page_url('game_list') ?>">Games</a></li>
          <li><a href="<?php echo Page::get_page_url('mon-compte') ?>">Account</a></li>
          <li><a href="<?php echo Page::get_page_url('logout') ?>">Logout</a></li>
        </ul>
      </nav>
<?php }else {?>
      <form id="form_login" action="<?php echo get_page_url('login')?>" method="post">
        <p>
          <input type="text" name="email" value="Entrez votre email" onclick="if(this.value = 'Entrez votre email') this.value = ''" />
        </p>
        <p>
          <input type="password" name="pass" value="12345678" onclick="if(this.value = '12345678') this.value = ''"/>
        </p>
        <button type="submit" name="submit_login" value="Ok">Sign in</button>
      </form>
<?php } ?>
    </header>
    <section id="main">
<?php
  $messages['error'] = Page::get_message(Page::PAGE_MESSAGE_ERROR);
  $messages['warning'] = Page::get_message(Page::PAGE_MESSAGE_WARNING);
  $messages['notice'] = Page::get_message(Page::PAGE_MESSAGE_NOTICE);
  
  if( count( $messages['error'] ) || count( $messages['warning'] ) || count( $messages['notice'] ) ) {
    echo '
      <div id="messages">';
    foreach( $messages as $message_class => $message_list ) {
      if( $message_list ) {
        echo '
          <ul class="'.$message_class.'">
            <li>'.implode('</li>
            <li>', $message_list ).'</li>
          </ul>';
      }
    }
    echo '
      </div>';
  }
?>
      <div id="content" class="page-<?php echo get_current_page()?>">
<?php echo $PAGE_CONTENU; ?>
      </div>
    </section>
    <footer>
      <p>&copy; Hypolite</p>
    </footer>
<?php
  if(DEBUG_SQL) {
    mysql_log();
  }
?>
  </div>
</body>
</html>