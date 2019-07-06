<?php

class ModelSportSportGroup extends PT_Model
{
    public function addSportGroup($data)
    {
        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "sport_group SET  name = '" . $this->db->escape((string)$data['group_name']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW(), date_added = NOW()");
        $sport_group_id = $this->db->lastInsertId();
        # SEO URL
        if (isset($data['sport_seo_url'])) {
           
            $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET  query = 'sport_group_id=" . (int)$sport_group_id . "', keyword = '" . $this->db->escape((string)$data['sport_seo_url']) . "', push = '" . $this->db->escape('url=sport/sport_group&sport_group_id=' . (int)$sport_group_id) . "'");
              
        }
        return $query;
    }

    public function editSportGroup($sport_group_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "sport_group SET name = '" . $this->db->escape((string)$data['group_name']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW() WHERE sport_group_id = '" . (int)$sport_group_id . "'");
    
        # SEO URL
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'sport_group_id=" . (int)$sport_group_id . "'");
        if (isset($data['sport_seo_url'])) {
           
            $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET  query = 'sport_group_id=" . (int)$sport_group_id . "', keyword = '" . $this->db->escape((string)$data['sport_seo_url']) . "', push = '" . $this->db->escape('url=sport/sport_group&sport_group_id=' . (int)$sport_group_id) . "'");
              
        }
    }

    public function deleteSportGroup($sport_group_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "sport_group WHERE sport_group_id = '" . (int)$sport_group_id . "'");

        $this->cache->delete('sport_group');
    }

    public function getSportGroup($sport_group_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "sport_group WHERE sport_group_id = '" . (int)$sport_group_id . "'");

        return $query->row;
    }
    public function getSportGroups()
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "sport_group");

        return $query->rows;
    }
        public function getSportGroupSeoUrls($sport_group_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'sport_group_id=" . (int)$sport_group_id . "'");

        return $query->row;
    }
}
