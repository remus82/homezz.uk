<?php
/*
 * Copyright (C) 2018 Puiu Calin
 * This program is a commercial software: is forbidden to use this software without licence, 
 * on multiple installations, and by purchasing from other source than those authorized for the sale of software.
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */

function gdpr_cookie_load($template = null) {
    switch ($template) {
        case('force_accept'):
            include gdpr_cookie_path_file('template/force_accept.php');
            break;
        default:
            include gdpr_cookie_path_file('template/base.php');
            break;
    }
}

function gdpr_cookie_path() {
    return osc_plugins_path() . 'gdpr_cookie/';
}

function gdpr_cookie_path_file($file) {
    return gdpr_cookie_path() . $file;
}

function gdpr_cookie_resource($file = null) {
    return osc_base_url() . 'oc-content/plugins/gdpr_cookie/images/' . $file;
}

function gdpr_cookie_user_data_path_folder($folder = null) {
    return gdpr_cookie_path() . 'data/folder/' . $folder;
}

function gdpr_cookie_user_data_path_zip($folder = null) {
    return gdpr_cookie_path() . 'data/zip/' . $folder;
}

function gdpr_cookie_pop_up() {
    /*
     * force options
     */
    if (!gdpr_cookie_valid()) {
        if (osc_get_preference('force_gdpr', 'gdpr_cookie') == 1) {
            //set cookie on firts visit
            $cookie = gdpr_cookie_get('gdpr_land_page');
            if (empty($cookie)) {
                $time = date('Y-m-d H:i:s');
                gdpr_cookie_set_cookie($time, 'gdpr_land_page');
            } else {
                //force accept
                $seconds = '30';
                $set_delay = osc_get_preference('force_delay', 'gdpr_cookie');
                if (is_numeric($set_delay)) {
                    $seconds = $set_delay;
                }
                $time = date("Y-m-d H:i:s", (strtotime(date($cookie)) + $seconds));
                if (date("Y-m-d H:i:s") > $time) {
                    gdpr_cookie_load('force_accept');
                }
            }
        }
    }
    /*
     * load options
     */
    gdpr_cookie_load();
}

osc_add_hook('after_html', 'gdpr_cookie_pop_up');

function gdpr_cookie_script() {
    ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.bt_settings img').hover(function () {
                $(this).attr('src', '<?php echo gdpr_cookie_resource('settings_30_hover.png'); ?>');
            }, function () {
                $(this).attr('src', '<?php echo gdpr_cookie_resource('settings_30.png'); ?>');
            });
            $('.bt_settings').click(function () {
                $('.gdp_cookie_widget .hide_block_w').slideToggle();
            });
            $('.gdp_cookie_widget .bt_accept').click(function () {
                array = {};
                $(".gd_preference").each(function () {
                    if ($(this).prop('checked') == true) {
                        var value = 1;
                    } else {
                        var value = 0;
                    }
                    array[$(this).attr('data-option')] = value;
                });
                array['accept'] = 1;
                var url = '<?php echo osc_ajax_plugin_url('gdpr_cookie/ajax/ajax.php') . '&gdpr_case=accept'; ?>';
                $.ajax({
                    type: "POST",
                    data: {result: array},
                    url: url,
                    dataType: 'json',
                    success: function (data) {
                        var success = data.success;
                        if (success == 'success') {
                            $('.gdp_cookie_widget ').slideToggle();
                            $('.cover-gdpr-cookie').hide();
                        } else {
                            alert('<?php echo osc_esc_js(__('Some error occurred, try again.', 'gdpr_cookie')); ?>');
                        }
                    }
                });
            });
            $('.gdpr_cookie_settings').click(function (event) {
                event.preventDefault();
                $('.gdp_cookie_widget .hide_block_w').show();
                $('.gdp_cookie_widget ').slideToggle();
            });
            $('.close_cookie_widget').click(function (event) {
                $('.gdp_cookie_widget ').slideToggle();
            });
        });
    </script>
    <?php
}

