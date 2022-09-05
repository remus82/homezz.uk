<?php
/*
  Plugin Name: Invoice Plugin
  Plugin URI: https://osclasspoint.com/osclass-plugins/payments-and-shopping/invoice-osclass-plugin-i93
  Description: Generate invoices for customers, enable automatic invoice generation after payment and sent invoice to customer.
  Version: 1.7.0
  Author: MB Themes
  Author URI: https://osclasspoint.com
  Author Email: info@osclasspoint.com
  Short Name: invoice
  Plugin update URI: invoice
  Support URI: https://forums.osclasspoint.com/invoice-plugin/
  Product Key: vOUF3cgopU8qyNMewkQS
*/

require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'model/ModelINV.php';
require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'email.php';
require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'functions.php';


osc_enqueue_style('inv-user-style', osc_base_url() . 'oc-content/plugins/invoice/css/user.css?v=' . date('YmdHis'));
osc_enqueue_style('tipped', osc_base_url() . 'oc-content/plugins/invoice/css/tipped.css');

osc_register_script('tipped', osc_base_url() . 'oc-content/plugins/invoice/js/tipped.js', 'jquery');
osc_enqueue_script('tipped');


osc_add_route('inv-profile-alt', 'user/invoice/(.+)', 'user/invoice/{pageType}', osc_plugin_folder(__FILE__).'user/profile.php', true, 'inv', 'profile', __('My invoices', 'invoice'));
osc_add_route('inv-profile', 'user/invoice', 'user/invoice', osc_plugin_folder(__FILE__).'user/profile.php', true, 'inv', 'profile', __('My invoices', 'invoice'));



// INSTALL FUNCTION - DEFINE VARIABLES
function inv_call_after_install() {
  osc_set_preference('connect', 'osclass_pay', 'plugin-invoice', 'STRING');
  osc_set_preference('from', '', 'plugin-invoice', 'STRING');
  osc_set_preference('currency', 'USD', 'plugin-invoice', 'STRING');
  osc_set_preference('currency_position', '', 'plugin-invoice', 'STRING');
  osc_set_preference('decimals', 2, 'plugin-invoice', 'INTEGER');
  osc_set_preference('space', 0, 'plugin-invoice', 'INTEGER');
  osc_set_preference('tseparator', '', 'plugin-invoice', 'STRING');
  osc_set_preference('dseparator', '.', 'plugin-invoice', 'STRING');
  osc_set_preference('notes', '', 'plugin-invoice', 'STRING');
  osc_set_preference('terms', '', 'plugin-invoice', 'STRING');
  osc_set_preference('tax', '20', 'plugin-invoice', 'STRING');
  osc_set_preference('auto_mail', 1, 'plugin-invoice', 'INTEGER');
  osc_set_preference('invoice_order', '', 'plugin-invoice', 'INTEGER');
  osc_set_preference('font', 'freeserif', 'plugin-invoice', 'STRING');
  osc_set_preference('validation', 0, 'plugin-invoice', 'INTEGER');
  

  ModelINV::newInstance()->install();
}


function inv_call_after_uninstall() {
  ModelINV::newInstance()->uninstall();
}


// ADMIN MENU
function inv_menu($title = NULL) {
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/invoice/css/admin.css?v=' . date('YmdHis') . '" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/invoice/css/bootstrap-switch.css" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/invoice/css/tipped.css" rel="stylesheet" type="text/css" />';
  echo '<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/invoice/js/admin.js?v=' . date('YmdHis') . '"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/invoice/js/tipped.js"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/invoice/js/bootstrap-switch.js"></script>';


  if( $title == '') { $title = __('Configure', 'invoice'); }

  $text  = '<div class="mb-head">';
  $text .= '<div class="mb-head-left">';
  $text .= '<h1>' . $title . '</h1>';
  $text .= '<h2>Invoice Plugin</h2>';
  $text .= '</div>';
  $text .= '<div class="mb-head-right">';
  $text .= '<ul class="mb-menu">';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=invoice/admin/configure.php"><i class="fa fa-wrench"></i><span>' . __('Configure', 'invoice') . '</span></a></li>';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=invoice/admin/list.php"><i class="fa fa-file-text-o"></i><span>' . __('Invoices', 'invoice') . '</span></a></li>';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=invoice/admin/report.php"><i class="fa fa-file-excel-o"></i><span>' . __('Report', 'invoice') . '</span></a></li>';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=invoice/admin/invoice.php"><i class="fa fa-plus-circle"></i><span>' . __('Create', 'invoice') . '</span></a></li>';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=invoice/admin/profiles.php"><i class="fa fa-users"></i><span>' . __('Profiles', 'invoice') . '</span></a></li>';
  $text .= '</ul>';
  $text .= '</div>';
  $text .= '</div>';

  echo $text;
}



