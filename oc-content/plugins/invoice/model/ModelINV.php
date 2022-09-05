<?php
class ModelINV extends DAO {
private static $instance;

public static function newInstance() {
  if( !self::$instance instanceof self ) {
    self::$instance = new self;
  }
  return self::$instance;
}

function __construct() {
  parent::__construct();
}


public function getTable_invoice() {
  return DB_TABLE_PREFIX.'t_invoice';
}

public function getTable_invoice_item() {
  return DB_TABLE_PREFIX.'t_invoice_item';
}

public function getTable_invoice_user() {
  return DB_TABLE_PREFIX.'t_invoice_user';
}

public function getTable_item() {
  return DB_TABLE_PREFIX.'t_item';
}

public function getTable_user() {
  return DB_TABLE_PREFIX.'t_user';
}

public function getTable_osp_log() {
  return DB_TABLE_PREFIX.'t_osp_log';
}

public function getTable_sms_log() {
  return DB_TABLE_PREFIX.'t_log_sms_payment';
}

public function getTable_pp_log() {
  return DB_TABLE_PREFIX.'t_payment_pro_invoice';
}

public function getTable_pp_log_item() {
  return DB_TABLE_PREFIX.'t_payment_pro_invoice_row';
}



public function import($file) {
  $path = osc_plugin_resource($file);
  $sql = file_get_contents($path);

  if(!$this->dao->importSQL($sql) ){
    throw new Exception("Error importSQL::ModelINV<br>" . $file . "<br>" . $this->dao->getErrorLevel() . " - " . $this->dao->getErrorDesc() );
  }
}


public function install($version = '') {
  if($version == '') {
    $this->import('invoice/model/struct.sql');

    osc_set_preference('version', 100, 'plugin-invoice', 'INTEGER');


    $locales = OSCLocale::newInstance()->listAllEnabled();

    // CONTACT COMPANY
    foreach($locales as $l) {
      $email_text  = '<p>Hi {USER_NAME}</p>';
      $email_text .= '<p>You have recieved new invoice {INVOICE_ID} and is attached to this email.</p>';

      $email_text .= '<p><br/></p>';
      $email_text .= '<p>Thank you, <br />{WEB_TITLE}</p>';

      $inv_mail_invoice = array();
      $inv_mail_invoice[$l['pk_c_code']]['s_title'] = '{WEB_TITLE} - New invoice {INVOICE_ID}';
      $inv_mail_invoice[$l['pk_c_code']]['s_text'] = $email_text;
    }
    
    Page::newInstance()->insert( array('s_internal_name' => 'inv_mail_invoice', 'b_indelible' => '1'), $inv_mail_invoice);
  }
  
  
  if($version == '' || $version < 110) {
    $this->import('invoice/model/struct_update_110.sql');
  }
}



// DO QUERIES ON VERSION UPDATE
public function versionUpdate() {
  $version = (inv_param('version') <> '' ? inv_param('version') : 100);    // v100 is initial

  if($version < 110) { 
    $this->install($version);
    osc_set_preference('version', 110, 'plugin-invoice', 'INTEGER');
  }
}


public function uninstall() {
  // DELETE ALL TABLES
  $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_invoice()));
  $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_invoice_item()));
  $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_invoice_user()));


  // DELETE ALL PREFERENCES
  $db_prefix = DB_TABLE_PREFIX;
  $query = "DELETE FROM {$db_prefix}t_preference WHERE s_section = 'plugin-invoice'";
  $this->dao->query($query);


  // DELETE MAILS
  $page_invoice = Page::newInstance()->findByInternalName('inv_mail_invoice');
  Page::newInstance()->deleteByPrimaryKey($page_invoice['pk_i_id']);
}



// GET OSCLASS PAY PAYMENT LOG
public function getOspLog($id) {
  $this->dao->select('*');
  $this->dao->from($this->getTable_osp_log());

  $this->dao->where('pk_i_id', $id);
  
  $result = $this->dao->get();
  
  if($result) {
    return $result->row();
  }
  
  return false;
}


// GET SMS PAYMENTS (FORTUMO) PAYMENT LOG
public function getSmsLog($id) {
  $this->dao->select('*');
  $this->dao->from($this->getTable_sms_log());

  $this->dao->where('record_id', $id);
  
  $result = $this->dao->get();
  
  if($result) {
    return $result->row();
  }
  
  return false;
}


// GET REPORTS
public function getReports() {
  $files = glob(osc_base_path() . 'oc-content/plugins/invoice/report/*.csv');
  $output = array();

  if(count($files) > 0) {
    foreach($files as $f) {
      $data = pathinfo($f);
      $output[] = array('name' => $data['filename'], 'extension' => $data['extension'], 'path' => $f, 'url' => osc_base_url() . 'oc-content/plugins/invoice/report/' . $data['basename'], 'date' => date('Y-m-d H:i:s', filemtime($f)));
    }


    array_multisort(array_column($output, 'date'), SORT_DESC, $output);
  }

  return $output;
}


