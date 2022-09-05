<?php
  $type = (Params::getParam('pageType') <> '' ? Params::getParam('pageType') : 'invoices');

  $invoices = ModelINV::newInstance()->getInvoicesByUserId(osc_logged_user_id());
  
  if(Params::getParam('action') == 'invupdate') {
    $data = array(
      'fk_i_user_id' => osc_logged_user_id(),
      's_header' => osc_esc_html(Params::getParam('s_header')),
      's_vat_number' => osc_esc_html(Params::getParam('s_vat_number')),
      's_vat_number_local' => osc_esc_html(Params::getParam('s_vat_number_local')),
      's_ship_to' => osc_esc_html(Params::getParam('s_ship_to')),
      'i_vat_number_verified' => 0
    );
    
    ModelInv::newInstance()->updateInvoiceUserData($data);
    osc_add_flash_ok_message(__('Your billing profile has been successfully updated', 'invoice'));
    header('Location: ' . osc_route_url('inv-profile-alt', array('pageType' => 'profile')));
    exit;
  }
  
  $user_data = ModelInv::newInstance()->getInvoicesUserData(osc_logged_user_id());
  $status = inv_user_profile_status($user_data);
  $fields = trim(inv_param('fields'));
  $fields_array = explode(',', $fields);
?>

<div id="inv-prof" class="inv-body inv-prof">
  <div class="inv-nav">
    <a href="#invoices" data-tab="invoices" class="<?php if($type == 'invoices') { ?>active<?php } ?>"><?php _e('My invoices', 'invoice'); ?></a>
    <a href="#profile" data-tab="profile" class="<?php if($type == 'profile') { ?>active<?php } ?>"><?php _e('Billing profile', 'invoice'); ?></a>
  </div>
  
  <div class="inv-tabs">
    <div class="inv-tab" data-tab="invoices" <?php if($type != 'invoices') { ?>style="display:none;"<?php } ?>>    

      <div class="inv-table-invoices">
        <?php if(function_exists('osp_param') && osp_param('selling_allow') == 1) { ?>
          <div class="inv-notification">
            <?php echo sprintf(__('Note that here you will find only invoices issued by %s.', 'invoice'), osc_get_domain()); ?><br/>
            <?php _e('If you ordered products from external sellers, please check your mailbox or contact seller directly to get invoice for your order.', 'invoice'); ?>
          </div>
        <?php } ?>
      
        <div class="inv-table-wrap">
          <div class="inv-head-row">
            <div class="inv-col id"><?php _e('ID', 'invoice'); ?></div>
            <div class="inv-col to"><?php _e('Header', 'invoice'); ?></div>
            <div class="inv-col date"><?php _e('Date', 'invoice'); ?></div>
            <div class="inv-col duedate"><?php _e('Due date', 'invoice'); ?></div>
            <div class="inv-col amount"><?php _e('Amount', 'invoice'); ?></div>
            <div class="inv-col balance"><?php _e('Balance', 'invoice'); ?></div>
            <div class="inv-col download">&nbsp;</div>
          </div>

          <?php if(!is_array($invoices) || count($invoices) <= 0) { ?>
            <div class="inv-row inv-row-empty">
              <i class="fa fa-warning"></i><span><?php _e('No invoices found', 'invoice'); ?></span>
            </div>
          <?php } else { ?>
            <?php foreach($invoices as $inv) { ?>
              <?php
                $file = trim($inv['s_file']);
                $path = osc_base_path() . 'oc-content/plugins/invoice/download/';
                $url = osc_base_url() . 'oc-content/plugins/invoice/download/';

                if($file <> '' && file_exists($path . $file)) {
                  $link = $url . $file;
                  $title = __('Click to download invoice in PDF format', 'invoice');
                } else {
                  $link = '#';
                  $title = __('Invoice is not generated yet, please check in few days or contact us', 'invoice');
                }

                if($inv['i_status'] == 9) {
                  $link = '#';
                  $title = __('This invoice has been cancelled, contact us for more details', 'invoice');
                }
              ?>

              <div class="inv-row">
                <div class="inv-col id"><?php echo $inv['s_identifier']; ?></div>
                <div class="inv-col to"><?php echo ($inv['s_to'] <> '' ? osc_highlight($inv['s_to'], 85) : osc_logged_user_name()); ?></div>
                <div class="inv-col date"><?php echo ($inv['dt_date'] <> '' ? date('j. M Y', strtotime($inv['dt_date'])) : date('j. M Y')); ?></div>
                <div class="inv-col duedate"><?php echo ($inv['dt_due_date'] <> '' ? date('j. M Y', strtotime($inv['dt_due_date'])) : date('j. M Y')); ?></div>
                <div class="inv-col amount"><?php echo inv_currency_symbol($inv['s_currency'] <> '' ? $inv['s_currency'] : inv_param('currency')); ?><?php echo number_format($inv['f_amount'] <> '' ? $inv['f_amount'] : 0, 2); ?></div>
                <div class="inv-col balance"><?php echo inv_currency_symbol($inv['s_currency'] <> '' ? $inv['s_currency'] : inv_param('currency')); ?><?php echo number_format($inv['f_balance'] <> '' ? $inv['f_balance'] : 0, 2); ?></div>
                <div class="inv-col download">
                  <a class="inv-download-button inv-has-tooltip <?php if($link == '#') { ?>inv-disabled<?php } ?>" target="_blank" <?php if($link == '#') { ?>onclick="return false;"<?php } ?> title="<?php echo osc_esc_html($title); ?>" href="<?php echo $link; ?>">
                    <svg aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512"><path fill="currentColor" d="M224 136V0H24C10.7 0 0 10.7 0 24v464c0 13.3 10.7 24 24 24h336c13.3 0 24-10.7 24-24V160H248c-13.2 0-24-10.8-24-24zm76.45 211.36l-96.42 95.7c-6.65 6.61-17.39 6.61-24.04 0l-96.42-95.7C73.42 337.29 80.54 320 94.82 320H160v-80c0-8.84 7.16-16 16-16h32c8.84 0 16 7.16 16 16v80h65.18c14.28 0 21.4 17.29 11.27 27.36zM377 105L279.1 7c-4.5-4.5-10.6-7-17-7H256v128h128v-6.1c0-6.3-2.5-12.4-7-16.9z" class=""></path></svg>
                    <?php _e('Download', 'invoice'); ?>
                  </a>
                </div>
              </div>
            <?php } ?>
          <?php } ?>
        </div>  
      </div>  

    </div>
    
    <div class="inv-tab" data-tab="profile" <?php if($type != 'profile') { ?>style="display:none;"<?php } ?>>    
    
      <div class="inv-form">
        <form class="nocsrf" method="POST" name="inv-form" action="<?php echo osc_route_url('inv-profile'); ?>" enctype="multipart/form-data">
          <input type="hidden" name="action" value="invupdate" />

          <?php if(inv_param('validation') == 1) { ?>
            <div class="inv-row inv-status <?php echo $status['status']; ?>"><?php echo $status['message']; ?></div>
          <?php } ?>
          
          <?php if(in_array('vat_number', $fields_array) || $fields == '') { ?>
            <div class="inv-row">
              <label for="s_vat_number"><?php _e('International VAT Number', 'invoice'); ?>:</label>
              <input type="text" id="s_vat_number" name="s_vat_number" value="<?php echo ($user_data !== false ? trim(osc_esc_html(@$user_data['s_vat_number'])) : ''); ?>" />
              <div class="inv-help"><?php _e('International VAT number of your company', 'invoice'); ?></div>
            </div>
          <?php } ?>
          
          <?php if(in_array('vat_number_local', $fields_array) || $fields == '') { ?>
            <div class="inv-row">
              <label for="s_vat_number_local"><?php _e('Local VAT Number', 'invoice'); ?>:</label>
              <input type="text" id="s_vat_number_local" name="s_vat_number_local" value="<?php echo ($user_data !== false ? trim(osc_esc_html(@$user_data['s_vat_number_local'])) : ''); ?>" />
              <div class="inv-help"><?php _e('Local in-country VAT number of your company', 'invoice'); ?></div>
            </div>
          <?php } ?>
          
          <?php if(in_array('header', $fields_array) || $fields == '') { ?>
            <div class="inv-row">
              <label for="s_header"><?php _e('Invoice header', 'invoice'); ?>:</label>
              <textarea id="s_header" name="s_header" placeholder="<?php echo osc_esc_html(__('Company name', 'invoice')); ?>&#10;<?php echo osc_esc_html(__('Street / Address', 'invoice')); ?>&#10;<?php echo osc_esc_html(__('ZIP', 'invoice')); ?>, <?php echo osc_esc_html(__('City', 'invoice')); ?>&#10;<?php echo osc_esc_html(__('Local VAT Number', 'invoice')); ?>&#10;<?php echo osc_esc_html(__('International VAT Number', 'invoice')); ?>"><?php echo ($user_data !== false ? trim(osc_esc_html(@$user_data['s_header'])) : ''); ?></textarea>
              <div class="inv-help"><?php _e('Will be used on invoice header instead of data from your user profile. Enter VAT number as well, even entered in above input(s). Keep blank to use default header.', 'invoice'); ?></div>
            </div>
          <?php } ?>
          
          <?php if(in_array('ship', $fields_array) || $fields == '') { ?>
            <div class="inv-row">
              <label for="s_ship_to"><?php _e('Shipping address', 'invoice'); ?>:</label>
              <textarea id="s_ship_to" name="s_ship_to"><?php echo ($user_data !== false ? trim(osc_esc_html(@$user_data['s_ship_to'])) : ''); ?></textarea>
              <div class="inv-help"><?php _e('Will be added to invoice as your standard shipping address. Keep blank to not add extra shipping address to invoice.', 'invoice'); ?></div>
            </div>
          <?php } ?>
          
          <div class="inv-row">
            <button type="submit" class="inv-button"><?php _e('Save', 'invoice'); ?></button>
          </div>
        </form>
      </div>

    </div>
  </div>
  
</div>

<script>
  $(document).ready(function() {
    Tipped.create('.inv-has-tooltip', { maxWidth: 200, radius: false, behavior: 'hide'});
    
    // IDENTIFY TAB BASED ON HASH
    var hash = window.location.hash;

    if(hash == '#profile') {
      $('.inv-nav a').removeClass('active');
      $('.inv-nav a[data-tab="profile"]').addClass('active');
      $('.inv-tab').hide(0);
      $('.inv-tab[data-tab="profile"]').show(0);
    }

    // USER ACCOUNT TABS
    $('body').on('click', '.inv-nav a', function(e) {
      //e.preventDefault();
      
      var id = $(this).attr('data-tab');
      
      $('.inv-nav a').removeClass('active');
      $(this).addClass('active');
    
      $('.inv-tab').hide(0);
      $('.inv-tab[data-tab="' + id + '"]').show(0);
    });
  });
</script>