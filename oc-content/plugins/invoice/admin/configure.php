<?php
  // Create menu
  $title = __('Configure', 'invoice');
  inv_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = mb_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check or value

  $connect = mb_param_update('connect', 'plugin_action', 'value', 'plugin-invoice');
  $currency = mb_param_update('currency', 'plugin_action', 'value', 'plugin-invoice');
  $currency_position = mb_param_update('currency_position', 'plugin_action', 'value', 'plugin-invoice');
  $decimals = mb_param_update('decimals', 'plugin_action', 'value', 'plugin-invoice');
  $tseparator = mb_param_update('tseparator', 'plugin_action', 'value', 'plugin-invoice');
  $dseparator = mb_param_update('dseparator', 'plugin_action', 'value', 'plugin-invoice');
  $space = mb_param_update('space', 'plugin_action', 'check', 'plugin-invoice');

  $from = mb_param_update('from', 'plugin_action', 'value', 'plugin-invoice');
  $tax = mb_param_update('tax', 'plugin_action', 'value', 'plugin-invoice');
  $notes = mb_param_update('notes', 'plugin_action', 'value', 'plugin-invoice');
  $terms = mb_param_update('terms', 'plugin_action', 'value', 'plugin-invoice');
  $auto_mail = mb_param_update('auto_mail', 'plugin_action', 'check', 'plugin-invoice');
  $invoice_order = mb_param_update('invoice_order', 'plugin_action', 'value', 'plugin-invoice');
  $font = mb_param_update('font', 'plugin_action', 'value', 'plugin-invoice');

  $validation = mb_param_update('validation', 'plugin_action', 'check', 'plugin-invoice');
  $fields = mb_param_update('fields', 'plugin_action', 'value', 'plugin-invoice');


  $currencies = inv_available_currencies(true);
  $symbols = inv_available_currencies();

  $connect_array = explode(',', $connect);
  $fields_array = explode(',', $fields);

  if(Params::getParam('plugin_action') == 'done') {
    $stamp_file = Params::getFiles('stamp');

    if(!empty($stamp_file) && isset($stamp_file['name'])) {
      inv_upload_stamp($stamp_file);
    }

    message_ok( __('Settings were successfully saved', 'invoice') );
  }

  
  // Remove stamp
  if(Params::getParam('what') == 'remove_stamp') {
    if(inv_is_demo()) {
      message_error(__('You cannot do this on demo site.', 'invoice'));

    } else {
      if(unlink(inv_get_stamp(true))) {
        message_ok(__('Electronical stamp image removed successfully.', 'invoice'));
      } else {
        message_error(sprintf(__('Unable to remove stamp image, please remove it manually at %s', 'invoice'), inv_get_stamp(true)));
      }
    }
  }
?>


