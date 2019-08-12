<?php

class ModelClubMember extends PT_Model {


     public function addMember($data)
    {

        $date = $this->db->escape((string)$data['year']).'-'. $this->db->escape((string)$data['month']);
       
        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "member SET  club_id = '" . $this->db->escape((string)$data['club_id']) . "', date = '".$date. "',induction = '" . $this->db->escape((string)$data['member_induct']) . "',unlist = '" . $this->db->escape((string)$data['member_unlist']) . "',net = '" . $this->db->escape((string)$data['net_growth']) . "',points = '" . $this->db->escape((string)$data['point_accumulate']) . "', date_added = NOW()");

        return $query;
    }

     public function getMemberById($club_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "member WHERE club_id = '" . (int)$club_id . "' AND review = '1'");

        return $query->rows;
    }

     public function getMember($member_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "member WHERE member_id = '" . (int)$member_id . "'");

        return $query->row;
    }

     public function getTotalMemberById($club_id)
    {
        $query = $this->db->query("SELECT DISTINCT SUM(net) as total FROM " . DB_PREFIX . "member  WHERE club_id = '" . (int)$club_id . "' AND review = '1'");

        return $query->row['total'];
    }
}
