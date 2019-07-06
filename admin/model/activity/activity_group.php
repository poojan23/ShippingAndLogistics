<?php

class ModelActivityActivityGroup extends PT_Model
{
    public function addActivityGroup($data)
    {
        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "activity_group SET  name = '" . $this->db->escape((string)$data['group_name']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW(), date_added = NOW()");
        $activity_group_id = $this->db->lastInsertId();
        # SEO URL
        if (isset($data['activity_seo_url'])) {
           
            $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET  query = 'activity_group_id=" . (int)$activity_group_id . "', keyword = '" . $this->db->escape((string)$data['activity_seo_url']) . "', push = '" . $this->db->escape('url=activity/activity_group&activity_group_id=' . (int)$activity_group_id) . "'");
              
        }
        return $query;
    }

    public function editActivityGroup($activity_group_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "activity_group SET name = '" . $this->db->escape((string)$data['group_name']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW() WHERE activity_group_id = '" . (int)$activity_group_id . "'");
    
        # SEO URL
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'activity_group_id=" . (int)$activity_group_id . "'");
        if (isset($data['activity_seo_url'])) {
           
            $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET  query = 'activity_group_id=" . (int)$activity_group_id . "', keyword = '" . $this->db->escape((string)$data['activity_seo_url']) . "', push = '" . $this->db->escape('url=activity/activity_group&activity_group_id=' . (int)$activity_group_id) . "'");
              
        }
    }

    public function deleteActivityGroup($activity_group_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "activity_group WHERE activity_group_id = '" . (int)$activity_group_id . "'");

        $this->cache->delete('activity_group');
    }

    public function getActivityGroup($activity_group_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "activity_group WHERE activity_group_id = '" . (int)$activity_group_id . "'");

        return $query->row;
    }
    public function getActivityGroups()
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "activity_group");

        return $query->rows;
    }
        public function getActivityGroupSeoUrls($activity_group_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'activity_group_id=" . (int)$activity_group_id . "'");

        return $query->row;
    }
}
