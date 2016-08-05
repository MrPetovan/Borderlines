<?php
  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['member_id']) && is_array($_POST['member_id'])) {
        foreach($_POST['member_id'] as $member_id) {
          $member = new Member($member_id);
          switch($_POST['action']) {
            case 'valid' :
              $member->set_visible(true);
              break;
            case 'delete' :
            case 'devalid' :
              $member->set_visible(false);
              break;
          }
          $member->db_save();
        }
      }
    }
  }

  if(isset($_POST['export_csv'])) {
    switch ($_POST['export_csv_type']) {
      case 'liste_membres' :
        $data = Member::export_csv_liste_utilisateurs();
        $filename = strtolower( SITE_NAME ).'_members_'.date('Ymd').'.csv';
        break;
    }
    header("Content-Type: text/csv");
    header("Content-Disposition: attachment; filename=$filename");

    echo iconv('utf-8', 'iso-8859-15', $data);

    exit;
  }
?>
