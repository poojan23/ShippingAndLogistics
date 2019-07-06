<?php

class ModelSportSport extends PT_Model
{
    public function addSport($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "sport SET sport_group_id = '" . (int)($data['sport_group_id']) . "',member_group_id = '" . (int)($data['member_group_id']) . "', status = '" . (int)$data['status'] . "'");

        $sport_id = $this->db->lastInsertId();

        if(isset($data['sport_fee'])) {
            foreach ($data['sport_fee'] as $sport_fees) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "sport_fee SET sport_id = '" . (int) $sport_id . "', period = '" . $this->db->escape($sport_fees['period']) . "', time = '" . $this->db->escape($sport_fees['time']) . "', fees = '" . $this->db->escape($sport_fees['fees']) . "', sort_order = '" . (int) $sport_fees['sort_order'] . "'");
            }
        }
        if(isset($data['coaching'])) {
            foreach ($data['coaching'] as $coaching) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "sport_coaching SET sport_id = '" . (int) $sport_id . "', isActive = '1',coach_name = '" . $this->db->escape($coaching['coach_name']) . "',  coaching_type = '" . $this->db->escape($coaching['coaching_type']) . "', days = '" . $this->db->escape($coaching['days']) . "',coaching_time = '" . $this->db->escape($coaching['time']) . "', coaching_fees = '" . $this->db->escape($coaching['fees']) . "',sort_order = '" . (int) $coaching['sort_order'] . "'");
            }
        }

        return $sport_id;
    }

    public function editSport($banner_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "banner SET name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "' WHERE banner_id = '" . (int)$banner_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "'");

        if(isset($data['banner_image'])) {
            foreach($data['banner_image'] as $language_id => $value) {
                foreach($value as $banner_image) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "banner_image SET banner_id = '" . (int)$banner_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($banner_image['title']) . "', link = '" . $this->db->escape($banner_image['link']) . "', image = '" . $this->db->escape($banner_image['image']) . "', sort_order = '" . (int)$banner_image['sort_order'] . "'");
                }
            }
        }
    }

    public function deleteSport($sport_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "banner WHERE banner_id = '" . (int)$banner_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "banner_image WHERE banner_id = '" . (int)$banner_id . "'");
    }

    public function getSport($sport_id) {
        $query = $this->db->query("SELECT  s.*,sg.name,sg.sport_group_id FROM " . DB_PREFIX . "sport s LEFT  JOIN " . DB_PREFIX . "sport_group sg on S.sport_group_id = sg.sport_group_id WHERE s.sport_id = '" . (int)$sport_id . "'");

        return $query->row;
    }

    public function getSports() {
        $query = $this->db->query("SELECT  s.*,sg.name FROM " . DB_PREFIX . "sport s LEFT  JOIN " . DB_PREFIX . "sport_group sg on S.sport_group_id = sg.sport_group_id WHERE s.status = '1'");

        return $query->rows;
    }

    public function getSportFees($sport_id) {
        $sport_fees_data = array();

        $sport_fees_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sport_fee WHERE sport_id = '" . (int)$sport_id . "' ORDER BY sort_order ASC");
        
        foreach($sport_fees_query->rows as $sport_fees) {
            $sport_fees_data[] = array(
                'period'         => $sport_fees['period'],
                'time'         => $sport_fees['time'],
                'fees'          => $sport_fees['fees'],
                'sort_order'    => $sport_fees['sort_order']
            );
        }

        return $sport_fees_data;
    }
    public function getSportCoaching($sport_id) {
        $sport_coach_data = array();

        $sport_coach_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sport_coaching WHERE sport_id = '" . (int)$sport_id . "' ORDER BY sort_order ASC");
        
        foreach($sport_coach_query->rows as $sport_coach) {
            $sport_coach_data[] = array(
                'coach_name'    => $sport_coach['coach_name'],
                'coaching_type' => $sport_coach['coaching_type'],
                'days'          => $sport_coach['days'],
                'time'          => $sport_coach['coaching_time'],
                'fees'          => $sport_coach['coaching_fees'],
                'sort_order'    => $sport_coach['sort_order']
            );
        }

        return $sport_coach_data;
    }

    public function getTotalSports() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "banner");

        return $query->row['total'];
    }

    public function showSports() {
        $sql = "SELECT * FROM " . DB_PREFIX . "banner";

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
