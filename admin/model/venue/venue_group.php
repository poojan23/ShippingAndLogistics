<?php

class ModelVenueVenueGroup extends PT_Model
{
    public function addVenueGroup($data)
    {
        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "venue_group SET  name = '" . $this->db->escape((string)$data['group_name']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW(), date_added = NOW()");
        $venue_group_id = $this->db->lastInsertId();
        # SEO URL
        if (isset($data['venue_seo_url'])) {
           
            $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET  query = 'venue_group_id=" . (int)$venue_group_id . "', keyword = '" . $this->db->escape((string)$data['venue_seo_url']) . "', push = '" . $this->db->escape('url=venue/venue_group&venue_group_id=' . (int)$venue_group_id) . "'");
              
        }
        return $query;
    }

    public function editVenueGroup($venue_group_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "venue_group SET name = '" . $this->db->escape((string)$data['group_name']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW() WHERE venue_group_id = '" . (int)$venue_group_id . "'");
    
        # SEO URL
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'venue_group_id=" . (int)$venue_group_id . "'");
        if (isset($data['venue_seo_url'])) {
           
            $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET  query = 'venue_group_id=" . (int)$venue_group_id . "', keyword = '" . $this->db->escape((string)$data['venue_seo_url']) . "', push = '" . $this->db->escape('url=venue/venue_group&venue_group_id=' . (int)$venue_group_id) . "'");
              
        }
    }

    public function deleteVenueGroup($venue_group_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "venue_group WHERE venue_group_id = '" . (int)$venue_group_id . "'");

        $this->cache->delete('venue_group');
    }

    public function getVenueGroup($venue_group_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "venue_group WHERE venue_group_id = '" . (int)$venue_group_id . "'");

        return $query->row;
    }
    public function getVenueGroups()
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "venue_group");

        return $query->rows;
    }
        public function getVenueGroupSeoUrls($venue_group_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'venue_group_id=" . (int)$venue_group_id . "'");

        return $query->row;
    }
}
