<?php

class ModelToolOnline extends PT_Model
{
    public function addOnline($ip, $url, $referer)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "unique_visitor` WHERE `date` = '" . $this->db->escape(date('Y-m-d')) . "'");

        if ($query->num_rows) {
            if (!preg_match('/' . $ip . '/i', $query->row['ip'])) {
                $newIp = "$query->row['ip'] $ip";

                $this->db->query("UPDATE `" . DB_PREFIX . "unique_visitor` SET `ip` = '" . $newIp . "', `view` = '`view` + 1' WHERE `date` = '" . $this->db->escape(date('Y-m-d')) . "'");
            }
        } else {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "unique_visitor` SET `date` = '" . $this->db->escape(date('Y-m-d')) . "', `ip` = '" . $this->db->escape($ip) . "', `url` = '" . $this->db->escape($url) . "', `referer` = '" . $this->db->escape($referer) . "', date_added = NOW()");
        }
    }

    public function getTotalOnlines()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "unique_visitor");

        return $query->row['total'];
    }
}
