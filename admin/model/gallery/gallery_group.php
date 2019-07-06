<?php

class ModelGalleryGalleryGroup extends PT_Model
{
    public function addGalleryGroup($data)
    {
        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "gallery_group SET  name = '" . $this->db->escape((string)$data['group_name']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW(), date_added = NOW()");
        $gallery_group_id = $this->db->lastInsertId();
        # SEO URL
        if (isset($data['gallery_seo_url'])) {
           
            $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET  query = 'gallery_group_id=" . (int)$gallery_group_id . "', keyword = '" . $this->db->escape((string)$data['gallery_seo_url']) . "', push = '" . $this->db->escape('url=gallery/gallery_group&gallery_group_id=' . (int)$gallery_group_id) . "'");
              
        }
        return $query;
    }

    public function editGalleryGroup($gallery_group_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "gallery_group SET name = '" . $this->db->escape((string)$data['group_name']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW() WHERE gallery_group_id = '" . (int)$gallery_group_id . "'");
    
        # SEO URL
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'gallery_group_id=" . (int)$gallery_group_id . "'");
        if (isset($data['gallery_seo_url'])) {
           
            $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET  query = 'gallery_group_id=" . (int)$gallery_group_id . "', keyword = '" . $this->db->escape((string)$data['gallery_seo_url']) . "', push = '" . $this->db->escape('url=gallery/gallery_group&gallery_group_id=' . (int)$gallery_group_id) . "'");
              
        }
    }

    public function deleteGalleryGroup($gallery_group_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "gallery_group WHERE gallery_group_id = '" . (int)$gallery_group_id . "'");

        $this->cache->delete('gallery_group');
    }

    public function getGalleryGroup($gallery_group_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "gallery_group WHERE gallery_group_id = '" . (int)$gallery_group_id . "'");

        return $query->row;
    }
    public function getGalleryGroups()
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "gallery_group");

        return $query->rows;
    }
        public function getGalleryGroupSeoUrls($gallery_group_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'gallery_group_id=" . (int)$gallery_group_id . "'");

        return $query->row;
    }
}
