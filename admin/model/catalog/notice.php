<?php

class ModelCatalogNotice extends PT_Model
{
    public function addNotice($data) {
        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "notice SET date = '" . $this->db->escape($data['date']) . "',title = '" . $this->db->escape($data['title']) . "',filename = '" . $this->db->escape($data['filename']) . "', mask = '" . $this->db->escape($data['mask']) . "', date_modified = NOW(), date_added = NOW()");

        return $query;
    }

    public function editNotice($notice_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "notice SET date = '" . $this->db->escape($data['date']) . "',title = '" . $this->db->escape($data['title']) . "',filename = '" . $this->db->escape($data['filename']) . "', mask = '" . $this->db->escape($data['mask']) . "', date_modified = NOW() WHERE notice_id = '" . (int)$notice_id . "'");
    }

    public function deleteNotice($notice_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "notice WHERE notice_id = '" . (int)$notice_id . "'");
    }

    public function getNotice($notice_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "notice WHERE notice_id = '" . (int)$notice_id . "'");

        return $query->row;
    }

    public function getNotices() {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "notice");

        return $query->rows;
    }
}