// ADMIN FOOTER
function inv_footer() {
  $pluginInfo = osc_plugin_get_info('invoice/index.php');
  $text  = '<div class="mb-footer">';
  $text .= '<a target="_blank" class="mb-developer" href="https://osclasspoint.com"><img src="https://osclasspoint.com/favicon.ico" alt="OsclassPoint Market" /> OsclassPoint Market</a>';
  $text .= '<a target="_blank" href="' . $pluginInfo['support_uri'] . '"><i class="fa fa-bug"></i> ' . __('Report Bug', 'invoice') . '</a>';
  $text .= '<a target="_blank" href="https://forums.osclasspoint.com/"><i class="fa fa-handshake-o"></i> ' . __('Support Forums', 'invoice') . '</a>';
  $text .= '<a target="_blank" class="mb-last" href="mailto:info@osclasspoint.com"><i class="fa fa-envelope"></i> ' . __('Contact Us', 'invoice') . '</a>';
  $text .= '<span class="mb-version">v' . $pluginInfo['version'] . '</span>';
  $text .= '</div>';

  $text .= '<script>var invDecimals = ' . (inv_param('decimals') > 0 ? inv_param('decimals') : 0) . ';</script>';

  return $text;
}


// CREATE LINK IN USER MENU
function inv_user_sidebar() {
  if(osc_current_web_theme() == 'veronika' || osc_current_web_theme() == 'stela' || osc_current_web_theme() == 'starter' || (defined('USER_MENU_ICONS') && USER_MENU_ICONS == 1) ) {
    echo '<li class="opt_inv_invoices"><a href="' . osc_route_url('inv-profile') . '" ><i class="fa fa-file-pdf-o"></i> ' . __('Invoices', 'invoice') . '</a></li>';
  } else {
    echo '<li class="opt_inv_invoices"><a href="' . osc_route_url('inv-profile') . '" >' . __('Invoices', 'invoice') . '</a></li>';
  }
}

osc_add_hook('user_menu', 'inv_user_sidebar');



// ADD MENU LINK TO PLUGIN LIST
function inv_admin_menu() {
echo '<h3><a href="#">Invoice Plugin</a></h3>
<ul> 
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/configure.php') . '">&raquo; ' . __('Configure', 'invoice') . '</a></li>
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/list.php') . '">&raquo; ' . __('Invoices', 'invoice') . '</a></li>
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/report.php') . '">&raquo; ' . __('Report', 'invoice') . '</a></li>
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/profiles.php') . '">&raquo; ' . __('Profiles', 'invoice') . '</a></li>
</ul>';
}


// ADD MENU TO PLUGINS MENU LIST
osc_add_hook('admin_menu','inv_admin_menu', 1);


// PLUGIN UPDATE
function inv_update_version() {
  ModelINV::newInstance()->versionUpdate();
}


// DISPLAY CONFIGURE LINK IN LIST OF PLUGINS
function inv_conf() {
  osc_admin_render_plugin( osc_plugin_path( dirname(__FILE__) ) . '/admin/configure.php' );
}

osc_add_hook( osc_plugin_path( __FILE__ ) . '_configure', 'inv_conf' );	
osc_register_plugin(osc_plugin_path(__FILE__), 'inv_call_after_install');
osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'inv_call_after_uninstall');
osc_add_hook(osc_plugin_path(__FILE__) . '_enable', 'inv_update_version');

?>