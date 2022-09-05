<?php
if ((!defined('ABS_PATH')))
    exit('ABS_PATH is not loaded. Direct access is not allowed.');
if (!OC_ADMIN)
    exit('User access is not allowed.');
/*
 * Copyright (C) 2018 Puiu Calin
 * This program is a commercial software: is forbidden to use this software without licence, 
 * on multiple installations, and by purchasing from other source than those authorized for the sale of software.
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */
?>
<h2>
    <?php _e('Settings', 'gdpr_cookie'); ?>
    <a class="gdpr_menu_link" href="<?php echo osc_admin_render_plugin_url('gdpr_cookie/admin/help.php'); ?>"><?php _e('Help', 'gdpr_cookie'); ?></a>
</h2>
<form class="gdpr-cookie-settings" action="<?php echo osc_admin_render_plugin_url('gdpr_cookie/admin/settings.php'); ?>" method="post">  
    <input type="hidden" name="gdpr_action_specific" value="done_settings" />
    <h3><?php _e('Data collection', 'gdpr_cookie'); ?></h3>
    <fieldset>
        <div class="form-horizontal">
            <i style="margin-left:180px;"><?php _e('Choose what option you use on your site so user can choose what data will be allowed for collect.', 'gdpr_cookie'); ?></i>
            <br />
            <br />
            <div class="form-row">
                <div class="form-label"><?php _e('You use on site', 'gdpr_cookie'); ?></div>
                <div class="form-controls">
                    <label>
                        <input type="checkbox" value="1" name="google_analytics" <?php if (osc_get_preference('google_analytics', 'gdpr_cookie') == 1) { ?><?php echo 'checked="checked"'; ?><?php } ?>/> 
                        <?php _e('Google analytics', 'gdpr_cookie'); ?>
                    </label>
                    <br />
                    <label>
                        <input type="checkbox" value="1" name="ads" <?php if (osc_get_preference('ads', 'gdpr_cookie') == 1) { ?><?php echo 'checked="checked"'; ?><?php } ?>/> 
                        <?php _e('External services like advertising adsense or other', 'gdpr_cookie'); ?>
                    </label>
                    <br />
                    <label>
                        <input type="checkbox" value="1" name="custom" <?php if (osc_get_preference('custom', 'gdpr_cookie') == 1) { ?><?php echo 'checked="checked"'; ?><?php } ?>/> 
                        <?php _e('Custom option', 'gdpr_cookie'); ?>
                    </label>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('Privacy Policy Page link', 'gdpr_cookie'); ?></div>
                <div class="form-controls">
                    <input style="width:500px;" name="privacy_link" value="<?php echo osc_esc_html(osc_get_preference('privacy_link', 'gdpr_cookie')); ?>" />
                    <div class="help-box"><?php _e('Enter the link for your policy page', 'gdpr_cookie'); ?></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('Tracking ID', 'gdpr_cookie'); ?></div>
                <div class="form-controls">
                    <input name="analytics_id" value="<?php echo osc_esc_html(osc_get_preference('analytics_id', 'gdpr_cookie')); ?>" />
                    <div class="help-box"><?php _e('Enter the tracking id for Google Analytics', 'gdpr_cookie'); ?></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('Custom text', 'gdpr_cookie'); ?></div>
                <div class="form-controls">
                    <input name="custom_text" value="<?php echo osc_esc_html(osc_get_preference('custom_text', 'gdpr_cookie')); ?>" />
                    <div class="help-box"><?php _e('Enter the text for custom option', 'gdpr_cookie'); ?></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('Force accept', 'gdpr_cookie'); ?></div>
                <div class="form-controls">
                    <select name="force_gdpr">
                        <option <?php if (osc_get_preference('force_gdpr', 'gdpr_cookie') == 1) { ?><?php echo 'selected="selected"'; ?><?php } ?> value="1"><?php _e('Yes', 'gdpr_cookie'); ?></option>
                        <option <?php if (osc_get_preference('force_gdpr', 'gdpr_cookie') == 0) { ?><?php echo 'selected="selected"'; ?><?php } ?> value="0"><?php _e('No', 'gdpr_cookie'); ?></option>
                    </select>
                    <div class="help-box"><?php _e('Force the accept of cookie and gdpr by cover the site after a few seconds of arriving on the site.', 'gdpr_cookie'); ?></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('Force delay', 'gdpr_cookie'); ?></div>
                <div class="form-controls">
                    <input name="force_delay" value="<?php echo osc_esc_html(osc_get_preference('force_delay', 'gdpr_cookie')); ?>" />
                    <div class="help-box"><?php _e('Set a delay in seconds. After this time the coverage of page will be displayed, and the user will be forced to save the gdpr preferences. The default value is 30 seconds.', 'gdpr_cookie'); ?></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('Delete archives', 'gdpr_cookie'); ?></div>
                <div class="form-controls">
                    <select name="delete_zip">
                        <option <?php if (osc_get_preference('delete_zip', 'gdpr_cookie') == 1) { ?><?php echo 'selected="selected"'; ?><?php } ?> value="1"><?php _e('Yes', 'gdpr_cookie'); ?></option>
                        <option <?php if (osc_get_preference('delete_zip', 'gdpr_cookie') == 0) { ?><?php echo 'selected="selected"'; ?><?php } ?> value="0"><?php _e('No', 'gdpr_cookie'); ?></option>
                    </select>
                    <div class="help-box"><?php _e('Delete the requested data after some time. This option will delete the archives to save space. The cron has to work.', 'gdpr_cookie'); ?></div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-label"><?php _e('Delete days', 'gdpr_cookie'); ?></div>
                <div class="form-controls">
                    <input name="delete_zip_days" value="<?php echo osc_esc_html(osc_get_preference('delete_zip_days', 'gdpr_cookie')); ?>" />
                    <div class="help-box"><?php _e('Set a delay in seconds. After this time the coverage of page will be displayed, and the user will be forced to save the gdpr preferences. The default value is 30 seconds.', 'gdpr_cookie'); ?></div>
                </div>
            </div>
            <h3><?php _e('User agreements on forms submission', 'gdpr_cookie'); ?></h3>
            <i><?php _e('If you use this option you have to edit your theme to add the line that will display the checkboxes. You will find the documentation on help page.', 'gdpr_cookie'); ?></i>
            <br />
            <br />
            <div class="form-row">
                <div class="form-label"><?php _e('Agreements checkboxes on', 'gdpr_cookie'); ?></div>
                <div class="form-controls">
                    <label>
                        <input type="checkbox" value="1" name="ag_register_user" <?php if (osc_get_preference('ag_register_user', 'gdpr_cookie') == 1) { ?><?php echo 'checked="checked"'; ?><?php } ?>/> 
                        <?php _e('Register user form', 'gdpr_cookie'); ?>
                    </label>
                    <br />
                    <label>
                        <input type="checkbox" value="1" name="ag_post_add" <?php if (osc_get_preference('ag_post_add', 'gdpr_cookie') == 1) { ?><?php echo 'checked="checked"'; ?><?php } ?>/> 
                        <?php _e('Publish item from', 'gdpr_cookie'); ?>
                    </label>
                    <br />
                    <label>
                        <input type="checkbox" value="1" name="ag_edit_add" <?php if (osc_get_preference('ag_edit_add', 'gdpr_cookie') == 1) { ?><?php echo 'checked="checked"'; ?><?php } ?>/> 
                        <?php _e('Edit item form', 'gdpr_cookie'); ?>
                    </label>
                    <br />
                    <label>
                        <input type="checkbox" value="1" name="ag_contact" <?php if (osc_get_preference('ag_contact', 'gdpr_cookie') == 1) { ?><?php echo 'checked="checked"'; ?><?php } ?>/> 
                        <?php _e('Contact site form', 'gdpr_cookie'); ?>
                    </label>
                    <br />
                    <label>
                        <input type="checkbox" value="1" name="ag_comment" <?php if (osc_get_preference('ag_comment', 'gdpr_cookie') == 1) { ?><?php echo 'checked="checked"'; ?><?php } ?>/> 
                        <?php _e('Comment form', 'gdpr_cookie'); ?>
                    </label>
                    <br />
                    <label>
                        <input type="checkbox" value="1" name="ag_contact_seller" <?php if (osc_get_preference('ag_contact_seller', 'gdpr_cookie') == 1) { ?><?php echo 'checked="checked"'; ?><?php } ?>/> 
                        <?php _e('Contact seller form', 'gdpr_cookie'); ?>
                    </label>
                    <br />
                    <label>
                        <input type="checkbox" value="1" name="ag_send_friend" <?php if (osc_get_preference('ag_send_friend', 'gdpr_cookie') == 1) { ?><?php echo 'checked="checked"'; ?><?php } ?>/> 
                        <?php _e('Send friend from', 'gdpr_cookie'); ?>
                    </label>
                </div>
                <div style="margin-top:20px;padding-left:180px;">
                    <i><?php _e('The line will display a simple checkbox for gdpr forms, see help page from implementation.', 'gdpr_cookie'); ?></i>
                    <pre>
                            &lt;?php gdpr_cookie_form_checkbox() ?&gt;
                    </pre>
                    <br />
                </div>
            </div>
        </div>
        <div class="form-actions">
            <input type="submit" value="<?php _e('Save changes', 'gdpr_cookie'); ?>" class="btn btn-submit" />
        </div>
    </fieldset>
