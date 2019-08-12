<?php

class ModelClubAddData extends PT_Model {


     public function addMember($data)
    {
        $date = $this->db->escape((string)$data['year']).'-'. $this->db->escape((string)$data['month']);
       
        $this->db->query("INSERT INTO " . DB_PREFIX . "member SET club_id = '" . $this->db->escape((string)$data['club_id']) . "', prefix = '" . $this->db->escape($data['prefix']) . "', project_id = '" . $this->db->escape((string)$data['project_id']) . "', date = '" . $date . "',induction = '" . $this->db->escape((string)$data['member_induct']) . "',unlist = '" . $this->db->escape((string)$data['member_unlist']) . "', net = '" . $this->db->escape((string)$data['net_growth']) . "', date_modified = NOW(), date_added = NOW()");
       
        return $this->db->lastInsertId();
    }


  


     public function addProject($data)
    {

        $date = $this->db->escape((string)$data['year']).'-'. $this->db->escape((string)$data['month']);
       
        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "projects SET club_id = '" . $this->db->escape((string)$data['club_id']) . "', date = '".$date. "',title = '" . $this->db->escape((string)$data['title']) . "',description = '" . $this->db->escape((string)$data['description']) . "',	amount = '" . $this->db->escape((string)$data['amount']) .  "',	no_of_beneficiary = '" . $this->db->escape((string)$data['no_of_beneficiary']) . "',points = '" . $this->db->escape((string)$data['points']) . "', date_added = NOW()");
        
        $project_id = $this->db->lastInsertId();
        $i=1;
        
        foreach ($this->request->post['image'] as $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "project_image SET project_id = '" . (int)$project_id . "', sort_order = '" . (int)$i++ . "', image = '" . $this->db->escape((string)$value) ."'");
        }

        foreach ($this->request->post['category'] as $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "project_to_category SET project_id = '" . (int)$project_id . "', category_id = '" . $this->db->escape((string)$value['category_id']) ."'");
        }


        $this->cache->delete('projects');

     return $project_id;
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

    public function getMemberProjectId($member_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "member WHERE member_id = '" . (int)$member_id . "'");

        if($query->num_rows) {
            return array(
                'member_id'     => $query->row['member_id'],
                'club_id'       => $query->row['club_id'],
                'prefix'        => $query->row['prefix'],
                'project_id'    => $query->row['project_id'],
                'date'          => $query->row['date'],
                'induction'     => $query->row['induction'],
                'unlist'        => $query->row['unlist'],
                'net'           => $query->row['net'],
                'review'        => $query->row['review'],
                'status'        => $query->row['status'],
                'date_added'    => $query->row['date_added'],
                'date_modified' => $query->row['date_modified']
            );
        } else {
            return;
        }
    }

     public function getTotalMemberById($club_id)
    {
        $query = $this->db->query("SELECT DISTINCT SUM(net) as total FROM " . DB_PREFIX . "member WHERE club_id = '" . (int)$club_id . "' AND review='1'");

        return $query->row['total'];
    }

     public function getMemberPoints($club_id)
    {
        $query = $this->db->query("SELECT sum(points) as total FROM " . DB_PREFIX . "member WHERE club_id = '" . (int)$club_id . "' AND review='1'");

        return $query->row['total'];
    }

     public function getTrfPoints($club_id)
    {
        $query = $this->db->query("SELECT sum(points) as total FROM " . DB_PREFIX . "trf WHERE club_id = '" . (int)$club_id . "' AND review='1'");

        return $query->row['total'];
    }

     public function getProjectPoints($club_id)
    {
        $query = $this->db->query("SELECT sum(points) as total FROM " . DB_PREFIX . "projects WHERE club_id = '" . (int)$club_id . "' AND review='1'");

        return $query->row['total'];
    }

    // exchange rate-------------------------------------------------------------------------------------------
    public function getExchangeRate()
    {
        $query = $this->db->query("SELECT rate FROM " . DB_PREFIX . "exchange_rate");

        return $query->row['rate'];
    }

    public function GetMemberProjectIds()
    {

        $query = $this->db->query("SELECT MAX(project_id) as pid FROM " . DB_PREFIX . "member");

        return $query->row['pid'];
    }

    public function createMemberId($member_id) {
        $project_info = $this->getMemberProjectId($member_id);

		if ($project_info && !$project_info['project_id']) {
			$query = $this->db->query("SELECT MAX(project_id) AS project_id FROM `" . DB_PREFIX . "member` WHERE prefix = '" . $this->db->escape($project_info['prefix']) . "'");

			if ($query->row['project_id']) {
				$project_id = $query->row['project_id'] + 1;
			} else {
				$project_id = 1;
			}

			$this->db->query("UPDATE `" . DB_PREFIX . "member` SET project_id = '" . (int)$project_id . "', prefix = '" . $this->db->escape($project_info['prefix']) . "' WHERE member_id = '" . (int)$member_id . "'");

			return $project_info['prefix'] . $project_id;
		}
    }

    // TRF -----------------------------------------------------------------------------------------------------------------------------

       public function addTrf($data)
    {

        $date = $this->db->escape((string)$data['year']).'-'. $this->db->escape((string)$data['month']);
       
        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "trf SET  club_id = '" . $this->db->escape((string)$data['club_id']) . "', prefix = '" . $this->db->escape($data['prefix']) . "', project_id = '" . $this->db->escape((string)$data['project_id']) . "',  date = '".$date. "',amount_inr = '" . $this->db->escape((string)$data['amount_inr']) . "',exchange_rate = '" . $this->db->escape((string)$data['exchange_rate']) . "',	amount_usd = '" . $this->db->escape((string)$data['amount_usd']) . "', date_modified = NOW(), date_added = NOW()");

        return $this->db->lastInsertId();
    }

    public function GetTrfProjectIds()
    {

        $query = $this->db->query("SELECT MAX(project_id) as pid FROM " . DB_PREFIX . "trf");

        return $query->row['pid'];
    }

    public function createTRfId($trf_id) {

        $project_info = $this->getTrfProjectId($trf_id);

		if ($project_info && !$project_info['project_id']) {
			$query = $this->db->query("SELECT MAX(project_id) AS project_id FROM `" . DB_PREFIX . "trf` WHERE prefix = '" . $this->db->escape($project_info['prefix']) . "'");

			if ($query->row['project_id']) {
				$project_id = $query->row['project_id'] + 1;
			} else {
				$project_id = 1;
			}

			$this->db->query("UPDATE `" . DB_PREFIX . "trf` SET project_id = '" . (int)$project_id . "', prefix = '" . $this->db->escape($project_info['prefix']) . "' WHERE trf_id = '" . (int)$trf_id . "'");

			return $project_info['prefix'] . $project_id;
		}
    }

    public function getTrfProjectId($trf_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "trf WHERE trf_id = '" . (int)$trf_id . "'");

        if($query->num_rows) {
            return array(
                'trf_id'     => $query->row['trf_id'],
                'club_id'       => $query->row['club_id'],
                'prefix'        => $query->row['prefix'],
                'project_id'    => $query->row['project_id'],
                'date'          => $query->row['date'],
                'amount_inr'     => $query->row['amount_inr'],
                'exchange_rate'        => $query->row['exchange_rate'],
                'amount_usd'           => $query->row['amount_usd'],
                'review'        => $query->row['review'],
                'status'        => $query->row['status'],
                'date_added'    => $query->row['date_added'],
                'date_modified' => $query->row['date_modified']
            );
        } else {
            return;
        }
    }

}
