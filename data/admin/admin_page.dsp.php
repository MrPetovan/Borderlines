<?php
  $PAGE_TITRE = "Administration des pages";
  include_once('data/static/html_functions.php');
  $tab = Page::db_get_all();

    echo '
<div class="texte_contenu">
  <div class="texte_texte">

  <h3>Liste des pages</h3>
    <form action="'.get_page_url($PAGE_CODE).'" method="post">
    <table class="table table-condensed table-striped table-hover">
      <thead>
        <tr>
          <td>Sel.</td>
          <td>Code</td>
          <td>DSP</td>
          <td>Login</td>
          <td>Admin</td>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">'.count($tab).' elements | <a href="'.get_page_url('admin_page_mod').'">Add a page</a></td>
        </tr>
      </tfoot>
      <tbody>';

    foreach($tab as $page) {
      echo '
        <tr>
          <td><input type="checkbox" name="page_id[]" value="'.$page->get_id().'"/></td>
          <td><a href="'.htmlentities_utf8(get_page_url('admin_page_mod', true, array('id' => $page->get_id()))).'">'.$page->get_code().'</a></td>
          <td>'.$page->get_dsp().'</td>
          <td>'.$page->get_login_required().'</td>
          <td>'.$page->get_admin_required().'</td>
        </tr>';
    }
    echo '
      </tbody>
    </table>
    </form>
  </div>
  </div>';
?>