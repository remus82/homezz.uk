<?php
  // Create menu
  $title = __('Reports', 'invoice');
  inv_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = mb_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check or value


  // GENERATE REPORT
  if(Params::getParam('plugin_action') == 'done') { 
    $date_type = Params::getParam('date_type');
    $status = Params::getParam('status');
    $start_date = Params::getParam('start_date');
    $end_date = Params::getParam('end_date');

    if($status == 0) { 
      $status_name = __('In preparation', 'invoice');
    } else if($status == 1) { 
      $status_name = __('PDF generated', 'invoice');
    } else if($status == 2) { 
      $status_name = __('PDF sent to client', 'invoice');
    } else if($status == 9) { 
      $status_name = __('Cancelled', 'invoice');
    } else {
      $status_name = __('All', 'invoice');
    }

    $invoices = ModelINV::newInstance()->getInvoicesForReport($start_date, $end_date, $date_type, $status);
    $html = '';
    $data = array();
    
    if(count($invoices) > 0) {
      $total_gross = 0;
      $total_tax = 0;
      $total_net = 0;

      $data[] = "From:," .  $start_date;
      $data[] = "To:," .  $end_date;
      $data[] = "Status:," .  $status_name;
      $data[] = ",";

      $data[] = "ID,Date,Customer,Currency,Net,VAT,Gross";

      foreach($invoices as $i) {
        $gross = $i['f_amount'];
        $net = round($gross/(1 + $i['f_tax']/100), 2);
        $tax = $gross - $net;

        $total_gross = $total_gross + $gross;
        $total_net = $total_net + $net;
        $total_tax = $total_tax + $tax;

        $to = str_replace(',', ';', $i['s_to']);
        $to = str_replace("\r", "", $to);
        $to = str_replace("\n", "", $to);

        $id = str_replace(',', ';', $i['s_identifier']);

        $data[] = $id . "," . $i['dt_date'] . "," . $to . "," . $i['s_currency'] . "," . $net . "," . $tax . "," . $gross;
      }

      $data[] = ",,,," . $total_net . "," . $total_tax . "," . $total_gross;


      $file_name = osc_base_path() . 'oc-content/plugins/invoice/report/' . date('YmdHis') . '_' . mb_generate_rand_string(6) . '.csv';
      $file = fopen($file_name, "w+");

      foreach ($data as $line) {
        fputcsv($file,explode(',', $line));
      }

      fclose($file);

      message_ok(sprintf(__('Report created based on %s invoices', 'invoice'), count($invoices)));

    } else {
      message_error(__('No invoices match your criteria', 'invoice'));

    }


  }


  // DELETE
  if(Params::getParam('what') == 'remove' && Params::getParam('name') <> '' && !inv_is_demo()) { 
    $path = osc_base_path() . 'oc-content/plugins/invoice/report/' . Params::getParam('name');

    if(file_exists($path) && !is_dir($path)) {
      @unlink($path);
    }    

    message_ok(__('Report removed', 'invoice'));
  }

?>


