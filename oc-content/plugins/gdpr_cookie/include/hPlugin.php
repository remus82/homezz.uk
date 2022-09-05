<?php

/*
 * Copyright (C) 2018 Puiu Calin
 * This program is a commercial software: is forbidden to use this software without licence, 
 * on multiple installations, and by purchasing from other source than those authorized for the sale of software.
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */

function h_gdpr_active_analytics() {
    $return = gdpr_cookie_active_preference('google_analytics');
    if (osc_is_web_user_logged_in()) {
        $cookie = GdprCookie::newInstance()->get_preferences(osc_logged_user_id());
        if (!empty($cookie)) {
            $json = json_decode($cookie['s_value'], true);
            if (isset($json['google_analytics'])) {
                $return == false;
            } else {
                $return == false;
            }
        }
    }
    return $return;
}

function h_gdpr_active_ads() {
    $return = gdpr_cookie_active_preference('ads');
    if (osc_is_web_user_logged_in()) {
        $cookie = GdprCookie::newInstance()->get_preferences(osc_logged_user_id());
        if (!empty($cookie)) {
            $json = json_decode($cookie['s_value'], true);
            if (isset($json['ads'])) {
                $return = true;
            } else {
                $return == false;
            }
        }
    }
    return $return;
}

function h_gdpr_active_custom() {
    $return = gdpr_cookie_active_preference('custom');
    if (osc_is_web_user_logged_in()) {
        $cookie = GdprCookie::newInstance()->get_preferences(osc_logged_user_id());
        if (!empty($cookie)) {
            $json = json_decode($cookie['s_value'], true);
            if (isset($json['custom'])) {
                $return = true;
            } else {
                $return == false;
            }
        }
    }
    return $return;
}

function h_gdpr_active_accept() {
    $return = gdpr_cookie_active_preference('accept');
    if (osc_is_web_user_logged_in()) {
        $cookie = GdprCookie::newInstance()->get_preferences(osc_logged_user_id());
        if (!empty($cookie)) {
            $json = json_decode($cookie['s_value'], true);
            if ($json['accept'] == 1) {
                $return = true;
            } else {
                $return == false;
            }
        }
    }
    return $return;
}

function h_gdpr_is_set() {
    //if cookie is set for first visit
    $cookie = gdpr_cookie_get();
    if (empty($cookie)) {
        $return = false;
        if (osc_is_web_user_logged_in()) {
            $cookie = GdprCookie::newInstance()->get_preferences(osc_logged_user_id());
            if (!empty($cookie)) {
                $return = true;
            }
        }
        return $return;
    } else {
        return true;
    }
}

function h_gdpr_user_file() {
    $user_id = osc_logged_user_id();
    $path = gdpr_cookie_user_data_path_zip('data_' . $user_id . '.zip');
    return $path;
}
