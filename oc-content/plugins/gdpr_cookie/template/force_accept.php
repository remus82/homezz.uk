<?php
if ((!defined('ABS_PATH')))
    exit('ABS_PATH is not loaded. Direct access is not allowed.');
/*
 * Copyright (C) 2018 Puiu Calin
 * This program is a commercial software: is forbidden to use this software without licence, 
 * on multiple installations, and by purchasing from other source than those authorized for the sale of software.
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */
?>
<div class="cover-gdpr-cookie">
    <div class="center-content">
        <span></span>
        <div class="in-content">
            <?php _e('Hi! Please select and save the preferences listed on bottom to continue to use our site.', 'gdpr_cookie'); ?>
        </div>
        <div class="show-settings-gdpr"><?php _e('See', 'gdpr_cookie'); ?></div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('.show-settings-gdpr').click(function(){
            $('.gdp_cookie_widget .hide_block_w').slideToggle();
        });
    });
</script>