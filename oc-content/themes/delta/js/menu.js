var $jq = jQuery.noConflict();


		jQuery(document).ready(function($) {
			jQuery('.stellarnav').stellarNav({
				theme: 'dark',
				breakpoint: 960,
				position: 'right',
				phoneBtn: '',
				locationBtn: ''
			});
		});
