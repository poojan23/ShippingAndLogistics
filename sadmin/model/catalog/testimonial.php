<?php

class ModelCatalogTestimonial extends PT_Model
{
    public function addTestimonial($data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "testimonial SET  name = '" . $this->db->escape((string)$data['name']) . "',image = '" . $this->db->escape((string)$data['image']) . "',designation = '" . $this->db->escape((string)$data['designation']) . "',description = '" . $this->db->escape((string)$data['description']) . "',sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW(), date_added = NOW()");

        return $testimonial_id;
    }

    public function editTestimonial($testimonial_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "testimonial SET  name = '" . $this->db->escape((string)$data['name']) . "',designation = '" . $this->db->escape((string)$data['designation']) . "',description = '" . $this->db->escape((string)$data['description']) . "',sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW() WHERE testimonial_id = '" . (int)$testimonial_id . "'");

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "testimonial SET image = '" . $this->db->escape((string)$data['image']) . "' WHERE testimonial_id = '" . (int)$testimonial_id . "'");
        }
    }

    public function deleteTestimonial($testimonial_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "testimonial WHERE testimonial_id = '" . (int)$testimonial_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'testimonial_id=" . (int)$testimonial_id . "'");

        $this->cache->delete('testimonial');
    }

    public function getTestimonial($testimonial_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "testimonial WHERE testimonial_id = '" . (int)$testimonial_id . "'");

        return $query->row;
    }
    public function getTestimonials()
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "testimonial WHERE status = '1' ORDER BY sort_order");

        return $query->rows;
    }

//    public function getServices($data = array())
//    {
//        if ($data) {
//            $sql = "SELECT *, (SELECT igd.name FROM " . DB_PREFIX . "testimonial_group_description igd WHERE igd.testimonial_group_id = i.testimonial_group_id AND igd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS testimonial_group FROM " . DB_PREFIX . "testimonial i LEFT JOIN " . DB_PREFIX . "testimonial_description id ON (i.testimonial_id = id.testimonial_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "'";
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
//            $testimonial_data = $this->cache->get('testimonial.' . (int)$this->config->get('config_language_id'));
//
//            if (!$testimonial_data) {
//                $query = $this->db->query("SELECT *, (SELECT igd.title FROM " . DB_PREFIX . "testimonial_group_description igd WHERE igd.testimonial_group_id = i.testimonial_group_id AND igd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS testimonial_group FROM " . DB_PREFIX . "testimonial i LEFT JOIN " . DB_PREFIX . "testimonial_description id ON (i.testimonial_id = id.testimonial_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY id.title");
//
//                $testimonial_data = $query->rows;
//
//                $this->cache->set('testimonial.' . (int)$this->config->get('config_language_id'), $testimonial_data);
//            }
//
//            return $testimonial_data;
//        }
//    }

    public function getTestimonialDescriptions($testimonial_id)
    {
        $testimonial_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "testimonial_description WHERE testimonial_id = '" . (int)$testimonial_id . "'");

        foreach ($query->rows as $result) {
            $testimonial_description_data[$result['language_id']] = array(
                'title'             => $result['title'],
                'description'       => $result['description'],
                'meta_title'        => $result['meta_title'],
                'meta_description'  => $result['meta_description'],
                'meta_keyword'      => $result['meta_keyword']
            );
        }

        return $testimonial_description_data;
    }

    public function getTestimonialSeoUrls($testimonial_id)
    {
        $testimonial_seo_url_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'testimonial_id=" . (int)$testimonial_id . "'");

        foreach ($query->rows as $result) {
            $testimonial_seo_url_data[$result['language_id']] = $result['keyword'];
        }

        return $testimonial_seo_url_data;
    }

    public function getTotalTestimonials()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "testimonial");

        return $query->row['total'];
    }
}
