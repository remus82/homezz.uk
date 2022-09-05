<?php

if ((!defined('ABS_PATH')))
    exit('ABS_PATH is not loaded. Direct access is not allowed.');
/*
 * Copyright (C) 2018 Puiu Calin
 * This program is a commercial software: is forbidden to use this software without licence, 
 * on multiple installations, and by purchasing from other source than those authorized for the sale of software.
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */
if (!osc_is_web_user_logged_in()) {
    exit('Only users');
}


$request = Params::getParam('request');

switch ($request) {
    case('export_data'):
        $exp_user_data = Params::getParam('user_data');
        $exp_comments = Params::getParam('user_comments');
        $exp_items = Params::getParam('user_items');
        if (!$exp_comments && !$exp_items && !$exp_items) {
            osc_add_flash_error_message(__('No action was selected, please select what data to export', 'gdpr_cookie'));
            header("Location:" . osc_route_url('user_data'));
            exit();
        }

        $user_id = osc_logged_user_id();
        $error = '';
        osc_run_hook('before_gdpr_export', $user_id);
        $folder = 'data_' . $user_id;
        $upload_path = gdpr_cookie_user_data_path_folder($folder);
        $zip_folder = gdpr_cookie_user_data_path_zip();
        if (!is_dir($upload_path)) {
            if (!@mkdir($upload_path, 0755, true)) {
                $error = 1;
            }
            //create zip folder
            if (!file_exists($zip_folder)) {
                if (!@mkdir($zip_folder, 0755)) {
                    $error = 1;
                }
            }
            if ($exp_user_data) {
                $user_folder = $upload_path . '/user';
                if (!is_dir($user_folder)) {
                    if (!@mkdir($user_folder, 0755, true)) {
                        $error = 1;
                    }
                }
            }
            if ($exp_comments) {
                $comments_folder = $upload_path . '/comments';
                if (!is_dir($comments_folder)) {
                    if (!@mkdir($comments_folder, 0755, true)) {
                        $error = 1;
                    }
                }
            }
            if ($exp_items) {
                $items_folder = $upload_path . '/items';
                if (!is_dir($items_folder)) {
                    if (!@mkdir($items_folder, 0755, true)) {
                        $error = 1;
                    }
                }
            }
            //some hook to create extra directory
            osc_run_hook('gdpr_export_make_dir', $user_id, $upload_path);
        }
        if (!$error) {
            /*
             * request start
             */
            if ($exp_user_data) {
                //user
                $user_data = GdprCookie::newInstance()->get_user_data($user_id);
                //insert log in user data
                $log = GdprCookie::newInstance()->get_user_log($user_id);
                if (!empty($log)) {
                    $user_data['log'] = $log;
                }
                //gdpr settings
                $gdpr = GdprCookie::newInstance()->get_preferences($user_id);
                if (!empty($gdpr)) {
                    $user_data['gdpr'] = $gdpr;
                }
                //add new data if you want
                $user_data = osc_apply_filter('gdpr_export_user', $user_data);
                $error = gdpr_cookie_export_file_content($upload_path . '/user/user.json', $user_data);
            }
            if ($exp_comments) {
                //comments
                $comments_data = GdprCookie::newInstance()->get_comments($user_id);
                $comments_data = osc_apply_filter('gdpr_export_comments', $comments_data);
                if (!empty($comments_data)) {
                    $error = gdpr_cookie_export_file_content($upload_path . '/comments/comments.json', $comments_data);
                }
            }
            //custom
            $custom = osc_apply_filter('gdpr_export_custom', $user_id);
            $custom_path = osc_apply_filter('gdpr_export_custom_path', $user_id);
            if (!empty($custom) && !empty($custom_path)) {
                if ($custom != $user_id) {
                    $error = gdpr_cookie_export_file_content($upload_path . '/' . $custom_path, $custom);
                }
            }

            //error data
            $data = array(
                'request_date' => date("Y-m-d H:i:s"),
                'error' => 'yes',
                'status' => 'error-on-export'
            );
            //items
            if ($error) {
                //delete folder, some error
                gdpr_cookie_delete_folder($upload_path);
                //some message for user
                gdpr_cookie_export_status($user_id, $data);
                osc_add_flash_error_message(__('Some error occurred when exporting the data', 'gdpr_cookie'));
            } else {
                if ($exp_items) {
                    //export items
                    $error = gdpr_cookie_export_items($user_id, $upload_path);
                }
                if ($error) {
                    //delete folder, some error
                    gdpr_cookie_delete_folder($upload_path);
                    gdpr_cookie_export_status($user_id, $data);
                    osc_add_flash_error_message(__('Some error occurred when exporting the data', 'gdpr_cookie'));
                } else {
                    //continue zip
                    $zip_return = gdpr_cookie_ZipArchive($upload_path, $zip_folder . '/' . $folder);
                    if ($zip_return) {
                        //all it ok delete the folder
                        gdpr_cookie_delete_folder($upload_path);

                        $data = array(
                            'request_date' => date("Y-m-d H:i:s"),
                            'error' => 'no',
                            'status' => 'archive-created',
                            'archive_name' => $folder
                        );
                        gdpr_cookie_export_status($user_id, $data, 1);
                        osc_add_flash_ok_message(__('The zip archive was created, you can download the archive bellow.', 'gdpr_cookie'));
                    } else {
                        gdpr_cookie_delete_folder($upload_path);
                        gdpr_cookie_export_status($user_id, $data);
                        osc_add_flash_error_message(__('Some error occurred when exporting the data', 'gdpr_cookie'));
                    }
                }
            }
        } else {
            //some error show to user
            $data = array(
                'request_date' => date("Y-m-d H:i:s"),
                'error' => 'yes',
                'status' => 'folder-create-error',
            );
            gdpr_cookie_export_status($user_id, $data);
            osc_add_flash_error_message(__('Some error occurred when exporting the data', 'gdpr_cookie'));
        }
        header("Location:" . osc_route_url('user_data'));
        break;
    case('download'):
        if (!file_exists(h_gdpr_user_file())) {
            osc_add_flash_error_message(__('You have no file for download, please create the file first.', 'gdpr_cookie'));
            header("Location:" . osc_route_url('user_data'));
            exit();
        }
        $user_id = osc_logged_user_id();
        $file_n = 'data_' . $user_id . '.zip';
        $file = h_gdpr_user_file();
        header("X-Robots-Tag: noindex, nofollow", true);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate");
        header("Content-Type: application/zip");
        header("Content-Disposition: attachment; filename=\"$file_n\"");
        header("Content-Length: " . filesize($file));
        header('Connection: close');
        ob_clean();
        flush();
        readfile($file);
        exit();
        break;
    default :
        require_once gdpr_cookie_path_file('/include/view/ExportUser.php');
        break;
}