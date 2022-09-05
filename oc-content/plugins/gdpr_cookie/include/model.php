<?php

/*
 * Copyright (C) 2018 Puiu Calin
 * This program is a commercial software: is forbidden to use this software without licence, 
 * on multiple installations, and by purchasing from other source than those authorized for the sale of software.
 * Unauthorized copying of this file, via any medium is strictly prohibited
 */

class GdprCookie extends DAO {

    private static $instance;

    public static function newInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    function __construct() {
        parent::__construct();
    }

    public function import($file) {
        $path = osc_plugin_resource($file);
        $sql = file_get_contents($path);
        if (!$this->dao->importSQL($sql)) {
            throw new Exception("Error importSQL::Model GdprCookie<br>" . $file);
        }
    }

    public function install() {
        $this->import('gdpr_cookie/database/struct.sql');
    }

    public function uninstall() {
        $this->dao->query(sprintf('DROP TABLE %s', $this->getTable()));
    }

    public function getTable() {
        return DB_TABLE_PREFIX . 't_gdpr';
    }

    public function getTableLog() {
        return DB_TABLE_PREFIX . 't_log';
    }

    public function insert_preferences($data, $user_id) {
        $get = $this->get_preferences($user_id);
        if (empty($get)) {
            $this->dao->insert($this->getTable(), array(
                'g_user_id' => $user_id,
                'b_gdpr' => 1,
                's_value' => $data
            ));
        } else {
            $this->dao->update(
                    $this->getTable(), array('s_value' => $data), array('g_user_id' => $user_id)
            );
        }
    }

    public function get_preferences($user_id) {
        $this->dao->select('*');
        $this->dao->from($this->getTable());
        $this->dao->where('g_user_id', $user_id);
        $result = $this->dao->get();
        if (!$result->row()) {
            return NULL;
        }
        return $result->row();
    }

    public function delete_preferences($user_id) {
        $this->dao->delete($this->getTable(), array('g_user_id' => $user_id));
    }

    public function this_column_exists($table, $name) {
        $sql = "
			SELECT * FROM information_schema.COLUMNS 
			WHERE TABLE_SCHEMA = DATABASE()
			AND TABLE_NAME = '" . DB_TABLE_PREFIX . $table . "'
			AND COLUMN_NAME = '" . $name . "'
		";
        $result = $this->dao->query($sql);
        $result = $result->result();
        if (empty($result)) {
            return false;
        } else {
            return true;
        }
    }

    public function get_user_log($user_id) {
        $this->dao->select('s_section, s_action, s_data, s_ip');
        $this->dao->from($this->getTableLog());
        $this->dao->where('fk_i_who_id', $user_id);
        $result = $this->dao->get();
        if (!$result->result()) {
            return NULL;
        }
        return $result->result();
    }

    public function get_user_data($user_id) {
        $this->dao->select('s_name, s_username, s_password, s_email, s_website, s_phone_land, s_phone_mobile, s_access_ip,s_address, s_country, s_region, s_city');
        $this->dao->from(DB_TABLE_PREFIX . 't_user');
        $this->dao->where('pk_i_id', $user_id);
        $result = $this->dao->get();
        if (!$result->row()) {
            return NULL;
        }
        return $this->extendData_user($result->row(), $user_id);
    }

    private function extendData_user($user, $user_id) {
        $this->dao->select('fk_c_locale_code, s_info');
        $this->dao->from(DB_TABLE_PREFIX . 't_user_description');
        $this->dao->where('fk_i_user_id', $user_id);
        $result = $this->dao->get();
        $descriptions = $result->result();

        $user['locale'] = array();
        foreach ($descriptions as $sub_row) {
            $user['locale'][$sub_row['fk_c_locale_code']] = $sub_row;
        }
        return $user;
    }

    public function get_comments($user_id) {
        $this->dao->select('s_title, s_body, fk_i_item_id');
        $this->dao->from(DB_TABLE_PREFIX . 't_item_comment');
        $this->dao->where('fk_i_user_id', $user_id);
        $result = $this->dao->get();
        if ($result == false) {
            return array();
        }

        return $result->result();
    }

