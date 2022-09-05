<?php

// INCLUDE MAILER SCRIPT
function inv_include_mailer() {
  if(file_exists(osc_lib_path() . 'phpmailer/class.phpmailer.php')) {
    require_once osc_lib_path() . 'phpmailer/class.phpmailer.php';
  } else if(file_exists(osc_lib_path() . 'vendor/phpmailer/phpmailer/class.phpmailer.php')) {
    require_once osc_lib_path() . 'vendor/phpmailer/phpmailer/class.phpmailer.php';
  }
}



// GENERATE PAGINATION
function inv_admin_paginate($file, $page_id, $per_page, $count_all, $class = '', $params = '') {
  $html = '';
  $page_id = (int)$page_id;
  $page_id = ($page_id <= 0 ? 1 : $page_id);
  $base_link = osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=' . $file . $params;

  if($per_page < $count_all) {
    $html .= '<div id="mb-pagination" class="' . $class . '">';
    $html .= '<div class="mb-pagination-wrap">';
    $html .= '<div>' . __('Page:', 'invoice') . '</div>';

    $pages = ceil($count_all/$per_page); 
    $page_actual = ($page_id == '' ? 1 : $page_id);

    if($pages > 6) {

      // Too many pages to list them all
      if($page_id == 1) { 
        $ids = array(1,2,3, $pages);

      } else if ($page_id > 1 && $page_id < $pages) {
        $ids = array(1,$page_id-1, $page_id, $page_id+1, $pages);

      } else {
        $ids = array(1, $page_id-2, $page_id-1, $page_id);
      }

      $old = -1;
      $ids = array_unique(array_filter($ids));

      foreach($ids as $i) {
        $url = $base_link . '&pageId=' . $i;
        
        if($old <> -1 && $old <> $i - 1) {
          $html .= '<span>&middot;&middot;&middot;</span>';
        }

        $html .= '<a href="' . $url . '" ' . ($page_actual == $i ? 'class="mb-active"' : '') . '>' . $i . '</a>';
        $old = $i;
      }

    } else {

      // List all pages
      for ($i = 1; $i <= $pages; $i++) {
        $url = $base_link . '&pageId=' . $i;
        $html .= '<a href="' . $url . '" ' . ($page_actual == $i ? 'class="mb-active"' : '') . '>' . $i . '</a>';
      }
    }

    $html .= '</div>';
    $html .= '</div>';
  }

  return $html;
}


// GET STATUS OF USER PROFILE
function inv_user_profile_status($data) {
  if(inv_param('validation') == 0) {
    $status = 'success';
    $name = __('Valid', 'invoice');
    $message = __('Validation is not required ', 'invoice');
  } else if(!isset($data['i_vat_number_verified'])) {
    $status = 'unavailable';
    $name = __('N/A', 'invoice');
    $message = __('Your billing profile has not been submitted yet', 'invoice');
  } else if($data['i_vat_number_verified'] == 0 || $data['i_vat_number_verified'] == '') {
    $status = 'pending';
    $name = __('Pending', 'invoice');
    $message = __('Your billing profile has not been validated yet', 'invoice');
  } else if ($data['i_vat_number_verified'] == 9) {
    $status = 'reject';
    $name = __('Rejected', 'invoice');
    $message = __('Your billing profile has been rejected as invalid. Contact us for more details and then submit your profile again.', 'invoice');
  } else {
    $status = 'success';
    $name = __('Valid', 'invoice');
    $message = __('Your billing profile has been successfully validated', 'invoice');
  }
  
  return array(
    'status' => $status,
    'name' => $name,
    'message' => $message
  );
}


// REMOVE INVOICE USER DATA
function inv_remove_user_data($user_id) {
  ModelINV::newInstance()->removeInvoiceUserData($user_id);
}

osc_add_hook('delete_user ', 'inv_remove_user_data');


// DATABASE PRICE FORMAT
function inv_db_format($number, $dec = '') {
  if($dec > 0) {
    $decimals = $dec;
  } else {
    $decimals = 2;
  }

  return number_format(floatval($number), $decimals, '.', '');
}


// FORMAT PRICE
function inv_format_price($price, $currency = '') {
  $currency_position = inv_param('currency_position');
  $decimals = (int)inv_param('decimals');
  $tseparator = inv_param('tseparator');
  $dseparator = inv_param('dseparator');
  $space = inv_param('space');

  if($space == 1) {
    $space = ' ';
  } else {
    $space = '';
  }

  if($currency <> '') {
    $currency = inv_currency_symbol($currency);
  } else {
    $space = '';
  }


  $price = number_format($price, $decimals, $dseparator, $tseparator);

  if($currency_position == 1) {
    $output = $currency . $space . $price;
  } else {
    $output = $price . $space . $currency;
  }

  return $output;
}