function gdpr_cookie_set_cookie($cookie_value = null, $cookie_name = null) {
    if ($cookie_name == null) {
        $cookie_name = 'accept_cookie_data';
    }
    $time = time() + 2592000;
    if (setcookie($cookie_name, $cookie_value, $time, "/")) {
        return true;
    }
}

function gdpr_cookie_get($cookie_name = null) {
    $cookie = '';
    if ($cookie_name == null) {
        $cookie_name = 'accept_cookie_data';
    }
    if (isset($_COOKIE[$cookie_name])) {
        $cookie = $_COOKIE[$cookie_name];
    }

    return $cookie;
}

function gdpr_cookie_valid() {
    $cookie = gdpr_cookie_get();
    $return = false;
    if (!empty($cookie)) {
        $json = json_decode($cookie, true);
        if ($json['accept'] == 1) {
            $return = true;
        }
    } else {
        if (osc_is_web_user_logged_in()) {
            $cookie = GdprCookie::newInstance()->get_preferences(osc_logged_user_id());
            if (!empty($cookie)) {
                $json = json_decode($cookie['s_value'], true);
                if ($json['accept'] == 1) {
                    $return = true;
                }
            }
        }
    }

    return $return;
}

function gdpr_cookie_active_preference($option) {
    switch ($option) {
        case('google_analytics'):
            $cookie = gdpr_cookie_get();
            $return = false;
            if (!empty($cookie)) {
                $json = json_decode($cookie, true);
                if (isset($json['google_analytics'])) {
                    if ($json['google_analytics'] == 1) {
                        $return = true;
                    }
                }
            }
            return $return;
            break;
        case('ads'):
            $cookie = gdpr_cookie_get();
            $return = false;
            if (!empty($cookie)) {
                $json = json_decode($cookie, true);
                if (isset($json['ads'])) {
                    if ($json['ads'] == 1) {
                        $return = true;
                    }
                }
            }
            return $return;
            break;
        case('custom'):
            $cookie = gdpr_cookie_get();
            $return = false;
            if (!empty($cookie)) {
                $json = json_decode($cookie, true);
                if (isset($json['custom'])) {
                    if ($json['custom'] == 1) {
                        $return = true;
                    }
                }
            }
            return $return;
            break;
        case('accept'):
            $cookie = gdpr_cookie_get();
            $return = false;
            if (!empty($cookie)) {
                $json = json_decode($cookie, true);
                if ($json['accept'] == 1) {
                    $return = true;
                }
            }
            return $return;
            break;
    }
}

function gdpr_cookie_analytics() {
    if (osc_get_preference('google_analytics', 'gdpr_cookie') == 1 && osc_get_preference('analytics_id', 'gdpr_cookie') != '' && h_gdpr_active_analytics()) {
        $id = osc_get_preference('analytics_id', 'gdpr_cookie');
        ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $id; ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '<?php echo $id; ?>');
        </script>
        <?php
    }
}

osc_add_hook('header', 'gdpr_cookie_analytics');

function gdpr_cookie_settings_link() {
    ?>
    <a class="gdpr_cookie_settings" href="#"><?php _e('Cookie settings', 'gdpr_cookie'); ?></a>
    <?php
}

function gdpr_cookie_after_login($user, $url_redirect = null) {
    $user_id = $user['pk_i_id'];
    $cookie = gdpr_cookie_get();
    if (!empty($cookie)) {
        GdprCookie::newInstance()->insert_preferences($cookie, $user_id);
    } else {
        $saved_data = GdprCookie::newInstance()->get_preferences($user_id);
        if (!empty($saved_data)) {
            gdpr_cookie_set_cookie($saved_data['s_value']);
        }
    }
}

function gdpr_cookie_after_register($userId) {
    $cookie = gdpr_cookie_get();
    if (!empty($cookie)) {
        if (!osc_user_validation_enabled()) {
            GdprCookie::newInstance()->insert_preferences($cookie, $userId);
        }
    }
}

