<?php

if (!defined('ABS_PATH'))
    exit('ABS_PATH is not loaded. Direct access is not allowed.');
/*
 * Copyright (C) 2018 Puiu Calin
 * This program is a commercial software: is forbidden to use this software without licence, 
 * on multiple installations, and by purchasing from other source than those authorized for the sale of software.
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */

switch (Params::getParam('gdpr_case')) {
    case('accept'):
        $json['success'] = 'error';
        $array = Params::getParam('result');
        if (gdpr_cookie_set_cookie(json_encode($array))) {
            $json['success'] = 'success';
        }
        if (osc_is_web_user_logged_in()) {
            $user_id = osc_logged_user_id();
            GdprCookie::newInstance()->insert_preferences(json_encode($array), $user_id);
        }
        echo json_encode($json);
        break;
    default :
        echo json_encode(array('error' => __('no action defined')));
        break;
}