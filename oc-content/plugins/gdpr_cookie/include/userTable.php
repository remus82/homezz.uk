<?php

if ((!defined('ABS_PATH')))
    exit('ABS_PATH is not loaded. Direct access is not allowed.');
/*
 * Copyright (C) 2018 Puiu Calin
 * This program is a commercial software: is forbidden to use this software without licence, 
 * on multiple installations, and by purchasing from other source than those authorized for the sale of software.
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */

function gdpr_cookie_user_table($table) {
    return $table->addColumn('gdpr_cookie_data', __('Export data'));
}

osc_add_hook('admin_users_table', 'gdpr_cookie_user_table');

function gdpr_cookie_user_table_data($row, $aRow) {
    $user_id = $aRow['pk_i_id'];
    $data = GdprCookie::newInstance()->get_preferences($user_id);
    $text = '-';
    if (!empty($data)) {
        if (!empty($data['download_data'])) {
            $decode = json_decode($data['download_data'], true);
            if ($decode['error'] == 'no') {
                $href = osc_admin_render_plugin_url('gdpr_cookie/admin/controller/user.php?request=delete_archive&user_id=') . $user_id;
                $href = '<a href="' . $href . '">' . __('Delete archive') . '</a>';
                $date = __('Archive created on: ') . $decode['request_date'];
                $name = __('Name: ') . $decode['archive_name'];
                $text = $date . '<br />' . $name . '<br />' . $href;
            } else {
                $error = __('Error: ') . $decode['status'];
                $date = __('Archive requested on: ') . $decode['request_date'];
                $text = $date . '<br />' . $error;
            }
        }
    }
    $row['gdpr_cookie_data'] = $text;
    return $row;
}

osc_add_filter('users_processing_row', 'gdpr_cookie_user_table_data');
