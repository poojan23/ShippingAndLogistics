<?php

class ModelClubAddCitation extends PT_Model {

  public function getCitationTableForm()
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "dgcitation");

        return $query->rows;
    }


  public function addCitation($data)
    {
      $club_id = $data['club_id'];

      foreach($data as $value){

        $this->db->query("INSERT INTO " . DB_PREFIX . "citation_project SET club_id = '" . $this->db->escape((string)$club_id) . "', dg_id = '" . $this->db->escape((string)$value['dg_id']) . "',  project_id = '" . $this->db->escape((string)$value['project_id']) . "', claim = '" . $this->db->escape((string)$value['claim']) . "', remark = '" . $this->db->escape((string)$value['remark']) . "', date_added = NOW(), date_modified = NOW()");
      
      }
      return $query;

    }
}