// UPLOAD STAMP
function inv_upload_stamp($file) {
  $attachment = $file;
  $allowed_extensions = array('png', 'jpg', 'jpeg');

  $extension = strtolower(pathinfo($attachment['name'], PATHINFO_EXTENSION));
  $max_file_size = 4096 * 1000;  //(in bytes)
  $file_size = $attachment['size'];
  $file_name = 'stamp.' . $extension;

  if($attachment['name'] <> '') {
    if( $attachment['error'] == UPLOAD_ERR_OK) {
      if(in_array($extension, $allowed_extensions)) {
        if( $file_size < $max_file_size ) {
          if( move_uploaded_file($attachment['tmp_name'], osc_base_path() . 'oc-content/plugins/invoice/img/' . $file_name ) ) {
            return $file_name;

          } else {
            message_error(__('There was an error with image upload', 'invoice') );
          }
        } else {
          message_error( __('Stamp is too big and was not uploaded. Maximum file size is:', 'invoice') . ' ' . round($max_file_size/1000) . 'kb' );
        }
      } else {
        message_error( __('Your image extension is not allowed, file was not uploaded. Only files with following extensions are allowed', 'invoice') . ': ' . implode(', ', $allowed_extensions) );
      }
    } else {
      message_error( __('An error with image upload has occurred, please try again.', 'invoice') );
    }
  }
  
  return false;
}


// GET STAMP
function inv_get_stamp($path = true) {
  $stamp = osc_base_path() . 'oc-content/plugins/invoice/img/stamp.png';

  if(file_exists($stamp) && is_file($stamp)) {
    if($path) {
      return $stamp;
    } else {
      return osc_base_url() . 'oc-content/plugins/invoice/img/stamp.png';
    }
  } else {
    return false;
  }
}


// GET INVOICE ORDER NUMBER
function inv_order_number($inv) {
  $invoice_order = @$inv['s_identifier'];

  if($invoice_order == '' && inv_param('invoice_order') <> '' && is_numeric(inv_param('invoice_order'))) {
    $invoice_order = inv_param('invoice_order');
  }

  if($invoice_order == '') {
    $invoice_order = @$inv['pk_i_id'];
  }

  return $invoice_order;
}


// CONNECT TO SMS PAYMENTS (FORTUMO) PLUGIN
function inv_connect_sms($log_id) {
  $connect_array = explode(',', inv_param('connect'));

  if(in_array('fortumo', $connect_array) && $log_id > 0 && osc_plugin_is_enabled('sms_payments/index.php')) {
    require_once osc_base_path() . 'oc-content/plugins/sms_payments/index.php';

    $log = ModelINV::newInstance()->getSmsLog($log_id);

    if($log) {
      // Prepare data
      $item = Item::newInstance()->findByPrimaryKey($log['item_id']);
      $user = User::newInstance()->findByPrimaryKey($item['fk_i_user_id']);

      if(is_numeric(inv_param('invoice_order')) && inv_param('invoice_order') <> '') {
        $invoice_order = inv_param('invoice_order');
        osc_set_preference( 'invoice_order', inv_param('invoice_order')+1, 'plugin-invoice', 'INTEGER');  // increase invoice order
      } else {
        $invoice_order = $log['payment_id'];
      }

      $invoice_data = array(
        'fk_i_user_id' => $item['fk_i_user_id'],
        's_identifier' => $invoice_order,
        's_title' => __('Invoice', 'invoice'),
        's_from' => inv_param('from'),
        's_to' => inv_header_to($item['fk_i_user_id']), 
        'dt_date' => date('Y-m-d', strtotime($log['log_date'])),
        'dt_due_date' => date('Y-m-d', strtotime($log['log_date'])),
        'f_paid' => inv_db_format($log['price']),
        'f_amount' => inv_db_format($log['price']),
        'f_balance' => inv_db_format(0),
        'f_discount' => inv_db_format(0, 1),
        'f_shipping' => inv_db_format(0),
        'f_fee' => inv_db_format(0),
        'f_tax' => inv_db_format(inv_param('tax')),
        's_notes' => inv_param('notes'),
        's_terms' => inv_param('terms'),
        's_currency' => ($log['currency'] <> '' ? $log['currency'] : osp_currency()),
        's_email' => @$user['s_email'],
        's_file' => '',
        's_comment' => __('Source: SMS Payments (Fortumo)', 'invoice'),
        'i_status' => 0,
        's_cart' => $log['payment_type'],
        's_source' => 'FORTUMO',
        'i_payment_id' => $log['record_id']
      );

      $invoice_id = ModelINV::newInstance()->updateInvoice($invoice_data);


      // Generate invoice items
      $failed = false;
      $items = array();

      $tax = inv_param('tax')/100;
      $price = $log['price']/(1+$tax);

      $item = array(
        'fk_i_invoice_id' => $invoice_id,
        's_description' => __('Fortumo', 'invoice') . ' - ' . ucfirst($log['payment_type']),
        'i_quantity' => 1,
        'f_rate' => inv_db_format($price)
      );

      ModelINV::newInstance()->updateInvoiceItem($item);

     
      // Generate PDF
      if($invoice_id > 0) {
        inv_generate_pdf($invoice_id);
      }


      // Send email
      if($invoice_id > 0 && inv_param('auto_mail') == 1) {
        $invoice = ModelINV::newInstance()->getInvoice($invoice_id);

        inv_email_invoice($invoice);

        ModelINV::newInstance()->updateStatus($invoice_id, 2);
      }

    }
  }
}

