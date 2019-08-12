<?php

class ModelCustomerDashboard extends PT_Model {

    public function getTotalTrf($customer_id)
    {
        $query = $this->db->query("SELECT DISTINCT SUM(amount_usd) as total FROM " . DB_PREFIX . "trf WHERE customer_id = '" . (int)$customer_id . "' AND review=1");

        return $query->row['total'];
    }

    public function getTotalProject($customer_id)
    {
        $query = $this->db->query("SELECT DISTINCT COUNT(title) as total FROM " . DB_PREFIX . "projects WHERE customer_id = '" . (int)$customer_id . "' AND review='1'");

        return $query->row['total'];
    }

    public function getTotalMember($customer_id)
    {
    
        $query = $this->db->query("SELECT SUM(net) as total FROM " . DB_PREFIX . "member WHERE customer_id = '" . (int)$customer_id . "' AND review = '1'");

        return $query->row['total'];
    }

    public function getTotalClub($customer_id)
    {
        $query = $this->db->query("SELECT DISTINCT COUNT(customer_id) as total FROM " . DB_PREFIX . "customer WHERE parent_id = '" . (int)$customer_id . "'");

        return $query->row['total'];
    }

    public function getMemberTable($customer_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "member WHERE customer_id = '" . (int)$customer_id . "' AND review='1' ORDER BY date_added DESC LIMIT 5");

        return $query->rows;
    }

    public function getProjectTable($customer_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "projects WHERE customer_id = '" . (int)$customer_id . "' AND review='1' ORDER BY date_added DESC LIMIT 5");

        return $query->rows;
    }

    public function getTrfTable($customer_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "trf WHERE customer_id = '" . (int)$customer_id . "' AND review='1' ORDER BY date_added DESC LIMIT 5");

        return $query->rows;
    }

     public function getMemberById($customer_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "member WHERE customer_id = '" . (int)$customer_id . "' AND review='1'");

        return $query->rows;
    }

     public function getMember($member_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "member WHERE member_id = '" . (int)$member_id . "' AND review='1'");

        return $query->row;
    }

     public function getTotalMemberById($customer_id)
    {
        $query = $this->db->query("SELECT DISTINCT SUM(net) as totalmembers FROM " . DB_PREFIX . "member WHERE customer_id = '" . (int)$customer_id . "' AND review='1'");

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
        $query = $this->db->query("SELECT DISTINCT COUNT(customer_id) as total FROM " . DB_PREFIX . "customer");

        return $query->row['total'];
    }
    
    public function getTotalProjectHome()
    {
        $query = $this->db->query("SELECT COUNT(project_id) as total,SUM(amount) as amount,SUM(no_of_beneficiary) as nob  FROM " . DB_PREFIX . "projects WHERE review='1'");

        return $query->row;
    }

}
