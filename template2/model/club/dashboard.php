<?php

class ModelClubDashboard extends PT_Model {

    public function getTotalTrf($club_id)
    {
        $query = $this->db->query("SELECT DISTINCT SUM(amount_usd) as total FROM " . DB_PREFIX . "trf WHERE club_id = '" . (int)$club_id . "' AND review=1");

        return $query->row['total'];
    }

    public function getTotalProject($club_id)
    {
        $query = $this->db->query("SELECT DISTINCT COUNT(title) as total FROM " . DB_PREFIX . "projects WHERE club_id = '" . (int)$club_id . "' AND review='1'");

        return $query->row['total'];
    }

    public function getTotalMember($club_id)
    {
    
        $query = $this->db->query("SELECT SUM(net) as total FROM " . DB_PREFIX . "member WHERE club_id = '" . (int)$club_id . "' AND review = '1'");

        return $query->row['total'];
    }

    public function getTotalClub($club_id)
    {
        $query = $this->db->query("SELECT DISTINCT COUNT(club_id) as total FROM " . DB_PREFIX . "club WHERE parent_id = '" . (int)$club_id . "'");

        return $query->row['total'];
    }

    public function getMemberTable($club_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "member WHERE club_id = '" . (int)$club_id . "' AND review='1' ORDER BY date_added DESC LIMIT 5");

        return $query->rows;
    }

    public function getProjectTable($club_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "projects WHERE club_id = '" . (int)$club_id . "' AND review='1' ORDER BY date_added DESC LIMIT 5");

        return $query->rows;
    }

    public function getTrfTable($club_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "trf WHERE club_id = '" . (int)$club_id . "' AND review='1' ORDER BY date_added DESC LIMIT 5");

        return $query->rows;
    }

     public function getMemberById($club_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "member WHERE club_id = '" . (int)$club_id . "' AND review='1'");

        return $query->rows;
    }

     public function getMember($member_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "member WHERE member_id = '" . (int)$member_id . "' AND review='1'");

        return $query->row;
    }

     public function getTotalMemberById($club_id)
    {
        $query = $this->db->query("SELECT DISTINCT SUM(net) as totalmembers FROM " . DB_PREFIX . "member WHERE club_id = '" . (int)$club_id . "' AND review='1'");

        return $query->row;
    }

    // ----------------------------------------home page data-------------------------------------------------
        public function getTotalTrfHome()
    {
        $query = $this->db->query("SELECT DISTINCT SUM(amount_usd) as total FROM " . DB_PREFIX . "trf WHERE review=1");

        return $query->row['total'];
    }

    public function getTotalMemberHome()
    {
    
        $query = $this->db->query("SELECT SUM(net) as total FROM " . DB_PREFIX . "member WHERE review = '1'");

        return $query->row['total'];
    }

    public function getTotalClubHome()
    {
        $query = $this->db->query("SELECT DISTINCT COUNT(club_id) as total FROM " . DB_PREFIX . "club");

        return $query->row['total'];
    }
    
    public function getTotalProjectHome()
    {
        $query = $this->db->query("SELECT COUNT(project_id) as total,SUM(amount) as amount,SUM(no_of_beneficiary) as nob  FROM " . DB_PREFIX . "projects WHERE review='1'");

        return $query->row;
    }

}
