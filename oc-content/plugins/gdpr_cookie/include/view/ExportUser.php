<?php
if ((!defined('ABS_PATH')))
    exit('ABS_PATH is not loaded. Direct access is not allowed.');
/*
 * Copyright (C) 2018 Puiu Calin
 * This program is a commercial software: is forbidden to use this software without licence, 
 * on multiple installations, and by purchasing from other source than those authorized for the sale of software.
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */
$data = GdprCookie::newInstance()->get_preferences(osc_logged_user_id());
?>
<div class="gdpr-export-user-options">
    <h1><?php _e('Manage your data', 'gdpr_cookie'); ?></h1>
    <div class="g-c-form">
        <form action="<?php echo osc_route_url('user_data'); ?>" method="POST">
            <input type="hidden" name="request" value="export_data" />
            <div class="gdpr-row">
                <div class="gdpr-option">
                    <label class="switch">
                        <input name="user_data" value="1" checked="checked" type="checkbox">
                        <span class="slider  round"></span>
                    </label>
                </div>
                <div class="gdpr-label">
                    <?php _e('User account information', 'gdpr_cookie'); ?> 
                </div>
            </div>
            <div class="gdpr-row">
                <div class="gdpr-option">
                    <label class="switch">
                        <input name="user_comments" value="1" checked="checked" type="checkbox">
                        <span class="slider  round"></span>
                    </label>
                </div>
                <div class="gdpr-label">
                    <?php _e('Comments', 'gdpr_cookie'); ?> 
                </div>
            </div>
            <div class="gdpr-row">
                <div class="gdpr-option">
                    <label class="switch">
                        <input name="user_items" value="1" checked="checked" type="checkbox">
                        <span class="slider  round"></span>
                    </label>
                </div>
                <div class="gdpr-label">
                    <?php _e('Items', 'gdpr_cookie'); ?> 
                </div>
            </div>
            <?php osc_run_hook('gdpr_export_form'); ?>
            <input type="submit" class="exp-gdpr-button button" value="<?php if (file_exists(h_gdpr_user_file())) { ?><?php _e('Recreate archive', 'gdpr_cookie'); ?><?php } else { ?><?php _e('Create archive', 'gdpr_cookie'); ?><?php } ?>" />
        </form>           
    </div>
    <?php if (file_exists(h_gdpr_user_file())) { ?>
        <div class="gdpr-file">
            <div class="left-icon">
                <img src="<?php echo gdpr_cookie_resource('zip.png'); ?>" />
            </div>
            <div class="archive-text">
                <div class="t-align">
                    <?php _e('Download your data', 'gdpr_cookie'); ?>:
                    <a href="<?php echo osc_route_url('user_data', array('request' => 'download')); ?>">
                        <strong> <?php _e('Download', 'gdpr_cookie'); ?> </strong>
                    </a>
                    <?php if (!empty($data['download_data'])) { ?>
                        <?php $decode = json_decode($data['download_data'], true); ?>
                        <?php if (isset($decode['request_date'])) { ?>
                            <span class="dt-date">
                                <?php echo osc_format_date($decode['request_date']); ?>
                            </span>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>