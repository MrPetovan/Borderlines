<?php
  if(isset($_POST['submit'])) {
    if(isset($_POST['action'])) {
      if(isset($_POST['order_type_id']) && is_array($_POST['order_type_id'])) {
        foreach($_POST['order_type_id'] as $order_type_id) {

          $order_type = Order_Type::instance( $order_type_id );
          switch($_POST['action']) {
            case 'delete' :
              $order_type->db_delete();
              break;
          }
        }
      }
    }
  }

  // CUSTOM

  //Custom content

  // /CUSTOM