<?php

class ModelCatalogService extends PT_Model
{
    public function addService($data)
    {
//        echo "INSERT INTO " . DB_PREFIX . "service SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW(), date_added = NOW()";exit;
        $this->db->query("INSERT INTO " . DB_PREFIX . "service SET service_id = '" . (int)$data['service_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW(), date_added = NOW()");

        $service_id = $this->db->lastInsertId();

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "service SET image = '" . $this->db->escape((string)$data['image']) . "' WHERE service_id = '" . (int)$service_id . "'");
        }

        foreach ($this->request->post['service_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "service_description SET service_id = '" . (int)$service_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape((string)$value['name']) . "', description = '" . $this->db->escape((string)$value['description']) . "', meta_title = '" . $this->db->escape((string)$value['meta_title']) . "', meta_description = '" . $this->db->escape((string)$value['meta_description']) . "', meta_keyword = '" . $this->db->escape((string)$value['meta_keyword']) . "'");
        }

        # SEO URL
        if (isset($data['service_seo_url'])) {
            foreach ($data['service_seo_url'] as $language_id => $keyword) {
                if ($keyword) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET language_id = '" . (int)$language_id . "', query = 'service_id=" . (int)$service_id . "', keyword = '" . $this->db->escape($keyword) . "', push = '" . $this->db->escape('url=service/service&service_id=' . (int)$service_id) . "'");
                }
            }
        }

        $this->cache->delete('service');

        return $service_id;
    }

    public function editService($service_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "service SET   sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW() WHERE service_id = '" . (int)$service_id . "'");

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "service SET image = '" . $this->db->escape((string)$data['image']) . "' WHERE service_id = '" . (int)$service_id . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "service_description WHERE service_id = '" . (int)$service_id . "'");

        foreach ($this->request->post['service_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "service_description SET service_id = '" . (int)$service_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape((string)$value['name']) . "', description = '" . $this->db->escape((string)$value['description']) . "', meta_title = '" . $this->db->escape((string)$value['meta_title']) . "', meta_description = '" . $this->db->escape((string)$value['meta_description']) . "', meta_keyword = '" . $this->db->escape((string)$value['meta_keyword']) . "'");
        }

        #SEO URL
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'service_id=" . (int)$service_id . "'");

        if (isset($data['service_seo_url'])) {
            foreach ($data['service_seo_url'] as $language_id => $keyword) {
                if ($keyword) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET language_id = '" . (int)$language_id . "', query = 'service_id=" . (int)$service_id . "', keyword = '" . $this->db->escape($keyword) . "', push = '" . $this->db->escape('url=service/service&service_id=' . (int)$service_id) . "'");
                }
            }
        }

        $this->cache->delete('service');
    }

    public function deleteService($service_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "service WHERE service_id = '" . (int)$service_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "service_description WHERE service_id = '" . (int)$service_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'service_id=" . (int)$service_id . "'");

        $this->cache->delete('service');
    }

    public function getService($service_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "service WHERE service_id = '" . (int)$service_id . "'");

        return $query->row;
    }
    public function getServices()
    {
        $query = $this->db->query("SELECT  s.*,sd.* FROM " . DB_PREFIX . "service s LEFT JOIN " . DB_PREFIX . "service_description sd ON s.service_id = sd.service_id WHERE s.status = '1' ORDER BY s.sort_order");

        return $query->rows;
    }

//    public function getServices($data = array())
//    {
//        if ($data) {
//            $sql = "SELECT *, (SELECT igd.name FROM " . DB_PREFIX . "service_group_description igd WHERE igd.service_group_id = i.service_group_id AND igd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS service_group FROM " . DB_PREFIX . "service i LEFT JOIN " . DB_PREFIX . "service_description id ON (i.service_id = id.service_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";
//
//            $sort_data = array(
//                'id.title',
//                'i.sort_order'
//            );
//
//            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
//                $sql .= " ORDER BY " . $data['sort'];
//            } else {
//                $sql .= " ORDER BY id.title";
//            }
//
//            if (isset($data['order']) && ($data['order'] == 'DESC')) {
//                $sql .= " DESC";
//            } else {
//                $sql .= " ASC";
//            }
//
//            if (isset($data['start']) || isset($data['limit'])) {
//                if ($data['start'] < 0) {
//                    $data['start'] = 0;
//                }
//
//                if ($data['limit'] < 1) {
//                    $data['limit'] = 20;
//                }
//
//                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
//            }
//
//            $query = $this->db->query($sql);
//
//            return $query->rows;
//        } else {
//            $service_data = $this->cache->get('service.' . (int)$this->config->get('config_language_id'));
//
//            if (!$service_data) {
//                $query = $this->db->query("SELECT *, (SELECT igd.title FROM " . DB_PREFIX . "service_group_description igd WHERE igd.service_group_id = i.service_group_id AND igd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS service_group FROM " . DB_PREFIX . "service i LEFT JOIN " . DB_PREFIX . "service_description id ON (i.service_id = id.service_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY id.title");
//
//                $service_data = $query->rows;
//
//                $this->cache->set('service.' . (int)$this->config->get('config_language_id'), $service_data);
//            }
//
//            return $service_data;
//        }
//    }

    public function getServiceDescriptions($service_id)
    {
        $service_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "service_description WHERE service_id = '" . (int)$service_id . "'");

        foreach ($query->rows as $result) {
            $service_description_data[$result['language_id']] = array(
                'name'             => $result['name'],
                'description'       => $result['description'],
                'meta_title'        => $result['meta_title'],
                'meta_description'  => $result['meta_description'],
                'meta_keyword'      => $result['meta_keyword']
            );
        }

        return $service_description_data;
    }

    public function getServiceSeoUrls($service_id)
    {
        $service_seo_url_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'service_id=" . (int)$service_id . "'");

        foreach ($query->rows as $result) {
            $service_seo_url_data[$result['language_id']] = $result['keyword'];
        }

        return $service_seo_url_data;
    }

    public function getTotalServices()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "service");

        return $query->row['total'];
    }
}
