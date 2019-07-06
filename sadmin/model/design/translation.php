<?php
class ModelDesignTranslation extends PT_Model {
	public function addTranslation($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "translation` SET `store_id` = '" . (int)$data['store_id'] . "', `language_id` = '" . (int)$data['language_id'] . "', `route` = '" . $this->db->escape((string)$data['route']) . "', `key` = '" . $this->db->escape((string)$data['key']) . "', `value` = '" . $this->db->escape((string)$data['value']) . "', `date_added` = NOW()");
	}
		
	public function editTranslation($translation_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "translation` SET `store_id` = '" . (int)$data['store_id'] . "', `language_id` = '" . (int)$data['language_id'] . "', `route` = '" . $this->db->escape((string)$data['route']) . "', `key` = '" . $this->db->escape((string)$data['key']) . "', `value` = '" . $this->db->escape((string)$data['value']) . "' WHERE `translation_id` = '" . (int)$translation_id . "'");
	}

	public function deleteTranslation($translation_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "translation` WHERE `translation_id` = '" . (int)$translation_id . "'");
	}

	public function getTranslation($translation_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "translation` WHERE `translation_id` = '" . (int)$translation_id . "'");

		return $query->row;
	}
	
	public function getTranslations() {
		$query = $this->db->query("SELECT t.*,l.name FROM `" . DB_PREFIX . "translation` t LEFT JOIN `" . DB_PREFIX . "language` l ON t.language_id = l.language_id");

		return $query->rows;
	}	

	public function getTotalTranslations() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "translation`");
		
		return $query->row['total'];
	}	
}