<div class="mb-body">

  <!-- CONFIGURE SECTION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-wrench"></i> <?php _e('Configure', 'invoice'); ?></div>

    <div class="mb-inside mb-minify">
      <form name="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>configure.php" />
        <input type="hidden" name="plugin_action" value="done" />

        <div class="mb-row mb-row-select-multiple">
          <label for="connect" class="h1"><span><?php _e('Connect Invoice Plugin', 'invoice'); ?></span></label> 

          <input type="hidden" name="connect" id="connect" value="<?php echo $connect; ?>"/>
          <select id="connect_multiple" name="connect_multiple" multiple>
            <option value="osclass_pay" <?php if(in_array('osclass_pay', $connect_array)) { ?>selected="selected"<?php } ?>><?php _e('Osclass Pay', 'invoice'); ?></option>
            <option value="payments_pro" <?php if(in_array('payments_pro', $connect_array)) { ?>selected="selected"<?php } ?>><?php _e('Payments Pro', 'invoice'); ?></option>
            <option value="fortumo" <?php if(in_array('fortumo', $connect_array)) { ?>selected="selected"<?php } ?>><?php _e('SMS Payments (Fortumo)', 'invoice'); ?></option>
          </select>
          
          <div class="mb-explain"><?php _e('You may connect Invoice Plugin to payment plugin in order to generate and mail invoices automatically.', 'invoice'); ?></div>
        </div>

        <div class="mb-row">
          <label for="auto_mail" class="h7"><span><?php _e('Automatically Email Invoice', 'invoice'); ?></span></label> 
          <input name="auto_mail" type="checkbox" class="element-slide" <?php echo ($auto_mail == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('When enabled and plugin is connected to any payment plugin, invoice is automatically sent to client when generated.', 'invoice'); ?></div>
        </div>

        <div class="mb-row mb-row-select">
          <label for="currency" class="h4"><span><?php _e('Default Currency', 'invoice'); ?></span></label> 

          <select id="currency" name="currency">
            <?php foreach($currencies as $c) { ?>

              <option value="<?php echo $c; ?>" <?php if($c == inv_param('currency')) { ?>selected="selected"<?php } ?>><?php echo $c . ' (' . $symbols[$c] . ')'; ?></option>
            <?php } ?>
          </select>

          <div class="mb-explain"><?php _e('Select default currency for payments.', 'invoice'); ?></div>
        </div>

        <div class="mb-row mb-row-select">
          <label for="currency_position" class="h10"><span><?php _e('Currency Position', 'invoice'); ?></span></label> 

          <select id="currency_position" name="currency_position">
            <option value="0" <?php if($currency_position == 0) { ?>selected="selected"<?php } ?>><?php _e('After price', 'invoice'); ?></option>
            <option value="1" <?php if($currency_position == 1) { ?>selected="selected"<?php } ?>><?php _e('Before price', 'invoice'); ?></option>
          </select>

          <div class="mb-explain"><?php _e('Select position of currency in price.', 'invoice'); ?></div>
        </div>

        <div class="mb-row">
          <label for="space" class="h13"><span><?php _e('Space in Price', 'invoice'); ?></span></label> 
          <input name="space" type="checkbox" class="element-slide" <?php echo ($space == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('When enabled, white space is added between price and currency symbol.', 'invoice'); ?></div>
        </div>

        <div class="mb-row">
          <label for="dseparator" class="h11"><span><?php _e('Decimal Separator', 'invoice'); ?></span></label> 
          <input type="text" name="dseparator" id="dseparator" value="<?php echo $dseparator; ?>" style="text-align:right;" />
          
          <div class="mb-explain"><?php _e('Default is point (.)', 'invoice'); ?></div>
        </div>


        <div class="mb-row">
          <label for="decimals" class="h13"><span><?php _e('Number of Decimals', 'invoice'); ?></span></label> 
          <input type="text" name="decimals" id="decimals" value="<?php echo $decimals; ?>" style="text-align:right;" />
          <div class="mb-input-desc"><?php _e('places', 'invoice'); ?></div>
          
          <div class="mb-explain"><?php _e('Number of decimal places.', 'invoice'); ?></div>
        </div>


        <div class="mb-row">
          <label for="tseparator" class="h12"><span><?php _e('Thousands Separator', 'invoice'); ?></span></label> 
          <input type="text" name="tseparator" id="tseparator" value="<?php echo $tseparator; ?>" style="text-align:right;" />
          
          <div class="mb-explain"><?php _e('Default is none', 'invoice'); ?></div>
        </div>



        <div class="mb-row">
          <label for="from" class="h2"><span><?php _e('Default Billing Information', 'invoice'); ?></span></label> 
          <textarea name="from" id="from" class="mb-from"><?php echo $from; ?></textarea>
          
          <div class="mb-explain"><?php _e('Default header (who is this invoice from?) that may be changed at invoice.', 'invoice'); ?></div>
        </div>


        <div class="mb-row">
          <label for="tax" class="h4"><span><?php _e('Default VAT', 'invoice'); ?></span></label> 
          <input type="text" name="tax" id="tax" value="<?php echo number_format($tax, 1); ?>" style="text-align:right;" />
          <div class="mb-input-desc">%</div>
          
          <div class="mb-explain"><?php _e('Base tax that may be changed at invoice.', 'invoice'); ?></div>
        </div>

        <div class="mb-row">
          <label for="notes" class="h5"><span><?php _e('Default Notes', 'invoice'); ?></span></label> 
          <textarea name="notes" id="notes"><?php echo $notes; ?></textarea>
          
          <div class="mb-explain"><?php _e('Default invoice notes (shown on invoice at bottom).', 'invoice'); ?></div>
        </div>

        <div class="mb-row">
          <label for="terms" class="h6"><span><?php _e('Default Terms & Conditions', 'invoice'); ?></span></label> 
          <textarea name="terms" id="terms"><?php echo $terms; ?></textarea>
          
          <div class="mb-explain"><?php _e('Default terms and conditions (shown on invoice at bottom).', 'invoice'); ?></div>
        </div>

        <div class="mb-row">
          <label for="invoice_order" class="h7"><span><?php _e('Invoice Order', 'invoice'); ?></span></label> 
          <input type="text" name="invoice_order" id="invoice_order" value="<?php echo $invoice_order; ?>" />
          
          <div class="mb-explain"><?php _e('Default invoice order provided by your accountant. Enter number of order that will be used on next invoice. Will be put as invoice number. Example: Next order should have number 100, then 101, then 102, 103, ... put 100 as invoice order number. This number will be used on invoice name as well. Entry must be integer.', 'invoice'); ?></div>
        </div>

        <div class="mb-row">
          <label for="stamp" class="h8"><span><?php _e('Electronical Stamp', 'invoice'); ?></span></label> 
          <input type="file" name="stamp" id="stamp" />

          <?php if(inv_get_stamp()) { ?>
            <div class="mb-preview-stamp"><img src="<?php echo inv_get_stamp(false); ?>"/></div>
            <a class="mb-remove-stamp" href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=invoice/admin/configure.php&what=remove_stamp"><?php _e('Remove stamp', 'invoice'); ?></a>
          <?php } ?>          

          <div class="mb-explain"><?php _e('Upload your e-stamp (e-signature) that will be added to bottom right of invoice. This is used to sign invoice electronically. Supported image formats are png, jpg, jpeg. We recommend to use png image with transparent background of size about 240x60 px.', 'invoice'); ?></div>
        </div>

        <div class="mb-row">
          <label for="font" class="h9"><span><?php _e('PDF Font', 'invoice'); ?></span></label> 

          <select id="font" name="font">
            <option value="freesans" <?php if($font == 'freesans') { ?>selected="selected"<?php } ?>><?php _e('FreeSans', 'invoice'); ?></option>
            <option value="helvetica" <?php if($font == 'helvetica') { ?>selected="selected"<?php } ?>><?php _e('Helvetica', 'invoice'); ?></option>
            <option value="courier" <?php if($font == 'courier') { ?>selected="selected"<?php } ?>><?php _e('Courier', 'invoice'); ?></option>
          </select>
          
          <div class="mb-explain"><?php _e('Font used to generate PDF. By default FreeSans is used that has most character support.', 'invoice'); ?></div>
        </div>

        <div class="mb-row">
          <label for="validation" class=""><span><?php _e('Business Profile Validation', 'invoice'); ?></span></label> 
          <input name="validation" type="checkbox" class="element-slide" <?php echo ($validation == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('When enabled, business profile of user will have to be validated by admin.', 'invoice'); ?></div>
        </div>
        
        <div class="mb-row mb-row-select-multiple">
          <label for="fields" class=""><span><?php _e('Available Fields on Profile', 'invoice'); ?></span></label> 

          <input type="hidden" name="fields" id="fields" value="<?php echo $fields; ?>"/>
          <select id="fields_multiple" name="fields_multiple" multiple>
            <option value="vat_number" <?php if(in_array('vat_number', $fields_array)) { ?>selected="selected"<?php } ?>><?php _e('International VAT Number', 'invoice'); ?></option>
            <option value="vat_number_local" <?php if(in_array('vat_number_local', $fields_array)) { ?>selected="selected"<?php } ?>><?php _e('Local VAT Number', 'invoice'); ?></option>
            <option value="header" <?php if(in_array('header', $fields_array)) { ?>selected="selected"<?php } ?>><?php _e('Invoice header', 'invoice'); ?></option>
            <option value="ship" <?php if(in_array('ship', $fields_array)) { ?>selected="selected"<?php } ?>><?php _e('Shipping address', 'invoice'); ?></option>
          </select>
          
          <div class="mb-explain">
            <div><?php _e('Select fields those will be available in user billing profile. If no field is selected, all fields will be included in profile.', 'invoice'); ?></div>
            <div><?php _e('Use control key to select more than 1 value.', 'invoice'); ?></div>
          </div>
        </div>
        
        
        <div class="mb-row">&nbsp;</div>

        <div class="mb-foot">
          <?php if(inv_is_demo()) { ?>
            <a class="mb-button mb-has-tooltip disabled" onclick="return false;" style="cursor:not-allowed;opacity:0.5;" title="<?php echo osc_esc_html(__('This is demo site', 'invoice')); ?>"><?php _e('Save', 'invoice');?></a>
          <?php } else { ?>
            <button type="submit" class="mb-button"><?php _e('Save', 'invoice');?></button>
          <?php } ?>
        </div>
      </form>
    </div>
  </div>


  <!-- PLUGIN INTEGRATION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-wrench"></i> <?php _e('Plugin Setup', 'invoice'); ?></div>

    <div class="mb-inside">

      <div class="mb-row">
        <div class="mb-line"><?php _e('Plugin does not require any modifications in theme files.', 'invoice'); ?></div>

      </div>
    </div>
  </div>
</div>


<?php echo inv_footer(); ?>