// GET PAYMENTS PRO PAYMENT LOGS
public function getPpLog($id) {
  $this->dao->select('*');
  $this->dao->from($this->getTable_pp_log());

  $this->dao->where('pk_i_id', $id);
  
  $result = $this->dao->get();
  
  if($result) {
    $data = $result->row();
    $data['items'] = $this->getPpLogItems($id);
    return $data;
  }
  
  return false;
}


// GET PAYMENTS PRO PAYMENT LOG ITEMS
public function getPpLogItems($id) {
  $this->dao->select('*');
  $this->dao->from($this->getTable_pp_log_item());

  $this->dao->where('fk_i_invoice_id', $id);
  
  $result = $this->dao->get();
  
  if($result) {
    return $result->result();
  }
  
  return array();
}




// GET ALL INVOICES
public function getInvoices($options = array(), $only_count = false) {
  $selector = '';
  
  if($only_count === true) {
    $selector = 'count(pk_i_id) as i_count';
  }
  
  $this->dao->select($selector);
  $this->dao->from($this->getTable_invoice());

  if(isset($options['invoiceId']) && trim($options['invoiceId']) != '') {
    $this->dao->where(sprintf('s_identifier like "%%%s%%"', trim($options['invoiceId'])));
  } 
  
  if(isset($options['billTo']) && trim($options['billTo']) != '') {
    $this->dao->where(sprintf('s_to like "%%%s%%"', trim($options['billTo'])));
  } 
  
  if(isset($options['uemail']) && trim($options['uemail']) != '') {
    $this->dao->where(sprintf('s_email like "%%%s%%"', trim($options['uemail'])));
  } 
  
  if(isset($options['date']) && trim($options['date']) != '') {
    $this->dao->where(sprintf('dt_date like "%%%s%%"', trim($options['date'])));
  } 
  
  if(isset($options['balancemin']) && trim($options['balancemin']) != '') {
    $this->dao->where(sprintf('f_balance >= %d', trim($options['balancemin'])));
  } 
  
  if(isset($options['balancemax']) && trim($options['balancemax']) != '') {
    $this->dao->where(sprintf('f_balance <= %d', trim($options['balancemax'])));
  } 
  
  if(isset($options['status']) && trim($options['status']) != '') {
    $stat = ($options['status'] == 5 ? 0 : $options['status']);   // 5 is placeholder on search
    $this->dao->where(sprintf('coalesce(i_status, 0) = "%d"', $stat));
  } 

 
  if($only_count !== true) {
    // $limit[0] == limit; $limit[1] == page
    $page = (isset($options['pageId']) ? $options['pageId'] : 0);
    $per_page = (isset($options['per_page']) ? $options['per_page'] : -1);
    
    if($per_page < 0) {
      $per_page = 20;
    }
      
    if($page > 0 && $per_page > 0) {
      $this->dao->limit(($page-1)*$per_page, $per_page);
    } else if($per_page > 0) {
      $this->dao->limit($per_page);
    }  

    $this->dao->orderby('pk_i_id DESC');
  }

  $result = $this->dao->get();
  
  if($result) {
    if($only_count === true) {
      $data = $result->row();
      return isset($data['i_count']) ? $data['i_count'] : 0;
    } else {
      return $result->result();
    }
  }

  return ($only_count ? 0 : array());
}


// GET INVOICES FOR REPORT
public function getInvoicesForReport($start, $end, $date_type = 'DATE', $status = -1) {
  $this->dao->select();
  $this->dao->from($this->getTable_invoice());

  if($date_type == 'DATE') {
    $this->dao->where('date(dt_date) between "' . date('Y-m-d', strtotime($start)) . '" AND "' . date('Y-m-d', strtotime($end)) . '"');
  } else {
    $this->dao->where('date(dt_due_date) between "' . date('Y-m-d', strtotime($start)) . '" AND "' . date('Y-m-d', strtotime($end)) . '"');
  }

  if($status <> -1) {
    $this->dao->where('(coalesce(i_status,0) = "' . $status . '")');
  }

  $result = $this->dao->get();
  
  if($result) {
    return $result->result();
  }

  return array();
}




// GET USER INVOICES
public function getInvoicesByUserId($user_id) {
  if($user_id <= 0) { 
    return array();
  }

  $this->dao->select();
  $this->dao->from($this->getTable_invoice());

  $this->dao->where('fk_i_user_id', $user_id);
  $this->dao->orderby('pk_i_id DESC');

  $result = $this->dao->get();
  
  if($result) {
    return $result->result();
  }

  return array();
}

// GET USER INVOICES
public function getInvoicesUserData($user_id) {
  if($user_id <= 0) { 
    return false;
  }

  $this->dao->select();
  $this->dao->from($this->getTable_invoice_user());
  $this->dao->where('fk_i_user_id', $user_id);

  $result = $this->dao->get();
  
  if($result) {
    return $result->row();
  }

  return false;
}


