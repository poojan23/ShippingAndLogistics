<?php

class ModelCatalogEvent extends PT_Model {

    public function addEvent($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "events SET  event_group_id = '" . (int) $data['event_group_id'] . "',name = '" . $this->db->escape((string) $data['event_name']) . "',description = '" . $this->db->escape((string) $data['description']) . "',sort_order = '" . (int) $data['sort_order'] . "', status = '" . (isset($data['status']) ? (int) $data['status'] : 0) . "', date_modified = NOW(), date_added = NOW()");

        $event_id = $this->db->lastInsertId();

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "events SET image = '" . $this->db->escape((string) $data['image']) . "' WHERE event_id = '" . (int) $event_id . "'");
        }

        if (isset($data['event_image'])) {
            foreach ($data['event_image'] as $event_image) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "event_image SET event_id = '" . (int) $event_id . "', image = '" . $this->db->escape($event_image['image']) . "', sort_order = '" . (int) $event_image['sort_order'] . "'");
            }
        }

        # SEO URL
        if (isset($data['event_seo_url'])) {
            foreach ($data['event_seo_url'] as $language_id => $keyword) {
                if ($keyword) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET language_id = '" . (int) $language_id . "', query = 'event_id=" . (int) $event_id . "', keyword = '" . $this->db->escape($keyword) . "', push = '" . $this->db->escape('url=event/event&event_id=' . (int) $event_id) . "'");
                }
            }
        }

        $this->cache->delete('event');

        return $event_id;
    }

    public function editEvent($event_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "events SET event_group_id = '" . (int) $data['event_group_id'] . "',name = '" . $this->db->escape((string) $data['event_name']) . "',description = '" . $this->db->escape((string) $data['description']) . "',sort_order = '" . (int) $data['sort_order'] . "', status = '" . (isset($data['status']) ? (int) $data['status'] : 0) . "', date_modified = NOW() WHERE event_id = '" . (int) $event_id . "'");

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "events SET image = '" . $this->db->escape((string) $data['image']) . "' WHERE event_id = '" . (int) $event_id . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "event_image WHERE event_id = '" . (int) $event_id . "'");

        if (isset($data['event_image'])) {
            foreach ($data['event_image'] as $event_image) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "event_image SET event_id = '" . (int) $event_id . "', image = '" . $this->db->escape($event_image['image']) . "', sort_order = '" . (int) $event_image['sort_order'] . "'");
            }
        }
        #SEO URL
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'event_id=" . (int) $event_id . "'");

        if (isset($data['event_seo_url'])) {
            foreach ($data['event_seo_url'] as $language_id => $keyword) {
                if ($keyword) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET language_id = '" . (int) $language_id . "', query = 'event_id=" . (int) $event_id . "', keyword = '" . $this->db->escape($keyword) . "', push = '" . $this->db->escape('url=event/event&event_id=' . (int) $event_id) . "'");
                }
            }
        }

        $this->cache->delete('event');
    }

    public function deleteEvent($event_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "events WHERE event_id = '" . (int) $event_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "event_image WHERE event_id = '" . (int) $event_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'event_id=" . (int) $event_id . "'");

        $this->cache->delete('event');
    }

    public function getEvent($event_id) {
        $query = $this->db->query("SELECT e.*,ei.* FROM " . DB_PREFIX . "events e LEFT JOIN " . DB_PREFIX . "event_image ei ON (e.event_id = ei.event_id) WHERE e.event_id = '" . (int) $event_id . "'");

        return $query->row;
    }

    public function getEvents() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "events  WHERE status = 1");

        return $query->rows;
    }

    public function getEventDescriptions($event_id) {
        $event_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "event_description WHERE event_id = '" . (int) $event_id . "'");

        foreach ($query->rows as $result) {
            $event_description_data[$result['language_id']] = array(
                'title' => $result['title'],
                'description' => $result['description'],
                'meta_title' => $result['meta_title'],
                'meta_description' => $result['meta_description'],
                'meta_keyword' => $result['meta_keyword']
            );
        }

        return $event_description_data;
    }

    public function getEventSeoUrls($event_id) {
        $event_seo_url_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'event_id=" . (int) $event_id . "'");

        foreach ($query->rows as $result) {
            $event_seo_url_data[$result['language_id']] = $result['keyword'];
        }

        return $event_seo_url_data;
    }

    public function getTotalEvents() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "event");

        return $query->row['total'];
    }

    public function getEventImages($event_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "event_image WHERE event_id = '" . (int) $event_id . "' ORDER BY sort_order ASC");

        return $query->rows;
    }

}
