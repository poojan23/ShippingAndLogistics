<?php

class ModelClubTrf extends PT_Model {


     public function addTrf($data)
    {

        $date = $this->db->escape((string)$data['year']).'-'. $this->db->escape((string)$data['month']);
       
        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "trf SET  club_id = '" . $this->db->escape((string)$data['club_id']) . "', date = '".$date. "',amount_inr = '" . $this->db->escape((string)$data['amount_inr']) . "',exchange_rate = '" . $this->db->escape((string)$data['exchange_rate']) . "',	amount_usd = '" . $this->db->escape((string)$data['amount_usd']) . "',points = '" . $this->db->escape((string)$data['points']) . "', date_added = NOW()");

        return $query;
    }

    // public function editClub($club_id, $data)
    // {
    //     $this->db->query("UPDATE " . DB_PREFIX . "club SET  date = '" . $this->db->escape((string)$data['date']) . "',club_name = '" . $this->db->escape((string)$data['name']) . "',president = '" . $this->db->escape((string)$data['president']) . "',district_secretary = '" . $this->db->escape((string)$data['secretary']) . "',assistant_governor = '" . $this->db->escape((string)$data['governor']) . "',mobile = '" . $this->db->escape((string)$data['mobile']) . "',email = '" . $this->db->escape((string)$data['email']) . "',website = '" . $this->db->escape((string)$data['website']) . "',status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW() WHERE club_id = '" . (int)$club_id . "'");

    //     if (isset($data['image'])) {
    //         $this->db->query("UPDATE " . DB_PREFIX . "club SET image = '" . $this->db->escape((string)$data['image']) . "' WHERE club_id = '" . (int)$club_id . "'");
    //     }
    // }

    // public function deleteClub($club_id)
    // {
    //     $this->db->query("DELETE FROM " . DB_PREFIX . "club WHERE club_id = '" . (int)$club_id . "'");

    //     $this->cache->delete('club');
    // }

    // public function getMember($member_id)
    // {
    //     $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "member WHERE member_id = '" . (int)$member_id . "'");

    //     return $query->row;
    // }
     public function getTrfById($club_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "trf WHERE club_id = '" . (int)$club_id . "' AND review = '1'");

        return $query->rows;
    }

     public function getTrf($trf_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "trf WHERE trf_id = '" . (int)$trf_id . "'");

        return $query->row;
    }
     public function getTotalTrfById($club_id)
    {
        $query = $this->db->query("SELECT DISTINCT SUM(amount_usd) as total FROM " . DB_PREFIX . "trf WHERE club_id = '" . (int)$club_id . "'");

        return $query->row['total'];
    }
    // public function getMembers()
    // {
    //     $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "club WHERE status = '1'");

    //     return $query->rows;
    // }

}
