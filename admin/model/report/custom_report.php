<?php

class ModelReportCustomReport extends PT_Model {

    public function addCustomReport($data) {

        if (isset($data['field'])) {
            
            foreach ($data['field'] as $fields) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "column_fields SET customer_id = '" . (int) $data['customer_id'] . "', field_name = '" . $this->db->escape($fields['field_name']) . "', name = '" . $this->db->escape($fields['name']) . "',sort_order = '" . (int) $fields['sort_order'] . "'");
            }
        } else {
            
                $data['field'] = Array
                    (
                    "0" => Array
                        (
                        "field_name" => 'job_no',
                        "name" => 'Job No.',
                        "sort_order" => 1
                    ),
                    "1" => Array
                        (
                        "field_name" => 'igm_no',
                        "name" => 'IGM No.',
                        "sort_order" => 2,
                    ),
                    "2" => Array
                        (
                        "field_name" => 'igm_date',
                        "name" => 'IGN Date',
                        "sort_order" => 3
                    ),
                    "3" => Array
                        (
                        "field_name" => 'po_no',
                        "name" => 'PO No.',
                        "sort_order" => 4
                    ),
                    "4" => Array
                        (
                        "field_name" => 'shipper',
                        "name" => 'Shipper',
                        "sort_order" => 5
                    ),
                    "5" => Array
                        (
                        "field_name" => 'be_heading',
                        "name" => 'BE Heading',
                        "sort_order" => 6
                    ),
                    "6" => Array
                        (
                        "field_name" => 'no_of_package',
                        "name" => 'No. Of Package',
                        "sort_order" => 7
                    ),
                    "7" => Array
                        (
                        "field_name" => 'unit',
                        "name" => 'Unit',
                        "sort_order" => 8
                    ),
                    "8" => Array
                        (
                        "field_name" => 'net_wt',
                        "name" => ' Net Weight',
                        "sort_order" => 9
                    ),
                    "9" => Array
                        (
                        "field_name" => 'mode',
                        "name" => 'Mode',
                        "sort_order" => 10
                    ),
                    "10" => Array
                        (
                        "field_name" => 'org_eta_date',
                        "name" => 'Org Eta Date',
                        "sort_order" => 11
                    ),
                    "11" => Array
                        (
                        "field_name" => 'shipping_line_date',
                        "name" => 'Shipping Line Date',
                        "sort_order" => 12
                    ),
                    "12" => Array
                        (
                        "field_name" => 'tentative_eta_date',
                        "name" => 'Tentative Eta Date',
                        "sort_order" => 13
                    ),
                    "13" => Array
                        (
                        "field_name" => 'expected_date',
                        "name" => 'Expected Date',
                        "sort_order" => 14
                    ),
                    "14" => Array
                        (
                        "field_name" => 'invoice_no',
                        "name" => 'Invoice No.',
                        "sort_order" => 15
                    ),
                    "15" => Array
                        (
                        "field_name" => 'invoice_date',
                        "name" => 'Invoice Date',
                        "sort_order" => 16
                    ),
                    "16" => Array
                        (
                        "field_name" => 'mawb_no',
                        "name" => 'Mawb No.',
                        "sort_order" => 17
                    ),
                    "17" => Array
                        (
                        "field_name" => 'mawb_date',
                        "name" => 'Mawb Date',
                        "sort_order" => 18
                    ),
                    "18" => Array
                        (
                        "field_name" => 'hawb_no',
                        "name" => 'Hawb No.',
                        "sort_order" => 19
                    ),
                    "19" => Array
                        (
                        "field_name" => 'hawb_date',
                        "name" => 'Hawb Date',
                        "sort_order" => 20
                    ),
                    "20" => Array
                        (
                        "field_name" => 'be_no',
                        "name" => 'BE No.',
                        "sort_order" => 21
                    ),
                    "21" => Array
                        (
                        "field_name" => 'be_date',
                        "name" => 'BE Date',
                        "sort_order" => 22
                    ),
                    "22" => Array
                        (
                        "field_name" => 'airline',
                        "name" => 'Airline',
                        "sort_order" => 23
                    ),
                    "23" => Array
                        (
                        "field_name" => 'n_document_date',
                        "name" => 'N Document Date',
                        "sort_order" => 24
                    ),
                    "24" => Array
                        (
                        "field_name" => 'org_doc_date',
                        "name" => 'Org Document Date',
                        "sort_order" => 25
                    ),
                    "25" => Array
                        (
                        "field_name" => 'duty_inform_date',
                        "name" => 'Duty Inform Date',
                        "sort_order" => 26
                    ),
                    "26" => Array
                        (
                        "field_name" => 'duty_received_date',
                        "name" => 'Duty Received Date',
                        "sort_order" => 27
                    ),
                    "27" => Array
                        (
                        "field_name" => 'duty_paid_date',
                        "name" => 'Duty Paid Date',
                        "sort_order" => 28
                    ),
                    "28" => Array
                        (
                        "field_name" => 'total_duty',
                        "name" => 'Total Duty',
                        "sort_order" => 29
                    ),
                    "29" => Array
                        (
                        "field_name" => 'container_cleared_date',
                        "name" => 'Container Cleared Date',
                        "sort_order" => 30
                    ),
                    "30" => Array
                        (
                        "field_name" => 'detention_amt',
                        "name" => 'Detention Amount',
                        "sort_order" => 31
                    ),
                    "31" => Array
                        (
                        "field_name" => 'customer_remark',
                        "name" => 'Customer Remark',
                        "sort_order" => 32
                    ),
                    "32" => Array
                        (
                        "field_name" => 'delivery_location_remark',
                        "name" => 'Delivery Location Remark',
                        "sort_order" => 33
                    ),
                    "33" => Array
                        (
                        "field_name" => 'container_no',
                        "name" => 'Container No',
                        "sort_order" => 34
                    ),
                    "34" => Array
                        (
                        "field_name" => 'free_period_shipping_date',
                        "name" => 'Free Period Shipping Date',
                        "sort_order" => 35
                    ),
                    "35" => Array
                        (
                        "field_name" => 'expected_free_dt_date',
                        "name" => 'Expected Free Dt Date',
                        "sort_order" => 36
                    ),
                    "36" => Array
                        (
                        "field_name" => 'expected_free_dt_remark',
                        "name" => 'Expected Free Dt Remark',
                        "sort_order" => 37
                    )
                
            );
//            print_r($data['field']);exit;
            foreach ($data['field'] as $fields) {
//                echo "INSERT INTO " . DB_PREFIX . "column_fields SET customer_id = '" . (int) $data['customer_id'] . "', field_name = '" . $this->db->escape($fields['field_name']) . "', name = '" . $this->db->escape($fields['name']) . "',sort_order = '" . (int) $fields['sort_order'] . "'";
                $this->db->query("INSERT INTO " . DB_PREFIX . "column_fields SET customer_id = '" . (int) $data['customer_id'] . "', field_name = '" . $this->db->escape($fields['field_name']) . "', name = '" . $this->db->escape($fields['name']) . "',sort_order = '" . (int) $fields['sort_order'] . "'");
            }
        }



        return $column_id;
    }

    public function editCustomReport($column_id, $data) {

        $this->db->query("DELETE FROM " . DB_PREFIX . "column_fields WHERE customer_id = '" . (int) $data['customer_id'] . "'");

        foreach ($data['field'] as $fields) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "column_fields SET customer_id = '" . (int) $data['customer_id'] . "', field_name = '" . $this->db->escape($fields['field_name']) . "', name = '" . $this->db->escape($fields['name']) . "',sort_order = '" . (int) $fields['sort_order'] . "'");
        }
    }

    public function deleteArea($column_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "area WHERE column_id = '" . (int) $column_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "area_group WHERE column_id = '" . (int) $column_id . "'");
    }

    public function getArea($column_id) {
        $query = $this->db->query("SELECT  a.*,ag.area,cg.* FROM " . DB_PREFIX . "area a "
                . " LEFT  JOIN " . DB_PREFIX . "area_group ag on a.column_id = ag.column_id "
                . " LEFT  JOIN " . DB_PREFIX . "customer_group cg on a.customer_group_id = cg.customer_group_id"
                . " WHERE a.column_id = '" . (int) $column_id . "'");

        return $query->row;
    }

    public function getCustomFields() {
        $query = $this->db->query("SELECT  DISTINCT cf.customer_id,c.name as customer_name  FROM " . DB_PREFIX . "column_fields cf "
                . " LEFT  JOIN " . DB_PREFIX . "customer c on c.customer_id = cf.customer_id");

        return $query->rows;
    }

    public function getCustomReport($customer_id) {
        $field_data = array();
//        echo "SELECT * FROM " . DB_PREFIX . "column_fields WHERE customer_id = '" . (int)$customer_id . "' ORDER BY sort_order ASC";exit;
        $field_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "column_fields WHERE customer_id = '" . (int) $customer_id . "' ORDER BY sort_order ASC");

        foreach ($field_query->rows as $field) {
            $field_data[] = array(
                'field_name' => $field['field_name'],
                'customer_id' => $field['customer_id'],
                'name' => $field['name'],
                'sort_order' => $field['sort_order']
            );
        }
