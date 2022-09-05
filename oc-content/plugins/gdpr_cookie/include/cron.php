<?php

if ((!defined('ABS_PATH')))
    exit('ABS_PATH is not loaded. Direct access is not allowed.');
/*
 * Copyright (C) 2018 Puiu Calin
 * This program is a commercial software: is forbidden to use this software without licence, 
 * on multiple installations, and by purchasing from other source than those authorized for the sale of software.
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */

function gdpr_cookie_cron() {
    $records = GdprCookie::newInstance()->get_records_cron();
    if (!empty($records)) {
        foreach ($records as $record) {
            $user_id = $record['g_user_id'];
            $file = gdpr_cookie_user_data_path_zip('data_' . $user_id . '.zip');
            if (@unlink($file)) {
                GdprCookie::newInstance()->export_data_info($user_id, null, null);
            }
        }
    }
}

if (osc_get_preference('delete_zip', 'gdpr_cookie') == 1) {
    osc_add_hook('cron_hourly', 'gdpr_cookie_cron');
}