</form>
<div class="cbk_author">
    <?php gdpr_cookie_autor_rights(); ?>
</div>
<?php
switch (Params::getParam('gdpr_action_specific')) {
    case('done_settings'):
        osc_set_preference('google_analytics', trim(Params::getParam('google_analytics', false, false, false)), 'gdpr_cookie');
        osc_set_preference('ads', trim(Params::getParam('ads', false, false, false)), 'gdpr_cookie');
        osc_set_preference('custom', trim(Params::getParam('custom', false, false, false)), 'gdpr_cookie');
        osc_set_preference('privacy_link', Params::getParam('privacy_link'), 'gdpr_cookie');
        osc_set_preference('analytics_id', Params::getParam('analytics_id'), 'gdpr_cookie');
        osc_set_preference('custom_text', Params::getParam('custom_text'), 'gdpr_cookie');
        osc_set_preference('ag_register_user', Params::getParam('ag_register_user'), 'gdpr_cookie');
        osc_set_preference('ag_post_add', Params::getParam('ag_post_add'), 'gdpr_cookie');
        osc_set_preference('ag_edit_add', Params::getParam('ag_edit_add'), 'gdpr_cookie');
        osc_set_preference('ag_contact', Params::getParam('ag_contact'), 'gdpr_cookie');
        osc_set_preference('ag_comment', Params::getParam('ag_comment'), 'gdpr_cookie');
        osc_set_preference('ag_contact_seller', Params::getParam('ag_contact_seller'), 'gdpr_cookie');
        osc_set_preference('ag_send_friend', Params::getParam('ag_send_friend'), 'gdpr_cookie');
        osc_set_preference('force_gdpr', Params::getParam('force_gdpr'), 'gdpr_cookie');
        osc_set_preference('force_delay', Params::getParam('force_delay'), 'gdpr_cookie');
        osc_set_preference('delete_zip', Params::getParam('delete_zip'), 'gdpr_cookie');
        osc_set_preference('delete_zip_days', Params::getParam('delete_zip_days'), 'gdpr_cookie');

        osc_add_flash_ok_message(__('Plugin settings updated correctly', 'gdpr_cookie'), 'admin');
        header("Location:" . osc_admin_render_plugin_url('gdpr_cookie/admin/settings.php'));
        break;
}