<?php
/*
Plugin Name: Scrolltop
Plugin URI: http://www.osclass.org/
Description: Remonter en haut de page
Version: 1.0
Author: Georges ARMEDE
Author URI: http://www.osclass.org/
Short Name: Scrolltop
*/

    function scrolltop() {
      echo '<div id="back-top-right"><a href="#"><span class="round3"><i class="fa fa-arrow-circle-up"></i></span></a></div>';
    }
    
    function scrolltop_header() {
      osc_register_script('scrolltop', osc_base_url() . 'oc-content/plugins/scrolltop/js/scrolltop.js', 'jquery');
      osc_enqueue_script('scrolltop');
        
      echo '<link href="' . osc_base_url() . 'oc-content/plugins/scrolltop/css/scrolltop.css" rel="stylesheet" type="text/css" />';
        }
		
    function scrolltop_admin_menu() {
    echo '<h3><a href="#">Scrolltop</a></h3>
    <ul>
        <li><a href="'.osc_admin_render_plugin_url("scrolltop/help.php").'?section=types">&raquo; ' . __('F.A.Q. / Aide', 'scrolltop') . '</a></li>
    </ul>';
    }		


    /**
     *  HOOKS
     */
    osc_register_plugin(osc_plugin_path(__FILE__), '');
    osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', '');
	
	osc_add_hook('after_error_page', 'scrolltop');
    osc_add_hook('header', 'scrolltop_header');
	osc_add_hook('admin_menu', 'scrolltop_admin_menu');
    
?>
