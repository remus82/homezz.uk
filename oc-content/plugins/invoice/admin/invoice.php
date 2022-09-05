<?php
  error_reporting(E_ERROR | E_WARNING | E_PARSE);

  $currencies = inv_available_currencies(true);
  $symbols = inv_available_currencies();

  $id = Params::getParam('id');


  // Create menu
  $title = ($id > 0 ? __('Edit Invoice', 'invoice') : __('Create New Invoice', 'invoice'));
  inv_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = mb_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check or value


  // DELETE
  if(Params::getParam('what') == 'remove' && Params::getParam('id') > 0 && !inv_is_demo()) { 
    ModelINV::newInstance()->removeInvoice(Params::getParam('id'));
    message_ok(__('Invoice removed', 'invoice'));
  }


  // RE-OPEN
  if(Params::getParam('what') == 'open' && Params::getParam('id') > 0 && !inv_is_demo()) { 
    ModelINV::newInstance()->updateStatus(Params::getParam('id'), 1);
    message_ok(__('Invoice re-opened', 'invoice'));
  }


  // UPDATE
  if(Params::getParam('plugin_action') == 'update' && !inv_is_demo()) { 
    if($id > 0) {
      message_ok(__('Invoice updated', 'invoice'));
    } else {
      message_ok(__('Invoice created', 'invoice'));
    }

    $inv = ModelINV::newInstance()->getInvoice($id);
    $post = Params::getParamsAsArray();


    // Invoice data
    $invoice_data = array(
      'fk_i_user_id' => Params::getParam('fk_i_user_id'),
      's_identifier' => Params::getParam('s_identifier'),
      's_title' => Params::getParam('s_title'),
      's_from' => Params::getParam('s_from'),
      's_to' => Params::getParam('s_to'),
      'dt_date' => (Params::getParam('dt_date') <> '' ? date('Y-m-d', strtotime(Params::getParam('dt_date'))) : null),
      'dt_due_date' => (Params::getParam('dt_due_date') <> '' ? date('Y-m-d', strtotime(Params::getParam('dt_due_date'))) : null),
      'f_paid' => Params::getParam('f_paid'),
      'f_amount' => Params::getParam('f_amount'),
      'f_balance' => Params::getParam('f_balance'),
      'f_discount' => Params::getParam('f_discount'),
      'f_shipping' => Params::getParam('f_shipping'),
      'f_fee' => Params::getParam('f_fee'),
      'f_tax' => Params::getParam('f_tax'),
      's_notes' => Params::getParam('s_notes'),
      's_terms' => Params::getParam('s_terms'),
      's_currency' => Params::getParam('s_currency'),
      's_email' => Params::getParam('s_email'),
      's_file' => @$inv['s_file'],
      's_comment' => Params::getParam('s_comment'),
      'i_status' => @$inv['i_status'],
      's_file' => @$inv['s_file'],
      's_cart' => @$inv['s_cart'],
      's_source' => @$inv['s_source'],
      'i_payment_id' => @$inv['i_payment_id']
    );

    if($id > 0) {
      $invoice_data['pk_i_id'] = $id;
    } else {
      if(Params::getParam('s_identifier') == inv_param('invoice_order') && is_numeric(inv_param('invoice_order')) && inv_param('invoice_order') <> '') {
        osc_set_preference( 'invoice_order', inv_param('invoice_order')+1, 'plugin-invoice', 'INTEGER');  // increase invoice order
      }
    }

    $id = ModelINV::newInstance()->updateInvoice($invoice_data);

    Params::setParam('pk_i_id', $id);


    // Invoice items
    $items = array();

    foreach($post as $name => $value) {
      if(substr($name, 0, 7) === "invitem") {
        $item = explode('_', $name);  // [0] - invitem, [1] - name, [2] - id

        if($item[2] <> '') {  //placeholder
          if($item[2] > 0) {
            $items[$item[2]]['pk_i_id'] = $item[2];
          }

          $items[$item[2]]['fk_i_invoice_id'] = $id;


          if($item[1] == 'description') {
            $items[$item[2]]['s_description'] = $value;
          }

          if($item[1] == 'rate') {
            $items[$item[2]]['f_rate'] = $value;
          }

          if($item[1] == 'quantity') {
            $items[$item[2]]['i_quantity'] = $value;
          }
        }

      }
    }

 
    if(count($items) > 0) {
      foreach($items as $it) {
        ModelINV::newInstance()->updateInvoiceItem($it);
      }
    }
  }


  $inv = array();
  $user = array();
  
  if($id > 0) { 
    $inv = ModelINV::newInstance()->getInvoice($id);
  }

  if(isset($inv['fk_i_user_id'])) {
    $user = User::newInstance()->findByPrimaryKey($inv['fk_i_user_id']);
  }

  // GENERATE PDF
  if(Params::getParam('what') == 'pdf' && Params::getParam('id') > 0) { 

    inv_generate_pdf(Params::getParam('id'));

    message_ok(__('Invoice PDF generated', 'invoice'));
    $inv = ModelINV::newInstance()->getInvoice($id);

  }


  // MAIL PDF
  if(Params::getParam('what') == 'email' && Params::getParam('id') > 0) { 
    $inv = ModelINV::newInstance()->getInvoice(Params::getParam('id'));

    inv_email_invoice($inv, Params::getParam('email_enter'));

    ModelINV::newInstance()->updateStatus(Params::getParam('id'), 2);
    $inv = ModelINV::newInstance()->getInvoice($id);

    message_ok(sprintf(__('Invoice sent to %s', 'invoice'), (Params::getParam('email_enter') <> '' ? Params::getParam('email_enter') : $inv['s_email'])));
  }


  // GET PROPER INVOICE ORDER
  $invoice_order = inv_order_number($inv);
