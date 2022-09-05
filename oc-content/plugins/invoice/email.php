<?php

// CONTACT COMPANY MAIL
function inv_email_invoice($invoice, $email = '') {
  inv_include_mailer();

  $mPages = new Page() ;
  $aPage = $mPages->findByInternalName('inv_mail_invoice') ;
  $locale = osc_current_user_locale();
  $content = array();


  if(isset($aPage['locale'][$locale]['s_title'])) {
    $content = $aPage['locale'][$locale];
  } else {
    $content = current($aPage['locale'] <> '' ? $aPage['locale'] : array());
  }

  $email = ($email <> '' ? $email : $invoice['s_email']);
  $user = User::newInstance()->findByPrimaryKey($invoice['fk_i_user_id']);

  ModelINV::newInstance()->updateEmail($invoice['pk_i_id'], $email);

  $file_path = osc_base_path() . 'oc-content/plugins/invoice/download/' . $invoice['s_file'];

  $words = array();
  $words[] = array('{USER_NAME}', '{INVOICE_ID}', '{WEB_URL}', '{WEB_TITLE}');
  $words[] = array(@$user['s_name'], @$invoice['s_identifier'], osc_base_url(), osc_page_title());


  $title = osc_mailBeauty($content['s_title'], $words);
  $body  = osc_mailBeauty($content['s_text'], $words);

  if(trim($email) <> '') {
    $emailParams = array(
      'subject' => $title,
      'to' => $email,
      'to_name' => @$user['s_name'],
      'reply_to' => $email,
      'body' => $body,
      'alt_body' => $body,
      'attachment' => $file_path
    );

    osc_sendMail($emailParams);
  }
}

?>