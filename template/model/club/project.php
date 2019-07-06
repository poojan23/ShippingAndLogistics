<?php

class ModelClubProject extends PT_Model {

    public function getCategories()
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category WHERE status = '1' ORDER BY sort_order ASC");

        return $query->rows;
    }

     public function addProject($data)
    {
        
        // print_r($data); exit;

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
        //return $query;
    }
    
     public function getProjectById($club_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "projects WHERE club_id = '" . (int)$club_id . "' AND review = '1'");

        return $query->rows;
    }

    public function getProject($project_id)
    {
        
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "projects WHERE project_id = '" . (int)$project_id . "'");
        return $query->rows;
    }

    public function getCategoryByProjectId($project_id)
    {
        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "project_to_category pc LEFT JOIN " . DB_PREFIX . "category c ON (pc.category_id = c.category_id) WHERE project_id = '" . (int)$project_id . "'");
        return $query->rows;
    }

    public function getImageByProjectId($project_id)
    {
        
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "project_image WHERE project_id = '" . (int)$project_id . "'");
        return $query->rows;
    }

}
