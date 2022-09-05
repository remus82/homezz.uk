<?php
  // Create menu
  $title = __('Reports', 'invoice');
  inv_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = mb_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check or value


  // REMOVE PROFILE
  if(Params::getParam('what') == 'remove' && Params::getParam('id') > 0) { 
    ModelINV::newInstance()->removeProfile(Params::getParam('id'));
    osc_add_flash_ok_message(__('User profile has been successfully removed', 'invoice'), 'admin');
    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=invoice/admin/profiles.php');
    exit;
  }
  
  // APPROVE PROFILE
  if(Params::getParam('what') == 'approve' && Params::getParam('id') > 0) { 
    ModelINV::newInstance()->approveProfile(Params::getParam('id'));
    osc_add_flash_ok_message(__('User profile has been successfully approved', 'invoice'), 'admin');
    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=invoice/admin/profiles.php');
    exit;
  }
  
  // REJECT PROFILE
  if(Params::getParam('what') == 'reject' && Params::getParam('id') > 0) { 
    ModelINV::newInstance()->rejectProfile(Params::getParam('id'));
    osc_add_flash_ok_message(__('User profile has been successfully rejected', 'invoice'), 'admin');
    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=invoice/admin/profiles.php');
    exit;
  }
  
  $profiles = ModelINV::newInstance()->getProfiles(Params::getParamsAsArray());
?>