    public function get_items($user_id, $start = null, $end = null) {
        $this->dao->select('l.s_country, l.s_region, l.s_city, l.s_city_area, l.s_address,i.pk_i_id, i.fk_i_category_id, i.s_contact_email, i.s_contact_name, i.i_price, i.fk_c_currency_code');
        $this->dao->from(DB_TABLE_PREFIX . 't_item i, ' . DB_TABLE_PREFIX . 't_item_location l');
        $this->dao->where('l.fk_i_item_id = i.pk_i_id');
        $array_where = array(
            'i.fk_i_user_id' => $user_id
        );
        $this->dao->where($array_where);
        $this->dao->orderBy('i.pk_i_id', 'DESC');
        if ($end != null) {
            $this->dao->limit($start, $end);
        } else {
            if ($start > 0) {
                $this->dao->limit($start);
            }
        }

        $result = $this->dao->get();
        if ($result == false) {
            return array();
        }
        $items = $result->result();

        return $this->extendData_items($items);
    }

    public function extendData_items($items) {

        $results = array();
        $prefLocale = osc_current_user_locale();
        foreach ($items as $item) {
            $this->dao->select('s_title, s_description, fk_c_locale_code');
            $this->dao->from(DB_TABLE_PREFIX . 't_item_description');
            $this->dao->where(DB_TABLE_PREFIX . 't_item_description.fk_i_item_id', $item['pk_i_id']);
            $result = $this->dao->get();
            $descriptions = $result->result();
            $item['locale'] = array();
            foreach ($descriptions as $desc) {
                if ($desc['s_title'] != "" || $desc['s_description'] != "") {
                    $item['locale'][$desc['fk_c_locale_code']] = $desc;
                }
            }
            if (isset($item['locale'][$prefLocale])) {
                $item['s_title'] = $item['locale'][$prefLocale]['s_title'];
                $item['s_description'] = $item['locale'][$prefLocale]['s_description'];
            } else {
                $data = current($item['locale']);
                $item['s_title'] = $data['s_title'];
                $item['s_description'] = $data['s_description'];
                unset($data);
            }
            $this->dao->select('cd.s_name as category_name');
            $this->dao->from(DB_TABLE_PREFIX . 't_category_description as cd');
            $this->dao->where('cd.fk_i_category_id', $item['fk_i_category_id']);
            $this->dao->where('cd.fk_c_locale_code', $prefLocale);
            $result = $this->dao->get();
            $extraFields = $result->row();

            foreach ($extraFields as $key => $value) {
                $item[$key] = $value;
            }

            $this->dao->select('m.s_value, mt.s_name');
            $this->dao->from(DB_TABLE_PREFIX . 't_item_meta as m');
            $this->dao->join(DB_TABLE_PREFIX . 't_meta_fields  as mt ', 'm.fk_i_field_id = mt.pk_i_id', 'RIGHT');
            $this->dao->where('m.fk_i_item_id', $item['pk_i_id']);
            $meta = $this->dao->get();
            $meta = $meta->result();
            if (!empty($meta)) {
                $item['custom_fields'] = $meta;
            }
            $results[] = $item;
        }
        return $results;
    }

    public function export_data_info($user_id, $data, $date) {
        if ($date) {
            $date = date("Y-m-d H:i:s");
        }
        return $this->dao->update(
                        $this->getTable(), array('download_data' => $data, 'req_date' => $date), array('g_user_id' => $user_id)
        );
    }

    public function get_records_cron() {
        $days = 10;
        $s_days = osc_get_preference('delete_zip_days', 'gdpr_cookie');
        if (is_numeric($s_days)) {
            $days = $s_days;
        }
        $date = date("Y-m-d H:i:s");
        $this->dao->select('*');
        $this->dao->from($this->getTable());
        $this->dao->where("req_date + INTERVAL '$days' DAY < '$date'");
        $result = $this->dao->get();
        if (!$result->result()) {
            return NULL;
        }
        return $result->result();
    }

}
