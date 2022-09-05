<?php
if ((!defined('ABS_PATH')))
    exit('ABS_PATH is not loaded. Direct access is not allowed.');
/*
 * Copyright (C) 2018 Puiu Calin
 * This program is a commercial software: is forbidden to use this software without licence, 
 * on multiple installations, and by purchasing from other source than those authorized for the sale of software.
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */
gdpr_cookie_script();
?>
<div class="gdp_cookie_widget <?php if (gdpr_cookie_valid()) { ?> hide_widget<?php } ?>">
    <?php if (h_gdpr_is_set()) { ?>
        <div class="close_cookie_widget">X</div>
    <?php } ?>
    <div class="visible">
        <div class="text">
            <?php _e('This site uses cookies and other tracking technologies to assist with navigation and your ability to provide feedback, analyse your use of our products and services, assist with our promotional and marketing efforts, and provide content from third parties.', 'gdpr_cookie'); ?>
        </div>
        <div class="accept">
            <div class="bt_accept">
                <?php if (h_gdpr_is_set()) { ?>
                    <?php _e('Save', 'gdpr_cookie'); ?>
                <?php } else { ?>
                    <?php _e('Accept', 'gdpr_cookie'); ?>
                <?php } ?>
            </div>
            <div class="bt_settings"><img title=" <?php _e('Settings', 'gdpr_cookie'); ?>" src="<?php echo gdpr_cookie_resource('settings_30.png'); ?>" /></div>
        </div>
    </div>
    <div class="hide_block_w">
        <h3><?php _e('Privacy', 'gdpr_cookie'); ?></h3>
        <div class="c_block">
            <span class="c_text">
                <a href="<?php echo osc_esc_html(osc_get_preference('privacy_link', 'gdpr_cookie')); ?>">
                    <?php _e('Privacy Policy Page', 'gdpr_cookie'); ?>
                </a>
            </span>
        </div>
        <?php if (osc_get_preference('google_analytics', 'gdpr_cookie') == 1) { ?>
            <div class="c_block">
                <label class="switch">
                    <input data-option="google_analytics" class="gd_preference" type="checkbox" value="1" <?php if (h_gdpr_active_analytics() || !h_gdpr_is_set()) { ?>checked="checked"<?php } ?>>
                    <span class="slider  round"></span>
                </label>
                <span class="c_text">
                    <?php _e('Allow Google Analytics', 'gdpr_cookie'); ?>
                </span>
            </div>
        <?php } ?>
        <?php if (osc_get_preference('ads', 'gdpr_cookie') == 1) { ?>
            <div class="c_block">
                <label class="switch">
                    <input data-option="ads" class="gd_preference" type="checkbox" value="1" <?php if (h_gdpr_active_ads() || !h_gdpr_is_set()) { ?>checked="checked"<?php } ?>>
                    <span class="slider  round"></span>
                </label>
                <span class="c_text">
                    <?php _e('Allow Advertising', 'gdpr_cookie'); ?>
                </span>
            </div>
        <?php } ?>
        <?php if (osc_get_preference('custom', 'gdpr_cookie') == 1) { ?>
            <div class="c_block">
                <label class="switch">
                    <input data-option="custom" class="gd_preference" type="checkbox" value="1" <?php if (h_gdpr_active_custom() || !h_gdpr_is_set()) { ?>checked="checked"<?php } ?>>
                    <span class="slider  round"></span>
                </label>
                <span class="c_text">
                    <?php if (osc_get_preference('custom_text', 'gdpr_cookie') == "") { ?>
                        <?php _e('Custom', 'gdpr_cookie'); ?>
                    <?php } else { ?>
                        <?php _e(osc_get_preference('custom_text', 'gdpr_cookie'), 'gdpr_cookie'); ?>
                    <?php } ?>
                </span>
            </div>
        <?php } ?>
        <div class="c_short_text">
            * <?php _e('By default the site will store cookies that are necessary for the site function and cannot be switched off in our systems. By visiting the site you are agree with this, and by enabling the above preferences you confirm that you read an understand what each preference from the above list means.', 'gdpr_cookie'); ?>
        </div>
    </div>
</div>