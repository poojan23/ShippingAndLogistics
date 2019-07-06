<?php

class ModelGalleryGallery extends PT_Model
{
    public function addGallery($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "gallery SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "'");

        $gallery_id = $this->db->lastInsertId();

        if(isset($data['gallery_image'])) {
            foreach($data['gallery_image'] as $language_id => $value) {
                foreach($value as $gallery_image) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "gallery_image SET gallery_id = '" . (int)$gallery_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($gallery_image['title']) . "', link = '" . $this->db->escape($gallery_image['link']) . "', image = '" . $this->db->escape($gallery_image['image']) . "', sort_order = '" . (int)$gallery_image['sort_order'] . "'");
                }
            }
        }

        return $gallery_id;
    }

    public function editGallery($gallery_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "gallery SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "' WHERE gallery_id = '" . (int)$gallery_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "gallery_image WHERE gallery_id = '" . (int)$gallery_id . "'");

        if(isset($data['gallery_image'])) {
            foreach($data['gallery_image'] as $language_id => $value) {
                foreach($value as $gallery_image) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "gallery_image SET gallery_id = '" . (int)$gallery_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($gallery_image['title']) . "', link = '" . $this->db->escape($gallery_image['link']) . "', image = '" . $this->db->escape($gallery_image['image']) . "', sort_order = '" . (int)$gallery_image['sort_order'] . "'");
                }
            }
        }
    }

    public function deleteGallery($gallery_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "gallery WHERE gallery_id = '" . (int)$gallery_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "gallery_image WHERE gallery_id = '" . (int)$gallery_id . "'");
    }

    public function getGallery($gallery_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "gallery WHERE gallery_id = '" . (int)$gallery_id . "'");

        return $query->row;
    }

    public function getGallerys() {
        $query = $this->db->query("SELECT  * FROM " . DB_PREFIX . "gallery WHERE status = '1'");

        return $query->rows;
    }

    public function getGalleryImages($gallery_id) {
        $gallery_image_data = array();

        $gallery_image_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "gallery_image WHERE gallery_id = '" . (int)$gallery_id . "' ORDER BY sort_order ASC");

        foreach($gallery_image_query->rows as $gallery_image) {
            $gallery_image_data[$gallery_image['language_id']][] = array(
                'title'         => $gallery_image['title'],
                'link'          => $gallery_image['link'],
                'image'         => $gallery_image['image'],
                'sort_order'    => $gallery_image['sort_order']
            );
        }

        return $gallery_image_data;
    }

    public function getTotalGallerys() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "gallery");

        return $query->row['total'];
    }

    public function showGallerys() {
        $sql = "SELECT * FROM " . DB_PREFIX . "gallery";

        if(isset($this->request->post['search']['value'])) {
            $sql .= " WHERE name LIKE '%" . $this->db->escape($this->request->post['search']['value']) . "%'";
        }

        $sort_data = array(
            'name',
            'status'
        );

        if(isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
        }

        if(isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if(isset($data['start']) || isset($data['limit'])) {
            if($data['start'] < 0) {
                $data['start'] = 0;
            }

            if($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }
}