osc_add_hook('sms_log_saved', 'inv_connect_sms');


if(osc_is_admin_user_logged_in()) {
  //osc_run_hook('sms_log_saved', 7);
}



// CONNECT PAYMENTS PRO PLUGIN
function inv_connect_pp($log, $log_id) {
  $log_id = ($log_id <= 0 ? @$log['pk_i_id'] : $log_id);
  $connect_array = explode(',', inv_param('connect'));

  if(in_array('payments_pro', $connect_array) && $log_id > 0 && osc_plugin_is_enabled('payment_pro/index.php')) {
    require_once osc_base_path() . 'oc-content/plugins/payment_pro/index.php';

    $log = ModelINV::newInstance()->getPpLog($log_id);
    $disabled_payments = array('WALLET', 'NOSOURCE');

    if($log && !in_array($log['s_source'], $disabled_payments) ) {      // Prepare data
      $user = User::newInstance()->findByPrimaryKey($log['fk_i_user_id']);

      if(is_numeric(inv_param('invoice_order')) && inv_param('invoice_order') <> '') {
        $invoice_order = inv_param('invoice_order');
        osc_set_preference( 'invoice_order', inv_param('invoice_order')+1, 'plugin-invoice', 'INTEGER');  // increase invoice order
      } else {
        $invoice_order = $log['s_code'];
      }

      $invoice_data = array(
        'fk_i_user_id' => $log['fk_i_user_id'],
        's_identifier' => $invoice_order,
        's_title' => __('Invoice', 'invoice'),
        's_from' => inv_param('from'),
        's_to' => inv_header_to($log['fk_i_user_id'], @$log['s_email']),
        'dt_date' => date('Y-m-d', strtotime($log['dt_date'])),
        'dt_due_date' => date('Y-m-d', strtotime($log['dt_date'])),
        'f_paid' => inv_db_format($log['i_amount']/1000000),
        'f_amount' => inv_db_format($log['i_amount']/1000000),
        'f_balance' => inv_db_format(0),
        'f_discount' => inv_db_format(0, 1),
        'f_shipping' => inv_db_format(0),
        'f_fee' => inv_db_format(0),
        'f_tax' => inv_db_format(($log['i_amount_tax']/$log['i_amount'])*100, 1),
        's_notes' => inv_param('notes'),
        's_terms' => inv_param('terms'),
        's_currency' => ($log['s_currency_code'] <> '' ? $log['s_currency_code'] : osp_currency()),
        's_email' => ($log['s_email'] <> '' ? $log['s_email'] : @$user['s_email']),
        's_file' => '',
        's_comment' => __('Source: Payments Pro', 'invoice'),
        'i_status' => 0,
        's_cart' => '',
        's_source' => $log['s_source'],
        'i_payment_id' => $log['pk_i_id']
      );

      $invoice_id = ModelINV::newInstance()->updateInvoice($invoice_data);


      // Generate invoice items
      $failed = false;
      $cart = $log['items'];
      $items = array();

      if(empty($cart)) {
        $failed = true;
      }



      if(!$failed) {
        foreach($cart as $c) {
          $tax = $log['i_amount_tax']/$log['i_amount'];
          $price = ($c['i_amount']/1000000)/(1+$tax);

          $items[] = array(
            'fk_i_invoice_id' => $invoice_id,
            's_description' => ($c['s_concept'] <> '' ? $c['s_concept'] : __('Service', 'invoice')),
            'i_quantity' => ($c['i_quantity'] > 0 ? $c['i_quantity'] : 1),
            'f_rate' => inv_db_format(($price > 0 ? $price : 0))
          );
        }

      } else {

        $price = $log['i_amount']/1000000;
        $tax = $log['i_amount_tax']/$log['i_amount'];
        $price = $price/(1+$tax);

        $items[] = array(
          'fk_i_invoice_id' => $invoice_id,
          's_description' => __('Service', 'invoice'),
          'i_quantity' => 1,
          'f_rate' => inv_db_format($price)
        );

      }


      // Insert items into DB
      if(count($items) > 0) {
        foreach($items as $item) {
          ModelINV::newInstance()->updateInvoiceItem($item);
        }
      }

     
      // Generate PDF
      if($invoice_id > 0) {
        inv_generate_pdf($invoice_id);
      }

      // Send email
      if($invoice_id > 0 && inv_param('auto_mail') == 1) {
        $invoice = ModelINV::newInstance()->getInvoice($invoice_id);

        inv_email_invoice($invoice);

        ModelINV::newInstance()->updateStatus($invoice_id, 2);
      }

    }
  }
}