<div class="mb-body">

  <!-- USER PROFILES SECTION -->
  <div class="mb-box mb-bp">
    <div class="mb-head"><i class="fa fa-list"></i> <?php _e('Business Profiles List', 'invoice'); ?></div>

    <div class="mb-inside">
      <?php if(inv_param('validation') == 1) { ?>
        <div class="mb-row mb-notes">
          <div class="mb-line"><?php _e('Profiles validation is enabled. Only validated/approved profiles will be used on invoices.', 'invoice'); ?></div>
        </div>
      <?php } ?>
      
      <form name="promo_form" action="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=invoice/admin/profiles.php" method="POST" enctype="multipart/form-data" >
        <div id="mb-search-table">
          <div class="mb-col-3">
            <label for="uname"><?php _e('User Name', 'invoice'); ?></label>
            <input type="text" name="uname" value="<?php echo Params::getParam('uname'); ?>" />
          </div>
          
          <div class="mb-col-3">
            <label for="uemail"><?php _e('User Email', 'invoice'); ?></label>
            <input type="text" name="uemail" value="<?php echo Params::getParam('uemail'); ?>" />
          </div>
          
          <div class="mb-col-3">
            <label for="vat"><?php _e('Int. VAT Number', 'invoice'); ?></label>
            <input type="text" name="vat" value="<?php echo Params::getParam('vat'); ?>" />
          </div>
          
          <div class="mb-col-3">
            <label for="vat_local"><?php _e('Local VAT Number', 'invoice'); ?></label>
            <input type="text" name="vat_local" value="<?php echo Params::getParam('vat_local'); ?>" />
          </div>
          
          <div class="mb-col-3">
            <label for="header"><?php _e('Invoice Header', 'invoice'); ?></label>
            <input type="text" name="header" value="<?php echo Params::getParam('header'); ?>" />
          </div>
          
          <div class="mb-col-3">
            <label for="ship"><?php _e('Shipping Address', 'invoice'); ?></label>
            <input type="text" name="ship" value="<?php echo Params::getParam('ship'); ?>" />
          </div>
          
          <div class="mb-col-3">
            <label for="status"><?php _e('Status', 'invoice'); ?></label>
            <select name="status">
              <option value="" <?php if(Params::getParam('status') == "") { ?>selected="selected"<?php } ?>><?php _e('All', 'invoice'); ?></option>
              <option value="5" <?php if(Params::getParam('status') == 5) { ?>selected="selected"<?php } ?>><?php _e('Pending', 'invoice'); ?></option>
              <option value="1" <?php if(Params::getParam('status') == 1) { ?>selected="selected"<?php } ?>><?php _e('Approved', 'invoice'); ?></option>
              <option value="9" <?php if(Params::getParam('status') == 9) { ?>selected="selected"<?php } ?>><?php _e('Rejected', 'invoice'); ?></option>
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
          <div class="mb-col-1 mb-align-left"><?php _e('ID', 'invoice');?></div>
          <div class="mb-col-2 mb-align-left"><?php _e('User Name', 'invoice');?></div>
          <div class="mb-col-2 mb-align-left"><?php _e('Status', 'invoice');?></div>
          <div class="mb-col-3 mb-align-left"><?php _e('Int. VAT Number', 'invoice');?></div>
          <div class="mb-col-3 mb-align-left"><?php _e('Local VAT Number', 'invoice');?></div>
          <div class="mb-col-4 mb-align-left"><?php _e('Invoice Header', 'invoice');?></div>
          <div class="mb-col-4 mb-align-left"><?php _e('Shipping Address', 'invoice');?></div>
          <div class="mb-col-5 mb-align-right">&nbsp;</div>
        </div>

        <?php if(count($profiles) <= 0) { ?>
          <div class="mb-table-row mb-row-empty">
            <i class="fa fa-warning"></i><span><?php _e('No profiles has been found', 'invoice'); ?></span>
          </div>
        <?php } else { ?>
          <?php foreach($profiles as $p) { ?>
            <?php
              $status = inv_user_profile_status($p);
            ?>
            
            <div class="mb-table-row">
              <div class="mb-col-1 mb-col-id mb-align-left"><?php echo $p['fk_i_user_id']; ?></div>
              <div class="mb-col-2 mb-align-left"><a class="mb-has-tooltip-light" title="<?php echo $p['s_email']; ?>" href="<?php echo osc_admin_base_url(true); ?>?page=users&action=edit&id=<?php echo $p['fk_i_user_id']; ?>"><?php echo (@$p['s_name'] <> '' ? $p['s_name'] : __('Unknown/Removed user', 'invoice')); ?></a></div>
              <div class="mb-col-2 mb-align-left">
                <span class="mb-inv-stat <?php echo $status['status']; ?>"><?php echo $status['name']; ?></span>
              </div>
              <div class="mb-col-3 mb-align-left"><?php echo ($p['s_vat_number'] <> '' ? $p['s_vat_number'] : '-'); ?></div>
              <div class="mb-col-3 mb-align-left"><?php echo ($p['s_vat_number_local'] <> '' ? $p['s_vat_number_local'] : '-'); ?></div>
              <div class="mb-col-4 mb-align-left"><?php echo ($p['s_header'] <> '' ? $p['s_header'] : '-'); ?></div>
              <div class="mb-col-4 mb-align-left"><?php echo ($p['s_ship_to'] <> '' ? $p['s_ship_to'] : '-'); ?></div>

              <div class="mb-col-5 mb-col-del mb-align-right">
              
                <?php if(inv_param('validation') == 1) { ?>
                  <?php if($p['i_vat_number_verified'] == 0 || $p['i_vat_number_verified'] == '' || $p['i_vat_number_verified'] == 9) { ?>
                    <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=invoice/admin/profiles.php&what=approve&id=<?php echo $p['fk_i_user_id']; ?>" class="mb-inv-approve mb-btn mb-button-green"><i class="fa fa-check"></i> <?php _e('Approve', 'invoice'); ?></a>
                  <?php } ?>
                  
                  <?php if($p['i_vat_number_verified'] == 0 || $p['i_vat_number_verified'] == '' || $p['i_vat_number_verified'] == 1) { ?>
                    <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=invoice/admin/profiles.php&what=reject&id=<?php echo $p['fk_i_user_id']; ?>" class="mb-inv-reject mb-btn mb-button-gray"><i class="fa fa-times"></i> <?php _e('Reject', 'invoice'); ?></a>
                  <?php } ?>
                <?php } ?>

                <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=invoice/admin/profile_edit.php&id=<?php echo $p['fk_i_user_id']; ?>" class="mb-inv-edit mb-button-blue mb-btn"><i class="fa fa-pencil"></i> <?php _e('Edit', 'invoice'); ?></a>

                <?php if(inv_is_demo()) { ?>
                  <a href="#" class="mb-inv-remove mb-button-red mb-btn mb-has-tooltip-light mb-disabled" disabled title="This is demo site, you cannot remove invoice"><i class="fa fa-trash"></i></a>
                <?php } else { ?>
                  <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=invoice/admin/profiles.php&what=remove&id=<?php echo $p['fk_i_user_id']; ?>" class="mb-inv-remove mb-btn mb-button-red mb-has-tooltip-light" title="<?php echo osc_esc_html(__('Remove profile', 'invoice')); ?>" onclick="return confirm('<?php echo osc_esc_js(__('Are you sure you want to remove this profile? Action cannot be undone.', 'invoice')); ?>')"><i class="fa fa-trash"></i></a>
                <?php } ?>
              </div>
            </div>
          <?php } ?>
        <?php } ?>

        <div class="mb-row">&nbsp;</div>

      </div>
    </div>
  </div>


</div>


<?php echo inv_footer(); ?>