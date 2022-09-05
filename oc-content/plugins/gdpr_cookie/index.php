<?php

/*
 * Copyright (C) 2018 Puiu Calin
 * This program is a commercial software: is forbidden to use this software without licence, 
 * on multiple installations, and by purchasing from other source than those authorized for the sale of software.
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */

/*
  Plugin Name: GDPR AND COOKIE
  Plugin URI: http://osclass.calinbehtuk.ro/
  Description: General Data Protection And Cookie Consent.
  Version: 1.0.2
  Author: Puiu Calin
  Author URI: http://osclass.calinbehtuk.ro/
  Plugin update URI: gdpr-and-cookie
  Short Name: gdpr-and-cookie
 */

define('GDPR_COOKIE_VERSION', '102');

require_once 'include/model.php';
require_once 'include/functions.php';
require_once 'include/hPlugin.php';
require_once 'include/cron.php';
require_once 'include/userTable.php';
require_once 'custom.php';

function gdpr_cookie_install() {
    GdprCookie::newInstance()->install();
    osc_set_preference('version', GDPR_COOKIE_VERSION, 'gdpr_cookie');
}

function gdpr_cookie_uninstall() {
    GdprCookie::newInstance()->uninstall();
}

function gdpr_cookie_admin_menu() {
    osc_admin_menu_plugins(__('Gdpr Cookie', 'gdpr_cookie'), osc_admin_render_plugin_url('gdpr_cookie/admin/settings.php'), 'gdpr_cookie');
}

function gdpr_cookie_admin_css() {
    osc_enqueue_style('gdpr_cookie_admin_css', osc_base_url() . 'oc-content/plugins/gdpr_cookie/css/admin.css');
}

function gdpr_cookie_load_style() {
    $route = Params::getParam('route');
    switch ($route) {
        case('user_data'):
            osc_enqueue_style('gdpr_cookie_user_dash', osc_base_url() . 'oc-content/plugins/gdpr_cookie/css/user_dash.css');
            break;
    }
}

osc_add_hook('init_custom', 'gdpr_cookie_load_style');
if (osc_plugin_is_enabled('gdpr_cookie/index.php')) {
    gdpr_cookie_update_plugin();
}

function gdpr_cookie_users_data_menu() {
    echo '<li class="gdpr_cookie_link"><a href="' . osc_route_url('user_data') . '" >' . __("Your data", "gdpr_cookie") . '</a></li>';
}


osc_add_route('user_data', 'user_data', 'user_data', osc_plugin_folder(__FILE__) . 'include/controller/user.php', true);
osc_add_hook('user_menu', 'gdpr_cookie_users_data_menu');

osc_add_hook('admin_header', 'gdpr_cookie_admin_css');
osc_add_hook('admin_menu_init', 'gdpr_cookie_admin_menu');
osc_enqueue_style('gdpr_cookie', osc_base_url() . 'oc-content/plugins/gdpr_cookie/css/style.css');
osc_register_plugin(osc_plugin_path(__FILE__), 'gdpr_cookie_install');
osc_add_hook(osc_plugin_path(__FILE__) . "_uninstall", 'gdpr_cookie_uninstall');