osc_add_hook('payment_pro_invoice_saved', 'inv_connect_pp');

if(osc_is_admin_user_logged_in()) {
  //osc_run_hook('payment_pro_invoice_saved', array(), 4);
}



if(osc_is_admin_user_logged_in() && osc_logged_admin_username() == 'admin') {
  //inv_connect_osp(374);
}

// CONNECT TO OSCLASS PAY PLUGIN
function inv_connect_osp($log_id) {
  $connect_array = explode(',', inv_param('connect'));

  if(in_array('osclass_pay', $connect_array) && $log_id > 0 && osc_plugin_is_enabled('osclass_pay/index.php')) {
    require_once osc_base_path() . 'oc-content/plugins/osclass_pay/index.php';

    $log = ModelINV::newInstance()->getOspLog($log_id);

    $disabled_payments = array('WALLET', 'ADMIN', 'REGISTRATION', 'REFERRAL', 'PERIODICAL');

    if($log && !in_array($log['s_source'], $disabled_payments) ) {
      // Prepare data
      $user = User::newInstance()->findByPrimaryKey($log['fk_i_user_id']);

      if(is_numeric(inv_param('invoice_order')) && inv_param('invoice_order') <> '') {
        $invoice_order = inv_param('invoice_order');
        osc_set_preference( 'invoice_order', inv_param('invoice_order')+1, 'plugin-invoice', 'INTEGER');  // increase invoice order
      } else {
        $invoice_order = $log['s_code'];
      }


      $invoice_data = array(
        'fk_i_user_id' => $log['fk_i_user_id'],
        's_identifier' => $invoice_order,
        's_title' => __('Invoice', 'invoice'),
        's_from' => inv_param('from'),
        's_to' => inv_header_to($log['fk_i_user_id'], @$log['s_email']),
        'dt_date' => date('Y-m-d', strtotime($log['dt_date'])),
        'dt_due_date' => date('Y-m-d', strtotime($log['dt_date'])),
        'f_paid' => inv_db_format(floatval($log['i_amount'])/1000000000000),
        'f_amount' => inv_db_format(floatval($log['i_amount'])/1000000000000),
        'f_balance' => inv_db_format(0),
        'f_discount' => inv_db_format(0, 1),
        'f_shipping' => inv_db_format(0),
        'f_fee' => inv_db_format(0),
        'f_tax' => inv_db_format(inv_param('tax')),
        's_notes' => inv_param('notes'),
        's_terms' => inv_param('terms'),
        's_currency' => ($log['s_currency_code'] <> '' ? $log['s_currency_code'] : osp_currency()),
        's_email' => ($log['s_email'] <> '' ? $log['s_email'] : @$user['s_email']),
        's_file' => '',
        's_comment' => __('Source: Osclass Pay', 'invoice'),
        'i_status' => 0,
        's_cart' => $log['s_cart'],
        's_source' => $log['s_source'],
        'i_payment_id' => $log['pk_i_id']
      );

      $invoice_id = ModelINV::newInstance()->updateInvoice($invoice_data);


      // Generate invoice items
      $failed = false;
      $cart = $log['s_cart'];
      $items = array();

      if(trim($cart) == '' || in_array($log['s_source'], array('WALLET', 'ADMIN', 'REGISTRATION', 'REFERRAL', 'PERIODICAL'))) {
        $failed = true;
      }

      if(!$failed) {
        $cart = explode('|', $cart);
      
        foreach($cart as $c) {
          $product = explode('x', $c);

          $type = $product[0];
          $quantity = isset($product[1]) ? $product[1] : 1;
          $item_id = isset($product[2]) ? $product[2] : null;
          $duration = isset($product[3]) ? $product[3] : null;
          $repeat = isset($product[4]) ? $product[4] : null;

          $description = osp_cart_string_to_title($c);
          $price = osp_get_fee($type, 1, $item_id, $duration, $repeat); 
          //$price = osp_get_fee($type, $quantity, $item_id, $duration, $repeat); 
          $tax = inv_param('tax')/100;

          $price = $price/(1+$tax);

          $items[] = array(
            'fk_i_invoice_id' => $invoice_id,
            's_description' => strip_tags($description <> '' ? $description : __('Service', 'invoice')),
            'i_quantity' => ($quantity > 0 ? ($type == '901' ? 1 : $quantity) : 1),
            'f_rate' => inv_db_format(($price > 0 ? $price : 0))
          );
        }

      } else {
        $price = floatval($log['i_amount'])/1000000000000;
        $tax = floatval(inv_param('tax'))/100;
        $price = $price/(1+$tax);

        $items[] = array(
          'fk_i_invoice_id' => $invoice_id,
          's_description' => ucwords(strtolower($log['s_source'] <> '' ? $log['s_source'] : __('Service', 'invoice'))),
          'i_quantity' => 1,
          'f_rate' => inv_db_format($price)
        );

      }


      // Insert items into DB
      if(count($items) > 0) {
        foreach($items as $item) {
          ModelINV::newInstance()->updateInvoiceItem($item);
        }
      }

     
      // Generate PDF
      if($invoice_id > 0) {
        inv_generate_pdf($invoice_id);
      }

      // Send email
      if($invoice_id > 0 && inv_param('auto_mail') == 1) {
        $invoice = ModelINV::newInstance()->getInvoice($invoice_id);

        inv_email_invoice($invoice);

        ModelINV::newInstance()->updateStatus($invoice_id, 2);
      }

    }
  }
}

