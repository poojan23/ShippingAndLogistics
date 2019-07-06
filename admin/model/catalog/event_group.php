<?php

class ModelCatalogEventGroup extends PT_Model
{
    public function addEventGroup($data)
    {
        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "event_group SET  name = '" . $this->db->escape((string)$data['name']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW(), date_added = NOW()");
        $event_group_id = $this->db->lastInsertId();
        # SEO URL
        if (isset($data['event_seo_url'])) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET  query = 'event_group_id=" . (int)$event_group_id . "', keyword = '" . $this->db->escape((string)$data['event_seo_url']) . "', push = '" . $this->db->escape('url=event/event_group&event_group_id=' . (int)$event_group_id) . "'");
              
        }
        return $query;
    }

    public function editEventGroup($event_group_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "event_group SET name = '" . $this->db->escape((string)$data['name']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW() WHERE event_group_id = '" . (int)$event_group_id . "'");
    
        # SEO URL
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'event_group_id=" . (int)$event_group_id . "'");
        if (isset($data['event_seo_url'])) {
           
            $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET  query = 'event_group_id=" . (int)$event_group_id . "', keyword = '" . $this->db->escape((string)$data['event_seo_url']) . "', push = '" . $this->db->escape('url=event/event_group&event_group_id=' . (int)$event_group_id) . "'");
              
        }
    }

    public function deleteEventGroup($event_group_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "event_group WHERE event_group_id = '" . (int)$event_group_id . "'");

        $this->cache->delete('event_group');
    }

    public function getEventGroup($event_group_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "event_group WHERE event_group_id = '" . (int)$event_group_id . "'");

        return $query->row;
    }
    public function getEventGroups()
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "event_group");

        return $query->rows;
    }
        public function getEventGroupSeoUrls($event_group_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'event_group_id=" . (int)$event_group_id . "'");

        return $query->row;
    }
}
