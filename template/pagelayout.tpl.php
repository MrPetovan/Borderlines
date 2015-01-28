<!DOCTYPE html>
<html lang="fr">
<head>
  <title><?php echo SITE_NAME?> - <?php echo wash_utf8($PAGE_TITRE); ?></title>
  <?php include('template/head.tpl.php'); ?>
</head>
<body>
  <nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="<?php echo URL_ROOT?>"><?php echo SITE_NAME?></a>
      </div>
<?php if(Member::get_current_user_id()) {
  $member = Member::get_current_user();
  $current_player = Player::get_current( $member );
?>
      <div id="navbar" class="collapse navbar-collapse">
        <p class="navbar-text">[ <?php echo guess_time(time(), GUESS_DATETIME_LOCALE)?> ]</p>
        <ul class="nav navbar-nav">
          <li><a href="<?php echo Page::get_page_url('dashboard') ?>"><?php echo __('Play')?></a></li>
          <?php if( $unread_count = Message_player::db_get_unread_count($current_player->id) ) :?>
          <li><a href="<?php echo Page::get_page_url('conversation_list') ?>"><strong><?php echo __('Conversations')?> (<?php echo $unread_count?>)</strong></a></li>
          <?php else:?>
          <li><a href="<?php echo Page::get_page_url('conversation_list') ?>"><?php echo __('Conversations')?></a></li>
          <?php endif;?>
          <li><a href="<?php echo Page::get_page_url('game_list') ?>"><?php echo __('Games')?></a></li>
          <li><a href="<?php echo Page::get_page_url('player_list') ?>"><?php echo __('Players')?></a></li>
          <li><a href="<?php echo Page::get_page_url('world_list') ?>"><?php echo __('Worlds')?></a></li>
          <li><a href="<?php echo Page::get_page_url('mon-compte') ?>"><?php echo __('Account')?></a></li>
          <li><a href="<?php echo Page::get_page_url('logout') ?>"><?php echo __('Logout')?></a></li>
        </ul>
      </div><!--/.nav-collapse -->
<?php }else {?>
      <form class="navbar-form navbar-right" id="form_login" action="<?php echo get_page_url('login')?>" method="post">
        <div class="form-group">
          <input type="text" name="email" value="" placeholder="<?php echo __('Email')?>" class="form-control"/>
        </div>
        <div class="form-group">
          <input type="password" name="pass" placeholder="<?php echo __('Password')?>" class="form-control"/>
        </div>
        <button type="submit" name="submit_login" value="Ok" class="btn btn-primary"><?php echo __('Sign in')?></button>
      </form>
      <div id="navbar" class="collapse navbar-collapse">
        <ul class="nav navbar-nav">
          <li><a href="<?php echo Page::get_page_url('accueil') ?>"><?php echo __('Home')?></a></li>
          <li><a href="<?php echo Page::get_page_url('register') ?>"><?php echo __('Sign Up')?></a></li>
          <li><a href="<?php echo Page::get_page_url('rappel-identifiants') ?>"><?php echo __('Forgotten password ?')?></a></li>
        </ul>
      </div>
<?php } ?>
    </div>
  </nav>

  <div class="container">
<?php Page::display_messages();?>
    <div id="content" class="page-<?php echo get_current_page()?>">
<?php echo $PAGE_CONTENU; ?>
    </div>

  </div><!-- /.container -->

  <?php include('template/footer.tpl.php'); ?>

  <?php include('template/body_bottom.tpl.php'); ?>
</body>
</html>