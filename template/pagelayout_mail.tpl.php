<html>
  <head>
    <title></title>
  </head>
  <body bgcolor="white" style="font-family: Arial, sans serif; margin: 0; padding: 10px;" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <table align="center" id="mail_auto" width="698" border="0" cellpadding="0" cellspacing="0" style="border:solid 1px #E0E0E0;font-family: Arial, sans serif;">
      <tr>
        <td style="font-size: 72px; color: #FFFFFF; background-color: #E0E0E0; font-weight: bold"><a style="color: #000000; text-decoration:none" href="<?php echo Page::get_url('accueil')?>"><?php echo SITE_NAME ?></a></td>
      </tr>
      <tr>
        <?php echo $PAGE_CONTENU?>
      </tr>
<?php /*
      <tr>
        <td style="padding-left:30px; padding-right:30px;">
          <p style="font-size: 12px; color:#444444; line-height: 15px; text-align:justify; margin-top:50px; margin-bottom:30px;">
            Conformément à la loi relative à l'informatique, aux fichiers et aux libertés du 6 janvier 1978, vous disposez d'un droit individuel d'opposition, d'accès, de modification et de suppression des données qui vous concernent.<br>
            Vous recevez ce message car vous êtes enregistré(e) dans la base de données de <?php echo SITE_NAME ?>. Si vous ne souhaitez plus recevoir d'emails <a style="color:#F22A83;" href="<?php echo get_page_url('mon-compte-infos')?>">cliquez ici</a>.
          </p>
        </td>
      </tr>
*/?>
    </table>
  </body>
</html>