// GET USER INVOICES
public function getProfiles($options = array()) {
  $this->dao->select('i.*, u.s_name, u.s_email');
  $this->dao->from($this->getTable_invoice_user() . ' as i');
  $this->dao->join($this->getTable_user() . ' as u', 'i.fk_i_user_id = u.pk_i_id', 'LEFT OUTER');

  if(isset($options['uname']) && trim($options['uname']) != '') {
    $this->dao->where(sprintf('u.s_name like "%%%s%%"', trim($options['uname'])));
  }

  if(isset($options['uemail']) && trim($options['uemail']) != '') {
    $this->dao->where(sprintf('u.s_email like "%%%s%%"', trim($options['uemail'])));
  } 
  
  if(isset($options['header']) && trim($options['header']) != '') {
    $this->dao->where(sprintf('i.s_header like "%%%s%%"', trim($options['header'])));
  } 
  
  if(isset($options['ship']) && trim($options['ship']) != '') {
    $this->dao->where(sprintf('i.s_ship_to like "%%%s%%"', trim($options['ship'])));
  }
  
  if(isset($options['vat']) && trim($options['vat']) != '') {
    $this->dao->where(sprintf('i.s_vat_number like "%%%s%%"', trim($options['vat'])));
  }
  
  if(isset($options['vat_local']) && trim($options['vat_local']) != '') {
    $this->dao->where(sprintf('i.s_vat_number_local like "%%%s%%"', trim($options['vat_local'])));
  }
  
  if(isset($options['status']) && $options['status'] !== '') {
    $stat = ($options['status'] == 5 ? 0 : $options['status']);   // 5 is placeholder on search
    $this->dao->where('i.i_vat_number_verified', $stat);
  }
  
  
  // $limit[0] == limit; $limit[1] == page
  $page = (isset($options['page']) ? $options['page'] : 0);
  $per_page = (isset($options['per_page']) ? $options['per_page'] : -1);
  
  // if($per_page < 0) {
    // $per_page = 9999;
  // }
    
  if($page > 0 && $per_page > 0) {
    $this->dao->limit(($page-1)*$per_page, $per_page);
  } else if($per_page > 0) {
    $this->dao->limit($per_page);
  }  
  
  $result = $this->dao->get();
  
  if($result) {
    return $result->result();
  }

  return array();
}


// APPROVE PROFILE
public function approveProfile($id) {
  return $this->dao->update($this->getTable_invoice_user(), array('i_vat_number_verified' => 1), array('fk_i_user_id' => $id));
}

// REJECT PROFILE
public function rejectProfile($id) {
  return $this->dao->update($this->getTable_invoice_user(), array('i_vat_number_verified' => 9), array('fk_i_user_id' => $id));
}

// REMOVE PROFILE
public function removeProfile($id) {
  return $this->dao->delete($this->getTable_invoice_user(), array('fk_i_user_id' => $id));
}


// GET INVOICE ITEMS
public function getInvoiceItems($invoice_id) {
  $this->dao->select();
  $this->dao->from($this->getTable_invoice_item());

  $this->dao->where('fk_i_invoice_id', $invoice_id);

  $result = $this->dao->get();
  
  if($result) {
    return $result->result();
  }

  return array();
}


// GET INVOICE BY ID
public function getInvoice($id) {
  $this->dao->select();
  $this->dao->from($this->getTable_invoice());

  $this->dao->where('pk_i_id', $id);

  $result = $this->dao->get();
  
  if($result) {
    $data = $result->row();
    $data['items'] = $this->getInvoiceItems($id);

    return $data;
  }

  return array();
}



// UPDATE INVOICE
public function updateInvoice($params) {
  $this->dao->replace($this->getTable_invoice(), $params);
  return $this->dao->insertedId();
}


// UPDATE INVOICE ITEM
public function updateInvoiceItem($params) {
  $this->dao->replace($this->getTable_invoice_item(), $params);
  return $this->dao->insertedId();
}

// UPDATE INVOICE USER DATA
public function updateInvoiceUserData($params) {
  $this->dao->replace($this->getTable_invoice_user(), $params);
  return $this->dao->insertedId();
}


// UPDATE FILE
public function updateFile($id, $name) {
  return $this->dao->update($this->getTable_invoice(), array('s_file' => $name), array('pk_i_id' => $id));
}


// UPDATE STATUS
public function updateStatus($id, $status) {
  return $this->dao->update($this->getTable_invoice(), array('i_status' => $status), array('pk_i_id' => $id));
}


// UPDATE EMAIL
public function updateEmail($id, $email) {
  return $this->dao->update($this->getTable_invoice(), array('s_email' => $email), array('pk_i_id' => $id));
}


// REMOVE INVOICE
public function removeInvoice($id) {
  return $this->dao->delete($this->getTable_invoice(), array('pk_i_id' => $id));
}


// REMOVE USER DATA
public function removeInvoiceUserData($user_id) {
  return $this->dao->delete($this->getTable_invoice_user(), array('fk_i_user_id' => $user_id));
}


}
?>