<?php

// Enable OSClass functions
define('ABS_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
require_once ABS_PATH . 'oc-load.php'; 

// Get posted data
$image_id = explode(",",$_POST['image_id']);
$image_path = explode(",",$_POST['image_path']);
$image_ext = explode(",",$_POST['image_ext']);

$contact_phone = $_POST['contact_phone'];
$mobil = $_POST['mobil'];
$mobil2 = $_POST['mobil2'];
$contact_website = $_POST['contact_website'];
$contact_name = $_POST['contact_name'];
$contact_email = $_POST['contact_email'];
$contact_email_en = $_POST['contact_email_en'];
$contact_address = $_POST['contact_address'];

$site_title = $_POST['site_title'];
$site_url = $_POST['site_url'];
$desc = stripslashes($_POST['desc']);
$title = stripslashes($_POST['title']);
$price = $_POST['price'];
$pub_date = $_POST['pub_date'];
$country = $_POST['country'];
$region = $_POST['region'];
$city = $_POST['city'];
$zip = $_POST['zip'];
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
<head>
<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
<title><?php _e('Print Ad','printad'); ?></title>

<script language="javascript">function printpage(){window.print();}</script>

<script language="javascript"> 
function toggle() {
	var ele = document.getElementById("pics");
	var text = document.getElementById("displayText");
	if(ele.style.display == "none") {
    		ele.style.display = "block";
		text.innerHTML = "<?php _e('Hide photos','printad'); ?>";
  	}
	else {
		ele.style.display = "none";
		text.innerHTML = "<?php _e('Show photos','printad'); ?>";
	}
} 
</script>


<style type="text/css">
    #pics { clear:both; display: block; float:left; width: 100%; padding:2px;}
    #pics li { list-style:none; display:inline-table; position:relative; float: left; width: 100px; height: 80px; padding:4px; margin:4px; border: 1px solid lightgray; background-color: white;}
    .box { border: 1px dotted #ccc; padding:5px; }

    .price { background: white; margin-left:15px; font-size:1.5em; font-weight:bold; border: 1px solid #ccc; padding: 5px;padding-bottom:8px;}

    #print {float:right; }
    #showhide {float:right; }

    #title { float:left; width:700px; padding:5px; border-bottom: 1px solid #ccc;background-color:#eee; }
    #pictures { float:left; width:700px; padding:5px; border-top: 1px solid #ccc;}
    #displayText {font-size:10px; text-decoration:none; color: gray;}

    #info {float:left; width:300px; border-right: 1px solid #ccc; padding:5px;}
    #desc {float:left; width:300px; border-left: 1px solid #ccc; padding:5px; margin-left:-1px;}
    #footer {float:left; width:700px;  border-top: 1px dotted #ccc;}
</style>


</head>
<body>    
    <div id="title">
       <font size="5"><?php echo $title; ?></font><span class="price"><?php echo $price; ?></span>
       <div id="print" style="font-weight:bold;padding-top: 4px;padding-right: 4px;color:#278827;"><a href="#" onclick="printpage();"><?php _e('Print!','printad'); ?></a></div>
    </div>
    <div id="info">
	<b><?php _e('Address','printad'); ?>:</b><br><?php if($contact_address!='') echo $contact_address.'<br>'; ?><?php echo $city.', '.$region; ?><br>
	<br>
	<b><?php _e('Published','printad'); ?>:</b><br><?php echo osc_format_date($pub_date); ?><br>
	<br>
	<b><?php _e('Contact info','printad'); ?>:</b><br>
		<?php if($contact_name!='') echo $contact_name.'<br>'; ?>
		<?php if($contact_phone!='')echo $contact_phone.'<br>'; ?>
		<?php if($contact_phone=='' && $mobil != '')echo $mobil.'<br>'; ?>
		<?php if($contact_phone=='' && $mobil == '' && $mobil2 != '')echo $mobil2.'<br>'; ?>
		<?php if($contact_email!='' && $contact_email_en == 1)echo $contact_email. '<br>'; ?>
		<?php if($contact_website!='')echo $contact_website.'<br>'; ?>
	
    </div>

    <div id="desc">
	<b><?php _e('Description','printad'); ?>:</b> <?php echo substr(strip_tags($desc), 0, stripos(strip_tags($desc), 'Tweet$')); ?>
    </div>

    <?php if($image_id[0]!=''){ ?>

    <div id="pictures">
    <a id="displayText" href="javascript:toggle();"><?php _e('Hide photos','printad'); ?></a>
	<div id="pics">
	    <?php for($index=0; $index<count($image_id); $index++){ ?>
		<li><img src="<?php echo $image_path[$index].$image_id[$index].'_thumbnail.'.$image_ext[$index]; ?>" width="140"></li>
	    <?php  } ?>
	</div>
    </div>
    <?php } ?>
    <div id="footer">
	<?php echo __('This listing is placed in','printad') . ' '; ?> <b><?php echo $site_title.'</b> - <i>'.$site_url; ?></i>
    </div>
</body>
</html>