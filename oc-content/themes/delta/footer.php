</section>

<footer>
  <div class="inside">

    <?php if(del_param('site_phone') != '' || del_param('site_email') != '') { ?>
      <div class="line1">
        <?php if(del_param('site_phone') != '') { ?>
          <div class="one">
            <span><?php _e('Suport tehnic', 'delta'); ?>:</span><strong><?php echo del_param('site_phone'); ?></strong>
          </div>
        <?php } ?>
        
        <?php if(del_param('site_phone') != '' && del_param('site_email') != '') { ?>
          <div class="one del">|</div>
        <?php } ?>

        <?php if(del_param('site_email') != '') { ?>
          <div class="one">
            <span><?php _e('Email', 'delta'); ?>:</span><strong><?php echo del_param('site_email'); ?></strong>
          </div>
        <?php } ?>        
      </div>
    <?php } ?>
    
    <div class="line2">
      <div class="box b1">
        <h4><?php _e('Popular categories', 'delta'); ?></h4>
        
        <?php 
          osc_goto_first_category();
          $i = 1;
        ?>

        <ul>
          <?php while(osc_has_categories()) { ?>
            <?php if($i <= 10) { ?>
              <li><a href="<?php echo osc_search_url(array('page' => 'search', 'sCategory' => osc_category_id())); ?>"><?php echo osc_category_name();?></a></li>
            <?php } ?>

            <?php $i++; ?>
          <?php } ?>
        </ul>
      </div>

      <div class="box b2">
      <h4><?php _e('Partners', 'delta'); ?></h4>


        <ul>
          <li><a href="https://newads.uk/">Newads.u - your portal to all categories of listings.</a></li>
        </ul>
            </br>
        <h4><?php _e('Popular locations', 'delta'); ?></h4>
        
        <?php 
          $regions = RegionStats::newInstance()->listRegions('%%%%', '>', 'i_num_items DESC'); 
          $i = 1;
        ?>

        <ul>
          <?php if(is_array($regions) && count($regions) > 0) { ?>
            <?php foreach($regions as $r) { ?>
              <?php if($i <= 10) { ?>
                <li><a href="<?php echo osc_search_url(array('page' => 'search', 'sRegion' => $r['pk_i_id']));?>"><?php echo $r['s_name']; ?></a></li>
                <?php $i++; ?>
              <?php } ?>
            <?php } ?>
          <?php } ?>
        </ul>
      </div>
      
      <div class="box b3">
        <h4><?php _e('Help & support', 'delta'); ?></h4>
        
        <ul>
          <li><a href="<?php echo osc_contact_url(); ?>"><?php _e('Contact', 'delta'); ?></a></li>
          
          <?php if(osc_is_web_user_logged_in()) { ?>
            <li><a href="<?php echo osc_user_dashboard_url(); ?>"><?php _e('My Profile', 'delta'); ?></a></li>
          <?php } else { ?>
            <li><a href="<?php echo del_reg_url('register'); ?>"><?php _e('Register', 'delta'); ?></a></li>
          <?php } ?>
          
          <?php if(del_param('footer_link')) { ?>
            <li><a href="#"> </a></li>
          <?php } ?> 
      
          <?php 
            $pages = Page::newInstance()->listAll($indelible = 0, $b_link = 1, $locale = null, $start = null, $limit = 10); 
            $i = 1;
          ?>

          <?php if(is_array($pages) && count($pages) > 0) { ?>
            <?php foreach($pages as $p) { ?>
              <?php if($i <= 10) { ?>
                <?php View::newInstance()->_exportVariableToView('page', $p); ?>
                <li><a href="<?php echo osc_static_page_url(); ?>"><?php echo osc_static_page_title();?></a></li>
                <?php $i++; ?>
              <?php } ?>
            <?php } ?>
          <?php } ?>
        </ul>
      </div>
    </div>
    

      <div class="box b3 share">
        <h4><?php _e('Social Media', 'delta'); ?></h4>
        
        <?php
          osc_reset_resources();

          if(osc_is_ad_page()) {
            $share_url = osc_item_url();
          } else {
            $share_url = osc_base_url();
          }

          $share_url = urlencode($share_url);
        ?>
        
        <ul>
          <li class="whatsapp"><a href="whatsapp://send?text=<?php echo $share_url; ?>" data-action="share/whatsapp/share"><i class="fab fa-whatsapp"></i></a></li>
          <li class="facebook"><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" title="<?php echo osc_esc_html(__('Share us on Facebook', 'veronika')); ?>" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
          <li class="pinterest"><a href="https://pinterest.com/pin/create/button/?url=<?php echo $share_url; ?>&media=<?php echo osc_base_url(); ?>oc-content/themes/<?php echo osc_current_web_theme(); ?>/images/logo.jpg&description=" title="<?php echo osc_esc_html(__('Share us on Pinterest', 'veronika')); ?>" target="_blank"><i class="fab fa-pinterest-p"></i></a></li>
          <li class="twitter"><a href="https://twitter.com/home?status=<?php echo $share_url; ?>%20-%20<?php _e('your', 'veronika'); ?>%20<?php _e('classifieds', 'veronika'); ?>" title="<?php echo osc_esc_html(__('Tweet us', 'veronika')); ?>" target="_blank"><i class="fab fa-twitter"></i></a></li>
          <li class="linkedin"><a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $share_url; ?>&title=<?php echo osc_esc_html(__('My', 'veronika')); ?>%20<?php echo osc_esc_html(__('classifieds', 'veronika')); ?>&summary=&source=" title="<?php echo osc_esc_html(__('Share us on LinkedIn', 'veronika')); ?>" target="_blank"><i class="fab fa-linkedin"></i></a></li>
        </ul>
      </div>
    </div>
    
    <div class="footer-hook"><?php osc_run_hook('footer'); ?></div>
    <div class="footer-widgets"><?php osc_show_widgets('footer'); ?></div>
  </div>
  
  <div class="inside copyright">
    <?php _e('Copyright', 'delta'); ?> &copy; <?php echo date('Y'); ?> <?php echo del_param('website_name'); ?> <?php _e('drepturi rezervate', 'delta'); ?>.
   
  </div>