osc_add_hook('osp_log_saved', 'inv_connect_osp');


if(osc_is_admin_user_logged_in()) {
  //osc_run_hook('osp_log_saved', 2295);
}



// PREPARE TO HEADER
function inv_header_to($user_id, $email = '') {
  $fields = trim(inv_param('fields'));
  $fields_array = explode(',', $fields);
  
  $user = User::newInstance()->findByPrimaryKey($user_id);
  $user_data = ModelInv::newInstance()->getInvoicesUserData($user_id);
  $text = '';
  
  if($user_data !== false) {
    if((inv_param('validation') == 1 && $user_data['i_vat_number_verified'] == 1) || inv_param('validation') == 0) {
      if(trim($user_data['s_header']) != '') {
        if(in_array('header', $fields_array) || $fields == '') {
          $text .= trim($user_data['s_header']);
        }
      }
      
      if(trim($user_data['s_ship_to']) != '') {
        if(in_array('ship', $fields_array) || $fields == '') {
          $text .= '<br/><br/>' . __('Shipping address', 'invoice') . '<br/>';
          $text .= trim($user_data['s_ship_to']);
        }
      }
      
      if(trim($text) != '') {
        return $text;
      }
    }
  } 
  
  if(trim($text) == '' && isset($user['pk_i_id']) && $user['pk_i_id'] > 0) {
    $data = array();

    $data[] = $user['s_name'];
    $data[] = ($user['s_phone_land'] <> '' ? $user['s_phone_land'] : $user['s_phone_mobile']);
    $data[] = $user['s_city'] . ($user['s_zip'] <> '' ? ' ' . $user['s_zip'] : '');

    if($user['s_region'] <> '') {
      $data[] = $user['s_region'] . ($user['s_country'] <> '' ? ', ' . $user['s_country'] : '');
    } else {
      $data[] = $user['s_country'];
    }

    $data[] = $user['s_email'];

    $data = array_filter(array_map('trim', $data));
    return implode("<br/>", $data);
  }

  return __('Client', 'invoice') . ' #' . $user_id . '\r\n' . $email;
}


// GENERATE PDF
function inv_generate_pdf($id) {
  $inv = ModelINV::newInstance()->getInvoice($id);

  require_once osc_base_path() . 'oc-content/plugins/invoice/src/tcpdf/tcpdf.php';
  $font = (inv_param('font') <> '' ? inv_param('font') : 'freesans');

  $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
  $pdf->SetTitle(__('Invoice', 'invoice') . ' ' . $inv['s_identifier']);
  $pdf->SetSubject(preg_replace( "/\r|\n/", "", $inv['s_to']));
  $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
  $pdf->SetMargins(2, 10, 4, 0);
  $pdf->SetHeaderMargin(0);
  $pdf->SetFooterMargin(0);
  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
  $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
  $pdf->SetFont($font, '', 10);
  $pdf->SetPrintHeader(false);
  $pdf->SetPrintFooter(false);

  $pdf->AddPage();


  // ADD LOGO
  if (inv_logo_path() <> '' && is_file(inv_logo_path())) {
    list($image_w, $image_h) = getimagesize(inv_logo_path());
    $ratio = $image_w / $image_h;
    
    $check_w = 50/$image_w;
    $check_h = 50/$image_h;

    $multiplier = min($check_w, $check_h);

    $w = $image_w*$multiplier;
    $h = $image_h*$multiplier;
    
    $pdf->Image(inv_logo_path(), 12, 13, $w, $h, '', '', false);
  }


  //$html = file_get_contents(osc_base_url() . 'oc-content/plugins/invoice/form/invoice.php?id=' . $id);

  $url = osc_base_url() . 'oc-content/plugins/invoice/form/invoice.php?id=' . $id;
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_HEADER, false);
  $html = curl_exec($curl);
  curl_close($curl);


  $file_id = ($inv['s_identifier'] <> '' ? $inv['s_identifier'] : $id);
  $file_name = $file_id . '_' . date('Ymd') . '_' . mb_generate_rand_string(6) . '.pdf';

  $pdf->writeHTML($html, true, false, true, false, '');
  $pdf->Output(osc_base_path() . 'oc-content/plugins/invoice/download/' . $file_name, 'F');

  if($inv['s_file'] <> '' && file_exists(osc_base_path() . 'oc-content/plugins/invoice/download/' . $inv['s_file'])) {
    unlink(osc_base_path() . 'oc-content/plugins/invoice/download/' . $inv['s_file']);
  }

  ModelINV::newInstance()->updateFile($id, $file_name);
  ModelINV::newInstance()->updateStatus($id, 1);
}