<div class="mb-body">

  <!-- NEW REPORT SECTION -->
  <div class="mb-box mb-bp">
    <div class="mb-head"><i class="fa fa-file-excel-o"></i> <?php _e('Create new report', 'invoice'); ?></div>

    <div class="mb-inside">
      <?php if(!is_writable(osc_base_path() . 'oc-content/plugins/invoice/report/')) { ?>
        <div class="mb-row mb-errors">
          <div class="mb-line"><?php _e('Folder oc-content/plugins/invoice/report/ is not writtable, report cannot be generated and saved!', 'invoice'); ?></div>
        </div>
      <?php } ?>
      
      <div class="mb-row mb-notes">
        <div class="mb-line"><?php _e('Generate CSV with invoices that match criteria.', 'invoice'); ?></div>
      </div>

      <form name="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>report.php" />
        <input type="hidden" name="plugin_action" value="done" />

        <div class="mb-row">
          <label for="start_date"><span><?php _e('Date Range', 'invoice'); ?></span></label> 
          <input name="start_date" type="text" id="start_date" placeholder="<?php echo osc_esc_html(__('Start date', 'invoice')); ?>" required/>

          <span class="dt-del"><i class="fa fa-arrows-h"></i></span>

          <input name="end_date" type="text" id="end_date" placeholder="<?php echo osc_esc_html(__('End date', 'invoice')); ?>" required/>

          <div class="mb-explain"><?php _e('Enter date range when invoices were created', 'invoice'); ?></div>
        </div>


        <div class="mb-row">
          <label for="date_type"><span><?php _e('Invoice date to filter on', 'invoice'); ?></span></label> 
          <select name="date_type">
            <option value="DATE"><?php _e('Date', 'invoice'); ?></option>
            <option value="DUE_DATE"><?php _e('Due Date', 'invoice'); ?></option>
          </select>
        </div>


        <div class="mb-row">
          <label for="status"><span><?php _e('Status', 'invoice'); ?></span></label> 
          <select name="status">
            <option value="-1" selected="selected"><?php _e('All statues', 'invoice'); ?></option>
            <option value="0"><?php _e('In preparation', 'invoice'); ?></option>
            <option value="1"><?php _e('PDF generated', 'invoice'); ?></option>
            <option value="2"><?php _e('PDF sent to client', 'invoice'); ?></option>
            <option value="9"><?php _e('Cancelled', 'invoice'); ?></option>
          </select>
        </div>


        <div class="mb-row">&nbsp;</div>

        <div class="mb-foot">
          <button type="submit" class="mb-button"><?php _e('Create', 'invoice');?></button>
        </div>
      </form>
    </div>
  </div>



  <!-- LIST SECTION -->
  <div class="mb-box mb-bp">
    <div class="mb-head"><i class="fa fa-list"></i> <?php _e('Reports List', 'invoice'); ?></div>

    <div class="mb-inside">
      <div class="mb-table mb-table-invoice">
        <div class="mb-table-head">
          <div class="mb-col-2 mb-align-left"><?php _e('ID', 'invoice');?></div>
          <div class="mb-col-8 mb-align-left"><?php _e('Report name', 'invoice');?></div>
          <div class="mb-col-6"><?php _e('Generated date', 'invoice'); ?></div>
          <div class="mb-col-4">&nbsp;</div>
          <div class="mb-col-2 mb-align-right">&nbsp;</div>
        </div>

        <?php $reports = ModelINV::newInstance()->getReports(); ?>

        <?php if(count($reports) <= 0) { ?>
          <div class="mb-table-row mb-row-empty">
            <i class="fa fa-warning"></i><span><?php _e('No reports created yet', 'invoice'); ?></span>
          </div>
        <?php } else { ?>
          <?php $i = 1; ?>

          <?php foreach($reports as $r) { ?>
            <div class="mb-table-row">
              <div class="mb-col-2 mb-col-id mb-align-left"><?php echo $i; ?></div>
              <div class="mb-col-8 mb-align-left"><?php echo $r['name']; ?></div>
              <div class="mb-col-6 mb-col-date"><?php echo date('j. M Y H:i:s', strtotime($r['date'])); ?></div>
              <div class="mb-col-4"><a href="<?php echo $r['url']; ?>"><?php _e('Download', 'invoice'); ?></a></div>
   
              <div class="mb-col-4 mb-col-del mb-align-right">
                <?php if(inv_is_demo()) { ?>
                  <a href="#" class="mb-inv-remove mb-btn mb-button-red mb-has-tooltip-light mb-disabled" disabled title="This is demo site, you cannot remove invoice"><i class="fa fa-trash"></i> <?php _e('Remove', 'invoice'); ?></a>
                <?php } else { ?>
                  <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=invoice/admin/report.php&what=remove&name=<?php echo $r['name'] . '.' . $r['extension']; ?>" class="mb-inv-remove mb-btn mb-button-red mb-has-tooltip-light" title="<?php echo osc_esc_html(__('Remove report', 'invoice')); ?>" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to remove this report? Action cannot be undone.', 'invoice')); ?>')"><i class="fa fa-trash"></i> <?php _e('Remove', 'invoice'); ?></a>
                <?php } ?>
              </div>
            </div>

            <?php $i++; ?>
          <?php } ?>
        <?php } ?>

        <div class="mb-row">&nbsp;</div>

      </form>
    </div>
  </div>


</div>


<?php echo inv_footer(); ?>