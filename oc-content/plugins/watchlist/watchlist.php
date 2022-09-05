<?php 
    $i_userId = osc_logged_user_id();
	if(Params::getParam('delete') != '' && osc_is_web_user_logged_in()){
		delete_item(Params::getParam('delete'), $i_userId);
	}

    $itemsPerPage = (Params::getParam('itemsPerPage') != '') ? Params::getParam('itemsPerPage') : 5;
    $iPage        = (Params::getParam('iPage') != '') ? Params::getParam('iPage') : 0;

    Search::newInstance()->addConditions(sprintf("%st_item_watchlist.fk_i_user_id = %d", DB_TABLE_PREFIX, $i_userId));
    Search::newInstance()->addConditions(sprintf("%st_item_watchlist.fk_i_item_id = %st_item.pk_i_id", DB_TABLE_PREFIX, DB_TABLE_PREFIX));
    Search::newInstance()->addTable(sprintf("%st_item_watchlist", DB_TABLE_PREFIX));
    Search::newInstance()->page($iPage, $itemsPerPage);

    $aItems      = Search::newInstance()->doSearch();
    $iTotalItems = Search::newInstance()->count();
    $iNumPages   = ceil($iTotalItems / $itemsPerPage) ;

    View::newInstance()->_exportVariableToView('items', $aItems);
    View::newInstance()->_exportVariableToView('search_total_pages', $iNumPages);
    View::newInstance()->_exportVariableToView('search_page', $iPage) ;

	// delete item from watchlist
	function delete_item($item, $uid){
		$conn = getConnection();
		$conn->osc_dbExec("DELETE FROM %st_item_watchlist WHERE fk_i_item_id = %d AND fk_i_user_id = %d LIMIT 1", DB_TABLE_PREFIX , $item, $uid);
	}
?>
<div class="content user_account">
    <h1>
        <strong><?php _e('Your watchlist', 'watchlist'); ?></strong>
    </h1>
    <div id="sidebar">
        <?php echo osc_private_user_menu(); ?>
    </div>
    <div id="main">
        <?php if (osc_count_items() == 0) { ?>
        <div class="empty"><?php _e('You don\'t have any items yet', 'watchlist'); ?></div>
        <?php } else { ?>
        <h2 class="round2"><i class="fa fa-star-o"></i>&nbsp;<?php printf(_n('You are watching %d item', 'You are watching %d items', $iTotalItems, 'watchlist'), $iTotalItems) ; ?></h2>
        <div class="ad_list">
            <table border="0" cellspacing="0">
                <tbody>
                    <?php $class = "even"; ?>
                    <?php while ( osc_has_items() ) { ?>
                    <tr class="<?php echo $class; ?>">
                      <?php if( osc_images_enabled_at_items() ) { ?>
                       <td class="photo">
                         <?php if(osc_count_item_resources()) { ?>
                          <a href="<?php echo osc_item_url(); ?>"><img src="<?php echo osc_resource_thumbnail_url(); ?>" width="150" height="125" title="<?php echo osc_item_title(); ?>" alt="<?php echo osc_item_title(); ?>" /></a>
                        <?php } else { ?>
                          <a href="<?php echo osc_item_url(); ?>"><img src="<?php echo osc_current_web_theme_url('images/no_photo.gif'); ?>" title="" alt="" width="150" height="125"/></a>
                        <?php } ?>
                       </td>
                       <?php } ?>
                       <td class="text">
                         <div id="search-list">
                          <?php if( osc_price_enabled_at_items() ) { ?>
                          <div class="price"><?php echo osc_item_formated_price(); } ?></div>
                          <div class="cat"><?php  echo osc_item_category(); ?></div>
                          <div class="date"><?php echo date("d-m-Y", strtotime(osc_item_pub_date())); ?></div>
                          <?php if(osc_item_city() <> '') { ?><div class="other"><?php  echo osc_item_city(); ?></div><?php } ?>
                          <?php if(osc_item_region() <> '') { ?><div class="other"><?php echo osc_item_region(); ?></div><?php } ?>
                         </div>
                         <h3>
                          <a class="item-name" href="<?php echo osc_item_url(); ?>"><?php echo osc_highlight( strip_tags( osc_item_title() ) ); ?></a>
                          <a class="edit-links" onclick="return confirm('<?php _e('Are you sure you want to delete this listing? This action cannot be undone.', 'watchlist'); ?>')" href="<?php echo osc_render_file_url(osc_plugin_folder(__FILE__) . 'watchlist.php') . '&delete=' . osc_item_id(); ?>" rel="nofollow"><i class="fa fa-trash-o"></i>&nbsp;<?php _e('Clear', 'watchlist'); ?></a>
                         </h3>           
                         <p class="desc"><?php echo osc_highlight( strip_tags( osc_item_description() ), 130); ?></p>
                         <p class="go-more">
                           <a id="more-link" href="<?php echo osc_item_url(); ?>">
                            <?php _e('see more details', 'watchlist'); ?>
                            <?php if (osc_count_premium_resources()>0) { 
                              echo ' ' . __('and', 'watchlist') . ' '; 
                              echo osc_count_premium_resources();
                              if(osc_count_premium_resources()>1) { echo ' ' . __('photos', 'watchlist'); } else { echo ' ' . __('photo', 'watchlist'); } } 
                            ?>      
                          </a>
                        </p>
                      </td>
                    </tr>
                    <?php $class = ($class == 'even') ? 'odd' : 'even'; ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="paginate">
            <?php echo osc_pagination(array('url' => osc_render_file_url(osc_plugin_folder(__FILE__) . 'watchlist.php') . '?iPage={PAGE}')); ?>
        </div>
        <?php } ?>
    </div>
</div>