// CREATE LOGO FUNCTION
function inv_logo_header() {
  if(function_exists('logo_header')) {
    return logo_header();
  }
  
  $html = '<img border="0" alt="' . osc_esc_html(osc_page_title()) . '" src="' . osc_current_web_theme_url('images/logo.jpg') . '" />';
  if( file_exists( WebThemes::newInstance()->getCurrentThemePath() . "images/logo.jpg" ) ) {
    return $html;
  } else if( osc_get_preference('default_logo', 'veronika_theme') && (file_exists( WebThemes::newInstance()->getCurrentThemePath() . "images/default-logo.jpg")) ) {
    return '<img border="0" alt="' . osc_esc_html(osc_page_title()) . '" src="' . osc_current_web_theme_url('images/default-logo.jpg') . '" />';
  } else if(file_exists(osc_base_path() . "oc-content/uploads/site_logo.png")) {
    return '<img border="0" alt="' . osc_esc_html(osc_page_title()) . '" src="' . osc_base_url() . 'oc-content/uploads/site_logo.png" />';
  } else {
    return osc_page_title();
  }
}


// CREATE LOGO PATH
function inv_logo_path() {
  if(file_exists( WebThemes::newInstance()->getCurrentThemePath() . "images/logo.jpg")) {
    return WebThemes::newInstance()->getCurrentThemePath() . "images/logo.jpg";
  } else if( osc_get_preference('default_logo', 'veronika_theme') && (file_exists( WebThemes::newInstance()->getCurrentThemePath() . "images/default-logo.jpg")) ) {
    return osc_current_web_theme_url('images/default-logo.jpg');
  } else if(file_exists(osc_base_path() . "oc-content/uploads/site_logo.png")) {
    return osc_base_url() . "oc-content/uploads/site_logo.png";
  } else {
    return false;
  }
}



// GET USER DATA - AJAX
function inv_get_user_ajax() {
  $user_id = Params::getParam('id');
  
  if($user_id <> '' && $user_id > 0) {
    $user = User::newInstance()->findByPrimaryKey($user_id);
    
    $location = array($user['s_name'], $user['s_address'], $user['s_zip'] . ' ' . $user['s_city'], $user['s_region'], $user['s_country'] );
    $location = array_filter(array_map('trim', $location));
    $location = implode("\r\n", $location);
    
    echo json_encode(array('user' => array('id' => $user_id, 'name' => $user['s_name'], 'email' => $user['s_email']), 'location' => $location));
  } else {
    echo json_encode(array('user' => array('id' => 0, 'name' => '', 'email' =>''), 'location' => ''));
  }

  exit;
}

osc_add_hook('ajax_admin_inv_get_user_ajax', 'inv_get_user_ajax');



// CALCULATE SUBTOTAL
function inv_subtotal($invoice) {
  $amount = 0;

  if(isset($invoice['items']) && count($invoice['items']) > 0) {
    foreach($invoice['items'] as $item) {
      $amount += $item['i_quantity'] * $item['f_rate'];
    }
  }

  return $amount;
}


// CALCULATE TOTAL
function inv_total($invoice) {
  $amount = 0;

  $amount += inv_subtotal($invoice);

  $amount = $amount * (100 + $invoice['f_tax'])/100;
  $amount = $amount * (100 - $invoice['f_discount'])/100;

  $amount += $invoice['f_fee'];
  $amount += $invoice['f_shipping'];

  return $amount;
}


// CALCULATE BALANCE
function inv_balance($invoice) {
  $amount = 0;

  $amount += inv_total($invoice);
  $amount -= $invoice['f_paid'];

  return $amount;
}


// GET CURRENCY SYMBOL
function inv_currency_symbol($code) {
  $cc = strtoupper($code);

  $currency = inv_available_currencies();
    
  if(array_key_exists($cc, $currency)){
    if($currency[$cc] <> '') {
      return $currency[$cc];
    }
  }

  return $cc;
}


