<?php if(1==2 && osc_is_search_page()) { ?>
  <div id="search-bar" class="mbBg">
    <div class="inside">

      <form action="<?php echo osc_base_url(true); ?>" method="GET" class="nocsrf" id="search-form" >
        <input type="hidden" name="page" value="search" />
        <input type="hidden" name="sCountry" id="sCountry" value="<?php echo Params::getParam('sCountry'); ?>"/>
        <input type="hidden" name="sRegion" id="sRegion" value="<?php echo Params::getParam('sRegion'); ?>"/>
        <input type="hidden" name="sCity" id="sCity" value="<?php echo Params::getParam('sCity'); ?>"/>

        <input type="hidden" name="sOrder" value="<?php echo osc_search_order(); ?>" />
        <input type="hidden" name="iOrderType" value="<?php $allowedTypesForSorting = Search::getAllowedTypesForSorting(); echo isset($allowedTypesForSorting[osc_search_order_type()]) ? $allowedTypesForSorting[osc_search_order_type()] : ''; ?>" />
        <input type="hidden" name="sCompany" class="sCompany" id="sCompany" value="<?php echo Params::getParam('sCompany');?>" />
        <input type="hidden" name="sShowAs" id="sShowAs" value="<?php echo Params::getParam('sShowAs'); ?>"/>
        <input type="hidden" name="showMore" id="showMore" value="<?php echo Params::getParam('showMore'); ?>"/>
        <input type="hidden" name="sCategory" value="<?php echo Params::getParam('sCategory'); ?>"/>
        <input type="hidden" name="userId" value="<?php echo Params::getParam('userId'); ?>"/>


        <div class="w1">
          <div id="query-picker" class="query-picker">
            <svg viewBox="0 0 32 32" color="#696766" height="16" width="16"><defs><path id="mbIconSearch" d="M12.618 23.318c-6.9 0-10.7-3.8-10.7-10.7 0-6.9 3.8-10.7 10.7-10.7 6.9 0 10.7 3.8 10.7 10.7 0 3.458-.923 6.134-2.745 7.955-1.821 1.822-4.497 2.745-7.955 2.745zm17.491 5.726l-7.677-7.678c1.854-2.155 2.804-5.087 2.804-8.748C25.236 4.6 20.636 0 12.618 0S0 4.6 0 12.618c0 8.019 4.6 12.618 12.618 12.618 3.485 0 6.317-.85 8.44-2.531l7.696 7.695 1.355-1.356z"></path></defs><use fill="currentColor" xlink:href="#mbIconSearch" fill-rule="evenodd"></use></svg>
            <input type="text" name="sPattern" class="pattern" placeholder="<?php _e('Search keywords', 'delta'); ?>" value="<?php echo Params::getParam('sPattern'); ?>" autocomplete="off"/>

            <div class="shower-wrap">
              <div class="shower"></div>
            </div>

            <div class="loader"></div>
          </div>
        </div>


        <div class="w2">
          <div id="location-picker" class="loc-picker ctr-<?php echo (del_count_countries() == 1 ? 'one' : 'more'); ?>">
            <div class="mini-box">
              <input type="text" id="term" class="term" placeholder="<?php _e('All over', 'delta'); ?>" value="<?php echo del_get_term(Params::getParam('term'), Params::getParam('sCountry'), Params::getParam('sRegion'), Params::getParam('sCity')); ?>" autocomplete="off" />
              <svg class="svg-left" viewBox="0 0 32 32" color="#696766" width="16px" height="16px"><defs><path id="mbIconMarker" d="M13.457 0c7.918 0 12.457 4.541 12.457 12.457C25.915 19.928 17.53 32 13.457 32 9.168 32 1 20.317 1 12.457 1 4.541 5.541 0 13.457 0zm0 30c2.44 0 10.457-10.658 10.457-17.543C23.915 5.616 20.299 2 13.457 2 6.617 2 3 5.616 3 12.457 3 19.649 10.802 30 13.457 30zm0-13.309a4.38 4.38 0 01-4.375-4.375 4.38 4.38 0 014.375-4.376 4.38 4.38 0 014.375 4.376 4.38 4.38 0 01-4.375 4.375zm0-10.75a6.382 6.382 0 00-6.375 6.375 6.382 6.382 0 006.375 6.375 6.382 6.382 0 006.375-6.375 6.382 6.382 0 00-6.375-6.376"></path></defs><use fill="currentColor" xlink:href="#mbIconMarker" fill-rule="evenodd" transform="translate(3)"></use></svg>
              <svg class="svg-right" viewBox="0 0 32 32" color="#696766" width="20px" height="20px"><defs><path id="mbIconAngle" d="M12.147 25.2c-.462 0-.926-.185-1.285-.556L.57 14.024A2.05 2.05 0 010 12.586c0-.543.206-1.061.571-1.436L10.864.553a1.765 1.765 0 012.62.06c.71.795.683 2.057-.055 2.817l-8.9 9.16 8.902 9.183c.738.76.761 2.024.052 2.815a1.78 1.78 0 01-1.336.612"></path></defs><use fill="currentColor" transform="matrix(0 -1 -1 0 29 24)" xlink:href="#mbIconAngle" fill-rule="evenodd"></use></svg>              </div>

            <div class="shower-wrap">
              <div class="shower" id="shower">
                <?php echo del_def_location(); ?>
              </div>
            </div>

            <div class="loader"></div>
          </div>
        </div>

        <div class="wb"><button type="submit" class="btn mbBg3"><?php _e('Search', 'delta'); ?></button></div>
      </form>
    </div>
  </div>
<?php } ?>

<?php if(osc_is_home_page() )  {  osc_current_web_theme_path('inc.search-home.php'); } 
else{  osc_current_web_theme_path('inc.search-search.php'); }  ?>	