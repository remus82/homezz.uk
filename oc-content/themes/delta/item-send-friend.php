<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="<?php echo str_replace('_', '-', osc_current_user_locale()); ?>">
<head>
  <?php osc_current_web_theme_path('head.php') ; ?>
  <meta name="robots" content="noindex, nofollow" />
  <meta name="googlebot" content="noindex, nofollow" />
  <script type="text/javascript" src="<?php echo osc_current_web_theme_js_url('jquery.validate.min.js') ; ?>"></script>
</head>

<?php 
  $type = (Params::getParam('type') == '' ? 'send_friend' : Params::getParam('type')); 
  $user_id = Params::getParam('userId');

  if($type == 'itemviewer') {
    ob_clean();
    ob_start(); 
    Params::setParam('itemviewer', 1);
    require_once osc_base_path().'oc-content/themes/delta/item.php'; 
    
    $viewer_content = ob_get_contents();
    ob_end_clean();

    echo $viewer_content;
    exit;
  }
?>  


<body id="body-item-forms" class="fw-supporting">
  <?php osc_current_web_theme_path('header.php'); ?></div>


  <?php if($type == 'friend') { ?>

    <!-- SEND TO FRIEND FORM -->
    <div id="send-friend-form" class="fw-box" style="display:block;">
      <div class="head">
        <h1><?php _e('Send to friend', 'delta'); ?></h1>
      </div>

      <div class="middle">
        <div class="row">
          <div id="item-card">
            <?php if(osc_images_enabled_at_items()) { ?> 
              <?php osc_get_item_resources(); ?>
              <?php osc_reset_resources(); ?>

              <?php if(osc_count_item_resources() > 0 ) { ?>
                <div class="img">
                  <?php for($i = 0;osc_has_item_resources(); $i++) { ?>
                    <img src="<?php echo osc_resource_url(); ?>" alt="<?php echo osc_esc_html(osc_item_title()); ?> - <?php echo $i+1;?>"/>
                    <?php break; ?>
                  <?php } ?>
                </div>
              <?php } ?>
            <?php } ?>
            
            <div class="dsc">
              <strong><?php echo osc_item_title(); ?></strong>
              
              <?php if(del_check_category_price(osc_item_category_id())) { ?>
                <div><?php echo osc_item_formated_price(); ?></div>
              <?php } ?>
            </div>
          </div>
        </div>
            
        <ul id="error_list"></ul>

        <form target="_top" id="sendfriend" name="sendfriend" action="<?php echo osc_base_url(true); ?>" method="post">
          <fieldset>
            <input type="hidden" name="action" value="send_friend_post" />
            <input type="hidden" name="page" value="item" />
            <input type="hidden" name="id" value="<?php echo osc_item_id(); ?>" />

            <?php if(osc_is_web_user_logged_in()) { ?>
              <input type="hidden" name="yourName" value="<?php echo osc_esc_html( osc_logged_user_name() ); ?>" />
              <input type="hidden" name="yourEmail" value="<?php echo osc_logged_user_email();?>" />
            <?php } else { ?>
              <div class="row">
                <label for="yourName"><span><?php _e('Your name', 'delta'); ?></span><span class="req">*</span></label> 
                <div class="input-box"><?php SendFriendForm::your_name(); ?></div>

                <label for="yourEmail"><span><?php _e('Your e-mail address', 'delta'); ?></span><span class="req">*</span></label>
                <div class="input-box"><?php SendFriendForm::your_email(); ?></div>
              </div>
            <?php } ?>

            <div class="row">
              <label for="friendName"><span><?php _e("Your friend's name", 'delta'); ?></span><span class="req">*</span></label>
              <div class="input-box"><?php SendFriendForm::friend_name(); ?></div>

              <label for="friendEmail"><span><?php _e("Your friend's e-mail address", 'delta'); ?></span><span class="req">*</span></label>
              <div class="input-box last"><?php SendFriendForm::friend_email(); ?></div>
            </div>
                  
            <div class="row last">
              <label for="message"><span><?php _e('Message', 'delta'); ?></span><span class="req">*</span></label>
              <?php SendFriendForm::your_message(); ?>
            </div>

            <?php del_show_recaptcha(); ?>

            <button type="<?php echo (del_param('forms_ajax') == 1 ? 'button' : 'submit'); ?>" id="send-message" class="mbBg item-form-submit" data-type="friend"><?php _e('Send message', 'delta'); ?></button>
          </fieldset>
        </form>

        <?php SendFriendForm::js_validation(); ?>
      </div>
    </div>
  <?php } ?>

 

  <?php if($type == 'comment') { ?>

    <!-- NEW COMMENT FORM -->
    <?php if( osc_comments_enabled() && (osc_reg_user_post_comments () && osc_is_web_user_logged_in() || !osc_reg_user_post_comments()) ) { ?>
      <form target="_top" action="<?php echo osc_base_url(true) ; ?>" method="post" name="comment_form" id="comment_form" class="fw-box" style="display:block;">
        <input type="hidden" name="action" value="add_comment" />
        <input type="hidden" name="page" value="item" />
        <input type="hidden" name="id" value="<?php echo osc_item_id() ; ?>" />

        <fieldset>
          <div class="head">
            <h1><?php _e('Add a new comment', 'delta'); ?></h1>
          </div>

          <div class="middle">
          
            <div class="row">
              <div id="item-card">
                <?php if(osc_images_enabled_at_items()) { ?> 
                  <?php osc_get_item_resources(); ?>
                  <?php osc_reset_resources(); ?>

                  <?php if(osc_count_item_resources() > 0 ) { ?>
                    <div class="img">
                      <?php for($i = 0;osc_has_item_resources(); $i++) { ?>
                        <img src="<?php echo osc_resource_url(); ?>" alt="<?php echo osc_esc_html(osc_item_title()); ?> - <?php echo $i+1;?>"/>
                        <?php break; ?>
                      <?php } ?>
                    </div>
                  <?php } ?>
                <?php } ?>
                
                <div class="dsc">
                  <strong><?php echo osc_item_title(); ?></strong>
                  
                  <?php if(del_check_category_price(osc_item_category_id())) { ?>
                    <div><?php echo osc_item_formated_price(); ?></div>
                  <?php } ?>
                </div>
              </div>
            </div>
              
            <?php CommentForm::js_validation(); ?>
            <ul id="comment_error_list"></ul>

            <?php if(osc_is_web_user_logged_in()) { ?>
              <input type="hidden" name="authorName" value="<?php echo osc_esc_html( osc_logged_user_name() ); ?>" />
              <input type="hidden" name="authorEmail" value="<?php echo osc_logged_user_email();?>" />
            <?php } else { ?>
              <div class="row">
                <label for="authorName"><?php _e('Name', 'delta') ; ?></label> 
                <div class="input-box"><?php CommentForm::author_input_text(); ?></div>
              </div>

              <div class="row">
                <label for="authorEmail"><span><?php _e('E-mail', 'delta') ; ?></span><span class="req">*</span></label> 
                <div class="input-box"><?php CommentForm::email_input_text(); ?></div>
              </div>                  
            <?php } ?>
            
            <?php if(osc_enable_comment_rating()) { ?>
            <div class="row">
              <label for=""><?php _e('Rating', 'delta'); ?></label>
              <div class="stars">
                <?php //CommentForm::rating_input_text(); ?>
                <input type="hidden" name="rating" value="" />

                <div class="comment-leave-rating">
                  <i class="fa fa-star is-rating-item" data-value="1"></i> 
                  <i class="fa fa-star is-rating-item" data-value="2"></i> 
                  <i class="fa fa-star is-rating-item" data-value="3"></i> 
                  <i class="fa fa-star is-rating-item" data-value="4"></i> 
                  <i class="fa fa-star is-rating-item" data-value="5"></i> 
                </div>
                
                <span class="comment-rating-selected"></span>
              </div>
            </div>
            <?php } ?>

            <div class="row" id="last">
              <label for="title"><?php _e('Title', 'delta') ; ?></label>
              <div class="input-box"><?php CommentForm::title_input_text(); ?></div>
            </div>
        
            <div class="row">
              <label for="body"><span><?php _e('Message', 'delta'); ?></span><span class="req">*</span></label>
              <?php CommentForm::body_input_textarea(); ?>
            </div>

            <?php del_show_recaptcha(); ?>

            <button type="<?php echo (del_param('forms_ajax') == 1 ? 'button' : 'submit'); ?>" id="send-comment" class="mbBg item-form-submit" data-type="comment"><?php _e('Submit comment', 'delta') ; ?></button>
          </div>
        </fieldset>
      </form>
    <?php } ?>
  <?php } ?>


  <?php if($type == 'contact') { ?>

    <!-- ITEM CONTACT FORM -->
    <form target="_top" action="<?php echo osc_base_url(true) ; ?>" method="post" name="contact_form" id="contact_form" class="fw-box" style="display:block;">
      <input type="hidden" name="action" value="contact_post" />
      <input type="hidden" name="page" value="item" />
      <input type="hidden" name="id" value="<?php echo osc_item_id() ; ?>" />

      <?php osc_prepare_user_info() ; ?>

      <fieldset>
        <div class="head">
          <h1><?php _e('Contact seller', 'delta'); ?></h1>
        </div>

        <div class="middle">
          <?php ContactForm::js_validation(); ?>
          <ul id="error_list"></ul>

          <?php if( osc_item_is_expired () ) { ?>
            <div class="problem">
              <?php _e('This listing expired, you cannot contact seller.', 'delta') ; ?>
            </div>
          <?php } else if( (osc_logged_user_id() == osc_item_user_id()) && osc_logged_user_id() != 0 ) { ?>
            <div class="problem">
              <?php _e('It is your own listing, you cannot contact yourself.', 'delta') ; ?>
            </div>
          <?php } else if( osc_reg_user_can_contact() && !osc_is_web_user_logged_in() ) { ?>
            <div class="problem">
              <?php _e('You must log in or register a new account in order to contact the advertiser.', 'delta') ; ?>
            </div>
          <?php } else { ?> 

            <?php if(osc_is_web_user_logged_in()) { ?>
              <input type="hidden" name="yourName" value="<?php echo osc_esc_html( osc_logged_user_name() ); ?>" />
              <input type="hidden" name="yourEmail" value="<?php echo osc_logged_user_email();?>" />
            <?php } else { ?>
              <div class="row">
                <label for="yourName"><?php _e('Name', 'delta') ; ?><span class="req">*</span></label> 
                <div class="input-box"><?php ContactForm::your_name(); ?></div>
              </div>

              <div class="row">
                <label for="yourEmail"><span><?php _e('E-mail', 'delta') ; ?></span><span class="req">*</span></label> 
                <div class="input-box"><?php ContactForm::your_email(); ?></div>
              </div>       
            <?php } ?>
       

            <div class="row">
              <label for="phoneNumber"><span><?php _e('Phone', 'delta') ; ?></span></label> 
              <div class="input-box"><?php ContactForm::your_phone_number(); ?></div>
            </div>          
      
            <div class="row">
              <label for="message"><span><?php _e('Message', 'delta'); ?></span><span class="req">*</span></label>
              <?php ContactForm::your_message(); ?>
            </div>

            <?php del_show_recaptcha(); ?>

            <button type="<?php echo (del_param('forms_ajax') == 1 ? 'button' : 'submit'); ?>" id="send-message" class="mbBg item-form-submit" data-type="contact"><?php _e('Send message', 'delta') ; ?></button>
          <?php } ?>
        </div>
      </fieldset>
    </form>
  <?php } ?>



  <?php if($type == 'contact_public') { ?>

    <!-- PUBLIC PROFILE CONTACT SELLER -->
    <?php if(osc_reg_user_can_contact() && osc_is_web_user_logged_in() || !osc_reg_user_can_contact() ) { ?>
      <form target="_top" action="<?php echo osc_base_url(true) ; ?>" method="post" name="contact_form" id="contact_form_public" class="fw-box" style="display:block;">
        <input type="hidden" name="action" value="contact_post" class="nocsrf" />
        <input type="hidden" name="page" value="user" />
        <input type="hidden" name="id" value="<?php echo $user_id; ?>" />
        <?php if(osc_is_web_user_logged_in()) { ?>
        <input type="hidden" id="yourName" name="yourName" value="<?php echo osc_logged_user_name(); ?>">
        <input type="hidden" id="yourEmail" name="yourEmail" value="<?php echo osc_logged_user_email(); ?>">
        <?php } ?>

        <div class="head">
          <h1><?php _e('Contact seller', 'delta'); ?></h1>
        </div>

        <div class="middle">
          <fieldset>
            <?php ContactForm::js_validation(); ?>
            <ul id="error_list"></ul>

            <?php if($user_id == osc_logged_user_id() && osc_is_web_user_logged_in()) { ?>
              <div class="problem"><?php _e('This is your own profile!', 'delta'); ?></div>
            <?php } else { ?>
              <?php if(!osc_is_web_user_logged_in()) { ?>
                <div class="row">
                  <label for="yourName"><?php _e('Name', 'delta'); ?></label> 
                  <div class="input-box"><?php ContactForm::your_name(); ?></div>
                </div>

                <div class="row">
                  <label for="yourEmail"><span><?php _e('E-mail', 'delta') ; ?></span><span class="req">*</span></label> 
                  <div class="input-box"><?php ContactForm::your_email(); ?></div>
                </div>
              <?php } ?>              

              <div class="row last">
                <label for="phoneNumber"><span><?php _e('Phone number', 'delta') ; ?></span></label>
                <div class="input-box"><?php ContactForm::your_phone_number(); ?></div>
              </div>

              <div class="row">
                <label for="message"><span><?php _e('Message', 'delta'); ?></span><span class="req">*</span></label>
                <?php ContactForm::your_message(); ?>
              </div>

              <?php del_show_recaptcha(); ?>

              <button type="<?php echo (del_param('forms_ajax') == 1 ? 'button' : 'submit'); ?>" id="send-public-message" class="mbBg item-form-submit" data-type="contact_public"><?php _e('Send message', 'delta') ; ?></button>
            <?php } ?>
          </fieldset>
        </div>
      </form>
    <?php } ?>
  <?php } ?>

  <script>
    $('#sendfriend #yourName, #sendfriend #yourEmail, #sendfriend #friendName, #sendfriend #friendEmail, #sendfriend #yourName, #sendfriend #message').prop('required', true);
    $('#comment_form #body, #comment_form #yourName').prop('required', true);
    $('#contact_form #yourName, #contact_form #yourEmail, #contact_form #message').prop('required', true);
  </script>

  <?php osc_current_web_theme_path('footer.php') ; ?>
</body>
</html>