</footer>
 

<?php if(del_banner('body_left') !== false) { ?>
  <div id="body-banner" class="bleft">
    <?php echo del_banner('body_left'); ?>
  </div>
<?php } ?>

<?php if(del_banner('body_right') !== false) { ?>
  <div id="body-banner" class="bright">
    <?php echo del_banner('body_right'); ?>
  </div>
<?php } ?>


<?php if(del_param('scrolltop') == 1) { ?>
  <a id="scroll-to-top"><img src="<?php echo osc_current_web_theme_url('images/scroll-to-top.png'); ?>"/></a>
<?php } ?>


<?php if ( OSC_DEBUG || OSC_DEBUG_DB ) { ?>
  <div id="debug-mode" class="noselect"><?php _e('You have enabled DEBUG MODE, autocomplete for locations and items will not work! Disable it in your config.php.', 'delta'); ?></div>
<?php } ?>


<!---MOBILE BLOCKS --->
<div id="menu-cover" class="mobile-box"></div>


<div id="menu-options" class="mobile-box">
  <div class="head <?php if(osc_is_web_user_logged_in()) { ?>logged<?php } ?>">
    <?php if(!osc_is_web_user_logged_in()) { ?>
      <strong><?php _e('Welcome!', 'delta'); ?></strong>
    <?php } else { ?>
      <div class="image">
        <img src="<?php echo del_profile_picture(osc_logged_user_id(), 'small'); ?>" />
      </div>
      
      <strong><?php echo sprintf(__('Hi %s', 'delta'), osc_logged_user_name()); ?></strong>
    <?php } ?>
    
    <a href="#" class="close">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" width="30px" height="30px"><path fill="currentColor" d="M193.94 256L296.5 153.44l21.15-21.15c3.12-3.12 3.12-8.19 0-11.31l-22.63-22.63c-3.12-3.12-8.19-3.12-11.31 0L160 222.06 36.29 98.34c-3.12-3.12-8.19-3.12-11.31 0L2.34 120.97c-3.12 3.12-3.12 8.19 0 11.31L126.06 256 2.34 379.71c-3.12 3.12-3.12 8.19 0 11.31l22.63 22.63c3.12 3.12 8.19 3.12 11.31 0L160 289.94 262.56 392.5l21.15 21.15c3.12 3.12 8.19 3.12 11.31 0l22.63-22.63c3.12-3.12 3.12-8.19 0-11.31L193.94 256z" class=""></path></svg>
    </a>
  </div>
  
  <div class="body">
    <a href="<?php echo osc_user_list_items_url(); ?>"><i class="fab fa-stack-overflow"></i> <?php _e('Anunturile Mele', 'delta'); ?></a>
    <a href="<?php echo osc_user_profile_url(); ?>"><i class="fas fa-user-cog"></i> <?php _e('Profil', 'delta'); ?></a>
    <a href="<?php echo osc_user_alerts_url(); ?>"><i class="fas fa-check-double"></i> <?php _e('Anunturi Salvate', 'delta'); ?></a>

    <?php if(function_exists('fi_make_favorite')) { ?>
      <a href="<?php echo osc_route_url('favorite-lists'); ?>"><i class="far fa-heart"></i> <?php _e('Anunturi Favorite', 'delta'); ?></a>
    <?php } ?>

    <?php if(function_exists('im_messages')) { ?>
      <a href="<?php echo osc_route_url('im-threads'); ?>"><i class="far fa-comment-alt"></i> <?php _e('Mesaje', 'delta'); ?></a>
    <?php } ?>

    <?php if(function_exists('osp_user_sidebar')) { ?>
      <a href="<?php echo osc_route_url('osp-item'); ?>"><i class="fas fa-award"></i> <?php _e('Anunturi Promovate', 'delta'); ?></a>
    <?php } ?>
    
    <?php if(osc_is_web_user_logged_in()) { ?>
      <a href="<?php echo osc_user_public_profile_url(osc_logged_user_id()); ?>"><i class="far fa-address-card"></i> <?php _e('Profil Public ', 'delta'); ?></a>
    <?php } ?>
  </div>

  <div class="foot">
    <?php if(!osc_is_web_user_logged_in()) { ?>
      <a href="<?php echo osc_user_login_url(); ?>" class="btn mbBg3"><?php _e('Login', 'delta'); ?></a>
      <div class="row">
        <span><?php _e('Nu ai cont inca?', 'delta'); ?></span>
        <a href="<?php echo osc_register_account_url(); ?>"><?php _e('Inregistreaza-te', 'delta'); ?></a>
      </div>
    <?php } else { ?>
      <a class="logout btn mbBg3" href="<?php echo osc_user_logout_url(); ?>" class="btn mbBg3"><?php _e('Logout', 'delta'); ?></a>
    <?php } ?>

  </div>
