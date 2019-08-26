<?php

class ModelReportDsr extends PT_Model
{
    public function getCustomFields($customer_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "column_fields WHERE customer_id = '" . (int)$customer_id . "' ORDER BY sort_order ASC");

        return $query->rows;
    }

    public function getDSR($customer_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "dsr WHERE customer_id = '" . (int)$customer_id . "'");

        return $query->rows;
    }

    public function getDSRValue($customer_id) {        
        $query = $this->db->query("SELECT column_id, customer_id, field_name, name, sort_order, (SELECT CASE c.field_name WHEN 'job_no' THEN d.job_no WHEN 'igm_no' THEN d.igm_no WHEN 'igm_date' THEN d.igm_date ELSE 0 END FROM " . DB_PREFIX . "dsr d WHERE d.customer_id = c.customer_id LIMIT 1) AS dsr FROM " . DB_PREFIX . "column_fields c WHERE c.customer_id = '" . (int)$customer_id . "' ORDER BY sort_order ASC");

        return $query->rows;
    }

    public function compareValues($customer_id) {
        $dsr_data = array();

        $column_compare_query = $this->db->query("SELECT * FROM (SELECT `field_name`, `customer_id`, `sort_order` FROM `". DB_PREFIX . "column_fields`) a JOIN (SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA` = '" . DB_DATABASE . "' AND `TABLE_NAME` = '" . DB_PREFIX . "dsr') b ON a.field_name = b.COLUMN_NAME WHERE a.customer_id = '" . (int)$customer_id . "' ORDER BY a.sort_order ASC");

        $dsr_query = $this->db->query("SELECT `dsr_id`, `customer_id`, `" . $column_compare_query->row['COLUMN_NAME'] . "` FROM " . DB_PREFIX . "dsr");

        $dsr_data[] = array(
            'value' => $dsr_query->rows
        );

        return $dsr_query->rows;
    }
}