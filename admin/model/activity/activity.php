<?php

class ModelActivityActivity extends PT_Model
{
    public function addActivity($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "activity SET activity_group_id = '" . (int)($data['activity_group_id']) . "',member_group_id = '" . (int)($data['member_group_id']) . "', status = '" . (int)$data['status'] . "'");

        $activity_id = $this->db->lastInsertId();

        if(isset($data['activity_fee'])) {
            foreach ($data['activity_fee'] as $activity_fees) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "activity_fee SET activity_id = '" . (int) $activity_id . "', period = '" . $this->db->escape($activity_fees['period']) . "', fee = '" . $this->db->escape($activity_fees['fees']) . "', sort_order = '" . (int) $activity_fees['sort_order'] . "'");
            }
        }
        if(isset($data['time'])) {
            foreach ($data['time'] as $activity_time) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "activity_time SET activity_id = '" . (int) $activity_id . "',gender = '" . $this->db->escape($activity_time['gender_time']) . "', period = '" . $this->db->escape($activity_time['period']) . "', time = '" . $this->db->escape($activity_time['time']) . "', sort_order = '" . (int) $activity_time['sort_order'] . "'");
            }
        }
        if(isset($data['coaching'])) {
            foreach ($data['coaching'] as $coaching) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "activity_coach SET activity_id = '" . (int) $activity_id . "', isActive = '1',gender = '" . $this->db->escape($coaching['gender']) . "',coach_name = '" . $this->db->escape($coaching['coach_name']) . "', days = '" . $this->db->escape($coaching['days']) . "',coaching_time = '" . $this->db->escape($coaching['time']) . "',sort_order = '" . (int) $coaching['sort_order'] . "'");
            }
        }

        return $activity_id;
    }

    public function editActivity($activity_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "activity SET activity_group_id = '" . (int)($data['activity_group_id']) . "',member_group_id = '" . (int)($data['member_group_id']) . "', status = '" . (int) $data['status'] . "' WHERE activity_id = '" . (int) $activity_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "activity_fee WHERE activity_id = '" . (int) $activity_id . "'");

        foreach ($data['activity_fee'] as $activity_fees) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "activity_fee SET activity_id = '" . (int) $activity_id . "',period = '" . $this->db->escape($activity_fees['period']) . "', fee = '" . $this->db->escape($activity_fees['fees']) . "', sort_order = '" . (int) $activity_fees['sort_order'] . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "activity_time WHERE activity_id = '" . (int) $activity_id . "'");

        foreach ($data['time'] as $activity_time) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "activity_time SET activity_id = '" . (int) $activity_id . "',gender = '" . $this->db->escape($activity_time['gender_time']) . "', period = '" . $this->db->escape($activity_time['period']) . "', time = '" . $this->db->escape($activity_time['time']) . "', sort_order = '" . (int) $activity_time['sort_order'] . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "activity_coach WHERE activity_id = '" . (int) $activity_id . "'");

        foreach ($data['coaching'] as $coaching) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "activity_coach SET activity_id = '" . (int) $activity_id . "',isActive = '1',gender = '" . $this->db->escape($coaching['gender']) . "',coach_name = '" . $this->db->escape($coaching['coach_name']) . "', days = '" . $this->db->escape($coaching['days']) . "',coaching_time = '" . $this->db->escape($coaching['time']) . "',sort_order = '" . (int) $coaching['sort_order'] . "'");
        }
        
    }

    public function deleteActivity($activity_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "activity WHERE activity_id = '" . (int)$activity_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "activity_fee WHERE activity_id = '" . (int)$activity_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "activity_coach WHERE activity_id = '" . (int)$activity_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "activity_time WHERE activity_id = '" . (int)$activity_id . "'");
    }

    public function getActivity($activity_id) {
        $query = $this->db->query("SELECT  a.*,ag.name,ag.activity_group_id FROM " . DB_PREFIX . "activity a"
                . " LEFT  JOIN " . DB_PREFIX . "activity_group ag on a.activity_group_id = ag.activity_group_id"
                . " WHERE a.activity_id = '" . (int)$activity_id . "'");

        return $query->row;
    }

    public function getActivities() {
        $query = $this->db->query("SELECT  s.*,sg.name FROM " . DB_PREFIX . "activity s LEFT  JOIN " . DB_PREFIX . "activity_group sg on s.activity_group_id = sg.activity_group_id WHERE s.status = '1'");

        return $query->rows;
    }

    public function getActivityFees($activity_id) {
        $activity_fees_data = array();

        $activity_fees_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "activity_fee WHERE activity_id = '" . (int)$activity_id . "' ORDER BY sort_order ASC");
        
        foreach($activity_fees_query->rows as $activity_fees) {
            $activity_fees_data[] = array(
                'period'         => $activity_fees['period'],
                'fees'          => $activity_fees['fee'],
                'sort_order'    => $activity_fees['sort_order']
            );
        }

        return $activity_fees_data;
    }
    
    public function getActivityTime($activity_id) {
        $activity_time_data = array();

        $activity_time_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "activity_time WHERE activity_id = '" . (int)$activity_id . "' ORDER BY sort_order ASC");
        
        foreach($activity_time_query->rows as $activity_time) {
            $activity_time_data[] = array(
                'gender'         => $activity_time['gender'],
                'period'         => $activity_time['period'],
                'time'          => $activity_time['time'],
                'sort_order'    => $activity_time['sort_order']
            );
        }

        return $activity_time_data;
    }
    
    public function getActivityCoaching($activity_id) {
        $activity_coach_data = array();

        $activity_coach_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "activity_coach WHERE activity_id = '" . (int)$activity_id . "' ORDER BY sort_order ASC");
        
        foreach($activity_coach_query->rows as $activity_coach) {
            $activity_coach_data[] = array(
                'coach_name'    => $activity_coach['coach_name'],
                'gender'        => $activity_coach['gender'],
                'days'          => $activity_coach['days'],
                'time'          => $activity_coach['coaching_time'],
                'sort_order'    => $activity_coach['sort_order']
            );
        }

        return $activity_coach_data;
    }

    public function getTotalActivitys() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "banner");

        return $query->row['total'];
    }

    public function showActivitys() {
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