function gdpr_cookie_user_delete($userId) {
    GdprCookie::newInstance()->delete_preferences($userId);
}

osc_add_hook('after_login', 'gdpr_cookie_after_login');
osc_add_hook('validate_user', 'gdpr_cookie_after_login');
osc_add_hook('user_register_completed', 'gdpr_cookie_after_register');
osc_add_hook('after_delete_user', 'gdpr_cookie_user_delete');

function gdpr_cookie_autor_rights() {
    ?>
    <div class="autor_calinbehtuk">
        <span class="p_ro"></span><span>osclassCalinbehtuk</span> &#9400; 
        <?php echo date("Y"); ?> 
        <?php _e('All rights reserved', 'gdpr_cookie'); ?>
        | <a target="_blank" href="https://osclass.calinbehtuk.ro/contact"><?php _e('Contact', 'gdpr_cookie'); ?></a>
        <div class="fb-like" data-href="https://www.facebook.com/OsclassMarket-2038630222819201" data-layout="standard" data-action="like" data-show-faces="false" data-share="false"></div>
    </div>
    <div id="fb-root"></div>
    <script>(function (d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id))
                return;
            js = d.createElement(s);
            js.id = id;
            js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5&appId=231774027372208";
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>
    <?php
}

function gdpr_cookie_update_plugin() {
    $version = osc_get_preference('version', 'gdpr_cookie');
    if ($version < GDPR_COOKIE_VERSION) {
        osc_set_preference('version', GDPR_COOKIE_VERSION, 'gdpr_cookie');
        if (GDPR_COOKIE_VERSION < 102) {
            //add new option in table
            if (!GdprCookie::newInstance()->this_column_exists('t_gdpr', 'download_data')) {
                GdprCookie::newInstance()->import('gdpr_cookie/database/101.sql');
            }
        }
        if (GDPR_COOKIE_VERSION < 103) {
            //add new option in table
            if (!GdprCookie::newInstance()->this_column_exists('t_gdpr', 'req_date')) {
                GdprCookie::newInstance()->import('gdpr_cookie/database/102.sql');
            }
        }

        //run the future updates
    }
}

/*
 * forms checks
 */

function gdpr_cookie_pre_add($aItem = null, $flash_error = null) {
    $active = osc_get_preference('ag_post_add', 'gdpr_cookie');
    if ($active == 1) {
        $checkbox = Params::getParam('gdpr_checkbox_forms');
        $link = osc_get_preference('privacy_link', 'gdpr_cookie');
        if (empty($checkbox)) {
            $error = sprintf(__('You have to agree with site <a class="gdpr-warning-flash-link" href="%s">terms and data collection</a> if you want to continue', "gdpr_cookie"), $link);
            osc_add_flash_error_message($error);
            osc_redirect_to(osc_item_post_url());
            exit();
        }
    }
}

osc_add_hook('pre_item_add', 'gdpr_cookie_pre_add');

function gdpr_cookie_pre_edit($aItem = null, $flash_error = null) {
    $active = osc_get_preference('ag_edit_add', 'gdpr_cookie');
    if ($active == 1) {
        $checkbox = Params::getParam('gdpr_checkbox_forms');
        $link = osc_get_preference('privacy_link', 'gdpr_cookie');
        if (empty($checkbox)) {
            $error = sprintf(__('You have to agree with site <a class="gdpr-warning-flash-link" href="%s">terms and data collection</a> if you want to continue', "gdpr_cookie"), $link);
            osc_add_flash_error_message($error);
            osc_redirect_to(osc_item_edit_url());
            exit();
        }
    }
}

osc_add_hook('pre_item_edit', 'gdpr_cookie_pre_edit');

function gdpr_cookie_pre_register() {
    $active = osc_get_preference('ag_register_user', 'gdpr_cookie');
    if ($active == 1) {
        $checkbox = Params::getParam('gdpr_checkbox_forms');
        $link = osc_get_preference('privacy_link', 'gdpr_cookie');
        if (empty($checkbox)) {
            $error = sprintf(__('You have to agree with site <a class="gdpr-warning-flash-link" href="%s">terms and data collection</a> if you want to continue', "gdpr_cookie"), $link);
            osc_add_flash_error_message($error);
            osc_redirect_to(osc_register_account_url());
            exit();
        }
    }
}

