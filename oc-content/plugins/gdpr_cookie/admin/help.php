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
?>
<div class="gdpr_cookie_help">
    <h2>
        <?php _e('Help', 'gdpr_cookie'); ?>
        <a class="gdpr_menu_link" href="<?php echo osc_admin_render_plugin_url('gdpr_cookie/admin/settings.php'); ?>"><?php _e('Settings', 'gdpr_cookie'); ?></a>
    </h2>
    <h3>
        <?php _e('This plugin has some basic option for google analytics,google adsense and other type of affiliate programs that will collect information about your users. If you want more extension for this plugin you can contact us to implement some extra option that will fit with your site, but this extra custom work will not be free.', 'gdpr_cookie'); ?>
        <a target="_blank" href="https://osclass.calinbehtuk.ro/contact">Contact</a>
    </h3>
    <?php _e('The plugin has minimal option that you can use to restrict what personal data you collect and to accept use of cookie. The user will have option to select from 3 options Google Analytics, Ads from adsense or other advertise if this ads colect information about visitors and a custom option that you can use for other type of data.', 'gdpr_cookie'); ?><br />
    <?php _e('Google Analytics, ads and custom option will be loaded only after user will accept this, if the accept is not provide this data will not be loaded.', 'gdpr_cookie'); ?><br />
    <?php _e('The site needs to have a privacy policy page where you will explain to users how the data is collected and how it stored, by you or by third-party.', 'gdpr_cookie'); ?><br />
    <br />
    <br />
    <strong><?php _e('The widget for accepting cookie and set preferences will be display on bottom of the page on the first visit, if the user will agree the widget will be hidden. To display the options again you have to include in your theme a link  in you desired area. When the user will click on this link the widget with option will be displayed so the user can review the preferences.', 'gdpr_cookie'); ?></strong><br />
    <br />
    <br />
    <?php _e('This function will display a link', 'gdpr_cookie'); ?>
    <pre>
        &lt;?php gdpr_cookie_settings_link() ?&gt;
    </pre>
    <br />
    <br />
    <?php _e('If you want to implement this option in your theme on your buttons you can use this class. The class has to be in you div or link.', 'gdpr_cookie'); ?>
    <br/>
    <br/>
    <strong style="color:red">gdpr_cookie_settings</strong>
    <br/>
    <br/>
    <strong><?php _e('For Google Analytics', 'gdpr_cookie'); ?></strong><br />
    <br />
    <?php _e('Add the tracking id in plugin settings and the script will be loaded after user will accept this option', 'gdpr_cookie'); ?>
    <br />
    <br />
    <strong><?php _e('For ads like Adsense or other type', 'gdpr_cookie'); ?></strong><br />
    <br />
    <?php _e('You have to use a function to create a condition, and only if the user accept this option to display this content. All you code need to be inside this condition.', 'gdpr_cookie'); ?>
    <br />
    <br />
    <?php _e('Function to use', 'gdpr_cookie'); ?>:
    <pre>
    h_gdpr_active_ads()
    </pre>

    <?php _e('Example', 'gdpr_cookie'); ?>:
    <pre>
        &lt;?php if (h_gdpr_active_ads()) { ?&gt;
<br />
        <?php _e('your code', 'gdpr_cookie'); ?>
<br />
        &lt;?php } ?&gt;
    </pre>
    <strong><?php _e('Custom option', 'gdpr_cookie'); ?></strong><br />
    <br />
    <?php _e('You can use this option for any other situation when you need to request user permission to store and share his data, you have option to add a custom name for this in settings. For this option your code need to be inside of this condition.', 'gdpr_cookie'); ?>
    <br />
    <br />
    <?php _e('Function to use', 'gdpr_cookie'); ?>:
    <pre>
    h_gdpr_active_custom()
    </pre>
    <?php _e('Example', 'gdpr_cookie'); ?>:
    <pre>
        &lt;?php if (h_gdpr_active_custom()) { ?&gt;
<br />
        <?php _e('your code', 'gdpr_cookie'); ?>
<br />
        &lt;?php } ?&gt;
    </pre>
    <strong><?php _e('Include the checkbox line in forms', 'gdpr_cookie'); ?></strong>
    <pre>
        <i><?php _e('Bender form example', 'gdpr_cookie'); ?></i>
<br />
            &lt;div class=&quot;control-group&quot;&gt;
                &lt;label class=&quot;control-label&quot;&gt;&lt;?php _e('Gdpr', 'bender'); ?&gt;&lt;/label&gt;
                &lt;div class=&quot;controls&quot;&gt;
                    <strong style="color:red;">&lt;?php gdpr_cookie_form_checkbox() ?&gt;</strong>
                &lt;/div&gt;
            &lt;/div&gt;
    </pre>
    <strong><?php _e('Exports data', 'gdpr_cookie'); ?></strong>
    <br />
    <?php _e('You can use some extra option to include your data from plugins or from other tables', 'gdpr_cookie'); ?>
    <br />
    <?php _e('File custom.php contains some examples that you can use to add extra data in archive. You need to add your code in this functions and the data will be included in the archive.', 'gdpr_cookie'); ?>
    <br />
    <?php _e('Uncomment the lines from custom.php for test and make sure that you do this on a test site.', 'gdpr_cookie'); ?>
    <br />
    <br />
    <pre>
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
    </pre>
    <br />
    <br />
</div>
<center><iframe width="560" height="315" src="https://www.youtube.com/embed/a0oP7lEd0Uo" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></center>
<div class="cbk_author">
    <?php gdpr_cookie_autor_rights(); ?>
</div>