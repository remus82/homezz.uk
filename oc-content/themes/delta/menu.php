
  <div class="stellarnav">
      <ul>

      <li><a href="<?php echo osc_base_url(); ?>" class="l1 <?php if(osc_is_home_page()) { ?>active<?php } ?>">
        <i class="fas fa-home"></i>
        <span><?php _e('HOME', 'delta'); ?></span>
      </a>
      </li>
 
      <li>  <a href="<?php echo osc_search_url(array('page' => 'search')); ?>" class="l2 <?php if(osc_is_search_page()) { ?>active<?php } ?>">
        <i class="fas fa-search"></i>
        <span><?php _e('SEARCH', 'delta'); ?></span>
      </a>
      </li>
      <li> <a href="<?php echo osc_item_post_url(); ?>" class="l3 <?php if(osc_is_publish_page() || osc_is_edit_page()) { ?>active<?php } ?>">
        <i class="far fa-plus-square"></i>
        <span><?php _e('PUBLISH', 'delta'); ?></span>
      </a>
      </li>
    <?php if(function_exists('im_messages')) { ?>
    
      <li> <a href="<?php echo osc_route_url('im-threads'); ?>" class="l4 <?php if(osc_get_osclass_location() == 'im') { ?>active<?php } ?>">
          <i class="far fa-comment-alt">
            <?php $mes_counter = del_count_messages(osc_logged_user_id()); ?>
            <?php if($mes_counter > 0) { ?>
              <span class="circle"></span>
            <?php } ?>        
          </i>
          <span><?php _e('MESSAGES', 'delta'); ?></span>
      </a> 
      </li>
    <?php } else if(function_exists('fi_make_favorite')) { ?>
      <li>
        <a href="<?php echo osc_route_url('favorite-lists'); ?>" class="l4 <?php if(osc_get_osclass_location() == 'fi') { ?>active<?php } ?>">
          <i class="far fa-heart"></i>
          <span><?php _e('FAVORITES', 'delta'); ?></span>
        </a>
        </li>
    <?php }  ?>
    <!----------------------------------->
   
    <?php if(osc_is_web_user_logged_in()) { ?>

  
          <li class="has-sub">
              <a href="<?php echo osc_user_list_items_url(); ?>">
                <i class="far fa-user">
                  <span class="circle"></span>
                </i>
                  <span><?php _e('MY PROFILE', 'delta'); ?></span>
              </a>
            <ul>
                <li class="image">
                  <span><?php echo sprintf(__('Hello %s!', 'delta'), osc_logged_user_name()); ?> <img src="<?php echo osc_user_profile_img_url(osc_user_id()); ?>" alt="<?php echo osc_esc_html($contact_name); ?>" /></span>
                 
                </li>

                <li><a href="<?php echo osc_user_list_items_url(); ?>"><?php _e('My ads', 'delta'); ?></a></li>
                <li><a href="<?php echo osc_user_profile_url(); ?>"><?php _e('Profile', 'delta'); ?></a></li>
                <li><a href="<?php echo osc_user_alerts_url(); ?>"><?php _e('Saved', 'delta'); ?></a></li>
                <li><a href="<?php echo osc_item_post_url(); ?>" class="l3 <?php if(osc_is_publish_page() || osc_is_edit_page()) { ?>active<?php } ?>">
                        <i class="far fa-plus-square"></i>
                        <span><?php _e('PUBLISH', 'delta'); ?></span>
                    </a>
                </li>
                <?php if(function_exists('fi_make_favorite')) { ?>
                    <li><a href="<?php echo osc_route_url('favorite-lists'); ?>"><?php _e('Favorites', 'delta'); ?></a></li>
                  <?php } ?>

                  <?php if(function_exists('im_messages')) { ?>
                    <li> <a href="<?php echo osc_route_url('im-threads'); ?>"><?php _e('Messages', 'delta'); ?></a></li>
                  <?php } ?>

                  <?php if(function_exists('osp_user_sidebar')) { ?>
                    <li><a href="<?php echo osc_route_url('osp-item'); ?>"><?php _e('Promotion', 'delta'); ?></a></li>
                  <?php } ?>
                  
                  <li><a href="<?php echo osc_user_public_profile_url(osc_logged_user_id()); ?>"><?php _e('Public Profile  ', 'delta'); ?></a></li>
                  <li><a class="logout" href="<?php echo osc_user_logout_url(); ?>"><?php _e('Log out', 'delta'); ?></a></li>
            </ul>

          </li>
        <?php } else { ?>
        
          <li class="has-sub"><a href="#">
                    <i class="far fa-user">
                      <span class="circle"></span>
                    </i>
                    <span><?php _e('My Profile', 'delta'); ?></span>
                 </a>
            <ul>
                <li><a class="login" href="<?php echo osc_user_login_url(); ?>"><?php _e('LOGIN', 'delta'); ?></a> </li>
                <li><a class="register" href="<?php echo osc_register_account_url(); ?>"><?php _e('REGISTER', 'delta'); ?></a></li>
            </ul>
          </li>
        <?php } ?>
   
    <!------------------------------------------->
  <?php 
        function drawSubcategory($category) {
            if ( osc_count_subcategories2() > 0 ) {
                    osc_category_move_to_children();
                    ?>
                    <ul>
                        <?php while ( osc_has_categories() ) { ?>
                            <li><a   href="<?php echo osc_search_category_url(); ?>"><?php echo osc_category_name(); ?></a> 
                                <?php drawSubcategory(osc_category()); ?>
                            </li>
                        <?php } ?>
                    </ul>
    <?php
                    osc_category_move_to_parent();
                }
            }

            $total_categories   = osc_count_categories();

               osc_goto_first_category();  
                                
        if(osc_count_categories () > 0) {
                echo'   ';

            while ( osc_has_categories() ) { ?>
                            <li><a href="<?php echo osc_search_category_url(); ?>"><?php echo osc_category_name(); ?></a> 
                                    <?php drawSubcategory(osc_category()); ?>    
                            </li>
                <?php 
              
            } 

        echo'</ul>';
        };
        
        ?>   
</div>