osc_add_hook('before_user_register', 'gdpr_cookie_pre_register');

function gdpr_cookie_pre_site_contact($params = null) {
    $active = osc_get_preference('ag_contact', 'gdpr_cookie');
    if ($active == 1) {
        $checkbox = Params::getParam('gdpr_checkbox_forms');
        $link = osc_get_preference('privacy_link', 'gdpr_cookie');
        if (empty($checkbox)) {
            $error = sprintf(__('You have to agree with site <a class="gdpr-warning-flash-link" href="%s">terms and data collection</a> if you want to continue', "gdpr_cookie"), $link);
            osc_add_flash_error_message($error);
            osc_redirect_to(osc_contact_url());
            exit();
        }
    }
}

osc_add_hook('pre_contact_post', 'gdpr_cookie_pre_site_contact');

function gdpr_cookie_pre_contact_seller($item = null) {
    $active = osc_get_preference('ag_contact_seller', 'gdpr_cookie');
    if ($active == 1) {
        $checkbox = Params::getParam('gdpr_checkbox_forms');
        $link = osc_get_preference('privacy_link', 'gdpr_cookie');
        if (empty($checkbox)) {
            $error = sprintf(__('You have to agree with site <a class="gdpr-warning-flash-link" href="%s">terms and data collection</a> if you want to continue', "gdpr_cookie"), $link);
            osc_add_flash_error_message($error);
            osc_redirect_to(osc_item_url());
            exit();
        }
    }
}

osc_add_hook('pre_item_contact_post', 'gdpr_cookie_pre_contact_seller');

function gdpr_cookie_pre_contact_pub_profile() {
    $action = Params::getParam('action');
    switch ($action) {
        case('contact_post'):
            $active = osc_get_preference('ag_contact_seller', 'gdpr_cookie');
            if ($active == 1) {
                $checkbox = Params::getParam('gdpr_checkbox_forms');
                $link = osc_get_preference('privacy_link', 'gdpr_cookie');
                if (empty($checkbox)) {
                    $error = sprintf(__('You have to agree with site <a class="gdpr-warning-flash-link" href="%s">terms and data collection</a> if you want to continue', "gdpr_cookie"), $link);
                    osc_add_flash_error_message($error);
                    osc_redirect_to(osc_user_public_profile_url(Params::getParam('id')));
                    exit();
                }
            }
            break;
    }
}

osc_add_hook('init_user_non_secure', 'gdpr_cookie_pre_contact_pub_profile');

function gdpr_cookie_pre_comment($comment = null) {
    $active = osc_get_preference('ag_comment', 'gdpr_cookie');
    if ($active == 1) {
        $checkbox = Params::getParam('gdpr_checkbox_forms');
        $link = osc_get_preference('privacy_link', 'gdpr_cookie');
        if (empty($checkbox)) {
            $error = sprintf(__('You have to agree with site <a class="gdpr-warning-flash-link" href="%s">terms and data collection</a> if you want to continue', "gdpr_cookie"), $link);
            osc_add_flash_error_message($error);
            osc_redirect_to(osc_item_url());
            exit();
        }
    }
}

osc_add_hook('before_add_comment', 'gdpr_cookie_pre_comment');

function gdpr_cookie_pre_send_friend($item = null) {
    $active = osc_get_preference('ag_send_friend', 'gdpr_cookie');
    if ($active == 1) {
        $checkbox = Params::getParam('gdpr_checkbox_forms');
        $link = osc_get_preference('privacy_link', 'gdpr_cookie');
        if (empty($checkbox)) {
            $error = sprintf(__('You have to agree with site <a class="gdpr-warning-flash-link" href="%s">terms and data collection</a> if you want to continue', "gdpr_cookie"), $link);
            osc_add_flash_error_message($error);
            osc_redirect_to(osc_item_send_friend_url());
            exit();
        }
    }
}

