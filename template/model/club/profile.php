<?php

class ModelClubProfile extends PT_Model {

    public function getProfile($club_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "club WHERE club_id = '" . (int)$club_id . "'");

        return $query->row;
    }

    public function editProfile($club_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "club SET  mobile = '" . $this->db->escape((string)$data['mobile']) . "',website = '" . $this->db->escape((string)$data['website']) . "',date_modified = NOW() WHERE club_id = '" . (int)$club_id . "'");

        if ($data['password']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "user` SET  password = '" . $this->db->escape(password_hash(html_entity_decode($data['password'], ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "' WHERE club_id = '" . (int)$club_id . "'");
		}
    }
    
}
