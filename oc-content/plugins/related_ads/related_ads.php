<div id="related_ads">
  <h2><i class="fa fa-yelp"></i><?php _e('Related Ads', 'related_ads'); ?></h2>
  <div class="del"></div>

  <?php if( osc_count_items() == 0) { ?>
    <p class="empty"><?php _e('No Related Ads', 'related_ads'); ?></p>
  <?php } else { ?>
    <?php while ( osc_has_items() ) { ?>
      <div class="box">
        <?php if( osc_images_enabled_at_items() ) { ?>
          <div class="photo">
            <a href="<?php echo osc_item_url(); ?>">
              <div class="price"><?php if( osc_price_enabled_at_items() ) { echo osc_item_formated_price(); ?><?php } ?></div>
              <div class="title"><?php echo osc_item_title(); ?></div>

              <?php if( osc_count_item_resources() ) { ?>
                <img src="<?php echo osc_resource_thumbnail_url(); ?>" width="141" height="105" title="<?php echo osc_item_title(); ?>" alt="<?php echo osc_item_title(); ?>" />
              <?php } else { ?>
                <img src="<?php echo osc_current_web_theme_url('images/no_photo.gif'); ?>" alt="" width="141" height="105" title="" />
              <?php } ?>
            </a>
          </div>
        <?php } ?>
      </div>
    <?php } ?>
  <?php } ?>
</div>