//print_r($field_query);exit;
        return $field_data;
    }

    public function getAreaTime($column_id) {
        $area_time_data = array();

        $area_time_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "area_time WHERE column_id = '" . (int) $column_id . "' ORDER BY sort_order ASC");

        foreach ($area_time_query->rows as $area_time) {
            $area_time_data[] = array(
                'gender' => $area_time['gender'],
                'period' => $area_time['period'],
                'time' => $area_time['time'],
                'sort_order' => $area_time['sort_order']
            );
        }

        return $area_time_data;
    }

    public function getAreaCoaching($column_id) {
        $area_coach_data = array();

        $area_coach_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "area_coach WHERE column_id = '" . (int) $column_id . "' ORDER BY sort_order ASC");

        foreach ($area_coach_query->rows as $area_coach) {
            $area_coach_data[] = array(
                'coach_name' => $area_coach['coach_name'],
                'gender' => $area_coach['gender'],
                'days' => $area_coach['days'],
                'time' => $area_coach['coaching_time'],
                'sort_order' => $area_coach['sort_order']
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

        if (isset($this->request->post['search']['value'])) {
            $sql .= " WHERE name LIKE '%" . $this->db->escape($this->request->post['search']['value']) . "%'";
        }

        $sort_data = array(
            'name',
            'status'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY name";
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

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getAreasByCustomerId($customer_group_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "area WHERE customer_group_id = '" . $customer_group_id . "'");

        return $query->rows;
    }

    public function getAreasByAreaId($column_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "area_group WHERE column_id = '" . $column_id . "' group by area");

        return $query->rows;
    }

}
