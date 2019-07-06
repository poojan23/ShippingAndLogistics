<?php

class ModelLocalisationLanguage extends PT_Model {

    public function addLanguage($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "language SET name = '" . $this->db->escape((string) $data['name']) . "', code = '" . $this->db->escape((string) $data['code']) . "', locale = '" . $this->db->escape((string) $data['locale']) . "', sort_order = '" . (int) $data['sort_order'] . "', status = '" . (int) $data['status'] . "'");

        $this->cache->delete('catalog.language');
        $this->cache->delete('admin.language');

        $language_id = $this->db->getLastId();

        // Banner
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "banner_image WHERE language_id = '" . (int) $this->config->get('config_language_id') . "'");

        foreach ($query->rows as $banner_image) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int) $banner_image['banner_id'] . "', language_id = '" . (int) $language_id . "', title = '" . $this->db->escape($banner_image['title']) . "', link = '" . $this->db->escape($banner_image['link']) . "', image = '" . $this->db->escape($banner_image['image']) . "', sort_order = '" . (int) $banner_image['sort_order'] . "'");
        }

        $this->cache->delete('banner');

        // Information
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_description WHERE language_id = '" . (int) $this->config->get('config_language_id') . "'");

        foreach ($query->rows as $information) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "information_description SET information_id = '" . (int) $information['information_id'] . "', language_id = '" . (int) $language_id . "', title = '" . $this->db->escape($information['title']) . "', description = '" . $this->db->escape($information['description']) . "', meta_title = '" . $this->db->escape($information['meta_title']) . "', meta_description = '" . $this->db->escape($information['meta_description']) . "', meta_keyword = '" . $this->db->escape($information['meta_keyword']) . "'");
        }

        $this->cache->delete('information');

        return $language_id;
    }

    public function editLanguage($language_id, $data) {
        $language_query = $this->db->query("SELECT `code` FROM " . DB_PREFIX . "language WHERE language_id = '" . (int) $language_id . "'");

        $this->db->query("UPDATE " . DB_PREFIX . "language SET name = '" . $this->db->escape((string) $data['name']) . "', code = '" . $this->db->escape((string) $data['code']) . "', locale = '" . $this->db->escape((string) $data['locale']) . "', sort_order = '" . (int) $data['sort_order'] . "', status = '" . (int) $data['status'] . "' WHERE language_id = '" . (int) $language_id . "'");

        if ($language_query->row['code'] != $data['code']) {
            $this->db->query("UPDATE " . DB_PREFIX . "setting SET value = '" . $this->db->escape((string) $data['code']) . "' WHERE `key` = 'config_language' AND value = '" . $this->db->escape($language_query->row['code']) . "'");
            $this->db->query("UPDATE " . DB_PREFIX . "setting SET value = '" . $this->db->escape((string) $data['code']) . "' WHERE `key` = 'config_admin_language' AND value = '" . $this->db->escape($language_query->row['code']) . "'");
        }

        $this->cache->delete('catalog.language');
        $this->cache->delete('admin.language');
    }

    public function deleteLanguage($language_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "language WHERE language_id = '" . (int) $language_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE language_id = '" . (int) $language_id . "'");

        $this->cache->delete('catalog.language');
        $this->cache->delete('admin.language');

        /*
          Do not put any delete code for related tables for languages!!!!!!!!!

          It is not required as whenever you save to a multi language table then the entries for the deleted language will also be deleted!

          Wasting my time with people adding code here!
         */
    }

    public function getLanguage($language_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "language WHERE language_id = '" . (int) $language_id . "'");

        return $query->row;
    }

      public function getLanguages($data = array())
    {
        if ($data) {
            $sql = "SELECT * FROM " . DB_PREFIX . "language";

            $sort_data = array(
                'name',
                'code',
                'sort_order'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY sort_order, name";
            }

            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
            } else {
                $sql .= " ASC";
            }

            if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                    $data['start'] = 0;
                }

                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }

                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            }

            $query = $this->db->query($sql);

            return $query->rows;
        } else {
            $language_data = $this->cache->get('admin.language');

            if (!$language_data) {
                $language_data = array();

                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language ORDER BY sort_order, name");

                foreach ($query->rows as $result) {
                    $language_data[$result['code']] = array(
                        'language_id'   => $result['language_id'],
                        'name'          => $result['name'],
                        'code'          => $result['code'],
                        'locale'        => $result['locale'],
                        'image'         => $result['image'],
                        'sort_order'    => $result['sort_order'],
                        'status'        => $result['status']
                    );
                }

                $this->cache->set('admin.language', $language_data);
            }

            return $language_data;
        }
    }

    public function getLanguageByCode($code) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "language` WHERE code = '" . $this->db->escape($code) . "'");

        return $query->row;
    }

    public function getTotalLanguages() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "language");

        return $query->row['total'];
    }

}