// LIST OF ALL AVAILABLE CURRENCIES AND THEIR CODES
function inv_available_currencies($only_keys = false) {
  $currency = array(
    'AED' => '&#1583;.&#1573;',
    'AFN' => '&#65;&#102;',
    'ALL' => '&#76;&#101;&#107;',
    'AMD' => '',
    'ANG' => '&#402;',
    'AOA' => '&#75;&#122;',
    'ARS' => '&#36;',
    'AUD' => '&#36;',
    'AWG' => '&#402;',
    'AZN' => '&#1084;&#1072;&#1085;',
    'BAM' => '&#75;&#77;',
    'BBD' => '&#36;',
    'BDT' => '&#2547;',
    'BGN' => '&#1083;&#1074;',
    'BHD' => '.&#1583;.&#1576;',
    'BIF' => '&#70;&#66;&#117;',
    'BMD' => '&#36;',
    'BND' => '&#36;',
    'BOB' => '&#36;&#98;',
    'BRL' => '&#82;&#36;',
    'BSD' => '&#36;',
    'BTN' => '&#78;&#117;&#46;',
    'BWP' => '&#80;',
    'BYR' => '&#112;&#46;',
    'BZD' => '&#66;&#90;&#36;',
    'CAD' => '&#36;',
    'CDF' => '&#70;&#67;',
    'CHF' => '&#67;&#72;&#70;',
    'CLF' => '',
    'CLP' => '&#36;',
    'CNY' => '&#165;',
    'COP' => '&#36;',
    'CRC' => '&#8353;',
    'CUP' => '&#8396;',
    'CVE' => '&#36;',
    'CZK' => '&#75;&#269;',
    'DJF' => '&#70;&#100;&#106;',
    'DKK' => '&#107;&#114;',
    'DOP' => '&#82;&#68;&#36;',
    'DZD' => '&#1583;&#1580;',
    'EGP' => '&#163;',
    'ETB' => '&#66;&#114;',
    'EUR' => '&#8364;',
    'FJD' => '&#36;',
    'FKP' => '&#163;',
    'GBP' => '&#163;',
    'GEL' => '&#4314;',
    'GHS' => '&#162;',
    'GIP' => '&#163;',
    'GMD' => '&#68;',
    'GNF' => '&#70;&#71;',
    'GTQ' => '&#81;',
    'GYD' => '&#36;',
    'HKD' => '&#36;',
    'HNL' => '&#76;',
    'HRK' => '&#107;&#110;',
    'HTG' => '&#71;',
    'HUF' => '&#70;&#116;',
    'IDR' => '&#82;&#112;',
    'ILS' => '&#8362;',
    'INR' => '&#8377;',
    'IQD' => '&#1593;.&#1583;',
    'IRR' => '&#65020;',
    'ISK' => '&#107;&#114;',
    'JEP' => '&#163;',
    'JMD' => '&#74;&#36;',
    'JOD' => '&#74;&#68;',
    'JPY' => '&#165;',
    'KES' => '&#75;&#83;&#104;',
    'KGS' => '&#1083;&#1074;',
    'KHR' => '&#6107;',
    'KMF' => '&#67;&#70;',
    'KPW' => '&#8361;',
    'KRW' => '&#8361;',
    'KWD' => '&#1583;.&#1603;',
    'KYD' => '&#36;',
    'KZT' => '&#1083;&#1074;',
    'LAK' => '&#8365;',
    'LBP' => '&#163;',
    'LKR' => '&#8360;',
    'LRD' => '&#36;',
    'LSL' => '&#76;', // ?
    'LTL' => '&#76;&#116;',
    'LVL' => '&#76;&#115;',
    'LYD' => '&#1604;.&#1583;',
    'MAD' => '&#1583;.&#1605;.',
    'MDL' => '&#76;',
    'MGA' => '&#65;&#114;', // ?
    'MKD' => '&#1076;&#1077;&#1085;',
    'MMK' => '&#75;',
    'MNT' => '&#8366;',
    'MOP' => '&#77;&#79;&#80;&#36;',
    'MRO' => '&#85;&#77;',
    'MUR' => '&#8360;',
    'MVR' => '.&#1923;',
    'MWK' => '&#77;&#75;',
    'MXN' => '&#36;',
    'MYR' => '&#82;&#77;',
    'MZN' => '&#77;&#84;',
    'NAD' => '&#36;',
    'NGN' => '&#8358;',
    'NIO' => '&#67;&#36;',
    'NOK' => '&#107;&#114;',
    'NPR' => '&#8360;',
    'NZD' => '&#36;',
    'OMR' => '&#65020;',
    'PAB' => '&#66;&#47;&#46;',
    'PEN' => '&#83;&#47;&#46;',
    'PGK' => '&#75;',
    'PHP' => '&#8369;',
    'PKR' => '&#8360;',
    'PLN' => '&#122;&#322;',
    'PYG' => '&#71;&#115;',
    'QAR' => '&#65020;',
    'RON' => '&#108;&#101;&#105;',
    'RSD' => '&#1044;&#1080;&#1085;&#46;',
    'RUB' => '&#1088;&#1091;&#1073;',
    'RWF' => '&#1585;.&#1587;',
    'SAR' => '&#65020;',
    'SBD' => '&#36;',
    'SCR' => '&#8360;',
    'SDG' => '&#163;',
    'SEK' => '&#107;&#114;',
    'SGD' => '&#36;',
    'SHP' => '&#163;',
    'SLL' => '&#76;&#101;',
    'SOS' => '&#83;',
    'SRD' => '&#36;',
    'STD' => '&#68;&#98;',
    'SVC' => '&#36;',
    'SYP' => '&#163;',
    'SZL' => '&#76;',
    'THB' => '&#3647;',
    'TJS' => '&#84;&#74;&#83;',
    'TMT' => '&#109;',
    'TND' => '&#1583;.&#1578;',
    'TOP' => '&#84;&#36;',
    'TRY' => '&#8356;',
    'TTD' => '&#36;',
    'TWD' => '&#78;&#84;&#36;',
    'TZS' => '',
    'UAH' => '&#8372;',
    'UGX' => '&#85;&#83;&#104;',
    'USD' => '&#36;',
    'UYU' => '&#36;&#85;',
    'UZS' => '&#1083;&#1074;',
    'VEF' => '&#66;&#115;',
    'VND' => '&#8363;',
    'VUV' => '&#86;&#84;',
    'WST' => '&#87;&#83;&#36;',
    'XAF' => '&#70;&#67;&#70;&#65;',
    'XCD' => '&#36;',
    'XDR' => '',
    'XOF' => '',
    'XPF' => '&#70;',
    'YER' => '&#65020;',
    'ZAR' => '&#82;',
    'ZMK' => '&#90;&#75;',
    'ZWL' => '&#90;&#36;'
  );

  if($only_keys) {
    return array_keys($currency);
  } else {
    return $currency;
  }
}




