<?php

class ModelLaunchLaunch extends PT_Model
{
    public function addUser($data)
    {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "member` WHERE member_id = '1'");

        $this->db->query("INSERT INTO `" . DB_PREFIX . "member` SET member_id = '1', member_group_id = '1', email = '" . $this->db->escape($data['email']) . "', salt = '', password = '" . $this->db->escape(password_hash(html_entity_decode($data['password'], ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', status = '1', date_added = NOW()");

        $this->db->query("INSERT INTO `" . DB_PREFIX . "member_address` SET member_id = '1', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "'");

        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `key` = 'config_email'");

        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET `code` = 'config', `key` = 'config_email', value = '" . $this->db->escape($data['email']) . "'");

        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `key` = 'config_encryption'");

        $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` SET `code` = 'config', `key` = 'config_encryption', value = '" . $this->db->escape(token(1024)) . "'");

        $this->db->query("UPDATE `" . DB_PREFIX . "product` SET `viewed` = '0'");

        # set the current years prefix
        $this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `value` = 'INV-" . date('Y') . "-00' WHERE `key` = 'config_invoice_prefix'");
    }

    public function getCountry($country_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "country WHERE country_id = '" . (int)$country_id . "'");

        return $query->row;
    }

    public function getCountries($data = array())
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "country";

        $sort_data = array(
            'name',
            'iso_code_2',
            'iso_code_3'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getZonesByCountryId($country_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE country_id = '" . (int)$country_id . "' AND status = '1' ORDER BY name");

        return $query->rows;
    }
}
