<?php
  // Create menu
  $title = __('Reports', 'invoice');
  inv_menu($title);
  
  $id = Params::getParam('id');
  if($id <= 0) {
    $id = Params::getParam('fk_i_user_id');
  }

  // UPDATE PROFILE
  if(Params::getParam('plugin_action') == 'done') { 
    $data = array(
      'fk_i_user_id' => $id,
      's_header' => Params::getParam('s_header'),
      's_vat_number' => Params::getParam('s_vat_number'),
      's_vat_number_local' => Params::getParam('s_vat_number_local'),
      's_ship_to' => Params::getParam('s_ship_to'),
      'i_vat_number_verified' => Params::getParam('i_vat_number_verified')
    );
    
    ModelInv::newInstance()->updateInvoiceUserData($data);
    osc_add_flash_ok_message(__('Billing profile has been successfully updated', 'invoice'), 'admin');
    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=invoice/admin/profile_edit.php&id=' . $id);
    exit;
  }
  
  $user_data = ModelINV::newInstance()->getInvoicesUserData($id);
  $user = User::newInstance()->findByPrimaryKey($user_data['fk_i_user_id']);
?>


<div class="mb-body">

  <!-- USER PROFILE SECTION -->
  <div class="mb-box mb-bp">
    <div class="mb-head"><i class="fa fa-edit"></i> <?php _e('Edit Business Profile', 'invoice'); ?></div>

    <form name="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
      <input type="hidden" name="page" value="plugins" />
      <input type="hidden" name="action" value="renderplugin" />
      <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>profile_edit.php" />
      <input type="hidden" name="plugin_action" value="done" />

      <div class="mb-inside">
        <div class="mb-row">
          <label for="fk_i_user_id"><?php _e('ID', 'invoice'); ?></label>
          <div class="mb-input-box">
            <input type="text" readonly size="4" name="fk_i_user_id" value="<?php echo $user_data['fk_i_user_id']; ?>"/>
          </div>
        </div>
        
        <div class="mb-row">
          <label for="fk_i_user_id"><?php _e('Name', 'invoice'); ?></label>
          <div class="mb-input-box">
            <input type="text" readonly size="50" value="<?php echo @$user['s_name']; ?>"/>
          </div>
        </div>
        
        <div class="mb-row">
          <label for="fk_i_user_id"><?php _e('Email', 'invoice'); ?></label>
          <div class="mb-input-box">
            <input type="text" readonly size="50" value="<?php echo @$user['s_email']; ?>"/>
          </div>
        </div>
        
        <div class="mb-row">
          <label for="s_vat_number"><?php _e('International VAT Number', 'invoice'); ?></label>
          <div class="mb-input-box">
            <input type="text" name="s_vat_number" size="30" value="<?php echo $user_data['s_vat_number']; ?>"/>
          </div>
        </div>
        
        <div class="mb-row">
          <label for="s_vat_number_local"><?php _e('Local VAT Number', 'invoice'); ?></label>
          <div class="mb-input-box">
            <input type="text" name="s_vat_number_local" size="30" value="<?php echo $user_data['s_vat_number_local']; ?>"/>
          </div>
        </div>
        
        <div class="mb-row">
          <label for="s_header"><?php _e('Invoice Header', 'invoice'); ?></label>
          <div class="mb-input-box">
            <textarea name="s_header" style="min-width:240px;width:320px;"><?php echo $user_data['s_header']; ?></textarea>
          </div>
        </div>
        
        <div class="mb-row">
          <label for="s_ship_to"><?php _e('Shipping Address', 'invoice'); ?></label>
          <div class="mb-input-box">
            <textarea name="s_ship_to" style="min-width:240px;width:320px;"><?php echo $user_data['s_ship_to']; ?></textarea>
          </div>
        </div>
        
        <div class="mb-row">
          <label for="i_vat_number_verified"><?php _e('Status', 'invoice'); ?></label>
          <div class="mb-input-box">
            <select name="i_vat_number_verified">
              <option value="0" <?php if($user_data['i_vat_number_verified'] == 0) { ?>selected="selected"<?php } ?>><?php _e('Pending', 'invoice'); ?></option>
              <option value="1" <?php if($user_data['i_vat_number_verified'] == 1) { ?>selected="selected"<?php } ?>><?php _e('Approved', 'invoice'); ?></option>
              <option value="9" <?php if($user_data['i_vat_number_verified'] == 9) { ?>selected="selected"<?php } ?>><?php _e('Rejected', 'invoice'); ?></option>
            </select>
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


</div>


<?php echo inv_footer(); ?>