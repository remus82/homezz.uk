<?php
  if(!defined('ABS_PATH')) {
    define('ABS_PATH', dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/');
  }

  require_once ABS_PATH . 'oc-load.php';
  http_response_code(200);

  $id = Params::getParam('id');

  if($id > 0) { 
    $inv = ModelINV::newInstance()->getInvoice($id);
  }


  $amount = inv_subtotal($inv);
  $amount_disc = $amount * (100 - $inv['f_discount'])/100;
  //$tax = $amount_disc * (100 + $inv['f_tax'])/100 - $amount_disc;
  $tax = $amount_disc*((100 + $inv['f_tax'])/100) - $amount_disc;

  $discount = $amount * $inv['f_discount']/100;


  // ADD STAMP
  if (inv_get_stamp()) {
    list($image_w, $image_h) = getimagesize(inv_get_stamp());
    $ratio = $image_w / $image_h;

    $check_w = 240/$image_w;
    $check_h = 100/$image_h;

    $multiplier = min($check_w, $check_h);

    $w = $image_w*$multiplier;
    $h = $image_h*$multiplier;
  }

?>


<div>&nbsp;</div>

<table cellspacing="0" cellpadding="10" style="width:99%;border-collapse: collapse;font-size:13px;">
  <tr style="width:100%;">
    <td style="width:50%;text-align:left;">
      <table cellspacing="0" cellpadding="0" style="width:100%;">
        <tr>
          <td style="height:80px;">&nbsp;</td>
        </tr>

        <tr>
          <td>
            <table cellspacing="0" cellpadding="0" style="width:100%;">
              <tr>
                <td style="width:3%;height:100px;">&nbsp;</td><td style="width:97%;font-weight:bold;height:80px;"><?php echo nl2br($inv['s_from']); ?></td>
              </tr>
            </table>
          </td>
        </tr>

        <tr>
          <td>
            <table cellspacing="0" cellpadding="0" style="width:100%;">
              <tr>
                <td style="width:3%;">&nbsp;</td><td style="width:97%;"><?php _e('Bill to', 'invoice'); ?></td>
              </tr>
              
              <tr>
                <?php 
                  $to = $inv['s_to'];
                  $to = str_replace(__('Shipping address', 'invoice'), '<span style="font-weight:normal;">' . __('Shipping address', 'invoice') . '</span>', $to);                
                ?>
                <td style="width:3%;height:100px;">&nbsp;</td><td style="width:97%;font-weight:bold;height:80px;"><?php echo nl2br($to); ?></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>

    <td style="width:50%;text-align:right;">
      <table cellspacing="0" cellpadding="0" style="width:100%;">
        <tr>
          <td colspan="2" style="width:95%;font-size:30px;line-height:30px;color:#444;font-weight:lighter;"><?php echo ($inv['s_title'] <> '' ? $inv['s_title'] : __('Invoice', 'invoice')); ?></td>
          <td style="width:5%;text-align:right;height:26px;">&nbsp;</td>
        </tr>

        <tr>
          <td colspan="2" style="width:95%;color:#777;height:80px;">#<?php echo ($inv['s_identifier'] <> '' ? $inv['s_identifier'] : $inv['pk_i_id']); ?></td>
          <td style="width:5%;text-align:right;height:26px;">&nbsp;</td>
        </tr>
        
       
        <tr><td colspan="3">&nbsp;</td></tr>

        <tr>
          <td style="width:40%;text-align:right;color:#777;height:26px;"><?php _e('Date', 'invoice'); ?>:</td>
          <td style="width:55%;text-align:right;height:26px;"><?php echo ($inv['dt_date'] <> '' ? date('j. M Y', strtotime($inv['dt_date'])) : ''); ?></td>
          <td style="width:5%;text-align:right;height:26px;">&nbsp;</td>
        </tr>

        <tr>
          <td style="width:40%;text-align:right;color:#777;height:26px;"><?php _e('Due Date', 'invoice'); ?>:</td>
          <td style="width:55%;text-align:right;height:26px;"><?php echo ($inv['dt_due_date'] <> '' ? date('j. M Y', strtotime($inv['dt_due_date'])) : ''); ?></td>
          <td style="width:5%;text-align:right;height:26px;">&nbsp;</td>
        </tr>

        <tr style="font-weight:bold;background-color:#f0f0f0;">
          <td colspan="3">
            <table cellspacing="0" cellpadding="0" style="width:100%;">
              <tr><td colspan="3" style="height:10px;">&nbsp;</td></tr>

              <tr>
                <td style="width:38%;text-align:right;height:18px;"><?php _e('Balance Due', 'invoice'); ?>:</td>
                <td style="width:57%;text-align:right;height:18px;"><?php echo inv_format_price($inv['f_balance'], $inv['s_currency']); ?></td>
                <td style="width:5%;text-align:right;height:18px;">&nbsp;</td>
              </tr>

              <tr><td colspan="3" style="height:10px;">&nbsp;</td></tr>
            </table>
          </td>
        </tr>      
      </table>
    </td>
  </tr>

  <tr class="mb-items" style="width:100%;">
    <td colspan="2" style="width:100%;">
      <table cellspacing="0" cellpadding="5" style="width:100%;">
        <tr style="width:100%;background-color:#444;color:#fff;font-weight:bold;">
          <td>
            <table cellspacing="0" cellpadding="5" style="width:100%;">
              <tr>
                <td style="width:45%;"><?php _e('Item', 'invoice'); ?></td>
                <td style="width:15%;"><?php _e('Quantity', 'invoice'); ?></td>
                <td style="width:20%;text-align:right;"><?php _e('Rate', 'invoice'); ?></td>
                <td style="width:20%;text-align:right;"><?php _e('Amount', 'invoice'); ?></td>
              </tr>
            </table>
          </td>
        </tr>

        <?php if(isset($inv['items']) && count($inv['items']) > 0) { ?>
          <tr>
            <td>
              <table cellspacing="0" cellpadding="5" style="width:100%;">
                <?php foreach($inv['items'] as $item) { ?>
                  <tr style="width:100%">
                    <td style="width:45%;font-weight:bold;"><?php echo $item['s_description']; ?></td>
                    <td style="width:15%;"><?php echo $item['i_quantity']; ?></td>
                    <td style="width:20%;text-align:right;"><?php echo inv_format_price($item['f_rate'], $inv['s_currency']); ?></td>
                    <td style="width:20%;text-align:right;"><?php echo inv_format_price($item['i_quantity'] * $item['f_rate'], $inv['s_currency']); ?></td>
                  </tr>
                <?php } ?>
              </table>
            </td>
          </tr>
        <?php } ?>
      </table>
    </td>
  </tr>


  <tr><td style="height:30px;">&nbsp;</td></tr>


  <tr class="mb-subt">
    <td style="width:50%;">&nbsp;</td>
    <td style="width:50%;">
      <table cellspacing="0" cellpadding="0" style="width:100%;text-align:right;">
        <tr>
          <td style="width:40%;text-align:right;color:#777;height:26px;"><?php _e('Subtotal', 'invoice'); ?>:</td>
          <td style="width:55%;text-align:right;height:26px;"><?php echo inv_format_price(inv_subtotal($inv), $inv['s_currency']); ?></td>
          <td style="width:5%;height:26px;">&nbsp;</td>
        </tr>

        <?php if($inv['f_discount'] > 0) { ?>
          <tr>
            <td style="width:40%;text-align:right;color:#777;height:26px;"><?php _e('Discounts', 'invoice'); ?> (<?php echo (float)$inv['f_discount']; ?>%):</td>
            <td style="width:55%;text-align:right;height:26px;"><?php echo inv_format_price($discount, $inv['s_currency']); ?></td>
            <td style="width:5%;height:26px;">&nbsp;</td>
          </tr>
        <?php } ?>

        <?php if($inv['f_tax'] > 0) { ?>
          <tr>
            <td style="width:40%;text-align:right;color:#777;height:26px;"><?php _e('VAT', 'invoice'); ?> (<?php echo (float)$inv['f_tax']; ?>%):</td>
            <td style="width:55%;text-align:right;height:26px;"><?php echo inv_format_price($tax, $inv['s_currency']); ?></td>
            <td style="width:5%;height:26px;">&nbsp;</td>
          </tr>
        <?php } ?>

        <?php if($inv['f_shipping'] > 0) { ?>
          <tr>
            <td style="width:40%;text-align:right;color:#777;height:26px;"><?php _e('Shipping', 'invoice'); ?>:</td>
            <td style="width:55%;text-align:right;height:26px;"><?php echo inv_format_price($inv['f_shipping'], $inv['s_currency']); ?></td>
            <td style="width:5%;height:26px;">&nbsp;</td>
          </tr>
        <?php } ?>

        <?php if($inv['f_fee'] > 0) { ?>
          <tr>
            <td style="width:40%;text-align:right;color:#777;height:26px;"><?php _e('Fee', 'invoice'); ?>:</td>
            <td style="width:55%;text-align:right;height:26px;"><?php echo inv_format_price($inv['f_fee'], $inv['s_currency']); ?></td>
            <td style="width:5%;height:26px;">&nbsp;</td>
          </tr>
        <?php } ?>

        <tr>
          <td style="width:40%;text-align:right;color:#777;height:26px;"><?php _e('Total', 'invoice'); ?>:</td>
          <td style="width:55%;text-align:right;height:26px;"><?php echo inv_format_price($inv['f_amount'], $inv['s_currency']); ?></td>
          <td style="width:5%;height:26px;">&nbsp;</td>
        </tr>

        <tr>
          <td style="width:40%;text-align:right;color:#777;height:26px;"><?php _e('Amount Paid', 'invoice'); ?>:</td>
          <td style="width:55%;text-align:right;height:26px;"><?php echo inv_format_price($inv['f_paid'], $inv['s_currency']); ?></td>
          <td style="width:5%;height:26px;">&nbsp;</td>
        </tr>
     
        <?php if(inv_get_stamp()) { ?>
          <tr>
            <td colspan="3">
              <img src="<?php echo inv_get_stamp(); ?>" width="<?php echo $w; ?>" height="<?php echo $h; ?>"/>
           </td>
          </tr>
        <?php } ?>
      </table>
    </td>
  </tr>


  <?php if($inv['s_notes'] <> '' || $inv['s_terms'] <> '') { ?>
    <tr><td style="height:20px;">&nbsp;</td></tr>

    <tr class="mb-end">
      <td colspan="2" width="100%">
        <table style="width:100%;">
          <?php if($inv['s_notes'] <> '') { ?>
            <tr><td style="color:#777;height:18px;"><?php _e('Notes', 'invoice'); ?>:</td></tr>
            <tr><td><?php echo $inv['s_notes']; ?></td></tr>

            <tr><td style="height:30px;">&nbsp;</td></tr>
          <?php } ?>

          <?php if($inv['s_terms'] <> '') { ?>
            <tr><td style="color:#777;height:18px;"><?php _e('Terms', 'invoice'); ?>:</td></tr>
            <tr><td><?php echo ($inv['s_terms'] <> '' ? $inv['s_terms'] : '-'); ?></td></tr>
          <?php } ?>
        </table>
      </td>
    </tr>
  <?php } ?>
</table>
