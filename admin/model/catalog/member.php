<?php

class ModelCatalogMember extends PT_Model
{
    public function editMember($member_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "member SET  induction = '" . $this->db->escape((string)$data['member_induct']) . "',notes = '" . $this->db->escape((string)$data['notes']) . "', unlist = '" . $this->db->escape((string)$data['member_unlist']) . "', net = '" . $this->db->escape((string)$data['net_growth']) . "',review = '" . $this->db->escape((string)$data['review']) . "' WHERE member_id = '" . (int)$member_id . "'");
    }
    
    public function getMember($member_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "member WHERE member_id = '" . (int)$member_id . "'");

        return $query->row;
    }
    public function getMembers()
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "member WHERE status = '1'");

        return $query->rows;
    }
}
