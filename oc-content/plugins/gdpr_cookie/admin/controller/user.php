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

$request = Params::getParam("request");
switch ($request) {
    case('delete_archive'):
        $user_id = Params::getParam('user_id');
        if (!is_numeric($user_id)) {
            osc_add_flash_error_message(__('No user id', 'gdpr_cookie'), 'admin');
            header("Location: " . osc_admin_base_url());
            exit();
        }
        $file = gdpr_cookie_user_data_path_zip('data_' . $user_id . '.zip');
        if (file_exists($file)) {
            if (@unlink($file)) {
                GdprCookie::newInstance()->export_data_info($user_id, null, null);
            }
            osc_add_flash_ok_message(__('The archive was deleted', 'gdpr_cookie'), 'admin');
            header("Location: " . osc_admin_base_url(true). '?page=users');
        } else {
            osc_add_flash_error_message(__('No file for this user', 'gdpr_cookie'), 'admin');
            header("Location: " . osc_admin_base_url());
            exit();
        }
        break;
}