</div>

<div id="menu-user" class="mobile-box">
  <div class="body">
    <?php echo del_user_menu(); ?>
  </div>
</div>

<div id="overlay" class="black"></div>

<?php if(del_is_demo()) { ?>
  <div id="showcase-box" class="isDesktop isTablet">
    <a target="_blank" href="<?php echo osc_admin_render_theme_url('oc-content/themes/delta/admin/configure.php'); ?>"><em><?php _e('Du-te la', 'delta'); ?></em> <strong><?php _e('OC-Admin', 'delta'); ?></strong></a>
    <a href="#" class="show-banners"><em><?php _e('Show', 'delta'); ?></em> <strong><?php _e('Banere', 'delta'); ?></strong></a>
  </div>
<?php } ?>


<style>
.loc-picker .region-tab:empty:after {content:"<?php echo osc_esc_html(__('Selecteaza tara', 'delta')); ?>";}
.loc-picker .city-tab:empty:after {content:"<?php echo osc_esc_html(__('Selecteaza regiunea ', 'delta')); ?>";}
.cat-picker .wrapper:after {content:"<?php echo osc_esc_html(__('Selecteaza categoria apoi, subcategoria', 'delta')); ?>";}
a.fi_img-link.fi-no-image > img {content:url("<?php echo osc_base_url(); ?>/oc-content/themes/delta/images/no-image.png");}
</style>


