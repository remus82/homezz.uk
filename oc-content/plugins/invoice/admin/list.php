<?php
  // Create menu
  $title = __('Invoices List', 'invoice');
  inv_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = mb_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check or value


  // DELETE
  if(Params::getParam('what') == 'remove' && Params::getParam('id') > 0 && !inv_is_demo()) { 
    ModelINV::newInstance()->removeInvoice(Params::getParam('id'));

    osc_add_flash_ok_message(__('Invoice has been successfully removed', 'invoice'), 'admin');
    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=invoice/admin/list.php');
    exit;
  }

  // CANCEL
  if(Params::getParam('what') == 'cancel' && Params::getParam('id') > 0 && !inv_is_demo()) { 
    $inv = ModelINV::newInstance()->getInvoice(Params::getParam('id'));

    if(@$inv['s_file'] <> '') {
      $pdf = osc_base_path() . 'oc-content/plugins/invoice/download/' . $inv['s_file'];

      if(file_exists($pdf) && is_file($pdf)) {
        @unlink($pdf);
      }
    }

    ModelINV::newInstance()->updateStatus(Params::getParam('id'), 9);
    
    osc_add_flash_ok_message(__('Invoice has been successfully cancelled. Invoice PDF was removed', 'invoice'), 'admin');
    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=invoice/admin/list.php');
    exit;
  }
  
  $params = Params::getParamsAsArray();
  $invoices = ModelINV::newInstance()->getInvoices($params);
  $count_all = ModelINV::newInstance()->getInvoices($params, true);

?>


