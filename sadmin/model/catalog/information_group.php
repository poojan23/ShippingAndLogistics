<?php
/*
DROP TABLE IF EXISTS `pt_information_group`;
CREATE TABLE `pt_information_group` (
	`information_group_id` int(11) NOT NULL AUTO_INCREMENT,
    `image` varchar(255) NOT NULL,
    `top` int(1) NOT NULL,
    `bottom` int(1) NOT NULL,
    `sort_order` int(3) NOT NULL,
    `status` tinyint(1) NOT NULL,
    `date_added` datetime NOT NULL,
    `date_modified` datetime NOT NULL,
    PRIMARY KEY (`information_group_id`) 
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `pt_information_group_description`;
CREATE TABLE `pt_information_group_description` (
	`information_group_id` int(11) NOT NULL,
    `language_id` int(11) NOT NULL,
    `title` varchar(64) NOT NULL,
    `title_footer` varchar(64) NOT NULL,
    `description` mediumtext NOT NULL,
    `meta_title` varchar(255) NOT NULL,
    `meta_description` varchar(255) NOT NULL,
    `meta_keyword` varchar(255) NOT NULL,
    PRIMARY KEY (`information_group_id`,`language_id`) 
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
*/

class ModelCatalogInformationGroup extends PT_Model
{
    public function addInformationGroup($data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "information_group SET top = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', bottom = '" . (isset($data['bottom']) ? (int)$data['bottom'] : 0) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW(), date_added = NOW()");

        $information_group_id = $this->db->lastInsertId();

        if ($data['image']) {
            $this->db->query("UPDATE " . DB_PREFIX . "information_group SET image = '" . $this->db->escape((string)$data['image']) . "' WHERE information_group_id = '" . (int)$information_group_id . "'");
        }

        foreach ($this->request->post['information_group_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "information_group_description SET information_group_id = '" . (int)$information_group_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape((string)$value['title']) . "', title_footer = '" . $this->db->escape((string)$value['title_footer']) . "', description = '" . $this->db->escape((string)$value['description']) . "', meta_title = '" . $this->db->escape((string)$value['meta_title']) . "', meta_description = '" . $this->db->escape((string)$value['meta_description']) . "', meta_keyword = '" . $this->db->escape((string)$value['meta_keyword']) . "'");
        }

        # SEO URL
        if (isset($data['information_group_seo_url'])) {
            foreach ($data['information_group_seo_url'] as $language_id => $keyword) {
                if ($keyword) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET language_id = '" . (int)$language_id . "', query = 'information_group_id=" . (int)$information_group_id . "', keyword = '" . $this->db->escape($keyword) . "', push = '" . $this->db->escape('url=information/information_group&information_group_id=' . (int)$information_group_id) . "'");
                }
            }
        }

        $this->cache->delete('information_group');

        return $information_group_id;
    }

    public function editInformationGroup($information_group_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "information_group SET top = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "', bottom = '" . (isset($data['bottom']) ? (int)$data['bottom'] : 0) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW() WHERE information_group_id = '" . (int)$information_group_id . "'");

        if ($data['image']) {
            $this->db->query("UPDATE " . DB_PREFIX . "information_group SET image = '" . $this->db->escape((string)$data['image']) . "' WHERE information_group_id = '" . (int)$information_group_id . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "information_group_description WHERE information_group_id = '" . (int)$information_group_id . "'");

        foreach ($this->request->post['information_group_description'] as $language_id => $value) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "information_group_description SET information_group_id = '" . (int)$information_group_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape((string)$value['title']) . "', title_footer = '" . $this->db->escape((string)$value['title_footer']) . "', description = '" . $this->db->escape((string)$value['description']) . "', meta_title = '" . $this->db->escape((string)$value['meta_title']) . "', meta_description = '" . $this->db->escape((string)$value['meta_description']) . "', meta_keyword = '" . $this->db->escape((string)$value['meta_keyword']) . "'");
        }

        #SEO URL
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'information_group_id=" . (int)$information_group_id . "'");

        if (isset($data['information_group_seo_url'])) {
            foreach ($data['information_group_seo_url'] as $language_id => $keyword) {
                if ($keyword) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET language_id = '" . (int)$language_id . "', query = 'information_group_id=" . (int)$information_group_id . "', keyword = '" . $this->db->escape($keyword) . "', push = '" . $this->db->escape('url=information/information_group&information_group_id=' . (int)$information_group_id) . "'");
                }
            }
        }

        $this->cache->delete('information_group');
    }

    public function deleteInformationGroup($information_group_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "information_group WHERE information_group_id = '" . (int)$information_group_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "information_group_description WHERE information_group_id = '" . (int)$information_group_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'information_group_id=" . (int)$information_group_id . "'");

        $this->cache->delete('information_group');
    }

    public function getInformationGroup($information_group_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information_group WHERE information_group_id = '" . (int)$information_group_id . "'");

        return $query->row;
    }

    public function getInformationGroups($data = array())
    {
        if ($data) {
            $sql = "SELECT * FROM " . DB_PREFIX . "information_group ig LEFT JOIN " . DB_PREFIX . "information_group_description igd ON (ig.information_group_id = igd.information_group_id) WHERE igd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

            $sort_data = array(
                'igd.title',
                'ig.sort_order'
            );

            if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY igd.title";
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
            $information_group_data = $this->cache->get('information_group.' . (int)$this->config->get('config_language_id'));

            if (!$information_group_data) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_group ig LEFT JOIN " . DB_PREFIX . "information_group_description igd ON (ig.information_group_id = igd.information_group_id) WHERE igd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY igd.title");

                $information_group_data = $query->rows;

                $this->cache->set('information_group.' . (int)$this->config->get('config_language_id'), $information_group_data);
            }

            return $information_group_data;
        }
    }

    public function getInformationGroupDescriptions($information_group_id)
    {
        $information_group_description_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_group_description WHERE information_group_id = '" . (int)$information_group_id . "'");

        foreach ($query->rows as $result) {
            $information_group_description_data[$result['language_id']] = array(
                'title'     => $result['title'],
                'title_footer'      => $result['title_footer'],
                'description'       => $result['description'],
                'meta_title'        => $result['meta_title'],
                'meta_description'  => $result['meta_description'],
                'meta_keyword'      => $result['meta_keyword']
            );
        }

        return $information_group_description_data;
    }

    public function getInformationGroupSeoUrls($information_group_id)
    {
        $information_group_seo_url_data = array();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'information_group_id=" . (int)$information_group_id . "'");

        foreach ($query->rows as $result) {
            $information_group_seo_url_data[$result['language_id']] = $result['keyword'];
        }

        return $information_group_seo_url_data;
    }

    public function getTotalInformationGroups()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "information_group");

        return $query->row['total'];
    }
}
