<?php 
  osc_goto_first_locale(); 
  
  $mes_counter = del_count_messages(osc_logged_user_id()); 
  $fav_counter = del_count_favorite();
?>
<?php require_once('menu.php'); ?>
<header> 

  <div class="inside">
    <div class="relative1">
    <div id="container">
    <h1 id="text"></h1>
  </div>

      <?php if(function_exists('blg_home_link')) { ?>
        <a class="blog" href="<?php echo blg_home_link(); ?>"><?php _e('Blog', 'delta'); ?></a>
      <?php } ?>

      <?php if(function_exists('bpr_companies_url')) { ?>
        <a class="company" href="<?php echo bpr_companies_url(); ?>"><?php _e('Companies', 'delta'); ?></a>
      <?php } ?>

      <?php if(function_exists('frm_home')) { ?>
        <a class="forum" href="<?php echo frm_home(); ?>"><?php _e('Forum', 'delta'); ?></a>
      <?php } ?>

      <?php if(function_exists('fi_make_favorite')) { ?>
        <a class="favorite" href="<?php echo osc_route_url('favorite-lists'); ?>">
          <span><?php _e('Favorites', 'delta'); ?></span>

          <?php if($fav_counter > 0) { ?>
            <span class="counter mbBg3"><?php echo $fav_counter; ?></span>
          <?php } ?>
        </a>
      <?php } ?>
      
      <?php if(function_exists('im_messages')) { ?>
        <a href="<?php echo osc_route_url('im-threads'); ?>">
          <span><?php _e('Messages', 'delta'); ?></span>
        
          <?php if($mes_counter > 0) { ?>
            <span class="counter mbBg3"><?php echo $mes_counter; ?></span>
          <?php } ?>        
        </a>
      <?php } ?>
      
      <?php if(function_exists('faq_home_link')) { ?>
        <a href="<?php echo faq_home_link(); ?>"><?php _e('FAQ', 'delta'); ?></a>
      <?php } ?>

    </div>
    
  </div>
  

<?php 
  $loc = (osc_get_osclass_location() == '' ? 'home' : osc_get_osclass_location());
  $sec = (osc_get_osclass_section() == '' ? 'default' : osc_get_osclass_section());
?>

<section class="content loc-<?php echo $loc; ?> sec-<?php echo $sec; ?>">

<?php
  if(osc_is_home_page()) { 
    osc_current_web_theme_path('inc.search.php'); 
    //osc_current_web_theme_path('inc.category.php');
  }
?>

</header>
<div class="flash-box">
  <div class="flash-wrap">
    <?php osc_show_flash_message(); ?>
  </div>
</div>


<?php
  osc_show_widgets('header');
  $breadcrumb = osc_breadcrumb('>', false);
  $breadcrumb = str_replace('<span itemprop="title">' . osc_page_title() . '</span>', '<span itemprop="title">' . del_param('website_name') . '</span>', $breadcrumb);
  $breadcrumb = str_replace('<span itemprop="name">' . osc_page_title() . '</span>', '<span itemprop="name">' . del_param('website_name') . '</span>', $breadcrumb);
?>

<?php if($breadcrumb != '') { ?>
  <div id="bread">
    <div class="inside">
      <div class="wrap">
        <?php if(osc_is_ad_page()) { ?>
          <?php $link_array = array('page' => 'search', 'sCategory' => osc_item_category_id(), 'sCountry' => osc_item_country_code(), 'sRegion' => osc_item_region_id(), 'sCity' => osc_item_city_id()); ?>
          <a href="<?php echo osc_search_url($link_array); ?>" class="goback" ><i class="fas fa-arrow-left"></i> <?php _e('Cauta', 'delta'); ?></a>
        <?php } ?>
        
        <div class="bread-text"><?php echo $breadcrumb; ?></div>
        
        <?php if(osc_is_ad_page()) { ?>
          <?php
            $next_link = del_next_prev_item('next', osc_item_category_id(), osc_item_id());
            $prev_link = del_next_prev_item('prev', osc_item_category_id(), osc_item_id());
          ?>
          
          <div class="navlinks">
            <?php if($prev_link !== false) { ?><a href="<?php echo $prev_link; ?>" class="prev"><i class="fas fa-angle-left"></i> <?php _e('Previous', 'delta'); ?></a><?php } ?>
            <?php if($next_link !== false) { ?><a href="<?php echo $next_link; ?>" class="next"><i class="fas fa-angle-right"></i> <?php _e('Next', 'delta'); ?></a><?php } ?>
          </div>
        <?php } else if(osc_get_osclass_location() == 'user' && osc_get_osclass_section() == 'pub_profile') { ?>
          <?php
            $next_link = del_next_prev_user('next', osc_user_id());
            $prev_link = del_next_prev_user('prev', osc_user_id());
          ?>
          
          <div class="navlinks">
            <?php if($prev_link !== false) { ?><a href="<?php echo $prev_link; ?>" class="prev"><i class="fas fa-angle-left"></i> <?php _e('Previous', 'delta'); ?></a><?php } ?>
            <?php if($next_link !== false) { ?><a href="<?php echo $next_link; ?>" class="next"><i class="fas fa-angle-right"></i> <?php _e('Next', 'delta'); ?></a><?php } ?>
          </div>
        <?php } ?>
        
      </div>
    </div>
  </div>
<?php } ?>