<div class="mb-body">

  <!-- LIST SECTION -->
  <div class="mb-box mb-bp">
    <div class="mb-head"><i class="fa fa-wrench"></i> <?php _e('Invoices List', 'invoice'); ?></div>

    <div class="mb-inside">
      
      <form name="promo_form" action="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=invoice/admin/list.php" method="POST" enctype="multipart/form-data" >
        <div id="mb-search-table">
          <div class="mb-col-3">
            <label for="invoiceId"><?php _e('Invoice ID', 'invoice'); ?></label>
            <input type="text" name="invoiceId" value="<?php echo Params::getParam('invoiceId'); ?>" />
          </div>
          
          <div class="mb-col-3">
            <label for="billTo"><?php _e('Bill to', 'invoice'); ?></label>
            <input type="text" name="billTo" value="<?php echo Params::getParam('billTo'); ?>" />
          </div>
          
          <div class="mb-col-3">
            <label for="date"><?php _e('Inv. Date', 'invoice'); ?></label>
            <input type="text" name="date" value="<?php echo Params::getParam('date'); ?>" placeholder="yyyy-mm-dd"/>
          </div>
          
          <div class="mb-col-3">
            <label for="uemail"><?php _e('Cust. Email', 'invoice'); ?></label>
            <input type="text" name="uemail" value="<?php echo Params::getParam('uemail'); ?>" />
          </div>
          
          <div class="mb-col-3">
            <label for="balancemin"><?php _e('Min. Balance', 'invoice'); ?></label>
            <input type="text" name="balancemin" value="<?php echo Params::getParam('balancemin'); ?>" />
          </div>
          
          <div class="mb-col-3">
            <label for="balancemax"><?php _e('Max. Balance', 'invoice'); ?></label>
            <input type="text" name="balancemax" value="<?php echo Params::getParam('balancemax'); ?>" />
          </div>
          
          <div class="mb-col-3">
            <label for="status"><?php _e('Status', 'invoice'); ?></label>
            <select name="status">
              <option value="" <?php if(Params::getParam('status') == "") { ?>selected="selected"<?php } ?>><?php _e('All', 'invoice'); ?></option>
              <option value="5" <?php if(Params::getParam('status') == "5") { ?>selected="selected"<?php } ?>><?php _e('In preparation', 'invoice'); ?></option>
              <option value="1" <?php if(Params::getParam('status') == "1") { ?>selected="selected"<?php } ?>><?php _e('PDF generated', 'invoice'); ?></option>
              <option value="2" <?php if(Params::getParam('status') == "2") { ?>selected="selected"<?php } ?>><?php _e('PDF sent to client', 'invoice'); ?></option>
              <option value="9" <?php if(Params::getParam('status') == "9") { ?>selected="selected"<?php } ?>><?php _e('Cancelled', 'invoice'); ?></option>
            </select>
          </div>
          
          <div class="mb-col-3">
            <label for="">&nbsp;</label>
            <button type="submit" class="mb-button mb-button-black"><i class="fa fa-search"></i> <?php _e('Search', 'invoice'); ?></button>
          </div>
        </div>
      </form>
      
      <div class="mb-table mb-table-invoice">
        <div class="mb-table-head">
          <div class="mb-col-3 mb-align-left"><?php _e('Invoice ID', 'invoice');?></div>
          <div class="mb-col-8 mb-align-left"><?php _e('Who is this invoice to', 'invoice');?></div>
          <div class="mb-col-3 mb-align-left"><?php _e('Status', 'invoice'); ?></div>
          <div class="mb-col-2 mb-align-right"><?php _e('Total', 'invoice'); ?></div>
          <div class="mb-col-2 mb-align-right"><?php _e('Balance', 'invoice'); ?></div>
          <div class="mb-col-6 mb-align-right">&nbsp;</div>
        </div>

        <?php if(count($invoices) <= 0) { ?>
          <div class="mb-table-row mb-row-empty">
            <i class="fa fa-warning"></i><span><?php _e('No invoices has been found', 'invoice'); ?></span>
          </div>
        <?php } else { ?>
          <?php foreach($invoices as $i) { ?>
            <div class="mb-table-row">
              <div class="mb-col-3 mb-col-id mb-align-left"><a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=invoice/admin/invoice.php&id=<?php echo $i['pk_i_id']; ?>"><?php echo ($i['s_identifier'] <> '' ? $i['s_identifier'] : $i['pk_i_id']); ?></a></div>
              <div class="mb-col-8 mb-align-left"><?php echo implode(', ', preg_split('/\r+/', $i['s_to'])); ?></div>
              <div class="mb-col-3 mb-align-left mb-status">
                <?php 
                  if($i['i_status'] == 0) { 
                    echo '<i class="fa fa-hourglass-half"></i>';
                    _e('In preparation', 'invoice');
                  } else if($i['i_status'] == 1) { 
                    echo '<i class="fa fa-file-pdf-o"></i>';
                    _e('PDF generated', 'invoice');
                  } else if($i['i_status'] == 2) { 
                    echo '<i class="fa fa-mail-forward"></i>';
                    _e('PDF sent to client', 'invoice');
                  } else if($i['i_status'] == 9) { 
                    echo '<i class="fa fa-ban"></i>';
                    _e('Cancelled', 'invoice');
                  }
                ?>
              </div>
              <div class="mb-col-2 mb-align-right"><?php echo inv_format_price($i['f_amount'], $i['s_currency']); ?></div>
              <div class="mb-col-2 mb-align-right"><?php echo inv_format_price($i['f_balance'], $i['s_currency']); ?></div>
 
              <div class="mb-col-6 mb-col-del mb-align-right">
                <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=invoice/admin/invoice.php&id=<?php echo $i['pk_i_id']; ?>" class="mb-inv-edit mb-button-blue mb-btn"><i class="fa fa-pencil"></i> <?php _e('Edit', 'invoice'); ?></a>

                <?php if(inv_is_demo()) { ?>
                  <a href="#" class="mb-inv-remove mb-button-red mb-has-tooltip-light mb-btn mb-disabled" disabled title="This is demo site, you cannot remove invoice"><i class="fa fa-trash"></i> <?php _e('Remove', 'invoice'); ?></a>
                <?php } else { ?>
                  <?php if($i['i_status'] <> 9) { ?>
                    <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=invoice/admin/list.php&what=cancel&id=<?php echo $i['pk_i_id']; ?>" class="mb-inv-cancel mb-btn mb-button-gray mb-has-tooltip-light" title="<?php echo osc_esc_html(__('Cancel invoice', 'invoice')); ?>"><i class="fa fa-ban"></i> <?php _e('Cancel', 'invoice'); ?></a>
                  <?php } ?>

                  <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=invoice/admin/list.php&what=remove&id=<?php echo $i['pk_i_id']; ?>" class="mb-inv-remove mb-btn mb-button-red mb-has-tooltip-light" title="<?php echo osc_esc_html(__('Remove invoice', 'invoice')); ?>" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to remove this invoice? Action cannot be undone.', 'invoice')); ?>')"><i class="fa fa-trash"></i></a>
                <?php } ?>
              </div>
            </div>
          <?php } ?>
          
          <?php 
            $param_string = '&invoiceId=' . Params::getParam('invoiceId') . '&billTo=' . Params::getParam('billTo') . '&uemail=' . Params::getParam('uemail') . '&date=' . Params::getParam('date') . '&balancemin=' . Params::getParam('balancemin') . '&balancemax=' . Params::getParam('balancemax') . '&status=' . Params::getParam('status');
            echo inv_admin_paginate('invoice/admin/list.php', Params::getParam('pageId'), 25, $count_all, '', $param_string); 
          ?>
        <?php } ?>
      </div>
    </div>
  </div>

</div>


<?php echo inv_footer(); ?>