<?php if(Params::getParam('type') != 'itemviewer') { ?>
<script>
  $(document).ready(function(){

    // JAVASCRIPT AJAX LOADER FOR LOCATIONS 
    var termClicked = false;
    var currentCountry = "<?php echo del_ajax_country(); ?>";
    var currentRegion = "<?php echo del_ajax_region(); ?>";
    var currentCity = "<?php echo del_ajax_city(); ?>";
  

    // Create delay
    var delay = (function(){
      var timer = 0;
      return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
      };
    })();


    $(document).ajaxSend(function(evt, request, settings) {
      var url = settings.url;

      if (url.indexOf("ajaxLoc") >= 0) {
        $(".loc-picker, .location-picker").addClass('searching');
      }
    });

    $(document).ajaxStop(function() {
      $(".loc-picker, .location-picker").removeClass('searching');
    });



    $('body').on('keyup', '.loc-picker .term', function(e) {

      delay(function(){
        var min_length = 1;
        var elem = $(e.target);
        var term = encodeURIComponent(elem.val());

        // If comma entered, remove characters after comma including
        if(term.indexOf(',') > 1) {
          term = term.substr(0, term.indexOf(','));
        }

        // If comma entered, remove characters after - including (because city is shown in format City - Region)
        if(term.indexOf(' - ') > 1) {
          term = term.substr(0, term.indexOf(' - '));
        }

        var block = elem.closest('.loc-picker');
        var shower = elem.closest('.loc-picker').find('.shower');

        shower.html('');

        if(term != '' && term.length >= min_length) {
          // Combined ajax for country, region & city
          $.ajax({
            type: "POST",
            url: baseAjaxUrl + "&ajaxLoc=1&term=" + term,
            dataType: 'json',
            success: function(data) {
              var length = data.length;
              var result = '';
              var result_first = '';
              var countCountry = 0;
              var countRegion = 0;
              var countCity = 0;


              if(shower.find('.service.min-char').length <= 0) {
                for(key in data) {

                  // Prepare location IDs
                  var id = '';
                  var country_code = '';
                  if( data[key].country_code ) {
                    country_code = data[key].country_code;
                    id = country_code;
                  }

                  var region_id = '';
                  if( data[key].region_id ) {
                    region_id = data[key].region_id;
                    id = region_id;
                  }

                  var city_id = '';
                  if( data[key].city_id ) {
                    city_id = data[key].city_id;
                    id = city_id;
                  }
                    

                  // Count cities, regions & countries
                  if (data[key].type == 'city') {
                    countCity = countCity + 1;
                  } else if (data[key].type == 'region') {
                    countRegion = countRegion + 1;
                  } else if (data[key].type == 'country') {
                    countCountry = countCountry + 1;
                  }


                  // Find currently selected element
                  var selectedClass = '';
                  if( 
                    data[key].type == 'country' && parseInt(currentCountry) == parseInt(data[key].country_code) 
                    || data[key].type == 'region' && parseInt(currentRegion) == parseInt(data[key].region_id) 
                    || data[key].type == 'city' && parseInt(currentCity) == parseInt(data[key].city_id) 
                  ) { 
                    selectedClass = ' selected'; 
                  }


                  // For cities, get region name
                  var nameTop = data[key].name_top;

                  if(nameTop != '' && nameTop != 'null' && nameTop !== null && nameTop !== undefined) {
                    nameTop = nameTop.replace(/'/g, '');
                  } else {
                    nameTop = '';
                  }

                  if(data[key].type != 'city_more') {

                    // When classic city, region or country in loop and same does not already exists
                    if(shower.find('div[data-code="' + data[key].type + data[key].id + '"]').length <= 0) {
                      result += '<div class="option ' + data[key].type + selectedClass + '" data-country="' + country_code + '" data-region="' + region_id + '" data-city="' + city_id + '" data-code="' + data[key].type + id + '" id="' + id + '" title="' + nameTop + '"><strong>' + data[key].name + '</strong></div>';
                    }
                  }
                }


                // No city, region or country found
                if( countCity == 0 && countRegion == 0 && countCountry == 0 && shower.find('.empty-loc').length <= 0 && shower.find('.service.min-char').length <= 0) {
                  shower.find('.option').remove();
                  result_first += '<div class="option service empty-pick empty-loc"><?php echo osc_esc_js(__('Nici o locatie nu se potriveste', 'delta')); ?></div>';
                }
              }

              shower.html(result_first + result);
            }
          });

        } else {
          // Term is not length enough, show default content
          //shower.html('<div class="option service min-char"><?php echo osc_esc_js(__('Introdu cel putin', 'delta')); ?> ' + (min_length - term.length) + ' <?php echo osc_esc_js(__('more letter(s)', 'delta')); ?></div>');

          shower.html('<?php echo osc_esc_js(del_def_location()); ?>');
        }
      }, 500 );
    });
  });
</script>
<?php } ?>