// CORE FUNCTIONS
function inv_param($name) {
  return osc_get_preference($name, 'plugin-invoice');
}


if(!function_exists('mb_param_update')) {
  function mb_param_update( $param_name, $update_param_name, $type = NULL, $plugin_var_name = NULL ) {
  
    $val = '';
    if( $type == 'check') {

      // Checkbox input
      if( Params::getParam( $param_name ) == 'on' ) {
        $val = 1;
      } else {
        if( Params::getParam( $update_param_name ) == 'done' ) {
          $val = 0;
        } else {
          $val = ( osc_get_preference( $param_name, $plugin_var_name ) != '' ) ? osc_get_preference( $param_name, $plugin_var_name ) : '';
        }
      }
    } else {

      // Other inputs (text, password, ...)
      if( Params::getParam( $update_param_name ) == 'done' && Params::existParam($param_name)) {
        $val = Params::getParam( $param_name );
      } else {
        $val = ( osc_get_preference( $param_name, $plugin_var_name) != '' ) ? osc_get_preference( $param_name, $plugin_var_name ) : '';
      }
    }


    // If save button was pressed, update param
    if( Params::getParam( $update_param_name ) == 'done' ) {

      if(osc_get_preference( $param_name, $plugin_var_name ) == '') {
        osc_set_preference( $param_name, $val, $plugin_var_name, 'STRING');  
      } else {
        $dao_preference = new Preference();
        $dao_preference->update( array( "s_value" => $val ), array( "s_section" => $plugin_var_name, "s_name" => $param_name ));
        osc_reset_preferences();
        unset($dao_preference);
      }
    }

    return $val;
  }
}


// CHECK IF RUNNING ON DEMO
function inv_is_demo() {
  if(osc_logged_admin_username() == 'admin') {
    return false;
  } else if(isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'],'mb-themes') !== false || strpos($_SERVER['HTTP_HOST'],'abprofitrade') !== false)) {
    return true;
  } else {
    return false;
  }
}


if(!function_exists('message_ok')) {
  function message_ok( $text ) {
    $final  = '<div class="flashmessage flashmessage-ok flashmessage-inline">';
    $final .= $text;
    $final .= '</div>';
    echo $final;
  }
}


if(!function_exists('message_error')) {
  function message_error( $text ) {
    $final  = '<div class="flashmessage flashmessage-error flashmessage-inline">';
    $final .= $text;
    $final .= '</div>';
    echo $final;
  }
}


if( !function_exists('osc_is_contact_page') ) {
  function osc_is_contact_page() {
    $location = Rewrite::newInstance()->get_location();
    $section = Rewrite::newInstance()->get_section();
    if( $location == 'contact' ) {
      return true ;
    }

    return false ;
  }
}


// COOKIES WORK
if(!function_exists('mb_set_cookie')) {
  function mb_set_cookie($name, $val) {
    Cookie::newInstance()->set_expires( 86400 * 30 );
    Cookie::newInstance()->push($name, $val);
    Cookie::newInstance()->set();
  }
}


if(!function_exists('mb_get_cookie')) {
  function mb_get_cookie($name) {
    return Cookie::newInstance()->get_value($name);
  }
}

if(!function_exists('mb_drop_cookie')) {
  function mb_drop_cookie($name) {
    Cookie::newInstance()->pop($name);
  }
}


if(!function_exists('mb_generate_rand_string')) {
  function mb_generate_rand_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
  }
}


?>