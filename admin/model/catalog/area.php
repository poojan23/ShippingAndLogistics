<?php

class ModelCatalogArea extends PT_Model
{
    public function addArea($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "area SET customer_group_id = '" . (int)($data['customer_group_id']) . "',status = '" . (int)$data['status'] . "'");

        $area_id = $this->db->lastInsertId();

        if(isset($data['area'])) {
            foreach ($data['area'] as $areass) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "area_group SET area_id = '" . (int) $area_id . "', area = '" . $this->db->escape($areass['area']) . "', sort_order = '" . (int) $areass['sort_order'] . "'");
            }
        }

        return $area_id;
    }

    public function editArea($area_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "area SET customer_group_id = '" . (int)($data['customer_group_id']) . "', status = '" . (int) $data['status'] . "' WHERE area_id = '" . (int) $area_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "area_group WHERE area_id = '" . (int) $area_id . "'");

        foreach ($data['area'] as $areass) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "area_group SET area_id = '" . (int) $area_id . "',area = '" . $this->db->escape($areass['area']) . "', sort_order = '" . (int) $areass['sort_order'] . "'");
        }
        
    }

    public function deleteArea($area_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "area WHERE area_id = '" . (int)$area_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "area_group WHERE area_id = '" . (int)$area_id . "'");
    }

    public function getArea($area_id) {
        $query = $this->db->query("SELECT  a.*,ag.area,cg.* FROM " . DB_PREFIX . "area a "
                . " LEFT  JOIN " . DB_PREFIX . "area_group ag on a.area_id = ag.area_id "
                . " LEFT  JOIN " . DB_PREFIX . "customer_group cg on a.customer_group_id = cg.customer_group_id"
                . " WHERE a.area_id = '" . (int)$area_id . "'");

        return $query->row;
    }

    public function getAreas() {
        $query = $this->db->query("SELECT  a.*,cg.* FROM " . DB_PREFIX . "area a "
                . " LEFT  JOIN " . DB_PREFIX . "customer_group cg on a.customer_group_id = cg.customer_group_id WHERE a.status = '1'");

        return $query->rows;
    }

    public function getAreaGroup($area_id) {
        $areass_data = array();

        $areass_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "area_group WHERE area_id = '" . (int)$area_id . "' ORDER BY sort_order ASC");
        
        foreach($areass_query->rows as $areass) {
            $areass_data[] = array(
                'area'         => $areass['area'],
                'sort_order'    => $areass['sort_order']
            );
        }

        return $areass_data;
    }
    
    public function getAreaTime($area_id) {
        $area_time_data = array();

        $area_time_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "area_time WHERE area_id = '" . (int)$area_id . "' ORDER BY sort_order ASC");
        
        foreach($area_time_query->rows as $area_time) {
            $area_time_data[] = array(
                'gender'         => $area_time['gender'],
                'period'         => $area_time['period'],
                'time'          => $area_time['time'],
                'sort_order'    => $area_time['sort_order']
            );
        }

        return $area_time_data;
    }
    
    public function getAreaCoaching($area_id) {
        $area_coach_data = array();

        $area_coach_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "area_coach WHERE area_id = '" . (int)$area_id . "' ORDER BY sort_order ASC");
        
        foreach($area_coach_query->rows as $area_coach) {
            $area_coach_data[] = array(
                'coach_name'    => $area_coach['coach_name'],
                'gender'        => $area_coach['gender'],
                'days'          => $area_coach['days'],
                'time'          => $area_coach['coaching_time'],
                'sort_order'    => $area_coach['sort_order']
            );
        }

        return $area_coach_data;
    }

    public function getTotalAreas() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "banner");

        return $query->row['total'];
    }

    public function showAreas() {
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
    
    public function getAreasByCustomerId($customer_group_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "area WHERE customer_group_id = '" . $customer_group_id . "'");

        return $query->rows;
    }
    
    public function getAreasByAreaId($area_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "area_group WHERE area_id = '" . $area_id . "' group by area");

        return $query->rows;
    }
}
