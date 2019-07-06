<?php

class ModelCatalogInformation extends PT_Model
{
    public function addInformation($data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "information SET  sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW(), date_added = NOW()");

        $information_id = $this->db->lastInsertId();

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "information SET image = '" . $this->db->escape((string)$data['image']) . "' WHERE information_id = '" . (int)$information_id . "'");
        }

        foreach ($this->request->post['information_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "information_description SET information_id = '" . (int)$information_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape((string)$value['title']) . "', description = '" . $this->db->escape((string)$value['description']) . "', meta_title = '" . $this->db->escape((string)$value['meta_title']) . "', meta_description = '" . $this->db->escape((string)$value['meta_description']) . "', meta_keyword = '" . $this->db->escape((string)$value['meta_keyword']) . "'");
        }

        # SEO URL
        if (isset($data['information_seo_url'])) {
            foreach ($data['information_seo_url'] as $language_id => $keyword) {
                if ($keyword) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET language_id = '" . (int)$language_id . "', query = 'information_id=" . (int)$information_id . "', keyword = '" . $this->db->escape($keyword) . "', push = '" . $this->db->escape('url=information/information&information_id=' . (int)$information_id) . "'");
                }
            }
        }

        $this->cache->delete('information');

        return $information_id;
    }

    public function editInformation($information_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "information SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW() WHERE information_id = '" . (int)$information_id . "'");

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "information SET image = '" . $this->db->escape((string)$data['image']) . "' WHERE information_id = '" . (int)$information_id . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "information_description WHERE information_id = '" . (int)$information_id . "'");

        foreach ($this->request->post['information_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "information_description SET information_id = '" . (int)$information_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape((string)$value['title']) . "', description = '" . $this->db->escape((string)$value['description']) . "', meta_title = '" . $this->db->escape((string)$value['meta_title']) . "', meta_description = '" . $this->db->escape((string)$value['meta_description']) . "', meta_keyword = '" . $this->db->escape((string)$value['meta_keyword']) . "'");
        }

        #SEO URL
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'information_id=" . (int)$information_id . "'");

        if (isset($data['information_seo_url'])) {
            foreach ($data['information_seo_url'] as $language_id => $keyword) {
                if ($keyword) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET language_id = '" . (int)$language_id . "', query = 'information_id=" . (int)$information_id . "', keyword = '" . $this->db->escape($keyword) . "', push = '" . $this->db->escape('url=information/information&information_id=' . (int)$information_id) . "'");
                }
            }
        }

        $this->cache->delete('information');
    }

    public function deleteInformation($information_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "information WHERE information_id = '" . (int)$information_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "information_description WHERE information_id = '" . (int)$information_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'information_id=" . (int)$information_id . "'");

        $this->cache->delete('information');
    }

    public function getInformation($information_id)
    {
        $query = $this->db->query("SELECT i.*,id.* FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE i.information_id = '" . (int)$information_id . "'");

        return $query->row;
    }

    public function getInformations()
    {
        $query = $this->db->query("SELECT i.*,id.* FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'");            
        
        return $query->rows;
        
    }

    public function getInformationDescriptions($information_id)
    {
        $information_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_description WHERE information_id = '" . (int)$information_id . "'");

        foreach ($query->rows as $result) {
            $information_description_data[$result['language_id']] = array(
                'title'             => $result['title'],
                'description'       => $result['description'],
                'meta_title'        => $result['meta_title'],
                'meta_description'  => $result['meta_description'],
                'meta_keyword'      => $result['meta_keyword']
            );
        }

        return $information_description_data;
    }

    public function getInformationSeoUrls($information_id)
    {
        $information_seo_url_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'information_id=" . (int)$information_id . "'");

        foreach ($query->rows as $result) {
            $information_seo_url_data[$result['language_id']] = $result['keyword'];
        }

        return $information_seo_url_data;
    }

    public function getTotalInformations()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "information");

        return $query->row['total'];
    }
}
