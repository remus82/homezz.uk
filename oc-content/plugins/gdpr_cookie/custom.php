<?php
if ((!defined('ABS_PATH')))
    exit('ABS_PATH is not loaded. Direct access is not allowed.');
/* 
 * Copyright (C) 2018 Puiu Calin
 * This program is a commercial software: is forbidden to use this software without licence, 
 * on multiple installations, and by purchasing from other source than those authorized for the sale of software.
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */

/*
 * Add your extra functions here to add extra data in archive
 * Bellow you have some examples how to use the extra option implement in the plugin that will allow you to add your data in the archive
 * 
 */




/*
 * some examples for custom filter to ad extra data
 */

/* <- this will be remove
function gdpr_cookie_custom_folder($user_id, $upload_path) {
    //create the foder in data_user_id 
    $custom_folder = $upload_path . '/custom';
    if (!@mkdir($custom_folder, 0755, true)) {
        //some error if the folder it not created
        osc_add_flash_error_message(__('Some error ...', 'gdpr_cookie'));
        header("Location" . osc_base_url());
        exit();
    }
}

osc_add_hook('gdpr_export_make_dir', 'gdpr_cookie_custom_folder');

function gdpr_cookie_custom_content($user_id) {
    $content = array(
        'some_id' => 123,
        'name_of_etc' => 'gdpr',
        'other_data' => 'yeeeee',
    );
    return $content;
}
osc_add_filter('gdpr_export_custom', 'gdpr_cookie_custom_content');

function gdpr_cookie_custom_file($user_id) {
    //folder/file
    $file = 'custom/custom.json';
    return $file;
}
osc_add_filter('gdpr_export_custom_path', 'gdpr_cookie_custom_file');


this will be remove -> */