?>


<div class="mb-body">

  <!-- LIST SECTION -->
  <div class="mb-box mb-bp">
    <div class="mb-head">
      <i class="fa fa-file-text-o"></i> <?php _e('Invoice Details', 'invoice'); ?>
      &nbsp;<?php if(@$inv['i_status'] == 9) { ?><strong>[<?php _e('Cancelled', 'invoice'); ?>]</strong><?php } ?>
    </div>

    <div class="mb-inside mb-invoice">
      <form name="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>invoice.php" />
        <input type="hidden" name="plugin_action" value="update" />

        <div class="mb-left">
          <div class="mb-ib">
            <?php if(@$inv['i_status'] == 9) { ?>
              <div class="mb-stamp"><?php _e('Cancelled', 'invoice'); ?></div>
            <?php } ?>

            <div class="mb-h-left">
               <div class="mb-row">
                 <div class="mb-logo-img">
                   <?php echo inv_logo_header(); ?>
                 </div>
               </div>

               <div class="mb-row">
                 <?php 
                   $from = (@$inv['s_from'] <> '' ? $inv['s_from'] : inv_param('from'));
                   $breaks = array("<br />","<br>","<br/>");  
                   $from = str_ireplace($breaks, "\r\n", $from);                     
                 ?>
                 <textarea id="s_from" name="s_from" placeholder="<?php echo osc_esc_js(__('Who is this invoice from?', 'invoice')); ?>" required><?php echo $from; ?></textarea>
               </div>

               <div class="mb-row">
                 <label for="s_to"><?php _e('Bill to', 'invoice'); ?></label>
                 <?php 
                   $breaks = array("<br />","<br>","<br/>");  
                   $to = str_ireplace($breaks, "\r\n", @$inv['s_to']);
                 ?>
                 <textarea id="s_to" name="s_to" placeholder="<?php echo osc_esc_js(__('Who is this invoice to?', 'invoice')); ?>" required><?php echo $to; ?></textarea>
               </div>
            </div>


            <div class="mb-h-right">
               <div class="mb-row mb-inv-title">
                 <input type="text" name="s_title" id="s_title" value="<?php echo (@$inv['s_title'] <> '' ? $inv['s_title'] : __('Invoice', 'invoice')); ?>" />
               </div>

               <div class="mb-row mb-inv-id">
                 <div class="mb-input-desc-left">#</div>
                 <input type="text" name="s_identifier" id="s_identifier" value="<?php echo $invoice_order; ?>" />
               </div>

               <div class="mb-row">&nbsp;</div>

               <div class="mb-row mb-dates">
                 <label for="dt_date"><?php _e('Date', 'invoice'); ?></label>
                 <input type="text" name="dt_date" id="dt_date" value="<?php echo (@$inv['dt_date'] <> '' ? date('j. M Y', strtotime($inv['dt_date'])) : date('j. M Y')); ?>" />
               </div>

               <div class="mb-row mb-dates">
                 <label for="dt_due_date"><?php _e('Due Date', 'invoice'); ?></label>
                 <input type="text" name="dt_due_date" id="dt_due_date" value="<?php echo (@$inv['dt_due_date'] <> '' ? date('j. M Y', strtotime($inv['dt_due_date'])) : ''); ?>" />
               </div>

               <div class="mb-row mb-balance">
                 <label for="f_balance"><?php _e('Balance Due', 'invoice'); ?></label>
                 <div class="mb-price-wrap">
                   <div class="mb-currency"><?php echo inv_currency_symbol(@$inv['s_currency'] <> '' ? $inv['s_currency'] : inv_param('currency')); ?></div>
                   <input type="text" name="f_balance" id="f_balance" value="<?php echo number_format(@$inv['f_balance'] <> '' ? $inv['f_balance'] : 0, inv_param('decimals'), '.', ''); ?>" readonly />
                 </div>
               </div>
            </div>

            <div class="mb-items">
              <div class="mb-r-head">
                <div class="mb-col-12"><?php _e('Item', 'invoice'); ?></div>
                <div class="mb-col-3"><?php _e('Quantity', 'invoice'); ?></div>
                <div class="mb-col-4"><?php _e('Rate', 'invoice'); ?></div>
                <div class="mb-col-4"><?php _e('Amount', 'invoice'); ?></div>
                <div class="mb-col-1">&nbsp;</div>
              </div>

              <div class="mb-ph-row">
                <div class="mb-r">
                  <div class="mb-col-12"><input type="text" name="invitem_description_" value="" placeholder="<?php echo osc_esc_html(__('Description of service or product...', 'invoice')); ?>" /></div>
                  <div class="mb-col-3"><input type="text" name="invitem_quantity_" class="invitem_quantity" value="1" placeholder="<?php echo osc_esc_html(__('Quantity', 'invoice')); ?>" /></div>
                  <div class="mb-col-4">
                    <div class="mb-price-wrap">
                      <div class="mb-currency"><?php echo inv_currency_symbol(@$inv['s_currency'] <> '' ? $inv['s_currency'] : inv_param('currency')); ?></div>
                      <input type="text" name="invitem_rate_" class="invitem_rate" value="<?php echo number_format(0, inv_param('decimals'), '.', ''); ?>" placeholder="<?php echo osc_esc_html(__('Rate', 'invoice')); ?>" />
                    </div>
                  </div>

                  <div class="mb-col-4">
                    <div class="mb-price-box">
                      <div class="mb-currency-alt"><?php echo inv_currency_symbol(@$inv['s_currency'] <> '' ? $inv['s_currency'] : inv_param('currency')); ?></div>
                      <span><?php echo number_format(0, inv_param('decimals'), '.', ''); ?></span>
                    </div>
                  </div>

                  <div class="mb-col-1 mb-item-remove"><a href="#" data-item-id="0"><i class="fa fa-times"></i></a></div>
                </div>
              </div>

              <?php if(isset($inv['items']) && count($inv['items']) > 0) { ?>
                <?php foreach($inv['items'] as $item) { ?>
                  <div class="mb-r">
                    <div class="mb-col-12"><input type="text" required name="invitem_description_<?php echo $item['pk_i_id']; ?>" value="<?php echo $item['s_description']; ?>" placeholder="<?php echo osc_esc_html(__('Description of service or product...', 'invoice')); ?>" /></div>
                    <div class="mb-col-3"><input type="text" required name="invitem_quantity_<?php echo $item['pk_i_id']; ?>" class="invitem_quantity" value="<?php echo ($item['i_quantity'] <> '' ? $item['i_quantity'] : 1); ?>" placeholder="<?php echo osc_esc_html(__('Quantity', 'invoice')); ?>" /></div>
                    <div class="mb-col-4">
                      <div class="mb-price-wrap">
                        <div class="mb-currency"><?php echo inv_currency_symbol($inv['s_currency'] <> '' ? $inv['s_currency'] : inv_param('currency')); ?></div>
                        <input type="text" required name="invitem_rate_<?php echo $item['pk_i_id']; ?>" class="invitem_rate" value="<?php echo number_format($item['f_rate'] <> '' ? $item['f_rate'] : 0, inv_param('decimals'), '.', ''); ?>" placeholder="<?php echo osc_esc_html(__('Rate', 'invoice')); ?>" />
                      </div>
                    </div>

                    <div class="mb-col-4">
                      <div class="mb-price-box">
                        <div class="mb-currency-alt"><?php echo inv_currency_symbol($inv['s_currency'] <> '' ? $inv['s_currency'] : inv_param('currency')); ?></div>
                        <span><?php echo number_format($item['i_quantity'] * $item['f_rate'], inv_param('decimals'), '.', ''); ?></span>
                      </div>
                    </div>

                    <div class="mb-col-1 mb-item-remove"><a href="#" data-item-id="<?php echo $inv['pk_i_id']; ?>"><i class="fa fa-times"></i></a></div>
                  </div>
                <?php } ?>

              <?php } else { ?>
                <div class="mb-r">
                  <div class="mb-col-12"><input type="text" name="invitem_description_-1" value="" placeholder="<?php echo osc_esc_html(__('Description of service or product...', 'invoice')); ?>" /></div>
                  <div class="mb-col-3"><input type="text" name="invitem_quantity_-1" class="invitem_quantity" value="1" placeholder="<?php echo osc_esc_html(__('Quantity', 'invoice')); ?>" /></div>
                  <div class="mb-col-4">
                    <div class="mb-price-wrap">
                      <div class="mb-currency"><?php echo inv_currency_symbol(@$inv['s_currency'] <> '' ? $inv['s_currency'] : inv_param('currency')); ?></div>
                      <input type="text" name="invitem_rate_-1" class="invitem_rate" value="<?php echo number_format(0, inv_param('decimals'), '.', ''); ?>" placeholder="<?php echo osc_esc_html(__('Rate', 'invoice')); ?>" />
                    </div>
                  </div>

                  <div class="mb-col-4">
                    <div class="mb-price-box">
                      <div class="mb-currency-alt"><?php echo inv_currency_symbol(@$inv['s_currency'] <> '' ? $inv['s_currency'] : inv_param('currency')); ?></div>
                      <span><?php echo number_format(0, inv_param('decimals'), '.', ''); ?></span>
                    </div>
                  </div>

                  <div class="mb-col-1 mb-item-remove"><a href="#" data-item-id="0"><i class="fa fa-times"></i></a></div>
                </div>

              <?php } ?>

              <div class="mb-new-item">
                <a href="#" class="mb-add-item"><i class="fa fa-plus-circle"></i> <span><?php _e('Add new line', 'invoice'); ?></span></a>
              </div>
            </div>

            <div class="mb-subt">
              <div class="mb-row">
                <label for="s_subtotal"><?php _e('Subtotal', 'invoice'); ?></label>
                <div class="mb-price-box">
                  <div class="mb-currency-alt"><?php echo inv_currency_symbol(@$inv['s_currency'] <> '' ? $inv['s_currency'] : inv_param('currency')); ?></div>
                  <span class="inv-subtotal"><?php echo number_format(inv_subtotal($inv), inv_param('decimals'), '.', ''); ?></span>
                </div>
              </div>

              <div class="mb-row mb-discount" <?php if(@$inv['f_discount'] <= 0) { ?>style="display:none;"<?php } ?> data-class="discount" >
                <label for="f_discount"><?php _e('Discounts', 'invoice'); ?></label>

                <div class="mb-input-wrap">
                  <div class="mb-input-desc-left">%</div>
                  <input type="text" name="f_discount" value="<?php echo number_format(@$inv['f_discount'] <> '' ? $inv['f_discount'] : 0, 1, '.', ''); ?>" placeholder="<?php echo osc_esc_html(__('Discount', 'invoice')); ?>" />
                </div>

                <div class="mb-line-remove"><a href="#"><i class="fa fa-times"></i></a></div>
              </div>

              <div class="mb-row mb-tax" <?php if(@$inv['f_tax'] <= 0) { ?>style="display:none;"<?php } ?> data-class="tax" >
                <label for="f_tax"><?php _e('VAT', 'invoice'); ?></label>

                <div class="mb-input-wrap">
                  <div class="mb-input-desc-left">%</div>
                  <input type="text" name="f_tax" value="<?php echo number_format(@$inv['f_tax'] <> '' ? $inv['f_tax'] : 0, 1, '.', ''); ?>" placeholder="<?php echo osc_esc_html(__('VAT', 'invoice')); ?>" />
                </div>

                <div class="mb-line-remove"><a href="#"><i class="fa fa-times"></i></a></div>
              </div>

              <div class="mb-row mb-shipping" <?php if(@$inv['f_shipping'] <= 0) { ?>style="display:none;"<?php } ?> data-class="shipping" >
                <label for="f_shipping"><?php _e('Shipping', 'invoice'); ?></label>

                <div class="mb-input-wrap">
                  <div class="mb-input-desc-left"><?php echo inv_currency_symbol(@$inv['s_currency'] <> '' ? $inv['s_currency'] : inv_param('currency')); ?></div>
                  <input type="text" name="f_shipping" value="<?php echo number_format(@$inv['f_shipping'] <> '' ? $inv['f_shipping'] : 0, inv_param('decimals'), '.', ''); ?>" placeholder="<?php echo osc_esc_html(__('Shipping', 'invoice')); ?>" />
                </div>

                <div class="mb-line-remove"><a href="#"><i class="fa fa-times"></i></a></div>
              </div>

              <div class="mb-row mb-fee" <?php if(@$inv['f_fee'] <= 0) { ?>style="display:none;"<?php } ?> data-class="fee" >
                <label for="f_fee"><?php _e('Fee', 'invoice'); ?></label>

                <div class="mb-input-wrap">
                  <div class="mb-input-desc-left"><?php echo inv_currency_symbol(@$inv['s_currency'] <> '' ? $inv['s_currency'] : inv_param('currency')); ?></div>
                  <input type="text" name="f_fee" value="<?php echo number_format(@$inv['f_fee'] <> '' ? $inv['f_fee'] : 0, inv_param('decimals'), '.', ''); ?>" placeholder="<?php echo osc_esc_html(__('Fee', 'invoice')); ?>" />
                </div>

                <div class="mb-line-remove"><a href="#"><i class="fa fa-times"></i></a></div>
              </div>

              <div class="mb-row mb-activation">
                <a href="#" class="mb-show-discount" data-class="discount" <?php if(@$inv['f_discount'] > 0) { ?>style="display:none;"<?php } ?> ><i class="fa fa-plus"></i><span><?php _e('Discount', 'invoice'); ?></span></a>
                <a href="#" class="mb-show-tax" data-class="tax" <?php if(@$inv['f_tax'] > 0) { ?>style="display:none;"<?php } ?> ><i class="fa fa-plus"></i><span><?php _e('VAT', 'invoice'); ?></span></a>
                <a href="#" class="mb-show-shipping" data-class="shipping" <?php if(@$inv['f_shipping'] > 0) { ?>style="display:none;"<?php } ?> ><i class="fa fa-plus"></i><span><?php _e('Shipping', 'invoice'); ?></span></a>
                <a href="#" class="mb-show-fee" data-class="fee" <?php if(@$inv['f_fee'] > 0) { ?>style="display:none;"<?php } ?> ><i class="fa fa-plus"></i><span><?php _e('Fee', 'invoice'); ?></span></a>
              </div>

              <div class="mb-row">
                <label for="f_amount"><?php _e('Total', 'invoice'); ?></label>
                <div class="mb-price-box mb-amount">
                  <div class="mb-currency-alt"><?php echo inv_currency_symbol(@$inv['s_currency'] <> '' ? $inv['s_currency'] : inv_param('currency')); ?></div>
                  <input type="text" name="f_amount" id="f_amount" value="<?php echo number_format(@$inv['f_amount'] <> '' ? $inv['f_amount'] : 0, inv_param('decimals'), '.', ''); ?>" readonly />
                </div>
              </div>

              <div class="mb-row">
                <label for="f_paid"><?php _e('Amount Paid', 'invoice'); ?></label>

                <div class="mb-input-wrap">
                  <div class="mb-input-desc-left"><?php echo inv_currency_symbol(@$inv['s_currency'] <> '' ? $inv['s_currency'] : inv_param('currency')); ?></div>
                  <input type="text" name="f_paid" value="<?php echo number_format(@$inv['f_paid'] <> '' ? $inv['f_paid'] : 0, inv_param('decimals'), '.', ''); ?>" placeholder="<?php echo osc_esc_html(__('Amount Paid', 'invoice')); ?>" />
                </div>
              </div>

            </div>


            <div class="mb-end">
              <div class="mb-row">
                <label for="s_notes"><?php _e('Notes', 'invoice'); ?></label>
                <textarea id="s_notes" name="s_notes" placeholder="<?php echo osc_esc_js(__('Notes - any relevant information not already covered', 'invoice')); ?>"><?php echo (isset($inv['s_notes']) ? $inv['s_notes'] : inv_param('notes')); ?></textarea>
              </div>

              <div class="mb-row">
                <label for="s_terms"><?php _e('Terms', 'invoice'); ?></label>
                <textarea id="s_terms" name="s_terms" placeholder="<?php echo osc_esc_js(__('Terms and conditions - late fees, payment methods, delivery schedule', 'invoice')); ?>"><?php echo (isset($inv['s_terms']) ? $inv['s_terms'] : inv_param('terms')) ?></textarea>
              </div>
            </div>
          </div>
        </div>


        <div class="mb-right">
          <div class="mb-options">

            <?php if(@$inv['i_status'] == 9) { ?>
              <div class="mb-row">
                <em><?php _e('This invoice has been cancelled, if you want to re-open it, please click on button bellow', 'invoice'); ?></em>
                <div class="mb-row"></div>

                <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=invoice/admin/invoice.php&what=open&id=<?php echo $inv['pk_i_id']; ?>" class="mb-button mb-button-secondary mb-has-tooltip-light"><?php _e('Re-open Invoice', 'invoice'); ?></a>

                <div class="mb-row">&nbsp;</div>

              </div>
            <?php } ?>


            <div class="mb-row">
              <label for="s_currency"><?php _e('Currency', 'invoice'); ?></label>
              <select name="s_currency" id="s_currency">
                <?php $default = (@$inv['s_currency'] <> '' ? $inv['s_currency'] : inv_param('currency')); ?>

                <?php foreach($currencies as $c) { ?>
                  <option value="<?php echo $c; ?>" <?php if($c == $default) { ?>selected="selected"<?php } ?>><?php echo $c . ' (' . $symbols[$c] . ')'; ?></option>
                <?php } ?>
              </select>
            </div>

            <div class="mb-row mb-user-lookup">
              <label for="s_user_name"><span><?php _e('Osclass User', 'osclass_pay'); ?></span></label>
              <input type="hidden" id="fk_i_user_id" name="fk_i_user_id" value="<?php echo osc_esc_html(isset($user['pk_i_id']) ? $user['pk_i_id'] : ''); ?>"/>
              <input type="text" id="s_user_name" name="s_user_name" placeholder="<?php echo osc_esc_html(__('Type user name or email...', 'invoice')); ?>" value="<?php echo osc_esc_html(isset($user['s_name']) ? $user['s_name'] : ''); ?>"/>
            </div>

            <div class="mb-row">
              <label for="s_comment"><span><?php _e('Private Comment', 'osclass_pay'); ?></span></label>
              <textarea id="s_comment" name="s_comment" placeholder="<?php echo osc_esc_html(__('Private comment - put there your personal note to this invoice...', 'invoice')); ?>"><?php echo @$inv['s_comment']; ?></textarea>
            </div>



            <?php if(@$inv['i_status'] <> 9) { ?>

              <div class="mb-row">
                <?php if(inv_is_demo()) { ?>
                  <a class="mb-button mb-button-primary mb-has-tooltip mb-disabled" onclick="return false;" style="cursor:not-allowed;opacity:0.5;" title="<?php echo osc_esc_html(__('This is demo site', 'invoice')); ?>"><?php _e('Update', 'invoice');?></a>
                <?php } else { ?>
                  <button type="submit" class="mb-button mb-button-primary"><?php echo ($id > 0 ? __('Update', 'invoice') : __('Create', 'invoice')); ?></button>
                <?php } ?>
              </div>


              <div class="mb-row mb-diff"></div>

              <div class="mb-row">
                <?php if(inv_is_demo()) { ?>
                  <a class="mb-button mb-button-secondary mb-has-tooltip mb-disabled mb-button-pdf" onclick="return false;" style="cursor:not-allowed;opacity:0.5;" title="<?php echo osc_esc_html(__('This is demo site', 'invoice')); ?>"><?php _e('Generate PDF', 'invoice');?></a>
                <?php } else { ?>
                  <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=invoice/admin/invoice.php&what=pdf&id=<?php echo @$inv['pk_i_id']; ?>" class="mb-button-pdf mb-button mb-button-secondary mb-has-tooltip-light" title="<?php echo osc_esc_html(__('Make sure to save changes before generating PDF', 'invoice')); ?>"><?php _e('Generate PDF', 'invoice'); ?></a>
                <?php } ?>

                <?php if(@$inv['s_file'] <> '' && file_exists(osc_base_path() . 'oc-content/plugins/invoice/download/' . $inv['s_file'])) { ?>
                  <a href="<?php echo osc_base_url(); ?>oc-content/plugins/invoice/download/<?php echo $inv['s_file']; ?>" target="_blank" class="mb-button mb-button-terciary"><?php _e('View PDF', 'invoice'); ?></a>
                <?php } ?>
              </div>

              <div class="mb-row mb-diff"></div>

              <div class="mb-row">
                <label for="s_email"><span><?php _e('Email Invoice', 'osclass_pay'); ?></span></label>
                <input type="text" id="s_email" name="s_email" placeholder="<?php echo osc_esc_html(__('Send invoice to email', 'invoice')); ?>" value="<?php echo @$inv['s_email']; ?>"/>

                <?php if(@$inv['s_file'] <> '' && file_exists(osc_base_path() . 'oc-content/plugins/invoice/download/' . $inv['s_file'])) { ?>
                  <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=invoice/admin/invoice.php&what=email&id=<?php echo $inv['pk_i_id']; ?>" class="mb-button-email mb-button mb-button-quatro mb-has-tooltip-light" title="<?php echo osc_esc_html(__('Make sure to save changes and generate PDF before mailing it', 'invoice')); ?>"><?php _e('Send', 'invoice'); ?></a>
                <?php } else { ?>
                  <a href="#" class="mb-button mb-button-quatro mb-has-tooltip-light mb-disabled" title="<?php echo osc_esc_html(__('Please generate PDF first', 'invoice')); ?>" onclick="return false;"><?php _e('Send', 'invoice'); ?></a>
                <?php } ?>
              </div>
            <?php } ?>


            <?php if(@$inv['i_payment_id'] <> '') { ?>
              <div class="mb-row mb-diff"></div>

              <div class="mb-row mb-connection">
                <em><?php _e('This invoice was generated by payment plugin:', 'invoice'); ?></em>
                <div>
                  #<?php echo $inv['i_payment_id']; ?><br/>
                  <?php echo $inv['s_source']; ?><br/>
                  <?php echo $inv['s_cart']; ?>
                </div>
              </div>
            <?php } ?>

          </div>

        </div>

        <div class="mb-row">&nbsp;</div>

        <div class="mb-foot">
          <?php if($id > 0) { ?>
            <?php if(inv_is_demo()) { ?>
              <a href="#" class="remove mb-button mb-has-tooltip-light mb-disabled" disabled title="This is demo site, you cannot remove invoice"><?php _e('Remove', 'invoice'); ?></a>
            <?php } else { ?>
              <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=invoice/admin/invoice.php&what=remove&id=<?php echo $inv['pk_i_id']; ?>" class="remove mb-button mb-has-tooltip-light" title="<?php echo osc_esc_html(__('Remove invoice', 'invoice')); ?>" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to remove this invoice? Action cannot be undone.', 'invoice')); ?>')"><?php _e('Remove', 'invoice'); ?></a>
            <?php } ?>
          <?php } ?>

          <?php if(inv_is_demo()) { ?>
            <a class="mb-button mb-has-tooltip mb-disabled" onclick="return false;" style="cursor:not-allowed;opacity:0.5;" title="<?php echo osc_esc_html(__('This is demo site', 'invoice')); ?>"><?php _e('Update', 'invoice');?></a>
          <?php } else { ?>
            <button type="submit" class="mb-button"><?php echo ($id > 0 ? __('Update', 'invoice') : __('Create', 'invoice')); ?></button>
          <?php } ?>
        </div>
      </form>
    </div>
  </div>
</div>


<script>
  var invTitleBlock = "<?php echo osc_esc_js(__('Please save changes first - click Update button', 'invoice')); ?>";
  var user_lookup_error = "<?php echo osc_esc_js(__('Error getting data, user not found', 'invoice')); ?>";
  var user_lookup_url = "<?php echo osc_admin_base_url(true); ?>?page=ajax&action=runhook&hook=inv_get_user_ajax&id=";
  var user_lookup_base = "<?php echo osc_admin_base_url(true); ?>?page=ajax&action=userajax";
</script>


<?php echo inv_footer(); ?>	