<?php

class ModelVenueVenue extends PT_Model {

    public function addVenue($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "venue SET venue_group_id = '" . (int) ($data['venue_group_id']) . "',member_group_id = '" . (int) ($data['member_group_id']) . "', status = '" . (int) $data['status'] . "'");

        $venue_id = $this->db->lastInsertId();

        if (isset($data['venue_fee'])) {
            foreach ($data['venue_fee'] as $venue_fees) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "venue_fee SET venue_id = '" . (int) $venue_id . "', time = '" . $this->db->escape($venue_fees['time']) . "', hire_charge = '" . $this->db->escape($venue_fees['hire_charge']) . "', cleaning_charge = '" . $this->db->escape($venue_fees['cleaning_charge']) . "', electric_point = '" . $this->db->escape($venue_fees['electric_point']) . "',extra_hour = '" . $this->db->escape($venue_fees['extra_hour']) . "',tv = '" . $this->db->escape($venue_fees['tv']) . "',deposite = '" . $this->db->escape($venue_fees['deposite']) . "', sort_order = '" . (int) $venue_fees['sort_order'] . "'");
            }
        }

        if (isset($data['tax'])) {
            foreach ($data['tax'] as $tax) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "venue_tax SET venue_id = '" . (int) $venue_id . "', isActive = '1',venue = '" . $this->db->escape($tax['venue']) . "', royalty = '" . $this->db->escape($tax['royalty']) . "',gst = '" . $this->db->escape($tax['gst']) . "', total = '" . $this->db->escape($tax['total']) . "',sort_order = '" . (int) $tax['sort_order'] . "'");
            }
        }

        if (isset($data['info'])) {
            foreach ($data['info'] as $info) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "venue_info SET venue_id = '" . (int) $venue_id . "', title = '" . $this->db->escape($info['title']) . "', description = '" . $this->db->escape($info['description']) . "'");
            }
        }

        return $venue_id;
    }

    public function editVenue($venue_id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "venue SET venue_group_id = '" . (int) ($data['venue_group_id']) . "',member_group_id = '" . (int) ($data['member_group_id']) . "', status = '" . (int) $data['status'] . "' WHERE venue_id = '" . (int) $venue_id . "'");

        $this->db->query("DELETE FROM " . DB_PREFIX . "venue_fee WHERE venue_id = '" . (int) $venue_id . "'");

        foreach ($data['venue_fee'] as $venue_fees) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "venue_fee SET venue_id = '" . (int) $venue_id . "', time = '" . $this->db->escape($venue_fees['time']) . "', hire_charge = '" . $this->db->escape($venue_fees['hire_charge']) . "', cleaning_charge = '" . $this->db->escape($venue_fees['cleaning_charge']) . "', electric_point = '" . $this->db->escape($venue_fees['electric_point']) . "',extra_hour = '" . $this->db->escape($venue_fees['extra_hour']) . "',tv = '" . $this->db->escape($venue_fees['tv']) . "',deposite = '" . $this->db->escape($venue_fees['deposite']) . "', sort_order = '" . (int) $venue_fees['sort_order'] . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "venue_tax WHERE venue_id = '" . (int) $venue_id . "'");

        foreach ($data['tax'] as $tax) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "venue_tax SET venue_id = '" . (int) $venue_id . "', isActive = '1',venue = '" . $this->db->escape($tax['venue']) . "', royalty = '" . $this->db->escape($tax['royalty']) . "',gst = '" . $this->db->escape($tax['gst']) . "', total = '" . $this->db->escape($tax['total']) . "',sort_order = '" . (int) $tax['sort_order'] . "'");
        }

        $this->db->query("DELETE FROM " . DB_PREFIX . "venue_info WHERE venue_id = '" . (int) $venue_id . "'");

        foreach ($data['info'] as $info) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "venue_info SET venue_id = '" . (int) $venue_id . "', title = '" . $this->db->escape($info['title']) . "', description = '" . $this->db->escape($info['description']) . "'");
        }
    }

    public function deleteVenue($venue_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "venue WHERE venue_id = '" . (int) $venue_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "venue_fee WHERE venue_id = '" . (int) $venue_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "venue_tax WHERE venue_id = '" . (int) $venue_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "venue_info WHERE venue_id = '" . (int) $venue_id . "'");
    }

    public function getVenue($venue_id) {
        $query = $this->db->query("SELECT  s.*,sg.name,sg.venue_group_id FROM " . DB_PREFIX . "venue s LEFT  JOIN " . DB_PREFIX . "venue_group sg on S.venue_group_id = sg.venue_group_id WHERE s.venue_id = '" . (int) $venue_id . "'");

        return $query->row;
    }

    public function getVenues() {
        $query = $this->db->query("SELECT  s.*,sg.name FROM " . DB_PREFIX . "venue s LEFT  JOIN " . DB_PREFIX . "venue_group sg on S.venue_group_id = sg.venue_group_id WHERE s.status = '1'");

        return $query->rows;
    }

    public function getVenueFees($venue_id) {
        $venue_fees_data = array();

        $venue_fees_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "venue_fee WHERE venue_id = '" . (int) $venue_id . "' ORDER BY sort_order ASC");

        foreach ($venue_fees_query->rows as $venue_fees) {
            $venue_fees_data[] = array(
                'time' => $venue_fees['time'],
                'hire_charge' => $venue_fees['hire_charge'],
                'cleaning_charge' => $venue_fees['cleaning_charge'],
                'electric_point' => $venue_fees['electric_point'],
                'extra_hour' => $venue_fees['extra_hour'],
                'tv' => $venue_fees['tv'],
                'deposite' => $venue_fees['deposite'],
                'sort_order' => $venue_fees['sort_order']
            );
        }

        return $venue_fees_data;
    }

    public function getVenueTax($venue_id) {
        $venue_tax_data = array();

        $venue_tax_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "venue_tax WHERE venue_id = '" . (int) $venue_id . "' ORDER BY sort_order ASC");

        foreach ($venue_tax_query->rows as $venue_tax) {
            $venue_tax_data[] = array(
                'venue' => $venue_tax['venue'],
                'royalty' => $venue_tax['royalty'],
                'gst' => $venue_tax['gst'],
                'total' => $venue_tax['total'],
                'sort_order' => $venue_tax['sort_order']
            );
        }

        return $venue_tax_data;
    }

    public function getVenueInfo($venue_id) {
        $venue_info_data = array();

        $venue_info_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "venue_info WHERE venue_id = '" . (int) $venue_id . "'");

        foreach ($venue_info_query->rows as $venue_info) {
            $venue_info_data[] = array(
                'title' => $venue_info['title'],
                'description' => $venue_info['description']
            );
        }

        return $venue_info_data;
    }

    public function getTotalVenues() {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "banner");

        return $query->row['total'];
    }

    public function showVenues() {
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

}
