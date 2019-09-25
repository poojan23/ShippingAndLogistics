<?php

class ModelCatalogDownload extends PT_Model
{
    public function addDownload($data) {
        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "download SET name = '" . $this->db->escape($data['name']) . "',filename = '" . $this->db->escape($data['filename']) . "', mask = '" . $this->db->escape($data['mask']) . "', date_modified = NOW(), date_added = NOW()");

        return $query;
    }

    public function editDownload($download_id, $data) {
        $query = $this->db->query("UPDATE " . DB_PREFIX . "download SET name = '" . $this->db->escape($data['name']) . "',filename = '" . $this->db->escape($data['filename']) . "', mask = '" . $this->db->escape($data['mask']) . "', date_modified = NOW() WHERE download_id = '" . (int)$download_id . "'");
        return $query;
    }

    public function deleteDownload($download_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "download WHERE download_id = '" . (int)$download_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "download_description WHERE download_id = '" . (int)$download_id . "'");
    }

    public function getDownload($download_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "download WHERE download_id = '" . (int)$download_id . "'");

        return $query->row;
    }

    public function getDownloads() {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "download");

        return $query->rows;
    }

    public function getDownloadByName($name) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "download_description WHERE name = '" . $this->db->escape($name) . "'");

        return $query->row;
    }

    public function getTotalDownloads() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "download");

        return $query->row['total'];
    }

    public function getSumDownloads() {
        $query = $this->db->query("SELECT SUM(viewed) AS sum FROM " . DB_PREFIX . "download");

        return $query->row['sum'];
    }
}
