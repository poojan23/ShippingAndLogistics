<?php

class ModelToolCrawler extends PT_Model
{
    public function addCrawler($ip, $url, $referer)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "crawler SET ip = '" . $this->db->escape($ip) . "', url = '" . $this->db->escape($url) . "', referer = '" . $this->db->escape($referer) . "', date_added = NOW()");

        return $this->db->lastInsertId();
    }

    public function getCrawlers()
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "crawler`");

        return $query->rows;
    }
}