osc_add_hook('pre_item_send_friend_post', 'gdpr_cookie_pre_send_friend');

function gdpr_cookie_form_checkbox() {
    $link = osc_get_preference('privacy_link', 'gdpr_cookie');
    ?>
    <div class="gdpr_checkbox_div">
        <input type="checkbox" name="gdpr_checkbox_forms" value="1" />
        <?php echo sprintf(__('I read and agree with <a class="gdpr-warning-checkbox" href="%s">terms and data collection</a> about my session on this site', "gdpr_cookie"), $link); ?>
    </div>
    <?php
}

function gdpr_cookie_export_file_content($file, $content, $action = null) {
    switch ($action) {
        case('append'):
            //not now
            break;
        default :
            $fp = fopen($file, "w");
            $fwrite = fwrite($fp, json_encode($content, JSON_PRETTY_PRINT));
            fclose($fp);
            if ($fwrite === false) {
                //error
                return true;
            }
            break;
    }
}

function gdpr_cookie_delete_folder($path) {
    if (is_dir($path) === true) {
        $files = array_diff(scandir($path), array('.', '..'));

        foreach ($files as $file) {
            gdpr_cookie_delete_folder(realpath($path) . '/' . $file);
        }

        return rmdir($path);
    } else if (is_file($path) === true) {
        return unlink($path);
    }

    return false;
}

function gdpr_cookie_export_items($user_id, $upload_path, $start = null) {
    $number_in_file = 10;
    if (is_numeric($start)) {
        $end = $start + $number_in_file;
        $file = substr($start, 0, -1);
    } else {
        $end = $number_in_file;
        $start = 0;
        $file = 0;
    }

    $total_items = Item::newInstance()->countItemTypesByUserID($user_id);
    $items = GdprCookie::newInstance()->get_items($user_id, $start, $end);
    $items = osc_apply_filter('gdpr_export_items', $items);
    $error = gdpr_cookie_export_file_content($upload_path . '/items/' . $file . '.json', $items);
    if (!$error) {
        //export images
        //create the folder
        $image_folder = $upload_path . '/items/images';
        if (!is_dir($image_folder)) {
            if (!@mkdir($image_folder, 0755, true)) {
                $error = 1;
            }
        }
        if (!$error) {
            foreach ($items as $item) {
                $images = ItemResource::newInstance()->getAllResourcesFromItem($item['pk_i_id']);
                if (!empty($images)) {
                    foreach ($images as $img) {
                        $image = 'item_' . $img['fk_i_item_id'] . '_' . $img['pk_i_id'] . '.' . $img['s_extension'];
                        $original_file = osc_base_path() . $img['s_path'] . $img['pk_i_id'] . '_thumbnail.' . $img['s_extension'];
                        $newfile = $image_folder . '/' . $image;
                        //copy the images in folder
                        if (!copy($original_file, $newfile)) {
                            $error = 1;
                        }
                    }
                }
            }
            if ($total_items > $end && !$error) {
                $error = gdpr_cookie_export_items($user_id, $upload_path, $start + $number_in_file);
            }
        }
    }
    return $error;
}

function gdpr_cookie_ZipArchive($source, $destination) {
    // Initialize archive object
    $zip = new ZipArchive();
    $zip->open($destination . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

    // Create recursive directory iterator
    /** @var SplFileInfo[] $files */
    $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        // Skip directories (they would be added automatically)
        if (!$file->isDir()) {
            // Get real and relative path for current file
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen($source) + 1);

            // Add current file to archive
            $zip->addFile($filePath, $relativePath);
        }
    }

    // Zip archive will be created only after closing object
    return $zip->close();
}

function gdpr_cookie_export_status($user_id, $data, $date = null) {
    if (!empty($data)) {
        GdprCookie::newInstance()->export_data_info($user_id, json_encode($data), $date);
    }
}
